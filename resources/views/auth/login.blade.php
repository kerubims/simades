@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<div class="flex-grow flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <div class="glassmorphism p-8 sm:p-10 rounded-[2rem] shadow-2xl relative overflow-hidden">
            {{-- Accent line --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-[#006767] to-[#0ea5e9]"></div>
            
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary/10 mb-4 shadow-inner">
                    <span class="material-symbols-outlined text-primary text-4xl fill-icon">water_drop</span>
                </div>
                <h1 class="text-3xl font-extrabold text-on-surface tracking-tight font-manrope">SIMADES</h1>
                <p class="text-on-surface-variant text-sm mt-2 font-medium">Sistem Informasi Manajemen Air Desa</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-3 shadow-sm">
                    <span class="material-symbols-outlined fill-icon text-red-500 shrink-0">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-3 shadow-sm">
                    <span class="material-symbols-outlined fill-icon text-red-500 shrink-0">error</span>
                    <ul class="list-disc pl-5 w-full">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="flex flex-col gap-6">
                @csrf
                
                <div class="space-y-1.5">
                    <label for="username" class="block text-sm font-semibold text-on-surface ml-1">Username / NIK / No. KK</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-on-surface-variant/50 text-[20px]">person</span>
                        </div>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan identitas..." required autofocus
                            class="block w-full pl-11 pr-4 py-3.5 bg-white/50 border border-outline-variant/50 rounded-xl focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface transition-all duration-200 shadow-sm placeholder:text-on-surface-variant/40" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="block text-sm font-semibold text-on-surface ml-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-on-surface-variant/50 text-[20px]">lock</span>
                        </div>
                        <input type="password" id="password" name="password" placeholder="••••••••" required
                            class="block w-full pl-11 pr-4 py-3.5 bg-white/50 border border-outline-variant/50 rounded-xl focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary text-on-surface transition-all duration-200 shadow-sm placeholder:text-on-surface-variant/40" />
                    </div>
                </div>

                <button type="submit" class="mt-4 w-full bg-primary text-white font-bold py-3.5 rounded-xl hover:bg-primary/90 focus:ring-4 focus:ring-primary/20 transition-all duration-200 shadow-lg shadow-primary/30 flex justify-center items-center gap-2 group">
                    <span>Masuk</span>
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-xs text-on-surface-variant/70">&copy; {{ date('Y') }} Desa Digital. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>
@endsection
