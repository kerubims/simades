<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PelangganService;
use App\Services\TagihanService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
        private readonly PelangganService $pelangganService,
    ) {}

    public function index(): View
    {
        $statistik = $this->tagihanService->getStatistikBulanIni();
        $totalPelanggan = $this->pelangganService->countAktif();

        $bulan = (int) date('n');
        $tahun = (int) date('Y');
        $tagihanBulanIni = $this->tagihanService->getByPeriode($bulan, $tahun);

        return view('admin.dashboard', compact('statistik', 'totalPelanggan', 'tagihanBulanIni'));
    }
}
