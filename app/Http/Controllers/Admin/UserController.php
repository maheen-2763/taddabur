<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    private const SORTS = [
        'newest' => ['created_at', 'desc'],
        'oldest' => ['created_at', 'asc'],
        'name'   => ['name', 'asc'],
        'active' => ['ayahs_read', 'desc'],
    ];


    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');
        $sort   = (string) $request->query('sort', 'newest');
        [$column, $direction] = self::SORTS[$sort] ?? self::SORTS['newest'];

        $users = User::query()
            ->addSelect(['ayahs_read' => DB::table('user_read_ayahs')
                ->selectRaw('COUNT(*)')
                ->whereColumn('user_read_ayahs.user_id', 'users.id')])
            ->withCount('bookmarks')
            ->when($search !== '', fn($q) => $q->where(
                fn($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
            ))
            ->when($status === 'verified', fn($q) => $q->whereNotNull('email_verified_at'))
            ->when($status === 'unverified', fn($q) => $q->whereNull('email_verified_at'))
            ->orderBy($column, $direction)
            ->orderByDesc('users.id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $count = fn(string $table) => DB::table($table)->where('user_id', $user->id)->count();

        $stats = [
            'Ayahs read'         => $count('user_read_ayahs'),
            'Ayahs listened'     => $count('listened_ayahs'),
            'Chapters completed' => $count('chapter_completions'),
            'Hadiths read'       => $count('hadith_reads'),
            'Bookmarks'          => $count('bookmarks'),
            'Notes'              => DB::table('notes')->where('user_id', $user->id)->whereNull('deleted_at')->count(),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }
}
