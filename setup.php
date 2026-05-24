<?php
/**
 * AFIT VAS v1.0 — Database Setup & Seeder Script
 * 
 * Sets up the database schema and seeds initial data.
 * Run via CLI: php setup.php
 * 
 * @author Afolayan Prince-Joseph (U22CS1012)
 */

$envPath = __DIR__ . '/backend/.env';
if (!file_exists($envPath)) {
    die("Error: .env file not found. Please copy .env.example to .env and configure the database.\n");
}

$env = parse_ini_file($envPath);
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$user = $env['DB_USER'] ?? 'root';
$pass = $env['DB_PASS'] ?? '';
$dbname = $env['DB_NAME'] ?? 'afit_vas';

try {
    echo "Connecting to MySQL server...\n";
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Creating database '$dbname' if not exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    $files = [
        'database/schema.sql'
    ];

    foreach ($files as $file) {
        $path = __DIR__ . '/' . $file;
        if (file_exists($path)) {
            echo "Executing $file...\n";
            $sql = file_get_contents($path);
            
            // Execute in chunks if it has multiple statements
            $pdo->exec($sql);
            echo "✓ $file imported successfully.\n";
        } else {
            echo "⚠ Warning: $file not found.\n";
        }
    }

    echo "\nSetup completed successfully.\n";
    echo "Default Admin: superadmin@afit.edu.ng / Password123\n";
    echo "API URL: http://localhost/P%20V1/P%20V1/Final_AFIT_VAS/backend/public/api/health\n";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
