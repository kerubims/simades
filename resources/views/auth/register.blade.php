<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun - SIMADES</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL,GRAD,opsz@400,0,0,24" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#006767",
                        "primary-container": "#008282",
                        "on-primary": "#ffffff",
                        surface: "#f9f9fc",
                        "on-surface": "#1a1c1e",
                        "surface-variant": "#e2e2e5",
                        "on-surface-variant": "#3d4949",
                        outline: "#6d7979",
                        error: "#ba1a1a",
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Manrope', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-surface text-on-surface font-sans min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl overflow-hidden border border-outline/10">
        <div class="p-8 sm:p-12">
            
            <div class="text-center mb-8">
                <div class="flex justify-center items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-primary text-4xl">water_drop</span>
                    <span class="font-heading text-3xl font-bold text-primary">SIMADES</span>
                </div>
                <h1 class="text-2xl font-heading font-bold text-on-surface">Pendaftaran Akun Warga</h1>
                <p class="text-on-surface-variant mt-2">Lengkapi data diri Anda untuk memantau tagihan air</p>
            </div>

            @if(session('error'))
            <div class="bg-error/10 border-l-4 border-error text-error p-4 mb-6 rounded-r-lg" role="alert">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-error/10 border-l-4 border-error text-error p-4 mb-6 rounded-r-lg" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informasi Akun -->
                    <div class="space-y-4 md:col-span-2">
                        <h2 class="font-heading font-bold text-lg border-b pb-2 text-primary">Informasi Akun</h2>
                        
                        <div>
                            <label for="username" class="block text-sm font-medium text-on-surface-variant mb-1">Username</label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required
                                class="w-full px-4 py-3 rounded-xl border border-outline/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-surface-variant/30">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-on-surface-variant mb-1">Password</label>
                                <input type="password" id="password" name="password" required
                                    class="w-full px-4 py-3 rounded-xl border border-outline/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-surface-variant/30">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-on-surface-variant mb-1">Konfirmasi Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    class="w-full px-4 py-3 rounded-xl border border-outline/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-surface-variant/30">
                            </div>
                        </div>
                    </div>

                    <!-- Data Diri -->
                    <div class="space-y-4 md:col-span-2 mt-2">
                        <h2 class="font-heading font-bold text-lg border-b pb-2 text-primary">Data Diri & Alamat</h2>
                        
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-on-surface-variant mb-1">Nama Lengkap (Sesuai KTP)</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                class="w-full px-4 py-3 rounded-xl border border-outline/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-surface-variant/30">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nik" class="block text-sm font-medium text-on-surface-variant mb-1">NIK</label>
                                <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required placeholder="16 Digit NIK"
                                    class="w-full px-4 py-3 rounded-xl border border-outline/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-surface-variant/30">
                            </div>
                            <div>
                                <label for="no_kk" class="block text-sm font-medium text-on-surface-variant mb-1">Nomor KK</label>
                                <input type="text" id="no_kk" name="no_kk" value="{{ old('no_kk') }}" required placeholder="16 Digit No. KK"
                                    class="w-full px-4 py-3 rounded-xl border border-outline/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-surface-variant/30">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="rt" class="block text-sm font-medium text-on-surface-variant mb-1">RT</label>
                                <input type="text" id="rt" name="rt" value="{{ old('rt') }}" required placeholder="Contoh: 01"
                                    class="w-full px-4 py-3 rounded-xl border border-outline/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-surface-variant/30">
                            </div>
                            <div>
                                <label for="rw" class="block text-sm font-medium text-on-surface-variant mb-1">RW</label>
                                <input type="text" id="rw" name="rw" value="{{ old('rw') }}" required placeholder="Contoh: 02"
                                    class="w-full px-4 py-3 rounded-xl border border-outline/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-surface-variant/30">
                            </div>
                        </div>

                        <div>
                            <label for="no_whatsapp" class="block text-sm font-medium text-on-surface-variant mb-1">Nomor WhatsApp Aktif</label>
                            <input type="text" id="no_whatsapp" name="no_whatsapp" value="{{ old('no_whatsapp') }}" required placeholder="Contoh: 0812..."
                                class="w-full px-4 py-3 rounded-xl border border-outline/30 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-surface-variant/30">                            
                        </div>
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-primary hover:bg-primary-container text-on-primary font-semibold py-4 px-6 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2 mt-8">
                    <span>Daftar Sekarang</span>
                    <span class="material-symbols-outlined">how_to_reg</span>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-on-surface-variant">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Masuk di sini</a>
                </p>
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="text-sm text-outline hover:text-primary transition-colors flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Kembali ke Halaman Utama
                    </a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
