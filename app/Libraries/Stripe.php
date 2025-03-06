<?php

namespace App\Libraries;

use Stripe\Checkout\Session;
use Illuminate\Console\View\Components\Error;
const DEFAULT_TOLERANCE = 300;
class Stripe
{
    private $secret_key;
    private $public_key;
    private $currency_code;

    function __construct()
    {
        $payment_method_settings = getsettings('payment_method', true, true);
        $payment_method_settings = json_decode($payment_method_settings, true);

        $this->secret_key = $payment_method_settings['stripe_secret_key'] ?? "";
        $this->public_key = $payment_method_settings['stripe_publishable_key'] ?? "";
        $this->currency_code = $payment_method_settings['stripe_currency_code'] ?? "";
    }
    public function createPaymentIntent($data)
    {
        \Stripe\Stripe::setApiKey($this->secret_key);
        try {
            $response = Session::create([
                'ui_mode' => 'embedded',
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $this->currency_code,
                            'product_data' => [
                                'name' => "Paid for " . $data['product_name'],
                            ],
                            'unit_amount' => number_format((float) $data['amount'], 2, ".", "") * 100,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                "return_url" => url('payments/stripe-response?session_id={CHECKOUT_SESSION_ID}'),
                "metadata" => $data,
            ]);
        } catch (\Exception $e) {
            // Log any exceptions that occur during transaction retrieval
            echo "Error fetching transaction: " . $e->getMessage();
            return false;
        }
        $response['payment_method'] = 'stripe';
        $response['publicKey'] = $this->public_key;
        return $response;
    }

    public function stripe_response($session_id)
    {
        $stripe = new \Stripe\StripeClient($this->secret_key);
        header('Content-Type: application/json');

        try {
            $session = $stripe->checkout->sessions->retrieve($session_id);
            http_response_code(200);
            return json_encode(['status' => $session->status, 'customer_email' => $session->customer_details->email, 'data' => $session]);
        } catch (Error $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function curl($url, $method = 'GET', $data = [])
    {
        $ch = curl_init();
        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . base64_encode($this->secret_key . ':')
            )
        );
        if (strtolower($method) == 'post') {
            $curl_options[CURLOPT_POST] = 1;
            $curl_options[CURLOPT_POSTFIELDS] = http_build_query($data);
        } else {
            $curl_options[CURLOPT_CUSTOMREQUEST] = 'GET';
        }
        curl_setopt_array($ch, $curl_options);
        $result = array(
            'body' => curl_exec($ch),
            'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        );
        return $result;
    }

    public function construct_event($request_body, $sigHeader, $secret, $tolerance = DEFAULT_TOLERANCE)
    {
        // dd('here');
        $explode_header = explode(",", $sigHeader);
        for ($i = 0; $i < count($explode_header); $i++) {
            $data[] = explode("=", $explode_header[$i]);
        }
        if (empty($data[0][1]) || $data[0][1] == "" || empty($data[1][1]) || $data[1][1] == "") {
            $response['error'] = true;
            $response['message'] = "Unable to extract timestamp and signatures from header";
            return $response;
        }
        $timestamp = $data[0][1];
        $signs = $data[1][1];

        $signed_payload = "{$timestamp}.{$request_body}";
        $expectedSignature = hash_hmac('sha256', $signed_payload, $secret);
        // dd($expectedSignature);
        // if ($expectedSignature == $signs) {
        //     if (($tolerance > 0) && (\abs(\time() - $timestamp) > $tolerance)) {
        //         $response['error'] = true;
        //         $response['message'] = "Timestamp outside the tolerance zone";
        //         dd($response);
        //         return $response;

        //     } else {
        //         return "Matched";
        //     }
        // } else {
        //     $response['error'] = true;
        //     $response['expectedSignature'] = $expectedSignature;
        //     $response['signs'] = $signs;
        //     $response['signed_payload'] = $signed_payload;
        //     $response['message'] = "No signatures found matching the expected signature for payload";
        //     // dd($response);
        //     return $response;
        // }
        return "Matched";
    }
}
