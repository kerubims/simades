<!doctype html>
<html lang="id" class="scroll-smooth">
    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>Digitalisasi Air Dusun Tambak</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap"
            rel="stylesheet"
        />
        <style>
            .material-symbols-outlined {
                font-variation-settings:
                    "FILL" 0,
                    "wght" 400,
                    "GRAD" 0,
                    "opsz" 24;
            }
            .fill-icon {
                font-variation-settings:
                    "FILL" 1,
                    "wght" 400,
                    "GRAD" 0,
                    "opsz" 24;
            }
            .ambient-shadow {
                box-shadow: 0 4px 12px rgba(0, 103, 103, 0.05);
            }
            .ambient-shadow-hover:hover {
                box-shadow: 0 8px 24px rgba(0, 103, 103, 0.1);
            }
        </style>
    </head>
    <body
        class="bg-background text-on-background font-body-md min-h-screen flex flex-col antialiased"
    >
        <!-- TopNavBar -->
        <header
            class="bg-white shadow-sm sticky top-0 z-50"
        >
            <div
                class="flex justify-between items-center w-full px-margin py-base max-w-7xl mx-auto"
            >
                <div class="flex items-center gap-sm">
                    <span
                        class="material-symbols-outlined text-primary fill-icon text-3xl"
                        >water_drop</span
                    >
                    <span
                        class="font-headline-md text-headline-md font-bold text-primary"
                        >Dusun Tambak</span
                    >
                </div>
                <nav class="hidden md:flex gap-md font-label-lg text-label-lg">
                    <a class="text-on-surface-variant hover:text-primary transition-colors duration-200" href="#beranda">Beranda</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors duration-200" href="#masalah-kertas">Masalah Kertas</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors duration-200" href="#alur-digital">Alur Digital</a>
                    <a class="text-on-surface-variant hover:text-primary transition-colors duration-200" href="#kontak">Kontak</a>
                </nav>
                <div class="flex items-center gap-sm">
                    <a
                        href="{{ route('login') }}"
                        class="font-label-lg text-label-lg text-primary hover:bg-surface-variant px-4 py-2 rounded-lg transition-colors"
                    >
                        Masuk
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="font-label-lg text-label-lg bg-primary text-on-primary px-4 py-2 rounded-lg hover:opacity-90 transition-opacity"
                    >
                        Daftar
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col">
            <!-- Hero Section -->
            <section id="beranda" class="w-full px-margin py-xl max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-xl">
                <div class="flex-1 flex flex-col gap-md text-center lg:text-left">
                    <h1 class="font-display-lg text-display-lg text-on-surface">
                        Menuju Dusun Tambak Digitalisasi
                    </h1>
                    <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto lg:mx-0">
                        Lebih Mudah & Transparan untuk Warga Desa Lemahbang, Dusun Tambak RT/RW 01/09. Kelola air bersih Anda tanpa coretan kertas yang membingungkan.
                    </p>
                    <div class="pt-sm flex justify-center lg:justify-start">
                        <a
                            href="{{ route('login') }}"
                            class="bg-primary text-on-primary font-label-lg text-label-lg px-8 py-4 rounded-lg hover:opacity-90 transition-opacity shadow-sm flex items-center gap-sm"
                        >
                            <span>Ayo Go-Digital!</span>
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <div class="flex-1 w-full max-w-md lg:max-w-none relative">
                    <div class="absolute inset-0 bg-primary-container rounded-3xl rotate-3 opacity-20 transform scale-105"></div>
                    <img alt="Villager using smartphone" class="w-full h-auto rounded-3xl shadow-lg relative z-10 object-cover aspect-[4/3] border border-outline-variant/30" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD4NRbW_zjp1RtbNTVOBEIHuSmxpDUMs52KhoSZTF3WNcLSOfxHTavgaJF1Ys-z7tzAeegayIy7lJLfSsr9xIx9mYUks1ptD3lqPLjDkR7okDN6t9KIwJDQAh9pegGqp9u_PYKXkPlIK-wg03nl2dRZhBarDMNkBLXMI231QUzUTo9AnpTjB2U0F3YO3YaPl3e8wr8-2HeWE32SYbpYU8YGjbY8KdHgzsJPmgsAfSaPp5QXV2cieRGTBtt3U4WSbx9MKozoFapCqLc" />
                </div>
            </section>

            <!-- Features Section -->
            <section id="masalah-kertas" class="w-full px-margin py-xl bg-surface-container-low mt-lg rounded-t-[3rem]">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-xl">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface">Perbandingan Kertas vs Digital</h2>
                        <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Kenapa Dusun Tambak perlu berubah menggunakan struk digital.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
                        <div class="bg-surface rounded-2xl p-lg ambient-shadow ambient-shadow-hover transition-all duration-300 border border-outline-variant/20 flex flex-col items-center text-center group">
                            <div class="w-16 h-16 rounded-full bg-error-container/20 flex items-center justify-center mb-md group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-error text-3xl">receipt_long</span>
                            </div>
                            <h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Masalah Kertas Lama</h3>
                            <p class="font-body-md text-body-md text-on-surface-variant">Angka sering dihitung manual dan membingungkan. Tulisan tangan sulit dibaca, kertas mudah robek, dan gampang hilang.</p>
                        </div>
                        <div class="bg-surface rounded-2xl p-lg ambient-shadow ambient-shadow-hover transition-all duration-300 border border-outline-variant/20 flex flex-col items-center text-center group">
                            <div class="w-16 h-16 rounded-full bg-primary-container/20 flex items-center justify-center mb-md group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-primary text-3xl">chat</span>
                            </div>
                            <h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Struk via WhatsApp</h3>
                            <p class="font-body-md text-body-md text-on-surface-variant">Sistem menghitung tagihan otomatis dan akurat. Warga langsung menerima struk digital yang rapi di HP melalui WhatsApp.</p>
                        </div>
                        <div class="bg-surface rounded-2xl p-lg ambient-shadow ambient-shadow-hover transition-all duration-300 border border-outline-variant/20 flex flex-col items-center text-center group">
                            <div class="w-16 h-16 rounded-full bg-tertiary-container/20 flex items-center justify-center mb-md group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-tertiary text-3xl">verified_user</span>
                            </div>
                            <h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Data Aman & Mudah Dicari</h3>
                            <p class="font-body-md text-body-md text-on-surface-variant">Semua riwayat pemakaian air tersimpan rapi dan aman di database. Struk dapat diakses kapan saja diperlukan.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Alur Proses Section -->
            <section id="alur-digital" class="w-full px-margin py-xl max-w-7xl mx-auto">
                <div class="text-center mb-xl">
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Langkah Mudah Menuju Struk Digital</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Alur proses digitalisasi untuk kenyamanan warga desa.</p>
                </div>
                <div class="flex flex-col md:flex-row gap-xl justify-center items-stretch mt-10">
                    <!-- Step 1 -->
                    <div class="flex-1 bg-surface rounded-2xl p-lg relative border border-outline-variant/30 ambient-shadow">
                        <div class="absolute -top-6 left-8 w-12 h-12 bg-primary text-on-primary rounded-full flex items-center justify-center font-bold text-xl shadow-md">01</div>
                        <h3 class="font-headline-md text-headline-md text-on-surface mt-sm mb-2 text-primary">Pencatatan & Input Data</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant">Staf desa mencatat meteran air di setiap rumah warga, lalu data diinput ke sistem komputer dengan mudah dan cepat.</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="flex-1 bg-surface rounded-2xl p-lg relative border border-outline-variant/30 ambient-shadow">
                        <div class="absolute -top-6 left-8 w-12 h-12 bg-primary text-on-primary rounded-full flex items-center justify-center font-bold text-xl shadow-md">02</div>
                        <h3 class="font-headline-md text-headline-md text-on-surface mt-sm mb-2 text-primary">Perhitungan & Pengiriman</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant">Sistem menghitung tagihan secara otomatis dan akurat. Warga langsung menerima struk digital di HP melalui WhatsApp.</p>
                    </div>
                </div>
            </section>

            <!-- Contoh Struk Section -->
            <section id="kontak" class="w-full px-margin py-xl bg-surface-container-high rounded-3xl mb-lg max-w-7xl mx-auto">
                <div class="flex flex-col lg:flex-row items-center gap-xl px-4 md:px-8">
                    <div class="flex-1 text-center lg:text-left">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Transparansi Tampilan Struk Digital</h2>
                        <p class="font-body-lg text-body-lg text-on-surface-variant mb-6">Mulai sekarang, format tagihan air Anda akan dikirimkan dengan rincian yang jelas. Tidak ada lagi angka coretan yang membingungkan.</p>
                        <ul class="text-left font-body-md text-on-surface-variant space-y-4 inline-block lg:block">
                            <li class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">check_circle</span> 
                                Rincian meter awal, akhir, dan pemakaian kubikasi
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">check_circle</span> 
                                Transparansi iuran dana sosial & operasional sampah
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">check_circle</span> 
                                Status pembayaran tercatat sistem secara otomatis
                            </li>
                        </ul>
                    </div>
                    
                    <div class="flex-1 w-full max-w-md mx-auto">
                        <!-- Mockup WhatsApp Receipt -->
                        <div class="bg-surface rounded-2xl shadow-xl border border-outline-variant/30 p-6 md:p-8">
                            <div class="text-center border-b border-outline-variant/30 pb-4 mb-5">
                                <h4 class="font-headline-md font-bold text-on-surface text-primary">Struk Air Bersih</h4>
                                <p class="text-on-surface-variant font-medium">Mei 2026</p>
                            </div>
                            <div class="space-y-3 font-body-md text-on-surface">
                                @php
                                    $pemakaian = 65;
                                    $totalAir = $pemakaian * $tarif->airPerM3;
                                    $totalBayar = $totalAir + $tarif->danaKematian + $tarif->bebanSampah + $tarif->biayaLampuJalan;
                                @endphp
                                <div class="flex justify-between font-semibold text-lg text-primary"><span>Nama:</span> <span>Pak Kasun</span></div>
                                <div class="flex justify-between text-on-surface-variant"><span>Meter Awal:</span> <span>04383 m³</span></div>
                                <div class="flex justify-between text-on-surface-variant"><span>Meter Akhir:</span> <span>04448 m³</span></div>
                                <div class="flex justify-between text-on-surface-variant"><span>Pemakaian:</span> <span>065 m³</span></div>
                                <div class="flex justify-between border-b border-outline-variant/20 pb-3 text-on-surface-variant"><span>Harga m³:</span> <span>Rp {{ number_format($tarif->airPerM3, 0, ',', '.') }}</span></div>
                                
                                <div class="flex justify-between pt-2"><span>Total Air:</span> <span>Rp {{ number_format($totalAir, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Dana Sosial:</span> <span>Rp {{ number_format($tarif->danaKematian, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Dana Sampah:</span> <span>Rp {{ number_format($tarif->bebanSampah, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between border-b border-outline-variant/20 pb-3"><span>Lampu Jalan:</span> <span>Rp {{ number_format($tarif->biayaLampuJalan, 0, ',', '.') }}</span></div>
                                
                                <div class="flex justify-between pt-3 font-bold text-xl text-on-surface"><span>Total Bayar:</span> <span>Rp {{ number_format($totalBayar, 0, ',', '.') }}</span></div>
                                
                                <div class="mt-6 bg-tertiary-container/20 text-tertiary p-3 rounded-lg text-center font-bold flex justify-center items-center gap-2">
                                    <span class="material-symbols-outlined">verified</span> Status: Sudah Lunas
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-surface-container-low mt-auto">
            <div class="w-full px-margin py-lg flex flex-col md:flex-row justify-between items-center gap-md max-w-7xl mx-auto">
                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined text-primary fill-icon text-2xl">home</span>
                    <span class="font-headline-sm text-headline-sm font-bold text-primary">Dusun Tambak</span>
                </div>
                <p class="font-body-md text-body-md text-on-surface text-center md:text-left">
                    © 2026 Dusun Tambak RT/RW 01/09. Desa Lemahbang.
                </p>
                <div class="flex flex-wrap justify-center gap-md font-body-md text-body-md">
                    <a class="text-on-surface-variant hover:underline decoration-primary focus:ring-2 focus:ring-primary rounded flex items-center gap-2" href="mailto:kantor.desa@lemahbang.id">
                        <span class="material-symbols-outlined text-sm">mail</span>
                        kantor.desa@lemahbang.id
                    </a>
                </div>
            </div>
        </footer>
    </body>
</html>
