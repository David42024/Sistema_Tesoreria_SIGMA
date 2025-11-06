<?php
// Script pequeño para eliminar la tabla 'transacciones_pasarela' mediante el bootstrap de Laravel.
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\Schema::dropIfExists('transacciones_pasarela');

echo "Tabla 'transacciones_pasarela' eliminada (si existía)\n";
