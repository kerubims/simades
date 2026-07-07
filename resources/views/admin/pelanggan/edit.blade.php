@extends('layouts.app')
@section('title', 'Edit Warga')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('admin.pelanggan.index') }}" class="text-on-surface-variant hover:text-primary"><span class="material-symbols-outlined">arrow_back</span></a>
        <h1 class="text-2xl font-bold text-on-surface">Edit Warga</h1>
    </div>
    <p class="text-on-surface-variant ml-8">Ubah data warga {{ $pelanggan->idPelanggan }} di Google Sheets.</p>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 p-6 max-w-2xl">
    <form action="{{ route('admin.pelanggan.update', $pelanggan->idPelanggan) }}" method="POST" class="flex flex-col gap-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold mb-1">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $pelanggan->namaLengkap) }}" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">NIK</label>
                <input type="text" name="nik" value="{{ old('nik', $pelanggan->nik) }}" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">No KK</label>
                <input type="text" name="no_kk" value="{{ old('no_kk', $pelanggan->noKk) }}" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">RT</label>
                <input type="text" name="rt" value="{{ old('rt', $pelanggan->rt) }}" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">RW</label>
                <input type="text" name="rw" value="{{ old('rw', $pelanggan->rw) }}" required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Nomor WhatsApp</label>
            <input type="text" name="no_whatsapp" value="{{ old('no_whatsapp', $pelanggan->getWhatsappLokal()) }}" placeholder="Contoh: 0812... atau 62812..." required class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
            <p class="text-xs text-on-surface-variant mt-1">Bisa diawali 08 atau 628 (Sistem otomatis mengubah ke format API 62)</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Status Aktif</label>
                <select name="status_aktif" class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary">
                    <option value="Aktif" {{ $pelanggan->isAktif() ? 'selected' : '' }}>Aktif</option>
                    <option value="Non-Aktif" {{ !$pelanggan->isAktif() ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Password (Opsional)</label>
                <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah" class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary" />
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.pelanggan.index') }}" class="px-6 py-2 border border-outline-variant rounded-lg font-bold text-on-surface hover:bg-surface-variant">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary text-on-primary rounded-lg font-bold hover:opacity-90">Update Data</button>
        </div>
    </form>
</div>
@endsection
