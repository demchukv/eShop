<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BalanceController extends Controller
{
    public function index()
    {
        $userIdSubOptions = [
            CommissionDistribution::USER_ID_SUB_SHAREHOLDERS => 'Shareholders',
            CommissionDistribution::USER_ID_SUB_COMPANY_ONE => 'Company One',
            CommissionDistribution::USER_ID_SUB_COMPANY_TWO => 'Company Two',
        ];

        $statusOptions = [
            CommissionDistribution::STATUS_PENDING => 'Pending',
            CommissionDistribution::STATUS_COMPLETED => 'Completed',
            CommissionDistribution::STATUS_CANCELED => 'Canceled',
        ];

        // За замовчуванням — поточний місяць і рік
        $defaultMonth = Carbon::now()->month;
        $defaultYear = Carbon::now()->year;

        // Fetch min and max years from the database
        $minMaxYears = CommissionDistribution::selectRaw('MIN(YEAR(created_at)) as min_year, MAX(YEAR(created_at)) as max_year')
            ->first();
        $minYear = $minMaxYears->min_year ?? $defaultYear; // Fallback to current year if no data
        $maxYear = $minMaxYears->max_year ?? $defaultYear; // Fallback to current year if no data

        // Підготовка списку місяців
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->format('F');
        }

        return view('admin.pages.tables.balance', compact(
            'userIdSubOptions',
            'statusOptions',
            'defaultMonth',
            'defaultYear',
            'months',
            'minYear',
            'maxYear'
        ));
    }

    public function list()
    {
        $offset = request('pagination_offset') ? request('pagination_offset') : 0;
        $limit = request()->input('limit', 10);
        $sort = request()->input('sort', 'id');
        $order = request()->input('order', 'ASC');
        $userIdFilter = request()->input('user_id_filter');
        $userIdSubFilter = request()->input('user_id_sub_filter');
        $statusFilter = request()->input('status_filter');
        $monthFilter = request()->input('month_filter', Carbon::now()->month);
        $yearFilter = request()->input('year_filter', Carbon::now()->year);

        $query = CommissionDistribution::query();

        $query->whereMonth('created_at', $monthFilter)
            ->whereYear('created_at', $yearFilter);

        if (!empty($userIdFilter)) {
            $query->where('user_id', $userIdFilter);
        }

        if (!empty($userIdSubFilter)) {
            $query->where('user_id_sub', $userIdSubFilter);
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $total = $query->count();

        $results = $query->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->select('id', 'order_id', 'user_id', 'user_id_sub', 'amount', 'message', 'status', 'created_at')
            ->get();

        $summaryQuery = CommissionDistribution::whereMonth('created_at', $monthFilter)
            ->whereYear('created_at', $yearFilter);
        if (!empty($userIdFilter)) {
            $summaryQuery->where('user_id', $userIdFilter);
        }
        $summary = $summaryQuery->selectRaw('status, SUM(amount) as total_amount')
            ->groupBy('status')
            ->pluck('total_amount', 'status')
            ->map(function ($amount) {
                return formateCurrency(formatePriceDecimal($amount));
            })->toArray();

        $userIdOneSummary = [];
        if ($userIdFilter == 1) {
            $userIdOneSummary = CommissionDistribution::whereMonth('created_at', $monthFilter)
                ->whereYear('created_at', $yearFilter)
                ->where('user_id', 1)
                ->selectRaw('user_id_sub, status, SUM(amount) as total_amount')
                ->groupBy('user_id_sub', 'status')
                ->get()
                ->groupBy('user_id_sub')
                ->map(function ($group) {
                    return $group->pluck('total_amount', 'status')->map(function ($amount) {
                        return formateCurrency(formatePriceDecimal($amount));
                    })->toArray();
                })->toArray();
        }

        $rows = [];
        foreach ($results as $row) {
            $tempRow = [];
            $tempRow['id'] = $row->id;
            $tempRow['order_id'] = $row->order_id;
            $tempRow['user_id'] = $row->user_id;
            $tempRow['user_id_sub'] = $row->user_id_sub ?? 'N/A';
            $tempRow['amount'] = formateCurrency(formatePriceDecimal($row->amount));
            $tempRow['message'] = $row->message;
            $tempRow['status'] = match ($row->status) {
                CommissionDistribution::STATUS_PENDING => '<span class="badge bg-success">Pending</span>',
                CommissionDistribution::STATUS_COMPLETED => '<span class="badge bg-primary">Completed</span>',
                CommissionDistribution::STATUS_CANCELED => '<span class="badge bg-danger">Canceled</span>',
                default => '<span class="badge bg-secondary">' . $row->status . '</span>',
            };
            $tempRow['created_at'] = Carbon::parse($row->created_at)->format('Y-m-d');
            $rows[] = $tempRow;
        }

        return response()->json([
            'rows' => $rows,
            'total' => $total,
            'summary' => $summary,
            'user_id_one_summary' => $userIdOneSummary,
            'year' => $yearFilter,
            'month' => Carbon::create()->month($monthFilter)->format('F'),
        ]);
    }
}
