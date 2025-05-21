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
                'type' => $request['type'] ?? "",
                'order_id' => $request['order_id'] ?? "",
            ];
        }
        $data['user_id'] = Auth::user()->id ?? 0;
        $checkout_session = $this->stripe->createPaymentIntent($data);

        return $checkout_session;
    }

    public function stripe_response(Request $request)
    {
        if (null !== $request->query("session_id")) {
            $system_settings = getSettings('system_settings', true);
            $system_settings = json_decode($system_settings, true);
            $stripe_credentials = getsettings('payment_method', true);
            $stripe_credentials = json_decode($stripe_credentials, true);

            $session_id = $request->query("session_id");
            $res = $this->stripe->stripe_response($session_id);
            $res = json_decode($res, true);
            Log::info('Stripe response $res = ', $res);

            if ($res['status'] == "complete") {
                $newRequest = new Request();
                $payment_intent_id = $res['data']['payment_intent'];
                $res['data']['metadata']['stripe_payment_id'] = $res['data']['payment_intent'];
                $res['data']['metadata']['payment_method'] = 'stripe';

                // Отримуємо деталі платежу і комісію
                $fee = 0;
                try {
                    \Stripe\Stripe::setApiKey($stripe_credentials['stripe_secret_key']);
                    $paymentIntent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
                    if ($paymentIntent->status === 'succeeded') {
                        $charge = \Stripe\Charge::retrieve($paymentIntent->latest_charge);
                        $balanceTransaction = \Stripe\BalanceTransaction::retrieve($charge->balance_transaction);
                        $fee = $balanceTransaction->fee / 100; // Переводимо з центів у долари
                    }
                } catch (\Exception $e) {
                    Log::error('Stripe payment retrieval failed in stripe_response: ' . $e->getMessage());
                }

                // Додаємо fee до метаданих
                $res['data']['metadata']['fee'] = $fee;

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
                }

                // Pay for existing order after set delivery charge
                if (isset($res['data']['metadata']['type']) && $res['data']['metadata']['type'] == "pay_for_order") {
                    $orderController = app(OrderController::class);
                    $result = $orderController->pay_for_order($newRequest, $transactionController);
                    $result = json_decode($result->getContent(), true);
                    if ($result['error'] == false) {
                        return redirect(url('payments?response=order_success'));
                    }
                    return redirect(url('payments?response=order_failed'));
                }

                /** default place new order */
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
