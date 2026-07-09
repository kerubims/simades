<?php

namespace App\Services;

use App\Data\PelangganData;
use App\Data\UserData;

class PelangganService
{
    public function __construct(
        private readonly GoogleSheetsService $sheets
    ) {}

    /**
     * Ambil semua pelanggan aktif.
     *
     * @return array<int, PelangganData>
     */
    public function getAll(bool $aktifOnly = false): array
    {
        $rows = $this->sheets->getSheet('pelanggan');
        $result = [];

        foreach ($rows as $index => $row) {
            $pelanggan = PelangganData::fromSheetRow($row, $index + 2);

            if ($aktifOnly && ! $pelanggan->isAktif()) {
                continue;
            }

            $result[] = $pelanggan;
        }

        return $result;
    }

    public function findById(?string $idPelanggan): ?PelangganData
    {
        if (empty($idPelanggan)) {
            return null;
        }

        $found = $this->sheets->findRow('pelanggan', 'id_pelanggan', $idPelanggan);

        if ($found === null) {
            return null;
        }

        return PelangganData::fromSheetRow($found['data'], $found['rowIndex']);
    }

    public function findByIdUser(string $idUser): ?PelangganData
    {
        $found = $this->sheets->findRow('pelanggan', 'id_user', $idUser);

        if ($found === null) {
            return null;
        }

        return PelangganData::fromSheetRow($found['data'], $found['rowIndex']);
    }

    /**
     * Buat pelanggan baru beserta akun user-nya.
     *
     * @param  array<string, string>  $data
     */
    public function create(array $data): bool
    {
        $idPelanggan = $this->generateIdPelanggan();
        $idUser = $this->generateIdUser();

        // Buat akun user dulu
        $userCreated = $this->sheets->appendRow('users', [
            'id_user' => $idUser,
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'role' => 'warga',
        ]);

        if (! $userCreated) {
            return false;
        }

        // Buat data pelanggan
        return $this->sheets->appendRow('pelanggan', [
            'id_pelanggan' => $idPelanggan,
            'id_user' => $idUser,
            'nama_lengkap' => $data['nama_lengkap'],
            'nik' => $data['nik'] ?? '',
            'no_kk' => $data['no_kk'] ?? '',
            'rt' => $data['rt'],
            'rw' => $data['rw'],
            'no_whatsapp' => $data['no_whatsapp'],
            'status_aktif' => 'Aktif',
        ]);
    }

    /**
     * Update data pelanggan.
     *
     * @param  array<string, string>  $data
     */
    public function update(PelangganData $pelanggan, array $data): bool
    {
        return $this->sheets->updateRow('pelanggan', $pelanggan->rowIndex, [
            'id_pelanggan' => $pelanggan->idPelanggan,
            'id_user' => $pelanggan->idUser,
            'nama_lengkap' => $data['nama_lengkap'] ?? $pelanggan->namaLengkap,
            'nik' => $data['nik'] ?? $pelanggan->nik,
            'no_kk' => $data['no_kk'] ?? $pelanggan->noKk,
            'rt' => $data['rt'] ?? $pelanggan->rt,
            'rw' => $data['rw'] ?? $pelanggan->rw,
            'no_whatsapp' => $data['no_whatsapp'] ?? $pelanggan->noWhatsapp,
            'status_aktif' => $data['status_aktif'] ?? $pelanggan->statusAktif,
        ]);
    }

    /**
     * Update password user.
     */
    public function updateUserPassword(string $idUser, string $newPassword): bool
    {
        $user = $this->findUserByIdUser($idUser);

        if ($user === null) {
            return false;
        }

        return $this->sheets->updateRow('users', $user->rowIndex, [
            'id_user' => $user->idUser,
            'username' => $user->username,
            'password' => bcrypt($newPassword),
            'role' => $user->role,
        ]);
    }

    /**
     * Nonaktifkan pelanggan (soft delete).
     */
    public function deactivate(PelangganData $pelanggan): bool
    {
        return $this->sheets->updateRow('pelanggan', $pelanggan->rowIndex, [
            'id_pelanggan' => $pelanggan->idPelanggan,
            'id_user' => $pelanggan->idUser,
            'nama_lengkap' => $pelanggan->namaLengkap,
            'nik' => $pelanggan->nik,
            'no_kk' => $pelanggan->noKk,
            'rt' => $pelanggan->rt,
            'rw' => $pelanggan->rw,
            'no_whatsapp' => $pelanggan->noWhatsapp,
            'status_aktif' => 'Non-Aktif',
        ]);
    }

    /**
     * Hapus akun user secara permanen dari sheet users dan data pelanggan dari sheet pelanggan.
     */
    public function deleteUser(PelangganData $pelanggan): bool
    {
        $user = $this->findUserByIdUser($pelanggan->idUser);

        if ($user !== null) {
            // Hapus baris user dari sheet users
            $this->sheets->deleteRow('users', $user->rowIndex);
        }

        // Hapus baris data pelanggan
        $deleted = $this->sheets->deleteRow('pelanggan', $pelanggan->rowIndex);

        return $deleted;
    }

    /**
     * Total pelanggan aktif.
     */
    public function countAktif(): int
    {
        return count($this->getAll(aktifOnly: true));
    }

    private function generateIdPelanggan(): string
    {
        $maxNumber = 0;

        // 1. Cek dari sheet pelanggan
        $rowsPelanggan = $this->sheets->getSheet('pelanggan');
        foreach ($rowsPelanggan as $row) {
            $id = $row['id_pelanggan'] ?? '';
            if (preg_match('/^PLG(\d+)$/', $id, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        // 2. Cek juga dari sheet transaksi_tagihan (menghindari ID didaur ulang jika tagihannya masih ada)
        $rowsTagihan = $this->sheets->getSheet('transaksi_tagihan');
        foreach ($rowsTagihan as $row) {
            $id = $row['id_pelanggan'] ?? '';
            if (preg_match('/^PLG(\d+)$/', $id, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $nextNumber = $maxNumber + 1;

        return 'PLG'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    private function generateIdUser(): string
    {
        $rows = $this->sheets->getSheet('users');
        $maxNumber = 0;

        foreach ($rows as $row) {
            $id = $row['id_user'] ?? '';
            if (preg_match('/^U(\d+)$/', $id, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $nextNumber = $maxNumber + 1;

        return 'U'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Cari user by username.
     */
    public function findUserByUsername(string $username): ?UserData
    {
        $found = $this->sheets->findRow('users', 'username', $username);

        if ($found === null) {
            return null;
        }

        return UserData::fromSheetRow($found['data'], $found['rowIndex']);
    }

    public function findUserByIdUser(string $idUser): ?UserData
    {
        $found = $this->sheets->findRow('users', 'id_user', $idUser);

        if ($found === null) {
            return null;
        }

        return UserData::fromSheetRow($found['data'], $found['rowIndex']);
    }

    public function findPelangganByNikOrKk(string $identifier): ?PelangganData
    {
        $found = $this->sheets->findRow('pelanggan', 'nik', $identifier);
        if ($found !== null) {
            return PelangganData::fromSheetRow($found['data'], $found['rowIndex']);
        }

        $found = $this->sheets->findRow('pelanggan', 'no_kk', $identifier);
        if ($found !== null) {
            return PelangganData::fromSheetRow($found['data'], $found['rowIndex']);
        }

        return null;
    }

    public function findUserByIdentifier(string $identifier): ?UserData
    {
        // 1. Coba cari di sheet users sebagai username
        $user = $this->findUserByUsername($identifier);
        if ($user !== null) {
            return $user;
        }

        // 2. Jika tidak ketemu, cari di pelanggan sebagai NIK atau No KK
        $pelanggan = $this->findPelangganByNikOrKk($identifier);
        if ($pelanggan !== null) {
            return $this->findUserByIdUser($pelanggan->idUser);
        }

        return null;
    }

    /**
     * Ambil semua user admin.
     *
     * @return array<int, UserData>
     */
    public function getAllAdmins(): array
    {
        $rows = $this->sheets->getSheet('users');
        $result = [];

        foreach ($rows as $index => $row) {
            $user = UserData::fromSheetRow($row, $index + 2);
            if ($user->role === 'admin') {
                $result[] = $user;
            }
        }

        return $result;
    }

    public function createAdmin(array $data): bool
    {
        $idUser = $this->generateIdUser();

        return $this->sheets->appendRow('users', [
            'id_user' => $idUser,
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'role' => 'admin',
        ]);
    }

    public function updateAdmin(UserData $admin, array $data): bool
    {
        return $this->sheets->updateRow('users', $admin->rowIndex, [
            'id_user' => $admin->idUser,
            'username' => $data['username'] ?? $admin->username,
            'password' => ! empty($data['password']) ? bcrypt($data['password']) : $admin->password,
            'role' => 'admin',
        ]);
    }

    public function deleteAdmin(UserData $admin): bool
    {
        return $this->sheets->deleteRow('users', $admin->rowIndex);
    }
}
