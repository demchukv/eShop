<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\ComboProduct;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class FeaturedSectionsController extends Controller
{

    public function index()
    {
        $store_id = getStoreId();
        $categories = Category::where('status', 1)->where('store_id', $store_id)->orderBy('id', 'desc')->get();
        return view('admin.pages.tables.featured_section', ['categories' => $categories]);
    }
    public function store(Request $request)
    {

        $store_id = getStoreId();

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'short_description' => 'required',
            'style' => 'required',
            'product_type' => 'required',
            'banner_image' => 'required',
            'background_color' => ['required', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'header_style' => 'required',
            'categories'=>'required|array|min:1'
        ],
        [
            'background_color.regex' => 'The background color must be a valid hexadecimal code (e.g., #FFF or #FFFFFF).',
        ]);
        // If the product type is not custom products, custom combo products, or digital products, make categories required
    if (!in_array($request->product_type, ['custom_products', 'custom_combo_products', 'digital_product'])) {
        $validatorRules['categories'] = 'required|array|min:1';
    }
        if ($request->product_type == 'custom_products') {

            $validator = Validator::make($request->all(), [
                'product_ids' => 'required|array|min:1',
                'banner_image' => 'required',
                'background_color' => ['required', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
                'header_style' => 'required',
            ], [
                'product_ids.required' => labels('admin_labels.select_at_least_one_product', 'Please select at least one product.'),
                'background_color.regex' => 'The background color must be a valid hexadecimal code (e.g., #FFF or #FFFFFF).',
            ]);
        }
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        } else {
            if (isExist(['title' => $request->title], 'sections')) {
                $response["error"] = true;
                $response["message"] = "Title Already Exists !";
                $response["data"] = array();
                return response()->json($response);
            }

            if (isset($request->product_ids) && !empty($request->product_ids) && $request->product_type == 'custom_products') {
                $product_ids = implode(',', $request->product_ids);
            } elseif (isset($request->digital_product_ids) && !empty($request->digital_product_ids) && $request->product_type == 'digital_product') {
                $product_ids = implode(',', $request->digital_product_ids);
            } elseif (isset($request->product_ids) && !empty($request->product_ids) && $request->product_type == 'custom_combo_products') {
                $product_ids = implode(',', $request->product_ids);
            } else {
                $product_ids = null;
            }

            $validatedData['title'] = $request->title;
            $validatedData['banner_image'] = $request->banner_image;
            $validatedData['background_color'] = $request->background_color;
            $validatedData['short_description'] = $request->short_description;
            $validatedData['product_type'] = $request->product_type;
            $validatedData['categories'] = (isset($request->categories) && !empty($request->categories)) ? implode(',', $request->categories) : null;
            $validatedData['product_ids'] = $product_ids;
            $validatedData['style'] = $request->style;
            $validatedData['header_style'] = $request->header_style;
            $validatedData['store_id'] = $store_id;

            Section::create($validatedData);

            if ($request->ajax()) {
                return response()->json([
                    'message' => labels('admin_labels.feature_section_created_successfully', 'Feature Section created successfully')
                ]);
            }
        }
    }

    public function list()
    {
        $store_id = getStoreId();

        $search = trim(request('search'));
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : 0;
        $limit = (request('limit')) ? request('limit') : "10";
        $section = Section::where('store_id', $store_id)
        ->when($search, function ($query) use ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('short_description', 'LIKE', '%' . $search . '%');
            });
        });
        $product_type_labels = [
            'new_added_products' => 'New Added Products',
            'products_on_sale' => 'Products on Sale',
            'top_rated_products' => 'Top Rated Products',
            'most_selling_products' => 'Most Selling Products',
            'custom_products' => 'Custom Products',
            'digital_product' => 'Digital Product',
            'custom_combo_products' => 'Custom Combo Products'
        ];
        $styles = [
            'style_1' => 'Style 1',
            'style_2' => 'Style 2',
            'style_3' => 'Style 3',

        ];
        $section->where('store_id', $store_id);
        $total = $section->count();
        $section = $section->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function ($s) use ($product_type_labels, $styles) {
                $edit_url = route('feature_section.edit', $s->id);
                $delete_url = route('feature_section.destroy', $s->id);
                $action = '<div class="dropdown bootstrap-table-dropdown">
                    <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-horizontal-rounded"></i>
                    </a>
                    <div class="dropdown-menu table_dropdown offer_action_dropdown" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item dropdown_menu_items" href="' . $edit_url . '"><i class="bx bx-pencil mx-2"></i> Edit</a>
                        <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . $delete_url . '"><i class="bx bx-trash mx-2"></i> Delete</a>
                    </div>
                </div>';
                $banner_image = route('admin.dynamic_image', [
                    'url' => getMediaImageUrl($s->banner_image),
                    'width' => 60,
                    'quality' => 90
                ]);
                return [
                    'id' => $s->id,
                    'title' => $s->title,
                    'short_description' => $s->short_description,
                    'style' => isset($styles[$s->style]) ? $styles[$s->style] : $s->style,
                    'banner_image' => '<div class="d-flex justify-content-around"><a href="' . getMediaImageUrl($s->banner_image)  . '" data-lightbox="banner-' . $s->id . '"><img src="' . $banner_image . '" alt="Avatar" class="rounded"/></a></div>',
                    'categories' => $s->categories,
                    'product_ids' => $s->product_ids,
                    'product_type' => isset($product_type_labels[$s->product_type]) ? $product_type_labels[$s->product_type] : $s->product_type,
                    'date' => Carbon::parse($s->created_at)->format('d-m-Y'),
                    'operate' => $action,
                ];
            });

        return response()->json([
            "rows" => $section,
            "total" => $total,
        ]);
    }
    public function edit($data)
    {
        $store_id = getStoreId();

        $data = Section::where('store_id', $store_id)
            ->find($data);

        if ($data === null || empty($data)) {
            return view('admin.pages.views.no_data_found');
        } else {
            $categories = Category::where('status', 1)->where('store_id', $store_id)->orderBy('id', 'desc')->get();
            $product_details = Product::whereIn('id', explode(',', $data->product_ids))->where('store_id', $store_id)
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();

            $combo_product_details = ComboProduct::whereIn('id', explode(',', $data->product_ids))->where('store_id', $store_id)
            ->orderBy('id', 'desc')
                ->get()
                ->toArray();

            return view('admin.pages.forms.update_featured_section', compact('data', 'categories', 'product_details', 'combo_product_details'));
        }
    }

    public function update(Request $request, $data)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'short_description' => 'required',
            'style' => 'required',
            'product_type' => 'required',
            'banner_image' => 'required',
            'background_color' => ['required', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'header_style' => 'required',
        ],
        [
            'background_color.regex' => 'The background color must be a valid hexadecimal code (e.g., #FFF or #FFFFFF).',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        } else {
            if (isExist(['title' => $request->title], 'sections', $data)) {
                $response["error"] = true;
                $response["message"] = "Title Already Exists !";
                $response["data"] = array();
                return response()->json($response);
            }
            if (isset($request->product_ids) && !empty($request->product_ids) && $request->product_type == 'custom_products') {
                $product_ids = implode(',', $request->product_ids);
            } elseif (isset($request->digital_product_ids) && !empty($request->digital_product_ids) && $request->product_type == 'digital_product') {
                $product_ids = implode(',', $request->digital_product_ids);
            } elseif (isset($request->combo_product_ids) && !empty($request->combo_product_ids) && $request->product_type == 'custom_combo_products') {
                $product_ids = implode(',', $request->combo_product_ids);
            } else {
                $product_ids = null;
            }


            $validatedData['title'] = $request->title;
            $validatedData['short_description'] = $request->short_description;
            $validatedData['product_type'] = $request->product_type;
            $validatedData['categories'] = (isset($request->categories) && !empty($request->categories)) ? implode(',', $request->categories) : null;
            $validatedData['product_ids'] = $product_ids;
            $validatedData['style'] = $request->style;
            $validatedData['banner_image'] = $request->banner_image;
            $validatedData['background_color'] = $request->background_color;
            $validatedData['header_style'] = $request->header_style;

            Section::where('id', $data)->update($validatedData);

            if ($request->ajax()) {
                return response()->json([
                    'message' => labels('admin_labels.feature_section_updated_successfully', 'Feature Section updated successfully'),
                    'location' => route('feature_section.index')
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $section = Section::find($id);

        if ($section->delete()) {
            return response()->json([
                'error' => false,
                'message' => labels('admin_labels.featured_section_deleted_successfully', 'Featured section deleted successfully!')
            ]);
        } else {
            return response()->json(['error' => labels('admin_labels.something_went_wrong', 'Something went wrong')]);
        }
    }

    public function sectionOrder()
    {
        $store_id = getStoreId();
        $sections = Section::where('store_id', $store_id)->orderBy('row_order', 'asc')->get();

        return

            view('admin.pages.tables.section_order', ['sections' => $sections]);
    }

    public function updateSectionOrder(Request $request)
    {

        $section_ids = $request->input('section_id');
        $i = 0;

        foreach ($section_ids as $section_id) {
            $data = [
                'row_order' => $i
            ];

            Section::where('id', $section_id)->update($data);

            $i++;
        }
        return response()->json(['error' => false, 'message' => labels('admin_labels.section_order_saved', 'Section Order Saved!')]);
    }
    public function delete_selected_data(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:sections,id'
        ]);

        foreach ($request->ids as $id) {
            $sections = Section::find($id);

            if ($sections) {
                Section::where('id', $id)->delete();
            }
        }
        Section::destroy($request->ids);

        return response()->json(['message' => 'Selected data deleted successfully.']);
    }
}
