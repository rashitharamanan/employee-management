<?php

namespace App\Repositories\Hr;

use PDO;

class PayrollRepository
{
    public function __construct(private PDO $db)
    {
    }

    /**
     * Active employee master records used to compute the payroll draft.
     */
    public function getEmployees(): array
    {
        return $this->db->query(
            "SELECT id, employee_code, full_name, profile_photo, department, designation,
                    joining_date, employment_type, basic_salary
             FROM employees
             ORDER BY full_name ASC"
        )->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Whether a payroll run already exists for the given month.
     */
    public function hasRun(string $month): bool
    {
        $statement = $this->db->prepare("SELECT COUNT(*) FROM payroll_records WHERE pay_period = :month");
        $statement->execute([':month' => $month]);
        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * Persisted payroll records for a month, joined with employee details.
     */
    public function getMonthRecords(string $month): array
    {
        $statement = $this->db->prepare(
            "SELECT pr.id, pr.employee_id, pr.pay_period,
                    e.employee_code, e.full_name, e.profile_photo, e.department, e.designation,
                    e.joining_date, e.employment_type,
                    pr.basic_salary, pr.hra, pr.special_allowance, pr.allowances, pr.gross_pay,
                    pr.pf, pr.professional_tax, pr.income_tax, pr.deductions, pr.net_pay,
                    pr.status, pr.paid_on,
                    (pr.basic_salary <= 0) AS salary_pending
             FROM payroll_records pr
             INNER JOIN employees e ON e.id = pr.employee_id
             WHERE pr.pay_period = :month
             ORDER BY e.full_name ASC"
        );
        $statement->execute([':month' => $month]);
        return $statement->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
/**
     * Upsert a full payroll run for a month. Money columns are refreshed while
     * the status / paid_on of existing records is preserved.
     */
    public function saveRun(string $month, array $records): int
    {
        if (empty($records)) {
            return 0;
        }

        $statement = $this->db->prepare(
            "INSERT INTO payroll_records
             (employee_id, pay_period, basic_salary, hra, special_allowance, allowances, gross_pay,
              pf, professional_tax, income_tax, deductions, net_pay, status)
             VALUES (:employee_id, :pay_period, :basic_salary, :hra, :special_allowance, :allowances, :gross_pay,
                     :pf, :professional_tax, :income_tax, :deductions, :net_pay, 'PENDING')
             ON DUPLICATE KEY UPDATE
                 basic_salary = VALUES(basic_salary),
                 hra = VALUES(hra),
                 special_allowance = VALUES(special_allowance),
                 allowances = VALUES(allowances),
                 gross_pay = VALUES(gross_pay),
                 pf = VALUES(pf),
                 professional_tax = VALUES(professional_tax),
                 income_tax = VALUES(income_tax),
                 deductions = VALUES(deductions),
                 net_pay = VALUES(net_pay)"
        );

        $saved = 0;
        foreach ($records as $record) {
            $saved += (int) $statement->execute([
                ':employee_id' => $record['employee_id'],
                ':pay_period' => $month,
                ':basic_salary' => $record['basic_salary'],
                ':hra' => $record['hra'],
                ':special_allowance' => $record['special_allowance'],
                ':allowances' => $record['allowances'],
                ':gross_pay' => $record['gross_pay'],
                ':pf' => $record['pf'],
                ':professional_tax' => $record['professional_tax'],
                ':income_tax' => $record['income_tax'],
                ':deductions' => $record['deductions'],
                ':net_pay' => $record['net_pay'],
            ]);
        }

        return $saved;
    }

    /**
     * Find a single persisted payroll record joined with the employee.
     */
    public function findPayslip(int $id): ?array
    {
        $statement = $this->db->prepare(
            "SELECT pr.id, pr.employee_id, pr.pay_period,
                    e.employee_code, e.full_name, e.profile_photo, e.department, e.designation,
                    e.joining_date, e.employment_type,
                    pr.basic_salary, pr.hra, pr.special_allowance, pr.allowances, pr.gross_pay,
                    pr.pf, pr.professional_tax, pr.income_tax, pr.deductions, pr.net_pay,
                    pr.status, pr.paid_on,
                    (pr.basic_salary <= 0) AS salary_pending
             FROM payroll_records pr
             INNER JOIN employees e ON e.id = pr.employee_id
             WHERE pr.id = :id
             LIMIT 1"
        );
        $statement->execute([':id' => $id]);
        $record = $statement->fetch(PDO::FETCH_ASSOC);

        return $record ?: null;
    }

    /**
     * Update a payroll record status. Marking PAID records the payment date.
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->updateStatusByMonth($id, $status, date('Y-m'), $id);
    }

    /**
     * Update payroll status by pay period month and employee ID or record ID.
     */
    public function updateStatusByMonth(int $id, string $status, string $month, int $employeeId = 0): bool
    {
        // 1. Try finding by record ID & month
        $check = $this->db->prepare("SELECT id, status FROM payroll_records WHERE id = :id AND pay_period = :month");
        $check->execute([':id' => $id, ':month' => $month]);
        $existing = $check->fetch(PDO::FETCH_ASSOC);

        // 2. If not found, try finding by employee_id & month
        if (!$existing) {
            $empIdToSearch = $employeeId > 0 ? $employeeId : $id;
            $check2 = $this->db->prepare("SELECT id, status FROM payroll_records WHERE employee_id = :emp_id AND pay_period = :month");
            $check2->execute([':emp_id' => $empIdToSearch, ':month' => $month]);
            $existing = $check2->fetch(PDO::FETCH_ASSOC);
        }

        // 3. Fallback: find by record ID alone
        if (!$existing) {
            $check3 = $this->db->prepare("SELECT id, status FROM payroll_records WHERE id = :id");
            $check3->execute([':id' => $id]);
            $existing = $check3->fetch(PDO::FETCH_ASSOC);
        }

        if (!$existing) {
            return false;
        }

        $recordId = (int) $existing['id'];
        $paidOn = ($status === 'PAID') ? date('Y-m-d') : null;

        $update = $this->db->prepare(
            "UPDATE payroll_records
                SET status = :status,
                    paid_on = :paid_on
              WHERE id = :record_id"
        );
        $update->execute([
            ':record_id' => $recordId,
            ':status' => $status,
            ':paid_on' => $paidOn,
        ]);

        return true;
    }

    /**
     * Distinct departments for the filter dropdown.
     */
    public function getDepartments(): array
    {
        return $this->db->query(
            "SELECT DISTINCT department FROM employees
             WHERE status = 'ACTIVE' AND department IS NOT NULL AND department != ''
             ORDER BY department ASC"
        )->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }
}