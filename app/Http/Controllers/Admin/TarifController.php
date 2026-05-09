<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TarifController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService
    ) {}

    public function index(): View
    {
        $tarif = $this->tagihanService->getTarif();

        return view('admin.tarif.index', compact('tarif'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'air_per_m3' => ['required', 'integer', 'min:0'],
            'beban_sampah' => ['required', 'integer', 'min:0'],
            'dana_kematian' => ['required', 'integer', 'min:0'],
        ]);

        $success = $this->tagihanService->updateTarif(
            airPerM3: (int) $validated['air_per_m3'],
            bebanSampah: (int) $validated['beban_sampah'],
            danaKematian: (int) $validated['dana_kematian'],
        );

        if (! $success) {
            return back()->with('error', 'Gagal update tarif. Coba lagi.');
        }

        return back()->with('success', 'Tarif berhasil diperbarui.');
    }
}
