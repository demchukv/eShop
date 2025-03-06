<?php

namespace App\Http\Controllers;

use App\Libraries\Shiprocket;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\ProductFaq;
use App\Models\Zipcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // public function get_products(Request $request, $param = '')
    // {

    //     $tags = [];
    //     $limit = $request->input('limit', 25);
    //     $offset = $request->input('offset', 0);
    //     $order = $request->filled('order') ? $request->input('order') : 'ASC';
    //     $sort = $request->filled('sort') ? $request->input('sort') : 'p.row_order';
    //     if ($sort == 'pv.price') {
    //         $sort = "price";
    //     }
    //     $seller_id = $request->filled('seller_id') ? $request->input('seller_id') : null;
    //     $filters['search'] = $request->filled('search') ? trim($request->input('search')) : '';
    //     $filters['tags'] = $request->input('tags', '');
    //     $filters['attribute_value_ids'] = $request->filled('attribute_value_ids') ? $request->input('attribute_value_ids') : null;
    //     $filters['is_similar_products'] = $request->filled('is_similar_products') ? $request->input('is_similar_products') : null;
    //     $filters['discount'] = $request->filled('discount') ? $request->input('discount', 0) : 0;
    //     $filters['product_type'] = $request->input('top_rated_product', 0) == 1 ? 'top_rated_product_including_all_products' : null;
    //     $filters['min_price'] = $request->filled('min_price') ? $request->input('min_price') : 0;
    //     $filters['max_price'] = $request->filled('max_price') ? $request->input('max_price') : 0;
    //     $zipcode = $request->filled('zipcode') ? $request->input('zipcode') : 0;
    //     //find product according to zipcode
    //     if ($request->filled('zipcode')) {
    //         $is_pincode = Zipcode::where('zipcode', $zipcode)->exists();
    //         if ($is_pincode) {
    //             $zipcode_id = Zipcode::where('zipcode', $zipcode)->firstOrFail()->id;
    //             $zipcode = $zipcode_id[0]['id'];
    //         } else {
    //             return response()->json([
    //                 'error' => true,
    //                 'message' => 'Products Not Found !'
    //             ], 422);
    //         }
    //     }
    //     $category_id = $request->input('category_id', null);
    //     $product_id = $request->input('id', null);
    //     $user_id = Auth::user() != '' ? Auth::user()->id : 0;
    //     $product_ids = $request->input('product_ids', null);
    //     $product_variant_ids = $request->filled('product_variant_ids') ? $request->input('product_variant_ids') : null;

    //     if (!is_null($product_ids)) {
    //         $product_id = explode(",", $product_ids);
    //     }
    //     if (!is_null($category_id)) {
    //         $category_id = explode(",", $category_id);
    //     }
    //     if (!is_null($product_variant_ids)) {
    //         $filters['product_variant_ids'] = explode(",", $product_variant_ids);
    //     }

    //     if ($param == 'grid-view' || $param == 'list-view') {

    //         $products = fetchProduct($user_id, (isset($filters)) ? $filters : null, $product_id, $category_id, $limit, $offset, $sort, $order, null, $zipcode, $seller_id);

    //         foreach ($products['product'] as $product) {

    //             if (!empty($product->tags)) {
    //                 $tags = array_values(array_unique(array_merge($tags, $product->tags)));
    //             }
    //         }
    //         if (!empty($products['product'])) {
    //             $response = [
    //                 'error' => false,
    //                 'message' => 'Products retrieved successfully!',
    //                 'min_price' => isset($products['min_price']) && !empty($products['min_price']) ? strval($products['min_price']) : '0',
    //                 'max_price' => isset($products['max_price']) && !empty($products['max_price']) ? strval($products['max_price']) : '0',
    //                 'search' => $filters['search'],
    //                 'filters' => isset($products['filters']) && !empty($products['filters']) ? $products['filters'] : [],
    //                 'tags' => !empty($tags) ? $tags : [],
    //                 'total' => isset($products['total']) ? strval($products['total']) : '',
    //                 'offset' => $offset,
    //                 'data' => $products['product'],
    //             ];
    //         } else {
    //             $response = [
    //                 'error' => true,
    //                 'message' => 'Products Not Found!',
    //                 'data' => [],
    //             ];
    //         }
    //         if ($param == 'grid-view') {
    //             return Inertia::render('Products', [
    //                 'products' => $response,
    //             ]);
    //         } elseif ($param == 'list-view') {
    //             return Inertia::render('ProductsListView', [
    //                 'products' => $response,
    //             ]);
    //         }
    //     } else {
    //         $product_id = fetchDetails('products', ['slug' => $param], '*');


    //         $id = isset($product_id[0]->id) && !empty($product_id[0]->id) ? $product_id[0]->id : '';


    //         $product = fetchProduct($user_id, (isset($filters)) ? $filters : null, $id, $category_id, $limit, $offset, $sort, $order, null, $zipcode, $seller_id);
    //         foreach ($product['product'] as $product) {

    //             if (!empty($product->tags)) {
    //                 $tags = array_values(array_unique(array_merge($tags, $product->tags)));
    //             }
    //         }
    //         if (!empty($product->product)) {
    //             $response = [
    //                 'error' => false,
    //                 'message' => 'Products retrieved successfully!',
    //                 'min_price' => isset($product['min_price']) && !empty($product['min_price']) ? strval($product['min_price']) : '0',
    //                 'max_price' => isset($product['max_price']) && !empty($product['max_price']) ? strval($product['max_price']) : '0',
    //                 'search' => $filters['search'],
    //                 'filters' => isset($product['filters']) && !empty($product['filters']) ? $product['filters'] : [],
    //                 'tags' => !empty($tags) ? $tags : [],
    //                 'total' => isset($product['total']) ? strval($product['total']) : '',
    //                 'offset' => $offset,
    //                 'data' => $product['product'],
    //             ];
    //         } else {
    //             $response = [
    //                 'error' => true,
    //                 'message' => 'Products Not Found!',
    //                 'data' => [],
    //             ];
    //         }
    //         return Inertia::render('ProductPage', [
    //             'product' => $product,
    //         ]);
    //     }
    // }

    public function AddProductFaqs(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'product_id' => 'required|integer|exists:products,id',
            'question' => 'required',
        ]);

        $faq = new ProductFaq();
        $faq->user_id = $request->user_id;
        $faq->product_id = $request->product_id;
        $faq->question = $request->question;

        $faq->save();

        return response()->json(['message' => 'Product faq add successfully']);
    }

    public function add_to_favorites(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'product_id' => 'required|integer|exists:products,id',
            'product_type' => 'required',
        ]);

        $user_id = Auth::user() != '' ? Auth::user()->id : 0;

        $product_id = $request->input('product_id');
        $product_type = $request->input('product_type');
        if (isExist(['user_id' => $user_id, 'product_id' => $product_id,'product_type' => $product_type], 'favorites')) {
            $response = [
                'error' => true,
                'message' => 'Already added to favorite !',
                'data' => [],
            ];
            return response()->json($response);
        }
        $data = [
            'user_id' => $user_id,
            'product_id' => $product_id,
            'product_type' => $product_type,
        ];
        $fav_res = Favorite::create($data);
        $store_id = session('store_id');
        $favorite_count = getFavorites(user_id:$user_id,store_id:$store_id);
        if ($fav_res) {
            $response = [
                'error' => false,
                'message' => 'Added to favorite !',
                'wishlist_count' => $favorite_count['favorites_count'],
                'data' => [],
            ];
        } else {
            $response = [
                'error' => true,
                'message' => 'Not Added to favorite !',
                'data' => [],
            ];
        }
        return response()->json($response);
    }

    // public function get_favorites(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'limit' => 'numeric',
    //         'offset' => 'numeric',
    //     ]);
    //     if ($validator->fails()) {
    //         $response = [
    //             'error' => true,
    //             'message' => $validator->errors()->first(),
    //             'code' => 102,
    //         ];
    //         return response()->json($response);
    //     } else {

    //         $user_id = Auth::user() != '' ? Auth::user()->id : 0;

    //         $limit = $request->input('limit', 25);
    //         $offset = $request->input('offset', 0);

    //         $query = DB::table('favorites as f')
    //             ->join('products as p', 'p.id', '=', 'f.product_id')
    //             ->join('product_variants as pv', 'pv.product_id', '=', 'p.id')
    //             ->where('f.user_id', $user_id)
    //             ->where('p.status', 1)
    //             ->select(DB::raw('(select count(id) from favorites where user_id = ' . $user_id . ') as total'), 'f.*')
    //             ->groupBy('f.product_id')
    //             ->limit($limit)
    //             ->offset($offset);


    //         $total = 0;
    //         $res1 = [];
    //         $res = $query->get()->toArray();
    //         if (!empty($res)) {

    //             $total = $res[0]->total;
    //             foreach ($res as $item) {

    //                 unset($item->total);
    //                 $proDetails = fetchProduct($user_id, null, $item->product_id ?? null);
    //                 if (!empty($proDetails)) {
    //                     $res1[] = $proDetails['product'][0];
    //                 }
    //             }
    //         } else {
    //             $response = [
    //                 'error' => true,
    //                 'message' => 'No Favorite Product(s) Are Added',
    //                 'total' => [],
    //                 'data' => [],
    //             ];
    //             return response()->json($response);
    //         }
    //         $response = [
    //             'error' => false,
    //             'message' => 'Data Retrieved Successfully',
    //             'total' => $total,
    //             'data' => $res1,
    //         ];
    //         return Inertia::render('Favorites', [
    //             'response' => $response,
    //         ]);
    //     }
    // }

    public function remove_from_favorite(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'productId' => 'required|integer',
            'productType' => 'required',
        ]);
        $product_id = $request->input('productId');
        if ($product_id == '') {
            $response = [
                'error' => true,
                'message' => 'Please pass product id',
                'code' => 102,
            ];
            return response()->json($response);
        } else {
            // uncomment after dynamic login

            if (auth()->check()) {
                $user_id = Auth::user() != '' ? Auth::user()->id : 0;
            } else {
                $response = [
                    'error' => true,
                    'message' => 'Please Login first.',
                    'code' => 102,
                ];
                return response()->json($response);
            }


            $productType = $request->input('productType');
            if (!isExist(['user_id' => $user_id, 'product_id' => $product_id,'product_type' =>$productType ], 'favorites')) {
                $response = [
                    'error' => true,
                    'message' => 'Item not added as favorite !',
                    'data' => [],
                ];
                return response()->json($response);
            }
            $data = [
                'user_id' => $user_id,
                'product_id' => $product_id,
                'product_type' => $productType,
            ];
            deleteDetails($data, 'favorites');
            $favorite_count = getFavorites($user_id);
            $response = [
                'error' => false,
                'message' => 'Removed from favorite',
                'wishlist_count' => $favorite_count['favorites_count'],
                'data' => [],
            ];
            return response()->json($response);
        }
    }

    // public function category($category_slug = '')
    // {
    //     $offset =  0;
    //     $sort = 'id';
    //     $order =  'ASC';
    //     $limit = 12;

    //     $category_slug = isset($category_slug) && !empty($category_slug) ? $category_slug : '';

    //     $category_id = fetchDetails('categories', ['slug' => $category_slug], '*');
    //     $category_id = $category_id[0]->id;

    //     $products = fetchProduct(null, '', null, $category_id, $limit, $offset, $sort, $order);
    //     $categories = getCategories($category_id);
    //     return Inertia::render('CategoryProductListing', [
    //         'products' => $products,
    //         'categories' => $categories['categories'][0]
    //     ]);
    // }

    // public function brand($brand_slug = '')
    // {
    //     $brand_id = fetchDetails('brands', ['slug' => $brand_slug], '*');
    //     $brand_id = $brand_id[0]->id;
    //     $products = fetchProduct(null, '', null, '', 10, 0, '', 'p.id', '', '', '', $brand_id);
    //     return Inertia::render('BrandProductListing', [
    //         'products' => $products,
    //     ]);
    // }

    public function check_zipcode(request $request)
    {
        $validator = Validator::make(request()->all(), [
            'product_id' => 'required|numeric|exists:products,id',
            'zipcode' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first(),
                'data' => [],
            ]);
        } else {
            $zipcode = request('zipcode');
            $is_pincode = isexist(['zipcode' => $zipcode], 'zipcodes');
            $product_id = request('product_id');
            if ($is_pincode) {
                $zipcode_id = fetchdetails('zipcodes', ['zipcode' => $zipcode], 'id');
                $zipcode_id = $zipcode_id !== '' ? $zipcode_id[0]->id : '';
                $is_available = isProductDelivarable('zipcode', $zipcode_id, $product_id);
                if ($is_available) {
                    session(['valid_zipcode' => $zipcode]);
                    return response()->json([
                        'error' => false,
                        'message' => 'Product is deliverable on "' . $zipcode . '"',
                    ]);
                } else {
                    return response()->json([
                        'error' => true,
                        'message' => 'Product is not deliverable on "' . $zipcode . '"',
                    ]);
                }
            } else {

                $product_data = fetchdetails('products', ['id' => $product_id], 'pickup_location');
                $product_variant_data = fetchdetails('product_variants', ['product_id' => $product_id], 'weight');
                $pickup_pincode = fetchdetails('pickup_locations', ['pickup_location' => $product_data[0]->pickup_location], 'pincode');;

                if (!empty($zipcode)) {
                    $availability_deliverability = [
                        'pickup_postcode' => $pickup_pincode ?? "",
                        'delivery_postcode' => $zipcode,
                        'cod' => 0,
                        'weight' => $product_variant_data,
                    ];
                    $shiprocket  =  new Shiprocket();

                    $check_deliverability = $shiprocket->check_serviceability($availability_deliverability);

                    if (isset($check_deliverability['status_code']) && $check_deliverability['status_code'] == 422) {
                        return response()->json([
                            'error' => true,
                            'message' => 'Invalid Delivery Pincode "' . $zipcode . '"',
                        ]);
                    } else {
                        if (isset($check_deliverability['status']) && $check_deliverability['status'] == 200 && !empty($check_deliverability['data']['available_courier_companies'])) {
                            $estimate_date = $check_deliverability['data']['available_courier_companies'][0]['etd'];
                            session(['valid_zipcode' => $zipcode]);
                            return response()->json([
                                'error' => false,
                                'message' => 'Product is deliverable by ' . $estimate_date . '',
                            ]);
                        } else {
                            return response()->json([
                                'error' => true,
                                'message' => 'Product is not deliverable on "' . $zipcode . '"',
                            ]);
                        }
                    }
                } else {
                    return response()->json([
                        'error' => true,
                        'message' => 'Cannot deliver to "' . $zipcode . '"',
                    ]);
                }
            }
        }
    }
    public function get_compare_data(Request $request)
    {
        $product_ids = $request->compare_data;

        if (empty($product_ids) || !isset($product_ids[0])) {
            return response()->json([
                'error' => true,
                'message' => 'Product IDs are required in compare_data',
                'data' => [],
            ]);
        } else {
            $product_details = [];

            foreach ($product_ids as $product_id) {
                $products = fetchproduct("", "", $product_id);
                $product_details[] = $products['product'];
            }
            return  $product_details;
        }
    }
}
