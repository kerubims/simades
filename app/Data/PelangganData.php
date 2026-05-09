<?php

namespace App\Data;

readonly class PelangganData
{
    public function __construct(
        public string $idPelanggan,
        public string $idUser,
        public string $namaLengkap,
        public string $nik,
        public string $noKk,
        public string $rt,
        public string $rw,
        public string $noWhatsapp,
        public string $statusAktif,
        public int $rowIndex = 0,
    ) {}

    /**
     * @param array<string, string> $row
     */
    public static function fromSheetRow(array $row, int $rowIndex = 0): self
    {
        return new self(
            idPelanggan: $row['id_pelanggan'] ?? '',
            idUser: $row['id_user'] ?? '',
            namaLengkap: $row['nama_lengkap'] ?? '',
            nik: $row['nik'] ?? '',
            noKk: $row['no_kk'] ?? '',
            rt: $row['rt'] ?? '',
            rw: $row['rw'] ?? '',
            noWhatsapp: $row['no_whatsapp'] ?? '',
            statusAktif: $row['status_aktif'] ?? 'Aktif',
            rowIndex: $rowIndex,
        );
    }

    public function isAktif(): bool
    {
        return $this->statusAktif === 'Aktif';
    }

    public function getWhatsappLokal(): string
    {
        if (str_starts_with($this->noWhatsapp, '628')) {
            return '08' . substr($this->noWhatsapp, 3);
        }
        return $this->noWhatsapp;
    }
}
