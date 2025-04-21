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
            $refund_ratio = $refund_amount / $total_order_amount;
            $proportional_fee = $transaction->fee * $refund_ratio;
            $refund_amount += $proportional_fee;
        }
        Log::info("Wallet refund processed for order_item_id: {$orderItem->id}");
        if ($refund_method === 'wallet') {
            process_refund($orderItem->id, 'returned', 'order_items');
            Log::info("Wallet refund processed for order_item_id: {$orderItem->id}");
        } elseif ($refund_method === 'original_payment' && $transaction->type === 'stripe') {
            $this->processStripeRefund($orderItem, $transaction, $refund_amount);
            Log::info("Stripe refund processed for order_item_id: {$orderItem->id}");
        }

        update_order_item($orderItem->id, 'returned', 0);

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

            $transaction->update([
                'refund_amount' => $refund_amount,
                'refund_status' => 'pending',
                'refund_id' => $refund->id,
                'is_refund' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe Refund Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
