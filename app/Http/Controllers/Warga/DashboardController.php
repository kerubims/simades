<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Services\PelangganService;
use App\Services\TagihanService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
        private readonly PelangganService $pelangganService,
    ) {}

    public function index(): View
    {
        $idPelanggan = session('pelanggan_id');

        if (! $idPelanggan) {
            abort(403, 'Data pelanggan Anda tidak ditemukan. Pastikan sinkronisasi data Admin dan Warga benar, lalu coba login ulang.');
        }

        $pelanggan = $this->pelangganService->findById($idPelanggan);

        $bulan = (int) date('n');
        $tahun = (int) date('Y');
        $tagihanBulanIni = $this->tagihanService->getTagihanPelangganPeriode($idPelanggan, $bulan, $tahun);
        $tarif = $this->tagihanService->getTarif();

        $qrisPath = $this->tagihanService->getQrisPath();
        $qrisUrl = $qrisPath ? Storage::disk('public')->url($qrisPath) : null;

        return view('warga.dashboard', compact('pelanggan', 'tagihanBulanIni', 'tarif', 'qrisUrl'));
    }
}
