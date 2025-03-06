<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\OfferSliders;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;

class OfferController extends Controller
{
    public function index()
    {
        $store_id = getStoreId();
        $categories = Category::where('status', 1)->where('store_id', $store_id)->orderBy('id', 'desc')->get();
        return view('admin.pages.forms.offers', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $store_id = getStoreId();


        $rules = [
            'title' => 'required',
            'banner_image' => 'required',
            'image' => 'required',
        ];

        // Additional validation based on the 'type' of the request
        switch ($request->type) {
            case 'categories':
                $rules['category_id'] = 'required|exists:categories,id';
                break;

            case 'brand':
                $rules['brand_id'] = 'required|exists:brands,id';
                break;

            case 'products':
                $rules['product_id'] = 'required|exists:products,id';
                break;

            case 'combo_products':
                $rules['combo_product_id'] = 'required';
                break;

            case 'offer_url':
                $rules['link'] = 'required';
                break;

            default:
                break;
        }

        // Min and max discount validation
        $min_discount = $request->input('min_discount');
        $max_discount = $request->input('max_discount');

        if ($max_discount < $min_discount) {
            return response()->json([
                'error' => false,
                'error_message' => labels('admin_labels.max_discount_greater_than_min_discount', 'Max discount should be greater than min discount.'),
                'csrfHash' => csrf_token(),
                'data' => []
            ]);
        }

        // Add discount validation rules if required
        if (!empty($min_discount) || !empty($max_discount)) {
            $rules['min_discount'] = 'required';
            $rules['max_discount'] = 'required';
        }

        // Validate the request
        $validator = Validator::make($request->all(), $rules, [
            'category_id.required' => labels('admin_labels.select_at_least_one_category', 'Please Select At least One category.'),
            'brand_id.required' => labels('admin_labels.select_at_least_one_brand', 'Please Select At least One brand.'),
            'product_id.required' => labels('admin_labels.select_at_least_one_product', 'Please Select At least One Product.'),
            'combo_product_id.required' => labels('admin_labels.select_at_least_one_product', 'Please Select At least One Product.'),
        ]);

        // Check for validation failures
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }
        $type_id = 0;
        $link = '';
        if (isset($request->type) && $request->type == 'categories' && isset($request->category_id) && !empty($request->category_id)) {
            $type_id = $request->category_id;
        }

        if (isset($request->type) && $request->type == 'brand' && isset($request->brand_id) && !empty($request->brand_id)) {
            $type_id = $request->brand_id;
        }
        if (isset($request->type) && $request->type == 'products' && isset($request->product_id) && !empty($request->product_id)) {
            $type_id = $request->product_id;
        }
        if (isset($request->type) && $request->type == 'combo_products' && isset($request->combo_product_id) && !empty($request->combo_product_id)) {
            $type_id = $request->combo_product_id;
        }
        if (isset($request->type) && $request->type == 'offer_url' && !empty($request->link)) {
            $link = $request->link;
            $type_id = 0;
        }
        $validatedData['type'] = $request->type;
        $validatedData['title'] = ($request->title !== null) && !empty($request->title) ? $request->title : '';
        $validatedData['title'] = ($request->title !== null) && !empty($request->title) ? $request->title : '';
        $validatedData['link'] = $link;
        $validatedData['image'] = $request->input('image');
        $validatedData['banner_image'] = $request->input('banner_image');
        $validatedData['min_discount'] = ($request->input('min_discount') !== null) && !empty($request->input('min_discount')) ? $request->input('min_discount') : '';
        $validatedData['max_discount'] = ($request->input('max_discount') !== null) && !empty($request->input('max_discount')) ? $request->input('max_discount') : '';
        $validatedData['type_id'] = $type_id;
        $validatedData['store_id'] = $store_id;

        Offer::create($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.offer_created_successfully', 'Offer created successfully')
            ]);
        }
    }

    public function list()
    {
        $store_id = getStoreId();

        $search = trim(request('search'));
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : 0;
        $limit = request('limit', 10);

        // Mapping array for displaying type values
        $offer_type = [
            'default' => 'Default',
            'categories' => 'Category',
            'all_products' => 'All Products',
            'all_combo_products' => 'All Combo Products',
            'products' => 'Specific Product',
            'combo_products' => 'Specific Combo Product',
            'brand' => 'Brand',
            'offer_url' => 'Offer URL'
        ];

        $offers = Offer::where('store_id', $store_id);

        if ($search) {
            $offers->where('title', 'like', '%' . $search . '%');
        }

        $total = $offers->count();

        $offers = $offers->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function ($o) use ($offer_type) {
                $edit_url = route('offers.edit', $o->id);
                $delete_url = route('offers.destroy', $o->id);
                $action = '<div class="dropdown bootstrap-table-dropdown">
            <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="bx bx-dots-horizontal-rounded"></i>
            </a>
            <div class="dropdown-menu table_dropdown offer_action_dropdown" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item dropdown_menu_items" href="' . $edit_url . '"><i class="bx bx-pencil mx-2"></i> Edit</a>
                <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . $delete_url . '"><i class="bx bx-trash mx-2"></i> Delete</a>
            </div>
        </div>';

                // Use the mapping array to display type
                $type = isset($offer_type[$o->type]) ? $offer_type[$o->type] : $o->type;
                $image = route('admin.dynamic_image', [
                    'url' => getMediaImageUrl($o->image),
                    'width' => 60,
                    'quality' => 90
                ]);
                $banner = route('admin.dynamic_image', [
                    'url' => getMediaImageUrl($o->banner_image),
                    'width' => 60,
                    'quality' => 90
                ]);
                return [
                    'id' => $o->id,
                    'type' => $type,
                    'title' => $o->title,
                    'operate' => $action,
                    'link' => $o->link,
                    'min_discount' => $o->min_discount,
                    'max_discount' => $o->max_discount,
                    'image' => '<div><a href="' . getMediaImageUrl($o->image)   . '" data-lightbox="image-' . $o->id . '"><img src="' . $image . '" alt="Avatar" class="rounded"/></a></div>',
                    'banner_image' => '<div class=""><a href="' . getMediaImageUrl($o->banner_image)  . '" data-lightbox="image-' . $o->id . '"><img src="' . $banner . '" alt="Avatar" class="rounded"/></a></div>',
                ];
            });

        return response()->json([
            "rows" => $offers,
            "total" => $total,
        ]);
    }


    public function update_status($id)
    {
        $offer = Offer::findOrFail($id);
        if (isForeignKeyInUse('offer_sliders', 'offer_ids', $id)) {
            return response()->json([
                'status_error' => labels('admin_labels.cannot_deactivate_offer_associated_with_slider', 'You cannot deactivate this offer because it is associated with offer slider.')
            ]);
        } else {
            $offer->status = $offer->status == '1' ? '0' : '1';
            $offer->save();
            return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
        }
    }

    public function destroy($id)
    {
        $offer = Offer::find($id);
        if (isForeignKeyInUse('offer_sliders', 'offer_ids', $id, 1)) {
            return response()->json([
                'error' => labels('admin_labels.cannot_delete_offer_associated_with_slider', 'You cannot delete this offer because it is associated with offer slider.')
            ]);
        }
        if ($offer->delete()) {
            return response()->json([
                'error' => false,
                'message' => labels('admin_labels.offer_deleted_successfully', 'Offer deleted successfully!')
            ]);
        }
        return response()->json(['error' => labels('admin_labels.something_went_wrong', 'Something went wrong')]);
    }

    public function edit($data)
    {
        $store_id = getStoreId();

        $data = Offer::where('store_id', $store_id)
            ->find($data);

        if ($data === null || empty($data)) {
            return view('admin.pages.views.no_data_found');
        } else {
            $categories = Category::where('status', 1)->where('store_id', $store_id)->orderBy('id', 'desc')->get();
            $brands = Brand::where('status', 1)->where('store_id', $store_id)->orderBy('id', 'desc')->get();
            return view('admin.pages.forms.update_offer', compact('data', 'categories', 'brands'));
        }
    }

    public function update(Request $request, $data)
    {

        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'image' => 'required',
        ]);

        $min_discount = ($request->input('min_discount') !== null) && !empty($request->input('min_discount')) ? $request->input('min_discount') : '';
        $max_discount = ($request->input('max_discount') !== null) && !empty($request->input('max_discount')) ? $request->input('max_discount') : '';

        if ($max_discount < $min_discount) {
            $response = [
                'error' => false,
                'error_message' =>
                labels('admin_labels.max_discount_greater_than_min_discount', 'Max discount should be greater than to min discount.'),
                'csrfHash' => csrf_token(),
                'data' => []
            ];
            return response()->json($response);
        }

        if ($request->type === 'categories') {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,id',
                'min_discount' => 'required',
                'max_discount' => 'required',
            ], [
                'category_id.required' =>
                labels('admin_labels.select_at_least_one_category', 'Please Select Atleast One category.'),
            ]);
        }

        if ($request->type === 'offer_url') {
            $validator = Validator::make($request->all(), [
                'link' => 'required',
            ]);
        }

        if ($request->type === 'all_products') {
            $validator = Validator::make($request->all(), [
                'min_discount' => 'required',
                'max_discount' => 'required',
            ]);
        }
        if ($request->type === 'all_combo_products') {
            $validator = Validator::make($request->all(), [
                'min_discount' => 'required',
                'max_discount' => 'required',
            ]);
        }

        if ($request->type === 'brand') {
            $validator = Validator::make($request->all(), [
                'brand_id' => 'required|exists:brands,id',
                'min_discount' => 'required',
                'max_discount' => 'required',
            ], [
                'brand_id.required' => labels('admin_labels.select_at_least_one_brand', 'Please Select at Least One Brand.'),
            ]);
        }

        if ($request->type === 'products') {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
            ], [
                'product_id.required' => labels('admin_labels.select_at_least_one_product', 'Please Select at Least One Product.'),
            ]);
        }
        if ($request->type === 'combo_products') {
            $validator = Validator::make($request->all(), [
                'combo_product_id' => 'required|exists:combo_products,id',
            ], [
                'combo_product_id.required' => labels('admin_labels.select_at_least_one_product', 'Please Select at Least One Product.'),
            ]);
        }
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $type_id = 0;
        $link = '';
        if (isset($request->type) && $request->type == 'categories' && isset($request->category_id) && !empty($request->category_id)) {
            $type_id = $request->category_id;
        }

        if (isset($request->type) && $request->type == 'brand' && isset($request->brand_id) && !empty($request->brand_id)) {
            $type_id = $request->brand_id;
        }
        if (isset($request->type) && $request->type == 'products' && isset($request->product_id) && !empty($request->product_id)) {
            $type_id = $request->product_id;
        }
        if (isset($request->type) && $request->type == 'combo_products' && isset($request->combo_product_id) && !empty($request->combo_product_id)) {
            $type_id = $request->combo_product_id;
        }
        if (isset($request->type) && $request->type == 'offer_url' && !empty($request->link)) {
            $link = $request->link;
            $type_id = 0;
        }

        $validatedData['type'] = $request->type;
        $validatedData['title'] = ($request->title !== null) && !empty($request->title) ? $request->title : '';
        $validatedData['link'] = $link;
        $validatedData['image'] = $request->input('image');
        $validatedData['banner_image'] = $request->input('banner_image');
        $validatedData['min_discount'] = ($request->input('min_discount') !== null) && !empty($request->input('min_discount')) ? $request->input('min_discount') : '';
        $validatedData['max_discount'] = ($request->input('max_discount') !== null) && !empty($request->input('max_discount')) ? $request->input('max_discount') : '';
        $validatedData['type_id'] = $type_id;

        Offer::where('id', $data)->update($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.offer_updated_sucesfully', 'Offer updated successfully'),
                'location' => route('offers.index')
            ]);
        }
    }

    public function offer_slider()
    {

        $store_id = getStoreId();
        $offers = Offer::where('store_id', $store_id)->get();

        return view('admin.pages.forms.offer_sliders', ['offers' => $offers]);
    }


    public function offer_data(Request $request)
    {
        $store_id = getStoreId();
        $search = trim($request->input('search'));
        $limit = (int) $request->input('limit', 50);

        // Base query for offers
        $baseQuery = Offer::where('store_id', $store_id)
            ->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%');
            });

        // Fetch offers and count total
        $offers = $baseQuery->limit($limit)->get(['id', 'type', 'image', 'min_discount', 'max_discount']);
        $totalCount = $baseQuery->count();

        // Map the offers to desired response structure
        $response = [
            'total' => $totalCount,
            'results' => $offers->map(function ($offer) {
                // Set text based on offer type
                switch ($offer->type) {
                    case 'all_products':
                        $text = 'All Products';
                        break;

                    case 'all_combo_products':
                        $text = 'All Combo Products';
                        break;
                    case 'combo_products':
                        $text = 'Combo Products';
                        break;
                    case 'offer_url':
                        $text = 'Offer URL';
                        break;

                    default:
                        $text = $offer->type;
                }

                return [
                    'id' => $offer->id,
                    'text' => $text,
                    'image' => getMediaImageUrl($offer->image),
                    'min_discount' => $offer->min_discount,
                    'max_discount' => $offer->max_discount,
                ];
            }),
        ];

        return response()->json($response);
    }


    public function store_offer_slider(Request $request)
    {
        $store_id = getStoreId();
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'banner_image' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $validatedData = $validator->validated();

        $validatedData['title'] = isset($request->title) ? $request->title : "";
        $validatedData['banner_image'] = isset($request->banner_image) ? $request->banner_image : "";
        $validatedData['offer_ids'] = isset($request->offer_ids) ? implode(',', $request->offer_ids) : '';
        $validatedData['status'] = 1;
        $validatedData['store_id'] = isset($store_id) ? $store_id : '';

        OfferSliders::create($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.offer_slider_created_successfully', 'Offer slider created successfully')
            ]);
        }
    }

    public function offer_sliders_list(Request $request)
    {
        $store_id = getStoreId();
        $search = trim(request('search'));
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : 0;
        $limit = (request('limit')) ? request('limit') : "10";
        $status = $request->input('status') ?? '';
        $offer_slider_data = OfferSliders::when($search, function ($query) use ($search) {
            return $query->where('title', 'like', '%' . $search . '%');
        });

        if ($status !== '') {
            $offer_slider_data->where('status', $status);
        }
        $offer_slider_data->where('store_id', $store_id);
        $total = $offer_slider_data->count();

        $sliders = $offer_slider_data->orderBy($sort, $order)->offset($offset)
            ->limit($limit)
            ->get();

        $data = $sliders->map(function ($s) {
            $delete_url = route('admin.offer_slider.destroy', $s->id);
            $edit_url = route('admin.offer_sliders.update', $s->id);
            $offerIds = explode(',', $s->offer_ids);
            $related_offers = Offer::whereIn('id', $offerIds)->get();
            $offer_details = $related_offers->map(function ($offer) {
                $offer_type_map = [
                    'default' => 'Default',
                    'categories' => 'Category',
                    'all_products' => 'All Products',
                    'all_combo_products' => 'All Combo Products',
                    'products' => 'Specific Product',
                    'combo_products' => 'Combo Products',
                    'brand' => 'Brand',
                    'offer_url' => 'Offer URL'
                ];
                return $offer_type_map[$offer->type] . ' (id-' . $offer->id . ')';
            })->implode(' , ');
            $image = route('admin.dynamic_image', [
                'url' => getMediaImageUrl($s->image),
                'width' => 60,
                'quality' => 90
            ]);
            $banner = route('admin.dynamic_image', [
                'url' => getMediaImageUrl($s->banner_image),
                'width' => 60,
                'quality' => 90
            ]);
            $action = '<div class="dropdown bootstrap-table-dropdown">
                    <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                    </a>
                    <div class="dropdown-menu table_dropdown offer_action_dropdown" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item dropdown_menu_items" href="' . $edit_url . '"><i class="bx bx-pencil mx-2"></i> Edit</a>
                    <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . $delete_url . '"><i class="bx bx-trash mx-2"></i> Delete</a>
                </div>
                </div>';
            return [
                'id' => $s->id,
                'title' => $s->title,
                'offer_ids' => $offer_details,
                'status' => '<select class="form-select status_dropdown change_toggle_status ' . ($s->status == 1 ? 'active_status' : 'inactive_status') . '" data-id="' . $s->id . '" data-url="/admin/offer_sliders/update_status/' . $s->id . '" aria-label="">
              <option value="1" ' . ($s->status == 1 ? 'selected' : '') . '>Active</option>
              <option value="0" ' . ($s->status == 0 ? 'selected' : '') . '>Deactive</option>
          </select>',
                'image' => '<div><a href="' . getMediaImageUrl($s->image)  . '" data-lightbox="image-' . $s->id . '"><img src="' . $image . '" alt="Avatar" class="rounded"/></a></div>',
                'banner' => '<div><a href="' . getMediaImageUrl($s->banner_image)  . '" data-lightbox="banner-' . $s->id . '"><img src="' . $banner . '" alt="Avatar" class="rounded"/></a></div>',
                'operate' => $action,
            ];
        });

        return response()->json([
            "rows" => $data,
            "total" => $total,
        ]);
    }


    public function update_offer_slider_status($id)
    {
        $offer_slider = OfferSliders::findOrFail($id);
        $offer_slider->status = $offer_slider->status == '1' ? '0' : '1';
        $offer_slider->save();
        return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
    }

    public function offer_slider_edit($data)
    {
        $store_id = getStoreId();

        $offer_sliders = OfferSliders::where('status', 1)->where('store_id', $store_id)->get();

        $data = OfferSliders::where('store_id', $store_id)
            ->find($data);
        if ($data === null || empty($data)) {
            return view('admin.pages.views.no_data_found');
        } else {
            $offers = Offer::where('store_id', $store_id)->get();

            return view('admin.pages.forms.update_offer_slider', [
                'data' => $data,
                'offer_sliders' => $offer_sliders,
                'offers' => $offers
            ]);
        }
    }

    public function offer_slider_update(Request $request, $data)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }
        $slider = OfferSliders::find($data);

        $validatedData = $validator->validated();

        $validatedData['title'] = $request->title;
        $validatedData['offer_ids'] = isset($request->offer_ids) ? implode(',', $request->offer_ids) : '';
        $validatedData['banner_image'] = isset($request->banner_image) ? $request->banner_image : "";


        $slider->update($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.slider_updated_successfully', 'Slider updated successfully'),
                'location' => route('offer_sliders.index')
            ]);
        }
    }

    public function offer_slider_destroy($id)
    {
        $OfferSliders = OfferSliders::find($id);

        if ($OfferSliders->delete()) {
            return response()->json([
                'error' => false,
                'message' => labels('admin_labels.offer_deleted_successfully', 'Offer Slider deleted successfully!')
            ]);
        }
        return response()->json(['error' => labels('admin_labels.something_went_wrong', 'Something went wrong')]);
    }
    public function delete_selected_data(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:offers,id'
        ]);

        $nonDeletableIds = [];

        foreach ($request->ids as $id) {

            if (isForeignKeyInUse('offer_sliders', 'offer_ids', $id, 1)) {
                $nonDeletableIds[] = $id;
            }
        }
        if (!empty($nonDeletableIds)) {
            return response()->json([
                'error' => labels(
                    'admin_labels.cannot_delete_offer_associated_with_slider',
                    'You cannot delete these offers: ' . implode(', ', $nonDeletableIds) . ' because they are associated with offer sliders'
                ),
                'non_deletable_ids' => $nonDeletableIds
            ], 401);
        }
        Offer::destroy($request->ids);

        return response()->json(['message' => 'Selected offers deleted successfully.']);
    }
    public function delete_selected_slider_data(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:offer_sliders,id'
        ]);

        foreach ($request->ids as $id) {
            $slider = OfferSliders::find($id);

            if ($slider) {
                OfferSliders::where('id', $id)->delete();
            }
        }
        OfferSliders::destroy($request->ids);

        return response()->json(['message' => 'Selected data deleted successfully.']);
    }
}
