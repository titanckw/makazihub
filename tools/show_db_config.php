<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "default: " . config('database.default') . PHP_EOL;
echo "host: " . config('database.connections.' . config('database.default') . '.host') . PHP_EOL;
echo "username: " . config('database.connections.' . config('database.default') . '.username') . PHP_EOL;
echo "db connection driver: " . config('database.connections.' . config('database.default') . '.driver') . PHP_EOL;
