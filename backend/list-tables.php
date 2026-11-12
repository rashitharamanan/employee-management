<?php

$db = new PDO('mysql:host=localhost;dbname=employee_qr', 'root', '');

echo "=== TABLES ===\n";
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    echo $t . "\n";
}

echo "\n=== EMPLOYEES COLUMNS ===\n";
$cols = $db->query('DESCRIBE employees')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== USERS COLUMNS ===\n";
$cols = $db->query('DESCRIBE users')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== ATTENDANCE_RECORDS COLUMNS ===\n";
$cols = $db->query('DESCRIBE attendance_records')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== PAYROLL_RECORDS COLUMNS ===\n";
$cols = $db->query('DESCRIBE payroll_records')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== LEAVE_REQUESTS COLUMNS ===\n";
$cols = $db->query('DESCRIBE leave_requests')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== ONBOARDING_RECORDS COLUMNS ===\n";
$cols = $db->query('DESCRIBE onboarding_records')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== EXIT_RECORDS COLUMNS ===\n";
$cols = $db->query('DESCRIBE exit_records')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== JOB_OPENINGS COLUMNS ===\n";
$cols = $db->query('DESCRIBE job_openings')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== RECRUITMENT_CANDIDATES COLUMNS ===\n";
$cols = $db->query('DESCRIBE recruitment_candidates')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== RECRUITMENT_INTERVIEWS COLUMNS ===\n";
$cols = $db->query('DESCRIBE recruitment_interviews')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== GENERATED_REPORTS COLUMNS ===\n";
$cols = $db->query('DESCRIBE generated_reports')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== HR_WORKFLOW_TASKS COLUMNS ===\n";
$cols = $db->query('DESCRIBE hr_workflow_tasks')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

echo "\n=== HR_DOCUMENT_TEMPLATES COLUMNS ===\n";
$cols = $db->query('DESCRIBE hr_document_templates')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ")\n";
}

