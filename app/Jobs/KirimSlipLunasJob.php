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

class KirimSlipLunasJob implements ShouldQueue
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
        Log::info("KirimSlipLunasJob: generate PDF untuk {$this->tagihan->idTagihan}");

        $tarif = $tagihanService->getTarif();

        $pdfContent = Pdf::loadView('pdf.slip_tagihan', [
            'tagihan' => $this->tagihan,
            'pelanggan' => $this->pelanggan,
            'tarif' => $tarif,
        ])
            ->setPaper('a5', 'portrait')
            ->output();

        if (! $this->pelanggan->noWhatsapp) {
            Log::warning("KirimSlipLunasJob: {$this->pelanggan->idPelanggan} tidak memiliki nomor WA, skip kirim.");

            return;
        }

        $caption = "✅ *Pembayaran Lunas - {$this->tagihan->periodeLabel()}*\n\n"
            ."Yth. {$this->pelanggan->namaLengkap}\n"
            ."RT/RW: {$this->pelanggan->rt}/{$this->pelanggan->rw}\n\n"
            ."💧 Pemakaian: {$this->tagihan->totalPemakaianM3} m³\n"
            .'💰 Total Dibayar: Rp '.number_format($this->tagihan->totalTagihan, 0, ',', '.')
            ."\n\nTerima kasih atas pembayaran Anda. Semoga sehat selalu 🙏";

        $filename = "bukti-lunas-{$this->tagihan->idTagihan}.pdf";

        $whatsAppService->kirimSlipPdf(
            noWhatsapp: $this->pelanggan->noWhatsapp,
            pdfBase64: base64_encode($pdfContent),
            filename: $filename,
            caption: $caption,
        );

        Log::info("KirimSlipLunasJob: slip lunas berhasil dikirim ke {$this->pelanggan->noWhatsapp}");
    }
}
