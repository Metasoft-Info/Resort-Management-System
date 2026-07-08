<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminMenuSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    public function index() {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }
    public function create() { 
        $menuSettings = AdminMenuSetting::where('is_active', true)->orderBy('order')->get();
        return view('admin.users.create', compact('menuSettings')); 
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|string',
            'permissions' => 'nullable|array',
        ]);
        // Password will be auto-hashed by model cast
        $validated['permissions'] = $request->input('permissions', []);
        $validated['is_active'] = $request->has('is_active');
        User::create($validated);
        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }
    public function edit(User $user) {
        $menuSettings = AdminMenuSetting::where('is_active', true)->orderBy('order')->get();
        return view('admin.users.edit', compact('user', 'menuSettings'));
    }
    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|string',
            'permissions' => 'nullable|array',
        ]);
        if($request->filled('password')) {
            $validated['password'] = $request->password; // Auto-hashed by model cast
        }
        $validated['permissions'] = $request->input('permissions', []);
        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }
    public function destroy(User $user) {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself!');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }
}