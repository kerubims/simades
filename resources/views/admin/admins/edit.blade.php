@extends('layouts.app')
@section('title', 'Edit Admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.admins.index') }}" class="text-secondary hover:text-secondary-container flex items-center gap-1 w-fit mb-2 font-medium">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali
    </a>
    <h1 class="text-2xl font-bold text-on-surface">Edit Admin</h1>
    <p class="text-on-surface-variant">Update profil atau ganti password akun administrator.</p>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 p-6 max-w-2xl">
    <form action="{{ route('admin.admins.update', $admin->idUser) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-4 mb-6">
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-semibold text-on-surface mb-1">Username <span class="text-error">*</span></label>
                <input type="text" id="username" name="username" value="{{ old('username', $admin->username) }}" required
                    class="w-full @error('username') border-error @enderror"
                    placeholder="Masukkan username (tanpa spasi)">
                @error('username')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-on-surface mb-1">Password Baru (Opsional)</label>
                <input type="password" id="password" name="password"
                    class="w-full @error('password') border-error @enderror"
                    placeholder="Kosongkan jika tidak ingin ganti password">
                <p class="text-xs text-on-surface-variant mt-1">Isi hanya jika Anda ingin mengubah password saat ini.</p>
                @error('password')
                    <p class="text-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 rounded-lg font-bold text-on-surface-variant hover:bg-surface-container-high transition-colors">
                Batal
            </a>
            <button type="submit" class="bg-primary text-on-primary px-4 py-2 rounded-lg font-bold hover:bg-primary/90 transition-colors shadow-sm">
                Update Admin
            </button>
        </div>
    </form>
</div>
@endsection
