<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QrisController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService
    ) {}

    public function index(): View
    {
        $qrisPath = $this->tagihanService->getQrisPath();
        $qrisUrl = $qrisPath ? Storage::disk('public')->url($qrisPath) : null;

        return view('admin.qris.index', compact('qrisUrl'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'qris_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Hapus file lama jika ada
        $oldPath = $this->tagihanService->getQrisPath();
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Simpan file baru ke disk public
        $path = $request->file('qris_image')->store('qris', 'public');

        // Simpan path ke Google Sheets
        $this->tagihanService->saveQrisPath($path);

        return back()->with('success', 'Gambar QRIS berhasil diunggah.');
    }
}
