<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Services\PelangganService;
use App\Services\TagihanService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

    public function uploadBukti(Request $request, string $idTagihan): RedirectResponse
    {
        $request->validate([
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $tagihan = $this->tagihanService->findById($idTagihan);

        if (! $tagihan || $tagihan->idPelanggan !== session('pelanggan_id')) {
            return back()->with('error', 'Tagihan tidak ditemukan atau bukan milik Anda.');
        }

        if ($tagihan->isSudahLunas()) {
            return back()->with('error', 'Tagihan sudah lunas, tidak perlu upload bukti lagi.');
        }

        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store('bukti_pembayaran', 'public');
            $tagihan->buktiPembayaran = $path;
            $tagihan->statusBayar = 'Menunggu Konfirmasi';
            $tagihan->alasanPenolakan = null;

            $this->tagihanService->updateTagihan($tagihan);

            return back()->with('success', 'Bukti pembayaran berhasil diunggah. Mohon tunggu konfirmasi dari Admin.');
        }

        return back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }
}
