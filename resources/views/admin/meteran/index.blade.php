@extends('layouts.app')
@section('title', 'Catat Meteran')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface">Catat Meteran Air Warga</h1>
    <p class="text-on-surface-variant">Periode: <strong>{{ date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) }}</strong></p>
</div>

<!-- Filter Periode -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <form method="GET" action="{{ route('admin.meteran.index') }}" class="flex items-center gap-4 bg-surface p-4 rounded-xl border border-outline-variant/20 shadow-sm w-fit">
        <div class="flex items-center gap-2">
            <label class="font-semibold text-sm">Bulan:</label>
            <select name="bulan" class="rounded-lg border-outline-variant py-1 text-sm">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $bulan === $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="font-semibold text-sm">Tahun:</label>
            <select name="tahun" class="rounded-lg border-outline-variant py-1 text-sm">
                @foreach(range(date('Y')-1, date('Y')+1) as $y)
                    <option value="{{ $y }}" {{ $tahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-secondary text-white px-4 py-1.5 rounded-lg text-sm font-bold">Tampilkan</button>
    </form>

    <div class="relative w-full md:w-80">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
        <input type="text" id="search-meteran" placeholder="Cari nama atau no. pelanggan..."
            class="w-full pl-10 pr-4 py-2 rounded-xl border border-outline-variant/40 bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
            oninput="filterTable('search-meteran', 'tbl-meteran')">
    </div>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 overflow-hidden">
    <form action="{{ route('admin.meteran.massal') }}" method="POST">
        @csrf
        <input type="hidden" name="bulan" value="{{ $bulan }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant/30">
                        <th class="p-4 font-semibold text-on-surface-variant">No. Pelanggan</th>
                        <th class="p-4 font-semibold text-on-surface-variant">Nama</th>
                        <th class="p-4 font-semibold text-on-surface-variant">Meteran Akhir (Bulan Ini)</th>
                        <th class="p-4 font-semibold text-on-surface-variant">Status</th>
                    </tr>
                </thead>
                <tbody id="tbl-meteran">
                    @php $idx = 0; @endphp
                    @forelse($pelangganList as $p)
                        @php $tagihan = $tagihanMap[$p->idPelanggan] ?? null; @endphp
                        <tr class="border-b border-outline-variant/10 hover:bg-surface-container-lowest">
                            <td class="p-4 font-medium">{{ $p->idPelanggan }}</td>
                            <td class="p-4 font-bold">{{ $p->namaLengkap }}</td>
                            <td class="p-4">
                                @if($tagihan)
                                    <span class="font-bold text-lg">{{ $tagihan->meterAkhir }}</span>
                                    <span class="text-sm text-on-surface-variant"> m&sup3;</span>
                                @elseif(isset($processingMap[$p->idPelanggan]))
                                    <span class="font-bold text-lg text-primary">{{ $processingMap[$p->idPelanggan]['meter_akhir'] }}</span>
                                    <span class="text-sm text-on-surface-variant"> m&sup3;</span>
                                @else
                                    <div class="flex flex-col gap-2">
                                        @php $mAwal = $meterAwalMap[$p->idPelanggan] ?? 0; @endphp
                                        @if($mAwal == 0)
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs w-16 text-on-surface-variant">Awal:</span>
                                                <input type="number" name="meteran[{{ $idx }}][meter_awal]" min="0" placeholder="Awal..." class="rounded-lg border-outline-variant focus:border-primary focus:ring-primary w-24 px-2 py-1 text-sm" />
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs w-16 text-on-surface-variant">Akhir:</span>
                                            <input type="hidden" name="meteran[{{ $idx }}][id_pelanggan]" value="{{ $p->idPelanggan }}">
                                            <input type="number" name="meteran[{{ $idx }}][meter_akhir]" min="0" placeholder="Akhir..." class="rounded-lg border-outline-variant focus:border-primary focus:ring-primary w-24 px-2 py-1 text-sm" />
                                            <span class="text-sm text-on-surface-variant">m&sup3;</span>
                                        </div>
                                    </div>
                                    @php $idx++; @endphp
                                @endif
                            </td>
                            <td class="p-4">
                                @if($tagihan)
                                    <span class="px-2 py-1 bg-[#dcfce7] text-[#166534] text-xs font-bold rounded-full">Sudah Dicatat</span>
                                @elseif(isset($processingMap[$p->idPelanggan]))
                                    <span class="px-2 py-1 bg-[#e0f2fe] text-[#0369a1] text-xs font-bold rounded-full flex items-center gap-1 w-fit"><span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Memproses...</span>
                                @else
                                    <span class="px-2 py-1 bg-[#f1f5f9] text-[#475569] text-xs font-bold rounded-full">Belum Dicatat</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-on-surface-variant">Tidak ada pelanggan aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($idx > 0)
            <div class="p-4 bg-surface-container-low border-t border-outline-variant/30 flex justify-end">
                <button type="submit" class="bg-primary text-on-primary px-6 py-2 rounded-lg font-bold hover:opacity-90 flex items-center gap-2">
                    <span class="material-symbols-outlined">save</span> Simpan dan Generate Tagihan
                </button>
            </div>
        @endif
    </form>
</div>
@endsection

@section('scripts')
@if(count($processingMap) > 0)
<script>
    let countdown = 4;

    const timer = setInterval(() => {
        countdown--;
        if (countdown <= 0) {
            clearInterval(timer);
            window.location.reload();
        }
    }, 1000);
</script>
@endif
<script>
function filterTable(inputId, tbodyId) {
    const q = document.getElementById(inputId).value.toLowerCase();
    const rows = document.getElementById(tbodyId).querySelectorAll('tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
@endsection
