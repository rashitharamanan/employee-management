<?php

$db = new PDO('mysql:host=localhost;dbname=employee_qr', 'root', '');

// Simulate the updated query
$sql = "
    SELECT
        u.id,
        CONCAT('USR', LPAD(u.id, 3, '0')) AS user_id,
        u.username,
        u.email,
        u.status,
        u.last_login_at  AS last_login,
        u.created_at,
        u.updated_at,
        COALESCE(e.full_name, u.username) AS full_name,

        r.name           AS role,

        COALESCE(c.name, '') AS organisation_name

    FROM users u

    LEFT JOIN roles r
        ON r.id = u.role_id

LEFT JOIN companies c
                ON c.id = u.company_id
        
    LEFT JOIN employees e
        ON e.id = u.employee_id

    ORDER BY u.created_at DESC
";

$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo "=== API Response (simulated) ===\n";
echo json_encode([
    'success' => true,
    'message' => 'Users retrieved successfully.',
    'data' => $result
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

?>
