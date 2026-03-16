<?php
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$lease = App\Models\Lease::first();
if (!$lease) {
    echo "no lease\n";
    exit(0);
}

try {
    $inv = app(App\Services\InvoiceService::class)->generateForLease($lease, \Carbon\Carbon::now());
    print_r($inv->toArray());
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL;
}
