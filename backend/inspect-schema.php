<?php

$db = new PDO('mysql:host=localhost;dbname=employee_qr', 'root', '');

$tables = [
    'attendance_records',
    'leave_requests',
    'leave_balances',
    'leave_types',
    'performance_reviews',
    'performance_goals',
    'performance_ratings',
    'training_programs',
    'training_enrollments',
    'payroll_records',
    'holidays',
];

foreach ($tables as $table) {
    echo "=== {$table} ===\n";
    try {
        $cols = $db->query("DESCRIBE {$table}")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $col) {
            echo $col['Field'] . ' (' . $col['Type'] . ")\n";
        }
    } catch (Throwable $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
