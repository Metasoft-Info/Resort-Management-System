<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ResortInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        $resortInfo = ResortInfo::first();
        return response()->view('admin.login', compact('resortInfo'))
            ->header('Cache-Control', 'no-store, private, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function sessionToken(Request $request)
    {
        return response()->json(['token' => $request->session()->token()])
            ->header('Cache-Control', 'no-store, private, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            
            ActivityLog::log('User logged in', 'User', Auth::id());
            
            $request->session()->forget('url.intended');
            if ($request->expectsJson()) {
                return response()->json(['redirect' => route('admin.dashboard', [], false)])
                    ->header('Cache-Control', 'no-store, private');
            }
            return redirect()->route('admin.dashboard');
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'The provided credentials do not match our records.'], 422);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        ActivityLog::log('User logged out', 'User', $userId);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $validated['name'];

        if ($validated['new_password']) {
            if (!$validated['current_password'] || !Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        ActivityLog::log('Updated profile', 'User', $user->id, ['name' => $user->name]);

        return back()->with('success', 'Profile updated successfully!');
    }
}
