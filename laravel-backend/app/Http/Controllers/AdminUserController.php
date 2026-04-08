<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|min:8',
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->back()->with('success', 'Password updated successfully for ' . $user->name);
    }

    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own admin account.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User ' . $user->name . ' has been deleted.');
    }
}
