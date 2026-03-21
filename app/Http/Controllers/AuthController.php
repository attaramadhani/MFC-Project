<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        $request->validate([
            'nama_pengguna' => 'required|string|max:255|unique:users,nama_user',
            'kata_sandi' => 'required|min:3',
            'konfirmasi' => 'required|same:kata_sandi',
        ], [
            'nama_pengguna.required' => 'Username dan password wajib diisi.',
            'nama_pengguna.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
            'konfirmasi.same' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'nama_user' => $request->nama_pengguna,
            'pass_user' => Hash::make($request->kata_sandi),
            'role' => 'pelanggan',
        ]);

        Auth::login($user);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pendaftaran berhasil. Kamu sudah otomatis login.');
        }

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        $request->validate([
            'nama_pengguna' => 'required|string',
            'kata_sandi' => 'required|string',
        ]);

        $user = User::where('nama_user', $request->nama_pengguna)->first();

        if ($user) {
            $passDb = $user->pass_user;

            $valid = Hash::check($request->kata_sandi, $passDb) || ($request->kata_sandi === $passDb);

            if ($valid) {
                Auth::login($user);
                $request->session()->regenerate();

                return $this->redirectByRole($user->role);
            }
        }

        return back()
            ->withErrors(['login' => 'Username atau password salah.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function redirectByRole($role)
    {
        return $role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('pelanggan.index');
    }
}