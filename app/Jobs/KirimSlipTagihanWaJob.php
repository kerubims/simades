<?php

namespace App\Jobs;

use App\Data\PelangganData;
use App\Data\TagihanData;
use App\Services\TagihanService;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job ini khusus untuk mengirimkan slip tagihan ke WhatsApp TANPA mengubah
 * data tagihan di Google Sheets sama sekali. Gunakan untuk kirim ulang dan broadcast.
 */
class KirimSlipTagihanWaJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public readonly PelangganData $pelanggan,
        public readonly TagihanData $tagihan,
    ) {}

    public function handle(TagihanService $tagihanService, WhatsAppService $whatsAppService): void
    {
        Log::info("KirimSlipTagihanWaJob: generate PDF untuk {$this->tagihan->idTagihan}");

        if (! $this->pelanggan->noWhatsapp) {
            Log::warning("KirimSlipTagihanWaJob: {$this->pelanggan->idPelanggan} tidak punya nomor WA, skip.");

            return;
        }

        $tarif = $tagihanService->getTarif();

        $pdfContent = Pdf::loadView('pdf.slip_tagihan', [
            'tagihan' => $this->tagihan,
            'pelanggan' => $this->pelanggan,
            'tarif' => $tarif,
        ])
            ->setPaper('a5', 'portrait')
            ->output();

        $caption = "📋 *Tagihan Air Desa - {$this->tagihan->periodeLabel()}*\n\n"
            ."Yth. {$this->pelanggan->namaLengkap}\n"
            ."RT/RW: {$this->pelanggan->rt}/{$this->pelanggan->rw}\n\n"
            ."💧 Pemakaian: {$this->tagihan->totalPemakaianM3} m³\n"
            .'💰 Total Tagihan: Rp '.number_format($this->tagihan->totalTagihan, 0, ',', '.')
            ."\n\nMohon segera melakukan pembayaran. Terima kasih 🙏";

        $filename = "slip-tagihan-{$this->tagihan->idTagihan}.pdf";

        $whatsAppService->kirimSlipPdf(
            noWhatsapp: $this->pelanggan->noWhatsapp,
            pdfBase64: base64_encode($pdfContent),
            filename: $filename,
            caption: $caption,
        );

        Log::info("KirimSlipTagihanWaJob: slip berhasil dikirim ke {$this->pelanggan->noWhatsapp}");
    }
}
