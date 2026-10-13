<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DonorController extends Controller
{
    public const PAID_STATUS = 'success';

    public function index(Request $request): View
    {
        $f = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:30'],
            'method' => ['nullable', 'string', 'max:30'],
            'from'   => ['nullable', 'date'],
            'to'     => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = Donor::query()
            ->when($f['status'] ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($f['method'] ?? null, fn($q, $v) => $q->where('payment_method', $v))
            ->when($f['from'] ?? null, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($f['to'] ?? null, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($f['search'] ?? null, fn($q, $v) => $q->where(
                fn($q) => $q->where('name', 'like', "%{$v}%")
                    ->orWhere('razorpay_order_id', 'like', "%{$v}%")
                    ->orWhere('razorpay_payment_id', 'like', "%{$v}%")
            ));

        $byStatus = DB::table('donors')
            ->select('status', DB::raw('COUNT(*) as total_count'), DB::raw('COALESCE(SUM(amount), 0) as total_amount'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $paid      = $byStatus->get(self::PAID_STATUS);
        $paidCount = (int) ($paid->total_count ?? 0);
        $paidTotal = (float) ($paid->total_amount ?? 0);

        return view('admin.donations.index', [
            'donors'     => (clone $query)->latest()->paginate(25)->withQueryString(),
            'paidStatus' => self::PAID_STATUS,
            'summary'    => [
                'attempts'  => (int) $byStatus->sum('total_count'),
                'paidCount' => $paidCount,
                'paidTotal' => $paidTotal,
                'average'   => $paidCount > 0 ? $paidTotal / $paidCount : 0,
            ],
            'filtered'   => [
                'paidTotal' => (float) (clone $query)->where('status', self::PAID_STATUS)->sum('amount'),
            ],
            'statuses'   => $byStatus->keys()->filter()->values(),
            'methods'    => Donor::whereNotNull('payment_method')->distinct()->orderBy('payment_method')->pluck('payment_method'),
        ]);
    }
}
