<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Daftar role yang diizinkan untuk dibuat/diedit oleh user yang sedang login.
     */
    private function getAllowedRoles(): array
    {
        $currentUserRole = auth()->user()->role ?? '';

        if ($currentUserRole === 'superadmin') {
            // Superadmin bisa mengelola SEMUA role
            return ['owner', 'superadmin', 'admin', 'kaprod', 'bahan', 'print', 'jahit', 'qc', 'finishing'];
        }

        if ($currentUserRole === 'owner') {
            // Owner bisa mengelola SEMUA role KECUALI superadmin
            return ['owner', 'admin', 'kaprod', 'bahan', 'print', 'jahit', 'qc', 'finishing'];
        }

        // Default jika role admin membuka menu user
        return ['admin', 'kaprod', 'bahan', 'print', 'jahit', 'qc', 'finishing'];
    }

    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $allowedRoles = $this->getAllowedRoles();
        $search = $request->input('search');
        $cabangs = Cabang::where('is_active', true)->get();

        // Filter: Jika role owner, kecualikan user dengan role 'superadmin'
        $users = User::with('cabang')
            ->when($currentUser->role === 'owner', function ($query) {
                return $query->where('role', '!=', 'superadmin');
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users', 'cabangs', 'search', 'allowedRoles'));
    }

    public function store(Request $request)
    {
        $allowedRoles = $this->getAllowedRoles();

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
            'role'                  => ['required', Rule::in($allowedRoles)], // Validasi hanya role yang diizinkan
            'cabang_id'             => 'nullable|exists:cabangs,id',
            'is_active'             => 'nullable|boolean',
        ], [
            'email.unique'          => 'Email login ini sudah terdaftar pada akun lain.',
            'role.in'               => 'Anda tidak memiliki hak akses untuk membuat user dengan role ini.',
            'password.min'          => 'Password minimal harus terdiri dari 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok dengan password yang dimasukkan.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Akun user divisi baru berhasil dibuat!');
    }

    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $allowedRoles = $this->getAllowedRoles();

        // Proteksi: Owner tidak boleh mengedit akun ber-role superadmin
        if ($currentUser->role === 'owner' && $user->role === 'superadmin') {
            abort(403, 'Anda tidak memiliki wewenang untuk mengedit akun Superadmin.');
        }

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'              => 'nullable|string|min:6|confirmed',
            'role'                  => ['required', Rule::in($allowedRoles)], // Validasi role target
            'cabang_id'             => 'nullable|exists:cabangs,id',
            'is_active'             => 'nullable|boolean',
        ], [
            'email.unique'          => 'Email login ini sudah digunakan oleh akun lain.',
            'role.in'               => 'Anda tidak memiliki hak akses untuk menetapkan role ini.',
            'password.min'          => 'Password baru minimal harus terdiri dari 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
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

    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        // Proteksi: Owner tidak boleh menghapus akun superadmin
        if ($currentUser->role === 'owner' && $user->role === 'superadmin') {
            abort(403, 'Anda tidak memiliki wewenang untuk menghapus akun Superadmin.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}