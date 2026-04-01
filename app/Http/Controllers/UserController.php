<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivity;

class UserController extends Controller
{
    use LogsActivity;
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'age' => 'required|integer|min:0|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'display_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $path = null;

        if ($request->hasFile('display_image')) {
            $path = $request->file('display_image')->store('display_images', 'public'); // display_images/xxx.png
        }
        $user = User::create([
            'name' => $validated['name'],
            //    'age' => $validated['age'],
            'email' => $validated['email'],
            'display_image' => $path,
            'password' => Hash::make($validated['password']),
        ]);
        $this->logActivity(
            action: 'created',
            description: auth()->user()->name . ' created user ' . $user->name,
            model: 'User',
            modelId: $user->id
        );

        return response()->json($user, 201);
    }

    public function show(string $id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'display_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('display_image')) {
            // delete old
            if ($user->display_image) {
                Storage::disk('public')->delete($user->display_image);
            }

            $user->display_image = $request->file('display_image')->store('display_images', 'public');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();
        $user->save();

        // ✅ add this
        $this->logActivity(
            action: 'updated',
            description: auth()->user()->name . ' updated user ' . $user->name,
            model: 'User',
            modelId: $user->id
        );

        return response()->json($user);


    }
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $this->logActivity(
            action: 'deleted',
            description: auth()->user()->name . ' deleted user with id ' . $id,
            model: 'User',
            modelId: $id
        );
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
