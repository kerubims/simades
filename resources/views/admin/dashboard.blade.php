@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-on-surface">Dashboard Pengelola SIMADES</h1>
    <p class="text-on-surface-variant">Ringkasan operasional dan keuangan air desa.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-2">
            <span class="text-on-surface-variant font-medium">Total Warga Aktif</span>
            <span class="material-symbols-outlined text-primary bg-primary-container/20 p-2 rounded-lg">group</span>
        </div>
        <h3 class="text-3xl font-bold text-on-surface">{{ $totalPelanggan }} <span class="text-sm font-normal text-on-surface-variant">KK</span></h3>
    </div>
    
    <div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-2">
            <span class="text-on-surface-variant font-medium">Air Terpakai Bulan Ini</span>
            <span class="material-symbols-outlined text-secondary bg-secondary-container/20 p-2 rounded-lg">water_drop</span>
        </div>
        <h3 class="text-3xl font-bold text-on-surface">{{ number_format($statistik['total_pemakaian_m3'], 0, ',', '.') }} <span class="text-sm font-normal text-on-surface-variant">m&sup3;</span></h3>
    </div>

    <div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-2">
            <span class="text-on-surface-variant font-medium">Pendapatan Masuk</span>
            <span class="material-symbols-outlined text-tertiary bg-tertiary-container/20 p-2 rounded-lg">payments</span>
        </div>
        <h3 class="text-2xl font-bold text-on-surface text-tertiary">Rp {{ number_format($statistik['total_pendapatan'], 0, ',', '.') }}</h3>
        <p class="text-xs text-on-surface-variant mt-1">{{ $statistik['sudah_bayar'] }} Tagihan Lunas</p>
    </div>

    <div class="bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant/20 flex flex-col justify-between">
        <div class="flex justify-between items-start mb-2">
            <span class="text-on-surface-variant font-medium">Belum Dibayar</span>
            <span class="material-symbols-outlined text-error bg-error-container/20 p-2 rounded-lg">warning</span>
        </div>
        <h3 class="text-3xl font-bold text-error">{{ $statistik['belum_bayar'] }}</h3>
        <p class="text-xs text-on-surface-variant mt-1">Tagihan Tertunggak</p>
    </div>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 overflow-hidden mb-8 p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-bold text-on-surface">Grafik Transaksi (Pendapatan & Pemakaian)</h2>
        <select id="chartPeriode" class="bg-surface-container border border-outline-variant/30 text-on-surface text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5">
            <option value="mingguan">Bulan Ini (Mingguan)</option>
            <option value="bulanan" selected>12 Bulan Terakhir (Bulanan)</option>
            <option value="tahunan">5 Tahun Terakhir (Tahunan)</option>
        </select>
    </div>
    <div class="relative w-full h-72">
        <canvas id="transaksiChart"></canvas>
    </div>
</div>

<div class="bg-surface rounded-2xl shadow-sm border border-outline-variant/20 overflow-hidden">
    <div class="p-6 border-b border-outline-variant/20 flex justify-between items-center">
        <h2 class="font-bold text-on-surface">Tagihan Terbaru (Bulan Ini)</h2>
        <a href="{{ route('admin.tagihan.index') }}" class="text-primary font-medium text-sm hover:underline">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/30">
                    <th class="p-4 font-semibold text-on-surface-variant">ID Tagihan</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Warga</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Pemakaian</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Total</th>
                    <th class="p-4 font-semibold text-on-surface-variant">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse(array_slice($tagihanBulanIni, 0, 5) as $t)
                    <tr class="border-b border-outline-variant/10">
                        <td class="p-4 font-medium text-primary">{{ $t->idTagihan }}</td>
                        <td class="p-4">{{ $t->idPelanggan }}</td>
                        <td class="p-4">{{ $t->totalPemakaianM3 }} m&sup3;</td>
                        <td class="p-4">Rp {{ number_format($t->totalTagihan, 0, ',', '.') }}</td>
                        <td class="p-4">
                            @if($t->isSudahLunas())
                                <span class="px-2 py-1 bg-[#dcfce7] text-[#166534] text-xs font-bold rounded-full">Lunas</span>
                            @else
                                <span class="px-2 py-1 bg-[#fef3c7] text-[#b45309] text-xs font-bold rounded-full">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-on-surface-variant">Belum ada tagihan dicatat bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('transaksiChart').getContext('2d');
        const allChartData = @json($chartData);
        let currentChart = null;

        function renderChart(periode) {
            let dataRaw = allChartData[periode] || [];
            
            // Untuk bulanan dan tahunan, kita balikkan karena dari terlama ke terbaru
            if(periode === 'bulanan' || periode === 'tahunan') {
                dataRaw = [...dataRaw].reverse();
            }

            const labels = dataRaw.map(item => item.label);
            const dataPemakaian = dataRaw.map(item => item.pemakaian);
            const dataPendapatan = dataRaw.map(item => item.pendapatan);

            if (currentChart) {
                currentChart.destroy();
            }

            currentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Total Pemakaian (m³)',
                            data: dataPemakaian,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1,
                            yAxisID: 'y-pemakaian'
                        },
                        {
                            label: 'Total Pendapatan (Rp)',
                            data: dataPendapatan,
                            type: 'line',
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y-pendapatan'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        'y-pemakaian': {
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Pemakaian (m³)'
                            },
                            beginAtZero: true
                        },
                        'y-pendapatan': {
                            type: 'linear',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Pendapatan (Rp)'
                            },
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        }

        // Render inisial berdasarkan dropdown (bulanan)
        const dropdown = document.getElementById('chartPeriode');
        renderChart(dropdown.value);

        // Event listener saat ganti opsi dropdown
        dropdown.addEventListener('change', function(e) {
            renderChart(e.target.value);
        });
    });
</script>
@endsection
