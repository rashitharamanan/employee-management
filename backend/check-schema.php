<?php

$db = new PDO('mysql:host=localhost;dbname=employee_qr', 'root', '');

echo "=== USERS TABLE COLUMNS ===\n";
$result = $db->query('DESCRIBE users');
$cols = $result->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $col) {
    echo $col['Field'] . ' (' . $col['Type'] . ')\n';
}

echo "\n=== API TEST - Users Endpoint ===\n";
$users = $db->query('SELECT * FROM users LIMIT 1')->fetchAll(PDO::FETCH_ASSOC);
echo "Sample user data:\n";
print_r($users[0] ?? []);

?>
