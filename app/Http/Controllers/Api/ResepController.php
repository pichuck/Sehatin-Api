<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResepController extends Controller
{
    public function index()
    {
        $resep = Resep::with('user')->get();

        return response()->json([
            'message' => 'Success',
            'data' => $resep,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|exists:users,id',
            'isi' => 'sometimes|string',
            'category' => 'sometimes|string',
            'foto' => 'sometimes|file|image|max:2048',
        ]);
        

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $validated['foto'] = $request->file('foto')->store('resep', 'public');
        }

        $resep = Resep::create($validated);

        $resep->load('user');

        return response()->json([
            'message' => 'Resep berhasil ditambahkan',
            'data' => $resep,
        ]);
    }

    public function show(Resep $resep)
    {
        $resep->load('user');

        return response()->json([
            'message' => 'Success',
            'data' => $resep,
        ]);
    }

    public function update(Request $request, Resep $resep)
    {
        $validated = $request->validate([
            'judul' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|exists:users,id',
            'isi' => 'sometimes|string',
            'category' => 'sometimes|string',
            'foto' => 'sometimes|file|image|max:2048',
        ]);
        

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            if ($resep->foto && Storage::disk('public')->exists($resep->foto)) {
                Storage::disk('public')->delete($resep->foto);
            }

            $validated['foto'] = $request->file('foto')->store('resep', 'public');
        }

        $resep->update($validated);

        $resep->load('user');

        return response()->json([
            'message' => 'Resep berhasil diupdate',
            'data' => $resep,
        ]);
    }

    public function destroy(Resep $resep)
    {
        if ($resep->foto && Storage::disk('public')->exists($resep->foto)) {
            Storage::disk('public')->delete($resep->foto);
        }

        $resep->delete();

        return response()->json([
            'message' => 'Resep berhasil dihapus',
        ]);
    }
}