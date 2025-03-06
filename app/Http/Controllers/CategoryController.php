<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    // public function getCategories($id = null, $limit = '', $offset = '', $sort = 'row_order', $order = 'ASC', $has_child_or_item = 'true', $slug = '', $ignore_status = '', $seller_id = '', $store_id = '')
    // {
    //     $level = 0;

    //     if ($ignore_status == 1) {
    //         $where = isset($id) ? ['c1.id' => $id] : function ($query) {
    //             $query->where('c1.parent_id', 0)
    //                 ->where('c1.status', 1)
    //                 ->orWhereNull('c1.parent_id')
    //                 ->where('c1.status', 1)
    //                 ->orWhere('c1.parent_id', '');
    //         };
    //     } else {
    //         $where = isset($id) ? ['c1.id' => $id, 'c1.status' => 1] : function ($query) {
    //             $query->where('c1.parent_id', 0)
    //                 ->where('c1.status', 1)
    //                 ->orWhereNull('c1.parent_id')
    //                 ->where('c1.status', 1)
    //                 ->orWhere('c1.parent_id', '');
    //         };
    //     }

    //     $query = DB::table('categories as c1')
    //         ->select('c1.*')
    //         ->where($where)
    //         ->where('c1.store_id', $store_id);

    //     if (!empty($slug)) {
    //         $query->where('c1.slug', $slug);
    //     }

    //     if ($has_child_or_item === 'false') {

    //         $query->leftJoin('categories as c2', 'c2.parent_id', '=', 'c1.id')
    //             ->leftJoin('products as p', 'p.category_id', '=', 'c1.id')
    //             ->where(function ($query) {
    //                 $query->orWhere('c1.id', '=', DB::raw('p.category_id'))
    //                     ->orWhere('c2.parent_id', '=', 'c1.id');
    //             })
    //             ->groupBy('c1.id');
    //     }

    //     if (!empty($limit) || !empty($offset)) {
    //         $query->offset($offset)->limit($limit);
    //     }

    //     $query->orderBy($sort, $order);
    //     $categories = $query->get();
    //     $countRes = DB::table('categories as c1')->where($where)->selectRaw('count(c1.id) as total')->first();
    //     $countRes = $countRes->total;


    //     $i = 0;
    //     foreach ($categories as $pCat) {
    //         if ($has_child_or_item === 'true') {
    //             $pCat->children = subCategories($pCat->id, $level);
    //         }
    //         $categories[$i]->text = e($pCat->name);
    //         $categories[$i]->name = e($categories[$i]->name);
    //         $categories[$i]->state = ['opened' => true];
    //         $categories[$i]->icon = "jstree-folder";
    //         $categories[$i]->level = $level;
    //         $categories[$i]->image = getMediaImageUrl($categories[$i]->image);
    //         $categories[$i]->banner = getMediaImageUrl($categories[$i]->banner);
    //         $i++;
    //     }


    //     if (isset($categories[0])) {
    //         $categories[0]->total = $countRes;
    //     }

    //     return Response::json(compact('categories', 'countRes'));
    // }
    // public function subCategories($id, $level)
    // {
    //     $level = $level + 1;
    //     $category = Category::find($id);
    //     $categories = $category->children;

    //     $i = 0;
    //     foreach ($categories as $p_cat) {

    //         $categories[$i]->children = $this->subCategories($p_cat->id, $level);
    //         $categories[$i]->text = e($p_cat->name); // Use the Laravel "e" helper for output escaping
    //         $categories[$i]->state = ['opened' => true];
    //         $categories[$i]->level = $level;
    //         $p_cat['image'] = getMediaImageUrl($p_cat['image']);
    //         $p_cat['banner'] = getMediaImageUrl($p_cat['banner']);
    //         $i++;
    //     }

    //     return $categories;
    // }
    public function getCategories($id = null, $limit = '', $offset = '', $sort = 'row_order', $order = 'ASC', $has_child_or_item = 'true', $slug = '', $ignore_status = '', $seller_id = '', $store_id = '')
    {
        // dd($ignore_status);
        $query = Category::with(['children' => function ($query) use ($has_child_or_item) {
            if ($has_child_or_item === 'false') {
                $query->withCount('products')
                    ->withCount('children')
                    ->having('products_count', '>', 0)
                    ->orHaving('children_count', '>', 0);
            } else {
                $query->with('children');
            }
        }]);

        if ($ignore_status == 1) {
            $query->where(function ($q) use ($id) {
                $q->whereNull('parent_id')
                    ->orWhere('parent_id', 0)
                    ->orWhere('id', $id);
            });
        } else {
            $query->where(function ($q) use ($id) {
                $q->where('status', 1)
                    ->whereNull('parent_id')
                    ->orWhere('status', 1)
                    ->where('parent_id', 0)
                    ->orWhere('id', $id)
                    ->where('status', 1);
            });
        }

        if (!empty($slug)) {
            $query->where('slug', $slug);
        }

        $query->where('store_id', $store_id);

        if (!empty($limit) || !empty($offset)) {
            $query->offset($offset)->limit($limit);
        }

        $query->orderBy($sort, $order);
        // dd($query->tosql(), $query->getBindings());
        $categories = $query->get();

        $countRes = Category::where(function ($q) use ($id, $ignore_status) {
            if ($ignore_status == 1) {
                $q->whereNull('parent_id')
                    ->orWhere('parent_id', 0)
                    ->orWhere('id', $id);
            } else {
                $q->where('status', 1)
                    ->whereNull('parent_id')
                    ->orWhere('status', 1)
                    ->where('parent_id', 0)
                    ->orWhere('id', $id)
                    ->where('status', 1);
            }
        })->count();

        $categories = $this->formatCategories($categories);

        if (!empty($categories)) {
            $categories[0]['total'] = $countRes;
        }

        return response()->json(compact('categories', 'countRes'));
    }

    private function formatCategories($categories, $level = 0)
    {
        $formattedCategories = [];

        foreach ($categories as $category) {
            $category['text'] = e($category['name']);
            $category['name'] = e($category['name']);
            $category['state'] = ['opened' => true];
            $category['icon'] = "jstree-folder";
            $category['level'] = $level;
            $category['image'] = getMediaImageUrl($category['image']);
            $category['banner'] = getMediaImageUrl($category['banner']);

            if (!empty($category['children'])) {
                $category['children'] = $this->formatCategories($category['children'], $level + 1);
            }

            $formattedCategories[] = $category;
        }

        return $formattedCategories;
    }
}
