<?php

namespace App\Http\Controllers;

use App\Services\PelangganService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly PelangganService $pelangganService
    ) {}

    public function showLogin(): View|RedirectResponse
    {
        if (session('user_id')) {
            return $this->redirectByRole(session('user_role'));
        }

        return view('auth.login');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (session('user_id')) {
            return $this->redirectByRole(session('user_role'));
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
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

        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'string', 'max:20'],
            'no_kk' => ['required', 'string', 'max:20'],
            'rt' => ['required', 'string', 'max:5'],
            'rw' => ['required', 'string', 'max:5'],
            'no_whatsapp' => ['required', 'string', 'max:20'],
        ]);

        // Cek username apakah sudah ada
        $existingUser = $this->pelangganService->findUserByUsername($data['username']);
        if ($existingUser !== null) {
            return back()->withInput()->with('error', 'Username sudah digunakan, silakan pilih yang lain.');
        }

        $created = $this->pelangganService->create($data);

        if (! $created) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan masuk menggunakan username dan password Anda.');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = $this->pelangganService->findUserByIdentifier($request->username);

        if ($user === null || ! Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('username'))
                ->with('error', 'Username/NIK/No.KK atau password salah.');
        }

        // Set session
        session([
            'user_id' => $user->idUser,
            'user_role' => $user->role,
            'username' => $user->username,
        ]);

        // Jika warga, simpan juga id_pelanggan di session
        if ($user->role === 'warga') {
            $pelanggan = $this->pelangganService->findByIdUser($user->idUser);
            if ($pelanggan) {
                session([
                    'pelanggan_id' => $pelanggan->idPelanggan,
                    'pelanggan_nama' => $pelanggan->namaLengkap
                ]);
            }
        }

        return $this->redirectByRole($user->role);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->flush();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    private function redirectByRole(?string $role): RedirectResponse
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'warga' => redirect()->route('warga.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
