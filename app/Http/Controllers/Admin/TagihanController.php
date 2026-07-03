<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\KirimSlipLunasJob;
use App\Jobs\KirimSlipTagihanWaJob;
use App\Services\PelangganService;
use App\Services\TagihanService;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
        private readonly PelangganService $pelangganService,
        private readonly WhatsAppService $whatsAppService,
    ) {}

    public function index(Request $request): View
    {
        $bulan = (int) ($request->query('bulan', date('n')));
        $tahun = (int) ($request->query('tahun', date('Y')));

        $tagihanList = $this->tagihanService->getByPeriode($bulan, $tahun);
        $pelangganMap = [];
        foreach ($this->pelangganService->getAll() as $p) {
            $pelangganMap[$p->idPelanggan] = $p;
        }

        $qrisPath = $this->tagihanService->getQrisPath();
        $qrisUrl = $qrisPath ? \Illuminate\Support\Facades\Storage::disk('public')->url($qrisPath) : null;

        return view('admin.tagihan.index', compact('tagihanList', 'pelangganMap', 'bulan', 'tahun', 'qrisUrl'));
    }

    public function tandaiLunas(string $idTagihan): RedirectResponse
    {
        $semuaTagihan = $this->tagihanService->getAll();
        $tagihan = null;

        foreach ($semuaTagihan as $t) {
            if ($t->idTagihan === $idTagihan) {
                $tagihan = $t;
                break;
            }
        }

        if ($tagihan === null) {
            return back()->with('error', 'Tagihan tidak ditemukan.');
        }

        $tagihan->statusBayar = 'Lunas';
        $tagihan->alasanPenolakan = null;

        $success = $this->tagihanService->updateTagihan($tagihan);

        if (! $success) {
            return back()->with('error', 'Gagal update status pembayaran.');
        }

        // Kirim slip bukti lunas ke WhatsApp warga
        $pelanggan = $this->pelangganService->findById($tagihan->idPelanggan);
        if ($pelanggan && $pelanggan->noWhatsapp) {
            KirimSlipLunasJob::dispatch($pelanggan, $tagihan);
        }

        return back()->with('success', 'Tagihan berhasil ditandai lunas. Slip bukti sedang dikirim ke WhatsApp warga.');
    }

    public function tolakPembayaran(Request $request, string $idTagihan): RedirectResponse
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:255',
        ]);

        $semuaTagihan = $this->tagihanService->getAll();
        $tagihan = null;

        foreach ($semuaTagihan as $t) {
            if ($t->idTagihan === $idTagihan) {
                $tagihan = $t;
                break;
            }
        }

        if ($tagihan === null) {
            return back()->with('error', 'Tagihan tidak ditemukan.');
        }

        if ($tagihan->buktiPembayaran) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($tagihan->buktiPembayaran);
        }

        $tagihan->statusBayar = 'Belum Bayar';
        $tagihan->buktiPembayaran = null;
        $tagihan->alasanPenolakan = $request->input('alasan_penolakan');

        $success = $this->tagihanService->updateTagihan($tagihan);

        if (! $success) {
            return back()->with('error', 'Gagal menolak pembayaran.');
        }

        return back()->with('success', 'Pembayaran berhasil ditolak. Warga dapat mengunggah bukti baru.');
    }

    /**
     * Kirim ulang slip WA ke satu pelanggan.
     */
    public function kirimUlangWa(string $idTagihan): RedirectResponse
    {
        $semuaTagihan = $this->tagihanService->getAll();
        $tagihan = null;

        foreach ($semuaTagihan as $t) {
            if ($t->idTagihan === $idTagihan) {
                $tagihan = $t;
                break;
            }
        }

        if ($tagihan === null) {
            return back()->with('error', 'Tagihan tidak ditemukan.');
        }

        $pelanggan = $this->pelangganService->findById($tagihan->idPelanggan);

        if ($pelanggan === null) {
            return back()->with('error', 'Data pelanggan tidak ditemukan.');
        }

        // Dispatch job kirim WA tanpa menyentuh data tagihan
        KirimSlipTagihanWaJob::dispatch($pelanggan, $tagihan);

        return back()->with('success', "Slip tagihan sedang dikirim ulang ke {$pelanggan->namaLengkap}.");
    }

    /**
     * Broadcast tagihan ke semua warga yang belum bayar di periode tertentu.
     */
    public function broadcastBelumBayar(Request $request): RedirectResponse
    {
        $bulan = (int) $request->input('bulan', date('n'));
        $tahun = (int) $request->input('tahun', date('Y'));

        $tagihanList = $this->tagihanService->getByPeriode($bulan, $tahun);
        $dispatched = 0;

        foreach ($tagihanList as $tagihan) {
            if ($tagihan->isSudahLunas()) {
                continue;
            }

            $pelanggan = $this->pelangganService->findById($tagihan->idPelanggan);

            if ($pelanggan === null || ! $pelanggan->noWhatsapp) {
                continue;
            }

            KirimSlipTagihanWaJob::dispatch($pelanggan, $tagihan);

            $dispatched++;
        }

        return back()->with('success', "Broadcast berhasil. {$dispatched} tagihan sedang dikirim ke WhatsApp.");
    }
}
