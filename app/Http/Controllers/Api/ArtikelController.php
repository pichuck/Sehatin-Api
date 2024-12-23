<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtikelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all artikel with penulis relationship
        $artikel = Artikel::with('user')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $artikel
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id', // Directly validating user_id
            'isi' => 'required|string',
            'category' => 'required|string',
            'foto' => 'required|file|image|max:2048',
        ]);
    
        // Handle the foto upload
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('artikel', 'public');
        }
    
        // Create the artikel
        $artikel = Artikel::create($validated);
    
        return response()->json([
            'message' => 'Artikel berhasil ditambahkan',
            'data' => $artikel,
        ]);
    }
    
    


    /**
     * Display the specified resource.
     */
    public function show(Artikel $artikel)
    {
        // Load penulis relationship
        $artikel->load('user');

        return response()->json([
            'message' => 'Success',
            'data' => $artikel
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Artikel $artikel)
    {
        // Validate request
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id', 
            'isi' => 'nullable|string',
            'category' => 'nullable|string',
            'foto' => 'nullable|file|image|max:2048',
        ]);
    
        // Handle the foto upload if exists
        if ($request->hasFile('foto')) {
            // Delete old file
            if ($artikel->foto && Storage::disk('public')->exists($artikel->foto)) {
                Storage::disk('public')->delete($artikel->foto);
            }
    
            // Store new file
            $validated['foto'] = $request->file('foto')->store('artikel', 'public');
        }
    
        // Update artikel
        $artikel->update($validated);
    
        return response()->json([
            'message' => 'Artikel berhasil diupdate',
            'data' => $artikel
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artikel $artikel)
    {
        // Delete file if exists
        if ($artikel->foto) {
            Storage::disk('public')->delete($artikel->foto);
        }

        // Delete artikel
        $artikel->delete();

        return response()->json([
            'message' => 'Artikel berhasil dihapus'
        ]);
    }
}