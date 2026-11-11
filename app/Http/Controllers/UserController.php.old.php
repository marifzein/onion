<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');$cabangs = Cabang::where('is_active', true)->get();

        $users = User::with('cabang')
            ->when($search, function ($query,$search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "\%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users', 'cabangs', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:6|confirmed', // Harus sama dengan password_confirmation
            'role'                  => 'required|in:superadmin,owner,admin,kaprod,bahan,print,jahit,qc,finishing',
            'cabang_id'             => 'nullable|exists:cabangs,id',
            'is_active'             => 'nullable|boolean',
        ], [
            'password.confirmed'    => 'Konfirmasi password tidak cocok dengan password yang dimasukkan.',
            'password.min'          => 'Password minimal harus 6 karakter.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User divisi baru berhasil dibuat!');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'              => 'nullable|string|min:6|confirmed', // Jika diisi, wajib confirmed
            'role'                  => 'required|in:superadmin,owner,admin,kaprod,bahan,print,jahit,qc,finishing',
            'cabang_id'             => 'nullable|exists:cabangs,id',
            'is_active'             => 'nullable|boolean',
        ], [
            'password.confirmed'    => 'Konfirmasi password tidak cocok dengan password yang dimasukkan.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);

        return redirect()->route('users.index')->with('success', "Data user {$user->name} berhasil diperbarui!");
    }

    // public function destroy(User $user)
    // {
    //     if (auth()->id() === $user->id) {
    //         return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
    //     }

    //     $user->delete();
    //     return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    // }
}