<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }


    /**
     * Login user
     */
    public function login(Request $request)
    {

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $token = $user->createToken('web')->plainTextToken;
        // ✅ add this after successful login
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => $user->name . ' logged in',
            'ip_address' => $request->ip(),
        ]);
        return response()->json([
            'message' => 'Logged in successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
         ActivityLog::create([
        'user_id'     => auth()->id(),
        'action'      => 'logout',
        'description' => auth()->user()->name . ' logged out',
        'ip_address'  => $request->ip(),
    ]);
        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }


    public function about()
    {
        return response()->json([
            'message' => 'Tthis is jjust a littlke test.',
        ]);
    }

    public function users()
    {
        // Return all users (you can limit fields to be safe)
        return response()->json(
            User::select('id', 'name', 'email', 'created_at')->orderBy('id', 'desc')->get()
        );
    }
}
