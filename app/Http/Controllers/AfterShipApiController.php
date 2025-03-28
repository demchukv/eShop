<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


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
            return response()->json(['error' => 'Couriers List shipping method not enabled. Set up Aftership API key.'], 403);
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

    // public function createCustomCarrier(Request $request)
    // {
    //     $shippingSettings = json_decode(getSettings('shipping_method', true), true);

    //     // Перевірка, чи увімкнено Couriers List
    //     if (!$shippingSettings['couriers_list_method']) {
    //         return response()->json(['error' => 'Couriers List shipping method is disabled'], 403);
    //     }

    //     $request->validate([
    //         'order_id' => 'required|exists:orders,id',
    //         'parcel_id' => 'required|exists:parcels,id',
    //         'carrier_name' => 'required|string|max:255', // Назва перевізника зі списку
    //         'tracking_number' => 'required|string|max:255',
    //         'status' => 'nullable|in:processed,shipped,delivered',
    //     ]);


    //     $orderTracking = OrderTracking::updateOrCreate(
    //         ['parcel_id' => $request->parcel_id], // Унікальний ключ для пошуку
    //         [
    //             'order_id' => $request->order_id,
    //             'carrier_id' => $request->carrier_name,
    //             'tracking_number' => $request->tracking_number,
    //             'status' => $request->status ?? 'processed',
    //         ]
    //     );

    //     // Оновлення статусу order_items (опціонально)
    //     \App\Models\OrderItems::where('order_id', $request->order_id)
    //         ->update(['active_status' => $request->status ?? 'processed']);

    //     return redirect()->back()->with('success', 'Custom carrier parcel created successfully.');
    // }
}
