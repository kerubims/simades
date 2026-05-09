@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface">Dashboard Pengelola SIMADES</h1>
    <p class="text-on-surface-variant">Ringkasan operasional dan keuangan air desa.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-2">
            <span class="text-on-surface-variant font-medium">Total Pelanggan Aktif</span>
            <span class="material-symbols-outlined text-primary bg-primary-container/20 p-2 rounded-lg">group</span>
        </div>
        <h3 class="text-3xl font-bold text-on-surface">{{ $totalPelanggan }} <span class="text-sm font-normal text-on-surface-variant">KK</span></h3>
    </div>
    
    <div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-2">
            <span class="text-on-surface-variant font-medium">Air Terpakai Bulan Ini</span>
            <span class="material-symbols-outlined text-secondary bg-secondary-container/20 p-2 rounded-lg">water_drop</span>
        </div>
        <h3 class="text-3xl font-bold text-on-surface">{{ number_format($statistik['total_pemakaian_m3'], 0, ',', '.') }} <span class="text-sm font-normal text-on-surface-variant">m&sup3;</span></h3>
    </div>

    <div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-2">
            <span class="text-on-surface-variant font-medium">Pendapatan Masuk</span>
            <span class="material-symbols-outlined text-tertiary bg-tertiary-container/20 p-2 rounded-lg">payments</span>
        </div>
        <h3 class="text-2xl font-bold text-on-surface text-tertiary">Rp {{ number_format($statistik['total_pendapatan'], 0, ',', '.') }}</h3>
        <p class="text-xs text-on-surface-variant mt-1">{{ $statistik['sudah_bayar'] }} Tagihan Lunas</p>
    </div>

    <div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-2">
            <span class="text-on-surface-variant font-medium">Belum Dibayar</span>
            <span class="material-symbols-outlined text-error bg-error-container/20 p-2 rounded-lg">warning</span>
        </div>
        <h3 class="text-3xl font-bold text-error">{{ $statistik['belum_bayar'] }}</h3>
        <p class="text-xs text-on-surface-variant mt-1">Tagihan Tertunggak</p>
    </div>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 overflow-hidden">
    <div class="p-6 border-b border-outline-variant/20 flex justify-between items-center">
        <h2 class="font-bold text-on-surface">Tagihan Terbaru (Bulan Ini)</h2>
        <a href="{{ route('admin.tagihan.index') }}" class="text-primary font-medium text-sm hover:underline">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/30">
                    <th class="p-4 font-semibold text-on-surface-variant">ID Tagihan</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Pelanggan</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Pemakaian</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Total</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse(array_slice($tagihanBulanIni, 0, 5) as $t)
                    <tr class="border-b border-outline-variant/10">
                        <td class="p-4 font-medium text-primary">{{ $t->idTagihan }}</td>
                        <td class="p-4">{{ $t->idPelanggan }}</td>
                        <td class="p-4">{{ $t->totalPemakaianM3 }} m&sup3;</td>
                        <td class="p-4">Rp {{ number_format($t->totalTagihan, 0, ',', '.') }}</td>
                        <td class="p-4">
                            @if($t->isSudahLunas())
                                <span class="px-2 py-1 bg-[#dcfce7] text-[#166534] text-xs font-bold rounded-full">Lunas</span>
                            @else
                                <span class="px-2 py-1 bg-[#fef3c7] text-[#b45309] text-xs font-bold rounded-full">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-on-surface-variant">Belum ada tagihan dicatat bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
