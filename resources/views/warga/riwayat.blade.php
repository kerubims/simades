@extends('layouts.app')
@section('title', 'Riwayat Tagihan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface">Riwayat Tagihan & Pemakaian</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-surface rounded-2xl shadow-sm border border-outline-variant/20 overflow-hidden">
        <div class="p-6 border-b border-outline-variant/20">
            <h2 class="font-bold text-on-surface">Daftar Tagihan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant/30">
                        <th class="p-4 font-semibold text-on-surface-variant">Periode</th>
                        <th class="p-4 font-semibold text-on-surface-variant">Pemakaian</th>
                        <th class="p-4 font-semibold text-on-surface-variant">Total Tagihan</th>
                        <th class="p-4 font-semibold text-on-surface-variant">Status</th>
                        <th class="p-4 font-semibold text-on-surface-variant text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $t)
                        <tr class="border-b border-outline-variant/10 hover:bg-surface-container-lowest transition-colors">
                            <td class="p-4">{{ $t->periodeLabel() }}</td>
                            <td class="p-4">{{ $t->totalPemakaianM3 }} m&sup3;</td>
                            <td class="p-4 font-medium">Rp {{ number_format($t->totalTagihan, 0, ',', '.') }}</td>
                            <td class="p-4">
                                @if($t->isSudahLunas())
                                    <span class="px-2 py-1 bg-[#dcfce7] text-[#166534] text-xs font-bold rounded-full">Lunas</span>
                                @else
                                    <span class="px-2 py-1 bg-[#fef3c7] text-[#b45309] text-xs font-bold rounded-full">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('warga.slip.download', $t->idTagihan) }}" class="text-primary hover:text-primary-container" title="Download Slip">
                                    <span class="material-symbols-outlined">download</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-on-surface-variant">Belum ada riwayat tagihan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-outline-variant/20 h-fit">
        <h2 class="font-bold text-on-surface mb-4">Grafik 6 Bulan Terakhir</h2>
        
        <div class="flex flex-col gap-4">
            @php
                $pemakaianArray = array_column($chartData, 'pemakaian');
                $max = count($pemakaianArray) > 0 ? max($pemakaianArray) : 1;
                if ($max == 0) $max = 1; // Mencegah pembagian dengan nol
            @endphp
            @foreach($chartData as $data)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-on-surface-variant">{{ $data['bulan'] }}</span>
                        <span class="font-bold">{{ $data['pemakaian'] }} m&sup3;</span>
                    </div>
                    <div class="w-full bg-surface-container-high rounded-full h-2">
                        @php
                            $pct = ($data['pemakaian'] / $max) * 100;
                        @endphp
                        <div class="bg-primary h-2 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
