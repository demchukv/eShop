<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComboProductFaq;
use App\Models\ComboProduct;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ComboProductFaqController extends Controller
{
    public function index()
    {
        return view('seller.pages.tables.combo_product_faqs');
    }
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:combo_products,id',
            'question' => 'required',
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $validatedData = $validator->validated();

        $user = Auth::user();

        $seller_id = Seller::where('user_id', $user->id)->value('id');


        $validatedData['product_id'] = $request->product_id;
        $validatedData['question'] = $request->question;
        $validatedData['answer'] = $request->answer;
        $validatedData['user_id'] = isset($request->user_id) && !empty($request->user_id) ? $request->user_id : $user->id;
        $validatedData['seller_id'] = $seller_id;
        $validatedData['answered_by'] = isset($request->answer) && !empty($request->answer) ? $user->id : 0;

        ComboProductFaq::create($validatedData);

        if ($request->ajax()) {
            return response()->json(['message' => labels('admin_labels.product_faq_created_successfully', 'Product Faq created successfully')]);
        }
    }


    public function list(Request $request)
    {
        $store_id = getStoreId();
        $search = trim(request('search'));
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : 0;
        $limit = (request('limit')) ? request('limit') : "10";

        $product_faqs = ComboProductFaq::leftJoin('combo_products', 'combo_product_faqs.product_id', '=', 'combo_products.id')
            ->leftJoin('users', 'combo_product_faqs.user_id', '=', 'users.id')
            ->where('combo_products.store_id', $store_id)
            ->select('combo_product_faqs.*', 'users.username');

        if (!empty($search)) {
            $product_faqs
                ->when($search, function ($query) use ($search) {
                    return $query->where('combo_product_faqs.question', 'like', '%' . $search . '%')
                        ->orWhere('combo_product_faqs.id', 'like', '%' . $search . '%')
                        ->orWhere('combo_product_faqs.answer', 'like', '%' . $search . '%');
                });
        }


        $total = $product_faqs->count();


        $product_faqs = $product_faqs->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get();


        $product_faqs = $product_faqs->map(function ($p) {
            $delete_url = route('seller.combo_product_faqs.destroy', $p->id);
            $action = '<div class="dropdown bootstrap-table-dropdown">
                        <a href="#" class="text-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-horizontal-rounded"></i>
                        </a>
                        <div class="dropdown-menu table_dropdown product_faq_action_dropdown" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item edit-product-faq" data-id="' . $p->id . '" data-bs-toggle="modal" data-bs-target="#edit_modal"><i class="bx bx-pencil"></i> Edit</a>
                            <a class="dropdown-item delete-data" data-url="' . $delete_url . '"><i class="bx bx-trash"></i> Delete</a>
                        </div>
                    </div>';

            return [
                'id' => $p->id,
                'question' => $p->question,
                'answer' => $p->answer,
                'answered_by' => $p->answered_by,
                'product_name' => $this->getProductName($p->product_id),
                'username' => $this->getUserName($p->user_id),
                'answered_by_name' => $p->username,
                'date_added' => getFormatedDate($p->created_at),
                'operate' => $action,
            ];
        });

        return response()->json([
            "rows" => $product_faqs,
            "total" => $total,
        ]);
    }


    private function getUserName($userId)
    {
        $user = User::find($userId);
        return $user ? $user->username : null;
    }
    private function getProductName($productId)
    {
        $product = ComboProduct::find($productId);
        return $product ? $product->title : null;
    }
    public function update_status($id)
    {
        $product_faq = ComboProductFaq::findOrFail($id);
        $product_faq->status = $product_faq->status == '1' ? '0' : '1';
        $product_faq->save();
        return response()->json(['success' => labels('admin_labels.status_updated_successfully', 'Status updated successfully.')]);
    }




    public function destroy($id)
    {
        $product_faq = ComboProductFaq::find($id);
        if ($product_faq) {
            $product_faq->delete();
            return response()->json(['error' => false, 'message' => labels('admin_labels.faq_deleted_successfully', 'Faq deleted Successfully')]);
        } else {
            return response()->json(['error' => labels('admin_labels.product_faq_deleted_successfully', 'Product Faq deleted successfully!')]);
        }
    }

    public function edit($id)
    {
        $product_faq = ComboProductFaq::find($id);

        if (!$product_faq) {
            return response()->json([
                'error' => true,
                'message' => labels('admin_labels.data_not_found', 'Data not found')
            ], 404);
        }

        return response()->json($product_faq);
    }

    public function update(Request $request, $id)
    {
        $product_faq = ComboProductFaq::findOrFail($id);
        $product_faq->answer = $request->answer;
        $product_faq->save();
        if ($request->ajax()) {
            return response()->json(['message' => labels('admin_labels.product_faq_updated_successfully', 'Product Faq updated successfully')]);
        }
    }
}
