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
                    <span class="text-on-surface-variant">Meter Awal</span>
                    <span class="font-semibold text-on-surface">{{ $tagihanBulanIni->meterAwal }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-on-surface-variant">Meter Akhir</span>
                    <span class="font-semibold text-on-surface">{{ $tagihanBulanIni->meterAkhir }}</span>
                </div>
                <div class="flex justify-between items-center mb-3 pb-3 border-b border-outline-variant/30">
                    <span class="text-on-surface-variant font-semibold">Total Pemakaian</span>
                    <span class="font-bold text-on-surface">{{ $tagihanBulanIni->totalPemakaianM3 }} m&sup3;</span>
                </div>

                {{-- Rincian Biaya --}}
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-2">Rincian Biaya</p>
                @php
                    $biayaAir = $tagihanBulanIni->totalPemakaianM3 * $tarif->airPerM3;
                @endphp
                <div class="flex justify-between items-center mb-1 text-sm">
                    <span class="text-on-surface-variant">Biaya Air ({{ $tagihanBulanIni->totalPemakaianM3 }} m³ × Rp {{ number_format($tarif->airPerM3, 0, ',', '.') }})</span>
                    <span class="font-semibold text-on-surface">Rp {{ number_format($biayaAir, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-1 text-sm">
                    <span class="text-on-surface-variant">Beban Sampah</span>
                    <span class="font-semibold text-on-surface">Rp {{ number_format($tarif->bebanSampah, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-3 text-sm">
                    <span class="text-on-surface-variant">Dana Sosial / Kematian</span>
                    <span class="font-semibold text-on-surface">Rp {{ number_format($tarif->danaKematian, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center pt-3 border-t-2 border-primary/30">
                    <span class="font-bold text-on-surface">Total Tagihan</span>
                    <span class="font-bold text-primary text-xl">Rp {{ number_format($tagihanBulanIni->totalTagihan, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between p-3 rounded-lg {{ $tagihanBulanIni->isSudahLunas() ? 'bg-[#f0fdf4] text-[#166534] border border-[#bbf7d0]' : ($tagihanBulanIni->isMenungguKonfirmasi() ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-[#fffbeb] text-[#b45309] border border-[#fde68a]') }}">
                    <span class="font-semibold flex items-center gap-2">
                        <span class="material-symbols-outlined">{{ $tagihanBulanIni->isSudahLunas() ? 'check_circle' : ($tagihanBulanIni->isMenungguKonfirmasi() ? 'hourglass_empty' : 'pending_actions') }}</span>
                        Status: {{ $tagihanBulanIni->statusBayar }}
                    </span>
                </div>

                @if($tagihanBulanIni->alasanPenolakan && $tagihanBulanIni->statusBayar === 'Belum Bayar')
                    <div class="p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm">
                        <strong>Ditolak Admin:</strong> {{ $tagihanBulanIni->alasanPenolakan }}
                    </div>
                @endif

                @if($qrisUrl && $tagihanBulanIni->statusBayar === 'Belum Bayar')
                    <button type="button" onclick="openQrisModal()"
                        class="flex justify-center items-center gap-2 bg-primary text-white py-3 rounded-lg hover:bg-primary/90 font-bold transition-colors shadow-md shadow-primary/20">
                        <span class="material-symbols-outlined">qr_code_2</span> Bayar via QRIS
                    </button>
                    
                    <form action="{{ route('warga.tagihan.upload', $tagihanBulanIni->idTagihan) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2 mt-2 p-3 border border-outline-variant/30 rounded-xl bg-surface-container">
                        @csrf
                        <label class="text-sm font-semibold text-on-surface">Upload Bukti Pembayaran</label>
                        <input type="file" name="bukti" accept="image/*,.pdf" required class="block w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                        <button type="submit" class="mt-2 bg-primary text-white py-2 rounded-lg font-bold text-sm">Kirim Bukti Pembayaran</button>
                    </form>
                @endif
                
                @if($tagihanBulanIni->isMenungguKonfirmasi() && $tagihanBulanIni->buktiPembayaran)
                    <a href="{{ Storage::url($tagihanBulanIni->buktiPembayaran) }}" target="_blank" class="flex justify-center items-center gap-2 bg-blue-100 text-blue-700 py-3 rounded-lg hover:bg-blue-200 font-bold transition-colors">
                        <span class="material-symbols-outlined">visibility</span> Lihat Bukti yang Diunggah
                    </a>
                @endif
                
                <a href="{{ route('warga.slip.download', $tagihanBulanIni->idTagihan) }}" target="_blank" class="flex justify-center items-center gap-2 bg-secondary text-white py-3 rounded-lg hover:bg-secondary/90 font-bold transition-colors">
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

{{-- ===== MODAL QRIS PEMBAYARAN ===== --}}
@if($qrisUrl)
<div id="qris-modal"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
     aria-modal="true" role="dialog" aria-labelledby="qris-modal-title">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
         onclick="closeQrisModal()"></div>

    {{-- Panel --}}
    <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-sm mx-auto z-10 overflow-hidden">
        {{-- Header --}}
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 id="qris-modal-title" class="text-on-primary font-bold text-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-[22px]">qr_code_2</span>
                QRIS Pembayaran
            </h3>
            <button onclick="closeQrisModal()"
                    class="text-on-primary/70 hover:text-on-primary transition-colors rounded-lg p-1"
                    aria-label="Tutup">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- QRIS Image --}}
        <div class="p-6">
            <div class="bg-white rounded-xl p-4 border border-outline-variant/20 flex justify-center">
                <img src="{{ $qrisUrl }}" alt="QRIS Pembayaran" class="max-w-full max-h-80 object-contain">
            </div>
            <p class="text-center text-sm text-on-surface-variant mt-4">Scan QR code di atas untuk melakukan pembayaran tagihan air.</p>
        </div>

        {{-- Close --}}
        <div class="px-6 pb-6">
            <button onclick="closeQrisModal()"
                    class="w-full py-2.5 rounded-xl border border-outline-variant/40 text-on-surface-variant text-sm font-semibold hover:bg-surface-container transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    function openQrisModal() {
        const modal = document.getElementById('qris-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeQrisModal() {
        const modal = document.getElementById('qris-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeQrisModal();
    });
</script>
@endsection
