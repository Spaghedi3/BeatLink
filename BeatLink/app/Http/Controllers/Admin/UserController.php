<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Show a paginated list of all users in the admin panel.
     */
    public function index(Request $request)
    {
        // Optionally, you can filter or search, e.g. by email/username:
        $query = User::query();

        if ($request->filled('search')) {
            $q = $request->input('search');
            $query->where('username', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%");
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show a single user’s details.
     */
    public function show(User $user)
    {
        $user->load('favoriteTracks');
        return view('admin.users.show', compact('user'));
    }

    public function ban(User $user)
    {
        if ($user->is_admin) {
            return redirect()->back()->with('error', 'Admins cannot be banned.');
        }

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot ban yourself.');
        }

        $user->update(['banned' => true]);
        return redirect()->back()->with('status', 'User has been banned.');
    }
}
