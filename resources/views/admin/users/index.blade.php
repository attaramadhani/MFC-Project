@extends('layouts.admin')

@php
    $pageTitle = 'Kelola Pengguna';
@endphp

@section('content')

<div class="mb-3">
    <h2 class="h5 mb-1">Kelola Pengguna</h2>
    <div class="text-muted small">
        Ubah role pengguna dan reset password.
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>{{ $u->id_user }}</td>

                            <td>{{ $u->nama_user }}</td>

                            <td>
                                @if($u->role == 'admin')
                                    <span class="badge bg-warning text-dark">Admin</span>
                                @else
                                    <span class="badge bg-info text-dark">Pelanggan</span>
                                @endif
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($u->dibuat_pada)->format('d M Y') }}
                            </td>

                            <td class="text-end">

                                <form 
                                    method="POST"
                                    action="{{ route('admin.users.role', $u->id_user) }}"
                                    class="d-inline"
                                >
                                    @csrf

                                    <select 
                                        name="role"
                                        class="form-select form-select-sm d-inline w-auto"
                                        onchange="this.form.submit()"
                                    >
                                        <option value="pelanggan" {{ $u->role == 'pelanggan' ? 'selected' : '' }}>
                                            Pelanggan
                                        </option>

                                        <option value="admin" {{ $u->role == 'admin' ? 'selected' : '' }}>
                                            Admin
                                        </option>
                                    </select>

                                </form>

                                <form
                                    method="POST"
                                    action="{{ route('admin.users.reset', $u->id_user) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Reset password user ini ke 12345?')"
                                >
                                    @csrf

                                    <button class="btn btn-sm btn-outline-danger">
                                        Reset Password
                                    </button>

                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
</div>

@endsection