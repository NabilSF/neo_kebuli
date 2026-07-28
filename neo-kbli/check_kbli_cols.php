<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = Illuminate\Support\Facades\Schema::getColumns('kbli_items');
foreach ($cols as $col) {
    echo $col['name'] . ' => ' . $col['type'] . "\n";
}
