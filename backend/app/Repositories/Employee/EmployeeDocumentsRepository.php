<?php

namespace App\Repositories\Employee;

use PDO;

class EmployeeDocumentsRepository
{
    public function __construct(private PDO $db)
    {
    }

    /**
     * Ensure tables and columns exist
     */
    public function ensureSchema(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS employee_documents (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id INT UNSIGNED NOT NULL,
            document_name VARCHAR(255) NOT NULL,
            document_type VARCHAR(100) NOT NULL DEFAULT 'General',
            uploaded_on DATE NOT NULL,
            file_path VARCHAR(255) NULL,
            file_size VARCHAR(50) NULL DEFAULT '1.2 MB',
            status ENUM('ACTIVE','ARCHIVED') NOT NULL DEFAULT 'ACTIVE',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_doc_emp (employee_id),
            KEY idx_doc_type (document_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        try {
            $cols = $this->db->query("SHOW COLUMNS FROM employee_documents LIKE 'uploaded_on'")->fetchAll();
            if (empty($cols)) {
                $this->db->exec("ALTER TABLE employee_documents ADD COLUMN uploaded_on DATE NOT NULL AFTER document_type");
            }
        } catch (\Throwable $_) {}

        $this->db->exec("CREATE TABLE IF NOT EXISTS employee_document_requests (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id INT UNSIGNED NOT NULL,
            document_type VARCHAR(100) NOT NULL,
            reason TEXT NULL,
            urgency ENUM('NORMAL','URGENT') NOT NULL DEFAULT 'NORMAL',
            status ENUM('PENDING','APPROVED','REJECTED','COMPLETED') NOT NULL DEFAULT 'PENDING',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_req_emp (employee_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->exec("CREATE TABLE IF NOT EXISTS employee_bank_details (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            employee_id INT UNSIGNED NOT NULL UNIQUE,
            account_holder_name VARCHAR(150) NOT NULL,
            bank_name VARCHAR(150) NOT NULL,
            account_number VARCHAR(50) NOT NULL,
            ifsc_code VARCHAR(30) NOT NULL,
            branch_name VARCHAR(100) NULL,
            account_type VARCHAR(30) NOT NULL DEFAULT 'Savings',
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_bank_emp (employee_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    /**
     * Seed initial demo records matching the reference design
     */
    public function seedIfEmpty(int $employeeId): void
    {
        $this->ensureSchema();

        // 1. Seed Documents if empty
        $docCount = (int) $this->db->query("SELECT COUNT(*) FROM employee_documents WHERE employee_id = {$employeeId}")->fetchColumn();
        if ($docCount === 0) {
            $docs = [
                ['Offer Letter', 'Employment', '2024-01-12', 'storage/uploads/documents/offer_letter.pdf', '2.4 MB'],
                ['ID Proof (Aadhar)', 'Identity', '2024-01-14', 'storage/uploads/documents/aadhar_card.pdf', '1.1 MB'],
                ['PAN Card', 'Identity', '2024-01-14', 'storage/uploads/documents/pan_card.pdf', '980 KB'],
                ['Educational Certificate', 'Education', '2024-01-20', 'storage/uploads/documents/degree_cert.pdf', '3.2 MB'],
                ['Experience Letter', 'Experience', '2024-02-10', 'storage/uploads/documents/experience_letter.pdf', '1.8 MB'],
                // Additional files to make total documents = 12
                ['NDA & Confidentiality Agreement', 'Employment', '2024-01-12', 'storage/uploads/documents/nda.pdf', '1.5 MB'],
                ['Address Proof (Utility Bill)', 'Identity', '2024-01-15', 'storage/uploads/documents/address_proof.pdf', '850 KB'],
                ['10th Marksheet', 'Education', '2024-01-18', 'storage/uploads/documents/10th_marks.pdf', '1.4 MB'],
                ['12th Marksheet', 'Education', '2024-01-18', 'storage/uploads/documents/12th_marks.pdf', '1.4 MB'],
                ['Relieving Letter - Previous Org', 'Experience', '2024-02-10', 'storage/uploads/documents/relieving.pdf', '1.2 MB'],
                ['Bank Passbook Copy', 'Financial', '2024-01-16', 'storage/uploads/documents/bank_passbook.pdf', '900 KB'],
                ['Form 16 (FY 2024-25)', 'Financial', '2025-05-20', 'storage/uploads/documents/form_16.pdf', '2.1 MB']
            ];

            $stmt = $this->db->prepare("INSERT INTO employee_documents (employee_id, document_name, document_type, uploaded_on, file_path, file_size) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($docs as $d) {
                $stmt->execute([$employeeId, $d[0], $d[1], $d[2], $d[3], $d[4]]);
            }
        }

        // 2. Seed Payroll Records if empty
        $payCount = (int) $this->db->query("SELECT COUNT(*) FROM payroll_records WHERE employee_id = {$employeeId}")->fetchColumn();
        if ($payCount === 0) {
            $periods = [
                ['2025-08', 45000, 18000, 12000, 75000, 5400, 200, 6400, 12000, 58000, '2025-08-31'],
                ['2025-07', 45000, 18000, 12000, 75000, 5400, 200, 6400, 12000, 58000, '2025-07-31'],
                ['2025-06', 43000, 17000, 11000, 71000, 5160, 200, 5640, 11000, 55000, '2025-06-30'],
                ['2025-05', 43000, 17000, 11000, 71000, 5160, 200, 5640, 11000, 55000, '2025-05-31'],
                ['2025-04', 43000, 17000, 11000, 71000, 5160, 200, 5640, 11000, 55000, '2025-04-30'],
                ['2025-03', 43000, 17000, 11000, 71000, 5160, 200, 5640, 11000, 55000, '2025-03-31'],
                ['2025-02', 43000, 17000, 11000, 71000, 5160, 200, 5640, 11000, 55000, '2025-02-28'],
                ['2025-01', 43000, 17000, 11000, 71000, 5160, 200, 5640, 11000, 55000, '2025-01-31'],
                ['2024-12', 40000, 16000, 10000, 66000, 4800, 200, 6000, 11000, 55000, '2024-12-31'],
                ['2024-11', 40000, 16000, 10000, 66000, 4800, 200, 6000, 11000, 55000, '2024-11-30'],
                ['2024-10', 40000, 16000, 10000, 66000, 4800, 200, 6000, 11000, 55000, '2024-10-31'],
                ['2024-09', 40000, 16000, 10000, 66000, 4800, 200, 6000, 11000, 55000, '2024-09-30']
            ];

            $stmt = $this->db->prepare("INSERT INTO payroll_records (employee_id, pay_period, basic_salary, hra, special_allowance, allowances, gross_pay, pf, professional_tax, income_tax, deductions, net_pay, status, paid_on, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?, ?, 'PAID', ?, NOW(), NOW())");
            foreach ($periods as $p) {
                $stmt->execute([
                    $employeeId,
                    $p[0],
                    $p[1],
                    $p[2],
                    $p[3],
                    $p[4],
                    $p[5],
                    $p[6],
                    $p[7],
                    $p[8],
                    $p[9],
                    $p[10]
                ]);
            }
        }

        // 3. Seed Bank Details if empty
        $bankCount = (int) $this->db->query("SELECT COUNT(*) FROM employee_bank_details WHERE employee_id = {$employeeId}")->fetchColumn();
        if ($bankCount === 0) {
            $stmt = $this->db->prepare("INSERT INTO employee_bank_details (employee_id, account_holder_name, bank_name, account_number, ifsc_code, branch_name, account_type) VALUES (?, 'Priya Nair', 'HDFC Bank', '5010048291823', 'HDFC0001245', 'Indiranagar, Bengaluru', 'Savings')");
            $stmt->execute([$employeeId]);
        }
    }

    /**
     * Get employee basic info
     */
    public function getEmployee(int $employeeId): array
    {
        $stmt = $this->db->prepare("SELECT id, employee_code, full_name, email, department, designation, profile_photo, joining_date FROM employees WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $employeeId]);
        $emp = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($emp) {
            return $emp;
        }

        return [
            'id'            => $employeeId,
            'employee_code' => 'EMP1024',
            'full_name'     => 'Priya Nair',
            'email'         => 'priya.nair@mindazzle.com',
            'department'    => 'Design & Product',
            'designation'   => 'UI/UX Designer',
            'profile_photo' => null,
            'joining_date'  => '2024-01-10'
        ];
    }

    /**
     * Get documents list for employee
     */
    public function getDocuments(int $employeeId, string $search = '', string $type = ''): array
    {
        $sql = "SELECT id, document_name, document_type, uploaded_on, file_path, file_size 
                FROM employee_documents 
                WHERE employee_id = :eid AND status = 'ACTIVE'";
        $params = [':eid' => $employeeId];

        if (!empty($type) && $type !== 'ALL') {
            $sql .= " AND document_type = :type";
            $params[':type'] = $type;
        }

        if (!empty($search)) {
            $sql .= " AND (document_name LIKE :search OR document_type LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY uploaded_on ASC, id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get document by ID
     */
    public function getDocument(int $employeeId, int $documentId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM employee_documents WHERE id = :id AND employee_id = :eid LIMIT 1");
        $stmt->execute([':id' => $documentId, ':eid' => $employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Save uploaded document record
     */
    public function saveDocument(int $employeeId, array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO employee_documents (employee_id, document_name, document_type, uploaded_on, file_path, file_size)
            VALUES (:eid, :name, :type, :uploaded_on, :path, :size)
        ");
        $stmt->execute([
            ':eid'         => $employeeId,
            ':name'        => $data['document_name'],
            ':type'        => $data['document_type'] ?? 'General',
            ':uploaded_on' => $data['uploaded_on'] ?? date('Y-m-d'),
            ':path'        => $data['file_path'] ?? null,
            ':size'        => $data['file_size'] ?? '1.2 MB'
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Delete / Archive a document
     */
    public function deleteDocument(int $employeeId, int $documentId): bool
    {
        $stmt = $this->db->prepare("UPDATE employee_documents SET status = 'ARCHIVED' WHERE id = :id AND employee_id = :eid");
        return $stmt->execute([':id' => $documentId, ':eid' => $employeeId]);
    }

    /**
     * Get all payroll records for employee
     */
    public function getPayrollRecords(int $employeeId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM payroll_records 
            WHERE employee_id = :eid 
            ORDER BY pay_period DESC
        ");
        $stmt->execute([':eid' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single payroll record by period
     */
    public function getPayrollByPeriod(int $employeeId, string $period): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM payroll_records 
            WHERE employee_id = :eid AND pay_period = :period 
            LIMIT 1
        ");
        $stmt->execute([':eid' => $employeeId, ':period' => $period]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Create document request
     */
    public function createDocumentRequest(int $employeeId, array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO employee_document_requests (employee_id, document_type, reason, urgency, status)
            VALUES (:eid, :type, :reason, :urgency, 'PENDING')
        ");
        $stmt->execute([
            ':eid'     => $employeeId,
            ':type'    => $data['document_type'],
            ':reason'  => $data['reason'] ?? '',
            ':urgency' => $data['urgency'] ?? 'NORMAL'
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get bank details
     */
    public function getBankDetails(int $employeeId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM employee_bank_details WHERE employee_id = :eid LIMIT 1");
        $stmt->execute([':eid' => $employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Save/Update bank details
     */
    public function saveBankDetails(int $employeeId, array $data): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO employee_bank_details (employee_id, account_holder_name, bank_name, account_number, ifsc_code, branch_name, account_type)
            VALUES (:eid, :holder, :bank, :acc, :ifsc, :branch, :type)
            ON DUPLICATE KEY UPDATE 
                account_holder_name = VALUES(account_holder_name),
                bank_name = VALUES(bank_name),
                account_number = VALUES(account_number),
                ifsc_code = VALUES(ifsc_code),
                branch_name = VALUES(branch_name),
                account_type = VALUES(account_type),
                updated_at = NOW()
        ");
        $stmt->execute([
            ':eid'    => $employeeId,
            ':holder' => $data['account_holder_name'] ?? 'Priya Nair',
            ':bank'   => $data['bank_name'] ?? 'HDFC Bank',
            ':acc'    => $data['account_number'] ?? '',
            ':ifsc'   => $data['ifsc_code'] ?? '',
            ':branch' => $data['branch_name'] ?? '',
            ':type'   => $data['account_type'] ?? 'Savings'
        ]);
    }
}
