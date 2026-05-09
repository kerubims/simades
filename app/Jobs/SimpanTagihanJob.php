<?php

namespace App\Jobs;

use App\Data\PelangganData;
use App\Services\TagihanService;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
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
        public readonly bool $kirimWhatsapp = true,
        public readonly ?int $meterAwalOverride = null,
    ) {}

    public function handle(TagihanService $tagihanService, WhatsAppService $whatsAppService): void
    {
        Log::info("SimpanTagihanJob: mulai proses {$this->pelanggan->idPelanggan} periode {$this->bulan}/{$this->tahun}");

        // 1. Simpan tagihan ke Google Sheets (tanpa PDF dulu)
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

        // 2. Generate PDF slip
        $tarif = $tagihanService->getTarif();

        $pdfContent = Pdf::loadView('pdf.slip_tagihan', [
            'tagihan' => $tagihan,
            'pelanggan' => $this->pelanggan,
            'tarif' => $tarif,
        ])
            ->setPaper('a5', 'portrait')
            ->output();

        Log::info("SimpanTagihanJob: PDF generated untuk {$tagihan->idTagihan}");

        // 3. Kirim ke WhatsApp jika diminta
        if ($this->kirimWhatsapp && $this->pelanggan->noWhatsapp) {
            $caption = "📋 *Tagihan Air Desa - {$tagihan->periodeLabel()}*\n\n"
                ."Yth. {$this->pelanggan->namaLengkap}\n"
                ."RT/RW: {$this->pelanggan->rt}/{$this->pelanggan->rw}\n\n"
                ."💧 Pemakaian: {$tagihan->totalPemakaianM3} m³\n"
                .'💰 Total Tagihan: Rp '.number_format($tagihan->totalTagihan, 0, ',', '.')
                ."\n\nMohon segera melakukan pembayaran. Terima kasih 🙏";

            $filename = "slip-tagihan-{$tagihan->idTagihan}.pdf";

            $whatsAppService->kirimSlipPdf(
                noWhatsapp: $this->pelanggan->noWhatsapp,
                pdfBase64: base64_encode($pdfContent),
                filename: $filename,
                caption: $caption,
            );
        }

        Log::info("SimpanTagihanJob: selesai {$tagihan->idTagihan}");
    }
}
