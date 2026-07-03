@extends('layouts.app')
@section('title', 'Tambah Pelanggan')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('admin.pelanggan.index') }}" class="text-on-surface-variant hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
        <h1 class="text-2xl font-bold text-on-surface">Tambah Pelanggan Baru</h1>
    </div>
    <p class="text-on-surface-variant ml-8">Data akan ditambahkan ke Google Sheets.</p>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 p-6 max-w-2xl">
    <form action="{{ route('admin.pelanggan.store') }}" method="POST" class="flex flex-col gap-5">
        @csrf

        <h3 class="font-bold border-b border-outline-variant/30 pb-2 text-primary">Data Diri Warga</h3>
        
        <div>
            <label class="block text-sm font-semibold mb-1">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">NIK</label>
                <input type="text" name="nik" value="{{ old('nik') }}" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">No KK</label>
                <input type="text" name="no_kk" value="{{ old('no_kk') }}" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">RT</label>
                <input type="text" name="rt" value="{{ old('rt') }}" placeholder="Contoh: 01" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">RW</label>
                <input type="text" name="rw" value="{{ old('rw') }}" placeholder="Contoh: 02" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Nomor WhatsApp</label>
            <input type="text" name="no_whatsapp" value="{{ old('no_whatsapp') }}" placeholder="Contoh: 0812..." required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
        </div>

        <h3 class="font-bold border-b border-outline-variant/30 pb-2 text-primary mt-4">Akun Login Warga</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Username Login</label>
                <input type="text" name="username" value="{{ old('username') }}" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Password</label>
                <input type="text" name="password" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.pelanggan.index') }}" class="px-6 py-2 border border-outline-variant rounded-lg font-bold text-on-surface hover:bg-surface-variant">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary text-on-primary rounded-lg font-bold hover:opacity-90">Simpan Data</button>
        </div>
    </form>
</div>
@endsection
