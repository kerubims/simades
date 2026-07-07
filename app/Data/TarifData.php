<?php

namespace App\Data;

readonly class TarifData
{
    public function __construct(
        public int $airPerM3,
        public int $bebanSampah,
        public int $danaKematian,
        public int $biayaLampuJalan,
        public int $airRowIndex = 0,
        public int $sampahRowIndex = 0,
        public int $kematianRowIndex = 0,
        public int $lampuJalanRowIndex = 0,
    ) {}

    public function hitungTagihan(int $pemakaianM3): int
    {
        return ($pemakaianM3 * $this->airPerM3) + $this->bebanSampah + $this->danaKematian + $this->biayaLampuJalan;
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     */
    public static function fromSheetRows(array $rows): self
    {
        $tarif = [
            'air_per_m3' => ['nominal' => 500, 'row' => 0],
            'beban_sampah' => ['nominal' => 10000, 'row' => 0],
            'dana_kematian' => ['nominal' => 2000, 'row' => 0],
            'biaya_lampu_jalan' => ['nominal' => 0, 'row' => 0],
        ];

        foreach ($rows as $index => $row) {
            $komponen = strtolower($row['komponen'] ?? '');

            if ($komponen === 'air_per_m3') {
                $tarif['air_per_m3'] = ['nominal' => (int) ($row['nominal'] ?? 500), 'row' => $index + 2];
            } elseif ($komponen === 'beban_sampah') {
                $tarif['beban_sampah'] = ['nominal' => (int) ($row['nominal'] ?? 10000), 'row' => $index + 2];
            } elseif ($komponen === 'dana_kematian') {
                $tarif['dana_kematian'] = ['nominal' => (int) ($row['nominal'] ?? 2000), 'row' => $index + 2];
            } elseif ($komponen === 'biaya_lampu_jalan') {
                $tarif['biaya_lampu_jalan'] = ['nominal' => (int) ($row['nominal'] ?? 0), 'row' => $index + 2];
            }
        }

        return new self(
            airPerM3: $tarif['air_per_m3']['nominal'],
            bebanSampah: $tarif['beban_sampah']['nominal'],
            danaKematian: $tarif['dana_kematian']['nominal'],
            biayaLampuJalan: $tarif['biaya_lampu_jalan']['nominal'],
            airRowIndex: $tarif['air_per_m3']['row'],
            sampahRowIndex: $tarif['beban_sampah']['row'],
            kematianRowIndex: $tarif['dana_kematian']['row'],
            lampuJalanRowIndex: $tarif['biaya_lampu_jalan']['row'],
        );
    }
}
