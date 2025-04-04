<?php

namespace App\Http\Controllers;

use App\Models\CommissionDistribution;
use App\Models\OrderItems;
use App\Models\Product_variants;
use App\Models\SellerData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommissionController extends Controller
{
    /**
     * Split commissions between users based on order data
     */
    public function splitCommissionsBetweenUsers($data)
    {
        $order_id = $data['order_id'] ?? null;
        if (!$order_id) {
            Log::error('No order_id provided for commission distribution', $data);
            return;
        }

        // Get order items
        $order_items = OrderItems::where('order_id', $order_id)
            ->select('id', 'product_variant_id', 'quantity', 'seller_id')
            ->get();

        if ($order_items->isEmpty()) {
            Log::error('No order items found for order_id: ' . $order_id);
            return;
        }

        foreach ($order_items as $item) {
            // Get product variant data
            $variant = Product_variants::where('id', $item->product_variant_id)
                ->select('price', 'special_price', 'dealer_price')
                ->first();

            if (!$variant) {
                Log::warning('Product variant not found for product_variant_id: ' . $item->product_variant_id);
                continue;
            }

            // Get seller data
            $seller = SellerData::where('id', $item->seller_id)->select('id', 'user_id')->first();
            if (!$seller || !$seller->user_id) {
                Log::warning('Seller or user_id not found for seller_id: ' . $item->seller_id);
                continue;
            }

            $user = Auth::user();
            $seller_user = User::find($seller->user_id);

            // Calculate prices
            $price = $variant->special_price > 0 && $variant->special_price < $variant->price ? $variant->special_price : $variant->price;
            $dealer_price = $variant->dealer_price ?? 0;
            $quantity = $item->quantity;
            $seller_id = $seller->user_id;

            if ($dealer_price <= 0 || !$seller_id) {
                Log::warning('Invalid dealer_price or seller_id for order_item_id: ' . $item->id);
                continue;
            }

            // Calculate and distribute seller commission (95% of dealer_price)
            $seller_amount = $dealer_price * 0.95 * $quantity;
            $this->createCommissionRecord($order_id, $seller_id, $seller_amount, "95% from dealer_price for seller");

            // Calculate remaining 5% of dealer_price
            if ($seller_user->friends_code) {
                $manager = User::where('referral_code', $seller_user->friends_code)->first();
                if ($manager && $manager->role->name === 'manager') {
                    $manager_amount = $dealer_price * 0.01 * $quantity;
                    $this->createCommissionRecord($order_id, $manager->id, $manager_amount, "1% commission from dealer_price for manager");

                    $shareholders_amount = $dealer_price * 0.01 * $quantity;
                    $this->createCommissionRecord($order_id, 1, $shareholders_amount, "1% commission from dealer_price for shareholders", CommissionDistribution::USER_ID_SUB_SHAREHOLDERS);
                    $company_one_amount = $dealer_price * 0.02 * $quantity;
                    $this->createCommissionRecord($order_id, 1, $company_one_amount, "2% commission from dealer_price for base company account", CommissionDistribution::USER_ID_SUB_COMPANY_ONE);
                    $company_two_amount = $dealer_price * 0.01 * $quantity;
                    $this->createCommissionRecord($order_id, 1, $company_two_amount, "1% commission from dealer_price for hidden company account", CommissionDistribution::USER_ID_SUB_COMPANY_TWO);
                } else {
                    $shareholders_amount = $dealer_price * 0.01 * $quantity;
                    $this->createCommissionRecord($order_id, 1, $shareholders_amount, "1% commission from dealer_price for shareholders", CommissionDistribution::USER_ID_SUB_SHAREHOLDERS);
                    $company_one_amount = $dealer_price * 0.03 * $quantity;
                    $this->createCommissionRecord($order_id, 1, $company_one_amount, "3% commission from dealer_price for base company account", CommissionDistribution::USER_ID_SUB_COMPANY_ONE);
                    $company_two_amount = $dealer_price * 0.01 * $quantity;
                    $this->createCommissionRecord($order_id, 1, $company_two_amount, "1% commission from dealer_price for hidden company account", CommissionDistribution::USER_ID_SUB_COMPANY_TWO);
                }
            } else {
                $company_amount = $dealer_price * 0.05 * $quantity;
                $this->createCommissionRecord($order_id, 1, $company_amount, "5% commission from dealer_price for company");
            }

            // Calculate price difference for referrals
            $price_difference = ($price - $dealer_price) * $quantity;

            // 10% of price difference for company
            $company_amount = $price_difference * 0.1;
            $this->createCommissionRecord($order_id, 1, $company_amount, "10% commission from price_difference for company");

            // 90% of price difference for referrals
            $referral_amount = $price_difference * 0.9;
            if ($referral_amount > 0) {
                if (!$user->friends_code && $user->role->name === 'members') {
                    $this->createCommissionRecord($order_id, 1, $referral_amount, "User don't have friends_code. Referral commission for company");
                }
                if (!$user->friends_code && $user->role->name === 'manager') {
                    $this->createCommissionRecord($order_id, $user->id, $referral_amount, "User don't have friends_code and is manager. Referral commission for company");
                }
                if (!$user->friends_code && $user->role->name === 'dealer') {
                    $this->createCommissionRecord($order_id, $user->id, $referral_amount, "User don't have friends_code and is dealer. Referral commission for company");
                }
                if ($user->friends_code) {
                    $referral_user = User::where('referral_code', $user->friends_code)->first();
                    if ($referral_user) {
                        if ($referral_user->role->name === 'manager') {
                            $this->createCommissionRecord($order_id, $referral_user->id, $referral_amount, "Referral commission for user");
                        }
                        if ($referral_user->role->name === 'dealer') {
                            if ($referral_user->friends_code) {
                                $referral_manager = User::where('referral_code', $referral_user->friends_code)->first();
                                $this->createCommissionRecord($order_id, $referral_manager->id, $referral_amount * 0.5, "Referral commission for user");
                                $this->createCommissionRecord($order_id, $referral_user->id, $referral_amount * 0.5, "Referral commission for user");
                            } else {
                                $this->createCommissionRecord($order_id, $referral_user->id, $referral_amount, "Referral commission for user");
                            }
                        }
                    }
                }
            }
        }

        Log::info('Commissions distributed for order_id: ' . $order_id);
    }

    /**
     * Create a commission distribution record
     */
    private function createCommissionRecord($order_id, $user_id, $amount, $message, $user_id_sub = null)
    {
        CommissionDistribution::create([
            'order_id' => $order_id,
            'user_id' => $user_id,
            'user_id_sub' => $user_id_sub,
            'amount' => $amount,
            'message' => $message,
            'status' => CommissionDistribution::STATUS_PENDING,
        ]);
    }

    /**
     * Update commissions based on order cancellation
     */
    public function updateCommissions($order_id, $is_full_refund, $cancelled_items, $order_items)
    {
        $commissions = CommissionDistribution::where('order_id', $order_id)->get();

        if ($is_full_refund) {
            // Варіант 1: Повне повернення - змінюємо статус на canceled
            CommissionDistribution::where('order_id', $order_id)
                ->update(['status' => CommissionDistribution::STATUS_CANCELED]);
        } else {
            // Варіант 2: Часткове повернення - перераховуємо суми
            $total_order_amount = $order_items->sum('sub_total');
            $cancelled_amount = $cancelled_items->sum('sub_total');
            $remaining_ratio = ($total_order_amount - $cancelled_amount) / $total_order_amount;

            foreach ($commissions as $commission) {
                $original_amount = $commission->amount;
                $new_amount = $original_amount * $remaining_ratio;

                $commission->update([
                    'amount' => $new_amount,
                    'message' => $commission->message . " (Adjusted due to partial refund: original {$original_amount}, new {$new_amount})",
                ]);
            }
        }

        Log::info("Commissions updated for order_id: {$order_id}, full_refund: " . ($is_full_refund ? 'yes' : 'no'));
    }
}
