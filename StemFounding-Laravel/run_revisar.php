<?php

// Carga autoload de Composer y bootstrap de Laravel
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Instancia el Kernel de consola
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Iniciando loop de revisión de proyectos cada 60 segundos..." . PHP_EOL;

while (true) {
    // Ejecuta el comando directamente por su signature
    $exitCode = $kernel->call('revisar:estados-proyectos');

    // Muestra la salida del comando
    echo $kernel->output() . PHP_EOL;

    // Espera 60 segundos
    sleep(60);
}
