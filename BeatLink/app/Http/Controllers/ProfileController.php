<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    /** Show the “edit your profile” form */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /** Persist profile changes */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Delegate everything to the model:
        $user->updateFromProfile(
            $request->validated(),
            $request->file('profile_picture')
        );

        return redirect()
            ->route('profile.show', $user->username)
            ->with('status', 'profile-updated');
    }

    public function show(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        return view('profile.show', compact('user'));
    }

    /** Delete the current user */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Let the model handle cleanup + deletion:
        $user->deleteAccount();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
