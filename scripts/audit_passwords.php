<?php
// Simple script to audit 'aktor' passwords format
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$sql = <<<'SQL'
SELECT id_aktor, nama_aktor, password
FROM aktor
WHERE password NOT LIKE '$2y$%'
  AND password NOT LIKE '$2a$%'
  AND password NOT LIKE '$2x$%'
  AND password NOT LIKE 'argon2%';
SQL;

try {
    $rows = DB::select($sql);
    if (empty($rows)) {
        echo "OK: no non-hashed-looking passwords found in table 'aktor'.\n";
        exit(0);
    }

    echo "Found the following rows with non-hashed-looking password values:\n";
    foreach ($rows as $r) {
        echo "{$r->id_aktor}\t{$r->nama_aktor}\t{$r->password}\n";
    }
} catch (Exception $e) {
    echo "Error running audit: " . $e->getMessage() . "\n";
    exit(2);
}
