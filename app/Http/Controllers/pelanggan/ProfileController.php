<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = DB::table('users')->where('id_user', Auth::id())->first();
        abort_if(!$user, 404);

        return view('pelanggan.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = DB::table('users')->where('id_user', Auth::id())->first();
        abort_if(!$user, 404);

        $request->validate([
            'nama_user' => 'required|string|max:255|unique:users,nama_user,' . $user->id_user . ',id_user',
        ], [
            'nama_user.unique' => 'Username/Nama tersebut sudah digunakan oleh orang lain.',
        ]);

        DB::table('users')
            ->where('id_user', Auth::id())
            ->update([
                'nama_user' => $request->nama_user,
            ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = DB::table('users')->where('id_user', Auth::id())->first();
        abort_if(!$user, 404);

        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8',
            'password_konfirmasi' => 'required|same:password_baru',
        ], [
            'password_baru.min' => 'Password baru minimal 8 karakter.',
            'password_konfirmasi.same' => 'Konfirmasi password tidak sama.',
        ]);

        $passDb = $user->pass_user;

        // Check password - handle both old plaintext and hashed passwords
        $validOld = Hash::check($request->password_lama, $passDb) || ($request->password_lama === $passDb);

        if (!$validOld) {
            return back()->with('error', 'Password lama salah.');
        }

        DB::table('users')
            ->where('id_user', Auth::id())
            ->update([
                'pass_user' => Hash::make($request->password_baru),
            ]);

        return back()->with('success', 'Password berhasil diganti.');
    }
}
