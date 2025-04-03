<?php

namespace App\Http\Controllers;

use App\Libraries\Stripe;
use App\Libraries\Phonepe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TransactionController;
use App\Libraries\Razorpay;
use Illuminate\Support\Facades\Log;

class PaymentsController extends Controller
{

    public $phonepe;
    public $stripe;
    public $razorpay;

    public function __construct()
    {
        $this->phonepe = new Phonepe();
        $this->stripe = new Stripe();
        $this->razorpay = new Razorpay();
    }

    public function phonepe(Request $request)
    {
        $user_id = $request['user_id'];
        $final_total = $request['final_total'];
        $mobile = $request['mobile'];
        $transaction_id = time() . "" . rand("100", "999");
        $data = array(
            'merchantTransactionId' => $transaction_id,
            'merchantUserId' => $user_id,
            'amount' => $final_total * 100,
            'redirectUrl' => customUrl(url('payments/response')),
            'redirectMode' => 'POST',
            'callbackUrl' => url("webhook/phonepe_webhook"),
            'mobileNumber' => $mobile,
        );
        $res = $this->phonepe->pay($data);
        if ($res) {
            $response = [
                'error' => false,
                'message' => $res['message'],
                'message' => $res['message'],
                'transaction_id' => $res['data']['merchantTransactionId'] ?? "",
                'payment_url' => $res['data']['instrumentResponse']['redirectInfo']['url'] ?? "",
                'data' => $res,
            ];
            return json_encode($response);
        }
    }

    public function stripe(Request $request)
    {
        if (isset($request['type']) && $request['type'] == "wallet") {
            $data = [
                'amount' => $request['amount'] ?? "",
                'product_name' => $request['product_name'] ?? "",
                'type' => $request['type'] ?? "",
            ];
        } else {
            // Створюємо замовлення
            $order_data = [
                'product_variant_id' => $request['product_variant_id'] ?? "",
                'cart_product_type' => $request['product_type'] ?? "",
                'quantity' => $request['quantity'] ?? "",
                'store_id' => $request['store_id'] ?? "",
                'delivery_charge' => $request['delivery_charge'] ?? "",
                'discount' => $request['discount'] ?? "",
                'promo_code_id' => $request['promo_code_id'] ?? "",
                'promo_code' => $request['promo_code'] ?? "",
                'user_id' => Auth::user()->id ?? "",
                'is_wallet_used' => $request['is_wallet_used'] ?? "",
                'wallet_balance_used' => $request['wallet_balance_used'] ?? "",
                'mobile' => $request['address-mobile'] ?? "",
                'email' => $request['email'] ?? "",
                'address_id' => $request['selected_address_id'] ?? "",
                'delivery_time' => $request['delivery_time'] ?? "",
                'delivery_date' => $request['delivery_date'] ?? "",
                'longitude' => $request['longitude'] ?? "",
                'latitude' => $request['latitude'] ?? "",
                'order_note' => $request['order_note'] ?? "",
                'payment_method' => $request['payment_method'] ?? "",
                'product_type' => $request['product_type'] ?? "",
                'order_payment_currency_code' => $request['currency_code'] ?? "",
            ];

            $res = placeOrder($order_data, 1);
            $order_id = $res['order_id'] ?? "";

            $data = [
                'amount' => $request['amount'] ?? "",
                'product_name' => $request['product_name'] ?? "",
                'selected_address_id' => $request['selected_address_id'] ?? "",
                'address-mobile' => $request['address-mobile'] ?? "",
                'delivery_date' => $request['delivery_date'] ?? "",
                'delivery_time' => $request['delivery_time'] ?? "",
                'order_note' => $request['order_note'] ?? "",
                'payment_method' => $request['payment_method'] ?? "",
                'product_type' => $request['product_type'] ?? "",
                'promo_set' => $request['promo_set'] ?? "",
                'discount' => $request['discount'] ?? "",
                'is_wallet_used' => $request['is_wallet_used'] ?? "",
                'is_delivery_charge_returnable' => $request['is_delivery_charge_returnable'] ?? "",
                'wallet_balance_used' => $request['wallet_balance_used'] ?? "",
                'delivery_charge' => $request['delivery_charge'] ?? "",
                'currency_code' => $request['currency_code'] ?? "",
                'promo_code' => $request['promo_code'] ?? "",
                'email' => $request['email'] ?? "",
                'order_id' => $order_id,
            ];
        }
        $data['user_id'] = Auth::user()->id ?? 0;
        $checkout_session = $this->stripe->createPaymentIntent($data);

        return $checkout_session;
    }

    public function stripe_response(Request $request)
    {
        if (null !== $request->query("session_id")) {
            $session_id = $request->query("session_id");
            $res = $this->stripe->stripe_response($session_id);
            $res = json_decode($res, true);
            Log::info('Stripe response $res = ', $res);
            if ($res['status'] == "complete") {
                $newRequest = new Request();
                $res['data']['metadata']['stripe_payment_id'] = $res['data']['payment_intent'];
                $res['data']['metadata']['payment_method'] = 'stripe';
                $newRequest->replace([
                    'res' => $res['data']['metadata']
                ]);
                $transactionController = app(TransactionController::class);
                if (isset($res['data']['metadata']['type']) && $res['data']['metadata']['type'] == "wallet") {
                    $walletController = app(WalletController::class);
                    $result = $walletController->refill($newRequest, $transactionController);
                    $result = json_decode($result->getContent(), true);
                    if ($result['error'] == false) {
                        return redirect(url('payments?response=wallet_success'));
                    }
                    return redirect(url('payments?response=wallet_failed'));
                    if ($result['error'] == false) {
                        return redirect(url('payments?response=wallet_success'));
                    }
                    return redirect(url('payments?response=wallet_failed'));
                }
                $cartController = app(CartController::class);
                $result = $cartController->place_order($newRequest, $transactionController);
                $result = json_decode($result->getContent(), true);
                if ($result['error'] == false) {
                    Log::info('Get result stripe_response', $result);

                    return redirect(url('payments?response=order_success'));
                }
                Log::info('Get result with error stripe_response', $result);
                return redirect(url('payments?response=order_failed'));
            }
            return redirect(url('payments?response=order_failed'));
        }
        $response = [
            'error' => true,
            'message' => "request not allowed"
        ];
        Log::info('returned stripe response', ["stripe_response: response=>" . $response]);
        return json_encode($response);
    }

    public function razorpay(Request $request)
    {
        $amount = intval($request['amount'] * 100);
        $res = $this->razorpay->create_order($amount);
        return $res;
    }

    // метод для розрахунку комісії Stripe
    public function calculateStripeFee(Request $request)
    {
        $amount = $request->input('amount');

        if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
            return response()->json([
                'error' => true,
                'message' => 'Invalid amount provided',
            ]);
        }

        $feeData = $this->stripe->calculateFee($amount);

        return response()->json([
            'error' => false,
            'fee' => $feeData['fee'],
            'total_with_fee' => $feeData['total_with_fee'],
        ]);
    }
}
