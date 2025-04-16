<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReturnRequestController extends Controller
{
    public function index()
    {
        $currencyDetails = fetchDetails('currencies', ['is_default' => 1], 'symbol');
        $currency = !empty($currencyDetails) ? $currencyDetails[0]->symbol : '';

        return view('admin.pages.tables.manage_return_requests', compact('currency'));
    }

    public function list(Request $request)
    {
        $search = trim($request->input('search', ''));
        $offset = $search || $request->input('pagination_offset') ? $request->input('pagination_offset', 0) : 0;
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'rr.id');
        $order = $request->input('order', 'DESC');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $status = $request->input('status') !== null ? $request->input('status') : null;
        $reason = $request->input('reason');

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
                'rr.product_id',
                'rr.product_variant_id',
                'rr.reason',
                'rr.status',
                'rr.remarks',
                'rr.delivery_status',
                'rr.application_type',
                'rr.refund_amount',
                'rr.refund_method',
                'rr.description',
                'rr.evidence_path',
                'rr.return_method',
                'rr.created_at',
                'u.username',
                'p.name as product_name',
                'oi.seller_id',
                'd.id as disput_id'
            );

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
                $action .= '<a href="' . route('admin.disput.show', $row->disput_id) . '" class="btn btn-sm btn-primary me-2" title="View Disput"><i class="fa fa-eye"></i></a>';
            }
            $action .= '<a href="javascript:void(0)" class="btn btn-sm btn-info me-2 view-return" data-id="' . $row->id . '" data-order-id="' . $row->order_id . '" data-order-item-id="' . $row->order_item_id . '" data-username="' . $row->username . '" data-product-name="' . htmlspecialchars($row->product_name) . '" data-product-id="' . $row->product_id . '" data-product-variant-id="' . $row->product_variant_id . '" data-reason="' . htmlspecialchars($row->reason) . '" data-status="' . $row->status . '" data-remarks="' . htmlspecialchars($row->remarks ?? '') . '" data-delivery-status="' . ($row->delivery_status ?? '') . '" data-application-type="' . ($row->application_type ?? '') . '" data-refund-amount="' . $row->refund_amount . '" data-refund-method="' . ($row->refund_method ?? '') . '" data-description="' . htmlspecialchars($row->description ?? '') . '" data-evidence-path=\'' . ($row->evidence_path ?? '') . '\' data-return-method="' . ($row->return_method ?? '') . '" data-bs-toggle="modal" data-bs-target="#view_return_request_modal"><i class="fa fa-info-circle"></i></a>';
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

    public function approve(Request $request, $id)
    {
        $returnRequest = ReturnRequest::findOrFail($id);
        $returnRequest->update(['status' => 1]);
        return response()->json(['error' => false, 'message' => 'Return request approved.']);
    }

    public function decline(Request $request, $id)
    {
        $returnRequest = ReturnRequest::findOrFail($id);
        $returnRequest->update(['status' => 2]);
        return response()->json(['error' => false, 'message' => 'Return request declined.']);
    }
}
