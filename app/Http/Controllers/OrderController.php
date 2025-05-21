<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Auth;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use App\Models\CommissionDistribution;
use App\Models\User;

class OrderController extends Controller
{
    public function pay_for_order(Request $request, TransactionController $transactionController)
    {
        if ($request->has('res')) {
            $res = $request->input('res');
            $request = new Request($res);
            $request['final_total'] = $request['amount'];
        }
        $user_id = Auth::user()->id ?? ""; // айді поточного користувача
        if ($user_id == "") {
            $response = [
                'error' => true,
                'message' => 'Please Login first.',
                'code' => 102,
            ];
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $total = $request['final_total'];
        $order_id = $request['order_id'];

        // Обробка і збереження транзакції
        $transaction_id = $request['stripe_payment_id'];
        $status = 'success';
        $message = 'Payment Successfully';

        $stripe_credentials = getsettings('payment_method', true);
        $stripe_credentials = json_decode($stripe_credentials, true);


        // Додаємо запит до Stripe для отримання деталей платежу
        $fee = $request['fee'] ?? 0;
        if ($fee == 0) {
            try {
                \Stripe\Stripe::setApiKey($stripe_credentials['stripe_secret_key']);
                $paymentIntent = \Stripe\PaymentIntent::retrieve($transaction_id);
                if ($paymentIntent->status === 'succeeded') {
                    // Отримуємо пов’язаний Charge для отримання комісії
                    $charge = \Stripe\Charge::retrieve($paymentIntent->latest_charge);
                    $balanceTransaction = \Stripe\BalanceTransaction::retrieve($charge->balance_transaction);
                    $fee = $balanceTransaction->fee / 100; // Комісія в доларах (переводимо з центів)
                }
            } catch (\Exception $e) {
                \Log::error('Stripe payment retrieval failed: ' . $e->getMessage());
            }
        }
        $data = new Request([
            'status' => $status ?? "awaiting",
            'txn_id' => $transaction_id ?? null,
            'message' => $message ?? 'Payment Is Pending',
            'order_id' => $order_id,
            'user_id' => $user_id,
            'type' => $request['payment_method'],
            'amount' => $total,
            'fee' => round($fee, 2) ?? 0,
        ]);
        \Log::alert('Transaction data before store: ' . json_encode($data->all()));
        $transactionController->store($data);

        // Якщо замовлення створено, розподіляємо кошти по рахунках користувачів
        $data['order_id'] = $request['order_id'];
        $commissionController = new CommissionController();
        $commissionController->splitCommissionsBetweenUsers($data);

        // Оновлення замовлення
        try {
            $order = Order::find($order_id);
            $order->update([
                'is_delivery_charge_returnable' => isset($request['delivery_charge']) && !empty($request['delivery_charge']) && $request['delivery_charge'] > 0 ? 1 : 0,
                'payment_method' => $request['payment_method'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating order', 'error' => $e->getMessage()], 500);
        }

        // Оновлення статусів для товарів в замовленні
        $updatedCount = 0;
        $errors = [];
        $order_items = OrderItems::where('order_id', $order_id)->get();
        foreach ($order_items as $orderItem) {
            if (updateOrder(['status' => 'received'], ['id' => $orderItem->id], true, 'order_items')) {
                updateOrder(['active_status' => 'received'], ['id' => $orderItem->id], false, 'order_items');
            }
            // $updateResult = update_order_item($orderItem->id, 'received', 0);
            // if ($updateResult) {
            //     $updatedCount++;
            // } else {
            //     $errors[] = "Failed to update order item ID {$orderItem->id}";
            // }
        }




        return response()->json(['error' => false]);
    }
}
