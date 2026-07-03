<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Services\PelangganService;
use App\Services\TagihanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Throwable;

class SlipController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
        private readonly PelangganService $pelangganService,
    ) {}

    public function download(string $idTagihan): Response|RedirectResponse
    {
        try {
            $idPelanggan = session('pelanggan_id');

            if (empty($idPelanggan)) {
                return redirect()->route('login')->with('error', 'Sesi habis. Silakan login kembali.');
            }

            // Cari tagihan milik pelanggan ini
            $tagihan = $this->tagihanService->findById($idTagihan);

            if ($tagihan === null || $tagihan->idPelanggan !== $idPelanggan) {
                return back()->with('error', 'Slip tagihan tidak ditemukan.');
            }

            $pelanggan = $this->pelangganService->findById($idPelanggan);

            if ($pelanggan === null) {
                return back()->with('error', 'Data pelanggan tidak ditemukan.');
            }

            $tarif = $this->tagihanService->getTarif();

            $pdf = Pdf::loadView('pdf.slip_tagihan', compact('tagihan', 'pelanggan', 'tarif'))
                ->setPaper('a5', 'portrait');

            return $pdf->download("slip-tagihan-{$tagihan->idTagihan}.pdf");

        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal mengunduh slip: '.$e->getMessage());
        }
    }
}
