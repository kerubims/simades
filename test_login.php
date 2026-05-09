<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\PelangganService::class);
$user = $service->findUserByIdentifier('sanusi123');
dump('User found:', $user ? (array)$user : null);
if ($user) {
    $pelanggan = $service->findByIdUser($user->idUser);
    dump('Pelanggan found:', $pelanggan ? (array)$pelanggan : null);
}
