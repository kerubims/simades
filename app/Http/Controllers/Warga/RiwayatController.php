<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Services\TagihanService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService
    ) {}

    public function index(): View
    {
        $idPelanggan = session('pelanggan_id');

        if (! $idPelanggan) {
            abort(403, 'Data pelanggan Anda tidak ditemukan. Pastikan sinkronisasi data Admin dan Warga benar, lalu coba login ulang.');
        }

        $riwayat = $this->tagihanService->getByPelanggan($idPelanggan);

        // Urutkan terbaru dulu
        usort($riwayat, function ($a, $b) {
            if ($a->periodeTahun !== $b->periodeTahun) {
                return $b->periodeTahun - $a->periodeTahun;
            }

            return $b->periodeBulan - $a->periodeBulan;
        });

        $chartData = $this->tagihanService->getRiwayat6Bulan($idPelanggan);

        try {
            $tarif = $this->tagihanService->getTarif();
        } catch (\Throwable) {
            $tarif = null;
        }

        $qrisPath = $this->tagihanService->getQrisPath();
        $qrisUrl = $qrisPath ? Storage::disk('public')->url($qrisPath) : null;

        return view('warga.riwayat', compact('riwayat', 'chartData', 'tarif', 'qrisUrl'));
    }
}
