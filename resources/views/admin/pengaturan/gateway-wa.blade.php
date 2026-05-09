@extends('layouts.app')
@section('title', 'Gateway WhatsApp')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface">Pengaturan Gateway WhatsApp</h1>
    <p class="text-on-surface-variant">Hubungkan nomor WhatsApp untuk pengiriman otomatis slip tagihan ke warga.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 p-6 flex flex-col items-center justify-center min-h-[300px]">
        @if($status['connected'])
            <div class="w-24 h-24 bg-[#dcfce7] rounded-full flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#166534] text-5xl">check_circle</span>
            </div>
            <h2 class="text-xl font-bold text-on-surface mb-1">Terhubung</h2>
            <p class="text-on-surface-variant mb-6">Gateway WhatsApp aktif pada nomor: <strong>{{ $status['number'] ?? 'Tidak diketahui' }}</strong></p>
            
            <form action="{{ route('admin.pengaturan.logout') }}" method="POST" onsubmit="return confirm('Putuskan koneksi WhatsApp ini? Anda harus scan QR ulang.');">
                @csrf
                <button type="submit" class="bg-error text-on-error px-6 py-2 rounded-lg font-bold hover:bg-error-container hover:text-on-error-container transition-colors">
                    Putuskan Koneksi (Logout)
                </button>
            </form>
        @else
            <div class="text-center w-full" id="qr-container">
                <div class="w-24 h-24 bg-surface-container-high rounded-full flex items-center justify-center mb-4 mx-auto animate-pulse">
                    <span class="material-symbols-outlined text-outline text-4xl">qr_code_scanner</span>
                </div>
                <h2 class="text-lg font-bold text-on-surface mb-2">Menunggu QR Code...</h2>
                <p class="text-sm text-on-surface-variant mb-4">Pastikan server Node.js Baileys sedang berjalan.</p>
                <button onclick="fetchQr()" class="text-primary font-bold hover:underline">Muat Ulang QR</button>
            </div>
            
            <div class="text-center w-full hidden" id="qr-ready">
                <h2 class="text-lg font-bold text-on-surface mb-4">Scan QR Code di bawah</h2>
                <div class="bg-white p-4 rounded-xl border border-outline-variant/30 inline-block mb-4">
                    <img src="" id="qr-image" alt="WhatsApp QR Code" class="w-64 h-64 object-contain" />
                </div>
                <p class="text-sm text-on-surface-variant mb-4">Buka WhatsApp > Perangkat Tertaut > Tautkan Perangkat</p>
            </div>
        @endif
    </div>

    <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 p-6">
        <h3 class="font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">info</span> Status Server
        </h3>
        
        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-surface-container-lowest border border-outline-variant/20 rounded-lg">
                <span class="text-on-surface-variant">URL Gateway</span>
                <span class="font-mono text-sm">{{ config('services.whatsapp.gateway_url') }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-surface-container-lowest border border-outline-variant/20 rounded-lg">
                <span class="text-on-surface-variant">Koneksi Server</span>
                @if($status['message'] !== 'Gateway tidak dapat dihubungi')
                    <span class="px-2 py-1 bg-[#dcfce7] text-[#166534] text-xs font-bold rounded-full">Online</span>
                @else
                    <span class="px-2 py-1 bg-[#fef2f2] text-[#991b1b] text-xs font-bold rounded-full">Offline</span>
                @endif
            </div>
            <div class="flex justify-between items-center p-3 bg-surface-container-lowest border border-outline-variant/20 rounded-lg">
                <span class="text-on-surface-variant">Pesan Terakhir</span>
                <span class="text-sm font-medium">{{ $status['message'] }}</span>
            </div>
        </div>

        <div class="mt-6 p-4 bg-primary-container/10 rounded-xl text-sm text-on-surface">
            <strong>Catatan:</strong> Jika Gateway terputus atau error, jalankan ulang server Node.js di dalam folder <code>whatsapp-gateway</code> menggunakan perintah <code>npm start</code>.
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(!$status['connected'])
<script>
    async function fetchQr() {
        const container = document.getElementById('qr-container');
        const ready = document.getElementById('qr-ready');
        const img = document.getElementById('qr-image');

        try {
            const res = await fetch("{{ route('admin.pengaturan.qr') }}");
            const data = await res.json();

            if (data.qr) {
                img.src = data.qr;
                container.classList.add('hidden');
                ready.classList.remove('hidden');
                
                // Auto reload to check if connected
                setTimeout(() => window.location.reload(), 15000);
            } else if (data.message === 'Sudah terhubung dengan WhatsApp') {
                window.location.reload();
            }
        } catch (e) {
            console.error(e);
        }
    }

    // Coba ambil QR saat load
    document.addEventListener('DOMContentLoaded', fetchQr);
</script>
@endif
@endsection
