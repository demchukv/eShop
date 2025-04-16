<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerReturnRequestController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $seller_id = Seller::where('user_id', $user_id)->value('id');
        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';

        return view('seller.pages.tables.manage_return_requests', compact('currency', 'seller_id'));
    }

    public function list(Request $request)
    {
        $search = trim($request->input('search', ''));
        $offset = $request->input('pagination_offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'rr.id');
        $order = $request->input('order', 'DESC');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $status = $request->input('status') !== null ? $request->input('status') : null;
        $reason = $request->input('reason');
        $seller_id = Seller::where('user_id', Auth::id())->value('id');

        // Валідація offset і limit
        $offset = is_numeric($offset) && $offset >= 0 ? (int)$offset : 0;
        $limit = is_numeric($limit) && $limit > 0 ? (int)$limit : 10;

        // Define $currency
        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';

        $multipleWhere = [];

        if (!empty($search)) {
            $multipleWhere = [
                'rr.id' => $search,
                'rr.order_id' => $search,
                'rr.order_item_id' => $search,
                'u.username' => $search,
                'rr.reason' => $search,
                'p.name' => $search,
            ];
        }

        // Count query
        $countQuery = DB::table('return_requests as rr')
            ->leftJoin('users as u', 'u.id', '=', 'rr.user_id')
            ->leftJoin('products as p', 'p.id', '=', 'rr.product_id')
            ->leftJoin('order_items as oi', 'oi.id', '=', 'rr.order_item_id')
            ->where('oi.seller_id', $seller_id)
            ->selectRaw('COUNT(rr.id) as total');

        if ($start_date && $end_date) {
            $countQuery->whereDate('rr.created_at', '>=', $start_date)
                ->whereDate('rr.created_at', '<=', $end_date);
        }

        if (!empty($search)) {
            $countQuery->where(function ($query) use ($multipleWhere) {
                foreach ($multipleWhere as $column => $value) {
                    $query->orWhere($column, 'like', '%' . $value . '%');
                }
            });
        }

        if ($status !== null) {
            $countQuery->where('rr.status', $status);
        }

        if ($reason) {
            $countQuery->where('rr.reason', $reason);
        }

        $total = $countQuery->first()->total;

        // Data query
        $dataQuery = DB::table('return_requests as rr')
            ->leftJoin('users as u', 'u.id', '=', 'rr.user_id')
            ->leftJoin('products as p', 'p.id', '=', 'rr.product_id')
            ->leftJoin('order_items as oi', 'oi.id', '=', 'rr.order_item_id')
            ->leftJoin('disputs as d', 'd.return_request_id', '=', 'rr.id')
            ->select(
                'rr.id',
                'rr.order_id',
                'rr.order_item_id',
                'rr.user_id',
                'rr.reason',
                'rr.status',
                'rr.refund_amount',
                'rr.created_at',
                'u.username',
                'p.name as product_name',
                'oi.seller_id',
                'd.id as disput_id'
            )
            ->where('oi.seller_id', $seller_id);

        if ($start_date && $end_date) {
            $dataQuery->whereDate('rr.created_at', '>=', $start_date)
                ->whereDate('rr.created_at', '<=', $end_date);
        }

        if (!empty($search)) {
            $dataQuery->where(function ($query) use ($multipleWhere) {
                foreach ($multipleWhere as $column => $value) {
                    $query->orWhere($column, 'like', '%' . $value . '%');
                }
            });
        }

        if ($status !== null) {
            $dataQuery->where('rr.status', $status);
        }

        if ($reason) {
            $dataQuery->where('rr.reason', $reason);
        }

        $returnRequests = $dataQuery->orderBy($sort, $order)
            ->skip($offset)
            ->take($limit)
            ->get();

        $bulkData = [];
        $bulkData['total'] = $total;
        $rows = [];
        $count = 1;

        foreach ($returnRequests as $row) {
            $statusLabel = match ($row->status) {
                0 => '<label class="badge bg-secondary">Pending</label>',
                1 => '<label class="badge bg-success">Approved</label>',
                2 => '<label class="badge bg-danger">Declined</label>',
                default => '<label class="badge bg-dark">Unknown</label>',
            };

            $action = '<div class="d-flex align-items-center">';
            if ($row->disput_id) {
                $action .= '<a href="' . route('seller.disput.show', $row->disput_id) . '" class="btn btn-sm btn-primary me-2" title="View Disput"><i class="fa fa-eye"></i></a>';
            }
            $action .= '</div>';

            $rows[] = [
                'id' => $count,
                'return_request_id' => $row->id,
                'order_id' => $row->order_id,
                'order_item_id' => $row->order_item_id,
                'user_id' => $row->user_id,
                'username' => $row->username,
                'product_name' => $row->product_name,
                'reason' => config('return_reasons')[$row->reason] ?? $row->reason,
                'refund_amount' => $currency . number_format($row->refund_amount, 2),
                'status' => $statusLabel,
                'date_added' => \Carbon\Carbon::parse($row->created_at)->format('d-m-Y'),
                'operate' => $action,
            ];

            $count++;
        }

        $bulkData['rows'] = $rows;

        return response()->json($bulkData);
    }
}
