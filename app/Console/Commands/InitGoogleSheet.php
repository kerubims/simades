<?php

namespace App\Console\Commands;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\Request as GoogleSheetsRequest;
use Google\Service\Sheets\AddSheetRequest;
use Google\Service\Sheets\SheetProperties;
use Google\Service\Sheets\ValueRange;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class InitGoogleSheet extends Command
{
    protected $signature = 'simades:init-sheet';
    protected $description = 'Inisialisasi struktur dan header tabel Google Sheets untuk SIMADES';

    public function handle()
    {
        $this->info('Memulai inisialisasi Google Sheets...');
        
        $spreadsheetId = config('services.google_sheets.spreadsheet_id');
        $credentialPath = base_path(config('services.google_sheets.service_account_json'));

        if (!$spreadsheetId || !file_exists($credentialPath)) {
            $this->error('Spreadsheet ID atau file credential JSON tidak ditemukan.');
            return;
        }

        $client = new Client();
        $httpClient = new \GuzzleHttp\Client(['verify' => false]);
        $client->setHttpClient($httpClient);
        $client->setApplicationName('SIMADES');
        $client->setScopes([Sheets::SPREADSHEETS]);
        $client->setAuthConfig($credentialPath);
        $service = new Sheets($client);

        // Ambil info spreadsheet saat ini untuk mengecek sheet apa saja yang sudah ada
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);
        $existingSheets = [];
        foreach ($spreadsheet->getSheets() as $sheet) {
            $existingSheets[] = $sheet->getProperties()->getTitle();
        }

        $sheetsToCreate = [
            'users' => ['id_user', 'username', 'password', 'role'],
            'pelanggan' => ['id_pelanggan', 'id_user', 'nama_lengkap', 'nik', 'no_kk', 'rt', 'rw', 'no_whatsapp', 'status_aktif'],
            'pengaturan_tarif' => ['komponen', 'nominal'],
            'transaksi_tagihan' => ['id_tagihan', 'id_pelanggan', 'periode_bulan', 'periode_tahun', 'meter_awal', 'meter_akhir', 'total_pemakaian_m3', 'total_tagihan', 'status_bayar', 'link_pdf']
        ];

        $requests = [];
        foreach ($sheetsToCreate as $title => $headers) {
            if (!in_array($title, $existingSheets)) {
                $addSheetRequest = new AddSheetRequest();
                $properties = new SheetProperties();
                $properties->setTitle($title);
                $addSheetRequest->setProperties($properties);
                
                $req = new GoogleSheetsRequest();
                $req->setAddSheet($addSheetRequest);
                $requests[] = $req;
            }
        }

        // 1. Buat sheet yang belum ada
        if (!empty($requests)) {
            $batchUpdateRequest = new BatchUpdateSpreadsheetRequest([
                'requests' => $requests
            ]);
            $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
            $this->info('Berhasil membuat sheet baru: ' . count($requests));
        } else {
            $this->info('Semua sheet sudah tersedia.');
        }

        // 2. Set Header untuk masing-masing sheet
        foreach ($sheetsToCreate as $title => $headers) {
            $body = new ValueRange([
                'values' => [$headers]
            ]);
            $params = [
                'valueInputOption' => 'RAW'
            ];
            $service->spreadsheets_values->update($spreadsheetId, "{$title}!A1", $body, $params);
            $this->info("Header di-set untuk sheet: {$title}");
        }

        // 3. Masukkan data default Admin jika users kosong (atau baris 2 kosong)
        $usersData = $service->spreadsheets_values->get($spreadsheetId, "users!A2:D2")->getValues();
        if (empty($usersData)) {
            $adminPassword = Hash::make('admin123');
            $body = new ValueRange([
                'values' => [
                    ['U001', 'admin', $adminPassword, 'admin']
                ]
            ]);
            $service->spreadsheets_values->update($spreadsheetId, "users!A2", $body, ['valueInputOption' => 'RAW']);
            $this->info('Data default Admin berhasil dibuat (username: admin, password: admin123)');
        }

        // 4. Masukkan data pengaturan_tarif jika kosong
        $tarifData = $service->spreadsheets_values->get($spreadsheetId, "pengaturan_tarif!A2:B4")->getValues();
        if (empty($tarifData)) {
            $body = new ValueRange([
                'values' => [
                    ['air_per_m3', '500'],
                    ['beban_sampah', '10000'],
                    ['dana_kematian', '2000']
                ]
            ]);
            $service->spreadsheets_values->update($spreadsheetId, "pengaturan_tarif!A2", $body, ['valueInputOption' => 'RAW']);
            $this->info('Data default pengaturan_tarif berhasil dibuat.');
        }

        $this->info('Inisialisasi Google Sheets selesai.');
    }
}
