<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PelangganService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function __construct(
        private readonly PelangganService $pelangganService
    ) {}

    public function index(): View
    {
        $pelangganList = $this->pelangganService->getAll();

        return view('admin.pelanggan.index', compact('pelangganList'));
    }

    public function create(): View
    {
        return view('admin.pelanggan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->has('no_whatsapp')) {
            $wa = preg_replace('/[^0-9]/', '', $request->input('no_whatsapp'));
            if (str_starts_with($wa, '08')) {
                $wa = '628' . substr($wa, 2);
            }
            $request->merge(['no_whatsapp' => $wa]);
        }
        if ($request->has('rt')) {
            $request->merge(['rt' => str_pad($request->input('rt'), 2, '0', STR_PAD_LEFT)]);
        }
        if ($request->has('rw')) {
            $request->merge(['rw' => str_pad($request->input('rw'), 2, '0', STR_PAD_LEFT)]);
        }

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'string', 'max:20'],
            'no_kk' => ['required', 'string', 'max:20'],
            'rt' => ['required', 'string', 'max:5'],
            'rw' => ['required', 'string', 'max:5'],
            'no_whatsapp' => ['required', 'string', 'regex:/^628[0-9]{8,12}$/'],
            'username' => ['required', 'string', 'min:4', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $success = $this->pelangganService->create($validated);

        if (! $success) {
            return back()->withInput()->with('error', 'Gagal menyimpan pelanggan. Coba lagi.');
        }

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(string $id): View
    {
        $pelanggan = $this->pelangganService->findById($id);

        if ($pelanggan === null) {
            abort(404, 'Pelanggan tidak ditemukan.');
        }

        return view('admin.pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $pelanggan = $this->pelangganService->findById($id);

        if ($pelanggan === null) {
            abort(404, 'Pelanggan tidak ditemukan.');
        }

        if ($request->has('no_whatsapp')) {
            $wa = preg_replace('/[^0-9]/', '', $request->input('no_whatsapp'));
            if (str_starts_with($wa, '08')) {
                $wa = '628' . substr($wa, 2);
            }
            $request->merge(['no_whatsapp' => $wa]);
        }
        if ($request->has('rt')) {
            $request->merge(['rt' => str_pad($request->input('rt'), 2, '0', STR_PAD_LEFT)]);
        }
        if ($request->has('rw')) {
            $request->merge(['rw' => str_pad($request->input('rw'), 2, '0', STR_PAD_LEFT)]);
        }

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'string', 'max:20'],
            'no_kk' => ['required', 'string', 'max:20'],
            'rt' => ['required', 'string', 'max:5'],
            'rw' => ['required', 'string', 'max:5'],
            'no_whatsapp' => ['required', 'string', 'regex:/^628[0-9]{8,12}$/'],
            'status_aktif' => ['required', 'in:Aktif,Non-Aktif'],
        ]);

        $success = $this->pelangganService->update($pelanggan, $validated);

        if (! $success) {
            return back()->withInput()->with('error', 'Gagal update pelanggan.');
        }

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diupdate.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $pelanggan = $this->pelangganService->findById($id);

        if ($pelanggan === null) {
            abort(404, 'Pelanggan tidak ditemukan.');
        }

        $this->pelangganService->deactivate($pelanggan);

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Pelanggan berhasil dinonaktifkan.');
    }
}
