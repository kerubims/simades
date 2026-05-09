@extends('layouts.app')
@section('title', 'Manajemen Tagihan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface">Data Tagihan & Pembayaran</h1>
    <p class="text-on-surface-variant">Periode: <strong>{{ date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) }}</strong></p>
</div>

<!-- Filter Periode -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <form method="GET" action="{{ route('admin.tagihan.index') }}" class="flex items-center gap-4 bg-surface p-4 rounded-xl border border-outline-variant/20 shadow-sm w-fit">
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
        <input type="text" id="search-tagihan" placeholder="Cari nama atau ID tagihan..."
            class="w-full pl-10 pr-4 py-2 rounded-xl border border-outline-variant/40 bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
            oninput="filterTable('search-tagihan', 'tbl-tagihan')">
    </div>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/30">
                    <th class="p-4 font-semibold text-on-surface-variant">ID Tagihan</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Warga</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Meter (Awal - Akhir)</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Pemakaian</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Total</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Status</th>
                    <th class="p-4 font-semibold text-on-surface-variant text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tbl-tagihan">
                @forelse($tagihanList as $t)
                    @php $p = $pelangganMap[$t->idPelanggan] ?? null; @endphp
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-lowest">
                        <td class="p-4 text-xs font-mono">{{ $t->idTagihan }}</td>
                        <td class="p-4">
                            <p class="font-bold">{{ $p ? $p->namaLengkap : $t->idPelanggan }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $p ? 'RT ' . $p->rt . ' / RW ' . $p->rw : '' }}</p>
                        </td>
                        <td class="p-4 text-sm">{{ $t->meterAwal }} &ndash; {{ $t->meterAkhir }}</td>
                        <td class="p-4 font-medium">{{ $t->totalPemakaianM3 }} m&sup3;</td>
                        <td class="p-4 font-bold text-primary">Rp {{ number_format($t->totalTagihan, 0, ',', '.') }}</td>
                        <td class="p-4">
                            @if($t->isSudahLunas())
                                <span class="px-2 py-1 bg-[#dcfce7] text-[#166534] text-xs font-bold rounded-full flex items-center w-fit gap-1">
                                    <span class="material-symbols-outlined text-[14px] fill-icon">check_circle</span> Lunas
                                </span>
                            @else
                                <span class="px-2 py-1 bg-[#fef3c7] text-[#b45309] text-xs font-bold rounded-full flex items-center w-fit gap-1">
                                    <span class="material-symbols-outlined text-[14px]">schedule</span> Belum Bayar
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if(!$t->isSudahLunas())
                                <form action="{{ route('admin.tagihan.lunas', $t->idTagihan) }}" method="POST"
                                    onsubmit="return confirm('Tandai tagihan {{ $t->idTagihan }} sebagai lunas?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary text-white text-xs font-bold rounded-lg hover:opacity-90 transition-opacity">
                                        <span class="material-symbols-outlined text-[14px]">check</span>
                                        Tandai Lunas
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-on-surface-variant italic">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-on-surface-variant">Belum ada data tagihan di periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
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
