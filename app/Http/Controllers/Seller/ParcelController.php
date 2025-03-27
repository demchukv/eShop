<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ParcelController extends Controller
{
    public function createCustomCarrier(Request $request)
    {
        $shippingSettings = json_decode(getSettings('shipping_method', true), true);

        // Перевірка, чи увімкнено Couriers List
        if (!$shippingSettings['couriers_list_method']) {
            return response()->json(['error' => 'Couriers List shipping method is disabled'], 403);
        }

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'parcel_id' => 'required|exists:parcels,id',
            'carrier_name' => 'required|string|max:255', // Назва перевізника зі списку
            'tracking_number' => 'required|string|max:255',
            'status' => 'nullable|in:processed,shipped,delivered',
        ]);


        $orderTracking = OrderTracking::updateOrCreate(
            ['parcel_id' => $request->parcel_id], // Унікальний ключ для пошуку
            [
                'order_id' => $request->order_id,
                'carrier_id' => $request->carrier_name,
                'tracking_number' => $request->tracking_number,
                'status' => $request->status ?? 'processed',
            ]
        );

        // Оновлення статусу order_items (опціонально)
        \App\Models\OrderItems::where('order_id', $request->order_id)
            ->update(['active_status' => $request->status ?? 'processed']);

        return redirect()->back()->with('success', 'Custom carrier parcel created successfully.');
    }

    public function trackParcel($trackingId)
    {
        $tracking = OrderTracking::findOrFail($trackingId);
        $apiKey = 'YOUR_AFTERSHIP_API_KEY'; // Зберігайте в конфігурації або налаштуваннях

        $response = Http::withHeaders([
            'aftership-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.aftership.com/v4/trackings', [
            'tracking' => [
                'tracking_number' => $tracking->tracking_number,
                'slug' => $tracking->carrier_id,
            ],
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Unable to track parcel'], 500);
        }
    }
}
