<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\ZipcodeData;

class AddressController extends Controller
{
    public function getAddress($user_id = null, $id = null, $fetch_latest = false, $is_default = false)
    {
        $query = DB::table('addresses as addr')
            ->select('addr.*')
            ->where(function ($query) use ($user_id, $id) {
                if ($user_id !== null) {
                    $query->where('user_id', $user_id);
                }
                if ($id !== null) {
                    $query->where('addr.id', $id);
                }
            })
            ->groupBy('addr.id')
            ->orderByDesc('addr.id');

        if ($fetch_latest) {
            $query->limit(1);
        }

        if ($is_default) {
            $query->where('is_default', true);
        }


        $addresses = $query->get();
        $addresses = $addresses->map(function ($address) {
            $address->area = $address->area == "NULL" ? '' : $address->area;
            $zipcode = $address->pincode ?? null;

            $zipcode_id = fetchDetails('zipcodes', ['zipcode' => $zipcode], 'id');

            $zipcode_id = isset($zipcode_id) && !empty($zipcode_id) ? $zipcode_id[0]->id : '';
            $minimum_free_delivery_order_amount = fetchDetails('zipcodes', ['id' => $zipcode_id], ['minimum_free_delivery_order_amount', 'delivery_charges']);
            $address->minimum_free_delivery_order_amount = optional($minimum_free_delivery_order_amount)->minimum_free_delivery_order_amount ?? 0;
            $address->delivery_charges = optional($minimum_free_delivery_order_amount)->delivery_charges ?? 0;
            return $address;
        });

        return $addresses->toArray();
    }

    public function getAddressDetails(Request $request)
    {
        try {
            $zipcodeId = $request->query('zipcode_id');

            if (!$zipcodeId) {
                return response()->json([
                    'error' => true,
                    'message' => 'Zipcode ID is required.'
                ], 400);
            }

            // Кешування на 24 години
            $cacheKey = 'address_details_' . $zipcodeId;
            $zipcodeData = Cache::remember($cacheKey, now()->addHours(24), function () use ($zipcodeId) {
                return ZipcodeData::where('id', $zipcodeId)->first();
            });

            // $zipcodeData = ZipcodeData::where('id', $zipcodeId)->first();

            if (!$zipcodeData) {
                return response()->json([
                    'error' => true,
                    'message' => 'Zipcode data not found.'
                ], 404);
            }

            // Витягування даних із поля data
            $data = $zipcodeData->data ?? [];

            return response()->json([
                'error' => false,
                'data' => [
                    'country' => $data['country'] ?? null,
                    'region' => $data['state'] ?? $data['district'] ?? null,
                    'city' => $data['city'] ?? null,
                    'zipcode' => $data['postcode'] ?? null,
                    'street' => $data['formatted'] ?? $data['address_line2'] ?? null,
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to retrieve address details: ' . $e->getMessage()
            ], 500);
        }
    }
}
