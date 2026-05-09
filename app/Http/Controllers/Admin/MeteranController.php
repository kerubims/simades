<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SimpanTagihanJob;
use App\Services\PelangganService;
use App\Services\TagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeteranController extends Controller
{
    public function __construct(
        private readonly PelangganService $pelangganService,
        private readonly TagihanService $tagihanService,
    ) {}

    public function index(Request $request): View
    {
        $bulan = (int) ($request->query('bulan', date('n')));
        $tahun = (int) ($request->query('tahun', date('Y')));

        $pelangganList = $this->pelangganService->getAll(aktifOnly: true);
        $tagihanBulanIni = $this->tagihanService->getByPeriode($bulan, $tahun);

        // Buat map id_pelanggan => tagihan untuk pengecekan di view
        $tagihanMap = [];
        foreach ($tagihanBulanIni as $tagihan) {
            $tagihanMap[$tagihan->idPelanggan] = $tagihan;
        }

        $meterAwalMap = [];
        foreach ($pelangganList as $p) {
            if (!isset($tagihanMap[$p->idPelanggan])) {
                $meterAwalMap[$p->idPelanggan] = $this->tagihanService->getMeterAwalTerakhir($p->idPelanggan, $bulan, $tahun);
            }
        }

        $processingMap = \Illuminate\Support\Facades\Cache::get("processing_meter_{$bulan}_{$tahun}", []);

        return view('admin.meteran.index', compact('pelangganList', 'tagihanMap', 'meterAwalMap', 'processingMap', 'bulan', 'tahun'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_pelanggan' => ['required', 'string'],
            'meter_akhir' => ['required', 'integer', 'min:0'],
            'meter_awal' => ['nullable', 'integer', 'min:0'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'min:2020'],
            'kirim_wa' => ['nullable', 'boolean'],
        ]);

        $pelanggan = $this->pelangganService->findById($validated['id_pelanggan']);

        if ($pelanggan === null) {
            return back()->with('error', 'Pelanggan tidak ditemukan.');
        }

        // Cek apakah sudah ada tagihan periode ini
        $existing = $this->tagihanService->getTagihanPelangganPeriode(
            $pelanggan->idPelanggan,
            $validated['bulan'],
            $validated['tahun']
        );

        if ($existing !== null) {
            return back()->with('error', "Tagihan untuk {$pelanggan->namaLengkap} periode ini sudah ada.");
        }

        // Dispatch job ke background queue (tanpa kirim WA - WA dikirim saat lunas)
        SimpanTagihanJob::dispatch(
            pelanggan: $pelanggan,
            meterAkhir: (int) $validated['meter_akhir'],
            bulan: (int) $validated['bulan'],
            tahun: (int) $validated['tahun'],
            kirimWhatsapp: false,
            meterAwalOverride: isset($validated['meter_awal']) ? (int) $validated['meter_awal'] : null,
        );

        // Optimistic UI cache
        $processingMap = \Illuminate\Support\Facades\Cache::get("processing_meter_{$validated['bulan']}_{$validated['tahun']}", []);
        $processingMap[$pelanggan->idPelanggan] = [
            'meter_akhir' => (int) $validated['meter_akhir'],
        ];
        \Illuminate\Support\Facades\Cache::put("processing_meter_{$validated['bulan']}_{$validated['tahun']}", $processingMap, 60);

        return back()->with('success', "Meteran {$pelanggan->namaLengkap} berhasil disimpan. Tagihan sedang diproses.");
    }

    /**
     * Input meteran massal untuk semua pelanggan aktif sekaligus.
     */
    public function storeMassal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'min:2020'],
            'meteran' => ['required', 'array'],
            'meteran.*.id_pelanggan' => ['required', 'string'],
            'meteran.*.meter_akhir' => ['nullable', 'integer', 'min:0'],
            'meteran.*.meter_awal' => ['nullable', 'integer', 'min:0'],
        ]);

        $dispatched = 0;

        foreach ($validated['meteran'] as $item) {
            // Lewati jika form meteran ini tidak diisi (kosong)
            if (!isset($item['meter_akhir']) || $item['meter_akhir'] === null) {
                continue;
            }
            $pelanggan = $this->pelangganService->findById($item['id_pelanggan']);

            if ($pelanggan === null) {
                continue;
            }

            $existing = $this->tagihanService->getTagihanPelangganPeriode(
                $pelanggan->idPelanggan,
                (int) $validated['bulan'],
                (int) $validated['tahun']
            );

            if ($existing !== null) {
                continue;
            }

            SimpanTagihanJob::dispatch(
                pelanggan: $pelanggan,
                meterAkhir: (int) $item['meter_akhir'],
                bulan: (int) $validated['bulan'],
                tahun: (int) $validated['tahun'],
                kirimWhatsapp: false,
                meterAwalOverride: isset($item['meter_awal']) ? (int) $item['meter_awal'] : null,
            );

            $dispatched++;
        }

        if ($dispatched > 0) {
            $processingMap = \Illuminate\Support\Facades\Cache::get("processing_meter_{$validated['bulan']}_{$validated['tahun']}", []);
            foreach ($validated['meteran'] as $item) {
                if (!isset($item['meter_akhir']) || $item['meter_akhir'] === null) continue;
                $processingMap[$item['id_pelanggan']] = [
                    'meter_akhir' => (int) $item['meter_akhir'],
                ];
            }
            \Illuminate\Support\Facades\Cache::put("processing_meter_{$validated['bulan']}_{$validated['tahun']}", $processingMap, 60);
        }

        return redirect()->route('admin.meteran.index', ['bulan' => $validated['bulan'], 'tahun' => $validated['tahun']])
            ->with('success', "{$dispatched} data meteran berhasil disimpan dan diproses di background.");
    }
}
