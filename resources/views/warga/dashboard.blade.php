@extends('layouts.app')
@section('title', 'Dashboard Warga')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface">Halo, {{ $pelanggan->namaLengkap }}</h1>
    <p class="text-on-surface-variant">Nomor Pelanggan: <strong>{{ $pelanggan->idPelanggan }}</strong> | RT/RW: {{ $pelanggan->rt }}/{{ $pelanggan->rw }}</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Tagihan Bulan Ini -->
    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-outline-variant/20">
        <h2 class="text-lg font-bold text-on-surface flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-primary">receipt_long</span> Tagihan Bulan Ini
        </h2>

        @if($tagihanBulanIni)
            <div class="bg-surface-container p-4 rounded-xl mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-on-surface-variant">Periode</span>
                    <span class="font-bold text-on-surface">{{ $tagihanBulanIni->periodeLabel() }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-on-surface-variant">Pemakaian</span>
                    <span class="font-bold text-on-surface">{{ $tagihanBulanIni->totalPemakaianM3 }} m&sup3;</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-outline-variant/30">
                    <span class="font-bold text-on-surface">Total Tagihan</span>
                    <span class="font-bold text-primary text-xl">Rp {{ number_format($tagihanBulanIni->totalTagihan, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between p-3 rounded-lg {{ $tagihanBulanIni->isSudahLunas() ? 'bg-[#f0fdf4] text-[#166534] border border-[#bbf7d0]' : 'bg-[#fffbeb] text-[#b45309] border border-[#fde68a]' }}">
                    <span class="font-semibold flex items-center gap-2">
                        <span class="material-symbols-outlined">{{ $tagihanBulanIni->isSudahLunas() ? 'check_circle' : 'pending_actions' }}</span>
                        Status: {{ $tagihanBulanIni->statusBayar }}
                    </span>
                </div>
                
                <a href="{{ route('warga.slip.download', $tagihanBulanIni->idTagihan) }}" class="flex justify-center items-center gap-2 bg-secondary text-white py-3 rounded-lg hover:bg-secondary/90 font-bold transition-colors">
                    <span class="material-symbols-outlined">download</span> Download Slip PDF
                </a>
            </div>
        @else
            <div class="text-center py-8 text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl opacity-50 mb-2">inbox</span>
                <p>Belum ada tagihan untuk bulan ini.</p>
            </div>
        @endif
    </div>

    <!-- Informasi Tarif -->
    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-outline-variant/20">
        <h2 class="text-lg font-bold text-on-surface flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-tertiary">info</span> Informasi Tarif Saat Ini
        </h2>

        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-surface-container-low rounded-lg">
                <span class="text-on-surface-variant">Air per m&sup3;</span>
                <span class="font-bold">Rp {{ number_format($tarif->airPerM3, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-surface-container-low rounded-lg">
                <span class="text-on-surface-variant">Beban Sampah /Bulan</span>
                <span class="font-bold">Rp {{ number_format($tarif->bebanSampah, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-surface-container-low rounded-lg">
                <span class="text-on-surface-variant">Dana Kematian /Bulan</span>
                <span class="font-bold">Rp {{ number_format($tarif->danaKematian, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
