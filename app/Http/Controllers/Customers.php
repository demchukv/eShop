<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use App\Models\CommissionDistribution;

class Customers extends Controller
{
    public function getUserTransactionList(UserController $userController, Request $request)
    {
        $user_id = Auth::user()->id ?? null;
        $transaction_type = 'wallet';
        $res = $userController->transactions_list($user_id, '', $transaction_type);
        return $res;
    }
    public function wallet_withdrawal_request(UserController $userController, Request $request)
    {
        $user_id = Auth::user()->id ?? null;
        $transaction_type = 'wallet';
        $res = $userController->wallet_withdrawal_request($user_id);
        return $res;
    }

    public function get_transaction(UserController $userController, Request $request)
    {
        $user_id = Auth::user()->id ?? null;
        $transaction_type = 'transaction';
        $res = $userController->transactions_list($user_id, null, $transaction_type);
        return $res;
    }

    public function notifications(NotificationsController $notifications)
    {
        $user_id = Auth::user()->id ?? null;
        $res = $notifications->get_notifications($user_id);
        return $res;
    }

    public function getPendingCommissions(Request $request)
    {
        $user_id = Auth::user()->id ?? null;

        if (!$user_id) {
            return response()->json([
                'rows' => [],
                'total' => 0,
                'message' => 'User not authenticated',
            ]);
        }

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

        $commissions = $query->orderBy($sort, $order)
            ->skip($offset)
            ->take($limit)
            ->get();

        $rows = $commissions->map(function ($commission) {
            return [
                'id' => $commission->id,
                'order_id' => $commission->order_id,
                'amount' => $commission->amount,
                'message' => $commission->message,
                'status' => ucfirst($commission->status),
                'created_at' => $commission->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'rows' => $rows,
            'total' => $total,
        ]);
    }
}
