<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = [
    'sales_items',
    'sales',
    'purchases',
    'payments',
    'customers',
    'vendors',
    'clients',
    'bookings',
    'attendances',
    'salaries',
    'advance_salaries',
    'challans',
    'quotations',
    'bills',
    'services',
    'settings',
    'products',
    'product_serials',
    'inventories',
    'chart_of_accounts',
    'journal_entries',
    'journal_entry_items',
    'daily_expenses',
    'warranty_claims',
];

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) {
        echo "Table $table does not exist\n";
        continue;
    }
    echo "--- Table: $table ---\n";
    $indexes = DB::select("SHOW INDEX FROM `$table`");
    $indexedCols = [];
    foreach ($indexes as $idx) {
        $indexedCols[$idx->Key_name][] = $idx->Column_name;
    }
    foreach ($indexedCols as $keyName => $cols) {
        echo "  Index: $keyName (" . implode(', ', $cols) . ")\n";
    }
}
