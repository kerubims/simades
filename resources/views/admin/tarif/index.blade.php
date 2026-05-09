@extends('layouts.app')
@section('title', 'Pengaturan Tarif')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface">Pengaturan Tarif Air Desa</h1>
    <p class="text-on-surface-variant">Update komponen biaya yang akan dibebankan pada tagihan bulan berikutnya.</p>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 p-6 max-w-xl">
    <form action="{{ route('admin.tarif.update') }}" method="POST" class="flex flex-col gap-6">
        @csrf
        @method('PUT')
        
        <div class="flex items-center gap-4 bg-[#eff6ff] p-4 rounded-xl border border-[#bfdbfe]">
            <span class="material-symbols-outlined text-[#1d4ed8] text-4xl">water_drop</span>
            <div class="flex-grow">
                <label class="block text-sm font-bold text-[#1e3a8a] mb-1">Tarif Air per m&sup3;</label>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-[#1e40af]">Rp</span>
                    <input type="number" name="air_per_m3" value="{{ old('air_per_m3', $tarif->airPerM3) }}" min="0" required class="w-full rounded-lg border-[#93c5fd] focus:border-[#3b82f6] focus:ring-[#3b82f6] font-bold" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 bg-[#f0fdf4] p-4 rounded-xl border border-[#bbf7d0]">
            <span class="material-symbols-outlined text-[#15803d] text-4xl">delete</span>
            <div class="flex-grow">
                <label class="block text-sm font-bold text-[#14532d] mb-1">Beban Sampah (Per Bulan)</label>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-[#166534]">Rp</span>
                    <input type="number" name="beban_sampah" value="{{ old('beban_sampah', $tarif->bebanSampah) }}" min="0" required class="w-full rounded-lg border-[#86efac] focus:border-[#22c55e] focus:ring-[#22c55e] font-bold" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 bg-[#fdf2f8] p-4 rounded-xl border border-[#fbcfe8]">
            <span class="material-symbols-outlined text-[#be185d] text-4xl">volunteer_activism</span>
            <div class="flex-grow">
                <label class="block text-sm font-bold text-[#831843] mb-1">Dana Kematian / Sosial (Per Bulan)</label>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-[#9d174d]">Rp</span>
                    <input type="number" name="dana_kematian" value="{{ old('dana_kematian', $tarif->danaKematian) }}" min="0" required class="w-full rounded-lg border-[#f9a8d4] focus:border-[#ec4899] focus:ring-[#ec4899] font-bold" />
                </div>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-primary text-on-primary rounded-lg font-bold hover:opacity-90 flex items-center gap-2">
                <span class="material-symbols-outlined">save</span> Simpan Tarif Baru
            </button>
        </div>
    </form>
</div>
@endsection
