<?php

use Carbon\Carbon;
use App\Models\Faq;
use App\Models\Area;
use App\Models\Cart;
use App\Models\City;
use App\Models\User;
use App\Models\Order;
use App\Models\Store;
use App\Models\Seller;
use App\Models\Slider;
use Imagine\Image\Box;
use App\Models\Address;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Updates;
use App\Models\Zipcode;
use Imagine\Gd\Imagine;
use App\Models\Category;
use App\Models\Favorite;
use Imagine\Image\Point;
use App\Models\OrderItems;
use App\Libraries\Paystack;
use App\Libraries\Razorpay;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\ComboProduct;
use App\Models\OrderCharges;

use Illuminate\Mail\Message;
use App\Libraries\Shiprocket;
use App\Models\OrderTracking;
use App\Models\ProductRating;
use App\Models\ReturnRequest;
use App\Models\Attribute_values;
use App\Models\Product_variants;
use App\Models\OrderBankTransfers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\CustomFileRemover;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Request;
// use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\TransactionController;
use App\Models\Parcel;
use App\Models\Parcelitem;
use App\Models\Tax;
use App\Models\UserFcm;
use App\Models\Zone;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Spatie\MediaLibrary\MediaCollections\Filesystem as MediaFilesystem;
use Google\Client;
use Illuminate\Support\Facades\Validator;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;


function setUserReturnRequest($data, $table = 'orders')
{

    if ($table == 'orders') {
        foreach ($data as $row) {
            $requestData = [
                'user_id' => $row['user_id'],
                'product_id' => $row['product_id'],
                'product_variant_id' => $row['product_variant_id'],
                'order_id' => $row['order_id'],
                'order_item_id' => $row['order_item_id']
            ];
            ReturnRequest::create($requestData);
        }
    } else {
        $requestData = [
            'user_id' => $data->user_id,
            'product_id' => $data->product_id,
            'product_variant_id' => $data->product_variant_id,
            'order_id' => $data->order_id,
            'order_item_id' => $data->order_item_id
        ];
        ReturnRequest::create($requestData);
    }
}
function validateOrderStatus($order_ids, $status, $table = 'order_items', $user_id = null, $fromuser = false, $parcel_type = '')
{
    $error = 0;
    $cancelable_till = '';
    $returnable_till = '';
    $is_already_returned = 0;
    $is_already_cancelled = 0;
    $is_returnable = 0;
    $is_cancelable = 0;
    $returnable_count = 0;
    $cancelable_count = 0;
    $return_request = 0;
    $check_status = ['received', 'processed', 'shipped', 'delivered', 'cancelled', 'returned'];
    $user = Auth::user();

    $roleIdsToCheck = [1, 3, 5];

    if (in_array(strtolower(trim($status)), $check_status)) {

        if ($table == 'order_items') {
            $activeStatus = OrderItems::whereIn('id', explode(',', $order_ids))->pluck('active_status')->toArray();

            if (in_array('cancelled', $activeStatus) || in_array('returned', $activeStatus)) {
                $response = [
                    'error' => true,
                    'message' => "You can't update status once an item is cancelled or returned",
                    'data' => [],
                ];

                return $response;
            }
        }
        if ($table == 'parcels') {

            $parcelIds = explode(',', $order_ids);

            $results = DB::table('parcels as p')
                ->leftJoin('parcel_items as pi', 'pi.parcel_id', '=', 'p.id')
                ->whereIn('p.id', $parcelIds)
                ->select('p.active_status', 'pi.order_item_id')
                ->get();

            $orderItemIds = $results->pluck('order_item_id')->toArray();

            $activeStatuses = $results->pluck('active_status')->toArray();

            if (in_array("cancelled", $activeStatuses) || in_array("returned", $activeStatuses)) {
                return [
                    'error' => true,
                    'message' => "You can't update status once item cancelled / returned",
                    'data' => []
                ];
            }

            if (empty($orderItemIds)) {
                return [
                    'error' => true,
                    'message' => "You can't update status. Something went wrong!",
                    'data' => []
                ];
            }
        }

        $query = DB::table('order_items as oi')
            ->select('oi.id as order_item_id', 'oi.user_id', 'oi.product_variant_id', 'oi.order_id');

        if ($parcel_type === 'combo_order') {
            $query->leftJoin('combo_products as cp', 'cp.id', '=', 'oi.product_variant_id')
                ->addSelect('cp.*');
        } else {
            $query->leftJoin('product_variants as pv', 'pv.id', '=', 'oi.product_variant_id')
                ->leftJoin('products as p', 'pv.product_id', '=', 'p.id')
                ->addSelect('p.*', 'pv.*');
        }
        $query->leftJoin('parcel_items as pi', 'pi.order_item_id', '=', 'oi.id')
            ->leftJoin('parcels as pr', 'pr.id', '=', 'pi.parcel_id')
            ->addSelect('pr.active_status', 'pr.status as parcel_status');


        if ($table === 'parcels') {
            $query->addSelect('pr.active_status', 'pr.status as order_item_status')
                ->whereIn('oi.id', $orderItemIds)
                ->groupBy('oi.id');
        } else {
            $query->addSelect('oi.active_status', 'oi.status as order_item_status');
            if ($table === 'orders') {
                $query->where('oi.order_id', $order_ids);
            } else {
                $query->whereIn('oi.id', explode(',', $order_ids));
            }
        }

        $productData = $query->get();

        $priority_status = [
            'received' => 0,
            'processed' => 1,
            'shipped' => 2,
            'delivered' => 3,
            'return_request_pending' => 4,
            'return_request_approved' => 5,
            'return_pickedup' => 8,
            'cancelled' => 6,
            'returned' => 7,
        ];

        $is_posted_status_set = $canceling_delivered_item = $returning_non_delivered_item = false;
        $is_posted_status_set_count = 0;

        for ($i = 0; $i < count($productData); $i++) {
            /* check if there are any products returnable or cancellable products available in the list or not */
            if ($productData[$i]->is_returnable == 1) {
                $returnable_count += 1;
            }
            if ($productData[$i]->is_cancelable == 1) {
                $cancelable_count += 1;
            }

            /* check if the posted status is present in any of the variants */
            $productData[$i]->order_item_status = json_decode($productData[$i]->order_item_status, true);
            $order_item_status = array_column($productData[$i]->order_item_status, '0');
            if (in_array($status, $order_item_status)) {
                $is_posted_status_set_count++;
            }


            /* if all are marked as same as posted status set the flag */
            if ($is_posted_status_set_count == count($productData)) {
                $is_posted_status_set = true;
            }

            /* check if user is cancelling the order after it is delivered */
            if (($status == "cancelled") && (in_array("delivered", $order_item_status) || in_array("returned", $order_item_status))) {
                $canceling_delivered_item = true;
            }

            /* check if user is returning non delivered item */
            if (($status == "returned") && !in_array("delivered", $order_item_status)) {
                $returning_non_delivered_item = true;
            }
        }
        if ($table == 'parcels' && $status == 'returned') {
            $response['error'] = true;
            $response['message'] = "You cannot return Parcel Order!";
            $response['data'] = array();
            return $response;
        }
        if ($is_posted_status_set == true) {
            $response['error'] = true;
            $response['message'] = "Order is already marked as $status. You cannot set it again!";
            $response['data'] = array();
            return $response;
        }

        if ($canceling_delivered_item == true) {
            /* when user is trying cancel delivered order / item */
            $response['error'] = true;
            $response['message'] = "You cannot cancel delivered or returned order / item. You can only return that!";
            $response['data'] = array();
            return $response;
        }
        if ($returning_non_delivered_item == true) {
            /* when user is trying return non delivered order / item */
            $response['error'] = true;
            $response['message'] = "You cannot return a non-delivered order / item. First it has to be marked as delivered and then you can return it!";
            $response['data'] = array();
            return $response;
        }

        $is_returnable = ($returnable_count >= 1) ? 1 : 0;
        $is_cancelable = ($cancelable_count >= 1) ? 1 : 0;

        for ($i = 0; $i < count($productData); $i++) {

            if ($productData[$i]->active_status == 'returned') {
                $error = 1;
                $is_already_returned = 1;
                break;
            }

            if ($productData[$i]->active_status == 'cancelled') {
                $error = 1;
                $is_already_cancelled = 1;
                break;
            }

            if ($status == 'returned' && $productData[$i]->is_returnable == 0) {
                $error = 1;
                break;
            }

            if ($status == 'returned' && $productData[$i]->is_returnable == 1 && $priority_status[$productData[$i]->active_status] < 3) {
                $error = 1;
                $returnable_till = 'delivery';
                break;
            }

            if ($status == 'cancelled' && $productData[$i]->is_cancelable == 1) {
                $max = $priority_status[$productData[$i]->cancelable_till];
                $min = $priority_status[$productData[$i]->active_status];

                if ($min > $max) {
                    $error = 1;
                    $cancelable_till = $productData[$i]->cancelable_till;
                    break;
                }
            }

            if ($status == 'cancelled' && $productData[$i]->is_cancelable == 0) {
                $error = 1;
                break;
            }
        }

        if ($status == 'returned' && $error == 1 && !empty($returnable_till)) {
            return response()->json([
                'error' => true,
                'message' => (count($productData) > 1) ? "One of the order item is not delivered yet!" : "The order item is not delivered yet!",
                'data' => [],
            ]);
        }

        if ($status == 'returned' && $error == 1 && !$user && !$user->roles->whereIn('role_id', $roleIdsToCheck)) {
            return response()->json([
                'error' => true,
                'message' => (count($productData) > 1) ? "One of the order item can't be returned!" : "The order item can't be returned!",
                'data' => $productData,
            ]);
        }

        if ($status == 'cancelled' && $error == 1 && !empty($cancelable_till) && !$user && !$user->roles->whereIn('role_id', $roleIdsToCheck)) {
            return response()->json([
                'error' => true,
                'message' => (count($productData) > 1) ? "One of the order item can be cancelled till " . $cancelable_till . " only" : "The order item can be cancelled till " . $cancelable_till . " only",
                'data' => [],
            ]);
        }

        if ($status == 'cancelled' && $error == 1 && !$user && !$user->roles->whereIn('role_id', $roleIdsToCheck)) {
            return response()->json([
                'error' => true,
                'message' => (count($productData) > 1) ? "One of the order item can't be cancelled!" : "The order item can't be cancelled!",
                'data' => [],
            ]);
        }

        for ($i = 0; $i < count($productData); $i++) {


            if ($status == 'returned' && $productData[$i]->is_returnable == 1 && $error == 0) {
                $error = 1;
                $return_request_flag = 1;

                $return_status = [
                    'is_already_returned' => $is_already_returned,
                    'is_already_cancelled' => $is_already_cancelled,
                    'return_request_submitted' => $return_request,
                    'is_returnable' => $is_returnable,
                    'is_cancelable' => $is_cancelable,
                ];

                if ($fromuser == true || $fromuser == 1) {


                    if ($table == 'order_items') {

                        if (isExist(['user_id' => $productData[$i]->user_id, 'order_item_id' => $productData[$i]->order_item_id, 'order_id' => $productData[$i]->order_id], 'return_requests')) {

                            $response['error'] = true;
                            $response['message'] = "Return request already submitted !";
                            $response['data'] = array();
                            $response['return_status'] = $return_status;
                            return $response;
                        }
                        $request_data_item_data = $productData[$i];
                        setUserReturnRequest($request_data_item_data, $table);
                    } else {
                        for ($j = 0; $j < count($productData); $j++) {
                            if (isExist(['user_id' => $productData[$i]->user_id, 'order_item_id' => $productData[$i]->order_item_id, 'order_id' => $productData[$i]->order_id], 'return_requests')) {

                                $response['error'] = true;
                                $response['message'] = "Return request already submitted !";
                                $response['data'] = array();
                                $response['return_status'] = $return_status;
                                return $response;
                            }
                        }
                        $request_data_overall_item_data = $productData[$i];
                        setUserReturnRequest($request_data_overall_item_data, $table);
                    }
                }

                $response['error'] = false;
                $response['message'] = "Return request submitted successfully !";
                $response['return_request_flag'] = 1;
                $response['data'] = array();
                return $response;
            }
        }
        $response['error'] = false;
        $response['message'] = " ";
        $response['data'] = array();

        return $response;
    } else {
        $response['error'] = true;
        $response['message'] = "Invalid Status Passed";
        $response['data'] = array();
        return $response;
    }
}

function update_order_item($id, $status, $return_request = 0, $fromapp = false)
{
    if ($return_request == 0) {
        $res = validateOrderStatus($id, $status, 'order_items', '', true);

        if ($res['error']) {
            $response['error'] = (isset($res['return_request_flag'])) ? false : true;
            $response['message'] = $res['message'];
            $response['data'] = $res['data'];
            return $response;
        }
    }
    if ($fromapp == true) {
        if ($status == 'returned') {
            $status = 'return_request_pending';
        }
    }
    $order_item_details = fetchDetails('order_items', ['id' => $id], ['order_id', 'seller_id']);
    $order_details = fetchOrders($order_item_details[0]->order_id);
    $order_tracking_data = getShipmentId($id, $order_item_details[0]->order_id);
    if (!empty($order_details) && !empty($order_item_details)) {
        $order_details = $order_details['order_data'];
        $order_items_details = $order_details[0]->order_items;
        $key = array_search($id, array_column($order_items_details->toArray(), 'id'));
        $order_id = $order_details[0]->id;
        $store_id = $order_details[0]->store_id;
        $user_id = $order_details[0]->user_id;
        $order_counter = $order_items_details[$key]->order_counter;
        $order_cancel_counter = $order_items_details[$key]->order_cancel_counter;
        $order_return_counter = $order_items_details[$key]->order_return_counter;
        $seller_id = Seller::where('id', $order_item_details[0]->seller_id)->value('user_id');
        $user_res = fetchDetails('users', ['id' => $seller_id], ['fcm_id', 'username']);

        $fcm_ids = array();
        $results = UserFcm::join('users', 'user_fcm.user_id', '=', 'users.id')
            ->where('user_fcm.user_id', $seller_id)
            ->where('users.is_notification_on', 1)
            ->select('user_fcm.fcm_id')
            ->get();
        foreach ($results as $result) {
            if (is_object($result)) {
                $fcm_ids[] = $result->fcm_id;
            }
        }
        $registrationIDs_chunks = array_chunk($fcm_ids, 1000);
        if ($order_items_details[$key]->active_status == 'cancelled') {
            $response['error'] = true;
            $response['message'] = 'Status Already Updated';
            $response['data'] = array();
            return $response;
        }
        if (updateOrder(['status' => $status], ['id' => $id], true, 'order_items')) {
            updateOrder(['active_status' => $status], ['id' => $id], false, 'order_items');

            //send notification while order cancelled
            if ($status == 'cancelled') {
                $fcm_admin_subject = 'Order cancelled';
                $fcm_admin_msg = 'Hello ' . $user_res[0]->username . 'order of order item id ' . $id . ' is cancelled.';
                if (!empty($fcm_ids)) {
                    $fcmMsg = array(
                        'title' => "$fcm_admin_subject",
                        'body' => "$fcm_admin_msg",
                        'type' => "place_order",
                        'store_id' => "$store_id",
                        'content_available' => true
                    );
                    sendNotification('', $registrationIDs_chunks, $fcmMsg,);
                }
                if (isset($order_tracking_data) && !empty($order_tracking_data) && $order_tracking_data != null) {
                    cancel_shiprocket_order($order_tracking_data[0]['shiprocket_order_id']);
                }
            }
        }

        $response['error'] = false;
        $response['message'] = 'Status Updated Successfully';
        $response['data'] = array();
        return $response;
    }
}
