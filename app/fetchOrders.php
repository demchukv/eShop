<?php

function fetchOrders($order_id = NULL, $user_id = NULL, $status = NULL, $delivery_boy_id = NULL, $limit = NULL, $offset = NULL, $sort = 'o.id', $order = 'DESC', $download_invoice = false, $start_date = null, $end_date = null, $search = null, $city_id = null, $area_id = null, $seller_id = null, $order_type = '', $from_seller = false, $store_id = null)
{

    $total_query = DB::table('orders as o')
        ->select(DB::raw('COUNT(DISTINCT o.id) as total'), 'oi.order_type')
        ->leftJoin('users as u', 'u.id', '=', 'o.user_id')
        ->leftJoin('order_items as oi', 'o.id', '=', 'oi.order_id')
        ->leftJoin('product_variants as pv', 'pv.id', '=', 'oi.product_variant_id')
        ->leftJoin('products as p', 'pv.product_id', '=', 'p.id')
        ->leftJoin('order_trackings as ot', 'ot.order_item_id', '=', 'oi.id')
        ->leftJoin('addresses as a', 'a.id', '=', 'o.address_id')
        ->leftJoin('combo_products as cp', 'cp.id', '=', 'oi.product_variant_id')
        ->where(function ($query) {
            $query->where('oi.order_type', 'regular_order')
                ->orWhere('oi.order_type', 'combo_order');
        });
    if (isset($store_id) && $store_id !== NULL && !empty($store_id)) {
        $total_query->where('o.store_id', $store_id);
    }

    if (isset($order_id) && $order_id !== NULL && !empty($order_id)) {
        $total_query->where('o.id', $order_id);
    }

    if (isset($delivery_boy_id) && $delivery_boy_id !== null && !empty($delivery_boy_id)) {
        $total_query->where('oi.delivery_boy_id', $delivery_boy_id);
    }

    if (isset($user_id) && $user_id !== null && !empty($user_id)) {
        $total_query->where('o.user_id', $user_id);
    }

    if (isset($city_id) && $city_id !== null && !empty($city_id)) {
        $total_query->where('a.city_id', $city_id);
    }

    if (isset($area_id) && $area_id !== null && !empty($area_id)) {
        $total_query->where('a.area_id', $area_id);
    }

    if (isset($seller_id) && $seller_id !== null && !empty($seller_id)) {
        $total_query->where('oi.seller_id', $seller_id);
    }

    if (isset($order_type) && $order_type !== '' && $order_type === 'digital') {
        $total_query->where(function ($query) {
            $query->where('p.type', 'digital_product')
                ->orWhere('cp.product_type', 'digital_product');
        });
    }



    if (isset($order_type) && $order_type !== '' && $order_type === 'simple') {

        $total_query->where(function ($query) {
            $query->where('p.type', '!=', 'digital_product')
                ->orWhere('cp.product_type', '!=', 'digital_product');
        });
    }
    if (isset($status) && !empty($status) && $status != '' && is_array($status) && count($status) > 0) {
        $status = array_map('trim', $status);

        $total_query->whereIn('oi.active_status', $status);
    }

    if (isset($start_date) && $start_date !== null && isset($end_date) && $end_date !== null && !empty($end_date) && !empty($start_date)) {
        $total_query->whereDate('o.created_at', '>=', $start_date)
            ->whereDate('o.created_at', '<=', $end_date);
    }

    if (!empty($start_date)) {
        $total_query->whereDate('o.created_at', '>=', $start_date);
    }

    if (!empty($end_date)) {
        $total_query->whereDate('o.created_at', '<=', $end_date);
    }
    if (isset($search) && $search !== null && !empty($search)) {
        $filters = [
            'u.username' => $search,
            'u.email' => $search,
            'o.id' => $search,
            'o.mobile' => $search,
            'o.address' => $search,
            'o.payment_method' => $search,
            'o.delivery_time' => $search,
            'o.created_at' => $search,
            'oi.active_status' => $search,
            'p.name' => $search,
        ];

        $total_query->where(function ($query) use ($filters) {
            foreach ($filters as $column => $value) {
                $query->orWhere($column, 'LIKE', '%' . $value . '%');
            }
        });
    }
    if (isset($search) && $search !== null && !empty($search)) {
        $combo_filters = [
            'u.username' => $search,
            'u.email' => $search,
            'o.id' => $search,
            'o.mobile' => $search,
            'o.address' => $search,
            'o.payment_method' => $search,
            'o.delivery_time' => $search,
            'o.created_at' => $search,
            'oi.active_status' => $search,
            'cp.title' => $search,
        ];

        $total_query->where(function ($query) use ($filters) {
            foreach ($filters as $column => $value) {
                $query->orWhere($column, 'LIKE', '%' . $value . '%');
            }
        });
    }

    if (isset($seller_id) && $seller_id !== null) {
        $total_query->where('oi.active_status', '!=', 'awaiting');
    }
    $total_query->where('o.is_pos_order', 0);
    if ($sort === 'created_at') {
        $sort = 'o.created_at';
    }

    $total_query->orderBy($sort, $order);

    $orderCount = $total_query->get()->toArray();
    $total = "0";
    foreach ($orderCount as $row) {

        $total = $row->total;
    }
    if (empty($sort)) {
        $sort = 'o.created_at';
    }

    $regularOrderSearchRes = DB::table('orders AS o')
        ->select(
            'o.*',
            'u.username',
            'u.image as user_profile_image',
            'u.country_code',
            'p.name',
            'p.type',
            'p.slug',
            'p.download_allowed',
            'p.pickup_location',
            'a.name AS order_recipient_person',
            'pv.special_price',
            'pv.price',
            'oc.delivery_charge AS seller_delivery_charge',
            'oc.promo_discount AS seller_promo_discount',
            'oi.order_type',
            'sd.user_id as main_seller_id',
        )
        ->leftJoin('users AS u', 'u.id', '=', 'o.user_id')
        ->leftJoin('order_items AS oi', 'o.id', '=', 'oi.order_id')
        ->leftJoin('seller_data AS sd', 'sd.id', '=', 'oi.seller_id')
        ->leftJoin('product_variants AS pv', 'pv.id', '=', 'oi.product_variant_id')
        ->leftJoin('addresses AS a', 'a.id', '=', 'o.address_id')
        ->leftJoin('order_charges AS oc', 'o.id', '=', 'oc.order_id')
        ->leftJoin('products AS p', 'pv.product_id', '=', 'p.id');

    if (isset($store_id) && $store_id != null) {
        $regularOrderSearchRes->where('o.store_id', $store_id);
    }

    if (isset($order_id) && $order_id != null) {
        $regularOrderSearchRes->where('o.id', $order_id);
    }

    if (isset($user_id) && $user_id != null) {
        $regularOrderSearchRes->where('o.user_id', $user_id);
    }

    if (isset($delivery_boy_id) && $delivery_boy_id != null) {
        $regularOrderSearchRes->where('oi.delivery_boy_id', $delivery_boy_id);
    }

    if (isset($seller_id) && $seller_id != null) {
        $regularOrderSearchRes->where(function ($query) use ($seller_id) {
            $query->where('oi.seller_id', $seller_id)
                ->orWhere('oc.seller_id', $seller_id);
        });
    }

    if (isset($start_date) && $start_date != null && isset($end_date) && $end_date != null) {
        $regularOrderSearchRes->whereDate('o.created_at', '>=', $start_date)
            ->whereDate('o.created_at', '<=', $end_date);
    }

    if (!empty($start_date)) {
        $regularOrderSearchRes->whereDate('o.created_at', '>=', $start_date);
    }

    if (!empty($end_date)) {
        $regularOrderSearchRes->whereDate('o.created_at', '<=', $end_date);
    }

    if (isset($order_type) && $order_type != '' && $order_type == 'digital') {

        $regularOrderSearchRes->where('p.type', 'digital_product');
    }

    if (isset($order_type) && $order_type != '' && $order_type == 'simple') {
        $regularOrderSearchRes->where('p.type', '!=', 'digital_product');
    }

    if (isset($status) && !empty($status) && $status != '' && is_array($status) && count($status) > 0) {
        $status = array_map('trim', $status);
        $regularOrderSearchRes->whereIn('oi.active_status', $status);
    }

    if (isset($filters) && !empty($filters)) {
        $regularOrderSearchRes->where(function ($query) use ($filters) {
            foreach ($filters as $column => $value) {
                $query->orWhere($column, 'LIKE', '%' . $value . '%');
            }
        });
    }
    $regularOrderSearchRes->where('o.is_pos_order', 0);
    $regularOrderSearchRes->groupBy('o.id');
    $regularOrderSearchRes->orderBy($sort, $order);
    $regularOrderSearchRes = $regularOrderSearchRes->get();
    $comboOrderSearchRes = DB::table('orders AS o')
        ->select(
            'o.*',
            'u.username',
            'u.image as user_profile_image',
            'u.country_code',
            'a.name AS order_recipient_person',
            'oc.delivery_charge AS seller_delivery_charge',
            'oc.promo_discount AS seller_promo_discount',
            'cp.title as name',
            'cp.product_type as type',
            'cp.download_allowed',
            'cp.pickup_location',
            'cp.special_price',
            'cp.price',
            'cp.slug',
            'oi.order_type',
            'sd.user_id as main_seller_id',
        )
        ->leftJoin('users AS u', 'u.id', '=', 'o.user_id')
        ->leftJoin('order_items AS oi', 'o.id', '=', 'oi.order_id')
        ->leftJoin('seller_data AS sd', 'sd.id', '=', 'oi.seller_id')
        ->leftJoin('combo_products as cp', 'cp.id', '=', 'oi.product_variant_id')
        ->leftJoin('addresses AS a', 'a.id', '=', 'o.address_id')
        ->leftJoin('order_charges AS oc', 'o.id', '=', 'oc.order_id');

    if (isset($store_id) && $store_id != null) {
        $comboOrderSearchRes->where('o.store_id', $store_id);
    }

    if (isset($order_id) && $order_id != null) {
        $comboOrderSearchRes->where('o.id', $order_id);
    }

    if (isset($user_id) && $user_id != null) {
        $comboOrderSearchRes->where('o.user_id', $user_id);
    }

    if (isset($delivery_boy_id) && $delivery_boy_id != null) {
        $comboOrderSearchRes->where('oi.delivery_boy_id', $delivery_boy_id);
    }

    if (isset($seller_id) && $seller_id != null) {
        $comboOrderSearchRes->where(function ($query) use ($seller_id) {
            $query->where('oi.seller_id', $seller_id)
                ->orWhere('oc.seller_id', $seller_id);
        });
    }

    if (isset($start_date) && $start_date != null && isset($end_date) && $end_date != null) {
        $comboOrderSearchRes->whereDate('o.created_at', '>=', $start_date)
            ->whereDate('o.created_at', '<=', $end_date);
    }


    if (!empty($start_date)) {
        $comboOrderSearchRes->whereDate('o.created_at', '>=', $start_date);
    }

    if (!empty($end_date)) {
        $comboOrderSearchRes->whereDate('o.created_at', '<=', $end_date);
    }


    if (isset($order_type) && $order_type != '' && $order_type == 'digital') {
        $comboOrderSearchRes->where('cp.product_type', 'digital_product');
    }

    if (isset($order_type) && $order_type != '' && $order_type == 'simple') {
        $comboOrderSearchRes->where('cp.product_type', '!=', 'digital_product');
    }

    if (isset($status) && !empty($status) && $status != '' && is_array($status) && count($status) > 0) {
        $status = array_map('trim', $status);
        $comboOrderSearchRes->whereIn('oi.active_status', $status);
    }

    if (isset($combo_filters) && !empty($combo_filters)) {
        $comboOrderSearchRes->where(function ($query) use ($combo_filters) {
            foreach ($combo_filters as $column => $value) {
                $query->orWhere($column, 'LIKE', '%' . $value . '%');
            }
        });
    }
    $comboOrderSearchRes->where('o.is_pos_order', 0);
    $comboOrderSearchRes->groupBy('o.id');
    $comboOrderSearchRes->orderBy($sort, $order);


    $comboOrderSearchRes = $comboOrderSearchRes->get();


    $searchRes = $regularOrderSearchRes->merge($comboOrderSearchRes)->unique('id');

    $searchRes = $searchRes->sortBy($sort);
    // Applying limit and offset
    if ($limit != null || $offset != null) {
        $searchRes = $searchRes->slice($offset)->take($limit);
    }

    // Convert the sorted and sliced collection back to array
    $orderDetails = $searchRes->values()->all();

    for ($i = 0; $i < count($orderDetails); $i++) {
        $prCondition = ($user_id != NULL && !empty(trim($user_id)) && is_numeric($user_id))
            ? " pr.user_id = $user_id "
            : "";

        $crCondition = ($user_id != NULL && !empty(trim($user_id)) && is_numeric($user_id))
            ? " cr.user_id = $user_id "
            : "";
        $regularOrderItemData = DB::table('order_items AS oi')
            ->select(
                'oi.*',
                'p.id AS product_id',
                'p.is_cancelable',
                'p.is_attachment_required',
                'p.is_prices_inclusive_tax',
                'p.cancelable_till',
                'p.type AS product_type',
                'p.slug',
                'p.download_allowed',
                'p.download_link',
                'ss.store_name',
                'u.longitude AS seller_longitude',
                'u.mobile AS seller_mobile',
                'u.address AS seller_address',
                'u.latitude AS seller_latitude',
                DB::raw('(SELECT username FROM users WHERE id = oi.delivery_boy_id) AS delivery_boy_name'),
                'ss.store_description',
                'ss.rating AS seller_rating',
                'ss.logo AS seller_profile',
                'ot.courier_agency',
                'ot.tracking_id',
                'ot.awb_code',
                'ot.url',
                DB::raw('(SELECT username FROM users WHERE id = ' . $orderDetails[$i]->main_seller_id . ') AS seller_name'),
                'p.is_returnable',
                'pv.special_price',
                'pv.price AS main_price',
                'p.image',
                'p.name AS product_name',
                'p.pickup_location',
                'pv.weight',
                'p.rating AS product_rating',
                'pr.rating AS user_rating',
                'pr.images AS user_rating_images',
                'pr.title AS user_rating_title',
                'pr.comment AS user_rating_comment',
                'oi.status AS status',
                DB::raw('(SELECT COUNT(id) FROM order_items WHERE order_id = oi.order_id) AS order_counter'),
                DB::raw('(SELECT COUNT(active_status) FROM order_items WHERE active_status = "cancelled" AND order_id = oi.order_id) AS order_cancel_counter'),
                DB::raw('(SELECT COUNT(active_status) FROM order_items WHERE active_status = "returned" AND order_id = oi.order_id) AS order_return_counter')
            )
            ->leftJoin('product_variants AS pv', 'pv.id', '=', 'oi.product_variant_id')
            ->leftJoin('products AS p', 'pv.product_id', '=', 'p.id')
            ->leftJoin('product_ratings AS pr', function ($join) use ($prCondition) {
                $join->on('pv.product_id', '=', 'pr.product_id');
                if (!empty($prCondition)) {
                    $join->whereRaw($prCondition);
                }
            })
            ->leftJoin('seller_store AS ss', 'ss.seller_id', '=', 'oi.seller_id')
            ->leftJoin('users AS u', 'u.id', '=', 'ss.user_id')
            ->leftJoin('order_trackings AS ot', 'ot.order_item_id', '=', 'oi.id')
            ->leftJoin('users AS db', 'db.id', '=', 'oi.delivery_boy_id')
            ->leftJoin('users AS s', 's.id', '=', 'oi.seller_id')
            ->where('oi.order_type', 'regular_order')
            ->where('oi.order_id', $orderDetails[$i]->id)
            ->when(isset($seller_id) && $seller_id != null, function ($query) use ($seller_id) {
                $query->where('oi.seller_id', $seller_id)
                    ->where("oi.active_status", "!=", 'awaiting');
            })
            ->when(isset($order_type) && $order_type != '', function ($query) use ($order_type) {
                $query->where("p.type", $order_type == 'digital' ? '=' : '!=', 'digital_product');
            })
            ->when(isset($delivery_boy_id) && $delivery_boy_id != null, function ($query) use ($delivery_boy_id) {
                $query->where('oi.delivery_boy_id', '=', $delivery_boy_id);
            })
            ->when(isset($status) && !empty($status) && is_array($status) && count($status) > 0, function ($query) use ($status) {
                $query->whereIn('oi.active_status', array_map('trim', $status));
            })
            ->groupBy('oi.id')
            ->get();
        // dd($regularOrderItemData->toSql());

        $comboOrderItemData = DB::table('order_items AS oi')
            ->select(
                'oi.*',
                'cp.id AS product_id',
                'cp.is_cancelable',
                'cp.is_attachment_required',
                'cp.is_prices_inclusive_tax',
                'cp.cancelable_till',
                'cp.product_type',
                'cp.slug',
                'cp.download_allowed',
                'cp.download_link',
                'ss.store_name',
                'u.longitude AS seller_longitude',
                'u.mobile AS seller_mobile',
                'u.address AS seller_address',
                'u.latitude AS seller_latitude',
                DB::raw('(SELECT username FROM users WHERE id = oi.delivery_boy_id) AS delivery_boy_name'),
                'ss.store_description',
                'ss.rating AS seller_rating',
                'ss.logo AS seller_profile',
                'ot.courier_agency',
                'ot.tracking_id',
                'ot.awb_code',
                'ot.url',
                DB::raw('(SELECT username FROM users WHERE id = ' . $orderDetails[$i]->main_seller_id . ') AS seller_name'),
                'cp.is_returnable',
                'cp.special_price',
                'cp.price AS main_price',
                'cp.image',
                'cp.title AS product_name',
                'cp.pickup_location',
                'cp.weight',
                'cp.rating AS product_rating',
                'cr.rating AS user_rating',
                'cr.title AS user_rating_title',
                'cr.images AS user_rating_images',
                'cr.comment AS user_rating_comment',
                'oi.status AS status',
                DB::raw('(SELECT COUNT(id) FROM order_items WHERE order_id = oi.order_id) AS order_counter'),
                DB::raw('(SELECT COUNT(active_status) FROM order_items WHERE active_status = "cancelled" AND order_id = oi.order_id) AS order_cancel_counter'),
                DB::raw('(SELECT COUNT(active_status) FROM order_items WHERE active_status = "returned" AND order_id = oi.order_id) AS order_return_counter')
            )
            ->leftJoin('combo_products AS cp', 'cp.id', '=', 'oi.product_variant_id')
            ->leftJoin('combo_product_ratings AS cr', function ($join) use ($crCondition) {
                $join->on('cp.id', '=', 'cr.product_id');
                if (!empty($crCondition)) {
                    $join->whereRaw($crCondition);
                }
            })
            ->leftJoin('seller_store AS ss', 'ss.seller_id', '=', 'oi.seller_id')
            ->leftJoin('order_trackings AS ot', 'ot.order_item_id', '=', 'oi.id')
            ->leftJoin('users AS u', 'u.id', '=', 'oi.user_id')
            ->orWhereIn('oi.order_id', [$orderDetails[$i]->id])
            ->where('oi.order_type', 'combo_order')
            ->when(isset($seller_id) && $seller_id != null, function ($query) use ($seller_id) {
                $query->where('oi.seller_id', $seller_id);
                $query->where("oi.active_status", "!=", 'awaiting');
            })
            ->when(isset($order_type) && $order_type != '' && $order_type == 'digital', function ($query) {
                $query->where("cp.product_type", '=', 'digital_product');
            })
            ->when(isset($order_type) && $order_type != '' && $order_type == 'simple', function ($query) {
                $query->where("cp.product_type", '!=', 'digital_product');
            })
            ->when(isset($delivery_boy_id) && $delivery_boy_id != null, function ($query) use ($delivery_boy_id) {
                $query->where('oi.delivery_boy_id', '=', $delivery_boy_id);
            })
            ->when(isset($status) && !empty($status) && $status != '' && is_array($status) && count($status) > 0, function ($query) use ($status) {
                $status = array_map('trim', $status);
                $query->whereIn('oi.active_status', $status);
            })
            ->groupBy('oi.id')
            ->get();


        $orderItemData = $regularOrderItemData->merge($comboOrderItemData);

        //get return request data
        $return_request = fetchDetails('return_requests', ['user_id' => $user_id]);

        if ($orderDetails[$i]->payment_method == "bank_transfer") {
            $bankTransfer = fetchDetails('order_bank_transfers', ['order_id' => $orderDetails[$i]->id], ['attachments', 'id', 'status']);
            $bankTransfer = collect($bankTransfer); // convert array to collection because laravel map function is expecting a collection
            if (!$bankTransfer->isEmpty()) {

                $bankTransfer = $bankTransfer->map(function ($attachment) {

                    return [
                        'id' => $attachment->id,
                        'attachment' => asset($attachment->attachments),
                        'banktransfer_status' => $attachment->status,
                    ];
                });
            }
        }

        $orderDetails[$i]->latitude = (isset($orderDetails[$i]->latitude) && !empty($orderDetails[$i]->latitude)) ? $orderDetails[$i]->latitude : "";
        $orderDetails[$i]->longitude = (isset($orderDetails[$i]->longitude) && !empty($orderDetails[$i]->longitude)) ? $orderDetails[$i]->longitude : "";
        $orderDetails[$i]->order_recipient_person = (isset($orderDetails[$i]->order_recipient_person) && !empty($orderDetails[$i]->order_recipient_person)) ? $orderDetails[$i]->order_recipient_person : "";
        $orderDetails[$i]->attachments = (isset($bankTransfer) && !empty($bankTransfer)) ? $bankTransfer : [];
        $orderDetails[$i]->notes = (isset($orderDetails[$i]->notes) && !empty($orderDetails[$i]->notes)) ? $orderDetails[$i]->notes : "";
        $orderDetails[$i]->payment_method = ($orderDetails[$i]->payment_method == 'bank_transfer') ? ucwords(str_replace('_', " ", $orderDetails[$i]->payment_method)) : $orderDetails[$i]->payment_method;
        $orderDetails[$i]->courier_agency = "";
        $orderDetails[$i]->tracking_id = "";
        $orderDetails[$i]->url = "";

        if (isset($orderDetails[$i]->address_id) && $orderDetails[$i]->address_id != "" && $orderDetails[$i]->address_id != null) {
            $city_id = fetchDetails('addresses', ['id' => $orderDetails[$i]->address_id], 'city_id');
            $city_id = $city_id[0]->city_id ?? [];
        } else {
            $city_id = [];
        }

        $orderDetails[$i]->is_shiprocket_order = (isset($city_id) && $city_id == 0) ? 1 : 0;

        if (isset($seller_id) && !empty($seller_id)) {
            if (isset($orderDetails[$i]->seller_delivery_charge)) {
                $orderDetails[$i]->delivery_charge = $orderDetails[$i]->seller_delivery_charge;
            } else {
                $orderDetails[$i]->delivery_charge = $orderDetails[$i]->delivery_charge;
            }
        } else {
            $orderDetails[$i]->delivery_charge = $orderDetails[$i]->delivery_charge;
        }

        if (isset($orderDetails[$i]->seller_promo_dicount)) {
            $orderDetails[$i]->promo_discount = $orderDetails[$i]->seller_promo_dicount;
        } else {
            $orderDetails[$i]->promo_discount = $orderDetails[$i]->promo_discount;
        }
        $returnable_count = 0;
        $cancelable_count = 0;
        $already_returned_count = 0;
        $already_cancelled_count = 0;
        $return_request_submitted_count = 0;
        $total_tax_percent = $total_tax_amount = $item_subtotal = 0;


        for ($k = 0; $k < count($orderItemData); $k++) {
            $download_allowed[] = isset($orderItemData[$k]->download_allowed) ? intval($orderItemData[$k]->download_allowed) : 0;
            if (isset($orderItemData[$k]->quantity) && $orderItemData[$k]->quantity != 0) {
                $price = $orderItemData[$k]->special_price != '' && $orderItemData[$k]->special_price != null && $orderItemData[$k]->special_price > 0 && $orderItemData[$k]->special_price < $orderItemData[$k]->main_price ? $orderItemData[$k]->special_price : $orderItemData[$k]->main_price;
                $amount = $orderItemData[$k]->quantity * $price;
            }
            if (!empty($orderItemData)) {
                $user_rating_images = json_decode($orderItemData[$k]->user_rating_images, true);
                $orderItemData[$k]->user_rating_images = array();

                if (!empty($user_rating_images)) {
                    $orderItemData[$k]->user_rating_images = array_map(function ($image) {
                        return getImageUrl($image, "", "", 'image');
                    }, $user_rating_images);
                }

                if (isset($orderItemData[$k]->is_prices_inclusive_tax) && $orderItemData[$k]->is_prices_inclusive_tax == 1) {
                    $price_tax_amount = $price - ($price * (100 / (100 + $orderItemData[$k]->tax_percent)));
                } else {
                    $price_tax_amount = $price * ($orderItemData[$k]->tax_percent / 100);
                }

                $orderItemData[$k]->is_cancelable = intval($orderItemData[$k]->is_cancelable);
                $orderItemData[$k]->is_attachment_required = intval($orderItemData[$k]->is_attachment_required);
                $orderItemData[$k]->tax_amount = isset($price_tax_amount) && !empty($price_tax_amount) ? (float) number_format($price_tax_amount, 2) : 0.00;
                $orderItemData[$k]->net_amount = $orderItemData[$k]->price - $orderItemData[$k]->tax_amount;
                $item_subtotal += $orderItemData[$k]->sub_total;
                $orderItemData[$k]->seller_name = (!empty($orderItemData[$k]->seller_name)) ? $orderItemData[$k]->seller_name : '';
                $orderItemData[$k]->awb_code = isset($orderItemData[$k]->awb_code) && !empty($orderItemData[$k]->awb_code) && $orderItemData[$k]->awb_code != 'NULL' ? $orderItemData[$k]->awb_code : '';
                $orderItemData[$k]->store_description = (!empty($orderItemData[$k]->store_description)) ? $orderItemData[$k]->store_description : '';
                $orderItemData[$k]->seller_rating = (!empty($orderItemData[$k]->seller_rating)) ? number_format($orderItemData[$k]->seller_rating, 1) : "0";
                $orderItemData[$k]->seller_profile = (!empty($orderItemData[$k]->seller_profile)) ? getImageUrl($orderItemData[$k]->seller_profile, "", "", 'image') : '';
                $orderItemData[$k]->seller_latitude = (isset($orderItemData[$k]->seller_latitude) && !empty($orderItemData[$k]->seller_latitude)) ? $orderItemData[$k]->seller_latitude : '';
                $orderItemData[$k]->seller_longitude = (isset($orderItemData[$k]->seller_longitude) && !empty($orderItemData[$k]->seller_longitude)) ? $orderItemData[$k]->seller_longitude : '';
                $orderItemData[$k]->seller_address = (isset($orderItemData[$k]->seller_address) && !empty($orderItemData[$k]->seller_address)) ? $orderItemData[$k]->seller_address : '';
                $orderItemData[$k]->seller_mobile = (isset($orderItemData[$k]->seller_mobile) && !empty($orderItemData[$k]->seller_mobile)) ? $orderItemData[$k]->seller_mobile : '';
                $orderItemData[$k]->attachment = (isset($orderItemData[$k]->attachment) && !empty($orderItemData[$k]->attachment)) ? asset('/storage/' . $orderItemData[$k]->attachment) : '';

                if (isset($seller_id) && $seller_id != null) {
                    $orderItemData[$k]->otp = (getSellerPermission($orderItemData[$k]->seller_id, $store_id, "view_order_otp")) ? $orderItemData[$k]->otp : "0";
                }
                $orderItemData[$k]->pickup_location = isset($orderItemData[$k]->pickup_location) && !empty($orderItemData[$k]->pickup_location) && $orderItemData[$k]->pickup_location != 'NULL' ? $orderItemData[$k]->pickup_location : '';
                $orderItemData[$k]->hash_link = isset($orderItemData[$k]->hash_link) && !empty($orderItemData[$k]->hash_link) && $orderItemData[$k]->hash_link != 'NULL' ? asset('storage' . $orderItemData[$k]->hash_link) : '';
                $varaint_data = getVariantsValuesById($orderItemData[$k]->product_variant_id);

                $orderItemData[$k]->varaint_ids = (!empty($varaint_data)) ? $varaint_data[0]['variant_ids'] : '';
                $orderItemData[$k]->variant_values = (!empty($varaint_data)) ? $varaint_data[0]['variant_values'] : '';
                $orderItemData[$k]->attr_name = (!empty($varaint_data)) ? $varaint_data[0]['attr_name'] : '';
                $orderItemData[$k]->product_rating = (!empty($orderItemData[$k]->product_rating)) ? number_format($orderItemData[$k]->product_rating, 1) : "0";
                $orderItemData[$k]->name = (!empty($orderItemData[$k]->name)) ? $orderItemData[$k]->name : $orderItemData[$k]->product_name;
                $orderItemData[$k]->variant_values = (!empty($orderItemData[$k]->variant_values)) ? $orderItemData[$k]->variant_values : $orderItemData[$k]->variant_values;
                $orderItemData[$k]->user_rating = (!empty($orderItemData[$k]->user_rating)) ? $orderItemData[$k]->user_rating : '0';
                $orderItemData[$k]->user_rating_comment = (!empty($orderItemData[$k]->user_rating_comment)) ? $orderItemData[$k]->user_rating_comment : '';
                $orderItemData[$k]->status = json_decode($orderItemData[$k]->status);

                if (!in_array($orderItemData[$k]->active_status, ['returned', 'cancelled'])) {
                    $total_tax_percent = $total_tax_percent + $orderItemData[$k]->tax_percent;
                    $total_tax_amount =  $orderItemData[$k]->tax_amount * $orderItemData[$k]->quantity;
                }

                $orderItemData[$k]->image_sm = (empty($orderItemData[$k]->image) || file_exists(public_path(config('constants.MEDIA_PATH') . $orderItemData[$k]->image)) == FALSE) ? str_replace('///', '/', getImageUrl('', '', '', 'image', 'NO_IMAGE')) : str_replace('///', '/', getImageUrl($orderItemData[$k]->image, 'thumb', 'sm'));
                $orderItemData[$k]->image_md = (empty($orderItemData[$k]->image) || file_exists(public_path(config('constants.MEDIA_PATH') . $orderItemData[$k]->image)) == FALSE) ? str_replace('///', '/', getImageUrl('', '', '', 'image', 'NO_IMAGE')) : str_replace('///', '/', getImageUrl($orderItemData[$k]->image, 'thumb', 'md'));
                $orderItemData[$k]->image = (empty($orderItemData[$k]->image) || file_exists(public_path(config('constants.MEDIA_PATH') . $orderItemData[$k]->image)) == FALSE) ? str_replace('///', '/', getImageUrl('', '', '', 'image', 'NO_IMAGE')) : str_replace('///', '/', getImageUrl($orderItemData[$k]->image));
                $orderItemData[$k]->is_already_returned = ($orderItemData[$k]->active_status == 'returned') ? '1' : '0';
                $orderItemData[$k]->is_already_cancelled = ($orderItemData[$k]->active_status == 'cancelled') ? '1' : '0';

                $return_request_key = array_search($orderItemData[$k]->id, array_column($return_request, 'order_item_id'));

                if ($return_request_key !== false) {
                    $orderItemData[$k]->return_request_submitted = $return_request[$return_request_key]->status;

                    if ($orderItemData[$k]->return_request_submitted == '1') {
                        $return_request_submitted_count += $orderItemData[$k]->return_request_submitted;
                    }
                } else {
                    $orderItemData[$k]->return_request_submitted = '';
                    $return_request_submitted_count = null;
                }

                $orderItemData[$k]->courier_agency = (isset($orderItemData[$k]->courier_agency) && !empty($orderItemData[$k]->courier_agency)) ? $orderItemData[$k]->courier_agency : "";
                $orderItemData[$k]->tracking_id = (isset($orderItemData[$k]->tracking_id) && !empty($orderItemData[$k]->tracking_id)) ? $orderItemData[$k]->tracking_id : "";
                $orderItemData[$k]->url = (isset($orderItemData[$k]->url) && !empty($orderItemData[$k]->url)) ? $orderItemData[$k]->url : "";
                $orderItemData[$k]->shiprocket_order_tracking_url = (isset($orderItemData[$k]->awb_code) && !empty($orderItemData[$k]->awb_code) && $orderItemData[$k]->awb_code != '' && $orderItemData[$k]->awb_code != null) ? "https://shiprocket.co/tracking/" . $orderItemData[$k]->awb_code : "";
                $orderItemData[$k]->deliver_by = (isset($orderItemData[$k]->delivery_boy_name) && !empty($orderItemData[$k]->delivery_boy_name)) ? $orderItemData[$k]->delivery_boy_name : "";
                $orderItemData[$k]->delivery_boy_id = (isset($orderItemData[$k]->delivery_boy_id) && !empty($orderItemData[$k]->delivery_boy_id)) ? $orderItemData[$k]->delivery_boy_id : "";
                $orderItemData[$k]->discounted_price = (isset($orderItemData[$k]->discounted_price) && !empty($orderItemData[$k]->discounted_price)) ? $orderItemData[$k]->discounted_price : "";
                $orderItemData[$k]->delivery_boy_name = (isset($orderItemData[$k]->delivery_boy_name) && !empty($orderItemData[$k]->delivery_boy_name)) ? $orderItemData[$k]->delivery_boy_name : "";

                if (($orderDetails[$i]->type == 'digital_product' && in_array(0, $download_allowed)) || ($orderDetails[$i]->type != 'digital_product' && in_array(0, $download_allowed))) {
                    $orderDetails[$i]->download_allowed = 0;
                    $orderItemData[$k]->download_link = '';
                    $orderItemData[$k]->download_allowed = 0;
                } else {
                    $orderDetails[$i]->download_allowed = 1;
                    $orderItemData[$k]->download_link = asset('storage' . $orderItemData[$k]->download_link);
                    $orderItemData[$k]->download_allowed = 1;
                }
                $orderItemData[$k]->email = (isset($orderItemData[$k]->email) && !empty($orderItemData[$k]->email) ? $orderItemData[$k]->email : '');

                $returnable_count += $orderItemData[$k]->is_returnable;
                $cancelable_count += $orderItemData[$k]->is_cancelable;
                $already_returned_count += $orderItemData[$k]->is_already_returned;
                $already_cancelled_count += $orderItemData[$k]->is_already_cancelled;

                // $delivery_date = isset($orderItemData[$k]->status[3][1]) ? $orderItemData[$k]->status[3][1] : '';
                foreach ($orderItemData[$k]->status as $status) {
                    if ($status[0] == 'delivered') {
                        $delivery_date = $status[1];
                    }
                }
                $settings = getSettings('system_settings', true, true);
                $settings = json_decode($settings, true);
                $timestemp = strtotime($delivery_date);
                $today = date('Y-m-d');
                $return_till = date('Y-m-d', strtotime($delivery_date . ' + ' . $settings['max_days_to_return_item'] . ' days'));

                $orderItemData[$k]->is_returnable = isset($delivery_date) && !empty($delivery_date) && ($today < $return_till) ? 1 : 0;
            }
        }

        $orderDetails[$i]->delivery_time = (isset($orderDetails[$i]->delivery_time) && !empty($orderDetails[$i]->delivery_time)) ? $orderDetails[$i]->delivery_time : "";
        $orderDetails[$i]->delivery_date = (isset($orderDetails[$i]->delivery_date) && !empty($orderDetails[$i]->delivery_date)) ? $orderDetails[$i]->delivery_date : "";
        $orderDetails[$i]->is_returnable = ($returnable_count >= 1 && isset($delivery_date) && !empty($delivery_date) && $today < $return_till) ? 1 : 0;
        $orderDetails[$i]->is_cancelable = ($cancelable_count >= 1) ? 1 : 0;
        $orderDetails[$i]->is_already_returned = ($already_returned_count == count($orderItemData)) ? '1' : '0';
        $orderDetails[$i]->is_already_cancelled = ($already_cancelled_count == count($orderItemData)) ? '1' : '0';

        $orderDetails[$i]->user_profile_image = getMediaImageUrl($orderDetails[$i]->user_profile_image, 'USER_IMG_PATH');

        if ($return_request_submitted_count == null) {
            $orderDetails[$i]->return_request_submitted = '';
        } else {
            $orderDetails[$i]->return_request_submitted = ($return_request_submitted_count == count($orderItemData)) ? '1' : '0';
        }

        if ((isset($delivery_boy_id) && $delivery_boy_id != null) || (isset($seller_id) && $seller_id != null)) {

            $orderDetails[$i]->total = strval($item_subtotal);
            $orderDetails[$i]->final_total = strval($item_subtotal + $orderDetails[$i]->delivery_charge);

            $orderDetails[$i]->total_payable = strval($item_subtotal + $orderDetails[$i]->delivery_charge - $orderDetails[$i]->promo_discount - $orderDetails[$i]->wallet_balance);
        } else {
            $orderDetails[$i]->total = strval($orderDetails[$i]->total);
        }

        $orderDetails[$i]->address = (isset($orderDetails[$i]->address) && !empty($orderDetails[$i]->address)) ? outputEscaping($orderDetails[$i]->address) : "";
        $orderDetails[$i]->username = outputEscaping($orderDetails[$i]->username);
        $orderDetails[$i]->country_code = (isset($orderDetails[$i]->country_code) && !empty($orderDetails[$i]->country_code)) ? $orderDetails[$i]->country_code : '';
        $orderDetails[$i]->total_tax_percent = strval($total_tax_percent);
        $orderDetails[$i]->total_tax_amount = strval($total_tax_amount);
        unset($orderDetails[$i]->main_seller_id);
        if (isset($seller_id) && $seller_id != null) {
            if ($download_invoice == true || $download_invoice == 1) {
            }
        } else {
            if ($download_invoice == true || $download_invoice == 1) {
            }
        }

        if (!empty($orderItemData)) {

            $orderDetails[$i]->order_items = $orderItemData;
        } else {
            $orderDetails[$i]->order_items = [];
        }
    }
    $collection = collect($orderDetails);

    $order_data['total'] = $total;
    $order_data['order_data'] = $collection;
    return $order_data;
}
