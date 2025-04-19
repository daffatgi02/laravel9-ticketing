<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:hc');
    }

    public function index()
    {
        $users = User::with('department')->get();
        return response()->json(['users' => $users]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,it,ga,hc',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'active' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        return response()->json(['user' => $user, 'message' => 'User created successfully']);
    }

    public function show(User $user)
    {
        $user->load('department');
        return response()->json(['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,it,ga,hc',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'active' => 'boolean'
        ]);

        // Only update password if it's provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        return response()->json(['user' => $user, 'message' => 'User updated successfully']);
    }

    public function destroy(User $user)
    {
        // Check if user has associated tickets
        if ($user->tickets()->count() > 0 || $user->assignedTickets()->count() > 0) {
            return response()->json(['message' => 'Cannot delete user with associated tickets'], 422);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
