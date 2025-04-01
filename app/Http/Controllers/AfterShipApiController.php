<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\OrderTracking;
use App\Models\Parcel;
use App\Models\Parcelitem;
use App\Models\OrderItems;

use Tracking\Client;
use Tracking\Config;
use Tracking\Exception\AfterShipError;
use Tracking\API\Tracking\CreateTrackingRequest;
use Tracking\API\Tracking\CreateTrackingResponse;

class AfterShipApiController extends Controller
{
    // public $api_version = '2025-01';
    // public $api_url = 'https://api.aftership.com/tracking/';
    protected $as_api_key;
    protected $client;


    public function __construct()
    {
        $this->as_api_key = $this->setAfterShipApiKey();
        $this->client = new Client([
            'apiKey' => $this->as_api_key,
            'authenticationType' => Config::AUTHENTICATION_TYPE_API_KEY,
        ]);
    }

    public function setAfterShipApiKey()
    {
        $shippingSettings = json_decode(getSettings('shipping_method', true), true);
        if (!$shippingSettings['aftership_apikey']) {
            throw new \Exception('Aftership API key is not configured.');
        }
        return $shippingSettings['aftership_apikey'];
    }

    public function getCouriersList()
    {
        // Шлях до файлу кешу в storage
        $cacheFile = storage_path('app/aftership_couriers_cache.json');
        $cacheTTL = 86400; // 24 години в секундах

        // Перевіряємо, чи існує файл і чи не минув час кешування
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTTL) {
            // Читаємо дані з кешу
            $cachedData = json_decode(file_get_contents($cacheFile), true);
            \Log::error('Return couriers list from cache ');
            return response()->json($cachedData);
        }

        // Якщо кешу немає або він застарів - робимо запит до API
        try {
            $response = $this->client->courier->getAllCouriers();
            \Log::error('Return couriers list from AfterShip API');
            // Зберігаємо отримані дані у файл
            file_put_contents($cacheFile, json_encode($response));
            return response()->json($response);
        } catch (AfterShipError $e) {
            return response()->json(['error' => 'Error loading data from AfterShip', 'aftership' => $e->getMessage()], 400);
        }
    }


    public function createTracking(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'parcel_id' => 'required|exists:parcels,id',
            'courier_agency' => 'required|string|max:255',
            'tracking_id' => 'required|string|max:255',
            'url' => 'nullable|url',
        ]);

        $payload = new CreateTrackingRequest();
        $payload->tracking_number = $request->tracking_id;
        $payload->slug = $request->courier_agency;
        $payload->order_id = (string) $request->order_id;

        \Log::info('Send tracking data for register shipment: ' . json_encode([
            'tracking_number' => $payload->tracking_number,
            'slug' => $payload->slug,
            'order_id' => $payload->order_id,
        ]));

        try {
            // Виконуємо запит до AfterShip API через SDK
            $trackingInfo = $this->client->tracking->createTracking($payload);
            \Log::info('AfterShip response: ' . json_encode($trackingInfo));

            // Оновлюємо або створюємо запис у локальній базі даних
            $orderTracking = OrderTracking::updateOrCreate(
                ['parcel_id' => $request->parcel_id],
                [
                    'order_id' => $request->order_id,
                    'carrier_id' => $request->courier_agency,
                    'courier_agency' => $request->courier_agency,
                    'tracking_number' => $request->tracking_id,
                    'tracking_id' => $request->tracking_id,
                    'url' => $request->url ?? null,
                    'status' => 'pending',
                    'date' => now(),
                    'aftership_tracking_id' => $trackingInfo->id, // Використовуємо властивість id
                    'aftership_data' => json_encode($trackingInfo), // Зберігаємо весь об’єкт
                ]
            );

            // Оновлюємо статус у таблиці order_items (опціонально)
            \App\Models\OrderItems::where('order_id', $request->order_id)
                ->update(['active_status' => 'processed']);

            return response()->json([
                'message' => 'Tracking created successfully',
                'tracking' => (array) $trackingInfo, // Перетворюємо об’єкт у масив для JSON
                'error' => false
            ], 201);
        } catch (AfterShipError $e) {
            \Log::error('AfterShip API error: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')');
            return response()->json([
                'error' => 'Failed to create tracking in AfterShip',
                'details' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ],
            ], $e->getCode() === 4007 ? 400 : 500);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in createTracking: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unexpected error occurred',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Оновлює історію статусів, уникаючи дублювання статусу "shipped"
     *
     * @param mixed $model Модель (Parcel або OrderItems)
     * @param string $newStatus Новий статус
     * @param string $timestamp Часова мітка
     * @return void
     */
    protected function updateStatusHistory($model, string $newStatus, string $timestamp): void
    {
        $currentStatusHistory = $model->status ? json_decode($model->status, true) : [];

        // Перевіряємо, чи потрібно додавати новий статус
        if ($newStatus === 'shipped') {
            $hasShipped = false;
            foreach ($currentStatusHistory as $history) {
                if ($history[0] === 'shipped') {
                    $hasShipped = true;
                    break;
                }
            }
            // Якщо "shipped" уже є, не додаємо його знову
            if ($hasShipped) {
                return;
            }
        }

        // Додаємо новий статус до історії
        $currentStatusHistory[] = [$newStatus, $timestamp];
        $model->status = json_encode($currentStatusHistory);
    }

    /**
     * Отримати інформацію про трекінг за aftership_tracking_id
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTrackingById(Request $request)
    {
        $request->validate([
            'aftership_tracking_id' => 'required|string|max:255',
        ]);

        $cacheFile = storage_path('app/aftership_tracking_' . $request->aftership_tracking_id . '_cache.json');
        $cacheTTL = 3600;

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTTL) {
            $cachedData = json_decode(file_get_contents($cacheFile), true);
            \Log::info('Return tracking data from cache for ID: ' . $request->aftership_tracking_id);
            return response()->json($cachedData);
        }

        try {
            $trackingResponse = $this->client->tracking->getTrackingById($request->aftership_tracking_id);
            $trackingData = (array) $trackingResponse;

            $orderTracking = OrderTracking::where('aftership_tracking_id', $request->aftership_tracking_id)->first();
            if ($orderTracking) {
                $afterShipStatus = $trackingData['tag'] ?? $orderTracking->status;
                $mappedStatus = $this->mapAfterShipStatus($afterShipStatus);
                $timestamp = now()->format('d-m-Y h:i:sa');

                $orderTracking->update([
                    'status' => $mappedStatus,
                    'aftership_data' => json_encode($trackingData),
                    'tracking_number' => $trackingData['tracking_number'] ?? $orderTracking->tracking_number,
                    'carrier_id' => $trackingData['slug'] ?? $orderTracking->carrier_id,
                ]);

                $parcels = Parcel::where('order_id', $orderTracking->order_id)
                    ->where('id', $orderTracking->parcel_id)
                    ->get();

                foreach ($parcels as $parcel) {
                    $parcel->active_status = $mappedStatus;
                    $this->updateStatusHistory($parcel, $mappedStatus, $timestamp); // Використовуємо новий метод
                    $parcel->save();

                    $parcelItemIds = ParcelItem::where('parcel_id', $parcel->id)
                        ->pluck('order_item_id')
                        ->toArray();

                    $orderItems = OrderItems::where('order_id', $orderTracking->order_id)
                        ->whereIn('id', $parcelItemIds)
                        ->get();

                    foreach ($orderItems as $item) {
                        $item->active_status = $mappedStatus;
                        $this->updateStatusHistory($item, $mappedStatus, $timestamp); // Використовуємо новий метод
                        $item->save();
                    }
                }
            }

            $responseData = [
                'message' => 'Tracking retrieved successfully',
                'tracking' => $trackingData,
                'error' => false,
                'cached_at' => now()->toDateTimeString(),
            ];

            file_put_contents($cacheFile, json_encode($responseData));
            \Log::info('Tracking data cached for ID: ' . $request->aftership_tracking_id);

            return response()->json($responseData, 200);
        } catch (AfterShipError $e) {
            \Log::error('AfterShip API error on getTrackingById: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')');
            return response()->json([
                'error' => 'Failed to retrieve tracking from AfterShip',
                'details' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ],
            ], $e->getCode() === 4007 ? 400 : 500);
        } catch (\Exception $e) {
            \Log::error('Unexpected error in getTrackingById: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unexpected error occurred',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function clearTrackingCache(Request $request)
    {
        $request->validate(['aftership_tracking_id' => 'required|string']);
        $cacheFile = storage_path('app/aftership_tracking_' . $request->aftership_tracking_id . '_cache.json');
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
            return response()->json(['message' => 'Cache cleared successfully']);
        }
        return response()->json(['message' => 'No cache found'], 404);
    }

    /**
     * Мапінг статусів AfterShip на внутрішні статуси системи
     *
     * @param string $afterShipStatus
     * @return string
     */
    protected function mapAfterShipStatus(string $afterShipStatus): string
    {
        $statusMap = [
            'Pending' => 'shipped',
            'InfoReceived' => 'shipped',
            'InTransit' => 'shipped',
            'Delivered' => 'delivered',
            // Додаткові статуси можна додати тут у майбутньому
            // 'OutForDelivery' => 'shipped',
            // 'Exception' => 'returned',
            // 'FailedAttempt' => 'shipped',
        ];

        // Повертаємо зіставлений статус або зберігаємо оригінальний, якщо мапінг відсутній
        return $statusMap[$afterShipStatus] ?? $afterShipStatus;
    }

    /**
     * Обробка вебхука від AfterShip для оновлення статусу трекінгу
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        $shippingSettings = json_decode(getSettings('shipping_method', true), true);
        $secretKey = $shippingSettings['aftership_secret'] ?? null;

        if (!$secretKey) {
            \Log::error('AfterShip webhook failed: Secret key not configured.');
            return response()->json(['error' => 'Secret key not configured'], 403);
        }

        $signature = $request->header('aftership-hmac-sha256');
        $rawBody = $request->getContent();
        $computedSignature = base64_encode(hash_hmac('sha256', $rawBody, $secretKey, true));

        if (!hash_equals($signature, $computedSignature)) {
            \Log::error('AfterShip webhook failed: Invalid signature.');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = $request->json()->all();
        $trackingData = $payload['msg'] ?? null;
        $trackingId = $trackingData['id'] ?? null;

        if (!$trackingId || !$trackingData) {
            \Log::error('AfterShip webhook failed: Missing tracking ID or data.', ['payload' => $payload]);
            return response()->json(['error' => 'Missing tracking ID or data'], 400);
        }

        $orderTracking = OrderTracking::where('aftership_tracking_id', $trackingId)->first();
        if ($orderTracking) {
            try {
                $afterShipStatus = $trackingData['tag'] ?? $orderTracking->status;
                $mappedStatus = $this->mapAfterShipStatus($afterShipStatus);
                $timestamp = now()->format('d-m-Y h:i:sa');

                $orderTracking->update([
                    'status' => $mappedStatus,
                    'aftership_data' => json_encode($trackingData),
                    'tracking_number' => $trackingData['tracking_number'] ?? $orderTracking->tracking_number,
                    'carrier_id' => $trackingData['slug'] ?? $orderTracking->carrier_id,
                ]);

                $parcels = Parcel::where('order_id', $orderTracking->order_id)
                    ->where('id', $orderTracking->parcel_id)
                    ->get();

                if ($parcels->isEmpty()) {
                    \Log::warning('No parcels found for order_id: ' . $orderTracking->order_id);
                }

                foreach ($parcels as $parcel) {
                    $parcel->active_status = $mappedStatus;
                    $this->updateStatusHistory($parcel, $mappedStatus, $timestamp); // Використовуємо новий метод
                    $parcel->save();

                    $parcelItemIds = ParcelItem::where('parcel_id', $parcel->id)
                        ->pluck('order_item_id')
                        ->toArray();

                    $orderItems = OrderItems::where('order_id', $orderTracking->order_id)
                        ->whereIn('id', $parcelItemIds)
                        ->get();

                    foreach ($orderItems as $item) {
                        $item->active_status = $mappedStatus;
                        $this->updateStatusHistory($item, $mappedStatus, $timestamp); // Використовуємо новий метод
                        $item->save();
                    }
                }

                $cacheFile = storage_path('app/aftership_tracking_' . $trackingId . '_cache.json');
                if (file_exists($cacheFile)) {
                    unlink($cacheFile);
                    \Log::info('Cache cleared for tracking ID: ' . $trackingId);
                }

                \Log::info('AfterShip webhook processed successfully.', [
                    'tracking_id' => $trackingId,
                    'aftership_status' => $afterShipStatus,
                    'mapped_status' => $mappedStatus,
                    'order_id' => $orderTracking->order_id,
                    'parcel_id' => $orderTracking->parcel_id,
                ]);

                return response()->json(['message' => 'Webhook processed successfully'], 200);
            } catch (\Exception $e) {
                \Log::error('Failed to update tracking: ' . $e->getMessage());
                return response()->json(['error' => 'Database update failed'], 500);
            }
        }

        \Log::warning('AfterShip webhook: Tracking not found in database.', ['tracking_id' => $trackingId]);
        return response()->json(['error' => 'Tracking not found'], 404);
    }
}
