<?php

namespace App\Services;

use App\Data\PelangganData;
use App\Data\TagihanData;
use App\Data\TarifData;

class TagihanService
{
    public function __construct(
        private readonly GoogleSheetsService $sheets
    ) {}

    public function getTarif(): TarifData
    {
        $rows = $this->sheets->getSheet('pengaturan_tarif');

        return TarifData::fromSheetRows($rows);
    }

    /**
     * Ambil semua tagihan.
     *
     * @return array<int, TagihanData>
     */
    public function getAll(): array
    {
        $rows = $this->sheets->getSheet('transaksi_tagihan');
        $result = [];

        foreach ($rows as $index => $row) {
            $result[] = TagihanData::fromSheetRow($row, $index + 2);
        }

        return $result;
    }

    /**
     * Ambil tagihan berdasarkan ID pelanggan.
     *
     * @return array<int, TagihanData>
     */
    public function getByPelanggan(string $idPelanggan): array
    {
        $rows = $this->sheets->getSheet('transaksi_tagihan');
        $result = [];

        foreach ($rows as $index => $row) {
            if (($row['id_pelanggan'] ?? '') === $idPelanggan) {
                $result[] = TagihanData::fromSheetRow($row, $index + 2);
            }
        }

        return $result;
    }

    /**
     * Ambil tagihan berdasarkan ID.
     */
    public function findById(string $idTagihan): ?TagihanData
    {
        $rows = $this->sheets->getSheet('transaksi_tagihan');

        foreach ($rows as $index => $row) {
            if (($row['id_tagihan'] ?? '') === $idTagihan) {
                return TagihanData::fromSheetRow($row, $index + 2);
            }
        }

        return null;
    }

    /**
     * Ambil tagihan bulan & tahun tertentu.
     *
     * @return array<int, TagihanData>
     */
    public function getByPeriode(int $bulan, int $tahun): array
    {
        $rows = $this->sheets->getSheet('transaksi_tagihan');
        $result = [];

        foreach ($rows as $index => $row) {
            if ((int) ($row['periode_bulan'] ?? 0) === $bulan && (int) ($row['periode_tahun'] ?? 0) === $tahun) {
                $result[] = TagihanData::fromSheetRow($row, $index + 2);
            }
        }

        return $result;
    }

    /**
     * Tagihan spesifik per pelanggan per periode.
     */
    public function getTagihanPelangganPeriode(string $idPelanggan, int $bulan, int $tahun): ?TagihanData
    {
        $rows = $this->sheets->getSheet('transaksi_tagihan');

        foreach ($rows as $index => $row) {
            if (
                ($row['id_pelanggan'] ?? '') === $idPelanggan
                && (int) ($row['periode_bulan'] ?? 0) === $bulan
                && (int) ($row['periode_tahun'] ?? 0) === $tahun
            ) {
                return TagihanData::fromSheetRow($row, $index + 2);
            }
        }

        return null;
    }

    /**
     * Ambil meter akhir terakhir sebagai meter awal bulan ini.
     */
    public function getMeterAwalTerakhir(string $idPelanggan, int $bulanSekarang, int $tahunSekarang): int
    {
        $tagihanList = $this->getByPelanggan($idPelanggan);

        // Urutkan dari terbaru ke terlama
        usort($tagihanList, function (TagihanData $a, TagihanData $b) {
            if ($a->periodeTahun !== $b->periodeTahun) {
                return $b->periodeTahun - $a->periodeTahun;
            }

            return $b->periodeBulan - $a->periodeBulan;
        });

        // Ambil meter akhir bulan sebelumnya
        foreach ($tagihanList as $tagihan) {
            if ($tagihan->periodeTahun < $tahunSekarang
                || ($tagihan->periodeTahun === $tahunSekarang && $tagihan->periodeBulan < $bulanSekarang)) {
                return $tagihan->meterAkhir;
            }
        }

        return 0;
    }

    /**
     * Simpan tagihan baru ke Google Sheets.
     */
    public function simpanTagihan(
        PelangganData $pelanggan,
        int $meterAkhir,
        int $bulan,
        int $tahun,
        string $linkPdf = '',
        ?int $meterAwalOverride = null
    ): ?TagihanData {
        $tarif = $this->getTarif();
        $meterAwal = $meterAwalOverride ?? $this->getMeterAwalTerakhir($pelanggan->idPelanggan, $bulan, $tahun);
        $pemakaian = max(0, $meterAkhir - $meterAwal);
        $totalTagihan = $tarif->hitungTagihan($pemakaian);

        $idTagihan = $this->generateIdTagihan($pelanggan->idPelanggan, $bulan, $tahun);

        $existing = $this->sheets->findRow('transaksi_tagihan', 'id_tagihan', $idTagihan);

        $statusBayar = $existing ? ($existing['data']['status_bayar'] ?? 'Belum Bayar') : 'Belum Bayar';
        $linkPdfToSave = $linkPdf ?: ($existing ? ($existing['data']['link_pdf'] ?? '') : '');

        $tagihan = new TagihanData(
            idTagihan: $idTagihan,
            idPelanggan: $pelanggan->idPelanggan,
            periodeBulan: $bulan,
            periodeTahun: $tahun,
            meterAwal: $meterAwal,
            meterAkhir: $meterAkhir,
            totalPemakaianM3: $pemakaian,
            totalTagihan: $totalTagihan,
            statusBayar: $statusBayar,
            linkPdf: $linkPdfToSave,
            tanggalDibuat: $existing ? ($existing['data']['tanggal_dibuat'] ?? date('Y-m-d')) : date('Y-m-d'),
            tanggalPembayaran: $existing ? ($existing['data']['tanggal_pembayaran'] ?? null) : null,
        );

        if ($existing) {
            $success = $this->sheets->updateRow('transaksi_tagihan', $existing['rowIndex'], $tagihan->toSheetRow());
            if ($success) {
                $tagihan->rowIndex = $existing['rowIndex'];
            }
        } else {
            $success = $this->sheets->appendRow('transaksi_tagihan', $tagihan->toSheetRow());
        }

        return $success ? $tagihan : null;
    }

    /**
     * Update data tagihan secara penuh.
     */
    public function updateTagihan(TagihanData $tagihan): bool
    {
        return $this->sheets->updateRow('transaksi_tagihan', $tagihan->rowIndex, $tagihan->toSheetRow());
    }

    /**
     * Edit meteran (meter awal & akhir) dan hitung ulang tagihan.
     * Hanya boleh dilakukan jika status bukan Lunas.
     */
    public function updateTagihanMeteran(TagihanData $tagihan, int $meterAwal, int $meterAkhir): bool
    {
        $tarif = $this->getTarif();
        $pemakaian = max(0, $meterAkhir - $meterAwal);
        $totalTagihan = $tarif->hitungTagihan($pemakaian);

        $tagihan->meterAwal = $meterAwal;
        $tagihan->meterAkhir = $meterAkhir;
        $tagihan->totalPemakaianM3 = $pemakaian;
        $tagihan->totalTagihan = $totalTagihan;

        return $this->sheets->updateRow('transaksi_tagihan', $tagihan->rowIndex, $tagihan->toSheetRow());
    }

    /**
     * Hitung ulang semua tagihan yang belum lunas menggunakan tarif baru.
     */
    public function recalculateUnpaidBills(TarifData $newTarif): void
    {
        $allBills = $this->getAll();

        foreach ($allBills as $tagihan) {
            if (! $tagihan->isSudahLunas()) {
                $newTotal = $newTarif->hitungTagihan($tagihan->totalPemakaianM3);

                if ($newTotal !== $tagihan->totalTagihan) {
                    $tagihan->totalTagihan = $newTotal;
                    // Hati-hati dengan API rate limit jika data sangat banyak. Idealnya batchUpdate.
                    $this->sheets->updateRow('transaksi_tagihan', $tagihan->rowIndex, $tagihan->toSheetRow());
                }
            }
        }
    }

    /**
     * Update status bayar menjadi Lunas.
     */
    public function tandaiLunas(TagihanData $tagihan): bool
    {
        $tagihan->statusBayar = 'Lunas';
        $tagihan->tanggalPembayaran = date('Y-m-d');

        return $this->updateTagihan($tagihan);
    }

    /**
     * Update link PDF pada tagihan.
     */
    public function updateLinkPdf(TagihanData $tagihan, string $linkPdf): bool
    {
        return $this->sheets->updateCell(
            'transaksi_tagihan',
            "J{$tagihan->rowIndex}", // kolom J = link_pdf
            $linkPdf
        );
    }

    /**
     * Statistik dashboard admin.
     *
     * @return array{total_pendapatan: int, belum_bayar: int, sudah_bayar: int, total_pemakaian_m3: int}
     */
    public function getStatistikBulanIni(): array
    {
        $bulan = (int) date('n');
        $tahun = (int) date('Y');
        $tagihanBulanIni = $this->getByPeriode($bulan, $tahun);

        $totalPendapatan = 0;
        $belumBayar = 0;
        $sudahBayar = 0;
        $totalPemakaian = 0;

        foreach ($tagihanBulanIni as $tagihan) {
            if ($tagihan->isSudahLunas()) {
                $totalPendapatan += $tagihan->totalTagihan;
                $sudahBayar++;
            } else {
                $belumBayar++;
            }
            $totalPemakaian += $tagihan->totalPemakaianM3;
        }

        return [
            'total_pendapatan' => $totalPendapatan,
            'belum_bayar' => $belumBayar,
            'sudah_bayar' => $sudahBayar,
            'total_pemakaian_m3' => $totalPemakaian,
        ];
    }

    /**
     * Riwayat pendapatan dan pemakaian untuk grafik admin (Mingguan, Bulanan, Tahunan).
     *
     * @return array{mingguan: array, bulanan: array, tahunan: array}
     */
    public function getChartDataAdmin(): array
    {
        $now = now();
        $currentMonth = (int) $now->format('n');
        $currentYear = (int) $now->format('Y');

        // 1. DATA MINGGUAN (Berdasarkan parameter tanggal)
        $tagihanBulanIni = $this->getByPeriode($currentMonth, $currentYear);
        
        $mingguan = [
            ['label' => 'Minggu 1', 'pemakaian' => 0, 'pendapatan' => 0],
            ['label' => 'Minggu 2', 'pemakaian' => 0, 'pendapatan' => 0],
            ['label' => 'Minggu 3', 'pemakaian' => 0, 'pendapatan' => 0],
            ['label' => 'Minggu 4', 'pemakaian' => 0, 'pendapatan' => 0],
        ];

        foreach ($tagihanBulanIni as $tagihan) {
            // Tentukan minggu untuk pemakaian dari tanggalDibuat
            $day = 1;
            if ($tagihan->tanggalDibuat) {
                $day = (int) date('j', strtotime($tagihan->tanggalDibuat));
            }
            
            $weekIndex = 0;
            if ($day >= 8 && $day <= 14) {
                $weekIndex = 1;
            } elseif ($day >= 15 && $day <= 21) {
                $weekIndex = 2;
            } elseif ($day >= 22) {
                $weekIndex = 3;
            }

            $mingguan[$weekIndex]['pemakaian'] += $tagihan->totalPemakaianM3;
            
            if ($tagihan->isSudahLunas()) {
                // Tentukan minggu untuk pendapatan dari tanggalPembayaran
                $payDay = $day;
                if ($tagihan->tanggalPembayaran) {
                    $payDay = (int) date('j', strtotime($tagihan->tanggalPembayaran));
                }
                
                $payWeekIndex = 0;
                if ($payDay >= 8 && $payDay <= 14) {
                    $payWeekIndex = 1;
                } elseif ($payDay >= 15 && $payDay <= 21) {
                    $payWeekIndex = 2;
                } elseif ($payDay >= 22) {
                    $payWeekIndex = 3;
                }
                
                $mingguan[$payWeekIndex]['pendapatan'] += $tagihan->totalTagihan;
            }
        }

        // 2. DATA BULANAN (12 bulan terakhir)
        $bulanan = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulan = (int) $date->format('n');
            $tahun = (int) $date->format('Y');

            $tagihanList = $this->getByPeriode($bulan, $tahun);
            $pemakaian = 0;
            $pendapatan = 0;

            foreach ($tagihanList as $tagihan) {
                $pemakaian += $tagihan->totalPemakaianM3;
                if ($tagihan->isSudahLunas()) {
                    $pendapatan += $tagihan->totalTagihan;
                }
            }

            $bulanan[] = [
                'label' => $date->translatedFormat('M Y'),
                'pemakaian' => $pemakaian,
                'pendapatan' => $pendapatan,
            ];
        }

        // 3. DATA TAHUNAN (5 tahun terakhir)
        $tahunan = [];
        for ($i = 4; $i >= 0; $i--) {
            $tahunIterasi = $currentYear - $i;
            $pemakaian = 0;
            $pendapatan = 0;

            // Untuk tahunan, kita bisa iterasi 12 bulan atau ambil semua tagihan
            // Demi efisiensi sederhana, kita filter dari getAll() yang bisa di cache
            // Namun getAll() tidak dicache lama, tapi ok.
            // Lebih baik iterasi getAll lalu filter.
            $tahunan[] = [
                'label' => (string) $tahunIterasi,
                'pemakaian' => 0,
                'pendapatan' => 0,
            ];
        }
        
        $allTagihan = $this->getAll();
        foreach ($allTagihan as $tagihan) {
            foreach ($tahunan as &$thn) {
                if ($thn['label'] == $tagihan->periodeTahun) {
                    $thn['pemakaian'] += $tagihan->totalPemakaianM3;
                    if ($tagihan->isSudahLunas()) {
                        $thn['pendapatan'] += $tagihan->totalTagihan;
                    }
                    break;
                }
            }
        }

        return [
            'mingguan' => $mingguan,
            'bulanan' => $bulanan,
            'tahunan' => $tahunan,
        ];
    }

    /**
     * Riwayat pemakaian 6 bulan terakhir untuk grafik warga.
     *
     * @return array<int, array{bulan: string, pemakaian: int, tagihan: int}>
     */
    public function getRiwayat6Bulan(string $idPelanggan): array
    {
        $result = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulan = (int) $date->format('n');
            $tahun = (int) $date->format('Y');

            $tagihan = $this->getTagihanPelangganPeriode($idPelanggan, $bulan, $tahun);

            $result[] = [
                'bulan' => $date->translatedFormat('M Y'),
                'pemakaian' => $tagihan?->totalPemakaianM3 ?? 0,
                'tagihan' => $tagihan?->totalTagihan ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Update tarif air per m3 dan komponen lainnya.
     */
    public function updateTarif(int $airPerM3, int $bebanSampah, int $danaKematian, int $biayaLampuJalan): bool
    {
        $tarif = $this->getTarif();

        $ok1 = $this->sheets->updateCell('pengaturan_tarif', "B{$tarif->airRowIndex}", (string) $airPerM3);
        $ok2 = $this->sheets->updateCell('pengaturan_tarif', "B{$tarif->sampahRowIndex}", (string) $bebanSampah);
        $ok3 = $this->sheets->updateCell('pengaturan_tarif', "B{$tarif->kematianRowIndex}", (string) $danaKematian);

        // Biaya lampu jalan: update jika sudah ada, append jika belum
        if ($tarif->lampuJalanRowIndex > 0) {
            $ok4 = $this->sheets->updateCell('pengaturan_tarif', "B{$tarif->lampuJalanRowIndex}", (string) $biayaLampuJalan);
        } else {
            $ok4 = $this->sheets->appendRow('pengaturan_tarif', [
                'komponen' => 'biaya_lampu_jalan',
                'nominal' => (string) $biayaLampuJalan,
            ]);
        }

        $this->sheets->clearCache('pengaturan_tarif');

        return $ok1 && $ok2 && $ok3 && $ok4;
    }

    /**
     * Ambil path QRIS dari Google Sheets.
     */
    public function getQrisPath(): ?string
    {
        $rows = $this->sheets->getSheet('pengaturan_tarif');

        foreach ($rows as $row) {
            if (strtolower($row['komponen'] ?? '') === 'qris_path') {
                $path = $row['nominal'] ?? '';

                return $path !== '' ? $path : null;
            }
        }

        return null;
    }

    /**
     * Simpan path QRIS ke Google Sheets.
     */
    public function saveQrisPath(string $path): bool
    {
        $rows = $this->sheets->getSheet('pengaturan_tarif');

        foreach ($rows as $index => $row) {
            if (strtolower($row['komponen'] ?? '') === 'qris_path') {
                $rowIndex = $index + 2;
                $success = $this->sheets->updateCell('pengaturan_tarif', "B{$rowIndex}", $path);
                $this->sheets->clearCache('pengaturan_tarif');

                return $success;
            }
        }

        // Jika belum ada, append baris baru
        $success = $this->sheets->appendRow('pengaturan_tarif', [
            'komponen' => 'qris_path',
            'nominal' => $path,
        ]);
        $this->sheets->clearCache('pengaturan_tarif');

        return $success;
    }

    private function generateIdTagihan(string $idPelanggan, int $bulan, int $tahun): string
    {
        return sprintf('INV-%d%02d-%s', $tahun, $bulan, $idPelanggan);
    }
}
