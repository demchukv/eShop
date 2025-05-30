<?php

namespace App\Livewire\Orders;

use Illuminate\Support\Facades\Session;
use App\Models\OrderItems;
use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Refund;
use Stripe\Stripe as StripeConfig;
use App\Http\Controllers\CommissionController;
use App\Models\Parcelitem;
use App\Models\ReturnRequest;
use App\Models\Disput;


class Details extends Component
{
    public function render(Request $request)
    {
        $store_id = session('store_id');
        $order_id = $request->segment(2);
        $user = Auth::user();
        $payment_method = getSettings('payment_method', true, true);
        $payment_method = json_decode($payment_method);

        $user_orders = fetchOrders(order_id: $order_id, user_id: $user->id, store_id: $store_id);
        // dd($user_orders);
        if (count($user_orders['order_data']) < 1) {
            abort(404);
        }
        $user_orders_transaction_data = json_decode(json_encode($user_orders['order_data']), true);

        $couriersFilePath = storage_path('app/aftership_couriers_cache.json');
        // $main_transaction = Transaction::where('order_id', $order_id)->first();
        $main_transaction = Transaction::whereRaw("FIND_IN_SET($order_id, order_id)")->first();

        $couriersMap = \Cache::remember('aftership_couriers', 60 * 60 * 24, function () use ($couriersFilePath) {
            $couriersData = file_exists($couriersFilePath) ? json_decode(file_get_contents($couriersFilePath), true) : ['couriers' => []];
            return collect($couriersData['couriers'])->pluck('name', 'slug')->all();
        });

        foreach ($user_orders_transaction_data as &$user_order) {
            foreach ($user_order['order_items'] as &$user_order_item) {
                // \Log::debug(json_encode($user_order_item['status']));
                foreach ($user_order_item['status'] as $statuses) {
                    $user_order_item['status_name'][$statuses[0]] = $statuses[1];
                }
                // \Log::debug(json_encode($user_order_item['status_name']));
                $order_item_id = $user_order_item['id'];
                $transaction = Transaction::where('order_item_id', $order_item_id)->first();

                if ($transaction) {
                    $user_order_item['transaction'] = $transaction->toArray();
                } else {
                    $user_order_item['transaction'] = null;
                }

                $courierSlug = $user_order_item['courier_agency'] ?? null;
                $user_order_item['courier_agency_name'] = $courierSlug && isset($couriersMap[$courierSlug])
                    ? $couriersMap[$courierSlug]
                    : 'Unknown Courier';

                // Додаємо ID диспуту, якщо active_status = return_request_pending
                if ($user_order_item['active_status'] === 'return_request_pending') {
                    $return_request = ReturnRequest::where('order_item_id', $order_item_id)->first();
                    if ($return_request) {
                        $disput = Disput::where('return_request_id', $return_request->id)->first();
                        $user_order_item['disput_id'] = $disput ? $disput->id : null;
                    } else {
                        $user_order_item['disput_id'] = null;
                    }
                } else {
                    $user_order_item['disput_id'] = null;
                }
            }
        }

        $currency_id = $user_orders['order_data'][0]->order_payment_currency_id ?? null;
        $currency_symbol = "";
        if ($currency_id != null) {
            $currency = fetchDetails('currencies', ['id' => $currency_id]);
            $currency_symbol = $currency[0]->symbol;
        }



        return view('livewire.' . config('constants.theme') . '.orders.details', [
            'user_orders' => $user_orders,
            'order_transaction' => $user_orders_transaction_data,
            'currency_symbol' => $currency_symbol,
            'transaction' => $main_transaction,
            'user_info' => $user,
            'payment_method' => $payment_method
        ])->title("Orders Detail |");
    }


    public function update_order_item_status(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'order_status' => 'required',
            'order_item_id' => 'required|array',
            'order_item_id.*' => 'exists:order_items,id',
            'refund_method' => 'sometimes|in:wallet,card,contractual'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->all(),
                'code' => 102,
            ]);
        }

        $order_item_ids = $request->input('order_item_id');
        $refund_method = $request->input('refund_method', 'wallet');
        $payment_method = $request->input('payment_method');
        // dd($refund_method);
        \Log::info("Запит на оновлення статусу: order_status={$request['order_status']}, order_item_ids=" . json_encode($order_item_ids) . ", refund_method={$refund_method}");

        if ($request['order_status'] == 'cancelled') {
            $order_item = OrderItems::find($order_item_ids[0]);
            if (!$order_item) {
                return response()->json([
                    'error' => true,
                    'message' => 'Order item not found',
                ]);
            }

            if ($payment_method != 'contractual') {
                // $transaction = Transaction::where('order_id', $order_item->order_id)
                $transaction = Transaction::whereRaw("FIND_IN_SET($order_item->order_id, order_id)")
                    ->where('status', 'success')
                    ->first();

                if (!$transaction) {
                    return response()->json([
                        'error' => true,
                        'message' => 'No successful transaction found for this order',
                    ]);
                }

                $transaction_type = $transaction->transaction_type;
                $payment_type = $transaction->type;

                if ($transaction_type === 'wallet' && $refund_method !== 'wallet') {
                    return response()->json([
                        'error' => true,
                        'message' => 'For wallet payments, refund can only be made to wallet',
                    ]);
                }

                if ($transaction_type === 'transaction' && $payment_type !== 'stripe' && $refund_method !== 'wallet') {
                    return response()->json([
                        'error' => true,
                        'message' => 'For this payment method, refund can only be made to wallet',
                    ]);
                }
            }

            $order_items = OrderItems::where('order_id', $order_item->order_id)->get();
            $cancelled_items = OrderItems::whereIn('id', $order_item_ids)->get();
            $total_order_amount = $order_items->sum('sub_total');
            $cancelled_amount = $cancelled_items->sum('sub_total');
            $is_full_refund = $total_order_amount == $cancelled_amount;

            foreach ($order_item_ids as $order_item_id) {
                $order_item = OrderItems::find($order_item_id);

                if (!$order_item) {
                    \Log::warning("Product with ID {$order_item_id} not found, skip it.");
                    continue;
                }

                // Перевіряємо поточний статус
                if ($order_item->active_status === 'cancelled') {
                    \Log::warning("Product with ID {$order_item_id} alredy cancelled, skip.");
                    continue;
                }

                // Викликаємо update_order_item
                \Log::info("Parameters for update_order_item: order_item_id={$order_item->id}, status=cancelled, 1");
                $update_result = update_order_item($order_item->id, 'cancelled', 1);

                if ($update_result['error']) {
                    \Log::warning("Error update for order_item_id {$order_item_id}: " . $update_result['message']);
                    continue;
                }

                \Log::info("Successfully updated order_item_id: {$order_item_id} in update_order_item");

                updateStock($order_item->product_variant_id, $order_item->quantity, 'plus');

                if ($payment_method != 'contractual') {
                    if ($refund_method === 'wallet') {
                        process_refund($order_item->id, $request['order_status']);
                    } elseif ($refund_method === 'card' && $transaction->type === 'stripe') {
                        $this->processStripeRefund($request, $order_item, $transaction, $total_order_amount, $cancelled_amount);
                    }
                }
            }
            if ($payment_method != 'contractual') {
                $commissionController = new CommissionController();
                $commissionController->updateCommissions($order_item->order_id, $is_full_refund, $cancelled_items, $order_items);
            }
            // Логуємо стан усіх товарів після оновлення
            $updated_items = OrderItems::where('order_id', $order_item->order_id)->get();
            \Log::info("Стан товарів після скасування: " . json_encode($updated_items->toArray()));

            return response()->json([
                'error' => false,
                'message' => 'Order Items Cancelled Successfully',
            ]);
        }

        if ($request['order_status'] == 'returned') {
            $order_item = OrderItems::find($order_item_ids[0]);
            \Log::alert('return order item id: ' . json_encode(($order_item)));
            if (!$order_item) {
                return response()->json([
                    'error' => true,
                    'message' => 'Order item not found',
                ]);
            }

            $res = validateOrderStatus(
                $order_item->id,
                $request['order_status'],
                'order_items',
                '',
                true
            );

            if ($res['error']) {
                $response['error'] = (isset($res['return_request_flag'])) ? false : true;
                $response['message'] = $res['message'];
                $response['data'] = $res['data'];
                return response()->json($response);
            }

            $update_result = update_order_item($order_item->id, 'returned', 0);

            if ($update_result['error']) {
                return response()->json([
                    'error' => true,
                    'message' => $update_result['message'],
                ]);
            }

            return response()->json([
                'error' => false,
                'message' => 'Order Status Updated Successfully',
            ]);
        }
    }

    private function processStripeRefund(Request $request, $order_item, $transaction, $total_order_amount, $cancelled_amount)
    {
        $stripe = new \App\Libraries\Stripe();
        StripeConfig::setApiKey($stripe->getSecretKey());

        $cancelled_items = OrderItems::whereIn('id', $request->input('order_item_id'))->get();

        // Базова сума повернення
        $refund_amount = $cancelled_amount;

        // Розрахунок пропорційної комісії для часткового повернення
        if ($transaction->fee > 0 && $total_order_amount > 0) {
            // $refund_ratio = $cancelled_amount / $total_order_amount; // Співвідношення суми повернення до загальної суми
            $refund_ratio = $cancelled_amount / $transaction->amount; // Співвідношення суми повернення до загальної суми
            $proportional_fee = $transaction->fee * $refund_ratio;   // Пропорційна комісія
            $refund_amount += $proportional_fee;                     // Додаємо пропорційну комісію до суми повернення
        }

        // Конвертуємо суму в центи (Stripe працює з найменшими одиницями валюти)
        $refund_amount_cents = (int) ($refund_amount * 100);

        try {
            // Створюємо повернення в Stripe
            $refund = Refund::create([
                'payment_intent' => $transaction->txn_id,
                'amount' => $refund_amount_cents,
                'metadata' => [
                    'order_id' => $order_item->order_id,
                    'order_item_ids' => implode(',', $cancelled_items->pluck('id')->toArray()),
                ],
            ]);

            // Оновлюємо транзакцію з інформацією про повернення
            // $transaction->update([
            //     'refund_amount' => $refund_amount,
            //     'refund_status' => 'pending',
            //     'refund_id' => $refund->id,
            //     'is_refund' => true,
            // ]);
            // Create a new transaction for the refund
            Transaction::create([
                'transaction_type' => 'transaction',
                'user_id' => $transaction->user_id,
                'order_id' => $order_item->order_id,
                'order_item_id' => $order_item->id,
                'type' => 'credit',
                'txn_id' => $refund->id, // Use the refund ID as the transaction ID
                'amount' => $refund_amount,
                'fee' => 0, // Assuming no additional fee for the refund transaction
                'status' => 'pending',
                'currency_code' => $transaction->currency_code,
                'is_refund' => true,
                'refund_amount' => $refund_amount,
                'refund_status' => 'pending',
                'refund_id' => $refund->id,
                'transaction_date' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Stripe Refund Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getParcelItems(Request $request)
    {
        // Валідація вхідного order_item_id
        $validator = Validator::make($request->all(), [
            'order_item_id' => 'required|exists:order_items,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $orderItemId = $request->input('order_item_id');

        // Отримуємо parcel_id за order_item_id з таблиці parcel_items
        $parcelItem = Parcelitem::where('order_item_id', $orderItemId)->first();

        if (!$parcelItem) {
            return response()->json([
                'success' => false,
                'message' => 'Parcel item not found for the given order_item_id',
            ], 404);
        }

        $parcelId = $parcelItem->parcel_id;

        // Отримуємо всі parcel_items за parcel_id з пов’язаними OrderItems і Product
        $parcelItems = Parcelitem::where('parcel_id', $parcelId)
            ->with(['orderItem' => function ($query) {
                $query->with('product'); // Завантажуємо Product через OrderItems
            }])
            ->get();

        if ($parcelItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No parcel items found for this parcel_id',
            ], 404);
        }

        // Формуємо масив даних для відповіді
        $responseData = $parcelItems->map(function ($item) {
            $orderItem = $item->orderItem;
            $product = $orderItem->product ?? null; // Отримуємо Product

            return [
                'id' => $item->id,
                'order_item_id' => $item->order_item_id,
                'product_name' => $orderItem->product_name ?? 'N/A',
                'variant_name' => $orderItem->variant_name ?? null,
                'price_formatted' => $orderItem ? number_format($item->unit_price, 2) . ' ' . ($orderItem->order->order_payment_currency_code ?? '') : 'N/A',
                'image' => $product ? dynamic_image($product->image, 50) : asset('default-image.jpg'), // Додаємо зображення
            ];
        })->all();

        return response()->json([
            'success' => true,
            'data' => $responseData,
        ]);
    }

    public function confirmReceived(Request $request)
    {
        // Валідація вхідних даних
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:order_items,id',
            'items' => 'required|array',
            'items.*' => 'exists:parcel_items,id', // Перевіряємо, що кожен item є в parcel_items
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $orderItemId = $request->input('item_id');
        $selectedParcelItemIds = $request->input('items');

        // Перевіряємо, чи order_item_id належить до посилки
        $parcelItem = Parcelitem::where('order_item_id', $orderItemId)->first();
        if (!$parcelItem) {
            return response()->json([
                'success' => false,
                'message' => 'Parcel item not found for the given order_item_id',
            ], 404);
        }

        $parcelId = $parcelItem->parcel_id;

        // Отримуємо всі order_item_id, пов’язані з parcel_id
        $parcelItems = Parcelitem::where('parcel_id', $parcelId)
            ->whereIn('id', $selectedParcelItemIds)
            ->pluck('order_item_id')
            ->toArray();

        if (empty($parcelItems)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid order items found for the selected parcel items',
            ], 404);
        }

        // Оновлюємо поле is_completed для всіх вибраних order_item_id
        $updatedCount = OrderItems::whereIn('id', $parcelItems)
            ->where('is_completed', 0) // Оновлюємо тільки ті, що ще не підтверджені
            ->update(['is_completed' => 1]);

        if ($updatedCount > 0) {
            \Log::info("Successfully updated is_completed for order_item_ids: " . json_encode($parcelItems));
            return response()->json([
                'success' => true,
                'message' => 'Items confirmed successfully',
                'updated_count' => $updatedCount,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No items were updated. They may already be confirmed.',
            ], 400);
        }
    }
}
