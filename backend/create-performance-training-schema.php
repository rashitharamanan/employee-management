<?php
/**
 * Performance & Training Schema Migration
 * 
 * Run this file to create the database tables for the Performance & Training module.
 * Usage: php create-performance-training-schema.php
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

use App\Repositories\PerformanceRepository;
use App\Repositories\TrainingRepository;

try {
    $database = new Database();
    $pdo = $database->connect();

    echo "Creating Performance & Training tables...\n\n";

    $performanceRepo = new PerformanceRepository($pdo);
    $performanceRepo->ensureSchema();
    echo "✓ performance_reviews table created/verified.\n";
    echo "✓ performance_goals table created/verified.\n";
    echo "✓ performance_ratings table created/verified.\n\n";

    $trainingRepo = new TrainingRepository($pdo);
    $trainingRepo->ensureSchema();
    echo "✓ training_programs table created/verified.\n";
    echo "✓ training_sessions table created/verified.\n";
    echo "✓ training_enrollments table created/verified.\n";
    echo "✓ training_certificates table created/verified.\n\n";

    echo "Schema migration completed successfully!\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
