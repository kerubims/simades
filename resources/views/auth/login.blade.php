@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="flex-grow flex items-center justify-center">
    <div class="w-full max-w-md bg-surface p-8 rounded-2xl shadow-lg border border-outline-variant/20">
        <div class="text-center mb-8">
            <span class="material-symbols-outlined text-primary text-5xl mb-2 fill-icon">water_drop</span>
            <h1 class="text-2xl font-bold text-on-surface">Masuk ke SIMADES</h1>
            <p class="text-on-surface-variant text-sm mt-1">Sistem Informasi Manajemen Air Desa</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="flex flex-col gap-5">
            @csrf
            
            <div>
                <label for="username" class="block text-sm font-semibold text-on-surface mb-1">Username / NIK / No. KK</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan Username, NIK, atau No. KK" required autofocus
                    class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary text-on-surface" />
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-on-surface mb-1">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full rounded-lg border-outline-variant focus:border-primary focus:ring-primary text-on-surface" />
            </div>

            <button type="submit" class="mt-4 w-full bg-primary text-on-primary font-bold py-3 rounded-lg hover:opacity-90 transition-opacity">
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection
