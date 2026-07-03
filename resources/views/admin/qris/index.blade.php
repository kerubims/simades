@extends('layouts.app')
@section('title', 'QRIS Pembayaran')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface">QRIS Pembayaran</h1>
    <p class="text-on-surface-variant">Upload gambar QRIS yang akan ditampilkan ke warga untuk pembayaran tagihan air.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Upload Form --}}
    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-outline-variant/20">
        <h2 class="font-bold text-on-surface flex items-center gap-2 mb-6">
            <span class="material-symbols-outlined text-primary">upload</span>
            Upload QRIS Baru
        </h2>

        <form action="{{ route('admin.qris.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label for="qris_image" class="block text-sm font-semibold text-on-surface">Pilih Gambar QRIS</label>
                <div class="relative">
                    <input type="file" id="qris_image" name="qris_image" accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-on-surface-variant
                            file:mr-4 file:py-2.5 file:px-4
                            file:rounded-xl file:border-0
                            file:text-sm file:font-semibold
                            file:bg-primary/10 file:text-primary
                            hover:file:bg-primary/20
                            file:cursor-pointer file:transition-colors
                            cursor-pointer"
                        onchange="previewQrisImage(event)" required>
                </div>
                <p class="text-xs text-on-surface-variant">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
                @error('qris_image')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Preview gambar yang akan diupload --}}
            <div id="preview-container" class="space-y-2 hidden">
                <p class="text-sm font-semibold text-on-surface">Preview:</p>
                <div class="rounded-xl border border-outline-variant/30 overflow-hidden bg-white p-4 flex justify-center">
                    <img id="preview-image" src="" alt="Preview QRIS" class="max-w-full max-h-72 object-contain rounded-lg">
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[20px]">cloud_upload</span>
                Upload QRIS
            </button>
        </form>
    </div>

    {{-- Current QRIS Preview --}}
    <div class="bg-surface rounded-2xl p-6 shadow-sm border border-outline-variant/20">
        <h2 class="font-bold text-on-surface flex items-center gap-2 mb-6">
            <span class="material-symbols-outlined text-tertiary">qr_code_2</span>
            QRIS Saat Ini
        </h2>

        @if($qrisUrl)
            <div class="rounded-xl border border-outline-variant/30 overflow-hidden bg-white p-4 flex justify-center">
                <img src="{{ $qrisUrl }}" alt="QRIS Pembayaran" class="max-w-full max-h-96 object-contain rounded-lg">
            </div>
            <div class="mt-4 p-3 bg-[#f0fdf4] border border-[#bbf7d0] rounded-xl flex items-center gap-2">
                <span class="material-symbols-outlined fill-icon text-[#166534] text-[18px]">check_circle</span>
                <span class="text-sm text-[#166534] font-medium">QRIS sudah aktif dan ditampilkan pada halaman tagihan warga.</span>
            </div>
        @else
            <div class="text-center py-12 text-on-surface-variant">
                <span class="material-symbols-outlined text-5xl opacity-40 mb-3 block">qr_code_2</span>
                <p class="font-medium">Belum ada QRIS yang diunggah.</p>
                <p class="text-sm mt-1">Upload gambar QRIS terlebih dahulu.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewQrisImage(event) {
    const file = event.target.files[0];
    const container = document.getElementById('preview-container');
    const image = document.getElementById('preview-image');

    if (file) {
        image.src = URL.createObjectURL(file);
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
        image.src = '';
    }
}
</script>
@endsection
