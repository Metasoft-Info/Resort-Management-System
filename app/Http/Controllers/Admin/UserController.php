<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    public function index() {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }
    public function create() { return view('admin.users.create'); }
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|string',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return redirect()->route('admin.users.index')->with('success', 'Created');
    }
    public function edit(User $user) {
        return view('admin.users.edit', compact('user'));
    }
    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|string',
        ]);
        if($request->password) $validated['password'] = Hash::make($request->password);
        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'Updated');
    }
    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Deleted');
    }
}