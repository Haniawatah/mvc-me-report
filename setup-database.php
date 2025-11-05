<?php
/**
 * Script to set up the SQLite database
 */

// Create data directory if it doesn't exist
$dataDir = __DIR__ . '/var';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

// Create empty SQLite database file
$dbFile = $dataDir . '/data.db';
if (!file_exists($dbFile)) {
    file_put_contents($dbFile, '');
    chmod($dbFile, 0666); // Make it writable
}

echo "SQLite database file created at: $dbFile\n";
echo "Now run: php bin/console doctrine:schema:create\n";
