<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\BlogCategory;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{

    public function index()
    {
        return view('admin.pages.forms.blog_categories');
    }

    public function storeCategory(Request $request)
    {

        $store_id = getStoreId();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $validatedData = $validator->validated();

        $validatedData['slug'] = generateSlug($request->input('name'), 'blog_categories');
        $validatedData['status'] = 1;
        $validatedData['store_id'] = $store_id;

        BlogCategory::create($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.category_created_successfully', 'Category created successfully')
            ]);
        }
    }

    public function editCategory($data)
    {
        $store_id = getStoreId();
        $categories = BlogCategory::all();
        $data = BlogCategory::where('store_id', $store_id)
            ->find($data);

        if ($data === null || empty($data)) {
            return view('admin.pages.views.no_data_found');
        } else {
            return view('admin.pages.forms.update_blog_category', [
                'data' => $data,
                'categories' => $categories
            ]);
        }
    }


    public function updateCategory(Request $request, $data)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }
        $category = BlogCategory::find($data);

        $validatedData = $validator->validated();

        $new_name = $request->name;
        $current_name = $category->name;
        $current_slug = $category->slug;

        $validatedData['slug'] = generateSlug($new_name, 'blog_categories', 'slug', $current_slug, $current_name);

        $validatedData['status'] = 1;

        $category->update($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.category_updated_successfully', 'Category updated successfully'),
                'location' => route('admin.blogs.index')
            ]);
        }
    }

    public function updateCategoryStatus($id)
    {
        $category = BlogCategory::findOrFail($id);

        if (isForeignKeyInUse('blogs', 'category_id', $id)) {
            return response()->json([
                'status_error' => labels('admin_labels.cannot_deactivate_category_associated_with_blogs', 'You cannot deactivate this category because it is associated with blogs.')
            ]);
        } else {
            $category->status = $category->status == '1' ? '0' : '1';
            $category->save();
            return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
        }
    }


    public function destroyCategory($id)
    {
        $category = BlogCategory::find($id);

        if (isForeignKeyInUse('blogs', 'category_id', $id)) {
            return response()->json([
                'error' => labels('admin_labels.cannot_delete_category_associated_with_blogs', 'You cannot delete this category because it is associated with blogs.')
            ]);
        }
        if ($category) {
            $category->delete();
            return response()->json(['error' => false, 'message' => labels('admin_labels.blog_category_deleted_successfully', 'Blog Category deleted successfully!')]);
        } else {
            return response()->json(['error' => labels('admin_labels.data_not_found', 'Data Not Found')]);
        }
    }

    public function categoryList(Request $request)
    {
        $store_id = getStoreId();
        $search = trim(request('search'));
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : 0;
        $limit = (request('limit')) ? request('limit') : "10";
        $status = $request->input('status') ?? '';

        $category_data = BlogCategory::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });
        if ($status !== '') {
            $category_data->where('status', $status);
        }
        $category_data->where('store_id', $store_id);
        $total = $category_data->count();

        // Use Paginator to handle the server-side pagination
        $blogs = $category_data->orderBy($sort, $order)->offset($offset)
            ->limit($limit)
            ->get();

        // Prepare the data for the "Actions" field
        $data = $blogs->map(function ($b) {
            $delete_url = route('admin.blog_categories.destroy', $b->id);
            $edit_url = route('blog_categories.edit', $b->id);
            $action = '<div class="dropdown bootstrap-table-dropdown">
                <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                </a>
                <div class="dropdown-menu table_dropdown brand_action_dropdown" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item dropdown_menu_items" href="' . $edit_url . '"><i class="bx bx-pencil mx-2"></i> Edit</a>
                    <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . $delete_url . '"><i class="bx bx-trash mx-2"></i> Delete</a>
                </div>
            </div>';
            $image = route('admin.dynamic_image', [
                'url' => getMediaImageUrl($b->image),
                'width' => 60,
                'quality' => 90
            ]);
            return [
                'id' => $b->id,
                'name' => $b->name,
                'status' => '<div class="d-flex justify-content-center"><select class="form-select status_dropdown change_toggle_status ' . ($b->status == 1 ? 'active_status' : 'inactive_status') . '" data-id="' . $b->id . '" data-url="admin/blog_categories/update_status/' . $b->id . '" aria-label="">
                  <option value="1" ' . ($b->status == 1 ? 'selected' : '') . '>Active</option>
                  <option value="0" ' . ($b->status == 0 ? 'selected' : '') . '>Deactive</option>
              </div></select>',
                'image' => '<div class="d-flex justify-content-center"><a href="' . getMediaImageUrl($b->image)  . '" data-lightbox="image-' . $b->id . '"><img src="' . $image . '" alt="Avatar" class="rounded"/></a></div>',
                'operate' => $action,
            ];
        });

        return response()->json([
            "rows" => $data,
            "total" => $total,
        ]);
    }

    public function createBlog()
    {
        $store_id = getStoreId();
        $categories = BlogCategory::where('status', 1)->where('store_id', $store_id)->orderBy('id', 'desc')->get();

        return view('admin.pages.forms.blogs', ['categories' => $categories]);
    }

    public function storeBlog(Request $request)
    {

        $store_id = getStoreId();

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'image' => 'required',
            'category_id' => 'required|exists:blog_categories,id',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $validatedData = $validator->validated();

        $validatedData['category_id'] = $request->category_id;
        $validatedData['description'] = $request->description;
        $validatedData['slug'] = generateSlug($request->input('title'), 'blogs');
        $validatedData['status'] = 1;
        $validatedData['store_id'] = $store_id;

        Blog::create($validatedData);

        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.blog_created_successfully', 'Blog created successfully')
            ]);
        }
    }

    public function getBlogCategories(Request $request)
    {

        $search = trim($request->search) ?? "";
        $store_id = getStoreId();

        $categories = BlogCategory::where('name', 'like', '%' . $search . '%')->where('store_id', $store_id)->where('status', 1)->get();

        $data = array();
        foreach ($categories as $category) {
            $data[] = array("id" => $category->id, "text" => $category->name);
        }
        return response()->json($data);
    }

    public function blogList(Request $request)
    {
        $store_id = getStoreId();

        $search = trim(request('search'));
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : 0;
        $limit = (request('limit')) ? request('limit') : "10";
        $category_id = (request('category_id')) ? request('category_id') : "";
        $blog_data = Blog::when($search, function ($query) use ($search) {
            return $query->where('title', 'like', '%' . $search . '%');
        });

        if ($category_id !== '') {
            $blog_data->where('category_id', $category_id);
        }

        $blog_data->where('store_id', $store_id);
        $total = $blog_data->count();

        // Use Paginator to handle the server-side pagination
        $blogs = $blog_data->orderBy($sort, $order)->offset($offset)
            ->limit($limit)
            ->get();

        // Prepare the data for the "Actions" field
        $data = $blogs->map(function ($b) {
            $delete_url = route('blogs.destroy', $b->id);
            $edit_url = route('blogs.edit', $b->id);
            $action = '<div class="dropdown bootstrap-table-dropdown">
                <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                </a>
                <div class="dropdown-menu table_dropdown blog_action_dropdown" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item dropdown_menu_items" href="' . $edit_url . '"><i class="bx bx-pencil mx-2"></i> Edit</a>
                    <a class="dropdown-item delete-data dropdown_menu_items" data-url="' . $delete_url . '"><i class="bx bx-trash mx-2"></i> Delete</a>
                </div>
            </div>';
            $image = route('admin.dynamic_image', [
                'url' => getMediaImageUrl($b->image),
                'width' => 60,
                'quality' => 90
            ]);
            return [
                'id' => $b->id,
                'title' => $b->title,
                'status' => '<div><select class="form-select status_dropdown change_toggle_status ' . ($b->status == 1 ? 'active_status' : 'inactive_status') . '" data-id="' . $b->id . '" data-url="admin/blogs/update_status/' . $b->id . '" aria-label="">
                  <option value="1" ' . ($b->status == 1 ? 'selected' : '') . '>Active</option>
                  <option value="0" ' . ($b->status == 0 ? 'selected' : '') . '>Deactive</option>
              </div></select>',
                'image' => '<div class="d-flex justify-content-center"><a href="' . getMediaImageUrl($b->image)  . '" data-lightbox="image-' . $b->id . '"><img src="' . $image . '" alt="Avatar" class="rounded"/></a></div>',
                'operate' => $action,
            ];
        });

        return response()->json([
            "rows" => $data, // Return the formatted data for the "Actions" field
            "total" => $total,
        ]);
    }

    public function updateBlog(Request $request, $data)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'image' => 'required',
            'category_id' => 'required|exists:blog_categories,id',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }
        $blog = Blog::find($data);

        $validatedData = $validator->validated();

        $validatedData['title'] = $request->title;
        $validatedData['image'] = $request->image;
        $validatedData['category_id'] = $request->category_id;
        $validatedData['description'] = $request->description;

        $new_name = $request->title;
        $current_name = $blog->title;

        $current_slug = $blog->slug;

        $validatedData['slug'] = generateSlug($new_name, 'blogs', 'slug', $current_slug, $current_name);
        $validatedData['status'] = 1;

        $blog->update($validatedData);

        if ($request->ajax()) {
            return response()->json(['message' => 'Blog updated successfully', 'location' => route('manage_blogs.index')]);
        }
    }
    public function editBlog($data)
    {
        $store_id = getStoreId();
        $categories = BlogCategory::where('status', '1')->get();

        $data = Blog::where('store_id', $store_id)
            ->find($data);

        if ($data === null || empty($data)) {
            return view('admin.pages.views.no_data_found');
        } else {
            return view('admin.pages.forms.update_blog', [
                'data' => $data,
                'categories' => $categories
            ]);
        }
    }

    public function destroyBlog($id)
    {

        $blog = Blog::find($id);

        if ($blog->delete()) {
            return response()->json([
                'error' => false,
                'message' => labels('admin_labels.blog_deleted_successfully', 'Blog deleted successfully!')
            ]);
        } else {
            return response()->json(['error' => labels('admin_labels.something_went_wrong', 'Something went wrong')]);
        }
    }
    public function updateBlogStatus($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->status = $blog->status == '1' ? '0' : '1';
        $blog->save();
        return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
    }
    public function delete_selected_data(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:blog_categories,id'
        ]);

        $nonDeletableIds = [];

        foreach ($request->ids as $id) {

            if (isForeignKeyInUse('blogs', 'category_id', $id)) {

                $nonDeletableIds[] = $id;
            }
        }
        if (!empty($nonDeletableIds)) {
            return response()->json([
                'error' => labels(
                    'admin_labels.cannot_delete_category_associated_with_blogs',
                    'You cannot delete these categories: ' . implode(', ', $nonDeletableIds) . ' because they are associated with blogs'
                ),
                'non_deletable_ids' => $nonDeletableIds
            ], 401);
        }
        BlogCategory::destroy($request->ids);

        return response()->json(['message' => 'Selected categories deleted successfully.']);
    }
    public function delete_selected_blog_data(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:blogs,id'
        ]);

        foreach ($request->ids as $id) {
            $blog = Blog::find($id);

            if ($blog) {
                Blog::where('id', $id)->delete();
            }
        }
        Blog::destroy($request->ids);

        return response()->json(['message' => 'Selected data deleted successfully.']);
    }
}
