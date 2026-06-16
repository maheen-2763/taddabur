<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Services\BookmarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    public function __construct(private BookmarkService $bookmarkService) {}

    // GET /bookmarks
    public function index(): View
    {
        // ✅ Service handles loading + pagination
        $bookmarks = $this->bookmarkService->getUserBookmarks(Auth::user());

        return view('bookmarks.index', compact('bookmarks'));
    }

    // POST /bookmarks (AJAX)
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type'  => 'required|in:ayah,chapter',
            'id'    => 'required|integer',
            'label' => 'nullable|string|max:100',
        ]);

        // ✅ Service handles toggle logic (add or remove)
        $result = $this->bookmarkService->toggle(
            Auth::user(),
            $request->type,
            $request->id
        );

        return response()->json($result);
    }

    // DELETE /bookmarks/{bookmark}
    public function destroy(Bookmark $bookmark): JsonResponse
    {
        // ✅ Service handles ownership check + deletion
        $this->bookmarkService->delete(Auth::user(), $bookmark);

        return response()->json([
            'status'  => 'deleted',
            'message' => 'Bookmark deleted.',
        ]);
    }
}
