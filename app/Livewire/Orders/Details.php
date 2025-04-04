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

        if ($request['order_status'] == 'cancelled') {
            $order_item = OrderItems::find($order_item_ids[0]);
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

            // Отримуємо всі товари замовлення для перевірки повного/часткового повернення
            $order_items = OrderItems::where('order_id', $order_item->order_id)->get();
            $cancelled_items = OrderItems::whereIn('id', $order_item_ids)->get();
            $total_order_amount = $order_items->sum('sub_total');
            $cancelled_amount = $cancelled_items->sum('sub_total');
            $is_full_refund = $total_order_amount == $cancelled_amount;

            foreach ($order_item_ids as $order_item_id) {
                $order_item = OrderItems::find($order_item_id);

                if (!$order_item) {
                    continue;
                }

                update_order_item($order_item->id, $request['order_status'], 1);
                updateStock($order_item->product_variant_id, $order_item->quantity, 'plus');

                if ($refund_method === 'wallet') {
                    process_refund($order_item->id, $request['order_status']);
                } elseif ($refund_method === 'card') {
                    $this->processStripeRefund($request, $order_item);
                }
            }

            // Виклик методу з CommissionController для оновлення комісій
            $commissionController = new CommissionController();
            $commissionController->updateCommissions($order_item->order_id, $is_full_refund, $cancelled_items, $order_items);

            return response()->json([
                'error' => false,
                'message' => 'Order Items Cancelled Successfully',
            ]);
        }

        if ($request['order_status'] == 'returned') {
            $order_item = OrderItems::find($order_item_ids[0]);

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

            $request['order_status'] = 'return_request_pending';
            if (updateOrder(['status' => $request['order_status']], ['id' => $order_item->id], true)) {
                updateOrder(['active_status' => $request['order_status']], ['id' => $order_item->id], false);
                return response()->json([
                    'error' => false,
                    'message' => 'Order Status Updated Successfully',
                ]);
            }
        }
    }

    private function processStripeRefund(Request $request, $order_item)
    {
        $transaction = Transaction::where('order_id', $order_item->order_id)
            ->where('status', 'success')
            ->where('transaction_type', 'transaction')
            ->where('type', 'stripe')
            ->first();

        if (!$transaction || !$transaction->txn_id) {
            throw new \Exception('No valid Stripe transaction found for this order');
        }

        $order_items = OrderItems::where('order_id', $order_item->order_id)->get();
        $cancelled_items = OrderItems::whereIn('id', $request->input('order_item_id'))->get();

        $total_order_amount = $order_items->sum('sub_total'); // Повна сума замовлення
        $cancelled_amount = $cancelled_items->sum('sub_total'); // Сума скасованих товарів

        // Ініціалізація Stripe
        $stripe = new \App\Libraries\Stripe();
        StripeConfig::setApiKey($stripe->getSecretKey()); // Використовуємо getSecretKey

        $refund_amount = $cancelled_amount;
        if ($total_order_amount == $cancelled_amount) {
            // Повне повернення: додаємо комісію
            $refund_amount += $transaction->fee;
        }

        // Конвертуємо суму в центи (Stripe працює з найменшими одиницями валюти)
        $refund_amount_cents = (int) ($refund_amount * 100);

        try {
            // Створюємо повернення в Stripe
            $refund = Refund::create([
                'payment_intent' => $transaction->txn_id, // ID оригінального платежу
                'amount' => $refund_amount_cents,         // Сума повернення в центах
                'metadata' => [
                    'order_id' => $order_item->order_id,
                    'order_item_ids' => implode(',', $cancelled_items->pluck('id')->toArray()),
                ],
            ]);

            // Оновлюємо транзакцію з інформацією про повернення
            $transaction->update([
                'refund_amount' => $refund_amount,
                'refund_status' => 'pending', // Статус спочатку pending
                'refund_id' => $refund->id,   // Зберігаємо ID повернення від Stripe
                'is_refund' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Stripe Refund Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
