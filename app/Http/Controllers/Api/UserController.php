<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /users
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * POST /users
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'no_hp' => 'nullable|string|max:15',
            'jenis_kelamin' => 'nullable|string|in:laki-laki,perempuan',
            'foto' => 'nullable|file|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Simpan file ke direktori storage/public/userProfile
            $filePath = $request->file('foto')->store('userProfile', 'public');
            // Tambahkan URL publik ke data pengguna
            $fields['foto'] = asset('storage/' . $filePath);
        }

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     * GET /users/{id}
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * PUT /users/{id}
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'no_hp' => 'nullable|string|max:15',
            'jenis_kelamin' => 'nullable|string|in:laki-laki,perempuan',
            'foto' => 'nullable|file|image|max:2048',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Simpan file ke direktori storage/public/userProfile
            $filePath = $request->file('foto')->store('userProfile', 'public');
            // Tambahkan URL publik ke data pengguna
            $fields['foto'] = asset('storage/' . $filePath);
        }

        if ($request->has('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /users/{id}
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Delete foto if exists
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}