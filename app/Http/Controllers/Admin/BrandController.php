<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function index()
    {
        return view('admin.pages.forms.brands');
    }

    public function store(Request $request)
    {
        $store_id = getStoreId();

        // Validate input data
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required',
            'image' => 'required',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }

        $validatedData = $validator->validated();

        // Check if the brand name already exists in the same store
        $existingBrand = Brand::where('name', $validatedData['brand_name'])
            ->where('store_id', $store_id)
            ->first();

        // If the brand name already exists, return an error
        if ($existingBrand) {
            $response = [
                'error' => true,
                'message' => 'Brand name already exists in this store.',
                'language_message_key' => 'brand_name_exists',
            ];
            return response()->json($response, 400); // 400 Bad Request error
        }

        // Prepare the validated data for insertion
        $validatedData['name'] = $validatedData['brand_name'];
        unset($validatedData['brand_name']);

        $validatedData['slug'] = generateSlug($request->input('brand_name'), 'brands');
        $validatedData['status'] = 1;
        $validatedData['store_id'] = $store_id;

        // Create the new brand record
        Brand::create($validatedData);

        // Return a success response if it's an AJAX request
        if ($request->ajax()) {
            return response()->json(['message' => labels('admin_labels.brand_created_successfully', 'Brand created successfully')]);
        }

        // If it's not an AJAX request, redirect with success message
        return redirect()->back()->with('success', labels('admin_labels.brand_created_successfully', 'Brand created successfully'));
    }



    public function list(Request $request)
    {
        $store_id = getStoreId();
        $search = trim(request('search'));
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : 0;

        $limit = (request('limit')) ? request('limit') : "10";
        $status = $request->input('status') ?? '';

        $brand_data = Brand::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });
        if ($status !== '') {
            $brand_data->where('status', $status);
        }
        $brand_data->where('store_id', $store_id);
        $total = $brand_data->count();

        // Use Paginator to handle the server-side pagination
        $brands = $brand_data->orderBy($sort, $order)->offset($offset)
            ->limit($limit)
            ->get();

        // Prepare the data for the "Actions" field
        $data = $brands->map(function ($b) {
            $edit_url = route('brands.edit', $b->id);
            $delete_url = route('brands.destroy', $b->id);
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
                'operate' => $action,
                'status' => '<select class="form-select status_dropdown change_toggle_status ' . ($b->status == 1 ? 'active_status' : 'inactive_status') . '" data-id="' . $b->id . '" data-url="/admin/brand/update_status/' . $b->id . '" aria-label="">
                  <option value="1" ' . ($b->status == 1 ? 'selected' : '') . '>Active</option>
                  <option value="0" ' . ($b->status == 0 ? 'selected' : '') . '>Deactive</option>
              </select>',
                'image' => '<div class=""><a href="' . getMediaImageUrl($b->image) . '" data-lightbox="image-' . $b->id . '"><img src="' . $image . '" alt="Avatar" class="rounded"/></a></div>',
            ];
        });

        return response()->json([
            "rows" => $data, // Return the formatted data for the "Actions" field
            "total" => $total,
        ]);
    }

    public function update_status($id)
    {
        $brand = Brand::findOrFail($id);

        if (isForeignKeyInUse('products', 'brand', $id)) {
            return response()->json(['status_error' => labels('admin_labels.you_can_not_deactivate_this_brand_because_it_is_associated_with_product', 'You cannot deactivate this brand because it is associated with products')]);
        } else {
            $brand->status = $brand->status == '1' ? '0' : '1';
            $brand->save();
            return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
        }
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);
        if (isForeignKeyInUse('products', 'brand', $id)) {
            return response()->json(['error' => labels('admin_labels.you_can_not_delete_this_brand_because_it_is_associated_with_product', 'You cannot delete this brand because it is associated with products')]);
        } else {
            if ($brand) {
                $brand->delete();
                return response()->json(['error' => false, 'message' => labels('admin_labels.brand_deleted_successfully', 'Brand deleted Successfully')]);
            } else {
                return response()->json(['error' => labels('admin_labels.data_not_found', 'Data Not Found')]);
            }
        }
    }

    public function bulk_upload()
    {
        return view('admin.pages.forms.brand_bulk_upload');
    }

    public function process_bulk_upload(Request $request)
    {

        if (!$request->hasFile('upload_file')) {
            return response()->json(['error' => 'true', 'message' => labels('admin_labels.please_choose_file', 'Please Choose File')]);
        }

        // Validate allowed mime types
        $allowed_mime_types = [
            'text/x-comma-separated-values',
            'text/comma-separated-values',
            'application/x-csv',
            'text/x-csv',
            'text/csv',
            'application/csv',
        ];

        $uploaded_file = $request->file('upload_file');
        $uploaded_mime_type = $uploaded_file->getClientMimeType();

        if (!in_array($uploaded_mime_type, $allowed_mime_types)) {
            return response()->json(['error' => 'true', 'message' => labels('admin_labels.invalid_file_format', 'Invalid File Format')]);
        }

        $csv = $_FILES['upload_file']['tmp_name'];
        $temp = 0;
        $temp1 = 0;
        $handle = fopen($csv, "r");

        $type = $request->type;

        if ($type == 'upload') {
            while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {

                if ($temp != 0) {
                    if (empty($row[0])) {
                        return response()->json(['error' => 'true', 'message' => labels('admin_labels.name_is_empty_at_row', 'Name is empty at row') . $temp]);
                    }
                    if (!empty($row[0])) {
                        if (isExist(['name' => $row[0]], 'brands')) {
                            return response()->json(['error' => 'true', 'message' => labels('admin_labels.brand_already_exist_please_provide_another_brand_at_row', 'Brand is already exist!please provide another brand at row') . $temp]);
                        }
                    }
                    if (empty($row[1])) {
                        return response()->json(['error' => 'true', 'message' => labels('admin_labels.image_is_empty_at_row', 'Image is empty at row') . $temp]);
                    }
                    if (empty($row[2])) {
                        return response()->json(['error' => 'true', 'message' => labels('admin_labels.store_id_empty_at_row', 'Store id is empty at row') . $temp]);
                    }
                }
                $temp++;
            }
            fclose($handle);
            $handle = fopen($csv, "r");
            while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {
                if ($temp1 !== 0) {
                    $data = [
                        'name' => $row[0],
                        'slug' => generateSlug($row[0], 'brands'),
                        'image' => $row[1],
                        'status' => 1,
                        'store_id' => $row[2],
                    ];

                    Brand::create($data);
                }
                $temp1++;
            }
            fclose($handle);
            return response()->json(['error' => 'false', 'message' => labels('admin_labels.brand_uploaded_successfully', 'Brand Uploaded Successfully')]);
        } else { // bulk_update
            while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row vales
            {
                if ($temp != 0) {
                    if (empty($row[0])) {
                        return response()->json(['error' => 'true', 'message' => labels('admin_labels.brand_id_is_empty_at_row', 'Brand id is empty at row') . $temp]);
                    }
                    if (!empty($row[0])) {
                        if (!isExist(['id' => $row[0]], 'brands')) {
                            return response()->json(['error' => 'true', 'message' => labels('admin_labels.brand_not_exist_please_provide_another_brand_id_at_row', 'Brand not exist please provide another brand id at row') . $temp]);
                        }
                    }
                    if (empty($row[1])) {
                        return response()->json(['error' => 'true', 'message' => labels('admin_labels.name_is_empty_at_row', 'Name is empty at row') . $temp]);
                    }
                    if (empty($row[2])) {
                        return response()->json(['error' => 'true', 'message' => labels('admin_labels.image_is_empty_at_row', 'Image is empty at row') . $temp]);
                    }
                }
                $temp++;
            }
            fclose($handle);
            $handle = fopen($csv, "r");
            while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
                if ($temp1 !== 0) {
                    $brand_id = $row[0];
                    $brands = fetchDetails('brands', ['id' => $brand_id], '*');
                    if (isset($brands[0]) && !empty($brands[0])) {
                        $data = [];
                        if (!empty($row[1])) {
                            $data['name'] = $row[1];
                            $existing_brand = Brand::where('name', $data['name'])->first();
                            if ($existing_brand) {
                                return response()->json(['error' => 'true', 'message' => "Brand name '{$data['name']}' already exists. Please provide another name."]);
                            } else {
                                $data['slug'] = generateSlug($data['name'], 'brands');
                            }
                        } else {
                            $data['name'] = $brands[0]['name'];
                        }

                        if (!empty($row[2])) {
                            $data['image'] = $row[2];
                        } else {
                            $data['image'] = $brands[0]['image'];
                        }

                        Brand::where('id', $brand_id)->update($data);
                    }
                }
                $temp1++;
            }
            fclose($handle);
            return response()->json(['error' => 'false', 'message' =>  labels('admin_labels.brand_updated_successfully', 'Brand Updated Successfully')]);
        }
    }

    public function edit($data)
    {
        $store_id = getStoreId();

        $data = Brand::where('store_id', $store_id)
            ->find($data);
        if ($data === null || empty($data)) {
            return view('admin.pages.views.no_data_found');
        } else {
            return view('admin.pages.forms.update_brand', [
                'data' => $data
            ]);
        }
    }


    public function update(Request $request, $data)
    {
        $store_id = getStoreId();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }

        // Find the brand that you want to update
        $brand = Brand::find($data);

        if (!$brand) {
            return response()->json(['error' => 'Brand not found.'], 404);
        }

        // Get the new brand name and compare it with the current one
        $new_name = $request->name;
        $current_name = $brand->name;

        // Check if a brand with the same name exists in the same store and it's not the current brand being updated
        if ($new_name !== $current_name) {
            $existingBrand = Brand::where('name', $new_name)
                ->where('store_id', $store_id)
                ->first();

            if ($existingBrand) {
                return response()->json([
                    'error' => true,
                    'message' => 'Brand name already exists in this store.',
                    'language_message_key' => 'brand_name_exists',
                ], 400); // Return error if the brand name exists
            }
        }

        // Prepare the validated data for update
        $validatedData = $validator->validated();

        // Prepare the data for updating
        $validatedData['name'] = $new_name;
        $validatedData['image'] = $request->image;
        $validatedData['slug'] = generateSlug($new_name, 'brands', 'slug', $brand->slug, $current_name);
        $validatedData['status'] = 1;

        // Update the brand with new values
        $brand->update($validatedData);

        // Return success response for AJAX or regular requests
        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.brand_updated_successfully', 'Brand Updated Successfully'),
                'location' => route('brands.index')
            ]);
        }

        return redirect()->route('brands.index')->with('success', labels('admin_labels.brand_updated_successfully', 'Brand Updated Successfully'));
    }


    public function get_brand_list($search = "", $offset = 0, $limit = 25, $store_id, $ids = "")
    {

        $query = Brand::where('store_id', $store_id)->where('status', '1');

        if (!empty($ids)) {
            // Convert the comma-separated ids string to an array
            $idsArray = explode(',', $ids);
            $query->whereIn('id', $idsArray);
        }
        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        $total = $query->count();

        $brands = $query->skip($offset)->take($limit)->get()->toArray();


        if (!empty($brands)) {
            for ($i = 0; $i < count($brands); $i++) {
                $brands[$i] = $brands[$i];
                $brands[$i]['image'] = getMediaImageUrl($brands[$i]['image']);
                unset($brands[$i]['created_at']);
                unset($brands[$i]['updated_at']);
            }
        }
        $brands_data = [
            'error'   => empty($brands),
            'message' => empty($brands) ? labels('admin_labels.brands_not_found', 'Brands not found') : labels('admin_labels.brands_retrived_successfully', 'Brands Retrived Successfully'),
            'language_message_key' => empty($brands) ? 'brands_not_found' : 'brands_retrived_successfully',
            'total'   => $total,
            'data'    => empty($brands) ? [] : $brands,
        ];
        return $brands_data;
    }
    public function delete_selected_data(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:brands,id'
        ]);

        // Initialize an array to store the IDs that can't be deleted
        $nonDeletableIds = [];

        // Loop through each brand ID
        foreach ($request->ids as $id) {
            // Check if the brand is associated with products
            if (isForeignKeyInUse('products', 'brand', $id)) {
                // Add the ID to the list of non-deletable IDs
                $nonDeletableIds[] = $id;
            }
        }

        // If there are non-deletable IDs, return them in the response
        if (!empty($nonDeletableIds)) {
            return response()->json([
                'error' => labels(
                    'admin_labels.cannot_delete_brand_associated_with_products',
                    'You cannot delete these brands: ' . implode(', ', $nonDeletableIds) . ' because they are associated with products'
                ),
                'non_deletable_ids' => $nonDeletableIds
            ], 401);
        }

        // Delete the brands if no association is found
        Brand::destroy($request->ids);

        return response()->json(['message' => 'Selected brands deleted successfully.']);
    }
}
