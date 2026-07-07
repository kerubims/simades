@extends('layouts.app')
@section('title', 'Catat Meteran')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface">Catat Meteran Air Warga</h1>
    <p class="text-on-surface-variant">Periode: <strong>{{ date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) }}</strong></p>
</div>

<!-- Filter Periode -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <form method="GET" action="{{ route('admin.meteran.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-4 bg-surface p-4 rounded-xl border border-outline-variant/20 shadow-sm w-full md:w-fit">
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
        <input type="text" id="search-meteran" placeholder="Cari nama atau no. warga..."
            class="w-full pl-10 pr-4 py-2 rounded-xl border border-outline-variant/40 bg-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
    </div>
</div>

<!-- Legenda -->
<div class="flex flex-wrap items-center gap-3 mb-4 text-xs">
    <span class="font-semibold text-on-surface-variant">Keterangan:</span>
    <span class="flex items-center gap-1.5 px-2 py-1 bg-[#dcfce7] text-[#166534] font-bold rounded-full">
        <span class="material-symbols-outlined text-[14px]">check_circle</span> Sudah Dicatat (Lunas — tidak bisa diedit)
    </span>
    <span class="flex items-center gap-1.5 px-2 py-1 bg-[#fef9c3] text-[#854d0e] font-bold rounded-full">
        <span class="material-symbols-outlined text-[14px]">edit</span> Sudah Dicatat (Belum Lunas — bisa diedit)
    </span>
    <span class="flex items-center gap-1.5 px-2 py-1 bg-[#f1f5f9] text-[#475569] font-bold rounded-full">
        <span class="material-symbols-outlined text-[14px]">pending</span> Belum Dicatat
    </span>
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
                        <th class="p-4 font-semibold text-on-surface-variant">No. Warga</th>
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
                                    @if(!$tagihan->isSudahLunas())
                                        <span class="ml-2 text-xs text-[#854d0e] bg-[#fef9c3] px-1.5 py-0.5 rounded">awal: {{ $tagihan->meterAwal }}</span>
                                    @endif
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
                                    @if($tagihan->isSudahLunas())
                                        {{-- Lunas: tidak bisa diedit --}}
                                        <div class="flex flex-col gap-1.5">
                                            <span class="px-2 py-1 bg-[#dcfce7] text-[#166534] text-xs font-bold rounded-full flex items-center gap-1 w-fit">
                                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Sudah Dicatat
                                            </span>
                                            <span class="px-2 py-1 bg-[#dcfce7] text-[#166534] text-xs font-bold rounded-full flex items-center gap-1 w-fit">
                                                <span class="material-symbols-outlined text-[14px]">lock</span> Lunas
                                            </span>
                                        </div>
                                    @else
                                        {{-- Belum lunas: bisa diedit --}}
                                        <div class="flex flex-col gap-1.5">
                                            <span class="px-2 py-1 bg-[#fef9c3] text-[#854d0e] text-xs font-bold rounded-full flex items-center gap-1 w-fit">
                                                <span class="material-symbols-outlined text-[14px]">edit_note</span> Sudah Dicatat
                                            </span>
                                            <button type="button"
                                                onclick="openEditModal('{{ $tagihan->idTagihan }}', '{{ $p->namaLengkap }}', '{{ $tagihan->periodeLabel() }}', {{ $tagihan->meterAwal }}, {{ $tagihan->meterAkhir }}, {{ $tagihan->totalTagihan }})"
                                                class="px-2 py-1 bg-secondary text-white text-xs font-bold rounded-full flex items-center gap-1 w-fit hover:opacity-80 transition-opacity">
                                                <span class="material-symbols-outlined text-[14px]">edit</span> Edit Meteran
                                            </button>
                                        </div>
                                    @endif
                                @elseif(isset($processingMap[$p->idPelanggan]))
                                    <span class="px-2 py-1 bg-[#e0f2fe] text-[#0369a1] text-xs font-bold rounded-full flex items-center gap-1 w-fit"><span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Memproses...</span>
                                @else
                                    <span class="px-2 py-1 bg-[#f1f5f9] text-[#475569] text-xs font-bold rounded-full flex items-center gap-1 w-fit">
                                        <span class="material-symbols-outlined text-[14px]">pending</span> Belum Dicatat
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-on-surface-variant">Tidak ada warga aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($idx > 0)
            <div class="p-4 bg-surface-container-low border-t border-outline-variant/30 flex justify-center md:justify-end">
                <button type="submit" class="bg-primary text-on-primary px-6 py-2 rounded-lg font-bold hover:opacity-90 flex items-center gap-2">
                    <span class="material-symbols-outlined">save</span> Simpan dan Generate Tagihan
                </button>
            </div>
        @endif
    </form>
</div>

<!-- Modal Edit Meteran -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-surface w-full max-w-xl rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-4 md:p-6 border-b border-outline-variant/20 flex justify-between items-center bg-surface-container-low">
            <h2 class="text-lg md:text-xl font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary">edit_note</span> Edit Meteran
            </h2>
            <button onclick="closeEditModal()" class="text-on-surface-variant hover:text-error transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="p-4 md:p-6 overflow-y-auto">
            <p class="text-sm text-on-surface-variant mb-4">
                Warga: <strong id="modal-nama" class="text-on-surface"></strong> &mdash; Periode: <strong id="modal-periode" class="text-on-surface"></strong>
            </p>

            <form id="form-edit-meteran" method="POST" class="flex flex-col gap-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-1">Meter Awal</label>
                        <input type="number" name="meter_awal" id="modal_meter_awal" min="0" required 
                               class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary font-bold text-lg" 
                               oninput="hitungPreview()" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-on-surface-variant mb-1">Meter Akhir</label>
                        <input type="number" name="meter_akhir" id="modal_meter_akhir" min="0" required 
                               class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary font-bold text-lg" 
                               oninput="hitungPreview()" />
                    </div>
                </div>

                {{-- Preview Kalkulasi Modal --}}
                <div class="p-4 bg-[#eff6ff] rounded-xl border border-[#bfdbfe] text-sm mt-2">
                    <p class="font-semibold text-[#1e3a8a] mb-3">Preview Tagihan Baru:</p>
                    <div class="flex justify-between mb-1"><span class="text-[#1e40af]">Pemakaian</span><span class="font-bold text-[#1e40af]" id="modal-prev-pemakaian">0 m&sup3;</span></div>
                    
                    @php $tarif = app(App\Services\TagihanService::class)->getTarif(); @endphp
                    
                    <div class="mt-2 pt-2 border-t border-[#bfdbfe] space-y-1 text-xs text-[#1e40af]">
                        <div class="flex justify-between">
                            <span>Air (<span id="modal-rincian-pemakaian">0</span> m³ × Rp {{ number_format($tarif->airPerM3, 0, ',', '.') }})</span>
                            <span id="modal-rincian-air">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Beban Sampah</span>
                            <span>Rp {{ number_format($tarif->bebanSampah, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Dana Kematian</span>
                            <span>Rp {{ number_format($tarif->danaKematian, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Biaya Lampu Jalan</span>
                            <span>Rp {{ number_format($tarif->biayaLampuJalan, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between border-t border-[#bfdbfe] mt-3 pt-3">
                        <span class="text-[#1e40af] font-bold">Total Tagihan Baru</span>
                        <span class="font-bold text-[#1d4ed8] text-base md:text-lg" id="modal-prev-total">Rp 0</span>
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant font-bold hover:bg-surface-container-low transition-colors text-sm md:text-base">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-primary text-on-primary rounded-lg font-bold hover:opacity-90 flex items-center gap-2 transition-opacity text-sm md:text-base">
                        <span class="material-symbols-outlined">save</span> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
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
    const tarif = {
        airPerM3: {{ $tarif->airPerM3 ?? 0 }},
        bebanSampah: {{ $tarif->bebanSampah ?? 0 }},
        danaKematian: {{ $tarif->danaKematian ?? 0 }},
        biayaLampuJalan: {{ $tarif->biayaLampuJalan ?? 0 }}
    };

    function formatRupiah(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    function openEditModal(idTagihan, nama, periode, awal, akhir, totalOld) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('form-edit-meteran');
        
        // Populate data
        document.getElementById('modal-nama').textContent = nama;
        document.getElementById('modal-periode').textContent = periode;
        document.getElementById('modal_meter_awal').value = awal;
        document.getElementById('modal_meter_akhir').value = akhir;
        
        // Update form action route
        form.action = `/admin/meteran/${idTagihan}`;
        
        // Trigger calc
        hitungPreview();
        
        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Prevent body scroll on mobile
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function hitungPreview() {
        const awal = parseInt(document.getElementById('modal_meter_awal').value) || 0;
        const akhir = parseInt(document.getElementById('modal_meter_akhir').value) || 0;
        const pemakaian = Math.max(0, akhir - awal);
        const total = (pemakaian * tarif.airPerM3) + tarif.bebanSampah + tarif.danaKematian + tarif.biayaLampuJalan;

        document.getElementById('modal-prev-pemakaian').textContent = pemakaian + ' m³';
        document.getElementById('modal-rincian-pemakaian').textContent = pemakaian;
        document.getElementById('modal-rincian-air').textContent = formatRupiah(pemakaian * tarif.airPerM3);
        document.getElementById('modal-prev-total').textContent = formatRupiah(total);
    }

    // Close on outside click
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        new TablePagination('tbl-meteran', 'search-meteran');
    });
</script>
@endsection
