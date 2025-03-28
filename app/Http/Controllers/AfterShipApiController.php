<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\OrderTracking;


class AfterShipApiController extends Controller
{
    public $api_version = '2025-01';
    public $api_url = 'https://api.aftership.com/tracking/';
    public $as_api_key;

    public function __construct()
    {
        $this->as_api_key = $this->setAfterShipApiKey();
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
        $response = Http::withHeaders([
            'as-api-key' => $this->as_api_key,
            'Content-Type' => 'application/json',
        ])->get($this->api_url . $this->api_version . "/couriers/all");

        if ($response->successful()) {
            $data = $response->json();

            // Зберігаємо отримані дані у файл
            file_put_contents($cacheFile, json_encode($data));

            return response()->json($data);
        } else {
            if ($response) {
                return response()->json(['error' => 'Error loading data from AfterShip', 'aftership' => $response->json()], 400);
            } else {
                return response()->json(['error' => 'Unable to fetch couriers list'], 500);
            }
        }
    }


    public function createTracking(Request $request)
    {
        // Валідація вхідних даних
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'parcel_id' => 'required|exists:parcels,id',
            'courier_agency' => 'required|string|max:255', // slug перевізника з AfterShip
            'tracking_id' => 'required|string|max:255',    // Номер відстеження
            'url' => 'nullable|url',                       // URL відстеження (опціонально)
        ]);

        // Формуємо дані для API AfterShip
        $trackingData = [
            'tracking' => [
                'tracking_number' => $request->tracking_id,
                'slug' => $request->courier_agency,
                'order_id' => (string) $request->order_id, // Перетворюємо в string для AfterShip
            ]
        ];

        // Виконуємо POST-запит до AfterShip API
        $response = Http::withHeaders([
            'as-api-key' => $this->as_api_key,
            'Content-Type' => 'application/json',
        ])->post($this->api_url . $this->api_version . '/trackings', $trackingData);

        // Перевіряємо успішність запиту
        if ($response->successful()) {
            $data = $response->json();

            // Оновлюємо або створюємо запис у локальній базі даних
            $orderTracking = OrderTracking::updateOrCreate(
                ['parcel_id' => $request->parcel_id], // Унікальний ключ для пошуку
                [
                    'order_id' => $request->order_id,
                    'carrier_id' => $request->courier_agency, // Зберігаємо slug перевізника
                    'courier_agency' => $request->courier_agency, // Зберігаємо slug перевізника
                    'tracking_number' => $request->tracking_id, // Номер відстеження
                    'tracking_id' => $request->tracking_id, // Номер відстеження
                    'url' => $request->url ?? null, // URL відстеження
                    'status' => 'pending', // Початковий статус
                    'data' => json_encode($data['data']['tracking']), // Зберігаємо повну відповідь від AfterShip
                    'date' => now(), // Дата створення
                ]
            );

            // Оновлюємо статус у таблиці order_items (опціонально)
            \App\Models\OrderItems::where('order_id', $request->order_id)
                ->update(['active_status' => 'processed']);

            return response()->json([
                'message' => 'Tracking created successfully',
                'tracking' => $data['data']['tracking'],
                'error' => false
            ], 201);
        } else {
            $errorData = $response->json();
            \Log::error('AfterShip API error: ' . json_encode($errorData));
            return response()->json([
                'error' => 'Failed to create tracking in AfterShip',
                'details' => $errorData,
            ], $response->status());
        }
    }

    /**
     * Отримати інформацію про трекінг за aftership_tracking_id
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTrackingById(Request $request)
    {
        // Валідація вхідних даних
        $request->validate([
            'aftership_tracking_id' => 'required|string|max:255', // Унікальний ID трекінгу від AfterShip
        ]);

        // Шлях до файлу кешу для конкретного трекінгу
        $cacheFile = storage_path('app/aftership_tracking_' . $request->aftership_tracking_id . '_cache.json');
        $cacheTTL = 3600; // 1 година в секундах (можна налаштувати)

        // Перевіряємо, чи існує кеш і чи він ще актуальний
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTTL) {
            $cachedData = json_decode(file_get_contents($cacheFile), true);
            \Log::info('Return tracking data from cache for ID: ' . $request->aftership_tracking_id);
            return response()->json($cachedData);
        }

        // Виконуємо GET-запит до AfterShip API
        $response = Http::withHeaders([
            'as-api-key' => $this->as_api_key,
            'Content-Type' => 'application/json',
        ])->get($this->api_url . $this->api_version . '/trackings/' . $request->aftership_tracking_id);

        // Перевіряємо успішність запиту
        if ($response->successful()) {
            $data = $response->json();

            // Оновлюємо локальний запис у базі (опціонально)
            $orderTracking = OrderTracking::where('aftership_tracking_id', $request->aftership_tracking_id)->first();
            if ($orderTracking) {
                $orderTracking->update([
                    'status' => $data['data']['tracking']['tag'] ?? $orderTracking->status, // Оновлюємо статус
                    'aftership_data' => json_encode($data['data']['tracking']),             // Оновлюємо повну відповідь
                    'tracking_number' => $data['data']['tracking']['tracking_number'] ?? $orderTracking->tracking_number,
                    'carrier_id' => $data['data']['tracking']['slug'] ?? $orderTracking->carrier_id,
                ]);
                // синхронізуємо статус у таблиці order_items
                \App\Models\OrderItems::where('order_id', $orderTracking->order_id)
                    ->update(['active_status' => $data['data']['tracking']['tag'] ?? $orderTracking->status]);
            }
            // Зберігаємо дані в кеш
            $cacheData = [
                'message' => 'Tracking retrieved successfully',
                'tracking' => $data['data']['tracking'],
                'error' => false,
                'cached_at' => now()->toDateTimeString(), // Додаємо час кешування для дебагу
            ];
            file_put_contents($cacheFile, json_encode($cacheData));
            \Log::info('Tracking data cached for ID: ' . $request->aftership_tracking_id);

            return response()->json([
                'message' => 'Tracking retrieved successfully',
                'tracking' => $data['data']['tracking'],
                'error' => false
            ], 200);
        } else {
            $errorData = $response->json();
            \Log::error('AfterShip API error on getTrackingById: ' . json_encode($errorData));
            return response()->json([
                'error' => 'Failed to retrieve tracking from AfterShip',
                'details' => $errorData,
            ], $response->status());
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

    // ... інші методи (getCouriersList, createTracking, getTrackingById) залишаються без змін ...

    /**
     * Обробка вебхука від AfterShip для оновлення статусу трекінгу
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        // Отримуємо секретний ключ із налаштувань
        $shippingSettings = json_decode(getSettings('shipping_method', true), true);
        $secretKey = $shippingSettings['aftership_secret'] ?? null;

        if (!$secretKey) {
            \Log::error('AfterShip webhook failed: Secret key not configured.');
            return response()->json(['error' => 'Secret key not configured'], 403);
        }

        // Перевіряємо підпис вебхука (HMAC SHA256)
        $signature = $request->header('aftership-hmac-sha256');
        $rawBody = $request->getContent();
        $computedSignature = base64_encode(hash_hmac('sha256', $rawBody, $secretKey, true));

        if (!hash_equals($signature, $computedSignature)) {
            \Log::error('AfterShip webhook failed: Invalid signature.');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Отримуємо дані з вебхука
        $payload = $request->json()->all();
        $trackingId = $payload['tracking']['id'] ?? null; // aftership_tracking_id
        $status = $payload['tracking']['tag'] ?? null;     // Статус (наприклад, "InTransit", "Delivered")

        if (!$trackingId || !$status) {
            \Log::error('AfterShip webhook failed: Missing tracking ID or status.', ['payload' => $payload]);
            return response()->json(['error' => 'Missing tracking ID or status'], 400);
        }

        // Оновлюємо запис у базі
        $orderTracking = OrderTracking::where('aftership_tracking_id', $trackingId)->first();
        if ($orderTracking) {
            try {
                $orderTracking->update([
                    'status' => $status,
                    'aftership_data' => json_encode($payload['tracking']), // Оновлюємо повні дані
                    'tracking_number' => $payload['tracking']['tracking_number'] ?? $orderTracking->tracking_number,
                    'carrier_id' => $payload['tracking']['slug'] ?? $orderTracking->carrier_id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to update tracking: ' . $e->getMessage());
                return response()->json(['error' => 'Database update failed'], 500);
            }

            // Оновлюємо статус у order_items (опціонально)
            \App\Models\OrderItems::where('order_id', $orderTracking->order_id)
                ->update(['active_status' => $status]);

            \Log::info('AfterShip webhook processed successfully.', [
                'tracking_id' => $trackingId,
                'status' => $status,
            ]);
            // Очищаємо кеш для даного трекінгу
            $cacheFile = storage_path('app/aftership_tracking_' . $trackingId . '_cache.json');
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
                \Log::info('Cache cleared for tracking ID: ' . $trackingId);
            }

            return response()->json(['message' => 'Webhook processed successfully'], 200);
        }

        \Log::warning('AfterShip webhook: Tracking not found in database.', ['tracking_id' => $trackingId]);
        return response()->json(['error' => 'Tracking not found'], 404);
    }
}
