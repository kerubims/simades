@extends('layouts.app')
@section('title', 'Data Pelanggan')

@section('content')
<div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-on-surface">Data Pelanggan</h1>
        <p class="text-on-surface-variant">Kelola warga penerima layanan air bersih desa.</p>
    </div>
    <a href="{{ route('admin.pelanggan.create') }}" class="bg-primary text-on-primary font-bold py-2 px-4 rounded-lg flex items-center gap-2 hover:opacity-90">
        <span class="material-symbols-outlined">person_add</span> Tambah Pelanggan
    </a>
</div>

<div class="mb-4">
    <div class="relative max-w-sm">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
        <input type="text" id="search-pelanggan" placeholder="Cari nama, NIK, atau no. pelanggan..."
            class="w-full pl-10 pr-4 py-2 rounded-xl border border-outline-variant/40 bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
    </div>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/30">
                    <th class="p-4 font-semibold text-on-surface-variant">No. Pelanggan</th>
                    <th class="p-4 font-semibold text-on-surface-variant">NIK / Nama Lengkap</th>
                    <th class="p-4 font-semibold text-on-surface-variant">RT/RW</th>
                    <th class="p-4 font-semibold text-on-surface-variant">No. WhatsApp</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Status</th>
                    <th class="p-4 font-semibold text-on-surface-variant text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tbl-pelanggan">
                @forelse($pelangganList as $p)
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-lowest">
                        <td class="p-4 font-medium text-primary">{{ $p->idPelanggan }}</td>
                        <td class="p-4">
                            <div class="font-bold">{{ $p->namaLengkap }}</div>
                            <div class="text-xs text-on-surface-variant">{{ $p->nik }}</div>
                        </td>
                        <td class="p-4">RT {{ $p->rt }} / RW {{ $p->rw }}</td>
                        <td class="p-4">{{ $p->getWhatsappLokal() }}</td>
                        <td class="p-4">
                            @if($p->isAktif())
                                <span class="px-2 py-1 bg-[#dcfce7] text-[#166534] text-xs font-bold rounded-full">Aktif</span>
                            @else
                                <span class="px-2 py-1 bg-[#f1f5f9] text-[#475569] text-xs font-bold rounded-full">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('admin.pelanggan.edit', $p->idPelanggan) }}" class="text-secondary hover:text-secondary-container" title="Edit">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                @if($p->isAktif())
                                <form action="{{ route('admin.pelanggan.destroy', $p->idPelanggan) }}" method="POST"
                                      onsubmit="return confirm('⚠️ HAPUS AKUN\n\nAkun user {{ addslashes($p->namaLengkap) }} akan dihapus permanen.\nPelanggan tidak dapat login lagi dan harus registrasi ulang.\n\nData tagihan & riwayat tetap tersimpan.\n\nLanjutkan?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-error hover:text-on-error-container" title="Hapus Akun User">
                                        <span class="material-symbols-outlined">delete_forever</span>
                                    </button>
                                </form>
                                @else
                                <span class="text-on-surface-variant/40 cursor-not-allowed" title="Akun sudah dihapus">
                                    <span class="material-symbols-outlined">no_accounts</span>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-on-surface-variant">Belum ada data pelanggan.</td>
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
    new TablePagination('tbl-pelanggan', 'search-pelanggan');
});
</script>
@endsection
