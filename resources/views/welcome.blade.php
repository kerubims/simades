<!doctype html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>SIMADES - Solusi Pengelolaan Air Desa Modern</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap"
            rel="stylesheet"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet"
        />
        <script id="tailwind-config">
            tailwind.config = {
                darkMode: "class",
                theme: {
                    extend: {
                        colors: {
                            "on-error-container": "#93000a",
                            "on-surface-variant": "#3d4949",
                            error: "#ba1a1a",
                            "on-tertiary-fixed-variant": "#00522d",
                            "surface-container": "#eeeef0",
                            "tertiary-fixed-dim": "#7ed99e",
                            "on-tertiary": "#ffffff",
                            secondary: "#006a65",
                            "inverse-surface": "#2f3133",
                            "on-secondary-fixed-variant": "#00504c",
                            outline: "#6d7979",
                            "primary-fixed-dim": "#6fd7d6",
                            "surface-tint": "#006a6a",
                            "outline-variant": "#bcc9c8",
                            "secondary-fixed-dim": "#59dad1",
                            "tertiary-container": "#268451",
                            surface: "#f9f9fc",
                            "surface-variant": "#e2e2e5",
                            "on-primary-fixed": "#002020",
                            "primary-container": "#008282",
                            background: "#f9f9fc",
                            "on-surface": "#1a1c1e",
                            "tertiary-fixed": "#9af6b8",
                            "inverse-on-surface": "#f0f0f3",
                            "surface-bright": "#f9f9fc",
                            "on-secondary": "#ffffff",
                            "on-tertiary-fixed": "#00210f",
                            "surface-container-lowest": "#ffffff",
                            "on-tertiary-container": "#f6fff4",
                            "primary-fixed": "#8cf3f3",
                            "surface-dim": "#dadadc",
                            "inverse-primary": "#6fd7d6",
                            "secondary-container": "#76f3ea",
                            "error-container": "#ffdad6",
                            "on-secondary-fixed": "#00201e",
                            "on-error": "#ffffff",
                            "secondary-fixed": "#79f6ed",
                            "surface-container-highest": "#e2e2e5",
                            primary: "#006767",
                            "on-primary-fixed-variant": "#004f4f",
                            "surface-container-high": "#e8e8ea",
                            "surface-container-low": "#f3f3f6",
                            tertiary: "#006a3b",
                            "on-secondary-container": "#006f69",
                            "on-primary": "#ffffff",
                            "on-background": "#1a1c1e",
                            "on-primary-container": "#f3fffe",
                        },
                        borderRadius: {
                            DEFAULT: "0.25rem",
                            lg: "0.5rem",
                            xl: "0.75rem",
                            full: "9999px",
                        },
                        spacing: {
                            md: "24px",
                            gutter: "16px",
                            base: "8px",
                            sm: "12px",
                            xs: "4px",
                            lg: "40px",
                            xl: "64px",
                            margin: "20px",
                        },
                        fontFamily: {
                            "headline-lg": ["Manrope"],
                            "label-sm": ["Inter"],
                            "body-md": ["Inter"],
                            "headline-md": ["Manrope"],
                            "body-lg": ["Inter"],
                            "display-lg": ["Manrope"],
                            "label-lg": ["Inter"],
                        },
                        fontSize: {
                            "headline-lg": [
                                "32px",
                                { lineHeight: "1.3", fontWeight: "600" },
                            ],
                            "label-sm": [
                                "12px",
                                { lineHeight: "1.4", fontWeight: "500" },
                            ],
                            "body-md": [
                                "16px",
                                { lineHeight: "1.6", fontWeight: "400" },
                            ],
                            "headline-md": [
                                "24px",
                                { lineHeight: "1.3", fontWeight: "600" },
                            ],
                            "body-lg": [
                                "18px",
                                { lineHeight: "1.6", fontWeight: "400" },
                            ],
                            "display-lg": [
                                "48px",
                                { lineHeight: "1.2", fontWeight: "700" },
                            ],
                            "label-lg": [
                                "14px",
                                {
                                    lineHeight: "1.4",
                                    letterSpacing: "0.02em",
                                    fontWeight: "600",
                                },
                            ],
                        },
                    },
                },
            };
        </script>
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
            class="bg-surface dark:bg-on-surface-variant shadow-sm sticky top-0 z-50"
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
                        class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed"
                        >SIMADES</span
                    >
                </div>
                <nav class="hidden md:flex gap-md font-label-lg text-label-lg">
                    <a
                        class="text-on-surface-variant dark:text-outline-variant hover:text-primary dark:hover:text-primary-fixed transition-colors duration-200"
                        href="#"
                        >Fitur</a
                    >
                    <a
                        class="text-on-surface-variant dark:text-outline-variant hover:text-primary dark:hover:text-primary-fixed transition-colors duration-200"
                        href="#"
                        >Riwayat</a
                    >
                    <a
                        class="text-on-surface-variant dark:text-outline-variant hover:text-primary dark:hover:text-primary-fixed transition-colors duration-200"
                        href="#"
                        >Pembayaran</a
                    >
                    <a
                        class="text-on-surface-variant dark:text-outline-variant hover:text-primary dark:hover:text-primary-fixed transition-colors duration-200"
                        href="#"
                        >Bantuan</a
                    >
                </nav>
                <div class="flex items-center gap-sm">
                    <a
                        href="{{ route('login') }}"
                        class="font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors inline-block px-4 py-2"
                    >
                        Masuk
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="font-label-lg text-label-lg bg-primary text-on-primary hover:bg-primary-container px-4 py-2 rounded-lg transition-colors inline-block ml-2"
                    >
                        Daftar
                    </a>
                </div>
            </div>
        </header>
        <!-- Main Content -->
        <main class="flex-grow flex flex-col">
            <!-- Hero Section -->
            <section
                class="w-full px-margin py-xl max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-xl"
            >
                <div
                    class="flex-1 flex flex-col gap-md text-center lg:text-left"
                >
                    <h1 class="font-display-lg text-display-lg text-on-surface">
                        Kemudahan Pantau Air Desa dalam Genggaman
                    </h1>
                    <p
                        class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto lg:mx-0"
                    >
                        Solusi digital modern untuk memantau penggunaan dan
                        pembayaran air desa dengan mudah dan transparan.
                        SIMADES hadir untuk mendukung kemajuan desa Anda.
                    </p>
                    <div class="pt-sm flex justify-center lg:justify-start">
                        <a
                            href="{{ route('login') }}"
                            class="bg-primary text-on-primary font-label-lg text-label-lg px-8 py-4 rounded-lg hover:opacity-90 transition-opacity shadow-sm flex items-center gap-sm"
                        >
                            <span>Cek Tagihan Anda</span>
                            <span class="material-symbols-outlined"
                                >arrow_forward</span
                            >
                        </a>
                    </div>
                </div>
                <div class="flex-1 w-full max-w-md lg:max-w-none relative">
                    <!-- Abstract Background Shape -->
                    <div
                        class="absolute inset-0 bg-primary-container rounded-3xl rotate-3 opacity-20 transform scale-105"
                    ></div>
                    <!-- Hero Image -->
                    <img
                        alt="Villager using smartphone"
                        class="w-full h-auto rounded-3xl shadow-lg relative z-10 object-cover aspect-[4/3] border border-outline-variant/30"
                        data-alt="A high-quality, professional photograph of a happy Indonesian villager smiling warmly while checking their water usage on a modern smartphone. The setting is a bright, sunlit outdoor village environment, conveying a modern and optimistic mood. The lighting is natural and vibrant, highlighting the clear screen of the device. The visual style aligns with a clean, modern digital-first aesthetic, incorporating subtle aqua and teal tones in the environment to match the SIMADES brand palette."
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuD4NRbW_zjp1RtbNTVOBEIHuSmxpDUMs52KhoSZTF3WNcLSOfxHTavgaJF1Ys-z7tzAeegayIy7lJLfSsr9xIx9mYUks1ptD3lqPLjDkR7okDN6t9KIwJDQAh9pegGqp9u_PYKXkPlIK-wg03nl2dRZhBarDMNkBLXMI231QUzUTo9AnpTjB2U0F3YO3YaPl3e8wr8-2HeWE32SYbpYU8YGjbY8KdHgzsJPmgsAfSaPp5QXV2cieRGTBtt3U4WSbx9MKozoFapCqLc"
                    />
                </div>
            </section>
            <!-- Features Section -->
            <section
                class="w-full px-margin py-xl bg-surface-container-low mt-lg rounded-t-[3rem]"
            >
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-xl">
                        <h2
                            class="font-headline-lg text-headline-lg text-on-surface"
                        >
                            Layanan Unggulan Kami
                        </h2>
                        <p
                            class="font-body-md text-body-md text-on-surface-variant mt-xs"
                        >
                            Sistem yang dirancang khusus untuk kebutuhan
                            pengelolaan air pedesaan.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
                        <!-- Feature Card 1 -->
                        <div
                            class="bg-surface rounded-2xl p-lg ambient-shadow ambient-shadow-hover transition-all duration-300 border border-outline-variant/20 flex flex-col items-center text-center group"
                        >
                            <div
                                class="w-16 h-16 rounded-full bg-primary-container/20 flex items-center justify-center mb-md group-hover:scale-110 transition-transform"
                            >
                                <span
                                    class="material-symbols-outlined text-primary text-3xl"
                                    >fact_check</span
                                >
                            </div>
                            <h3
                                class="font-headline-md text-headline-md text-on-surface mb-xs"
                            >
                                Notifikasi Otomatis
                            </h3>
                            <p
                                class="font-body-md text-body-md text-on-surface-variant"
                            >
                                Terima bukti tagihan digital langsung ke WhatsApp Anda setiap bulannya. Cepat dan ramah lingkungan.
                            </p>
                        </div>
                        <!-- Feature Card 2 -->
                        <div
                            class="bg-surface rounded-2xl p-lg ambient-shadow ambient-shadow-hover transition-all duration-300 border border-outline-variant/20 flex flex-col items-center text-center group"
                        >
                            <div
                                class="w-16 h-16 rounded-full bg-secondary-container/20 flex items-center justify-center mb-md group-hover:scale-110 transition-transform"
                            >
                                <span
                                    class="material-symbols-outlined text-secondary text-3xl"
                                    >history</span
                                >
                            </div>
                            <h3
                                class="font-headline-md text-headline-md text-on-surface mb-xs"
                            >
                                Riwayat Pemakaian
                            </h3>
                            <p
                                class="font-body-md text-body-md text-on-surface-variant"
                            >
                                Pantau grafik penggunaan air bulanan Anda.
                                Transparan dan membantu Anda mengelola konsumsi
                                air keluarga.
                            </p>
                        </div>
                        <!-- Feature Card 3 -->
                        <div
                            class="bg-surface rounded-2xl p-lg ambient-shadow ambient-shadow-hover transition-all duration-300 border border-outline-variant/20 flex flex-col items-center text-center group"
                        >
                            <div
                                class="w-16 h-16 rounded-full bg-tertiary-container/20 flex items-center justify-center mb-md group-hover:scale-110 transition-transform"
                            >
                                <span
                                    class="material-symbols-outlined text-tertiary text-3xl"
                                    >payments</span
                                >
                            </div>
                            <h3
                                class="font-headline-md text-headline-md text-on-surface mb-xs"
                            >
                                Bayar Mudah
                            </h3>
                            <p
                                class="font-body-md text-body-md text-on-surface-variant"
                            >
                                Cek status lunas atau belum lunas dengan mudah melalui ponsel, dan unduh slip pembayaran kapan saja dibutuhkan.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <!-- Footer -->
        <footer
            class="bg-surface-container-highest dark:bg-inverse-surface mt-auto"
        >
            <div
                class="w-full px-margin py-lg flex flex-col md:flex-row justify-between items-center gap-md max-w-7xl mx-auto"
            >
                <div class="flex items-center gap-sm">
                    <span
                        class="material-symbols-outlined text-primary fill-icon text-2xl"
                        >water_drop</span
                    >
                    <span
                        class="font-headline-sm text-headline-sm font-bold text-primary dark:text-primary-fixed"
                        >SIMADES</span
                    >
                </div>
                <p
                    class="font-body-md text-body-md text-on-surface dark:text-inverse-on-surface text-center md:text-left"
                >
                    © 2026 SIMADES. Solusi Pengelolaan Air Desa Modern &amp;
                    Transparan.
                </p>
                <div
                    class="flex flex-wrap justify-center gap-md font-body-md text-body-md"
                >
                    <a
                        class="text-on-surface-variant dark:text-outline-variant hover:underline decoration-primary dark:decoration-primary-fixed focus:ring-2 focus:ring-primary rounded"
                        href="#"
                        >Kebijakan Privasi</a
                    >
                    <a
                        class="text-on-surface-variant dark:text-outline-variant hover:underline decoration-primary dark:decoration-primary-fixed focus:ring-2 focus:ring-primary rounded"
                        href="#"
                        >Syarat &amp; Ketentuan</a
                    >
                    <a
                        class="text-on-surface-variant dark:text-outline-variant hover:underline decoration-primary dark:decoration-primary-fixed focus:ring-2 focus:ring-primary rounded"
                        href="#"
                        >Hubungi Kami</a
                    >
                </div>
            </div>
        </footer>
    </body>
</html>
