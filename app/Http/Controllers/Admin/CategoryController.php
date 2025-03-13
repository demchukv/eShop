<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Category;
use App\Models\CategorySliders;
use App\Models\Location;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    public function index()
    {
        $store_id = getStoreId();
        $categories = Category::where('status', 1)->where('store_id', $store_id)->orderBy('id', 'desc')->get();

        return view('admin.pages.forms.categories', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $store_id = getStoreId();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_image' => 'required',
            'banner' => 'required',
        ]);

        // Check if the category name already exists in the same store
        $validator->after(function ($validator) use ($request, $store_id) {
            $existingCategory = Category::where('store_id', $store_id)
                ->where('name', $request->name)
                ->first();

            if ($existingCategory) {
                $validator->errors()->add('name', 'The category name already exists in this store.');
            }
        });

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()->all()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        $validatedData['image'] = $validatedData['category_image'];
        unset($validatedData['category_image']);

        $validatedData['banner'] = $request->banner;
        $validatedData['parent_id'] = ($request->input('parent_id') != null) ? $request->input('parent_id') : 0;
        $validatedData['slug'] = generateSlug($validatedData['name']);
        $validatedData['status'] = 1;
        $validatedData['store_id'] = $store_id;
        $validatedData['style'] = $request->filled('category_style') ? $request->category_style : '';

        Category::create($validatedData);

        return $request->ajax()
            ? response()->json(['message' => labels('admin_labels.category_created_successfully', 'Category created successfully')])
            : redirect()->back()->with('success', labels('admin_labels.category_created_successfully', 'Category created successfully'));
    }




    public function edit($id)
    {
        $store_id = getStoreId();

        $categories = Category::where('status', 1)
            ->where('store_id', $store_id)
            ->where('id', '!=', $id)
            ->get();

        $data = Category::where('store_id', $store_id)
            ->find($id);

        if ($data === null || empty($data)) {
            return view('admin.pages.views.no_data_found');
        } else {
            return view('admin.pages.forms.update_category', [
                'data' => $data,
                'categories' => $categories
            ]);
        }
    }



    public function update(Request $request, $data)
    {
        // Validation for the required fields
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_image' => 'required',
        ]);

        // If validation fails, return the error response
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()->all()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find the category by the given ID
        $category = Category::find($data);

        // Get the validated data
        $validatedData = $validator->validated();

        // New name and current category details
        $new_name = $request->name;
        $current_name = $category->name;
        $current_slug = $category->slug;

        // Check if the new name already exists (but not for the current category being updated)
        $existingCategory = Category::where('name', $new_name)
            ->where('store_id', getStoreId())  // Ensure the store ID matches
            ->where('id', '!=', $category->id) // Exclude the current category from the check
            ->first();

        // If a category with the same name already exists, return an error response
        if ($existingCategory) {
            $response = [
                'error' => true,
                'message' => 'Category name already exists.',
                'language_message_key' => 'category_name_exists',
            ];
            return response()->json($response, 400); // You can also return a 400 Bad Request status
        }

        // Prepare the category data to be updated
        $cateData = [
            'name' => $new_name,
            'image' => $request->category_image,
            'banner' => $request->banner,
            'parent_id' => isset($request->parent_id) ? $request->parent_id : 0,
            'slug' => generateSlug($new_name, 'categories', 'slug', $current_slug, $current_name),
            'style' => $request->filled('category_style') ? $request->category_style : '',
            'status' => 1,
        ];

        // Update the category with the new data
        $category->update($cateData);

        // Return success response
        return $request->ajax()
            ? response()->json(['message' => labels('admin_labels.category_updated_successfully', 'Category updated successfully'), 'location' => route('categories.index')])
            : redirect()->back()->with('success', labels('admin_labels.category_updated_successfully', 'Category updated successfully'));
    }




    public function update_status($id)
    {
        $category = Category::findOrFail($id);
        $tables = ['products', 'seller_store'];
        $columns = ['category_id', 'category_ids'];
        if (isForeignKeyInUse($tables, $columns, $id)) {
            return response()->json([
                'status_error' => labels('admin_labels.cannot_deactivate_category_associated_with_products_seller', 'You cannot deactivate this category because it is associated with products and seller.')
            ]);
        } else {
            $category->status = $category->status == '1' ? '0' : '1';
            $category->save();
            return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
        }
    }


    public function destroy($id)
    {
        // Find the category by ID
        $category = Category::find($id);

        // Define the tables and columns to check for foreign key constraints
        $tables = ['products', 'seller_store'];
        $columns = ['category_id', 'category_ids'];

        // Check if there are foreign key constraints
        if (isForeignKeyInUse($tables, $columns, $id)) {
            return response()->json([
                'error' => labels('admin_labels.cannot_delete_category_associated_with_products_seller', 'You cannot delete this category because it is associated with products and seller.')
            ]);
        }

        // Check if the category ID exists in the comma-separated category_ids of the category_sliders table
        $isCategoryInSliders = DB::table('category_sliders')
            ->where('category_ids', 'LIKE', '%' . $id . '%')
            ->exists();

        if ($isCategoryInSliders) {
            return response()->json([
                'error' => labels('admin_labels.cannot_delete_category_associated_with_sliders', 'You cannot delete this category because it is associated with category sliders.')
            ]);
        }

        // Attempt to delete the category
        if ($category && $category->delete()) {
            return response()->json([
                'error' => false,
                'message' => labels('admin_labels.category_deleted_successfully', 'Category deleted successfully!')
            ]);
        }

        return response()->json(['error' => labels('admin_labels.something_went_wrong', 'Something went wrong')]);
    }

    public function list(Request $request)
    {
        // Get the store ID
        $store_id = getStoreId();

        // Capture input parameters with defaults
        $search = trim($request->input('search', ''));
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        $offset = request()->input('search') || (request('pagination_offset')) ? (request('pagination_offset')) : 0;
        $limit = $request->input('limit', 10);
        $status = $request->input('status', '');
        // Build the query for categories
        $category_data = Category::where('store_id', $store_id);

        // Apply search filter if provided
        if (!empty($search)) {
            $category_data->where('name', 'like', '%' . $search . '%');
        }

        // Apply status filter only if status is provided
        if (!is_null($status) && $status !== '') {
            $category_data->where('status', $status);
        }

        // Count total records before applying pagination
        $total = $category_data->count();

        // Retrieve paginated data with sorting
        $categories = $category_data->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get();

        // Format data for response
        $data = $categories->map(function ($c) {

            // Format 'status' field with HTML select
            $status = '<select class="form-select status_dropdown change_toggle_status '
                . ($c->status == 1 ? 'active_status' : 'inactive_status')
                . '" data-id="' . $c->id
                . '" data-url="admin/categories/update_status/' . $c->id . '" aria-label="">'
                . '<option value="1" ' . ($c->status == 1 ? 'selected' : '') . '>Active</option>'
                . '<option value="0" ' . ($c->status == 0 ? 'selected' : '') . '>Deactive</option>'
                . '</select>';

            // Format 'image' and 'banner' fields with HTML tags
            $image = route('admin.dynamic_image', [
                'url' => getMediaImageUrl($c->image),
                'width' => 60,
                'quality' => 90
            ]);
            $banner = route('admin.dynamic_image', [
                'url' => getMediaImageUrl($c->banner),
                'width' => 60,
                'quality' => 90
            ]);

            $image = '<div class="d-flex justify-content-around"><a href="' . getMediaImageUrl($c->image) . '" data-lightbox="image-' . $c->id . '"><img src="' . $image . '" alt="Avatar" class="rounded"/></a></div>';
            $banner = '<div><a href="' . getMediaImageUrl($c->banner) . '" data-lightbox="banner-' . $c->id . '"><img src="' . $banner . '" alt="Avatar" class="rounded"/></a></div>';

            // Format 'operate' field with dropdown menu HTML
            $action = '<div class="dropdown bootstrap-table-dropdown">
                <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                </a>
                <div class="dropdown-menu table_dropdown category_action_dropdown" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item dropdown_menu_items" href="' . route('categories.update', $c->id) . '"><i class="bx bx-pencil mx-2"></i> Edit</a>
                    <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . route('admin.categories.destroy', $c->id) . '"><i class="bx bx-trash mx-2"></i> Delete</a>
                </div>
            </div>';

            return [
                'id' => $c->id,
                'name' => $c->name,
                'status' => $status,
                'image' => $image,
                'banner' => $banner,
                'operate' => $action,
            ];
        });

        // Return response as JSON
        return response()->json([
            "rows" => $data->toArray(), // Convert collection to array for JSON response
            "total" => $total,           // Return the total count
        ]);
    }



    public function get_seller_categories_filter()
    {
        $store_id = getStoreId();

        $seller_data = DB::table('seller_store')
            ->select('category_ids')
            ->where('store_id', $store_id)->get()[0];

        if (!$seller_data) {
            return [];
        }

        $category_ids = explode(",", $seller_data->category_ids);

        $categories = Category::whereIn('id', $category_ids)
            ->where('status', 1)
            ->where('store_id', $store_id)
            ->get()
            ->toArray();

        return $categories;
    }

    public function getCategoryDetails(Request $request)
    {
        $store_id = getStoreId();
        $search = trim($request->input('search'));
        $limit = (int) $request->input('limit', 10);

        $category = Category::where('name', 'like', '%' . $search . '%')
            ->where('store_id', $store_id)
            ->where('status', 1)
            ->limit($limit)
            ->get(['id', 'name']);

        $totalCount = Category::where('name', 'like', '%' . $search . '%')
            ->where('store_id', $store_id)
            ->selectRaw('count(id) as total')
            ->first()
            ->total;

        $response = [
            'total' => $totalCount,
            'results' => $category->map(function ($category) {
                return [
                    'id' => $category->id,
                    'text' => $category->name,
                ];
            }),
        ];

        return response()->json($response);
    }

    public function getCategories($id = null, $limit = '', $offset = '', $sort = 'row_order', $order = 'ASC', $has_child_or_item = 'true', $slug = '', $ignore_status = '', $seller_id = '', $store_id = '')
    {

        $level = 0;
        $query = DB::table('categories as c1');

        $storeId = getStoreId();
        if (isset($storeId) && !empty($storeId)) {
            $query->where('c1.store_id', $storeId);
        }
        if (isset($store_id) && !empty($store_id)) {
            $query->where('c1.store_id', $store_id);
        }
        if ($ignore_status == 1) {
            $query->where('c1.id', $id)->Where('c1.parent_id', 0);
        } else {
            $query->where(function ($q) use ($id) {
                $q->where('c1.id', $id)->where('c1.status', 1)
                    ->Where(function ($q) {
                        $q->where('c1.parent_id', 0)->where('c1.status', 1);
                    });
            });
        }

        if (!empty($slug)) {
            $query->where('c1.slug', $slug);
        }

        if ($has_child_or_item == 'false') {
            $query->leftJoin('categories as c2', 'c2.parent_id', '=', 'c1.id')
                ->leftJoin('products as p', 'p.category_id', '=', 'c1.id')
                ->where(function ($q) {
                    $q->where('c1.id', 'p.category_id')
                        ->orWhere('c2.parent_id', 'c1.id');
                })
                ->groupBy('c1.id');
        }

        if (!empty($limit) || !empty($offset)) {
            $query->offset($offset)->limit($limit);
        }

        $query->orderBy($sort, $order);

        $categories = $query->get(['c1.*']);

        $countRes = count($categories);

        $i = 0;
        foreach ($categories as $pCat) {
            $categories[$i]->children = $this->subCategories($pCat->id, $level);
            $categories[$i]->text = $pCat->name;
            $categories[$i]->name = $categories[$i]->name;
            $categories[$i]->state = ['opened' => true];
            $categories[$i]->icon = "jstree-folder";
            $categories[$i]->level = $level;
            $categories[$i]->image = dynamic_image(getImageUrl($pCat->image, 'thumb', 'sm'), 400);
            $categories[$i]->banner = dynamic_image(getImageUrl($pCat->banner, 'thumb', 'md'), 400);
            $i++;
        }

        if (isset($categories[0])) {
            $categories[0]->total = $countRes;
        }
        return Response::json(compact('categories', 'countRes'));
    }


    public function get_categories($id = null, $limit = '', $offset = '', $sort = 'row_order', $order = 'ASC', $has_child_or_item = 'true', $slug = '', $ignore_status = '', $seller_id = '', $store_id = '', $search = '', $ids = '')
    {
        $level = 0;
        $storeId = getStoreId();

        // Convert the comma-separated ids string to an array
        $idsArray = !empty($ids) ? explode(',', $ids) : [];
        // Initial count query to calculate the total number of categories
        $countQuery = DB::table('categories as c1');

        if (isset($storeId) && !empty($storeId)) {
            $countQuery->where('c1.store_id', $storeId);
        }
        if (isset($store_id) && !empty($store_id)) {
            $countQuery->where('c1.store_id', $store_id);
        }

        // If `ids` is provided, apply whereIn condition
        if (!empty($idsArray)) {
            $countQuery->whereIn('c1.id', $idsArray);
        } else {
            // Continue with other filters when no specific ids are provided
            if (!empty($id)) {
                $parentId = DB::table('categories')
                    ->where('id', $id)
                    ->value('parent_id');

                if ($parentId != 0) {
                    $countQuery->where('c1.id', $parentId);
                } else {
                    $countQuery->where('c1.id', $id);
                }
            } else {
                if ($ignore_status == 1) {
                    $countQuery->orWhere('c1.parent_id', 0);
                } else {
                    $countQuery->where(function ($q) {
                        $q->where('c1.parent_id', 0)->where('c1.status', 1);
                    });
                }
            }
        }

        if (!empty($slug)) {
            $countQuery->where('c1.slug', $slug);
        }

        if (!empty($search)) {
            $countQuery->leftJoin('categories as c2', 'c2.parent_id', '=', 'c1.id');
            $countQuery->where(function ($q) use ($search) {
                $q->where('c1.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('c2.name', 'LIKE', '%' . $search . '%');
            });
        }

        // Get the total count
        $total = $countQuery->count();

        // Main query for fetching category data
        $query = DB::table('categories as c1');

        if (isset($storeId) && !empty($storeId)) {
            $query->where('c1.store_id', $storeId);
        }
        if (isset($store_id) && !empty($store_id)) {
            $query->where('c1.store_id', $store_id);
        }

        // If `ids` is provided, apply whereIn condition
        if (!empty($idsArray)) {
            $query->whereIn('c1.id', $idsArray);
        } else {
            // Continue with other filters when no specific ids are provided
            if (!empty($id)) {
                if ($parentId != 0) {
                    $query->where('c1.id', $parentId);
                } else {
                    $query->where('c1.id', $id);
                }
            } else {
                if ($ignore_status == 1) {
                    $query->orWhere('c1.parent_id', 0);
                } else {
                    $query->where(function ($q) {
                        $q->where('c1.parent_id', 0)->where('c1.status', 1);
                    });
                }
            }
        }

        if (!empty($slug)) {
            $query->where('c1.slug', $slug);
        }

        if (!empty($search)) {
            $query->leftJoin('categories as c2', 'c2.parent_id', '=', 'c1.id');
            $query->where(function ($q) use ($search) {
                $q->where('c1.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('c2.name', 'LIKE', '%' . $search . '%');
            });
        }

        if (!empty($limit) || !empty($offset)) {
            $query->offset($offset)->limit($limit);
        }

        $query->orderBy($sort, $order);

        // Fetch the categories
        $categories = $query->get(['c1.*']);

        $i = 0;
        foreach ($categories as $pCat) {
            $childId = $id ?? null;
            $categories[$i]->children = $this->subCategories($pCat->id, $level, $childId);
            $categories[$i]->text = $pCat->name;
            $categories[$i]->name = $categories[$i]->name;
            $categories[$i]->state = ['opened' => true];
            $categories[$i]->icon = "jstree-folder";
            $categories[$i]->level = $level;
            $categories[$i]->image = dynamic_image(getImageUrl($pCat->image, 'thumb', 'sm'), 400);
            $categories[$i]->banner = dynamic_image(getImageUrl($pCat->banner, 'thumb', 'md'), 400);
            $i++;
        }
        return Response::json(['categories' => $categories, 'total' => $total]);
    }

    public function subCategories($id, $level)
    {
        $level = $level + 1;
        $category = Category::find($id);
        $categories = $category->children;

        $i = 0;
        foreach ($categories as $p_cat) {

            $categories[$i]->children = $this->subCategories($p_cat->id, $level);
            $categories[$i]->text = e($p_cat->name); // Use the Laravel "e" helper for output escaping
            $categories[$i]->state = ['opened' => true];
            $categories[$i]->level = $level;
            $p_cat['image'] = getMediaImageUrl($p_cat['image']);
            $p_cat['banner'] = getMediaImageUrl($p_cat['banner']);
            $i++;
        }

        return $categories;
    }


    public function getSellerCategories(Request $request)
    {
        $level = 0;
        $store_id = getStoreId();
        $sellerId = $request->seller_id ?? '';
        // Fetch category IDs from seller data
        $sellerData = DB::table('seller_store')
            ->select('category_ids')
            ->where('store_id', $store_id)
            ->where('seller_id', $sellerId)
            ->first();

        if (!$sellerData || empty($sellerData->category_ids)) {
            return []; // Return empty if no data is found
        }

        $categoryIds = explode(',', $sellerData->category_ids);

        // Fetch categories with the given IDs and status
        $categories = Category::whereIn('id', $categoryIds)
            ->where('status', 1)
            ->where('store_id', $store_id)
            ->get();

        $parentIds = []; // To store IDs of parent categories
        $filteredCategories = []; // To store the final categories list

        foreach ($categories as $pCat) {
            // Check if the parent category already exists in the list
            if (!in_array($pCat->parent_id, $parentIds)) {
                $category = $pCat->toArray();

                // Append additional data to the category
                $category['children'] = $this->subCategories($pCat->id, $level);
                $category['text'] = $pCat->name;
                $category['name'] = $category['name'];
                $category['state'] = ['opened' => true];
                $category['icon'] = "jstree-folder";
                $category['level'] = $level;
                $category['image'] = getMediaImageUrl($category['image']);
                $category['banner'] = getMediaImageUrl($category['banner']);

                $filteredCategories[] = $category;
                $parentIds[] = $pCat->id; // Add this category ID to parent IDs
            }
        }

        // Add total count to the first filtered category
        if (isset($filteredCategories[0])) {
            $filteredCategories[0]['total'] = count($categories);
        }

        return $filteredCategories;
    }

    public function categoryOrder()
    {
        $store_id = getStoreId();

        // Fetch only main categories (where parent_id is null or 0)
        $categories = Category::where('status', 1)
            ->where('store_id', $store_id)
            ->where(function ($query) {
                $query->whereNull('parent_id')
                    ->orWhere('parent_id', 0);
            })
            ->orderBy('row_order', 'asc')
            ->get();

        return view('admin.pages.tables.category_order', ['categories' => $categories]);
    }
    public function updateCategoryOrder(Request $request)
    {

        $category_ids = $request->input('category_id');
        $i = 0;

        foreach ($category_ids as $category_id) {
            $data = [
                'row_order' => $i
            ];

            Category::where('id', $category_id)->update($data);

            $i++;
        }
        return response()->json(['error' => false, 'message' => 'Category Order Saved !']);
    }

    public function category_slider()
    {

        $store_id = getStoreId();
        $categories = Category::where('status', 1)->where('store_id', $store_id)->orderBy('id', 'desc')->get();

        return view('admin.pages.forms.category_sliders', ['categories' => $categories]);
    }

    public function category_data(Request $request)
    {

        $store_id = getStoreId();
        $search = $request->input('term');
        $limit = (int) $request->input('limit', 10);


        // Query categories using where clause with name condition
        $query = Category::query()
            ->where('store_id', $store_id)
            ->where('status', 1)
            ->orderBy('id', 'desc');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $categories = $query->paginate($limit);
        // Map categories to format for response
        $formattedCategories = $categories->getCollection()->map(function ($category) {
            $level = 0;
            return [
                'id' => $category->id,
                'text' => $category->name,
                'image' => getMediaImageUrl($category->image),
                'parent_id' => $category->parent_id ?? "",
            ];
        });
        // Create a new collection instance with formatted categories
        $formattedCollection = new Collection($formattedCategories);

        // Construct the response
        $response = [
            'total' => $categories->total(),
            'results' => $formattedCollection,
        ];

        return response()->json($response);
    }

    public function store_category_slider(Request $request)
    {

        $store_id = getStoreId();
        $validator = Validator::make($request->all(), [
            'title' => 'required',

            'category_slider_style' => 'required',
            'background_color' => 'required',
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

        $validatedData['title'] = $request->title ?? '';;
        $validatedData['category_ids'] = isset($request->category_ids) ? implode(',', $request->category_ids) : '';

        // Rename the "category_slider_style" key to "style"
        $validatedData['style'] = $validatedData['category_slider_style'];
        unset($validatedData['category_slider_style']);


        $validatedData['status'] = 1;
        $validatedData['store_id'] = isset($store_id) ? $store_id : '';

        $validatedData['banner_image'] = $request->banner_image;
        CategorySliders::create($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.category_slider_created_successfully', 'Category slider created successfully')
            ]);
        }
    }

    public function category_sliders_list(Request $request)
    {
        $store_id = getStoreId();
        $search = trim($request->input('search'));
        $sort = $request->input('sort', 'category_sliders.id');
        $order = $request->input('order', 'DESC');
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $status = $request->input('status', '');

        // Start the query with join
        $category_slider_data = CategorySliders::select('category_sliders.*')
            ->leftJoin('categories', function ($join) {
                $join->on('categories.id', '=', DB::raw('FIND_IN_SET(categories.id, category_sliders.category_ids)'));
            })
            ->where('category_sliders.store_id', $store_id);

        // Filter by search term in title or category name
        if ($search) {
            $category_ids = Category::where('name', 'like', '%' . $search . '%')->pluck('id');
            // Check if any category ID matches
            if ($category_ids->isNotEmpty()) {
                $ids = $category_ids->implode(',');
                $category_slider_data->where(function ($query) use ($search, $ids) {
                    $query->where('category_sliders.title', 'like', '%' . $search . '%')
                        ->orWhereRaw("FIND_IN_SET(category_sliders.category_ids, '$ids')")
                        ->orWhere(DB::raw("FIND_IN_SET(categories.id, category_sliders.category_ids)"), '>', 0);
                });
            } else {
                $category_slider_data->where('category_sliders.title', 'like', '%' . $search . '%');
            }
        }
        // Apply status filter if provided
        if (!is_null($status) && $status !== '') {
            $category_slider_data->where('category_sliders.status', $status);
        }
        // Count total records for pagination
        $total = $category_slider_data->count();

        // Fetch paginated data
        $sliders = $category_slider_data->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get();

        // Prepare data for response
        $data = $sliders->map(function ($s) {
            $delete_url = route('admin.category_sliders.destroy', $s->id);
            $edit_url = route('admin.category_sliders.update', $s->id);

            // Retrieve category names based on category_ids
            $categoryIds = explode(',', $s->category_ids);
            $category_names = Category::whereIn('id', $categoryIds)->pluck('name')->implode(', ');

            $action = '<div class="dropdown bootstrap-table-dropdown">
                        <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-horizontal-rounded"></i>
                        </a>
                        <div class="dropdown-menu table_dropdown category_slider_action_dropdown" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item dropdown_menu_items" href="' . $edit_url . '"><i class="bx bx-pencil mx-2"></i> Edit</a>
                            <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . $delete_url . '"><i class="bx bx-trash mx-2"></i> Delete</a>
                        </div>
                    </div>';

            return [
                'id' => $s->id,
                'title' => $s->title,
                'categories' => $category_names,
                'status' => '<select class="form-select status_dropdown change_toggle_status ' . ($s->status == 1 ? 'active_status' : 'inactive_status') . '" data-id="' . $s->id . '" data-url="/admin/category_sliders/update_status/' . $s->id . '" aria-label="">
                <option value="1" ' . ($s->status == 1 ? 'selected' : '') . '>Active</option>
                <option value="0" ' . ($s->status == 0 ? 'selected' : '') . '>Deactive</option>
                </select>',
                'operate' => $action,
            ];
        });

        return response()->json([
            "rows" => $data,
            "total" => $total,
        ]);
    }


    public function update_category_slider_status($id)
    {
        $category_slider = CategorySliders::findOrFail($id);
        $category_slider->status = $category_slider->status == '1' ? '0' : '1';
        $category_slider->save();
        return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
    }

    public function category_slider_destroy($id)
    {
        $category = CategorySliders::find($id);

        if ($category->delete()) {
            return response()->json([
                'error' => false,
                'message' => labels('admin_labels.slider_deleted_successfully', 'Slider deleted successfully!')
            ]);
        } else {
            return response()->json(['error' => labels('admin_labels.something_went_wrong', 'Something went wrong')]);
        }
    }

    public function category_slider_edit($data)
    {
        $store_id = getStoreId();

        $category_sliders = CategorySliders::where('status', 1)->where('store_id', $store_id)->get();

        $categories = Category::where('status', 1)->where('store_id', $store_id)->orderBy('id', 'desc')->get();

        $data = CategorySliders::where('store_id', $store_id)
            ->find($data);

        if ($data === null || empty($data)) {
            return view('admin.pages.views.no_data_found');
        } else {
            return view('admin.pages.forms.update_category_slider', [
                'data' => $data,
                'category_sliders' => $category_sliders,
                'categories' => $categories
            ]);
        }
    }


    public function category_slider_update(Request $request, $data)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',

            'category_slider_style' => 'required',
            'background_color' => 'required',
            'banner_image' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }
        $slider = CategorySliders::find($data);

        $validatedData = $validator->validated();

        $validatedData['title'] = $request->title ?? '';
        $validatedData['category_ids'] = isset($request->category_ids) ? implode(',', $request->category_ids) : '';
        $validatedData['style'] = $validatedData['category_slider_style'];
        unset($validatedData['category_slider_style']);
        $validatedData['status'] = 1;
        $validatedData['banner_image'] = $request->banner_image;
        $slider->update($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.slider_updated_successfully', 'Slider updated successfully'),
                'location' => route('category_slider.index')
            ]);
        }
    }
    public function delete_selected_data(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id'
        ]);

        $tables = ['products', 'seller_store'];
        $columns = ['category_id', 'category_ids'];

        // Initialize an array to store the IDs that can't be deleted
        $nonDeletableIds = [];

        foreach ($request->ids as $id) {
            if (isForeignKeyInUse($tables, $columns, $id)) {
                // Collect the ID that cannot be deleted
                $nonDeletableIds[] = $id;
            }
        }
        // Check if there are any non-deletable IDs
        if (!empty($nonDeletableIds)) {
            return response()->json([
                'error' => labels('admin_labels.cannot_delete_category_associated_with_products_seller', 'You cannot delete these categories: ' . implode(', ', $nonDeletableIds) . ' because they are associated with products and sellers.'),
                'non_deletable_ids' => $nonDeletableIds
            ], 401);
        }

        // Proceed to delete the categories that are deletable
        Category::destroy($request->ids);

        return response()->json(['message' => 'Selected categories deleted successfully.']);
    }
    public function delete_selected_slider_data(Request $request)
    {

        // Validate the incoming request
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:category_sliders,id'
        ]);

        CategorySliders::destroy($request->ids);

        return response()->json(['message' => 'Selected sliders deleted successfully.']);
    }
}
