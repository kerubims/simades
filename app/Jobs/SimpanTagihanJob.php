<?php

namespace App\Jobs;

use App\Data\PelangganData;
use App\Services\TagihanService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SimpanTagihanJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /** Jumlah percobaan ulang jika gagal */
    public int $tries = 3;

    /** Timeout per percobaan (detik) */
    public int $timeout = 120;

    public function __construct(
        public readonly PelangganData $pelanggan,
        public readonly int $meterAkhir,
        public readonly int $bulan,
        public readonly int $tahun,
        public readonly ?int $meterAwalOverride = null,
    ) {}

    public function handle(TagihanService $tagihanService): void
    {
        Log::info("SimpanTagihanJob: mulai proses {$this->pelanggan->idPelanggan} periode {$this->bulan}/{$this->tahun}");

        // 1. Simpan tagihan ke Google Sheets
        $tagihan = $tagihanService->simpanTagihan(
            pelanggan: $this->pelanggan,
            meterAkhir: $this->meterAkhir,
            bulan: $this->bulan,
            tahun: $this->tahun,
            meterAwalOverride: $this->meterAwalOverride,
        );

        if ($tagihan === null) {
            Log::error("SimpanTagihanJob: gagal simpan tagihan {$this->pelanggan->idPelanggan}");

            return;
        }

        Log::info("SimpanTagihanJob: selesai {$tagihan->idTagihan}");
    }
}
