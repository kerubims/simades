<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Services\PelangganService;
use App\Services\TagihanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class SlipController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
        private readonly PelangganService $pelangganService,
    ) {}

    public function download(string $idTagihan): Response
    {
        $idPelanggan = session('pelanggan_id');

        // Cari tagihan
        $semuaTagihan = $this->tagihanService->getByPelanggan($idPelanggan);
        $tagihan = null;

        foreach ($semuaTagihan as $t) {
            if ($t->idTagihan === $idTagihan) {
                $tagihan = $t;
                break;
            }
        }

        if ($tagihan === null) {
            abort(404, 'Slip tagihan tidak ditemukan.');
        }

        $pelanggan = $this->pelangganService->findById($idPelanggan);
        $tarif = $this->tagihanService->getTarif();

        $pdf = Pdf::loadView('pdf.slip_tagihan', compact('tagihan', 'pelanggan', 'tarif'))
            ->setPaper('a5', 'portrait');

        return $pdf->download("slip-tagihan-{$tagihan->idTagihan}.pdf");
    }
}
