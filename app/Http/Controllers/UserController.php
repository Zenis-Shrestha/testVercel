<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            // 'role'=>''
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'age' => $validated['age'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json($user, 201);
    }

    public function show(string $id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0|max:150',
            'email' => ['required','email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->age = $validated['age'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json($user);
    }

    public function destroy(string $id)
    {
        User::findOrFail($id)->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
