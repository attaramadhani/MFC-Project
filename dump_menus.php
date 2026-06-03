<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menus = Illuminate\Support\Facades\DB::table('menu')->get();
file_put_contents('menus_dump.json', json_encode($menus, JSON_PRETTY_PRINT));
echo "Menus dumped to menus_dump.json\n";
