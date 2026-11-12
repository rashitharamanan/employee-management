<?php

$db = new PDO('mysql:host=localhost;dbname=employee_qr', 'root', '');

echo "=== ROLES ===\n";
foreach ($db->query('SELECT * FROM roles') as $row) {
    echo $row['id'] . ' | ' . $row['name'] . "\n";
}

echo "\n=== USERS (with roles) ===\n";
$sql = "SELECT u.id, u.username, u.email, u.employee_id, u.status, r.name AS role_name
        FROM users u LEFT JOIN roles r ON r.id = u.role_id";
foreach ($db->query($sql) as $row) {
    echo $row['id'] . ' | ' . $row['username'] . ' | ' . $row['email'] . ' | emp:' . ($row['employee_id'] ?? 'NULL') . ' | ' . $row['status'] . ' | role:' . ($row['role_name'] ?? 'NULL') . "\n";
}

echo "\n=== EMPLOYEES (first 5) ===\n";
foreach ($db->query('SELECT id, employee_code, full_name, email, department, designation, status FROM employees LIMIT 5') as $row) {
    echo $row['id'] . ' | ' . $row['employee_code'] . ' | ' . $row['full_name'] . ' | ' . $row['email'] . ' | ' . $row['department'] . ' | ' . $row['designation'] . ' | ' . $row['status'] . "\n";
}
