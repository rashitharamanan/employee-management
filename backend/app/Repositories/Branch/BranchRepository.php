<?php

namespace App\Repositories\Branch;

use PDO;

class BranchRepository
{
    public function __construct(private PDO $db) {}

    public function getAll(array $filters = []): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['company_id'])) {
            $where[]  = 'b.company_id = :company_id';
            $params[':company_id'] = (int) $filters['company_id'];
        }

        if (!empty($filters['status'])) {
            $where[]  = 'b.status = :status';
            $params[':status'] = strtolower($filters['status']);
        }

        $sql = 'SELECT b.*, c.name AS company_name
                FROM branches b
                LEFT JOIN companies c ON c.id = b.company_id'
             . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
             . ' ORDER BY b.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*, c.name AS company_name
             FROM branches b
             LEFT JOIN companies c ON c.id = b.company_id
             WHERE b.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO branches (company_id, name, location, phone, status)
             VALUES (:company_id, :name, :location, :phone, :status)'
        );
        $stmt->execute([
            ':company_id' => $data['company_id'],
            ':name'       => $data['name'],
            ':location'   => $data['location'] ?? null,
            ':phone'      => $data['phone']    ?? null,
            ':status'     => $data['status']   ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }
}
