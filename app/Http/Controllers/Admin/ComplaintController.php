<?php

namespace App\Http\Controllers\Admin;

use App\Actions\RecordActivity;
use App\Enums\ActivityEvent;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'status' => ['nullable', Rule::in(['all', ...Complaint::STATUSES])],
        ]);

        $status = $data['status'] ?? 'open';

        $complaints = Complaint::query()
            ->with('user:id,name')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.complaints.index', [
            'complaints' => $complaints,
            'status'     => $status,
            'counts'     => Complaint::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    public function show(Complaint $complaint): View
    {
        return view('admin.complaints.show', ['complaint' => $complaint->load('user:id,name')]);
    }

    public function update(Request $request, Complaint $complaint, RecordActivity $record): RedirectResponse
    {
        $data = $request->validate([
            'status'      => ['required', Rule::in(Complaint::STATUSES)],
            'admin_reply' => ['nullable', 'string', 'max:2000'],
        ]);

        $reply = $data['admin_reply'] ?? null;
        $statusChanged = $data['status'] !== $complaint->status;

        if ($reply !== $complaint->admin_reply) {
            $complaint->replied_at = $reply === null ? null : now();
        }

        $complaint->admin_reply = $reply;
        $complaint->status = $data['status'];
        // Resolved rehne par purana time bacha rahta hai, reopen par null
        $complaint->resolved_at = $data['status'] === 'resolved' ? ($complaint->resolved_at ?? now()) : null;
        $complaint->save();

        if ($statusChanged) {
            $record->handle(ActivityEvent::ComplaintStatusChanged, Auth::id(), ['status' => $data['status']]);
        }

        return redirect()->route('admin.complaints.show', $complaint)->with('success', 'Complaint has been updated.');
    }
}
