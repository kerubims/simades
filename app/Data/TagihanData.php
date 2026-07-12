<?php

namespace App\Data;

class TagihanData
{
    public function __construct(
        public string $idTagihan,
        public string $idPelanggan,
        public int $periodeBulan,
        public int $periodeTahun,
        public int $meterAwal,
        public int $meterAkhir,
        public int $totalPemakaianM3,
        public int $totalTagihan,
        public string $statusBayar,
        public string $linkPdf,
        public ?string $buktiPembayaran = null,
        public ?string $alasanPenolakan = null,
        public ?string $tanggalDibuat = null,
        public ?string $tanggalPembayaran = null,
        public int $rowIndex = 0,
    ) {}

    /**
     * @param array<string, string> $row
     */
    public static function fromSheetRow(array $row, int $rowIndex = 0): self
    {
        return new self(
            idTagihan: $row['id_tagihan'] ?? '',
            idPelanggan: $row['id_pelanggan'] ?? '',
            periodeBulan: (int) ($row['periode_bulan'] ?? 0),
            periodeTahun: (int) ($row['periode_tahun'] ?? 0),
            meterAwal: (int) ($row['meter_awal'] ?? 0),
            meterAkhir: (int) ($row['meter_akhir'] ?? 0),
            totalPemakaianM3: (int) ($row['total_pemakaian_m3'] ?? 0),
            totalTagihan: (int) ($row['total_tagihan'] ?? 0),
            statusBayar: $row['status_bayar'] ?? 'Belum Bayar',
            linkPdf: $row['link_pdf'] ?? '',
            buktiPembayaran: $row['bukti_pembayaran'] ?? null,
            alasanPenolakan: $row['alasan_penolakan'] ?? null,
            tanggalDibuat: $row['tanggal_dibuat'] ?? null,
            tanggalPembayaran: $row['tanggal_pembayaran'] ?? null,
            rowIndex: $rowIndex,
        );
    }

    public function isMenungguKonfirmasi(): bool
    {
        return $this->statusBayar === 'Menunggu Konfirmasi';
    }

    public function isSudahLunas(): bool
    {
        return $this->statusBayar === 'Lunas';
    }

    public function periodeLabel(): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return ($bulan[$this->periodeBulan] ?? $this->periodeBulan).' '.$this->periodeTahun;
    }

    /**
     * @return array<string, string>
     */
    public function toSheetRow(): array
    {
        return [
            'id_tagihan' => $this->idTagihan,
            'id_pelanggan' => $this->idPelanggan,
            'periode_bulan' => (string) $this->periodeBulan,
            'periode_tahun' => (string) $this->periodeTahun,
            'meter_awal' => (string) $this->meterAwal,
            'meter_akhir' => (string) $this->meterAkhir,
            'total_pemakaian_m3' => (string) $this->totalPemakaianM3,
            'total_tagihan' => (string) $this->totalTagihan,
            'status_bayar' => $this->statusBayar,
            'link_pdf' => $this->linkPdf,
            'bukti_pembayaran' => $this->buktiPembayaran ?? '',
            'alasan_penolakan' => $this->alasanPenolakan ?? '',
            'tanggal_dibuat' => $this->tanggalDibuat ?? '',
            'tanggal_pembayaran' => $this->tanggalPembayaran ?? '',
        ];
    }
}
