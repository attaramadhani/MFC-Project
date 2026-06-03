<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = DB::table('users')
            ->select('id_user', 'nama_user', 'role', 'dibuat_pada')
            ->orderByDesc('dibuat_pada')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function changeRole($id)
    {
        $role = request('role', 'pelanggan');

        if (!in_array($role, ['admin', 'pelanggan'], true)) {
            $role = 'pelanggan';
        }

        if ((int) $id === (int) Auth::id() && $role !== 'admin') {
            return back()->with('error', 'Tidak bisa mengubah role akun yang sedang dipakai.');
        }

        DB::table('users')->where('id_user', $id)->update([
            'role' => $role,
        ]);

        return back()->with('success', 'Role pengguna diperbarui.');
    }

    public function resetPassword($id)
    {
        DB::table('users')->where('id_user', $id)->update([
            'pass_user' => Hash::make('12345'),
        ]);

        return back()->with('success', 'Password direset ke "12345".');
    }
}