<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $admin = DB::table('users')->where('id_user', Auth::id())->first();
        abort_if(!$admin, 404);

        return view('admin.profile', compact('admin'));
    }

    public function updatePassword(Request $request)
    {
        $admin = DB::table('users')->where('id_user', Auth::id())->first();
        abort_if(!$admin, 404);

        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:3',
            'password_konfirmasi' => 'required|same:password_baru',
        ], [
            'password_konfirmasi.same' => 'Konfirmasi password tidak sama.',
        ]);

        $passDb = $admin->pass_user;

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