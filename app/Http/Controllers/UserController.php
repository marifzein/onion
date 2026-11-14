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
            return ['owner', 'superadmin', 'admin', 'kaprod', 'bahan', 'print', 'jahit', 'qc', 'finishing'];
        }

        if ($currentUserRole === 'owner') {
            return ['owner', 'admin', 'kaprod', 'bahan', 'print', 'jahit', 'qc', 'finishing'];
        }

        return ['admin', 'kaprod', 'bahan', 'print', 'jahit', 'qc', 'finishing'];
    }

    public function index(Request $request)
    {
        $currentUser = auth()->user();$allowedRoles = $this->getAllowedRoles();$search = $request->input('search');$cabangs = Cabang::where('is_active', true)->get();

        $users = User::with('cabang')
            ->when($currentUser->role === 'owner', function ($query) {
                return $query->where('role', '!=', 'superadmin');
            })
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

        return view('users.index', compact('users', 'cabangs', 'search', 'allowedRoles'));
    }

    public function store(Request $request)
    {
        $allowedRoles =$this->getAllowedRoles();

        $validated =$request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
            'role'                  => ['required', Rule::in($allowedRoles)],
            'cabang_id'             => 'nullable|exists:cabangs,id',
            'is_active'             => 'nullable|boolean',
            'avatar'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'email.unique'          => 'Email login ini sudah terdaftar pada akun lain.',
            'role.in'               => 'Anda tidak memiliki hak akses untuk membuat user dengan role ini.',
            'password.min'          => 'Password minimal harus terdiri dari 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok dengan password yang dimasukkan.',
            'avatar.image'          => 'File yang diunggah harus berupa gambar.',
            'avatar.mimes'          => 'Format avatar harus berjenis: jpeg, png, jpg.',
            'avatar.max'            => 'Ukuran file avatar maksimal 2MB.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] =$request->boolean('is_active', true);

        if ($request->hasFile('avatar')) {
            $file =$request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/avatars'),$filename);
            $validated['avatar'] = 'img/avatars/' .$filename;
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Akun user divisi baru berhasil dibuat!');
    }

    public function update(Request $request, User$user)
    {
        $currentUser = auth()->user();
        $allowedRoles =$this->getAllowedRoles();

        if ($currentUser->role === 'owner' &&$user->role === 'superadmin') {
            abort(403, 'Anda tidak memiliki wewenang untuk mengedit akun Superadmin.');
        }

        $validated =$request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'              => 'nullable|string|min:6|confirmed',
            'role'                  => ['required', Rule::in($allowedRoles)],
            'cabang_id'             => 'nullable|exists:cabangs,id',
            'is_active'             => 'nullable|boolean',
            'avatar'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'email.unique'          => 'Email login ini sudah digunakan oleh akun lain.',
            'role.in'               => 'Anda tidak memiliki hak akses untuk menetapkan role ini.',
            'password.min'          => 'Password baru minimal harus terdiri dari 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'avatar.image'          => 'File yang diunggah harus berupa gambar.',
            'avatar.mimes'          => 'Format avatar harus berjenis: jpeg, png, jpg.',
            'avatar.max'            => 'Ukuran file avatar maksimal 2MB.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }

            $file =$request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/avatars'),$filename);
            $validated['avatar'] = 'img/avatars/' .$filename;
        }

        $validated['is_active'] =$request->has('is_active');
        $user->update($validated);

        return redirect()->route('users.index')->with('success', "Data user {$user->name} berhasil diperbarui!");
    }

    // public function destroy(User $user)
    // {
    //     $currentUser = auth()->user();

    //     if ($currentUser->id ===$user->id) {
    //         return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
    //     }

    //     if ($currentUser->role === 'owner' &&$user->role === 'superadmin') {
    //         abort(403, 'Anda tidak memiliki wewenang untuk menghapus akun Superadmin.');
    //     }

    //     if ($user->avatar && file_exists(public_path($user->avatar))) {
    //         @unlink(public_path($user->avatar));
    //     }

    //     $user->delete();
    //     return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    // }

    public function showChangePasswordForm()
    {
        return view('users.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal harus terdiri dari 6 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password,$user->password)) {
            return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan salah.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('password.change')->with('success', 'Password akun Anda berhasil diperbarui!');
    }

    /**
     * Menampilkan halaman form ganti foto avatar user yang sedang login.
     */
    public function showChangeAvatarForm()
    {
        return view('users.change-avatar');
    }

    /**
     * Memproses update foto avatar user yang sedang login.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'avatar.required' => 'Pilih file gambar terlebih dahulu.',
            'avatar.image'    => 'File yang diunggah harus berupa gambar.',
            'avatar.mimes'    => 'Format foto harus berjenis: jpeg, png, jpg.',
            'avatar.max'      => 'Ukuran file foto maksimal 2MB.',
        ]);

        $user = auth()->user();

        // Hapus file avatar lama jika ada di folder public
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            @unlink(public_path($user->avatar));
        }

        // Upload file avatar baru
        $file = $request->file('avatar');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('img/avatars'), $filename);
        
        $user->update([
            'avatar' => 'img/avatars/' . $filename,
        ]);

        return redirect()->route('avatar.change')->with('success', 'Foto profil (avatar) Anda berhasil diperbarui!');
    }
}