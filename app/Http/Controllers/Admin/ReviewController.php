<?php

namespace App\Http\Controllers\Admin;

use App\Actions\RecordActivity;
use App\Enums\ActivityEvent;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'rating'     => ['nullable', 'integer', 'between:1,5'],
            'visibility' => ['nullable', Rule::in(['visible', 'hidden'])],
        ]);

        $visibility = $data['visibility'] ?? 'visible';

        $reviews = ($visibility === 'hidden' ? Review::onlyTrashed() : Review::query())
            ->with('user:id,name')
            ->when($data['rating'] ?? null, fn($q, $rating) => $q->where('rating', $rating))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews'    => $reviews,
            'visibility' => $visibility,
            // Average sirf visible reviews ka (hidden spam average na bigade)
            'stats'      => [
                'count'   => Review::count(),
                'average' => round((float) Review::avg('rating'), 1),
                'hidden'  => Review::onlyTrashed()->count(),
            ],
        ]);
    }

    public function hide(Review $review, RecordActivity $record): RedirectResponse
    {
        $review->delete();
        $record->handle(ActivityEvent::ReviewHidden, Auth::id());

        return back()->with('success', 'Review has been hided.');
    }

    public function restore(Review $review, RecordActivity $record): RedirectResponse
    {
        $review->restore();
        $record->handle(ActivityEvent::ReviewRestored, Auth::id());

        return back()->with('success', 'Review has been restored.');
    }
}
