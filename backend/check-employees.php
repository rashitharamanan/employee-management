<?php
$db = new PDO('mysql:host=localhost;dbname=employee_qr', 'root', '');
echo "=== EMPLOYEES TABLE ===\n";
foreach($db->query('DESCRIBE employees')->fetchAll(PDO::FETCH_ASSOC) as $c) {
    echo $c['Field'] . ' | ' . $c['Type'] . ' | ' . $c['Null'] . ' | ' . ($c['Default'] ?? 'NULL') . "\n";
}
echo "\n=== ROLES TABLE ===\n";
foreach($db->query('SELECT * FROM roles')->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo $r['id'] . ' | ' . $r['name'] . "\n";
}
echo "\n=== DEPARTMENTS TABLE ===\n";
foreach($db->query('SELECT * FROM departments LIMIT 5')->fetchAll(PDO::FETCH_ASSOC) as $d) {
    echo $d['id'] . ' | ' . $d['name'] . "\n";
}
echo "\n=== DESIGNATIONS TABLE ===\n";
foreach($db->query('SELECT * FROM designations LIMIT 5')->fetchAll(PDO::FETCH_ASSOC) as $d) {
    echo $d['id'] . ' | ' . $d['name'] . "\n";
}
