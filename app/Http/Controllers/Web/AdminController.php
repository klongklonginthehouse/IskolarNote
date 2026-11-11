<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Administrator-only area. Every route in this controller is behind the
 * 'role:admin' middleware alias in routes/web.php, so the checks below run
 * only for users whose server-side role is already 'admin'.
 */
class AdminController extends Controller
{
    public const ROLES = ['student', 'author', 'moderator', 'faculty', 'admin'];

    public function index()
    {
        $counts = User::query()
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('admin.dashboard', [
            'users' => User::orderBy('id')->paginate(20),
            'counts' => $counts,
            'roles' => self::ROLES,
            'stats' => [
                'users' => User::count(),
                'resources' => Resource::count(),
                'pending' => Resource::where('status', 'pending')->count(),
                'reports' => Report::whereNull('resolved_at')->count(),
            ],
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(self::ROLES)],
        ]);

        $actor = $request->user();

        if ($actor->id === $user->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        // Never leave the application without a reachable administrator.
        if ($user->role === 'admin' && $data['role'] !== 'admin'
            && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'The last administrator cannot be demoted.');
        }

        $user->update(['role' => $data['role']]);

        return back()->with('status', "{$user->name} is now {$data['role']}.");
    }
}
