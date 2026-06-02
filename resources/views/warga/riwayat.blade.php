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
                        @php
                            $tagihanJson = json_encode([
                                'idTagihan'        => $t->idTagihan,
                                'periode'          => $t->periodeLabel(),
                                'meterAwal'        => $t->meterAwal,
                                'meterAkhir'       => $t->meterAkhir,
                                'pemakaian'        => $t->totalPemakaianM3,
                                'totalTagihan'     => $t->totalTagihan,
                                'statusBayar'      => $t->statusBayar,
                                'sudahLunas'       => $t->isSudahLunas(),
                                'airPerM3'         => $tarif?->airPerM3 ?? 0,
                                'bebanSampah'      => $tarif?->bebanSampah ?? 0,
                                'danaKematian'     => $tarif?->danaKematian ?? 0,
                            ]);
                        @endphp
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
                                <button
                                    type="button"
                                    onclick="openSlipModal({{ $tagihanJson }})"
                                    class="inline-flex items-center gap-1 text-primary hover:text-primary-container font-semibold text-sm transition-colors"
                                    title="Lihat Rincian">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    <span class="hidden sm:inline">Lihat</span>
                                </button>
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

{{-- ===== MODAL SLIP DETAIL ===== --}}
<div id="slip-modal"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
     aria-modal="true" role="dialog" aria-labelledby="modal-title">

    {{-- Backdrop --}}
    <div id="slip-modal-backdrop"
         class="absolute inset-0 bg-black/40 backdrop-blur-sm"
         onclick="closeSlipModal()"></div>

    {{-- Panel --}}
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-md mx-auto z-10 overflow-hidden">

        {{-- Header --}}
        <div class="bg-primary px-6 py-5 flex items-center justify-between">
            <div>
                <h3 id="modal-title" class="text-on-primary font-bold text-lg">Rincian Tagihan Air</h3>
                <p id="modal-periode" class="text-on-primary/80 text-sm mt-0.5"></p>
            </div>
            <button onclick="closeSlipModal()"
                    class="text-on-primary/70 hover:text-on-primary transition-colors rounded-lg p-1"
                    aria-label="Tutup">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Status badge --}}
        <div class="px-6 pt-5 pb-2 flex items-center justify-between">
            <span class="text-sm font-semibold text-on-surface-variant">No. Tagihan</span>
            <span id="modal-id-tagihan" class="text-xs font-mono text-on-surface-variant bg-surface-container px-2 py-1 rounded"></span>
        </div>

        {{-- Status --}}
        <div class="px-6 pb-4">
            <div id="modal-status-lunas"
                 class="hidden w-full text-center py-2 rounded-xl bg-[#dcfce7] text-[#166534] font-bold text-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">verified</span> Sudah Lunas
            </div>
            <div id="modal-status-belum"
                 class="hidden w-full text-center py-2 rounded-xl bg-[#fef3c7] text-[#b45309] font-bold text-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">pending</span> Belum Bayar
            </div>
        </div>

        {{-- Meter section --}}
        <div class="mx-6 mb-4 bg-surface-container-low rounded-xl p-4 border border-outline-variant/20">
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-3">Pemakaian Air</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Meter Awal</span>
                    <span id="modal-meter-awal" class="font-semibold text-on-surface font-mono"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Meter Akhir</span>
                    <span id="modal-meter-akhir" class="font-semibold text-on-surface font-mono"></span>
                </div>
                <div class="flex justify-between border-t border-outline-variant/20 pt-2 mt-2">
                    <span class="font-bold text-on-surface">Total Pemakaian</span>
                    <span id="modal-pemakaian" class="font-bold text-primary"></span>
                </div>
            </div>
        </div>

        {{-- Biaya section --}}
        <div class="mx-6 mb-5 bg-surface-container-low rounded-xl p-4 border border-outline-variant/20">
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-3">Rincian Biaya</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span id="modal-label-air" class="text-on-surface-variant">Biaya Air</span>
                    <span id="modal-biaya-air" class="font-semibold text-on-surface"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Beban Sampah</span>
                    <span id="modal-biaya-sampah" class="font-semibold text-on-surface"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Dana Sosial / Kematian</span>
                    <span id="modal-biaya-sosial" class="font-semibold text-on-surface"></span>
                </div>
                <div class="flex justify-between border-t border-outline-variant/20 pt-2 mt-2">
                    <span class="font-bold text-on-surface text-base">Total Bayar</span>
                    <span id="modal-total" class="font-bold text-primary text-base"></span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 pb-6">
            <button onclick="closeSlipModal()"
                    class="w-full py-2.5 rounded-xl border border-outline-variant/40 text-on-surface-variant text-sm font-semibold hover:bg-surface-container transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    function openSlipModal(data) {
        // Periode & ID
        document.getElementById('modal-periode').textContent = data.periode;
        document.getElementById('modal-id-tagihan').textContent = data.idTagihan;

        // Status
        document.getElementById('modal-status-lunas').classList.toggle('hidden', !data.sudahLunas);
        document.getElementById('modal-status-belum').classList.toggle('hidden', data.sudahLunas);

        // Meter
        document.getElementById('modal-meter-awal').textContent = data.meterAwal + ' m³';
        document.getElementById('modal-meter-akhir').textContent = data.meterAkhir + ' m³';
        document.getElementById('modal-pemakaian').textContent = data.pemakaian + ' m³';

        // Biaya
        const biayaAir = data.pemakaian * data.airPerM3;
        document.getElementById('modal-label-air').textContent =
            'Biaya Air (' + data.pemakaian + ' m³ × ' + formatRupiah(data.airPerM3) + ')';
        document.getElementById('modal-biaya-air').textContent = formatRupiah(biayaAir);
        document.getElementById('modal-biaya-sampah').textContent = formatRupiah(data.bebanSampah);
        document.getElementById('modal-biaya-sosial').textContent = formatRupiah(data.danaKematian);
        document.getElementById('modal-total').textContent = formatRupiah(data.totalTagihan);



        // Show modal
        const modal = document.getElementById('slip-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSlipModal() {
        document.getElementById('slip-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSlipModal();
    });
</script>
@endsection
