<?php

try {
    $db = new PDO(
        "mysql:host=localhost;dbname=employee_qr;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "=== USERS TABLE ===\n";
    $usersStmt = $db->query("SELECT COUNT(*) as count FROM users");
    $userCount = $usersStmt->fetch(PDO::FETCH_ASSOC);
    echo "Total users: " . $userCount['count'] . "\n";
    
    $userList = $db->query("SELECT id, username, email, status FROM users LIMIT 5");
    echo "\nSample users:\n";
    print_r($userList->fetchAll(PDO::FETCH_ASSOC));

    echo "\n=== EMPLOYEES TABLE ===\n";
    $empStmt = $db->query("SELECT COUNT(*) as count FROM employees");
    $empCount = $empStmt->fetch(PDO::FETCH_ASSOC);
    echo "Total employees: " . $empCount['count'] . "\n";
    
    $empList = $db->query("SELECT id, full_name, email, status FROM employees LIMIT 5");
    echo "\nSample employees:\n";
    print_r($empList->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
