<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ActivityEvent;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class ActivityLogController  extends Controller
{

    public function index(Request $request): View
    {
        $data = $request->validate([
            'event' => ['nullable', Rule::enum(ActivityEvent::class)],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $logs = ActivityLog::query()
            ->when($data['event'] ?? null, fn($q, $v) => $q->where('event', $v))
            ->when($data['from'] ?? null, fn($q, $v) => $q->where('created_at', '>=', Carbon::parse($v)->startOfDay()))
            ->when($data['to'] ?? null, fn($q, $v) => $q->where('created_at', '<=', Carbon::parse($v)->endOfDay()))
            ->latest('created_at')->latest('id')
            ->paginate(50)->withQueryString();

        return view('admin.activity-log.index', ['logs' => $logs, 'events' => ActivityEvent::cases()]);
    }
}
