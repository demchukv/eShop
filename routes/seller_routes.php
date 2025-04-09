<?php


use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Seller\HomeController;
use App\Http\Controllers\Seller\CategoryController;
use App\Http\Controllers\Seller\PickupLocationController;
use App\Http\Controllers\Seller\MediaController;
use App\Http\Controllers\Seller\AreaController;
use App\Http\Controllers\Seller\TaxController;
use App\Http\Controllers\Seller\AttributeController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\PosController;
use App\Http\Controllers\Seller\StockController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\PaymentRequestController;
use App\Http\Controllers\Seller\ProductFaqController;
use App\Http\Controllers\Seller\TransactionController;
use App\Http\Controllers\Seller\ComboProductAttributeController;
use App\Http\Controllers\Seller\ComboProductController;
use App\Http\Controllers\Seller\ComboProductFaqController;
use App\Http\Controllers\Seller\LanguageController;
use App\Http\Controllers\Seller\ReportController;
use App\Http\Controllers\Seller\UserController;
use App\Http\Controllers\vendor\Chatify\MessagesController;
use App\Http\Controllers\SellerInviteController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Seller\PendingPaymentController;
use App\Http\Controllers\Seller\ParcelController;

Route::get('seller/orders/generatParcelInvoicePDF/{id}', [OrderController::class, 'generatParcelInvoicePDF'])->name('seller.orders.generatParcelInvoicePDF');
Route::get('seller/orders/generatInvoicePDF/{id}/{seller_id?}', [OrderController::class, 'generatInvoicePDF'])
    ->name('seller.orders.generatInvoicePDF');
Route::get('seller/zones/zones_data', [AreaController::class, 'zone_data']);
Route::get("seller/area/get_cities", [AreaController::class, 'getCities']);
Route::get("seller/area/get_zipcodes", [AreaController::class, 'get_zipcodes']);
Route::group(
    ['middleware' => ['auth', 'role:seller']],
    function () {

        Route::post('seller/parcels/create-custom-carrier', [ParcelController::class, 'createCustomCarrier'])->name('seller.parcels.create_custom_carrier');
        Route::get('/seller/parcels/track/{trackingId}', [ParcelController::class, 'trackParcel'])->name('seller.parcels.track');
        // account

        Route::get('seller/account/{id}', [UserController::class, 'edit']);

        Route::put('seller/account/update/{id}', [UserController::class, 'update'])->name('seller.account.update');

        // categories

        Route::get(
            '/seller/home',
            [HomeController::class, 'index']
        )->name('seller.home');

        Route::get('/seller/get_statistics', [HomeController::class, 'get_statistics']);

        Route::get('/seller/get_most_selling_category', [HomeController::class, 'get_most_selling_category']);

        Route::get('/seller/topSellingProducts', [HomeController::class, 'topSellingProducts']);

        Route::get('/seller/mostPopularProduct', [HomeController::class, 'mostPopularProduct']);

        Route::get('/seller/categories', [CategoryController::class, 'index'])->name('seller_categories.index');

        Route::get('/seller/categories/list', [CategoryController::class, 'list'])->name('seller_categories.list');

        Route::get('/seller/categories/get_seller_categories', [CategoryController::class, 'getSellerCategories']);
        Route::get('/seller/categories/get_seller_categories_filter', [CategoryController::class, 'get_seller_categories_filter']);

        // pickup locations

        Route::resource("seller/pickup_locations", PickupLocationController::class)->names([
            'index' => 'pickup_locations.index',
        ])->except('show');

        Route::get('seller/pickup_locations/list', [PickupLocationController::class, 'list'])->name('pickup_locations.list');

        Route::post('seller/pickup_locations', [PickupLocationController::class, 'store'])->name('pickup_locations.store');
        // media
        Route::get('seller/media/image', [MediaController::class, 'dynamic_image'])->name('seller.dynamic_image');
        Route::post('seller/media/upload', [MediaController::class, 'upload'])->name('seller.media.upload');

        Route::get('seller/media', [MediaController::class, 'index'])->name('seller.media');

        Route::get('seller/media/destroy/{id}', [MediaController::class, 'destroy'])->name('seller.media.destroy');

        // location

        Route::get("seller/area/zipcodes", [AreaController::class, 'zipcodes'])->name('seller.zipcodes');

        Route::get(
            'seller/zipcodes/list',
            [AreaController::class, 'zipcode_list']
        )->name('seller.zipcodes.list');

        Route::get("seller/area/city", [AreaController::class, 'city'])->name('seller.city');

        Route::get(
            'seller/city/list',
            [AreaController::class, 'city_list']
        )->name('seller.city.list');

        Route::get("seller/area/", [AreaController::class, 'area'])->name('area');

        Route::get(
            'seller/area/list',
            [AreaController::class, 'area_list']
        )->name('seller.area.list');



        // tax

        Route::resource("seller/tax", TaxController::class)->names(['index' => 'tax.index',])->except('show');

        Route::get('/tax/list', [TaxController::class, 'list'])->name('tax.list');

        Route::get('seller/tax/get_taxes', [TaxController::class, 'getTaxes']);

        //attributes

        Route::resource("seller/products/attributes", AttributeController::class)->names([
            'index' => 'attributes.index',
        ])->except('show');

        Route::get('seller/attributes/list', [AttributeController::class, 'list'])->name('seller_attributes.list');

        Route::post('seller/attribute/getAttributes', [AttributeController::class, 'getAttributes']);

        //products

        Route::resource("seller/products", ProductController::class)->names([
            'index' => 'seller.products.index',
            'edit' => 'seller.products.edit',
        ])->except('show');
        Route::post('seller/products', [ProductController::class, 'store'])->name('seller_products.store');

        Route::get('seller/products/update_status/{id}', [ProductController::class, 'update_status']);

        Route::get('seller/products/destroy/{id}', [ProductController::class, 'destroy'])->name('seller.products.destroy');

        Route::get('seller/products/fetch_attributes_by_id', [ProductController::class, 'fetchAttributesById']);

        Route::put('seller/products/update/{id}', [ProductController::class, 'update'])->name('seller.products.update');

        Route::get('seller/products/fetch_attribute_values_by_id', [ProductController::class, 'fetch_attribute_values_by_id']);

        Route::get('seller/products/fetch_variants_values_by_pid', [ProductController::class, 'fetch_variants_values_by_pid']);

        Route::get('seller/products/get_variants_by_id', [ProductController::class, 'get_variants_by_id']);

        Route::get('seller/products/get_countries', [ProductController::class, 'get_countries']);

        Route::get('seller/products/get_brands', [ProductController::class, 'get_brands']);

        Route::get('seller/products/get_brands', [ProductController::class, 'getBrands'])->name('seller.getBrands');

        Route::get('seller/products/manage_product', [ProductController::class, 'manageProduct'])->name('seller.products.manage_product');

        Route::get('seller/products/list', [ProductController::class, 'list'])->name('seller.products.list');

        Route::get('seller/products/get_product_details', [ProductController::class, 'get_product_details']);

        Route::get('seller/products/get_digital_product_data', [ProductController::class, 'getDigitalProductData']);

        Route::post('seller/products/delete_image', [ProductController::class, 'deleteImage']);

        Route::get('seller/product/product_bulk_upload', [ProductController::class, 'bulk_upload'])->name('seller.product_bulk_upload');

        Route::post("seller/product/bulk_upload", [ProductController::class, 'process_bulk_upload'])->name('seller.product.bulk_upload');

        Route::get('seller/product/view_product/{id}', [ProductController::class, 'show'])->name('seller.product.show');


        //product faqs

        Route::resource("seller/product_faqs", ProductFaqController::class)->names([
            'index' => 'seller.product_faqs.index',
        ])->except('show');
        Route::post('seller/product_faqs', [ProductFaqController::class, 'store'])->name('seller.product_faqs.store');
        Route::get('seller/product_faqs/list', [ProductFaqController::class, 'list'])->name('seller.product_faqs.list');
        Route::get('seller/product_faqs/edit/{id}', [ProductFaqController::class, 'edit']);
        Route::put('seller/product_faqs/update/{id}', [ProductFaqController::class, 'update']);
        Route::get('seller/product_faqs/destroy/{id}', [ProductFaqController::class, 'destroy'])->name('seller.product_faqs.destroy');


        //combo product faqs

        Route::resource("seller/combo_product_faqs", ComboProductFaqController::class)->names([
            'index' => 'seller.combo_product_faqs.index',
        ])->except('show');
        Route::post('seller/combo_product_faqs', [ComboProductFaqController::class, 'store'])->name('seller.combo_product_faqs.store');
        Route::get('seller/combo_product_faqs/list', [ComboProductFaqController::class, 'list'])->name('seller.combo_product_faqs.list');
        Route::get('seller/combo_product_faqs/edit/{id}', [ComboProductFaqController::class, 'edit']);
        Route::put('seller/combo_product_faqs/update/{id}', [ComboProductFaqController::class, 'update']);
        Route::get('seller/combo_product_faqs/destroy/{id}', [ComboProductFaqController::class, 'destroy'])->name('seller.combo_product_faqs.destroy');

        //Transaction
        Route::get('seller/transaction/wallet_transactions', [TransactionController::class, 'wallet_transactions'])->name('seller.transaction.wallet_transactions');
        Route::get('seller/transaction/wallet_transactions_list', [TransactionController::class, 'wallet_transactions_list'])->name('seller.transaction.wallet_transactions_list');

        // Pending Payments
        Route::get('seller/pending_payments', [PendingPaymentController::class, 'pending_payments'])->name('seller.pending_payments');
        Route::get('seller/pending_payments/list', [PendingPaymentController::class, 'pending_payments_list'])->name('seller.pending_payments.list');

        //Payment Request
        Route::get('seller/payment_request/withdrawal_requests', [PaymentRequestController::class, 'withdrawal_requests'])->name('seller.payment_request.withdrawal_requests');
        Route::get('seller/payment_request/get_payment_request_list', [PaymentRequestController::class, 'get_payment_request_list'])->name('seller.payment_request.get_payment_request_list');
        Route::put('seller/payment_request/add_withdrawal_request', [PaymentRequestController::class, 'add_withdrawal_request'])->name('seller.payment_request.add_withdrawal_request');

        //pos

        Route::get('seller/point_of_sale', [PosController::class, 'index'])->name('seller.point_of_sale.index');

        Route::post('seller/point_of_sale/register_user', [PosController::class, 'register_user'])->name('register.user');

        Route::get('seller/point_of_sale/get_users', [PosController::class, 'get_users']);

        Route::get('seller/point_of_sale/get_products', [PosController::class, 'get_products']);

        Route::get('seller/point_of_sale/get_combo_products', [PosController::class, 'get_combo_products']);

        Route::post('seller/point_of_sale/place_order', [PosController::class, 'place_order'])->name('place.order');

        Route::post('seller/point_of_sale/combo_place_order', [PosController::class, 'combo_place_order'])->name('combo.place.order');

        Route::post('seller/point_of_sale/get_poduct_variants', [PosController::class, 'get_poduct_variants']);

        Route::post('seller/point_of_sale/getCustomerAddress', [PosController::class, 'getCustomerAddress']);

        Route::put("seller/point_of_sale/update_user_address", [PosController::class, 'update_user_address'])->name('seller.update_user_address');

        //manage stock

        Route::get('seller/manage_stock', [StockController::class, 'index'])->name('seller.manage_stock.index');

        Route::get('seller/manage_stock/get_stock_list', [StockController::class, 'get_stock_list'])->name('stock_list');

        Route::get('seller/manage_stock/edit/{id}', [StockController::class, 'edit'])->name('stock.edit');

        Route::put('seller/manage_stock/update/{id}', [StockController::class, 'update'])->name('stock.update');

        // Manage Combo Stock


        Route::get('seller/manage_combo_stock', [StockController::class, 'manage_combo_stock'])->name('seller.manage_combo_stock.index');

        Route::put('seller/manage_combo_stock/update/{id}', [StockController::class, 'combo_stock_update'])->name('seller.combo_stock.update');

        Route::get('seller/manage_combo_stock/list', [StockController::class, 'combo_stock_list'])->name('seller.manage_combo_stock.list');


        Route::get('seller/manage_combo_stock/edit/{id}', [StockController::class, 'combo_stock_edit'])->name('seller.combo_stock.edit');

        //Chat

        Route::resource("seller/chat", MessagesController::class)->names([
            'index' => 'seller.chat.index',
        ]);


        // Orders Section

        Route::resource("seller/orders", OrderController::class)->names([
            'index' => 'seller.orders.index',
            // 'store' => 'feature_section.store',
            'edit' => 'seller.orders.edit',

            // 'update' => 'feature_section.update',

        ])->except('show');


        Route::post('seller/orders/update_order_status', [OrderController::class, 'update_order_status'])->name('seller.orders.update_order_status');
        Route::post('seller/orders/update_order_tracking', [OrderController::class, 'update_order_tracking'])->name('seller.orders.update_order_tracking');
        Route::post('seller/orders/create_shiprocket_order', [OrderController::class, 'create_shiprocket_order'])->name('seller.orders.create_shiprocket_order');
        Route::post('seller/orders/generate_awb', [OrderController::class, 'generate_awb'])->name('seller.orders.generate_awb');
        Route::post('seller/orders/send_pickup_request', [OrderController::class, 'send_pickup_request'])->name('seller.orders.send_pickup_request');
        Route::post('seller/orders/cancel_shiprocket_order', [OrderController::class, 'cancel_shiprocket_order'])->name('seller.orders.cancel_shiprocket_order');
        Route::post('seller/orders/generate_label', [OrderController::class, 'generate_label'])->name('seller.orders.generate_label');
        Route::post('seller/orders/generate_invoice', [OrderController::class, 'generate_invoice'])->name('seller.orders.generate_invoice');
        Route::post('seller/orders/getSellerOrderTrackingList', [OrderController::class, 'getSellerOrderTrackingList']);
        Route::post('seller/orders/update_shiprocket_order_status', [OrderController::class, 'update_shiprocket_order_status']);


        Route::get('seller/orders/list', [OrderController::class, 'list'])->name('seller.orders.list');

        Route::get('seller/orders/order_item_list', [OrderController::class, 'order_item_list'])->name('seller.orders.item_list');

        Route::get('seller/orders/destroy/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

        Route::post('seller/orders/send_digital_product', [OrderController::class, 'send_digital_product'])->name('seller.orders.send_digital_product');
        // Route::get('seller/orders/generatInvoicePDF/{id}', [OrderController::class, 'generatInvoicePDF'])->name('seller.orders.generatInvoicePDF');
        // Route::get('seller/orders/generatParcelInvoicePDF/{id}', [OrderController::class, 'generatParcelInvoicePDF'])->name('seller.orders.generatParcelInvoicePDF');



        Route::post('seller/set_store', function (Request $request) {

            session(['store_id' => $request->store_id]);
            session(['store_name' => $request->store_name]);
            session(['store_image' => $request->store_image]);
            return response()->json(['success' => true]);
        })->name('set_store');

        // combo products attributes

        Route::resource("seller/combo_product_attributes", ComboProductAttributeController::class)->names([
            'index' => 'seller.combo_product_attributes.index',
        ])->except('show');
        Route::get(
            'seller/combo_product_attributes/list',
            [ComboProductAttributeController::class, 'list']
        )->name('seller.combo_product_attributes.list');

        // combo products

        Route::resource("seller/combo_products", ComboProductController::class)->names([
            'index' => 'seller.combo_products.index',
            'edit' => 'seller.combo_products.edit',
        ])->except('show');
        Route::put('seller/combo_products/update/{id}', [ComboProductController::class, 'update'])->name('seller.combo_products.update');


        Route::get('combo_products/destroy/{id}', [ComboProductController::class, 'destroy'])->name('seller.combo_products.destroy');

        Route::post("seller/combo_products/store", [ComboProductController::class, 'store'])->name('seller.combo_products.store');

        Route::get('seller/combo_products/manage_product', [ComboProductController::class, 'manageProduct'])->name('seller.combo_products.manage_product');
        Route::get('seller/combo_products/get_product_details', [ComboProductController::class, 'getProductdetails']);

        Route::get(
            'seller/combo_products/list',
            [ComboProductController::class, 'list']
        )->name('seller.combo_products.list');

        Route::get('seller/combo_products/update_status/{id}', [ComboProductController::class, 'update_status']);

        Route::get('seller/combo_products/fetch_attributes_by_id', [ComboProductController::class, 'fetchAttributesById']);

        Route::get('seller/combo_products/view_product/{id}', [ComboProductController::class, 'show'])->name('seller.combo_products.show');

        Route::get('seller/combo_product/product_bulk_upload', [ComboProductController::class, 'bulk_upload'])->name('seller.combo.product.bulk_upload');

        Route::post("seller/combo_product/bulk_upload", [ComboProductController::class, 'process_bulk_upload'])->name('seller.combo.product.process_bulk_upload');

        Route::get('seller/products/get_product_details_for_combo', [ProductController::class, 'getProductdetailsForCombo']);
        // language

        Route::get("seller/settings/language", [LanguageController::class, 'index']);

        Route::put("seller/settings/languages/savelabel", [LanguageController::class, 'savelabel'])->name('savelabel');

        Route::get('seller/settings/languages/change', [LanguageController::class, 'change'])->name('changeLang');

        Route::get("seller/settings/set-language/{locale}", [LanguageController::class, 'setLanguage'])->name('set-language'); // language


        //Reports

        Route::get('seller/reports/sales_report', [ReportController::class, 'index'])->name('seller.reports.sales_report');

        Route::get('seller/reports/sales_report/list', [ReportController::class, 'list'])->name('seller.sales.report.list');



        // parcel routes

        Route::post("seller/orders/create_parcel", [OrderController::class, 'create_parcel'])->name('seller.create_parcel');

        Route::get(
            'seller/parcels/list',
            [OrderController::class, 'parcel_list']
        )->name('seller.parcels.list');

        Route::post('seller/orders/delete_parcel', [OrderController::class, 'delete_parcel']);

        Route::get('/seller/products/comments/{productId}', [App\Http\Controllers\Seller\ProductController::class, 'getComments'])->name('seller.products.comments');
    }


);
