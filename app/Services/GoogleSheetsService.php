<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\Request;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    private Sheets $service;

    private string $spreadsheetId;

    /** @var array<string, int> Cache TTL per sheet (seconds) */
    private array $cacheTtl = [
        'users' => 300,               // 5 menit
        'pelanggan' => 600,           // 10 menit
        'pengaturan_tarif' => 3600,   // 1 jam
        'transaksi_tagihan' => 60,    // 1 menit
        'pengaturan_gateway' => 3600, // 1 jam
    ];

    public function __construct()
    {
        $this->spreadsheetId = config('services.google_sheets.spreadsheet_id');
        $this->service = $this->buildSheetsService();
    }

    private function buildSheetsService(): Sheets
    {
        $client = new Client;
        $httpClient = new \GuzzleHttp\Client(['verify' => false]);
        $client->setHttpClient($httpClient);
        $client->setApplicationName('SIMADES');
        $client->setScopes([Sheets::SPREADSHEETS]);

        $credentialPath = base_path(config('services.google_sheets.service_account_json'));
        $client->setAuthConfig($credentialPath);

        return new Sheets($client);
    }

    /**
     * Ambil semua baris dari sebuah sheet.
     *
     * @return array<int, array<string, string>>
     */
    public function getSheet(string $sheetName, bool $useCache = true): array
    {
        $cacheKey = "sheets:{$sheetName}";
        $ttl = $this->cacheTtl[$sheetName] ?? 300;

        if ($useCache) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        try {
            $response = $this->service->spreadsheets_values->get(
                $this->spreadsheetId,
                $sheetName
            );

            $values = $response->getValues() ?? [];

            if (empty($values)) {
                return [];
            }

            $headers = array_shift($values);
            $rows = [];

            foreach ($values as $row) {
                $padded = array_pad($row, count($headers), '');
                $rows[] = array_combine($headers, $padded);
            }

            if ($useCache) {
                Cache::put($cacheKey, $rows, $ttl);
            }

            return $rows;
        } catch (\Throwable $e) {
            Log::error("GoogleSheets getSheet [{$sheetName}] error: ".$e->getMessage());

            return [];
        }
    }

    /**
     * Tambah baris baru di bawah (append).
     *
     * @param  array<string, string>  $data
     */
    public function appendRow(string $sheetName, array $data): bool
    {
        try {
            $headers = $this->getHeaders($sheetName);
            $rowValues = [];

            foreach ($headers as $header) {
                $rowValues[] = $data[$header] ?? '';
            }

            $valueRange = new ValueRange;
            $valueRange->setValues([$rowValues]);

            $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                $sheetName,
                $valueRange,
                ['valueInputOption' => 'RAW']
            );

            $this->clearCache($sheetName);

            return true;
        } catch (\Throwable $e) {
            Log::error("GoogleSheets appendRow [{$sheetName}] error: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Update satu baris berdasarkan nomor baris (1-indexed, row 1 = header).
     *
     * @param  array<string, string>  $data
     */
    public function updateRow(string $sheetName, int $rowNumber, array $data): bool
    {
        try {
            $headers = $this->getHeaders($sheetName);
            $rowValues = [];

            foreach ($headers as $header) {
                $rowValues[] = $data[$header] ?? '';
            }

            $range = "{$sheetName}!A{$rowNumber}";
            $valueRange = new ValueRange;
            $valueRange->setValues([$rowValues]);

            $this->service->spreadsheets_values->update(
                $this->spreadsheetId,
                $range,
                $valueRange,
                ['valueInputOption' => 'RAW']
            );

            $this->clearCache($sheetName);

            return true;
        } catch (\Throwable $e) {
            Log::error("GoogleSheets updateRow [{$sheetName}] row {$rowNumber} error: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Update satu cell tertentu.
     */
    public function updateCell(string $sheetName, string $cellRange, string $value): bool
    {
        try {
            $valueRange = new ValueRange;
            $valueRange->setValues([[$value]]);

            $this->service->spreadsheets_values->update(
                $this->spreadsheetId,
                "{$sheetName}!{$cellRange}",
                $valueRange,
                ['valueInputOption' => 'RAW']
            );

            $this->clearCache($sheetName);

            return true;
        } catch (\Throwable $e) {
            Log::error("GoogleSheets updateCell [{$sheetName}!{$cellRange}] error: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Cari baris berdasarkan nilai kolom tertentu.
     *
     * @return array{data: array<string, string>, rowIndex: int}|null
     */
    public function findRow(string $sheetName, string $column, string $value): ?array
    {
        $rows = $this->getSheet($sheetName);

        foreach ($rows as $index => $row) {
            if (isset($row[$column]) && trim((string) $row[$column]) === trim((string) $value)) {
                return [
                    'data' => $row,
                    'rowIndex' => $index + 2, // +2 karena baris 1 = header, index 0 = baris 2
                ];
            }
        }

        return null;
    }

    /**
     * Hapus baris dari sheet berdasarkan nomor baris (1-indexed, row 1 = header).
     */
    public function deleteRow(string $sheetName, int $rowNumber): bool
    {
        try {
            // Ambil sheetId dari spreadsheet
            $spreadsheet = $this->service->spreadsheets->get($this->spreadsheetId);
            $sheetId = null;

            foreach ($spreadsheet->getSheets() as $sheet) {
                if ($sheet->getProperties()->getTitle() === $sheetName) {
                    $sheetId = $sheet->getProperties()->getSheetId();
                    break;
                }
            }

            if ($sheetId === null) {
                Log::error("GoogleSheets deleteRow: sheet [{$sheetName}] tidak ditemukan.");

                return false;
            }

            $request = new Request([
                'deleteDimension' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'ROWS',
                        'startIndex' => $rowNumber - 1, // 0-indexed
                        'endIndex' => $rowNumber,       // exclusive
                    ],
                ],
            ]);

            $batchUpdateRequest = new BatchUpdateSpreadsheetRequest([
                'requests' => [$request],
            ]);

            $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
            $this->clearCache($sheetName);

            return true;
        } catch (\Throwable $e) {
            Log::error("GoogleSheets deleteRow [{$sheetName}] row {$rowNumber} error: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Hapus cache untuk sheet tertentu.
     */
    public function clearCache(string $sheetName): void
    {
        Cache::forget("sheets:{$sheetName}");
        Cache::forget("sheets:{$sheetName}:headers");
    }

    /**
     * Ambil header (baris pertama) dari sheet.
     *
     * @return array<int, string>
     */
    private function getHeaders(string $sheetName): array
    {
        $cacheKey = "sheets:{$sheetName}:headers";

        return Cache::remember($cacheKey, 3600, function () use ($sheetName) {
            $response = $this->service->spreadsheets_values->get(
                $this->spreadsheetId,
                "{$sheetName}!1:1"
            );

            return $response->getValues()[0] ?? [];
        });
    }
}
