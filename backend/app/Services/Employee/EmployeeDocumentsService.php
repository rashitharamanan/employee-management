<?php

namespace App\Services\Employee;

use App\Repositories\Employee\EmployeeDocumentsRepository;

class EmployeeDocumentsService
{
    public function __construct(
        private EmployeeDocumentsRepository $repository
    ) {
    }

    /**
     * Get complete bundle for Documents & Payroll page
     */
    public function getDocumentsPayrollData(int $employeeId, ?string $selectedMonth = null): array
    {
        if ($employeeId <= 0) {
            $employeeId = 1;
        }

        // Seed if empty
        $this->repository->seedIfEmpty($employeeId);

        // 1. Employee Info
        $employee = $this->repository->getEmployee($employeeId);

        // 2. Documents
        $allDocs = $this->repository->getDocuments($employeeId);
        $totalDocuments = count($allDocs);

        // Map documents with icon types
        $formattedDocs = [];
        foreach ($allDocs as $doc) {
            $name = $doc['document_name'];
            $type = $doc['document_type'];
            
            // Icon classification
            $iconType = 'pdf';
            if (stripos($name, 'Offer') !== false || stripos($name, 'NDA') !== false) {
                $iconType = 'offer'; // Red PDF icon
            } elseif (stripos($name, 'Aadhar') !== false || stripos($name, 'ID') !== false) {
                $iconType = 'id'; // Blue ID card icon
            } elseif (stripos($name, 'PAN') !== false) {
                $iconType = 'pan'; // Purple card icon
            } elseif (stripos($name, 'Educational') !== false || stripos($name, 'Degree') !== false || stripos($name, 'Marksheet') !== false) {
                $iconType = 'education'; // Green certificate icon
            } elseif (stripos($name, 'Experience') !== false || stripos($name, 'Relieving') !== false) {
                $iconType = 'experience'; // Amber briefcase icon
            }

            $formattedDocs[] = [
                'id'            => (int) $doc['id'],
                'document_name' => $name,
                'type'          => $type,
                'uploaded_on'   => date('d M Y', strtotime($doc['uploaded_on'])),
                'file_path'     => $doc['file_path'],
                'file_size'     => $doc['file_size'] ?: '1.2 MB',
                'icon_type'     => $iconType
            ];
        }

        // 3. Payroll Records
        $payrollList = $this->repository->getPayrollRecords($employeeId);
        $totalPayslips = count($payrollList);

        $targetMonth = $selectedMonth ?: '2025-08';
        $currentPay = null;
        $prevPay = null;

        foreach ($payrollList as $idx => $p) {
            if ($p['pay_period'] === $targetMonth) {
                $currentPay = $p;
                if (isset($payrollList[$idx + 1])) {
                    $prevPay = $payrollList[$idx + 1];
                }
                break;
            }
        }

        if (!$currentPay && !empty($payrollList)) {
            $currentPay = $payrollList[0];
            if (isset($payrollList[1])) {
                $prevPay = $payrollList[1];
            }
        }

        $netSalary = $currentPay ? (float) $currentPay['net_pay'] : 58000;
        $grossSalary = $currentPay ? (float) $currentPay['gross_pay'] : 75000;
        $deductions = $currentPay ? (float) $currentPay['deductions'] : 12000;

        // Compare percentage vs previous month
        $trendPct = 5;
        $trendUp = true;
        if ($prevPay && (float) $prevPay['net_pay'] > 0) {
            $prevNet = (float) $prevPay['net_pay'];
            $pctChange = round((($netSalary - $prevNet) / $prevNet) * 100);
            $trendPct = abs($pctChange);
            $trendUp = $pctChange >= 0;
        }

        // Available periods for dropdown
        $availableMonths = [];
        foreach ($payrollList as $p) {
            $time = strtotime($p['pay_period'] . '-01');
            $label = date('F Y', $time);
            $availableMonths[] = [
                'value'       => $p['pay_period'],
                'label'       => $label,
                'is_selected' => ($p['pay_period'] === ($currentPay['pay_period'] ?? '2025-08'))
            ];
        }

        // Recent Payslips list (top 3)
        $recentPayslips = [];
        $recentSlice = array_slice($payrollList, 0, 3);
        foreach ($recentSlice as $rp) {
            $time = strtotime($rp['pay_period'] . '-01');
            $recentPayslips[] = [
                'id'         => (int) $rp['id'],
                'period'     => $rp['pay_period'],
                'label'      => date('F Y', $time),
                'amount'     => '₹' . number_format((float) $rp['net_pay']),
                'raw_amount' => (float) $rp['net_pay'],
                'status'     => $rp['status'] ?? 'PAID',
                'paid_on'    => $rp['paid_on'] ? date('d M Y', strtotime($rp['paid_on'])) : ''
            ];
        }

        // Bank Details
        $bank = $this->repository->getBankDetails($employeeId);

        return [
            'employee' => [
                'id'          => (int) $employee['id'],
                'name'        => $employee['full_name'] ?: 'Priya Nair',
                'designation' => $employee['designation'] ?: 'UI/UX Designer',
                'department'  => $employee['department'] ?: 'Design & Product',
                'email'       => $employee['email'] ?: 'priya.nair@mindazzle.com',
                'avatar'      => '../../assets/images/priya-avatar.jpg'
            ],

            'kpis' => [
                'total_documents' => [
                    'count'    => $totalDocuments ?: 12,
                    'sub_text' => 'Employment, ID proof, certificates'
                ],
                'latest_salary' => [
                    'amount'   => '₹' . number_format($netSalary),
                    'sub_text' => 'for ' . (isset($currentPay['pay_period']) ? date('F Y', strtotime($currentPay['pay_period'] . '-01')) : 'August 2025')
                ],
                'next_payday' => [
                    'date'     => '30 Sep 2025',
                    'sub_text' => '12 days to go'
                ],
                'payslips' => [
                    'count'    => $totalPayslips ?: 12,
                    'sub_text' => 'Available for download'
                ]
            ],

            'documents' => $formattedDocs,

            'payroll_overview' => [
                'selected_period' => $currentPay['pay_period'] ?? '2025-08',
                'period_label'    => isset($currentPay['pay_period']) ? date('F Y', strtotime($currentPay['pay_period'] . '-01')) : 'August 2025',
                'net_salary'      => '₹' . number_format($netSalary),
                'raw_net_salary'  => $netSalary,
                'trend'           => [
                    'percentage' => $trendPct ?: 5,
                    'is_up'      => $trendUp,
                    'label'      => ($trendUp ? '▲ ' : '▼ ') . ($trendPct ?: 5) . '%',
                    'sub_text'   => 'Compared to last month'
                ],
                'breakdown' => [
                    'gross'      => '₹' . number_format($grossSalary),
                    'deductions' => '₹' . number_format($deductions),
                    'net'        => '₹' . number_format($netSalary)
                ],
                'available_months' => $availableMonths,
                'recent_payslips'  => $recentPayslips
            ],

            'bank_details' => [
                'account_holder' => $bank['account_holder_name'] ?? 'Priya Nair',
                'bank_name'      => $bank['bank_name'] ?? 'HDFC Bank',
                'account_number' => $bank['account_number'] ?? '5010048291823',
                'masked_account' => '•••• •••• •••• ' . substr($bank['account_number'] ?? '4829', -4),
                'ifsc_code'      => $bank['ifsc_code'] ?? 'HDFC0001245',
                'branch'         => $bank['branch_name'] ?? 'Indiranagar, Bengaluru',
                'account_type'   => $bank['account_type'] ?? 'Savings'
            ]
        ];
    }

    /**
     * Get detailed payslip for slip preview/print
     */
    public function getPayslipDetails(int $employeeId, string $period = '2025-08'): array
    {
        $this->repository->seedIfEmpty($employeeId);
        $emp = $this->repository->getEmployee($employeeId);
        $record = $this->repository->getPayrollByPeriod($employeeId, $period);

        $basic = $record ? (float) $record['basic_salary'] : 45000;
        $hra = $record ? (float) $record['hra'] : 18000;
        $special = $record ? (float) $record['special_allowance'] : 12000;
        $gross = $basic + $hra + $special;

        $pf = $record ? (float) $record['pf'] : 5400;
        $pt = $record ? (float) $record['professional_tax'] : 200;
        $tds = $record ? (float) $record['income_tax'] : 6400;
        $deductions = $pf + $pt + $tds;

        $net = $gross - $deductions;
        $bank = $this->repository->getBankDetails($employeeId);

        $time = strtotime($period . '-01');
        $monthName = date('F Y', $time);
        $payDate = $record && $record['paid_on'] ? date('d M Y', strtotime($record['paid_on'])) : date('t M Y', $time);

        return [
            'company' => [
                'name'    => 'Mindazzle Technologies Pvt. Ltd.',
                'address' => 'Embassy TechVillage, Outer Ring Road, Bengaluru 560103',
                'pan'     => 'AAACM9981K',
                'tan'     => 'BLRM12908B'
            ],
            'employee' => [
                'id'            => (int) $emp['id'],
                'employee_code' => $emp['employee_code'] ?: 'EMP1024',
                'name'          => $emp['full_name'] ?: 'Priya Nair',
                'designation'   => $emp['designation'] ?: 'UI/UX Designer',
                'department'    => $emp['department'] ?: 'Design & Product',
                'joining_date'  => $emp['joining_date'] ? date('d M Y', strtotime($emp['joining_date'])) : '10 Jan 2024',
                'bank_name'     => $bank['bank_name'] ?? 'HDFC Bank',
                'bank_account'  => '•••• ' . substr($bank['account_number'] ?? '4829', -4),
                'ifsc'          => $bank['ifsc_code'] ?? 'HDFC0001245'
            ],
            'payroll' => [
                'period'       => $monthName,
                'period_key'   => $period,
                'pay_date'     => $payDate,
                'working_days' => 22,
                'paid_days'    => 22,
                'leave_days'   => 1,
                'earnings' => [
                    ['item' => 'Basic Salary',       'amount' => $basic],
                    ['item' => 'House Rent Allowance (HRA)', 'amount' => $hra],
                    ['item' => 'Special Allowance',  'amount' => $special]
                ],
                'gross_earnings' => $gross,
                'deductions' => [
                    ['item' => 'Provident Fund (PF)', 'amount' => $pf],
                    ['item' => 'Professional Tax (PT)', 'amount' => $pt],
                    ['item' => 'Tax Deducted at Source (TDS)', 'amount' => $tds]
                ],
                'total_deductions' => $deductions,
                'net_pay'          => $net,
                'net_pay_words'    => 'Fifty-Eight Thousand Rupees Only'
            ]
        ];
    }

    /**
     * Upload document
     */
    public function uploadDocument(int $employeeId, array $file, string $type, string $customName = ''): array
    {
        if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Please select a valid file to upload.');
        }

        $origName = $file['name'];
        $fileSize = round($file['size'] / (1024 * 1024), 1) . ' MB';
        if ($file['size'] < 1024 * 1024) {
            $fileSize = round($file['size'] / 1024) . ' KB';
        }

        $uploadDir = __DIR__ . '/../../../../storage/uploads/documents';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($origName, PATHINFO_EXTENSION);
        $cleanName = $customName ?: pathinfo($origName, PATHINFO_FILENAME);
        $filename = 'doc_' . $employeeId . '_' . time() . '.' . $ext;
        $target = $uploadDir . '/' . $filename;

        move_uploaded_file($file['tmp_name'], $target);

        $docId = $this->repository->saveDocument($employeeId, [
            'document_name' => $cleanName,
            'document_type' => $type ?: 'General',
            'uploaded_on'   => date('Y-m-d'),
            'file_path'     => 'storage/uploads/documents/' . $filename,
            'file_size'     => $fileSize
        ]);

        return [
            'id'            => $docId,
            'document_name' => $cleanName,
            'document_type' => $type,
            'uploaded_on'   => date('d M Y'),
            'file_size'     => $fileSize
        ];
    }

    /**
     * Request document
     */
    public function requestDocument(int $employeeId, array $data): int
    {
        if (empty($data['document_type'])) {
            throw new \Exception('Document type is required.');
        }
        return $this->repository->createDocumentRequest($employeeId, $data);
    }

    /**
     * Update bank details
     */
    public function updateBankDetails(int $employeeId, array $data): void
    {
        if (empty($data['account_number']) || empty($data['ifsc_code'])) {
            throw new \Exception('Account number and IFSC code are required.');
        }
        $this->repository->saveBankDetails($employeeId, $data);
    }
}
