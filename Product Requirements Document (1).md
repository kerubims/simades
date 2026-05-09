# **Product Requirements Document (PRD)**

**Nama Produk:** Sistem Informasi Manajemen Air Desa (SIMADES)

**Platform:** Web Application (Responsive)

**Tech Stack:** Laravel, Google Spreadsheets API, WhatsApp API Gateway, DOMPDF

## **1\. Pendahuluan**

### **1.1 Latar Belakang**

Sistem pencatatan pemakaian air bersih di desa saat ini masih menggunakan metode manual dengan slip kertas. Hal ini rentan terhadap kesalahan hitung, risiko kehilangan data, tidak efisien dalam penggunaan kertas, dan kurangnya transparansi bagi warga.

### **1.2 Tujuan Produk**

Membangun sistem berbasis web untuk mendigitalisasi proses pencatatan meteran air, mengotomatisasi perhitungan tagihan (termasuk biaya tetap seperti sampah dan dana kematian), dan mengirimkan slip tagihan digital (PDF) langsung ke WhatsApp warga.

## **2\. Target Pengguna (Role)**

1. **Admin / Petugas:** Perangkat desa atau petugas lapangan yang bertugas mencatat meteran air bulanan, mengelola data warga, dan memantau status pembayaran.  
2. **Warga / Pelanggan:** Penduduk desa pemakai layanan air yang perlu melihat jumlah tagihan, riwayat pemakaian, dan mengunduh slip digital.

## **3\. Arsitektur & Teknologi Database (Pure Google Sheets)**

Sistem akan menggunakan **1 File Google Spreadsheet** yang bertindak sebagai database relasional, yang terdiri dari beberapa *Sheet* (Tab). Laravel akan berkomunikasi menggunakan Google API.

### **Struktur Data (Sheet/Tabel)**

**Sheet 1: users** (Untuk Autentikasi Login)

* id\_user (Auto-increment string, misal: U001)  
* username (Bisa berupa NIK atau ID Pelanggan)  
* password (Password yang di-hash menggunakan Bcrypt Laravel demi keamanan, JANGAN simpan plain text)  
* role (admin / warga)

**Sheet 2: pelanggan** (Data Master Warga)

* id\_pelanggan (Misal: PLG001)  
* id\_user (Relasi ke sheet users)  
* nama\_lengkap  
* rt\_rw  
* no\_whatsapp (Format: 628xxx)  
* status\_aktif (Aktif / Non-Aktif)

**Sheet 3: pengaturan\_tarif** (Data Master Tarif)

* komponen (Air\_per\_m3, Beban\_Sampah, Dana\_Kematian)  
* nominal (Misal: 500, 10000, 2000\)

**Sheet 4: transaksi\_tagihan** (Data Utama)

* id\_tagihan (Misal: INV-202310-PLG001)  
* id\_pelanggan  
* periode\_bulan  
* periode\_tahun  
* meter\_awal (Diambil dari meter\_akhir bulan sebelumnya)  
* meter\_akhir (Diinput petugas)  
* total\_pemakaian\_m3 (Otomatis: Akhir \- Awal)  
* total\_tagihan (Otomatis: (Pemakaian \* Tarif\_Air) \+ Beban\_Sampah \+ Dana\_Kematian)  
* status\_bayar (Belum Bayar / Lunas)  
* link\_pdf (URL tempat PDF tersimpan di server)

## **4\. Fitur Utama**

### **4.1 Modul Autentikasi (Sistem Login via Sheets)**

* **Login Multi-Role:** User memasukkan username dan password. Laravel akan mengambil data (Fetch) dari Sheet users, mencocokkan username, dan memverifikasi password menggunakan Hash::check(). Jika valid, user masuk ke dashboard sesuai role.  
* **Logout:** Mengakhiri sesi pengguna.

### **4.2 Fitur Admin / Petugas**

1. **Dashboard Admin:**  
   * Menampilkan total pendapatan bulan berjalan, jumlah warga yang belum/sudah bayar, dan total air yang didistribusikan.  
2. **Kelola Pelanggan:**  
   * CRUD (Create, Read, Update, Delete) data warga. Menambah warga otomatis membuatkan akun di Sheet users.  
3. **Input Pencatatan (Core Feature):**  
   * Petugas memilih bulan & tahun, lalu melihat daftar warga.  
   * Petugas hanya memasukkan **Meter Akhir**.  
   * *Sistem otomatis menghitung* selisih meter dan total biaya.  
4. **Generate & Broadcast WA:**  
   * Tombol "Kirim Tagihan". Sistem menggunakan DOMPDF untuk membuat PDF layout slip (seperti foto referensi).  
   * Sistem mengirim pesan WA \+ lampiran PDF (atau link PDF) ke no\_whatsapp warga.  
5. **Update Pembayaran:**  
   * Tombol untuk merubah status "Belum Bayar" menjadi "Lunas".

### **4.3 Fitur Warga**

1. **Dashboard Warga:**  
   * Menampilkan rincian tagihan bulan berjalan (Sama persis dengan rincian di slip fisik).  
   * Status pembayaran (Lunas/Belum).  
2. **Riwayat Pemakaian:**  
   * Tabel yang berisi riwayat meteran dan tagihan bulan-bulan sebelumnya.  
   * Grafik batang pemakaian air (m3) selama 6 bulan terakhir.  
3. **Unduh Slip:**  
   * Tombol untuk mengunduh slip tagihan dalam format PDF kapan saja.

## **5\. Alur Sistem (User Flow)**

**Alur Pencatatan & Penagihan Bulanan:**

1. Petugas login ke sistem melalui HP/Tablet.  
2. Petugas memilih menu "Catat Meteran".  
3. Petugas mendatangi rumah "Pak Kasun", membaca meteran air (misal: 1045).  
4. Petugas input angka 1045 di sistem.  
5. Sistem mengambil meter awal bulan lalu (misal: 1025). Sistem menghitung pemakaian: 20m3.  
6. Sistem memproses: (20 \* Rp500) \+ Rp10.000 (Sampah) \+ Rp2.000 (Kematian) \= Rp22.000.  
7. Petugas menekan "Simpan & Kirim".  
8. Data tersimpan di *Google Sheets*. PDF ter-generate. Warga otomatis menerima notifikasi WhatsApp.

## **6\. Integrasi & Pihak Ketiga**

1. **Google Sheets API:** Menggunakan service account JSON key untuk Read/Write ke Spreadsheet.  
2. **WhatsApp Gateway:** Menggunakan vendor pihak ketiga (contoh: Fonnte, Wablas, atau Watzap) untuk menyediakan endpoint pengiriman pesan dokumen/teks.  
3. **Laravel DOMPDF:** Library internal untuk merender view HTML/CSS (desain slip) menjadi file .pdf.

## **7\. Batasan, Asumsi & Mitigasi Risiko**

**PENTING: Karena menggunakan Google Sheets murni sebagai Database, ada batasan teknis yang harus dikelola oleh sistem Laravel:**

1. **Limitasi API Google (Rate Limit):**  
   * *Risiko:* Google membatasi jumlah *request* API (sekitar 60 request per pengguna per menit).  
   * *Mitigasi:* Laravel **HARUS** menerapkan sistem *Caching* (misal menggunakan Redis atau File Cache Laravel). Data statis seperti *Tarif* dan *Data Pelanggan* di-cache selama beberapa jam, dan hanya melakukan sinkronisasi dengan Sheets jika ada perubahan.  
2. **Kecepatan Respons (Latency):**  
   * *Risiko:* Membaca data login dari Sheets lebih lambat dari MySQL.  
   * *Mitigasi:* Saat petugas menyimpan data meteran massal, gunakan sistem *Job/Queue* di Laravel. Data disimpan ke Sheets di latar belakang (*background process*), sehingga petugas tidak perlu loading lama menunggu di halaman web.  
3. **Keamanan Data Password:**  
   * *Risiko:* Jika ada yang membuka file Spreadsheet secara langsung, password warga bisa terlihat.  
   * *Mitigasi:* Laravel wajib melakukan enkripsi (Bcrypt) pada saat akun dibuat. Di Google Sheets, kolom password hanya akan berisi teks acak (hash) yang tidak bisa dibaca manusia.  
4. **Konkurensi (Data Tertimpa):**  
   * *Risiko:* Dua admin melakukan input di baris yang sama.  
   * *Mitigasi:* Menggunakan metode append() (menambah baris baru di bawah) pada Sheets API, bukan melakukan penulisan ulang ke baris yang sudah ada, kecuali untuk fungsi edit spesifik.

*Dibuat untuk: Digitalisasi Pengelolaan Air Desa*