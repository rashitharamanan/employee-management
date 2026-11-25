<?php

class Database
{
    public function connect(): \PDO
    {
        // Check for Railway-provided MYSQL_URL (e.g. mysql://user:pass@host:port/dbname)
        $mysqlUrl = $_ENV['MYSQL_URL'] ?? $_SERVER['MYSQL_URL'] ?? getenv('MYSQL_URL');
        if ($mysqlUrl) {
            $parsed = parse_url($mysqlUrl);
            $host = $parsed['host'] ?? 'localhost';
            $port = $parsed['port'] ?? 3306;
            $database = ltrim($parsed['path'] ?? '', '/');
            $username = $parsed['user'] ?? 'root';
            $password = $parsed['pass'] ?? '';
        } else {
            // Support both standard DB_* and Railway MYSQL* env variables
            $host = $_ENV['DB_HOST'] ?? $_ENV['MYSQLHOST'] ?? $_SERVER['DB_HOST'] ?? $_SERVER['MYSQLHOST'] ?? getenv('DB_HOST') ?? getenv('MYSQLHOST') ?? 'localhost';
            $port = $_ENV['DB_PORT'] ?? $_ENV['MYSQLPORT'] ?? $_SERVER['DB_PORT'] ?? $_SERVER['MYSQLPORT'] ?? getenv('DB_PORT') ?? getenv('MYSQLPORT') ?? '3306';
            $database = $_ENV['DB_DATABASE'] ?? $_ENV['MYSQLDATABASE'] ?? $_SERVER['DB_DATABASE'] ?? $_SERVER['MYSQLDATABASE'] ?? getenv('DB_DATABASE') ?? getenv('MYSQLDATABASE') ?? 'employee_qr';
            $username = $_ENV['DB_USERNAME'] ?? $_ENV['MYSQLUSER'] ?? $_SERVER['DB_USERNAME'] ?? $_SERVER['MYSQLUSER'] ?? getenv('DB_USERNAME') ?? getenv('MYSQLUSER') ?? 'root';
            $password = $_ENV['DB_PASSWORD'] ?? $_ENV['MYSQLPASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? $_SERVER['MYSQLPASSWORD'] ?? getenv('DB_PASSWORD') ?? getenv('MYSQLPASSWORD') ?? '';
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        return new \PDO(
            $dsn,
            $username,
            $password,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }
}
