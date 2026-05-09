<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function __construct(
        private readonly WhatsAppService $whatsAppService
    ) {}

    public function gatewayWa(): View
    {
        $status = $this->whatsAppService->getStatus();

        return view('admin.pengaturan.gateway-wa', compact('status'));
    }

    public function getQrCode(): JsonResponse
    {
        $qrData = $this->whatsAppService->getQrCode();

        return response()->json($qrData);
    }

    public function getStatus(): JsonResponse
    {
        $status = $this->whatsAppService->getStatus();

        return response()->json($status);
    }

    public function logout(): RedirectResponse
    {
        $this->whatsAppService->logout();

        return back()->with('success', 'WhatsApp berhasil disconnect. Scan QR baru untuk menghubungkan kembali.');
    }
}
