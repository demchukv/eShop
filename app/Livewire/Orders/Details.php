<?php

namespace App\Livewire\Orders;

use App\Models\OrderItems;
use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Refund;
use Stripe\Stripe as StripeConfig;
use App\Http\Controllers\CommissionController;
use Carbon\Carbon;

class Details extends Component
{
    public function render(Request $request)
    {
        $store_id = session('store_id');
        $order_id = $request->segment(2);
        $user = Auth::user();

        $user_orders = fetchOrders(order_id: $order_id, user_id: $user->id, store_id: $store_id);
        if (count($user_orders['order_data']) < 1) {
            abort(404);
        }
        $user_orders_transaction_data = json_decode(json_encode($user_orders['order_data']), true);

        $couriersFilePath = storage_path('app/aftership_couriers_cache.json');
        $main_transaction = Transaction::where('order_id', $order_id)->first();

        $couriersMap = \Cache::remember('aftership_couriers', 60 * 60 * 24, function () use ($couriersFilePath) {
            $couriersData = file_exists($couriersFilePath) ? json_decode(file_get_contents($couriersFilePath), true) : ['couriers' => []];
            return collect($couriersData['couriers'])->pluck('name', 'slug')->all();
        });

        foreach ($user_orders_transaction_data as &$user_order) {
            foreach ($user_order['order_items'] as &$user_order_item) {
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
            'transaction' => $main_transaction
        ])->title("Orders Detail |");
    }


    public function update_order_item_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_status' => 'required',
            'order_item_id' => 'required|array',
            'order_item_id.*' => 'exists:order_items,id',
            'refund_method' => 'sometimes|in:wallet,card'
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

        \Log::info("Запит на оновлення статусу: order_status={$request['order_status']}, order_item_ids=" . json_encode($order_item_ids) . ", refund_method={$refund_method}");

        if ($request['order_status'] == 'cancelled') {
            $order_item = OrderItems::find($order_item_ids[0]);
            if (!$order_item) {
                return response()->json([
                    'error' => true,
                    'message' => 'Order item not found',
                ]);
            }

            $transaction = Transaction::where('order_id', $order_item->order_id)
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

                if ($refund_method === 'wallet') {
                    process_refund($order_item->id, $request['order_status']);
                } elseif ($refund_method === 'card' && $transaction->type === 'stripe') {
                    $this->processStripeRefund($request, $order_item, $transaction, $total_order_amount, $cancelled_amount);
                }
            }
            return response()->json([
                'error' => true,
                'message' => 'Stop processing order_items',
            ]);

            $commissionController = new CommissionController();
            $commissionController->updateCommissions($order_item->order_id, $is_full_refund, $cancelled_items, $order_items);

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
            $refund_ratio = $cancelled_amount / $total_order_amount; // Співвідношення суми повернення до загальної суми
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
            $transaction->update([
                'refund_amount' => $refund_amount,
                'refund_status' => 'pending',
                'refund_id' => $refund->id,
                'is_refund' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Stripe Refund Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
