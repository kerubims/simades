<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PelangganService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly PelangganService $pelangganService
    ) {}

    public function index(): View
    {
        $admins = $this->pelangganService->getAllAdmins();

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admins.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'min:4', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $exists = $this->pelangganService->findUserByUsername($validated['username']);
        if ($exists) {
            return back()->withInput()->with('error', 'Username sudah digunakan.');
        }

        $success = $this->pelangganService->createAdmin($validated);

        if (! $success) {
            return back()->withInput()->with('error', 'Gagal menyimpan admin. Coba lagi.');
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit(string $id): View
    {
        $admin = $this->pelangganService->findUserByIdUser($id);

        if ($admin === null || $admin->role !== 'admin') {
            abort(404, 'Admin tidak ditemukan.');
        }

        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $admin = $this->pelangganService->findUserByIdUser($id);

        if ($admin === null || $admin->role !== 'admin') {
            abort(404, 'Admin tidak ditemukan.');
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'min:4', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if ($validated['username'] !== $admin->username) {
            $exists = $this->pelangganService->findUserByUsername($validated['username']);
            if ($exists) {
                return back()->withInput()->with('error', 'Username sudah digunakan oleh user lain.');
            }
        }

        $success = $this->pelangganService->updateAdmin($admin, $validated);

        if (! $success) {
            return back()->withInput()->with('error', 'Gagal mengupdate admin.');
        }

        // If the logged-in admin changes their own password/username, we might want to log them out or just show success
        return redirect()->route('admin.admins.index')
            ->with('success', 'Data admin berhasil diupdate.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $admin = $this->pelangganService->findUserByIdUser($id);

        if ($admin === null || $admin->role !== 'admin') {
            abort(404, 'Admin tidak ditemukan.');
        }

        if (auth()->user() && auth()->user()->id_user === $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $success = $this->pelangganService->deleteAdmin($admin);

        if (! $success) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Gagal menghapus admin.');
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin berhasil dihapus.');
    }
}
