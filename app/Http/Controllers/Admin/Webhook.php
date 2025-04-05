<?php

namespace App\Http\Controllers\Admin;

use App\Libraries\Phonepe;
use App\Models\Transaction;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TransactionController;
use App\Libraries\Paystack;
use App\Libraries\Razorpay;
use App\Libraries\Stripe;
use App\Models\Setting;

class Webhook extends Controller
{

    public $transactionController = "";

    function __construct()
    {
        $this->transactionController = app(TransactionController::class);
    }

    public function phonepe_webhook(Request $request)
    {

        $phonepe = new Phonepe;
        $request = file_get_contents('php://input');
        $request = json_decode($request);
        $request = $request->response ?? "";
        Log::alert("phonepe webhook=>" . $request);
        if (!empty($request)) {

            $request = base64_decode($request);
            $request = json_decode($request, 1);
            $txn_id = $request['data']['merchantTransactionId'] ?? "";
            if (!empty($txn_id)) {
                $transaction = fetchDetails('transactions', ['txn_id' => $txn_id]);
                $amount = $request['data']['amount'] / 100;
            } else {
                $amount = 0;
            }
            if (!empty($transaction)) {
                $user_id = $transaction[0]->user_id;
                $transaction_type = (isset($transaction[0]->transaction_type)) ? $transaction[0]->transaction_type : "";
                $order_id = (isset($transaction[0]->order_id)) ? $transaction[0]->order_id : "";
            } else {
                Log::alert("Phonepe transaction id not found in local database ==>" . $request);
                return;
            }
            $status = $request['code'] ?? "";
            $check_status = $phonepe->check_status($txn_id);
            Log::alert("Phonepe check_status" . json_encode($check_status));
            if ($check_status['code'] = 'INTERNAL_SERVER_ERROR') {
                Log::alert("Phonepe INTERNAL SERVER ERROR!! retry to check status");
                $check_status = $phonepe->check_status($txn_id);
            }
            if ($check_status['success'] == true) {
                if ($status == 'PAYMENT_SUCCESS') {
                    $data['status'] = "success";
                    if ($transaction_type == 'transaction') {
                        $data['message'] = "Payment received successfully";
                        updateDetails(['active_status' => "received"], ['order_id' => $order_id], 'order_items');
                        $order_status = json_encode(array(array('received', date("d-m-Y h:i:sa"))));
                        updateDetails(['status' => $order_status], ['order_id' => $order_id], 'order_items');

                        // place order custome notification on payment success

                        sendCustomNotificationOnPaymentSuccess($order_id, $user_id);

                        $response['error'] = false;
                        $response['message'] = "Payment received successfully";

                        return response()->json($response);
                    } else {
                        $data['status'] = "success";
                        if (!updateBalance($amount, $user_id, 'add')) {
                            Log::alert("Phonepe Webhook | couldn't update in wallet balance  -->" . $request);
                        }
                        $data['message'] = "Wallet refill successful";
                    }
                    Transaction::where('txn_id', $txn_id)->update($data);
                    $response['error'] = false;
                    $response['message'] = "Wallet refill successful";
                    return response()->json($response);
                } else if ($status == "BAD_REQUEST" || $status == "AUTHORIZATION_FAILED" || $status == "PAYMENT_ERROR" || $status == "TRANSACTION_NOT_FOUND" || $status == "PAYMENT_DECLINED" || $status == "TIMED_OUT") {
                    $data['status'] = "failed";
                    if ($transaction_type == 'transaction') {
                        $data['message'] = "Payment couldn't be processed!";
                        updateDetails(['active_status' => "cancelled"], ['order_id' => $order_id], 'order_items');
                        $order_status = json_encode(array(array('cancelled', date("d-m-Y h:i:sa"))));
                        updateDetails(['status' => $order_status], ['order_id' => $order_id], 'order_items');
                        $order_items = fetchDetails('order_items', ['order_id' => $order_id]);
                        $product_variant_ids = [];
                        $qty = [];
                        foreach ($order_items as $items) {
                            array_push($product_variant_ids, $items->product_variant_id);
                            array_push($qty, $items->quantity);
                        }
                        $order_detail = fetchDetails('orders', ["id" => $order_id], 'wallet_balance');
                        $wallet_balance = $order_detail[0]->wallet_balance;
                        if ($wallet_balance > 0) {
                            updateBalance($wallet_balance, $user_id, "add");
                        }
                        updateStock($product_variant_ids, $qty, 'plus');
                    } else {
                        $data['message'] = "Wallet could not be recharged!";
                        $response['error'] = false;
                        $response['message'] = "Wallet could not be recharged!";

                        return response()->json($response);
                    }
                    Transaction::where('txn_id', $txn_id)->update($data);
                    return;
                }
            }
            return;
        }
        $response['error'] = false;
        $response['message'] = "phonepe No Request Found";

        return response()->json($response);
        Log::alert("phonepe No Request Found=>" . $request);
    }

    public function paypal_webhook(Request $request) //remaining
    {
        $res = $request->all();
        $request = json_encode($res);
        Log::alert("paypal webhook=>" . $request);
        if (!empty($res)) {
            $txn_id = $res['resource']['purchase_units'][0]['reference_id'] ?? "";
            if (!empty($txn_id)) {
                $transaction = fetchDetails('transactions', ['txn_id' => $txn_id]);
                $amount = $res['resource']['purchase_units'][0]['amount']['value'];
            } else {
                $amount = 0;
            }
            if (!empty($transaction)) {
                $user_id = $transaction[0]->user_id;
                $transaction_type = (isset($transaction[0]->transaction_type)) ? $transaction[0]->transaction_type : "";
                $order_id = (isset($transaction[0]->order_id)) ? $transaction[0]->order_id : "";
            } else {
                Log::alert("paypal transaction id not found in local database ==>" . $request);
                return;
            }
            if ($amount != number_format($transaction[0]->amount, 2, '.', '')) {
                Log::alert("paypal order amount doesn't match ==>" . $request);
                return;
            }
            $status = $res['resource']['status'] ?? "";
            $intent = $res['resource']['intent'];
            if ($status == 'COMPLETED' && $intent == "CAPTURE") {
                $data['status'] = "success";
                if ($transaction_type == 'transaction') {
                    $data['message'] = "Payment received successfully";
                    updateDetails(['active_status' => "received"], ['order_id' => $order_id], 'order_items');
                    $order_status = json_encode(array(array('received', date("d-m-Y h:i:sa"))));
                    updateDetails(['status' => $order_status], ['order_id' => $order_id], 'order_items');
                } else {
                    $data['message'] = "Wallet refill successful";
                    if (!updateBalance($amount, $user_id, 'add')) {
                        Log::alert("paypal Webhook | couldn\'t update in wallet balance  -->" . $request);
                    }
                }
                $this->transactionController->update_transaction($txn_id, $data);
            }
            return;
        }
        Log::alert("Paypal No Request Found=>" . $request);
    }

    public function paystack_webhook(Request $request)
    {
        $system_settings = getsettings('system_settings', true);
        $system_settings = json_decode($system_settings, true);
        $paystack = new Paystack;
        $credentials = getsettings('payment_method', true);
        $credentials = json_decode($credentials, true);
        $paystack_key_id = $credentials['paystack_key_id'];
        $secret_key = $credentials['paystack_secret_key'];

        $request_body = file_get_contents('php://input');
        $event = json_decode($request_body, true);
        Log::alert("paystack webhook=>" . $request);

        $order_id = $event['data']['metadata']['order_id'];
        if (is_numeric($order_id)) {
            if (!empty($event['data'])) {

                $txn_id = (isset($event['data']['reference'])) ? $event['data']['reference'] : "";
                if (isset($txn_id) && !empty($txn_id)) {
                    $transaction = fetchdetails('transactions', ['txn_id' => $txn_id], '*');
                    if (!empty($transaction)) {
                        $order_id = $transaction[0]->order_id;
                        $user_id = $transaction[0]->user_id;
                    } else {
                        $order_id = $event['data']['metadata']['order_id'];
                        $order_data = fetchorders($order_id);
                        $user_id = $order_data['order_data'][0]->user_id;
                    }
                }
                $amount = $event['data']['amount'] / 100;
                $currency = $event['data']['currency'];
            } else {
                $order_id = 0;
                $amount = 0;
                $currency = (isset($event['data']['currency'])) ? $event['data']['currency'] : "";
            }
        }

        /* Wallet refill has unique format for order ID - wallet-refill-user-{user_id}-{system_time}-{3 random_number}  */
        if (!is_numeric($order_id) && strpos($order_id, "wallet-refill-user") !== false) {

            $temp = explode("-", $order_id);
            if (isset($temp[3]) && is_numeric($temp[3]) && !empty($temp[3] && $temp[3] != '')) {
                $user_id = $temp[3];
            } else {
                $user_id = 0;
            }
        }


        if ($event['event'] == 'charge.success') {
            if (!empty($order_id)) { /* To do the wallet recharge if the order id is set in the pattern */

                if (strpos($order_id, "wallet-refill-user") !== false) {
                    $txn_id = (isset($event['data']['reference'])) ? $event['data']['reference'] : "";
                    $amount = $event['data']['amount'] / 100;
                    $data['transaction_type'] = "wallet";
                    $data['user_id'] = $user_id;
                    $data['order_id'] = $order_id;
                    $data['type'] = "credit";
                    $data['txn_id'] = $txn_id;
                    $data['amount'] = $amount;
                    $data['status'] = "success";
                    $data['message'] = "Wallet refill successful";
                    Transaction::create($data);

                    if (updatebalance($amount, $user_id, 'add')) {
                        $response['error'] = false;
                        $response['transaction_status'] = 'success';
                        $response['message'] = "Wallet recharged successfully!";
                    } else {
                        $response['error'] = true;
                        $response['transaction_status'] = 'failed';
                        $response['message'] = "Wallet could not be recharged!";
                        Log::alert('Paystack Webhook | wallet recharge failure --> ' . var_export($event, true));
                    }
                    return response()->json($response);
                } else {

                    /* process the order and mark it as received */
                    $order = fetchOrders($order_id, '', '', '', '', '', 'o.id', 'DESC');
                    Log::alert('Paystack Webhook | order --> ' . var_export($order, true));

                    if (isset($order['order_data'][0]->user_id)) {
                        $user = fetchdetails('users', ['id' => $order['order_data'][0]->user_id]);


                        $overall_total = array(
                            'total_amount' => $order['order_data'][0]->total,
                            'delivery_charge' => $order['order_data'][0]->delivery_charge,
                            'tax_amount' => $order['order_data'][0]->total_tax_amount,
                            'tax_percentage' => $order['order_data'][0]->total_tax_percent,
                            'discount' => $order['order_data'][0]->promo_discount,
                            'wallet' => $order['order_data'][0]->wallet_balance,
                            'final_total' => $order['order_data'][0]->final_total,
                            'otp' => $order['order_data'][0]->otp,
                            'address' => $order['order_data'][0]->address,
                            'payment_method' => $order['order_data'][0]->payment_method
                        );

                        /* No need to add because the transaction is already added just update the transaction status */
                        if (!empty($transaction)) {
                            $transaction_id = $transaction[0]->id;
                            updateDetails(['status' => 'success'], ['id' => $transaction_id], 'transactions');
                        } else {
                            /* add transaction of the payment */
                            $amount = ($event['data']['amount']);
                            $data = [
                                'transaction_type' => 'transaction',
                                'user_id' => $user_id,
                                'order_id' => $order_id,
                                'type' => 'paystack',
                                'txn_id' => $txn_id,
                                'amount' => $amount,
                                'status' => 'success',
                                'message' => 'order placed successfully',
                            ];
                            Transaction::create($data);
                        }



                        $status = json_encode(array(array('received', date("d-m-Y h:i:sa"))));
                        updateDetails(['status' => $status], ['order_id' => $order_id], 'order_items');
                        updateDetails(['active_status' => 'received'], ['order_id' => $order_id], 'order_items');



                        sendCustomNotificationOnPaymentSuccess($order_id, $user_id);

                        Log::alert('Paystack Webhook inner Success --> ' . var_export($event, true));
                    }
                    Log::alert('Paystack Webhook order Success --> ' . var_export($event, true));
                }
            } else {
                /* No order ID found / sending 304 error to payment gateway so it retries wenhook after sometime*/
                Log::alert('Paystack Webhook | Order id not found --> ' . var_export($event, true));
            }

            $response['error'] = false;
            $response['transaction_status'] = $event['event'];
            $response['message'] = "Transaction successfully done";
            Log::alert('Paystack Transaction Successfully --> ' . var_export($event, true));
            return response()->json($response);
        } else if ($event['event'] == 'charge.dispute.create') {
            if (!empty($order_id) && is_numeric($order_id)) {
                $order = fetchOrders($order_id, '', '', '', '', '', 'o.id', 'DESC');

                if ($order['order_data']['0']->active_status == 'received' || $order['order_data']['0']->active_status == 'processed') {
                    updateDetails(['active_status' => 'awaiting'], ['order_id' => $order_id], 'order_items');
                }

                if (!empty($transaction)) {
                    $transaction_id = $transaction[0]->id;
                    updateDetails(['status' => 'pending'], ['id' => $transaction_id], 'transactions');
                }

                Log::alert('Paystack Transaction is Pending --> ' . var_export($event, true));
            }
        } else {

            if (!empty($order_id) && is_numeric($order_id)) {
                updateDetails(['active_status' => 'cancelled'], ['order_id' => $order_id], 'order_items');
            }
            /* No need to add because the transaction is already added just update the transaction status */
            if (!empty($transaction)) {
                $transaction_id = $transaction[0]['id'];
                updateDetails(['status' => 'failed'], ['id' => $transaction_id], 'transactions');
            }

            $response['error'] = true;
            $response['transaction_status'] = $event['event'];
            $response['message'] = "Transaction could not be detected.";
            Log::alert('Paystack Webhook | Transaction could not be detected --> ' . var_export($event, true));
            return response()->json($response);
        }
    }
    public function razorpay_webhook(Request $request)
    {
        $system_settings = getsettings('system_settings', true);
        $system_settings = json_decode($system_settings, true);
        $razorpay = new Razorpay;
        $request = file_get_contents('php://input');
        if ($request === false || empty($request)) {
            $this->edie("Error in reading Post Data");
        }
        $request = json_decode($request, true);

        $payment_method_settings = getsettings('payment_method', true);
        $payment_method_settings = json_decode($payment_method_settings, true);

        $key_id = $payment_method_settings['razorpay_key_id'] ?? "";
        $secret_key = $payment_method_settings['razorpay_secret_key'] ?? "";
        $secret_hash = $payment_method_settings['razorpay_webhook_secret_key'] ?? "";
        define('RAZORPAY_SECRET_KEY', $secret_hash);
        Log::alert('Razorpay IPN POST --> ' . var_export($request, true));
        Log::alert('Razorpay IPN SERVER --> ' . var_export($_SERVER, true));
        $http_razorpay_signature = isset($_SERVER['HTTP_X_RAZORPAY_SIGNATURE']) ? $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] : "";
        $txn_id = (isset($request['payload']['payment']['entity']['id'])) ? $request['payload']['payment']['entity']['id'] : "";
        if (!empty($request['payload']['payment']['entity']['id'])) {
            if (!empty($txn_id)) {
                $transaction = fetchDetails('transactions', ['txn_id' => $txn_id], '*');
            }
            $amount = $request['payload']['payment']['entity']['amount'];
            $amount = ($amount / 100);
        } else {
            $amount = 0;
            $currency = (isset($request['payload']['payment']['entity']['currency'])) ? $request['payload']['payment']['entity']['currency'] : "";
        }
        if (!empty($transaction)) {
            $order_id = $transaction[0]->order_id;
            Log::alert('razorpay Webhook | transaction order id --> ' . var_export($order_id, true));
            $user_id = $transaction[0]->user_id;
        } else {
            $order_id = 0;
            $order_id = (isset($request['payload']['order']['entity']['notes']['order_id'])) ? $request['payload']['order']['entity']['notes']['order_id'] : $request['payload']['payment']['entity']['notes']['order_id'];
            Log::alert('razorpay Webhook | webhook order id --> ' . var_export($order_id, true));
        }
        if ($http_razorpay_signature) {
            if ($request['event'] == 'payment.authorized') {
                $currency = (isset($request['payload']['payment']['entity']['currency'])) ? $request['payload']['payment']['entity']['currency'] : "INR";
                $response = $razorpay->capture_payment($amount * 100, $txn_id, $currency);
            }
            if ($request['event'] == 'payment.captured' || $request['event'] == 'order.paid') {
                if ($request['event'] == 'order.paid') {
                    $order_id = $request['payload']['order']['entity']['receipt'];

                    $order_data = fetchOrders($order_id, '', '', '', '', '', 'o.id', 'DESC');
                    $user_id = (isset($order_data['order_data'][0]->user_id)) ? $order_data['order_data'][0]->user_id : "";
                }
                if (!empty($order_id)) {
                    /* To do the wallet recharge if the order id is set in the patter */
                    if (strpos($order_id, "wallet-refill-user") !== false) {
                        if (!is_numeric($order_id) && strpos($order_id, "wallet-refill-user") !== false) {
                            $temp = explode("-", $order_id);
                            if (isset($temp[3]) && is_numeric($temp[3]) && !empty($temp[3] && $temp[3] != '')) {
                                $user_id = $temp[3];
                            } else {
                                $user_id = 0;
                            }
                        }

                        $data['transaction_type'] = "wallet";
                        $data['user_id'] = $user_id;
                        $data['order_id'] = $order_id;
                        $data['type'] = "credit";
                        $data['txn_id'] = $txn_id;
                        $data['amount'] = $amount;
                        $data['status'] = "success";
                        $data['message'] = "Wallet refill successful";
                        Log::alert('Razorpay user ID -  transaction data--> ' . var_export($data, true));
                        Transaction::create($data);
                        Log::alert('Razorpay user ID -  transaction data--> ' . var_export($txn_id, true));

                        if (updateBalance($amount, $user_id, 'add')) {
                            $response['error'] = false;
                            $response['transaction_status'] = $request['event'];
                            $response['message'] = "Wallet recharged successfully!";
                            Log::alert('Razorpay user ID - Wallet recharged successfully --> ' . var_export($order_id, true));
                        } else {
                            $response['error'] = true;
                            $response['transaction_status'] = $request['event'];
                            $response['message'] = "Wallet could not be recharged!";
                            Log::alert('Razorpay user ID - Wallet recharged successfully --> ' . var_export($request['event'], true));
                        }
                        return response()->json($response);
                    } else {

                        /* process the order and mark it as received */
                        $order = fetchOrders($order_id, '', '', '', '', '', 'o.id', 'DESC');

                        Log::alert('Razorpay order -   data--> ' . var_export($order, true));
                        if (isset($order['order_data'][0]->user_id)) {
                            $user = fetchdetails('users', ['id' => $order['order_data'][0]->user_id]);
                            $overall_total = array(
                                'total_amount' => $order['order_data'][0]->total,
                                'delivery_charge' => $order['order_data'][0]->delivery_charge,
                                'tax_amount' => $order['order_data'][0]->total_tax_amount,
                                'tax_percentage' => $order['order_data'][0]->total_tax_percent,
                                'discount' => $order['order_data'][0]->promo_discount,
                                'wallet' => $order['order_data'][0]->wallet_balance,
                                'final_total' => $order['order_data'][0]->final_total,
                                'otp' => $order['order_data'][0]->otp,
                                'address' => $order['order_data'][0]->address,
                                'payment_method' => $order['order_data'][0]->payment_method
                            );

                            /* No need to add because the transaction is already added just update the transaction status */
                            if (!empty($transaction)) {
                                $transaction_id = $transaction[0]->id;
                                updateDetails(['status' => 'success'], ['id' => $transaction_id], 'transactions');
                            } else {
                                /* add transaction of the payment */
                                $amount = ($request['payload']['payment']['entity']['amount'] / 100);
                                $data = [
                                    'transaction_type' => 'transaction',
                                    'user_id' => $order['order_data'][0]->user_id,
                                    'order_id' => $order_id,
                                    'type' => 'razorpay',
                                    'txn_id' => $txn_id,
                                    'amount' => $amount,
                                    'status' => 'success',
                                    'message' => 'order placed successfully',
                                ];
                                Transaction::create($data);
                            }
                            updateDetails(['active_status' => 'received'], ['order_id' => $order_id], 'order_items');
                            $status = json_encode(array(array('received', date("d-m-Y h:i:sa"))));
                            updateDetails(['status' => $status], ['order_id' => $order_id], 'order_items');

                            // place order custome notification on payment success
                            $user_id = (isset($order_data['order_data'][0]->user_id)) ? $order_data['order_data'][0]->user_id : "";
                            sendCustomNotificationOnPaymentSuccess($order_id, $user_id);

                            $product_variant_ids = [];
                            $qty = [];
                            $order_items = fetchDetails('order_items', ['order_id' => $order_id]);
                            foreach ($order_items as $items) {
                                array_push($product_variant_ids, $items->product_variant_id);
                                array_push($qty, $items->quantity);
                            }
                            updatestock($product_variant_ids, $qty, 'plus');
                        }
                    }
                } else {
                    Log::alert('Razorpay Order id not found --> ' . var_export($request, true));
                    /* No order ID found */
                }

                $response['error'] = false;
                $response['transaction_status'] = $request['event'];
                $response['message'] = "Transaction successfully done";
                return response()->json($response);
            } elseif ($request['event'] == 'payment.failed') {

                if (!empty($order_id)) {
                    updateDetails(['active_status' => 'cancelled'], ['order_id' => $order_id], 'order_items');
                }
                /* No need to add because the transaction is already added just update the transaction status */
                if (!empty($transaction)) {
                    $transaction_id = $transaction[0]['id'];
                    updateDetails(['status' => 'failed'], ['id' => $transaction_id], 'transactions');
                }
                $response['error'] = true;
                $response['transaction_status'] = $request['event'];
                $response['message'] = "Transaction is failed. ";
                Log::alert('Razorpay Webhook | Transaction is failed --> ' . var_export($request['event'], true));
                return response()->json($response);
            } elseif ($request['event'] == 'payment.authorized') {
                if (!empty($order_id)) {
                    updateDetails(['active_status' => 'awaiting'], ['order_id' => $order_id], 'order_items');
                }
            } elseif ($request['event'] == "refund.processed") {
                //Refund Successfully
                $transaction = fetchdetails('transactions', ['txn_id' => $request['payload']['refund']['entity']['payment_id']]);
                if (empty($transaction)) {
                }
                process_refund($transaction[0]['id'], $transaction[0]['status']);
                $response['error'] = false;
                $response['transaction_status'] = $request['event'];
                $response['message'] = "Refund successfully done. ";
                Log::alert('Razorpay Webhook | Transaction is failed --> ' . var_export($request['event'], true));
                return response()->json($response);
            } elseif ($request['event'] == "refund.failed") {
                $response['error'] = true;
                $response['transaction_status'] = $request['event'];
                $response['message'] = "Refund is failed. ";
                Log::alert('Razorpay Webhook | Payment refund failed --> ' . var_export($request['event'], true));
                return response()->json($response);
            } else {
                $response['error'] = true;
                $response['transaction_status'] = $request['event'];
                $response['message'] = "Transaction could not be detected.";
                Log::alert('Razorpay Webhook | Transaction could not be detected --> ' . var_export($request['event'], true));
                return response()->json($response);
            }
        } else {
            Log::alert('razorpay Webhook | Invalid Server Signature  --> ' . var_export($request['event'], true));
            return false;
        }
    }
    public function stripe_webhook(Request $request)
    {
        $system_settings = getsettings('system_settings', true);
        $system_settings = json_decode($system_settings, true);
        $stripe = new Stripe;
        $credentials = getsettings('payment_method', true);
        $credentials = json_decode($credentials, true);

        $request_body = file_get_contents('php://input');
        $event = json_decode($request_body, false);

        Log::alert("stripe webhook=>" . $request_body);

        // Отримуємо підпис із заголовка
        $http_stripe_signature = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : "";

        // Верифікація вебхука з використанням секретного ключа
        $webhook_secret = $credentials['stripe_webhook_secret_key'];
        $result = $stripe->construct_event($request_body, $http_stripe_signature, $webhook_secret);

        if ($result !== "Matched") {
            Log::alert('Stripe Webhook | Invalid Server Signature --> ' . var_export($result, true));
            return response()->json([
                'error' => true,
                'message' => 'Webhook signature verification failed',
            ], 400);
        }

        // Обробка базової інформації про транзакцію
        if (!empty($event->data->object)) {
            $txn_id = $event->data->object->payment_intent ?? '';
            Log::alert('Stripe txn_id --> ' . $txn_id);
            if (!empty($txn_id)) {
                $transaction = fetchDetails('transactions', ['txn_id' => $txn_id], '*');
                Log::alert('transaction --> ' . var_export($transaction, true));
                if (isset($transaction) && !empty($transaction)) {
                    $order_id = $transaction[0]->order_id;
                    $user_id = $transaction[0]->user_id;
                } else {
                    $order_id = isset($event->data->object->metadata) && property_exists($event->data->object->metadata, 'order_id')
                        ? $event->data->object->metadata->order_id
                        : 0;
                    if (is_numeric($order_id) && $order_id > 0) {
                        $order_data = fetchOrders($order_id, '', '', '', '', '', 'o.id', 'DESC');
                        $user_id = $order_data['order_data'][0]->user_id;
                    } else {
                        $user_id = 0;
                    }
                }
            }
            $amount = $event->data->object->amount ?? 0;
            $currency = $event->data->object->currency ?? '';
            $balance_transaction = $event->data->object->balance_transaction ?? '';
        } else {
            $order_id = 0;
            $amount = 0;
            $currency = (isset($event->data->object->currency)) ? $event->data->object->currency : "";
            $balance_transaction = 0;
        }

        // Обробка формату order_id для поповнення гаманця
        if (empty($order_id) && !empty($event->data->object->metadata) && property_exists($event->data->object->metadata, 'order_id')) {
            $order_id = $event->data->object->metadata->order_id;
        }

        if (!is_numeric($order_id) && strpos($order_id, "wallet-refill-user") !== false) {
            $temp = explode("-", $order_id);
            if (isset($temp[3]) && is_numeric($temp[3]) && !empty($temp[3] && $temp[3] != '')) {
                $user_id = $temp[3];
            } else {
                $user_id = 0;
            }
        }

        // Обробка подій
        switch ($event->type) {
            case 'charge.succeeded':
                break;
                $fee = 0;
                if (!empty($balance_transaction)) {
                    try {
                        $stripeClient = new \Stripe\StripeClient($credentials['stripe_secret_key']);
                        $balance_txn = $stripeClient->balanceTransactions->retrieve($balance_transaction);
                        $fee = $balance_txn->fee / 100; // Комісія в доларах
                        Log::alert("Stripe Fee retrieved: " . $fee);
                    } catch (\Exception $e) {
                        Log::alert('Stripe Webhook | Failed to retrieve balance transaction: ' . $e->getMessage());
                    }
                }

                if (!empty($order_id)) {
                    $order = fetchOrders($order_id, '', '', '', '', '', 'o.id', 'DESC');
                    if (isset($order['order_data'][0]->user_id)) {
                        $user_id = $order['order_data'][0]->user_id;
                        if (!empty($transaction)) {
                            updateDetails(['status' => 'success', 'fee' => $fee], ['txn_id' => $txn_id], 'transactions');
                        } else {
                            $data = [
                                'transaction_type' => 'transaction',
                                'user_id' => $user_id,
                                'order_id' => $order_id,
                                'type' => 'stripe',
                                'txn_id' => $txn_id,
                                'amount' => ($amount - $fee) / 100,
                                'fee' => $fee,
                                'status' => 'success',
                                'message' => 'Payment Successfully',
                            ];
                            Transaction::create($data);
                        }
                        updateDetails(['active_status' => 'received'], ['order_id' => $order_id], 'order_items');
                        $status = json_encode([['received', date("d-m-Y h:i:sa")]]);
                        updateDetails(['status' => $status], ['order_id' => $order_id], 'order_items');
                        sendCustomNotificationOnPaymentSuccess($order_id, $user_id);
                    }
                }
                return response()->json([
                    'error' => false,
                    'transaction_status' => $event->type,
                    'message' => "Transaction successfully done. Fee: $fee",
                ]);

            case 'charge.failed':
                if (!empty($order_id)) {
                    updateDetails(['active_status' => 'cancelled'], ['order_id' => $order_id], 'order_items');
                }
                if (!empty($transaction)) {
                    $transaction_id = $transaction[0]['id'];
                    updateDetails(['status' => 'failed'], ['id' => $transaction_id], 'transactions');
                }
                return response()->json([
                    'error' => true,
                    'transaction_status' => $event->type,
                    'message' => "Transaction is failed.",
                ]);

            case 'charge.pending':
                return response()->json([
                    'error' => false,
                    'transaction_status' => $event->type,
                    'message' => "Waiting for customer to finish transaction",
                ]);

            case 'charge.expired':
                if (!empty($order_id)) {
                    updateDetails(['active_status' => 'cancelled'], ['order_id' => $order_id], 'order_items');
                }
                if (!empty($transaction)) {
                    $transaction_id = $transaction[0]['id'];
                    updateDetails(['status' => 'expired'], ['id' => $transaction_id], 'transactions');
                }
                return response()->json([
                    'error' => true,
                    'transaction_status' => $event->type,
                    'message' => "Transaction is expired.",
                ]);

            case 'refund.created':
                $refundId = $event->data->object->id;
                $transaction = Transaction::where('refund_id', $refundId)->first();
                if ($transaction) {
                    $transaction->update([
                        'is_refund' => 1,
                        'refund_status' => $event->data->object->status,
                    ]);
                    Log::alert("Refund created for transaction {$transaction->id}: status = {$event->data->object->status}");
                } else {
                    Log::alert("Refund created but no transaction found with refund_id: {$refundId}");
                }
                return response()->json([
                    'error' => false,
                    'transaction_status' => $event->type,
                    'message' => "Refund initiated successfully",
                ]);

            case 'refund.updated':
                $refundId = $event->data->object->id;
                $transaction = Transaction::where('refund_id', $refundId)->first();
                if ($transaction) {
                    $transaction->update([
                        'refund_status' => $event->data->object->status,
                    ]);
                    Log::alert("Refund updated for transaction {$transaction->id}: status = {$event->data->object->status}");
                } else {
                    Log::alert("Refund updated but no transaction found with refund_id: {$refundId}");
                }
                return response()->json([
                    'error' => false,
                    'transaction_status' => $event->type,
                    'message' => "Refund status updated",
                ]);

            case 'charge.refunded':
                $paymentIntentId = $event->data->object->payment_intent; // Отримуємо Payment Intent ID
                $transaction = Transaction::where('txn_id', $paymentIntentId)->first(); // Шукаємо за payment_intent

                if ($transaction) {
                    $refundAmount = $event->data->object->amount_refunded / 100; // Переводимо з центів у долари
                    $transaction->update([
                        'is_refund' => 1,
                        'refund_amount' => $refundAmount,
                        'refund_status' => 'succeeded', // Оновлюємо статус
                    ]);

                    // Оновлюємо статус замовлення, якщо є order_id
                    // if (!empty($transaction->order_id)) {
                    //     updateDetails(['active_status' => 'cancelled'], ['order_id' => $transaction->order_id], 'order_items');
                    // }
                    Log::alert("Charge refunded for transaction {$transaction->id}: amount = {$refundAmount}");
                } else {
                    Log::alert("Charge refunded but no transaction found with payment_intent: {$paymentIntentId}");
                }

                return response()->json([
                    'error' => false,
                    'transaction_status' => $event->type,
                    'message' => "Transaction is refunded. Amount: " . ($refundAmount ?? 'unknown'),
                ]);

            default:
                Log::alert('Stripe Webhook | Transaction could not be detected --> ' . var_export($event->type, true));
                return response()->json([
                    'error' => true,
                    'transaction_status' => $event->type,
                    'message' => "Transaction could not be detected.",
                ]);
        }
    }
}
