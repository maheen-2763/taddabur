<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    private const MAX_OPEN_COMPLAINTS = 5;

    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        return view('feedback.index', [
            'review'     => Review::withTrashed()->where('user_id', $userId)->first(),
            'complaints' => Complaint::where('user_id', $userId)->latest()->limit(20)->get(),
            'categories' => Complaint::USER_CATEGORIES,
        ]);
    }

    public function storeReview(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // withTrashed: update the same row even if an admin hid it (avoids a unique user_id violation)
        $review = Review::withTrashed()->firstOrNew(['user_id' => $request->user()->id]);
        $isNew = ! $review->exists;
        $review->fill([
            'rating'   => $data['rating'],
            'comment'  => $data['comment'] ?? null,
            'category' => 'general',
        ])->save();

        return redirect()->route('feedback.index')->with('success', $isNew ? 'JazakAllahu Khair! Your review has been saved.' : 'Your review has been updated.');
    }

    public function destroyReview(Request $request): RedirectResponse
    {
        // A user removing their own review is permanent (no soft delete)
        Review::withTrashed()->where('user_id', $request->user()->id)->forceDelete();

        return redirect()->route('feedback.index')->with('success', 'Your review has been removed.');
    }

    public function storeComplaint(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject'  => ['required', 'string', 'max:120'],
            'message'  => ['required', 'string', 'min:10', 'max:2000'],
            'category' => ['required', Rule::in(array_keys(Complaint::USER_CATEGORIES))],
        ]);

        $userId = $request->user()->id;

        if (Complaint::where('user_id', $userId)->open()->count() >= self::MAX_OPEN_COMPLAINTS) {
            return back()
                ->withErrors(['complaint' => 'You already have ' . self::MAX_OPEN_COMPLAINTS . ' open complaints. Please wait for a reply before sending more.'])
                ->withInput();
        }

        // status, user_id and admin_reply never come from the request, only validated fields
        Complaint::create($data + ['user_id' => $userId]);

        return redirect()->route('feedback.index')->with('success', 'Your complaint has been received. We will review it shortly.');
    }
}
