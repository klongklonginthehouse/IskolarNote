<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * The shelf is private to the account that owns it.
 *
 * There is no role that unlocks somebody else's shelf, administrator
 * included. The role decides what an account may do in the application
 * (review uploads, manage roles); it never widens a personal data view.
 * A request for another account's shelf is refused with 403 by the
 * ownership check below, not by a role list.
 */
class ProfileController extends Controller
{
    /** The signed-in account's own shelf. */
    public function shelf(Request $request)
    {
        return $this->renderShelf($request->user());
    }

    /**
     * The shelf at a named user URL, e.g. /shelf/1.
     *
     * Ownership is compared against the authenticated user on the server.
     * Anything else - including an administrator - gets 403.
     */
    public function show(Request $request, User $user)
    {
        if ($request->user()->id !== $user->id) {
            abort(403, 'A shelf belongs to the account that owns it.');
        }

        return $this->renderShelf($user);
    }

    private function renderShelf(User $user)
    {
        $items = Resource::with(['department:id,name'])
            ->where(fn ($q) => $q->where('user_id', $user->id)
                ->orWhereHas('downloads', fn ($d) => $d->where('user_id', $user->id)))
            ->latest()
            ->get();

        return view('shelf', [
            'user' => $user,
            'items' => $items,
            'badge' => $user->badge(),
            'reputation' => $user->reputation(),
        ]);
    }
}
