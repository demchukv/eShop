<?php

namespace App\Http\Controllers\Seller;


use App\Models\Pos;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Location;
use App\Models\Modifier;
use App\Models\CartAddOn;
use App\Models\Order_item;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Product_variants;
use App\Models\User_transaction;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\Partner\CategoryController;
use App\Http\Controllers\Partner\UserTransactionController;
use App\Models\Address;
use App\Models\OrderItems;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::user()->id;

        $seller_id = Seller::where('user_id', $user_id)->value('id');

        $categories = getSellerCategories($seller_id);

        $currency = Currency::where('is_default', 1)->value('symbol');

        $countries = fetchdetails('countries', '', 'name');

        $zipcodes = fetchdetails('zipcodes', '', 'zipcode');

        $search = trim($request->input('search', ''));

        $users = User::where('role_id', 2)
            ->where(function ($query) use ($search) {
                $query->where('username', 'LIKE', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->get();

        return view('seller.pages.forms.pos', ['categories' => $categories, 'zipcodes' => $zipcodes, 'users' => $users, 'currency' => $currency, 'countries' => $countries]);
    }

    public function register_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'password' => 'required|string',
            'mobile' => 'required|min:5|unique:users,mobile',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->all(),
                'data' => [],
            ]);
        } else {
            $mobile = $request->input('mobile');
            $username = $request->input('name');
            $password = bcrypt($request->input('password'));

            $user = User::create([
                'mobile' => $mobile,
                'username' => $username,
                'password' => $password,
                'role_id' => 2,
            ]);

            User::where('mobile', $mobile)->update(['active' => 1]);

            Address::create([
                'user_id' => $user->id,
                'mobile' => $mobile,
                'name' => $username,
                'type' => 'Home ',
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'country' => $request->input('country'),
                'address' => $request->input('address'),
            ]);

            if ($user) {
                $error = false;
                $message = 'Registered Successfully';
                return response()->json([
                    'error' => $error,
                    'message' => $message,
                ]);
            }
        }
    }

    public function get_users(Request $request)
    {
        $search = trim($request->input('search', ''));

        $users = User::where('role_id', 2)->where('active', 1)
            ->where(function ($query) use ($search) {
                $query->where('username', 'LIKE', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%');
            })
            ->get();



        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->id,
                'text' => $user->username . ' | ' . $user->mobile . ' | ' . $user->email,
                'mobile' => $user->mobile,
                'email' => $user->email,
                'username' => $user->username,
            ];
        }

        return $data;
    }


    public function get_products(Request $request)
    {
        $store_id = getStoreId();
        $max_limit = 25;
        if (Auth::user()->role_id === 4) {
            $user_id = Auth::user()->id;
            $seller_id = Seller::where('user_id', $user_id)->value('id');
        }

        $category_id = (isset($_GET['category_id']) && !empty($_GET['category_id']) && is_numeric($_GET['category_id'])) ? request('category_id') : "";
        $limit = (isset($_GET['limit']) && !empty($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] <= $max_limit) ? request('limit') : $max_limit;
        $offset = (isset($_GET['offset']) && !empty($_GET['offset']) && is_numeric($_GET['offset'])) ? $_GET['offset'] : 0;
        
        $sort = (isset($_GET['sort']) && !empty($_GET['sort'])) ? $_GET['sort'] : 'p.id';
        $order = (isset($_GET['order']) && !empty($_GET['order'])) ? $_GET['order'] : 'desc';
        $filter['search'] = (isset($_GET['search']) && !empty($_GET['search'])) ? $_GET['search'] : '';
        $filter['show_only_active_products'] = 1;
        $filter['show_only_physical_product'] = 1;
        $filterBy = $request->input('filter_by') ?? 'p.id';
        // Fetch the products and count the total
        $products = fetchProduct('', $filter, '', $category_id, $limit, $offset, $sort, $order, '', '', $seller_id, '', $store_id);

        $response['error'] = (!empty($products)) ? 'false' : 'true';
        $response['message'] = (!empty($products)) ? "Products fetched successfully" : "No products found";
        $response['products'] = (!empty($products)) ? $products : [];
        $response['total'] = $products['total'] ?? 0;

        print_r(json_encode($response));
    }

    public function get_combo_products()
    {
        $store_id = getStoreId();
        $max_limit = 25;

        $user_id = Auth::user()->id;
        $seller_id = Seller::where('user_id', $user_id)->value('id');


        $limit = (isset($_GET['limit']) && !empty($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] <= $max_limit) ? request('limit') : $max_limit;
        $offset = (isset($_GET['offset']) && !empty($_GET['offset']) && is_numeric($_GET['offset'])) ? $_GET['offset'] : 0;
        $sort = (isset($_GET['sort']) && !empty($_GET['sort'])) ? $_GET['sort'] : 'p.id';
        $order = (isset($_GET['order']) && !empty($_GET['order'])) ? $_GET['order'] : 'desc';
        $filter['search'] = (isset($_GET['search']) && !empty($_GET['search'])) ? $_GET['search'] : '';
        $filter['show_only_active_products'] = 1;

        $products = fetchComboProduct('', $filter, '', $limit, $offset, $sort, $order, '', '', $seller_id, $store_id);

        $response['error'] = (!empty($products)) ? 'false' : 'true';
        $response['message'] = (!empty($products)) ? labels('admin_labels.products_fetched_successfully', 'Products fetched successfully')
            :
            labels('admin_labels.no_products_found', 'No products found');
        $response['products'] = (!empty($products)) ? $products : [];

        $response['total'] = $products['total'] ?? 0;

        print_r(json_encode($response));
    }

    public function place_order(Request $request)
    {

        $store_id = getStoreId();
        if (!$request->has('data') || empty($request->input('data'))) {
            $response = [
                'error' => true,
                'message' => 'Cart is empty!!',

            ];
            return response()->json($response);
        }

        if (empty($request->input('payment_method'))) {
            $response = [
                'error' => true,
                'message' =>
                labels('admin_labels.select_at_least_one_payment_method', 'Please select at least one payment method'),

            ];
            return response()->json($response);
        }


        if ($request->has('payment_method') && !empty($request->input('payment_method')) && $request->input('payment_method') == "other" && empty($request->input('payment_method_name'))) {
            $response = [
                'error' => true,
                'message' =>
                labels('admin_labels.enter_payment_method_name_prompt', 'Please enter payment method name'),
                'csrfHash' => csrf_token(),
                'data' => []
            ];
            return response()->json($response);
        }

        $post_data = json_decode($request->data, true);


        if (isset($post_data) && !empty($post_data)) {
            foreach ($post_data as $key => $data) {
                if (!isset($data['variant_id']) || empty($data['variant_id'])) {
                    return response()->json([
                        'error' => true,
                        'message' =>
                        labels('admin_labels.variant_id_required', 'The variant ID field is required'),

                    ]);
                }

                if (!isset($data['quantity']) || empty($data['quantity'])) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Please enter valid quantity for ' . $data['title'],

                    ]);
                }
            }
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Cart is empty!!',

            ]);
        }
        $product_variant_id = array_column($post_data, "variant_id");
        $product_type = array_column($post_data, "product_type");
        $quantity = array_column($post_data, "quantity");
        $user_id = $request->user_id;

        $currency = Currency::where('is_default', 1)->value('code');
        $place_order_data = array();
        $product_type = $product_type;
        $place_order_data['product_variant_id'] = implode(",", $product_variant_id);
        $place_order_data['quantity'] = implode(",", $quantity);
        $place_order_data['user_id'] = $user_id;
        $user_mobile = fetchDetails("users", ['id' => $user_id], "mobile");
        $place_order_data['mobile'] = isset($user_mobile) && !empty($user_mobile) ? $user_mobile[0]->mobile : '';
        $place_order_data['address_id'] = $request->input('address_id');
        $place_order_data['is_wallet_used'] = 0;
        $place_order_data['delivery_charge'] = $request->input('delivery_charges');
        $place_order_data['discount'] = $request->input('discount');
        $place_order_data['is_delivery_charge_returnable'] = 0;
        $place_order_data['wallet_balance_used'] = 0;
        $place_order_data['active_status'] = "delivered";
        $place_order_data['is_pos_order'] = 1;
        $place_order_data['store_id'] = $store_id;
        $place_order_data['order_payment_currency_code'] = $currency;
        $payment_method_name = (isset($request->payment_method_name) && !empty($request->payment_method_name)) ? $request->payment_method_name : NULL;
        $place_order_data['payment_method'] = (isset($request->payment_method) && !empty($request->payment_method) && $request->payment_method != "other") ? $request->payment_method : $payment_method_name;
        $txn_id = (isset($request->txn_id) && !empty($request->txn_id)) ? $request->txn_id : NULL;


        $check_current_stock_status = validateStock($product_variant_id, $quantity, $product_type);

        if ($check_current_stock_status['error'] == true) {
            $response = [
                'error' => true,
                'message' => $check_current_stock_status['message'],
                'data' => []
            ];
            return response()->json($response);
        }


        $data = array(
            'product_variant_id' => implode(",", $product_variant_id),
            'qty' => implode(",", $quantity),
            'user_id' => $user_id,
            'store_id' => $store_id,
            'product_type' => implode(",", $product_type)
        );

        if (addToCart($data) != true) {
            $response = [
                'error' => true,
                'message' =>
                labels('admin_labels.items_not_added', 'Items are Not Added'),
                'data' => []
            ];
            return response()->json($response);
        }

        $cart = getCartTotal($user_id, false, 0, "", $store_id);

        if (empty($cart)) {
            $response = [
                'error' => true,
                'message' =>
                labels('admin_labels.cart_is_empty', 'Your Cart is empty.'),
                'data' => []
            ];
            return response()->json($response);
        }

        $final_total = $cart['overall_amount'];

        $place_order_data['final_total'] = floatval($final_total) - floatval($cart['delivery_charge']);
        $place_order_data['cart_product_type'] = implode(",", $product_type);

        $res = placeOrder($place_order_data);

        if (isset($res) && !empty($res)) {
            // creating transaction record for card payments
            $trans_data = [
                'transaction_type' => 'transaction',
                'user_id' => $user_id,
                'order_id' => $res->original['order_id'],
                'type' => strtolower($place_order_data['payment_method']),
                'txn_id' => $txn_id,
                'amount' => $final_total,
                'status' => "success",
                'message' =>
                labels('admin_labels.order_delivered_successfully', 'Order Delivered Successfully'),
            ];
            Transaction::forceCreate($trans_data);
        }
        $data['order_id'] = $res->original['order_id'];
        $response = [
            'error' => false,
            'message' =>
            labels('admin_labels.order_delivered_successfully', 'Order Delivered Successfully'),
            'data' => $res,
        ];
        return response()->json($response);
    }

    public function combo_place_order(Request $request)
    {

        $store_id = getStoreId();
        if (!$request->has('data') || empty($request->input('data'))) {
            $response = [
                'error' => true,
                'message' => 'Pass the data',
                'csrfHash' => csrf_token(),
                'data' => []
            ];
            return response()->json($response);
        }

        if (empty($request->input('payment_method'))) {
            $response = [
                'error' => true,
                'message' => labels('admin_labels.select_at_least_one_payment_method', 'Please select at least one payment method'),
                'csrfHash' => csrf_token(),
                'data' => []
            ];
            return response()->json($response);
        }

        if (!$request->has('user_id') || empty($request->input('user_id'))) {
            $response = [
                'error' => true,
                'message' => labels('admin_labels.select_customer_prompt', 'Please select the customer!'),
                'csrfHash' => csrf_token(),
                'data' => []
            ];
            return response()->json($response);
        }


        if ($request->has('payment_method') && !empty($request->input('payment_method')) && $request->input('payment_method') == "other" && empty($request->input('payment_method_name'))) {
            $response = [
                'error' => true,
                'message' => labels('admin_labels.enter_payment_method_name_prompt', 'Please enter payment method name'),
                'csrfHash' => csrf_token(),
                'data' => []
            ];
            return response()->json($response);
        }

        $post_data = json_decode($request->data, true);


        if (isset($post_data) && !empty($post_data)) {
            foreach ($post_data as $key => $data) {
                if (!isset($data['id']) || empty($data['id'])) {
                    return response()->json([
                        'error' => true,
                        'message' =>
                        labels('admin_labels.product_id_required', 'The product ID field is required'),
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_token(),
                        'data' => []
                    ]);
                }

                if (
                    !isset($data['quantity']) || empty($data['quantity'])
                ) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Please enter valid quantity for ' . $data['title'],
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_token(),
                        'data' => []
                    ]);
                }
            }
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Pass the data',
                'data' => []
            ]);
        }

        $product_id = array_column($post_data, "id");
        $quantity = array_column($post_data, "quantity");
        $user_id = $request->user_id;

        $payment_method_name = (isset($request->payment_method_name) && !empty($request->payment_method_name)) ? $request->payment_method_name : NULL;
        $txn_id = (isset($request->txn_id) && !empty($request->txn_id)) ? $request->txn_id : NULL;

        $check_current_stock_status = validateComboStock($product_id, $quantity);

        if ($check_current_stock_status['error'] == true) {
            $response = [
                'error' => true,
                'message' => $check_current_stock_status['message'],
                'data' => []
            ];
            return response()->json($response);
        }


        $final_total = !empty($request->input('final_total')) ? $request->input('final_total') : '';
        $sub_total = !empty($request->input('sub_total')) ? $request->input('sub_total') : '';
        $delivery_charges = !empty($request->input('delivery_charges')) ? $request->input('delivery_charges') : '';
        $discount = !empty($request->input('discount')) ? $request->input('discount') : '';
        $payment_method = $request->input('payment_method');


        $currency = Currency::where('is_default', 1)->value('code');


        $order_payment_currency_data = fetchDetails('currencies', ['code' => $currency], ['id', 'exchange_rate']);
        $user_mobile = fetchDetails("users", ['id' => $user_id], "mobile");

        $order_data = [
            'user_id' => $user_id,
            'mobile' => $user_mobile[0]->mobile,
            'address_id' => $request->input('address_id'),
            'address' => $request->input('address'),
            'total' => $sub_total,
            'final_total' => $final_total,
            'total_payable' => $final_total,
            'discount' => $discount,
            'delivery_charge' => $delivery_charges,
            'is_delivery_charge_returnable' => 0,
            'wallet_balance' => 0,
            'type' => 'combo_place_order',
            'store_id' => $store_id,
            'payment_method' => $payment_method != '' ? $payment_method : $payment_method_name,
            'is_pos_order' => 1,
            'order_payment_currency_code' => $currency,
            'order_payment_currency_id' => $order_payment_currency_data[0]->id ?? '',
            'order_payment_currency_conversion_rate' => $order_payment_currency_data[0]->exchange_rate,
            'base_currency_code' => $currency,
        ];

        $order = Order::forceCreate($order_data);

        $order->save();
        $combo_data = $request->data;
        $combo_data = json_decode($combo_data, true);
        $userId = Auth::user()->id;
        $seller_id = Seller::where('user_id', $userId)->value('id');

        foreach ($combo_data as $data) {

            $order_id = $order->id;
            $combo_data = [
                'user_id' => $user_id,
                'store_id' => $store_id,
                'order_id' => $order_id,
                'product_name' => $data['title'],
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'sub_total' => $data['price'] * $data['quantity'],
                'seller_id' => $seller_id,
                'status' => json_encode(array(array('delivered', date("d-m-Y h:i:sa")))),
                'active_status' => 'delivered',
            ];

            $order_item = OrderItems::forceCreate($combo_data);
        }

        if (isset($order_item) && !empty($order_item)) {
            // creating transaction record for card payments
            $trans_data = [
                'transaction_type' => 'transaction',
                'user_id' => $user_id,
                'order_id' => $order_id,
                'type' => $order_data['payment_method'],
                'txn_id' => $txn_id,
                'amount' => $final_total,
                'status' => "success",
                'message' =>
                labels('admin_labels.order_delivered_successfully', 'Order Delivered Successfully'),
            ];
            Transaction::forceCreate($trans_data);
        }
        $data['order_id'] = $order_id;
        $response = [
            'error' => false,
            'message' =>
            labels('admin_labels.order_delivered_successfully', 'Order Delivered Successfully'),
            'data' => $order,
        ];
        return response()->json($response);
    }

    public function get_poduct_variants(Request $request)
    {


        $res = fetchProduct('', '', $request['product_id']);

        if (!empty($res)) {

            $response = [
                'error' => false,
                'data' => $res['product'][0]->variants,
            ];
        } else {
            $response = [
                'error' => true,
                'message' =>
                labels('admin_labels.product_variants_not_found', 'Product variants not found.'),
                'data' => [],
            ];
        }
        return response()->json($response);
    }

    public function getCustomerAddress(Request $request)
    {
        $address_data = Address::leftJoin('users', 'addresses.user_id', '=', 'users.id')
            ->where('users.id', '=', $request->pos_user_id)
            ->get(['addresses.*', 'users.image']);

        if (!$address_data->isEmpty()) {
            $data['address_id'] = (!empty($address_data[0]->id) && $address_data[0]->id != 'NULL') ? $address_data[0]->id : '';
            $data['city'] = (!empty($address_data[0]->city) && $address_data[0]->city != 'NULL') ? $address_data[0]->city : '';
            $data['state'] = (!empty($address_data[0]->state) && $address_data[0]->state != 'NULL') ? $address_data[0]->state : '';
            $data['country'] = (!empty($address_data[0]->country) && $address_data[0]->country != 'NULL') ? $address_data[0]->country : '';
            $data['user_address'] = (!empty($address_data[0]->address) && $address_data[0]->address != 'NULL') ? $address_data[0]->address : '';
            $data['user_name'] = (!empty($address_data[0]->name) && $address_data[0]->name != 'NULL') ? $address_data[0]->name : '';
            $data['user_image'] = getMediaImageUrl($address_data[0]->image,'USER_IMG_PATH');
            $data['mobile'] = (!empty($address_data[0]->mobile) && $address_data[0]->mobile != 'NULL') ? $address_data[0]->mobile : $address_data[0]->alternate_mobile;
            $data['address'] = (!empty($address_data[0]->address) && $address_data[0]->address != 'NULL') ? $address_data[0]->address . ', ' : '';
            $data['address'] .= (!empty($address_data[0]->landmark) && $address_data[0]->landmark != 'NULL') ? $address_data[0]->landmark . ', ' : '';
            $data['address'] .= (!empty($address_data[0]->area) && $address_data[0]->area != 'NULL') ? $address_data[0]->area . ', ' : '';
            $data['address'] .= (!empty($address_data[0]->city) && $address_data[0]->city != 'NULL') ? $address_data[0]->city . ', ' : '';
            $data['address'] .= (!empty($address_data[0]->state) && $address_data[0]->state != 'NULL') ? $address_data[0]->state . ', ' : '';
            $data['address'] .= (!empty($address_data[0]->country) && $address_data[0]->country != 'NULL') ? $address_data[0]->country . ', ' : '';
            $data['address'] .= (!empty($address_data[0]->pincode) && $address_data[0]->pincode != 'NULL') ? $address_data[0]->pincode : '';
        }

        if (!$address_data->isEmpty()) {
            $response = [
                'error' => false,
                'data' => $data,
            ];
        } else {
            $response = [
                'error' => true,
                'message' =>
                labels('admin_labels.address_not_found', 'Address not found.'),
                'data' => [],
            ];
        }
        return response()->json($response);
    }

    public function update_user_address(Request $request)
    {
        $required_fields = [
            'address_id' => 'Address Id is required',
            'name' => 'Name is required',
            'mobile' => 'Mobile is required',
            'country' => 'Country is required',
            'state' => 'State is required',
            'city' => 'City is required',
            'address' => 'Address is required'
        ];

        // Initialize an empty array for missing fields
        $missing_fields = [];

        // Check each required field
        foreach ($required_fields as $field => $errorMessage) {
            if (empty($request->input($field))) {
                $missing_fields[] = [
                    'error' => true,
                    'error_message' => labels('admin_labels.' . $field . '_is_required', $errorMessage),
                    'csrfHash' => csrf_token(),
                    'data' => []
                ];
            }
        }

        // If any fields are missing, return the response with the first missing field error
        if (!empty($missing_fields)) {
            return response()->json($missing_fields[0]);
        }
        $address_id = !empty($request->input('address_id')) ? $request->input('address_id') : '';
        $address = Address::findOrFail($address_id);
        $address->name = !empty($request->input('name')) ? $request->name : '';
        $address->mobile = !empty($request->input('mobile')) ? $request->mobile : '';
        $address->address = !empty($request->input('address')) ? $request->address : '';
        $address->city = !empty($request->input('city')) ? $request->city : '';
        $address->state = !empty($request->input('state')) ? $request->state : '';
        $address->country = !empty($request->input('country')) ? $request->country : '';
        $address->save();
        if ($request->ajax()) {
            return response()->json(['message' => labels('admin_labels.address_updated_successfully', 'Address updated successfully')]);
        }
    }
}
