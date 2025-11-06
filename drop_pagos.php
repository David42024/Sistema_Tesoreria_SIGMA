<?php
// Script pequeño para eliminar la tabla 'pagos' mediante el bootstrap de Laravel.
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Usar el Schema facade totalmente calificado
\Illuminate\Support\Facades\Schema::dropIfExists('pagos');

echo "Tabla 'pagos' eliminada (si existía)\n";
