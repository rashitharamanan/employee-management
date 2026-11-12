<?php

namespace App\Repositories\Auth;

use PDO;

class AdminRepository
{
    public function __construct(
        private PDO $db
    ) {
    }

    public function findByUsername(string $username): ?array
    {
        $sql = "
            SELECT
                id,
                username,
                password,
                full_name,
                email
            FROM admins
            WHERE username = :username
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'username' => $username
        ]);

        $admin = $statement->fetch();

        return $admin ?: null;
    }
}