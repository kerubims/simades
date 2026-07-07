@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
<div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-on-surface">Manajemen User</h1>
        <p class="text-on-surface-variant">Kelola akun warga dan administrator sistem.</p>
    </div>
    <a href="{{ route('admin.admins.create') }}" class="bg-primary text-on-primary font-bold py-2 px-4 rounded-lg flex items-center gap-2 hover:opacity-90">
        <span class="material-symbols-outlined">person_add</span> Tambah Admin
    </a>
</div>

<!-- Tabs -->
<div class="flex border-b border-outline-variant/30 mb-6">
    <a href="{{ route('admin.pelanggan.index') }}" class="px-6 py-3 font-semibold text-on-surface-variant hover:text-primary hover:bg-surface-container-lowest border-b-2 border-transparent transition-colors">
        Data Warga
    </a>
    <a href="{{ route('admin.admins.index') }}" class="px-6 py-3 font-semibold text-primary border-b-2 border-primary bg-surface-container-lowest transition-colors">
        Data Admin
    </a>
</div>

<div class="mb-4">
    <div class="relative max-w-sm">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
        <input type="text" id="search-admin" placeholder="Cari username admin..."
            class="w-full pl-10 pr-4 py-2 rounded-xl border border-outline-variant/40 bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
    </div>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/30">
                    <th class="p-4 font-semibold text-on-surface-variant">ID User</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Username</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Role</th>
                    <th class="p-4 font-semibold text-on-surface-variant text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tbl-admin">
                @forelse($admins as $a)
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-lowest">
                        <td class="p-4 font-medium text-primary">{{ $a->idUser }}</td>
                        <td class="p-4 font-bold">{{ $a->username }}
                            @if(auth()->user() && auth()->user()->id_user === $a->idUser)
                                <span class="ml-2 px-2 py-0.5 bg-primary/10 text-primary text-xs rounded-full">Anda</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 bg-[#dbeafe] text-[#1e40af] text-xs font-bold rounded-full uppercase">{{ $a->role }}</span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('admin.admins.edit', $a->idUser) }}" class="text-secondary hover:text-secondary-container" title="Edit Profil/Password">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                @if(!auth()->user() || auth()->user()->id_user !== $a->idUser)
                                <form action="{{ route('admin.admins.destroy', $a->idUser) }}" method="POST"
                                      onsubmit="return confirm('⚠️ HAPUS ADMIN\n\nAkun admin {{ addslashes($a->username) }} akan dihapus permanen.\nLanjutkan?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-error hover:text-on-error-container" title="Hapus Akun Admin">
                                        <span class="material-symbols-outlined">delete_forever</span>
                                    </button>
                                </form>
                                @else
                                <span class="text-on-surface-variant/40 cursor-not-allowed" title="Tidak bisa menghapus akun sendiri">
                                    <span class="material-symbols-outlined">no_accounts</span>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-on-surface-variant">Belum ada data admin.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    new TablePagination('tbl-admin', 'search-admin');
});
</script>
@endsection
