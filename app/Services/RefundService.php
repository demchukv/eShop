<?php
// app/Services/RefundService.php
namespace App\Services;

use App\Http\Controllers\CommissionController;
use App\Models\OrderItems;
use App\Models\ReturnRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Stripe\Refund;
use Stripe\Stripe as StripeConfig;

class RefundService
{
    public function processReturnRefund(ReturnRequest $returnRequest)
    {
        \Log::debug('return request status: ' . json_encode($returnRequest->all()));
        if ($returnRequest->status != 4) {
            throw new \Exception('Return request must be in "Returned" status.');
        }

        $orderItem = $returnRequest->orderItem;
        if (!$orderItem) {
            throw new \Exception('Order item not found for return request.');
        }

        $transaction = Transaction::where('order_id', $orderItem->order_id)
            ->where('status', 'success')
            ->first();

        if (!$transaction) {
            throw new \Exception('No successful transaction found for this order.');
        }

        $refund_method = $returnRequest->refund_method;
        Log::info("Processing refund for return request: return_request_id={$returnRequest->id}, refund_method={$refund_method}");

        if ($transaction->transaction_type === 'wallet' && $refund_method !== 'wallet') {
            throw new \Exception('For wallet payments, refund can only be made to wallet.');
        }

        if ($transaction->transaction_type === 'transaction' && $transaction->type !== 'stripe' && $refund_method !== 'wallet') {
            throw new \Exception('For this payment method, refund can only be made to wallet.');
        }

        $total_order_amount = $orderItem->order->orderItems->sum('sub_total');
        $refund_amount = $returnRequest->refund_amount;

        if ($transaction->fee > 0 && $total_order_amount > 0) {
            // $refund_ratio = $refund_amount / $total_order_amount;
            $refund_ratio = $refund_amount / $transaction->amount;
            $proportional_fee = $transaction->fee * $refund_ratio;
            $refund_amount += $proportional_fee;
        }

        if ($refund_method === 'wallet') {
            process_refund($orderItem->id, 'returned', 'order_items');
            Log::info("Wallet refund processed for order_item_id: {$orderItem->id}");
        } elseif ($refund_method === 'original_payment' && $transaction->type === 'stripe') {
            $this->processStripeRefund($orderItem, $transaction, $refund_amount);
            Log::info("Stripe refund processed for order_item_id: {$orderItem->id}");
        }

        // update_order_item($orderItem->id, 'returned', 1, false);
        if (updateOrder(['status' => 'returned'], ['id' => $returnRequest->order_item_id], true, 'order_items')) {
            updateOrder(['active_status' => 'returned'], ['id' => $returnRequest->order_item_id], false, 'order_items');
        }

        $commissionController = new CommissionController();
        $orderItems = $orderItem->order->orderItems;
        $is_full_refund = $total_order_amount == $refund_amount;
        $cancelled_items = collect([$orderItem]);
        $commissionController->updateCommissions($orderItem->order_id, $is_full_refund, $cancelled_items, $orderItems);
    }

    protected function processStripeRefund($orderItem, $transaction, $refund_amount)
    {
        $stripe = new \App\Libraries\Stripe();
        StripeConfig::setApiKey($stripe->getSecretKey());

        $refund_amount_cents = (int) ($refund_amount * 100);

        try {
            $refund = Refund::create([
                'payment_intent' => $transaction->txn_id,
                'amount' => $refund_amount_cents,
                'metadata' => [
                    'order_id' => $orderItem->order_id,
                    'order_item_id' => $orderItem->id,
                ],
            ]);

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
                'order_id' => $orderItem->order_id,
                'order_item_id' => $orderItem->id,
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
            Log::error('Stripe Refund Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
