# SIMADES - Sistem Informasi Manajemen Air Desa

SIMADES adalah aplikasi sistem tagihan dan pengelolaan air bersih desa berbasis Web. Sistem ini dibangun dengan teknologi modern yang menggunakan **Google Sheets** sebagai database utama (untuk kemudahan kolaborasi warga/perangkat desa) dan mengintegrasikan **WhatsApp Gateway Node.js** untuk pengiriman bukti slip pembayaran secara otomatis.

## 🛠️ Stack Teknologi

- **Framework:** Laravel 13 (PHP)
- **Frontend:** Tailwind CSS (via Vite)
- **Database Utama:** Google Sheets API
- **Background Job & Queue:** MySQL
- **WhatsApp Gateway:** Node.js + `@whiskeysockets/baileys`
- **PDF Generator:** `barryvdh/laravel-dompdf` (Format A5)

---

## 🚀 Panduan Menjalankan Pertama Kali (Local Development)

### 1. Persyaratan Sistem

Pastikan perangkat Anda sudah terinstall:

- PHP >= 8.2
- Composer
- Node.js & npm
- Server Local (Laragon / XAMPP) yang memiliki MySQL aktif.

### 2. Instalasi Aplikasi Laravel

1. Clone / unduh repositori ini.
2. Buka terminal di dalam folder proyek, lalu jalankan instalasi dependensi PHP:
    ```bash
    composer install
    ```
3. Copy file environment dan buat App Key:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4. Install dan compile dependensi frontend (Tailwind):
    ```bash
    npm install
    npm run build
    ```

### 3. Konfigurasi Environment (`.env`)

Buka file `.env` dan atur konfigurasi berikut:

**A. Konfigurasi Database (Untuk Laravel Queue)**
Sistem menggunakan MySQL murni untuk antrean pengiriman pesan WA.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simades
DB_USERNAME=root
DB_PASSWORD=
```

_(Pastikan Anda membuat database kosong bernama `simades` di PhpMyAdmin / HeidiSQL)._

**B. Konfigurasi API Eksternal**
Masukkan ID Google Sheet dan kredensial.

```env
GOOGLE_SHEETS_SPREADSHEET_ID="11JY9KaXJTSlLv9HM1TtuQnTAaKzaFukdQaA6CE_nQ1c"
GOOGLE_SERVICE_ACCOUNT_JSON="credential/sistem-air-desa.json"
WHATSAPP_GATEWAY_URL="http://localhost:9005" (pastikan tidak bentrok dengan port lain).
```

### 4. Setup Google Sheets Database

1. Letakkan file kredensial JSON Google Service Account Anda ke dalam folder `credential/` (buat foldernya jika belum ada). Pastikan namanya sesuai dengan yang ditulis di `.env`.
2. Jangan lupa **bagikan (share)** Google Sheet Anda ke email _Service Account_ tersebut sebagai `Editor`.
3. Jalankan migrasi database MySQL (untuk sistem Queue):
    ```bash
    php artisan migrate
    ```
4. Jalankan perintah otomatisasi untuk membuat struktur tabel (Header) di Google Sheets Anda:
    ```bash
    php artisan simades:init-sheet
    ```
    _(Perintah ini akan membuat sheet: `users`, `pelanggan`, `pengaturan_tarif`, `transaksi_tagihan` dan data Admin default)._

### 5. Setup WhatsApp Gateway

Buka tab terminal baru, masuk ke folder gateway, dan install modul Node.js:

```bash
cd whatsapp-gateway
npm install
```

---

## 🏃 Cara Menjalankan Server Sehari-hari

Untuk menjalankan aplikasi secara utuh di komputer lokal, Anda butuh menghidupkan 3 layanan secara bersamaan (gunakan 3 tab Terminal):

1. **Terminal 1 (Jalankan Gateway WA):**
    ```bash
    cd whatsapp-gateway
    npm start
    ```
2. **Terminal 2 (Jalankan Web Server):**   
    ```bash
    php artisan serve
    ```
3. **Terminal 3 (Jalankan Background Worker):**
   Sangat penting agar proses generate PDF dan kirim WA berjalan otomatis.
    ```bash
    php artisan queue:work
    ```
npm run dev
> **Catatan Login:**
> Setelah jalan, login ke aplikasi menggunakan default admin:
> **Username:** `admin` | **Password:** `admin123`
> Kemudian scan QR Code WhatsApp di menu "Gateway WA" panel Admin.
> Untuk Warga, login bisa menggunakan **Username**, **NIK**, atau **No. KK**.

---

## 💡 Tips & Troubleshooting

1. **Vite Frontend (Development):**
   Jika Anda sedang mengembangkan/mengubah tampilan UI, tambahkan satu Terminal lagi untuk menjalankan:
   ```bash
   npm run dev
   ```
2. **Cache Google Sheets:**
   Aplikasi ini melakukan *cache* (penyimpanan sementara) pada data Google Sheets selama 10 menit untuk mempercepat proses. Jika Anda baru saja merombak struktur kolom/Header di Google Sheets secara manual atau via perintah `simades:init-sheet`, Anda wajib menjalankan perintah ini agar aplikasi tidak membaca data usang:
   ```bash
   php artisan cache:clear
   ```
3. **WhatsApp Sesi Kedaluwarsa:**
   Gateway telah dilengkapi fitur versi otomatis. Namun jika sewaktu-waktu akun Anda terputus paksa dari HP dan terminal terus-menerus *error*, segera matikan terminal, hapus folder `whatsapp-gateway/auth_info_baileys`, dan jalankan `npm start` lagi untuk men-*scan* ulang QR Code.

---

## 🌍 Panduan Deploy ke Server (Production)

Untuk melakukan _hosting_ aplikasi secara nyata (misal di VPS berbasis Ubuntu Server):

### 1. Persiapan Server

- Install PHP 8.2+, Nginx/Apache, MySQL, Composer, dan Node.js.
- Arahkan Root / Document Root domain server Nginx ke folder `/public` aplikasi.

### 2. Optimasi Laravel

Saat mendeploy, atur environment menjadi production dan jalankan optimasi:

```bash
# Ubah .env
APP_ENV=production
APP_DEBUG=false

# Install dependency tanpa paket dev
composer install --optimize-autoloader --no-dev
npm run build

# Cache sistem
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Menjalankan Queue Secara Permanen dengan Supervisor

Di server _production_, `php artisan queue:work` harus berjalan terus meskipun terminal ditutup. Gunakan **Supervisor** (Linux):

1. `sudo apt install supervisor`
2. Buat konfigurasi baru: `sudo nano /etc/supervisor/conf.d/simades-worker.conf`
3. Isi dengan:
    ```ini
    [program:simades-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /path/ke/folder/simades/artisan queue:work --sleep=3 --tries=3 --max-time=3600
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    user=www-data
    numprocs=1
    redirect_stderr=true
    stdout_logfile=/path/ke/folder/simades/storage/logs/worker.log
    stopwaitsecs=3600
    ```
4. Aktifkan worker:
    ```bash
    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start simades-worker:*
    ```

### 4. Menjalankan WhatsApp Gateway Secara Permanen dengan PM2

Agar Node.js WhatsApp tidak mati saat terminal server ditutup:

1. Install PM2 secara global:
    ```bash
    npm install -g pm2
    ```
2. Jalankan gateway dengan PM2:
    ```bash
    cd /path/ke/folder/simades/whatsapp-gateway
    pm2 start index.js --name "whatsapp-simades"
    ```
3. Buat PM2 otomatis jalan saat server _reboot_:
    ```bash
    pm2 startup
    pm2 save
    ```

🎉 **Aplikasi SIMADES siap melayani warga desa selama 24/7 di server Production!**
