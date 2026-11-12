<?php

namespace App\Repositories\Employee;

use PDO;

class EmployeeRepository
{
    public function __construct(
        private PDO $db
    ) {
    }

    /**
     * Get all employees
     */
    public function getAll(): array
    {
        $sql = "
            SELECT
                id,
                employee_code,
                full_name,
                date_of_birth,
                gender,
                profile_photo,
                email,
                personal_email,
                phone,
                department,
                designation,
                joining_date,
                employment_type,
                reporting_manager,
                status,
                address,
                city,
                state,
                pincode,
                emergency_contact_name,
                emergency_contact_phone,
                emergency_contact_relationship,
                qualification,
                experience_years,
                qr_path,
                created_at,
                updated_at
            FROM employees
            ORDER BY id DESC
        ";

        $statement = $this->db->query($sql);

        return $statement->fetchAll();
    }

    /**
     * Find employee by ID
     */
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                id,
                employee_code,
                full_name,
                date_of_birth,
                gender,
                profile_photo,
                email,
                personal_email,
                phone,
                department,
                designation,
                joining_date,
                employment_type,
                reporting_manager,
                status,
                address,
                city,
                state,
                pincode,
                emergency_contact_name,
                emergency_contact_phone,
                emergency_contact_relationship,
                qualification,
                experience_years,
                qr_path,
                created_at,
                updated_at
            FROM employees
            WHERE id = :id
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $employee = $statement->fetch();

        return $employee ?: null;
    }

    /**
     * Create employee
     */
    public function ensureSchema(): void
    {
        $existingCols = [];
        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM employees");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $existingCols[strtolower($row['Field'])] = true;
            }
        } catch (\Throwable $e) {
            // Table might not exist yet or query failed
        }

        $columnsToAdd = [
            'first_name' => "VARCHAR(60) NULL AFTER employee_code",
            'last_name' => "VARCHAR(60) NULL AFTER first_name",
            'marital_status' => "VARCHAR(30) NULL AFTER gender",
            'nationality' => "VARCHAR(60) NULL AFTER marital_status",
            'alternate_phone' => "VARCHAR(25) NULL AFTER phone",
            'work_location' => "VARCHAR(120) NULL AFTER reporting_manager",
            'work_shift' => "VARCHAR(60) NULL AFTER work_location",
            'probation_period' => "INT NULL AFTER work_shift",
            'notice_period' => "INT NULL AFTER probation_period",
            'basic_salary' => "DECIMAL(12,2) NULL AFTER notice_period",
            'pay_grade' => "VARCHAR(50) NULL AFTER basic_salary",
            'emergency_contact_alternate_phone' => "VARCHAR(25) NULL AFTER emergency_contact_relationship",
        ];

        foreach ($columnsToAdd as $col => $definition) {
            if (!isset($existingCols[strtolower($col)])) {
                try {
                    $this->db->exec("ALTER TABLE employees ADD COLUMN {$col} {$definition}");
                } catch (\Throwable $e) {
                    // Ignore duplicate column or schema errors
                }
            }
        }

        // Ensure employee_documents table exists
        try {
            $this->db->exec(<<<'SQL'
                CREATE TABLE IF NOT EXISTS employee_documents (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    employee_id INT NOT NULL,
                    document_type VARCHAR(100) NOT NULL,
                    document_name VARCHAR(255) NOT NULL,
                    file_path VARCHAR(255) NOT NULL,
                    file_size INT UNSIGNED NULL DEFAULT 0,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    KEY idx_emp_docs_emp (employee_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            SQL);
        } catch (\Throwable $e) {
            // Ignore if already created
        }
    }

    /**
     * Get next sequential employee code (e.g. EMP001, EMP002, ...)
     */
    public function getNextEmployeeCode(): string
    {
        try {
            $stmt = $this->db->query("
                SELECT employee_code 
                FROM employees 
                WHERE employee_code REGEXP '^EMP[0-9]+$' 
                ORDER BY CAST(SUBSTRING(employee_code, 4) AS UNSIGNED) DESC 
                LIMIT 1
            ");
            $latest = $stmt->fetchColumn();
            if ($latest && preg_match('/^EMP([0-9]+)$/', $latest, $m)) {
                $nextNum = ((int) $m[1]) + 1;
                return 'EMP' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT);
            }
        } catch (\Throwable $e) {
        }

        // Fallback: count total employees + 1
        try {
            $count = (int) $this->db->query("SELECT COUNT(*) FROM employees")->fetchColumn();
            return 'EMP' . str_pad((string)($count + 1), 3, '0', STR_PAD_LEFT);
        } catch (\Throwable $e) {
            return 'EMP001';
        }
    }

    /**
     * Get managers list for dropdown
     */
    public function getManagersList(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT id, employee_code, full_name, designation, department
                FROM employees
                WHERE status = 'ACTIVE'
                ORDER BY full_name ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Save employee documents
     */
    public function saveDocuments(int $employeeId, array $documents): void
    {
        if (empty($documents)) {
            return;
        }

        $stmt = $this->db->prepare("
            INSERT INTO employee_documents (employee_id, document_type, document_name, file_path, file_size)
            VALUES (:employee_id, :document_type, :document_name, :file_path, :file_size)
        ");

        foreach ($documents as $doc) {
            if (empty($doc['file_path'])) {
                continue;
            }
            $stmt->execute([
                ':employee_id' => $employeeId,
                ':document_type' => $doc['document_type'] ?? 'General',
                ':document_name' => $doc['document_name'] ?? basename($doc['file_path']),
                ':file_path' => $doc['file_path'],
                ':file_size' => (int) ($doc['file_size'] ?? 0)
            ]);
        }
    }

    /**
     * Get employee documents
     */
    public function getDocuments(int $employeeId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, document_type, document_name, file_path, file_size, created_at
                FROM employee_documents
                WHERE employee_id = :employee_id
                ORDER BY id ASC
            ");
            $stmt->execute([':employee_id' => $employeeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Create employee
     */
    public function create(array $data): int
    {
        $fields = [
            'employee_code',
            'full_name',
            'first_name',
            'last_name',
            'date_of_birth',
            'gender',
            'marital_status',
            'nationality',
            'profile_photo',
            'email',
            'personal_email',
            'phone',
            'alternate_phone',
            'department',
            'designation',
            'joining_date',
            'employment_type',
            'reporting_manager',
            'work_location',
            'work_shift',
            'probation_period',
            'notice_period',
            'basic_salary',
            'pay_grade',
            'status',
            'address',
            'city',
            'state',
            'pincode',
            'emergency_contact_name',
            'emergency_contact_phone',
            'emergency_contact_alternate_phone',
            'emergency_contact_relationship',
            'qualification',
            'experience_years'
        ];

        // Check which fields exist in employees table
        $columnsStmt = $this->db->query("SHOW COLUMNS FROM employees");
        $tableCols = [];
        while ($c = $columnsStmt->fetch(PDO::FETCH_ASSOC)) {
            $tableCols[strtolower($c['Field'])] = true;
        }

        $insertCols = [];
        $insertPlaceholders = [];
        $bindParams = [];

        foreach ($fields as $f) {
            if (isset($tableCols[strtolower($f)])) {
                $insertCols[] = $f;
                $insertPlaceholders[] = ':' . $f;
                $bindParams[$f] = array_key_exists($f, $data) ? $data[$f] : null;
            }
        }

        $sql = "INSERT INTO employees (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $insertPlaceholders) . ")";
        $statement = $this->db->prepare($sql);
        $statement->execute($bindParams);

        $employeeId = (int) $this->db->lastInsertId();

        // Save documents if provided
        if ($employeeId > 0 && !empty($data['documents']) && is_array($data['documents'])) {
            $this->saveDocuments($employeeId, $data['documents']);
        }

        return $employeeId;
    }

    /**
     * Update employee
     */
    public function update(
        int $id,
        array $data
    ): bool {

        $sql = "
            UPDATE employees
            SET
                full_name = :full_name,
                date_of_birth = :date_of_birth,
                gender = :gender,
                profile_photo = :profile_photo,
                email = :email,
                personal_email = :personal_email,
                phone = :phone,
                department = :department,
                designation = :designation,
                joining_date = :joining_date,
                employment_type = :employment_type,
                reporting_manager = :reporting_manager,
                status = :status,
                address = :address,
                city = :city,
                state = :state,
                pincode = :pincode,
                emergency_contact_name = :emergency_contact_name,
                emergency_contact_phone = :emergency_contact_phone,
                emergency_contact_relationship =
                    :emergency_contact_relationship,
                qualification = :qualification,
                experience_years = :experience_years
            WHERE id = :id
        ";

        $statement = $this->db->prepare($sql);

        return $statement->execute([
            'id' => $id,

            'full_name' =>
                $data['full_name'],

            'date_of_birth' =>
                $data['date_of_birth'],

            'gender' =>
                $data['gender'],

            'profile_photo' =>
                $data['profile_photo'],

            'email' =>
                $data['email'],

            'personal_email' =>
                $data['personal_email'],

            'phone' =>
                $data['phone'],

            'department' =>
                $data['department'],

            'designation' =>
                $data['designation'],

            'joining_date' =>
                $data['joining_date'],

            'employment_type' =>
                $data['employment_type'],

            'reporting_manager' =>
                $data['reporting_manager'],

            'status' =>
                $data['status'],

            'address' =>
                $data['address'],

            'city' =>
                $data['city'],

            'state' =>
                $data['state'],

            'pincode' =>
                $data['pincode'],

            'emergency_contact_name' =>
                $data['emergency_contact_name'],

            'emergency_contact_phone' =>
                $data['emergency_contact_phone'],

            'emergency_contact_relationship' =>
                $data['emergency_contact_relationship'],

            'qualification' =>
                $data['qualification'],

            'experience_years' =>
                $data['experience_years']
        ]);
    }

    /**
     * Delete employee
     */
    public function delete(int $id): bool
    {
        $sql = "
            DELETE FROM employees
            WHERE id = :id
        ";

        $statement = $this->db->prepare($sql);

        return $statement->execute([
            'id' => $id
        ]);
    }

    /**
     * Update employee QR path
     */
    public function updateQrPath(
        int $id,
        string $qrPath
    ): bool {

        $sql = "
            UPDATE employees
            SET qr_path = :qr_path
            WHERE id = :id
        ";

        $statement = $this->db->prepare($sql);

        return $statement->execute([
            'id' => $id,
            'qr_path' => $qrPath
        ]);
    }

    /**
     * Find employee by employee code
     */
    public function findByEmployeeCode(
        string $employeeCode
    ): ?array {

        $sql = "
            SELECT
                id,
                employee_code,
                full_name,
                date_of_birth,
                gender,
                profile_photo,
                email,
                personal_email,
                phone,
                department,
                designation,
                joining_date,
                employment_type,
                reporting_manager,
                status,
                address,
                city,
                state,
                pincode,
                emergency_contact_name,
                emergency_contact_phone,
                emergency_contact_relationship,
                qualification,
                experience_years,
                qr_path,
                created_at,
                updated_at
            FROM employees
            WHERE employee_code = :employee_code
            LIMIT 1
        ";

        $statement = $this->db->prepare($sql);

        $statement->execute([
            'employee_code' => $employeeCode
        ]);

        $employee = $statement->fetch();

        return $employee ?: null;
    }
}