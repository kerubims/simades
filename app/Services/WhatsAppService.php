<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $gatewayUrl;

    public function __construct()
    {
        $this->gatewayUrl = rtrim(config('services.whatsapp.gateway_url', 'http://localhost:3001'), '/');
    }

    /**
     * Kirim dokumen PDF langsung ke nomor WhatsApp.
     *
     * @param  string  $noWhatsapp  Format: 628xxx
     * @param  string  $pdfBase64   PDF dalam format base64
     * @param  string  $filename    Nama file yang akan diterima warga
     * @param  string  $caption     Pesan teks yang menyertai file
     */
    public function kirimSlipPdf(
        string $noWhatsapp,
        string $pdfBase64,
        string $filename,
        string $caption
    ): bool {
        try {
            $response = Http::timeout(30)->post("{$this->gatewayUrl}/send-document", [
                'to' => $noWhatsapp,
                'base64' => $pdfBase64,
                'filename' => $filename,
                'caption' => $caption,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp: slip terkirim ke {$noWhatsapp}");

                return true;
            }

            Log::warning("WhatsApp: gagal kirim ke {$noWhatsapp}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error("WhatsApp: exception saat kirim ke {$noWhatsapp}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Cek status koneksi gateway.
     *
     * @return array{connected: bool, number: string|null, message: string}
     */
    public function getStatus(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->gatewayUrl}/status");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'connected' => $data['connected'] ?? false,
                    'number' => $data['number'] ?? null,
                    'message' => $data['message'] ?? 'OK',
                ];
            }

            return ['connected' => false, 'number' => null, 'message' => 'Gateway tidak merespons'];
        } catch (\Throwable) {
            return ['connected' => false, 'number' => null, 'message' => 'Gateway tidak dapat dihubungi'];
        }
    }

    /**
     * Ambil QR code untuk scan WhatsApp.
     *
     * @return array{qr: string|null, message: string}
     */
    public function getQrCode(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->gatewayUrl}/qr");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'qr' => $data['qr'] ?? null,
                    'message' => $data['message'] ?? 'QR tersedia',
                ];
            }

            return ['qr' => null, 'message' => 'Gagal ambil QR code'];
        } catch (\Throwable) {
            return ['qr' => null, 'message' => 'Gateway tidak dapat dihubungi'];
        }
    }

    /**
     * Minta gateway untuk logout (putuskan koneksi WA).
     */
    public function logout(): bool
    {
        try {
            $response = Http::timeout(10)->post("{$this->gatewayUrl}/logout");

            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}
