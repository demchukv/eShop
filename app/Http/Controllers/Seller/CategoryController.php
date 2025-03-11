<?php

namespace App\Http\Controllers\Seller;

use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 1)->get();
        return view('seller.pages.tables.categories', ['categories' => $categories]);
    }

    public function list(Request $request)
    {
        $store_id = getStoreId();
        $user_id = Auth::user()->id;

        $seller_id = Seller::where('user_id', $user_id)->value('id');

        $search = trim(request('search'));
        $sort = request('sort') ?: 'id';
        $order = request('order') ?: 'DESC';
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : 0;
        $limit = request('limit') ?: 10;


        $seller_data = DB::table('seller_store')->select('category_ids')->where('seller_id', $seller_id)->where('store_id', $store_id)->get();

        if (!$seller_data) {
            return response()->json([
                "rows" => [],
                "total" => 0,
            ]);
        }

        $category_ids = explode(",", $seller_data[0]->category_ids);

        // $category_data = Category::whereIn('id', $category_ids)->where('store_id', $store_id);
        $category_data = Category::where('store_id', $store_id);
        if ($search) {
            $category_data->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('parent_id', 'like', '%' . $search . '%');
            });
        }
        $total = $category_data->count();

        $categories = $category_data->orderBy($sort, $order)->offset($offset)
            ->limit($limit)
            ->get();

        $data = $categories->map(function ($c) {
            $status = ($c->status == 1) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Deactive</span>';
            $image = route('seller.dynamic_image', [
                'url' => getMediaImageUrl($c->image),
                'width' => 60,
                'quality' => 90
            ]);
            $banner = route('seller.dynamic_image', [
                'url' => getMediaImageUrl($c->banner),
                'width' => 60,
                'quality' => 90
            ]);
            return [
                'id' => $c->id,
                'name' => $c->name,
                'status' => $status,
                'image' => '<div><a href="' . getMediaImageUrl($c->image)  . '" data-lightbox="image-' . $c->id . '"><img src="' . $image  . '" alt="Avatar" class="rounded"/></a></div>',
                'banner' => '<div ><a href="' . getMediaImageUrl($c->banner) . '" data-lightbox="banner-' . $c->id . '"><img src="' . $banner  . '" alt="Avatar" class="rounded"/></a></div>',
            ];
        });

        return response()->json([
            "rows" => $data,
            "total" => $total,
        ]);
    }



    protected function getSubcategories($parentCategoryId)
    {
        $subcategories = Category::where('parent_id', $parentCategoryId)->get()->map(function ($sub) {
            return [
                'id' => $sub->id,
                'name' => $sub->name,
                'image' => asset('/storage/' . $sub->image),
                'banner' => asset('/storage/' . $sub->banner),
                'subcategories' => $this->getSubcategories($sub->id),
            ];
        });

        return $subcategories;
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
        $store_id = $request->store_id ?? getStoreId();
        $level = 0;
        $user_id = Auth::user()->id;
        $seller_id = Seller::where('user_id', $user_id)->value('id');
        $seller_data = DB::table('seller_store')
            ->select('category_ids')
            ->where('store_id', $store_id)
            ->where('seller_id', $seller_id)->get()[0];


        if (!$seller_data) {
            return [];
        }

        $category_ids = explode(",", $seller_data->category_ids);

        // $categories = Category::whereIn('id', $category_ids)
        //     ->where('status', 1)
        //     ->where('store_id', $store_id)
        //     ->get()
        //     ->toArray();
        // Enable ALL categories
        $categories = Category::where('status', 1)
            ->where('store_id', $store_id)
            ->get()
            ->toArray();

        foreach ($categories as &$p_cat) {
            $p_cat['children'] = $this->subCategories($p_cat['id'], $level);
            $p_cat['text'] = e($p_cat['name']);
            $p_cat['name'] = e($p_cat['name']);
            $p_cat['state'] = ['opened' => true];
            $p_cat['icon'] = "jstree-folder";
            $p_cat['level'] = $level;
            $p_cat['image'] = getMediaImageUrl($p_cat['image']);
            $p_cat['banner'] = getMediaImageUrl($p_cat['banner']);
        }

        if (!empty($categories)) {
            $categories[0]['total'] = count($category_ids);
        }

        return $categories;
    }

    public function get_seller_categories(Request $request)
    {
        $store_id = $request->store_id ?? getStoreId();
        $user_id = Auth::user()->id;
        $seller_id = Seller::where('user_id', $user_id)->value('id');

        $level = 0;
        $seller_id = $request->seller_id ?? $seller_id;
        $search = trim($request->input('search', ''));

        $seller_data = DB::table('seller_store')
            ->select('category_ids')
            ->where('store_id', $store_id)
            ->where('seller_id', $seller_id)
            ->first();

        if (!$seller_data) {
            return response()->json([
                'categories' => [],
                'total' => 0
            ]);
        }

        $category_ids = explode(",", $seller_data->category_ids);

        // Apply search filter
        // $categoriesQuery = Category::whereIn('id', $category_ids)
        //     ->where('status', 1)
        //     ->where('store_id', $store_id);
        // enable ALL categories
        $categoriesQuery = Category::where('status', 1)
            ->where('store_id', $store_id);

        if ($search) {
            $categoriesQuery->where('name', 'like', '%' . $search . '%');
        }

        $categories = $categoriesQuery->get()->toArray();

        foreach ($categories as &$p_cat) {
            $p_cat['children'] = $this->subCategories($p_cat['id'], $level);
            $p_cat['text'] = e($p_cat['name']);
            $p_cat['name'] = e($p_cat['name']);
            $p_cat['state'] = ['opened' => true];
            $p_cat['icon'] = "jstree-folder";
            $p_cat['level'] = $level;
            $p_cat['image'] = getMediaImageUrl($p_cat['image']);
            $p_cat['banner'] = getMediaImageUrl($p_cat['banner']);
        }

        // Replace null values with empty strings
        foreach ($categories as &$category) {
            foreach ($category as $key => $value) {
                if ($value === null) {
                    $category[$key] = '';
                }
            }
        }
        unset($category);

        // Prepare the response with total as a separate key
        return response()->json([
            'categories' => $categories,
            'total' => count($categories)
        ]);
    }

    public function get_seller_categories_filter()
    {
        $store_id = $request->store_id ?? getStoreId();
        $user_id = Auth::user()->id;
        $seller_id = Seller::where('user_id', $user_id)->value('id');

        $level = 0;
        $seller_id = $seller_id;
        $seller_data = DB::table('seller_store')
            ->select('category_ids')
            ->where('store_id', $store_id)
            ->where('seller_id', $seller_id)->get()[0];



        if (!$seller_data) {
            return [];
        }

        $category_ids = explode(",", $seller_data->category_ids);

        // $categories = Category::whereIn('id', $category_ids)
        //     ->where('status', 1)
        //     ->where('store_id', $store_id)
        //     ->get()
        //     ->toArray();
        $categories = Category::where('status', 1)
            ->where('store_id', $store_id)
            ->get()
            ->toArray();

        return $categories;
    }
}
