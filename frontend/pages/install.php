<?php
/**
 * 1-Click Database Installer & Migrator for Cloud Deployments
 */
require_once __DIR__ . '/../../backend/config/database.php';

$message = '';
$status = '';
$details = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['auto'])) {
    try {
        $database = new Database();
        $pdo = $database->connect();

        $schemaFile = __DIR__ . '/../../database/schema.sql';
        if (!file_exists($schemaFile)) {
            throw new Exception("Schema file not found at: " . $schemaFile);
        }

        $sql = file_get_contents($schemaFile);

        // Temporarily disable foreign keys for bulk creation
        try { $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;"); } catch (\Throwable $_) {}

        // Split into clean statements
        $lines = explode("\n", $sql);
        $query = '';
        $executed = 0;
        $tablesCreated = 0;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
                continue;
            }
            if (stripos($trimmed, 'LOCK TABLES') === 0 || stripos($trimmed, 'UNLOCK TABLES') === 0) {
                continue;
            }

            $query .= $line . "\n";
            if (str_ends_with($trimmed, ';')) {
                try {
                    $pdo->exec($query);
                    $executed++;
                    if (stripos($query, 'CREATE TABLE') !== false) {
                        $tablesCreated++;
                    }
                } catch (\Throwable $e) {
                    $details[] = $e->getMessage();
                }
                $query = '';
            }
        }

        try { $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;"); } catch (\Throwable $_) {}

        $status = 'success';
        $message = "Database successfully initialized! Created {$tablesCreated} tables and executed {$executed} statements.";
    } catch (\Throwable $e) {
        $status = 'error';
        $message = "Installation failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Employee Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8fafc; padding: 20px; }
        .card { background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); padding: 40px; max-width: 500px; width: 100%; text-align: center; border: 1px solid #e2e8f0; }
        .icon { width: 64px; height: 64px; background: #eff6ff; color: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 28px; }
        h1 { font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        p { color: #64748b; font-size: 14px; line-height: 1.5; margin-bottom: 24px; }
        .btn { display: inline-block; width: 100%; padding: 12px 20px; background: #be185d; color: white; border-radius: 10px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; font-size: 15px; transition: background 0.2s; }
        .btn:hover { background: #9d174d; }
        .alert { padding: 16px; border-radius: 10px; margin-bottom: 20px; text-align: left; font-size: 14px; }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .btn-secondary { background: #f1f5f9; color: #334155; margin-top: 12px; }
        .btn-secondary:hover { background: #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🗄️</div>
        <h1>Database Initializer</h1>
        <p>Click below to initialize your cloud MySQL database with all 51 required tables and initial seed data.</p>

        <?php if ($status === 'success'): ?>
            <div class="alert alert-success">
                <strong>Success!</strong> <?= htmlspecialchars($message) ?>
            </div>
            <a href="login.php" class="btn">Proceed to Login &rarr;</a>
        <?php elseif ($status === 'error'): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?= htmlspecialchars($message) ?>
            </div>
            <form method="POST">
                <button type="submit" class="btn">Retry Database Setup</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <button type="submit" class="btn">Initialize Database Now</button>
            </form>
            <a href="login.php" class="btn btn-secondary">Back to Login</a>
        <?php endif; ?>
    </div>
</body>
</html>
