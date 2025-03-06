<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;

class Home extends Component
{
    public $user_id;
    public function __construct()
    {
        $this->user_id = Auth::user() != '' ? Auth::user()->id : NUll;
    }
    public $store_id = "";

    public function render(Request $request)
    {
        $store_id = session('store_id');
        $this->store_id = $store_id;
        $sliders = getSliders("", "", "", $store_id);

        $categoryController = app(CategoryController::class);
        $categories = $categoryController->getCategories(sort: 'row_order', order: "ASC", store_id: $store_id);
        $categories = $categories->original;
        // dd($categories);
        $BrandController = app(BrandController::class);
        $brands = $BrandController->getBrands("", "", "", "", "ASC", $store_id);

        $categories_section = $this->getCategoriesSection();
        $sections = $this->sections();

        return view('livewire.' . config('constants.theme') . '.home.home', [
            'sliders' => $sliders,
            'categories' => $categories,
            'brands' => $brands,
            'sections' => $sections,
            'categories_section' => $categories_section,
        ])->layoutData([
            'title' => "Home |",
        ]);
    }

    public function getCategoriesSection()
    {
        $store_id = session('store_id');
        $sliders = fetchDetails('category_sliders', ['store_id' => $store_id, 'status' => 1], '*');
        if (count($sliders) >= 1) {
            foreach ($sliders as $key => $slider) {
                $categories_detail = fetchDetails(table: 'categories', where_in_key: "id", where_in_value: explode(",", $slider->category_ids));
                $sliders[$key]->banner_image = dynamic_image(getImageUrl($slider->banner_image), 620);
                foreach ($categories_detail as $k => $details) {
                    $categories_detail[$k]->image = dynamic_image(getImageUrl($details->image), 400);
                    $categories_detail[$k]->banner = dynamic_image(getImageUrl($details->banner), 400);
                }
                $sliders[$key]->categories_detail = $categories_detail;
            }
        }
        return $sliders;
    }

    public function sections()
    {
        $store_id = session('store_id');
        $limit =  12;
        $offset =  0;
        $sections = DB::table('sections')
            ->where('store_id', $store_id)
            ->orderBy('row_order')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();

        $filters['show_only_active_products'] = true;
        if (!empty($sections)) {
            for ($i = 0; $i < count($sections); $i++) {
                $product_ids = explode(',', (string)$sections[$i]->product_ids);
                $product_ids = array_filter($product_ids);
                $product_categories = (isset($sections[$i]->categories) && !empty($sections[$i]->categories) && $sections[$i]->categories != NULL) ? explode(',', $sections[$i]->categories ?? '') : null;
                if (isset($sections[$i]->product_type) && !empty($sections[$i]->product_type)) {
                    $filters['product_type'] = (isset($sections[$i]->product_type)) ? $sections[$i]->product_type : null;
                }
                if ($sections[$i]->style == "style_1") {
                    $limit = 12;
                } elseif ($sections[$i]->style == "style_2" || $sections[$i]->style == "style_3") {
                    $limit = 6;
                }
                if ($sections[$i]->product_type === "custom_combo_products") {
                    $combo_products = fetchComboProduct(user_id: $this->user_id, id: (isset($product_ids)) ? $product_ids : null, limit: $limit, store_id: $store_id);
                } else {
                    $products = fetchProduct(user_id: $this->user_id, filter: (isset($filters)) ? $filters : null, id: (isset($product_ids)) ? $product_ids : null, category_id: $product_categories, limit: $limit, store_id: $this->store_id, is_detailed_data: 0);
                }
                $sections[$i]->title =  $sections[$i]->title;
                $sections[$i]->banner_image =  dynamic_image(getMediaImageUrl($sections[$i]->banner_image), 800);
                $sections[$i]->slug =  Str::slug($sections[$i]->title);
                $sections[$i]->short_description =  $sections[$i]->short_description;
                $sections[$i]->filters = (isset($products['filters'])) ? $products['filters'] : [];
                if ($sections[$i]->product_type === "custom_combo_products") {
                    $sections[$i]->product_details = (object)$combo_products['combo_product'];
                } else {
                    $sections[$i]->product_details = (object)$products['product'];
                }
            }
        }
        return $sections;
    }

    public function sendMailTemplate($to, $template_key, $data = ['username' => 'jay', 'appname' => 'Ezeemart'], $givenLanguage = "")
    {
        $response = sendMailTemplate(to: $to, template_key: $template_key, data: $data);
        return $response;
    }
}
