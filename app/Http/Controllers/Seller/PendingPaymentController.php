<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CommissionDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendingPaymentController extends Controller
{
    public function pending_payments()
    {
        return view('seller.pages.tables.pending_payments');
    }

    public function pending_payments_list(Request $request)
    {
        $user_id = Auth::user()->id;

        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');
        $search = $request->input('search', '');

        $query = CommissionDistribution::where('user_id', $user_id)
            ->where('status', CommissionDistribution::STATUS_PENDING);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        $total = $query->count();

        $pendingPayments = $query->orderBy($sort, $order)
            ->skip($offset)
            ->take($limit)
            ->get();

        $rows = $pendingPayments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'order_id' => $payment->order_id,
                'order_url' => route('seller.orders.edit', $payment->order_id), // Додаємо URL
                'amount' => $payment->amount,
                'message' => $payment->message,
                'status' => ucfirst($payment->status),
                'created_at' => $payment->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'rows' => $rows,
            'total' => $total,
        ]);
    }
}
