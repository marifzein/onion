<?php

namespace App\Http\Controllers;

use App\Models\Fabric;
use Illuminate\Http\Request;

class FabricController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $fabrics = Fabric::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('fabrics.index', compact('fabrics', 'search'));
    }

    public function create()
    {
        return view('fabrics.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:fabrics,name',
            'gramasi'     => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Fabric::create($validated);

        return redirect()->route('fabrics.index')->with('success', 'Bahan baru berhasil didaftarkan!');
    }

    public function edit(Fabric $fabric)
    {
        return view('fabrics.edit', compact('fabric'));
    }

    public function update(Request $request, Fabric $fabric)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:fabrics,name,' . $fabric->id,
            'gramasi'     => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $fabric->update($validated);

        return redirect()->route('fabrics.index')->with('success', 'Data bahan berhasil diperbarui!');
    }

    // public function destroy(Fabric $fabric)
    // {
    //     $fabric->delete();
    //     return redirect()->route('fabrics.index')->with('success', 'Data bahan berhasil dihapus!');
    // }

    // Endpoint JSON untuk dropdown search di form Input SO
    public function searchApi(Request $request)
    {
        $q = $request->input('q', '');
        $items = Fabric::where('is_active', true)
            ->when($q, function ($query, $q) {
                return $query->where('name', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'gramasi']);

        return response()->json($items);
    }
}