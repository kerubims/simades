<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PelangganService;
use App\Services\TagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
        private readonly PelangganService $pelangganService,
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

        $sortBy = $request->query('sort', 'status');

        // Sorting Logic
        usort($tagihanList, function ($a, $b) use ($pelangganMap, $sortBy) {
            $namaA = strtolower($pelangganMap[$a->idPelanggan]->namaLengkap ?? '');
            $namaB = strtolower($pelangganMap[$b->idPelanggan]->namaLengkap ?? '');

            if ($sortBy === 'nama_asc') {
                return strcmp($namaA, $namaB);
            } elseif ($sortBy === 'nama_desc') {
                return strcmp($namaB, $namaA);
            } elseif ($sortBy === 'tertinggi') {
                return $b->totalTagihan <=> $a->totalTagihan;
            } elseif ($sortBy === 'terendah') {
                return $a->totalTagihan <=> $b->totalTagihan;
            } elseif ($sortBy === 'terbaru') {
                return strcmp($b->idPelanggan, $a->idPelanggan);
            } else {
                // Default: Status Bayar -> Nama Lengkap
                $statusOrder = [
                    'Menunggu Konfirmasi' => 0,
                    'Belum Bayar' => 1,
                    'Lunas' => 2,
                ];

                $orderA = $statusOrder[$a->statusBayar] ?? 99;
                $orderB = $statusOrder[$b->statusBayar] ?? 99;

                if ($orderA !== $orderB) {
                    return $orderA <=> $orderB;
                }

                return strcmp($namaA, $namaB);
            }
        });

        $qrisPath = $this->tagihanService->getQrisPath();
        $qrisUrl = $qrisPath ? Storage::disk('public')->url($qrisPath) : null;

        return view('admin.tagihan.index', compact('tagihanList', 'pelangganMap', 'bulan', 'tahun', 'qrisUrl', 'sortBy'));
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

        return back()->with('success', 'Tagihan berhasil ditandai lunas.');
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
            Storage::disk('public')->delete($tagihan->buktiPembayaran);
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
}
