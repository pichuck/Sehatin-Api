<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        // Ambil semua data pengguna
        $users = User::all();

        // Kembalikan respon JSON
        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);
    }

    public function getUser(Request $request)
{
    $user = $request->user();

    return response()->json([
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
            'no_hp' => $user->no_hp,
            'foto' => $user->foto, // Pastikan ini sesuai dengan kolom di database
        ]
    ], 200);
}


    
    //public function register return 'register'
    public function register(Request $request)
{
    $fields = $request->validate([
        'name' => 'required|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'no_hp' => 'required',
        'jenis_kelamin' => 'required|in:laki-laki,perempuan',
        'foto' => 'nullable|file|image|max:2048'
    ]);

    if ($request->hasFile('foto')) {
        // Simpan file ke direktori storage/public/userProfile
        $filePath = $request->file('foto')->store('userProfile', 'public');
        // Tambahkan URL publik ke data pengguna
        $fields['foto'] = asset('storage/' . $filePath);
    }

    $fields['password'] = Hash::make($fields['password']); // Hash password sebelum disimpan

    $user = User::create($fields);

    $token = $user->createToken($request->name);

    return response()->json([
        'message' => 'Register Success',
        'user' => $user,
        'token' => $token->plainTextToken
    ]);
}


    public function login(Request $request)
{
    $fields = $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required'
    ]);

    $user = User::where('email', $fields['email'])->first();

    if (!$user || !Hash::check($fields['password'], $user->password)) {
        return response()->json(['message' => 'Login Failed'], 401);
    }

    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json([
        'message' => 'Login Successful',
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
            'no_hp' => $user->no_hp, 
        ],
        'token' => $token,
    ], 200);
}

    

    //public function logout return 'logout'
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout Success'
        ]);
    }
}