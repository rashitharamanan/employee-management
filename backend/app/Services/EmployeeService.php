<?php

namespace App\Services;

use App\Repositories\Employee\EmployeeRepository;
use App\Services\UserService;
use App\Services\QrService;
use PDO;

class EmployeeService
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private UserService $userService,
        private QrService $qrService,
        private PDO $db
    ) {
    }

    /**
     * Get all employees
     */
    public function getAllEmployees(): array
    {
        return $this->employeeRepository->getAll();
    }

    /**
     * Get one employee
     */
    public function getEmployee(int $id): array
    {
        if ($id <= 0) {
            throw new \Exception(
                'Invalid employee ID.'
            );
        }

        $employee =
            $this->employeeRepository->findById($id);

        if (!$employee) {
            throw new \Exception(
                'Employee not found.'
            );
        }

        return $employee;
    }

    /**
     * Create employee + login account + QR
     */
    public function createEmployee(
        array $data
    ): array {

        /*
         * ==========================================
         * PASSWORD
         * ==========================================
         */

        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? $password;

        if ($password !== '') {
            if (strlen($password) < 8) {
                throw new \Exception(
                    'Login password must contain at least 8 characters.'
                );
            }

            if ($password !== $confirmPassword) {
                throw new \Exception(
                    'Login password and confirm password do not match.'
                );
            }
        } else {
            // If no password provided, generate a default temporary password
            $password = 'Emp@' . bin2hex(random_bytes(3)) . '!';
            $confirmPassword = $password;
        }

        /*
         * ==========================================
         * FULL NAME / FIRST NAME / LAST NAME
         * ==========================================
         */

        $firstName = trim($data['first_name'] ?? '');
        $lastName = trim($data['last_name'] ?? '');
        $fullName = trim($data['full_name'] ?? '');

        if ($fullName === '' && ($firstName !== '' || $lastName !== '')) {
            $fullName = trim($firstName . ' ' . $lastName);
        }

        if ($fullName === '') {
            throw new \Exception(
                'Employee name is required.'
            );
        }

        if ($firstName === '' && $fullName !== '') {
            $parts = explode(' ', $fullName, 2);
            $firstName = $parts[0];
            $lastName = $parts[1] ?? '';
        }

        /*
         * ==========================================
         * OFFICIAL EMAIL
         * ==========================================
         *
         * This email will be used for LOGIN.
         */

        $email = trim(
            $data['email'] ?? $data['login_email'] ?? ''
        );

        if ($email === '') {
            throw new \Exception(
                'Official email is required.'
            );
        }

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new \Exception(
                'Invalid official email address.'
            );
        }

        /*
         * ==========================================
         * PERSONAL EMAIL
         * ==========================================
         */

        $personalEmail = trim(
            $data['personal_email'] ?? ''
        );

        if (
            $personalEmail !== '' &&
            !filter_var(
                $personalEmail,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new \Exception(
                'Invalid personal email address.'
            );
        }

        /*
         * ==========================================
         * PHONE
         * ==========================================
         */

        $phone = trim(
            $data['phone'] ?? ''
        );

        if (
            $phone !== '' &&
            !preg_match(
                '/^[0-9+\s-]{7,20}$/',
                $phone
            )
        ) {
            throw new \Exception(
                'Please enter a valid phone number.'
            );
        }

        $alternatePhone = trim(
            $data['alternate_phone'] ?? ''
        );

        /*
         * ==========================================
         * DATE OF BIRTH
         * ==========================================
         */

        $dateOfBirth =
            $this->normalizeAndValidateDate(
                $data['date_of_birth'] ?? '',
                'date of birth'
            );

        /*
         * ==========================================
         * JOINING DATE
         * ==========================================
         */

        $rawJoiningDate = trim($data['joining_date'] ?? '');

        if ($rawJoiningDate === '') {
            $joiningDate = date('Y-m-d');
        } else {
            $joiningDate =
                $this->normalizeAndValidateDate(
                    $rawJoiningDate,
                    'joining date'
                );
        }

        /*
         * ==========================================
         * DEPARTMENT
         * ==========================================
         */

        $department = trim(
            $data['department'] ?? ''
        );

        if ($department === '') {
            $department = 'General';
        }

        if ($department === '') {
            throw new \Exception(
                'Department is required.'
            );
        }

        /*
         * ==========================================
         * DESIGNATION
         * ==========================================
         */

        $designation = trim(
            $data['designation'] ?? ''
        );

        if ($designation === '') {
            throw new \Exception(
                'Designation is required.'
            );
        }

        /*
         * ==========================================
         * GENDER
         * ==========================================
         */

        $gender = trim(
            $data['gender'] ?? ''
        );

        if ($gender !== '') {

            $gender = strtoupper($gender);

            $allowedGenders = [
                'MALE',
                'FEMALE',
                'OTHER'
            ];

            if (
                !in_array(
                    $gender,
                    $allowedGenders,
                    true
                )
            ) {
                throw new \Exception(
                    'Invalid gender.'
                );
            }
        }

        /*
         * ==========================================
         * EMPLOYMENT TYPE
         * ==========================================
         */

        $employmentType = trim(
            $data['employment_type'] ?? ''
        );

        /*
         * ==========================================
         * REPORTING MANAGER
         * ==========================================
         */

        $reportingManager =
            $data['reporting_manager'] ?? null;

        if (
            $reportingManager !== null &&
            $reportingManager !== ''
        ) {

            if (
                !filter_var(
                    $reportingManager,
                    FILTER_VALIDATE_INT
                )
            ) {
                throw new \Exception(
                    'Invalid reporting manager.'
                );
            }

            $reportingManager =
                (int) $reportingManager;

        } else {

            $reportingManager = null;
        }

        /*
         * ==========================================
         * EMERGENCY CONTACT PHONE
         * ==========================================
         */

        $emergencyPhone = trim(
            $data['emergency_contact_phone'] ?? ''
        );

        if (
            $emergencyPhone !== '' &&
            !preg_match(
                '/^[0-9]{10}$/',
                $emergencyPhone
            )
        ) {
            throw new \Exception(
                'Emergency contact phone must contain exactly 10 digits.'
            );
        }

        /*
         * ==========================================
         * PINCODE
         * ==========================================
         */

        $pincode = trim(
            $data['pincode'] ?? ''
        );

        if (
            $pincode !== '' &&
            !preg_match(
                '/^[0-9]{6}$/',
                $pincode
            )
        ) {
            throw new \Exception(
                'Pincode must contain exactly 6 digits.'
            );
        }

        /*
         * ==========================================
         * EXPERIENCE
         * ==========================================
         */

        $experience =
            $data['experience_years'] ?? null;

        if (
            $experience !== null &&
            $experience !== ''
        ) {

            if (
                !is_numeric($experience) ||
                $experience < 0
            ) {
                throw new \Exception(
                    'Experience must be a valid number.'
                );
            }

            $experience =
                (float) $experience;

        } else {

            $experience = null;
        }

        /*
         * ==========================================
         * EMPLOYEE STATUS
         * ==========================================
         */

        $status = strtoupper(
            trim(
                $data['status'] ?? 'ACTIVE'
            )
        );

        $allowedStatuses = [
            'ACTIVE',
            'INACTIVE',
            'SUSPENDED'
        ];

        if (
            !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            throw new \Exception(
                'Invalid employee status.'
            );
        }

        /*
         * Login account can only be created
         * for an active employee.
         */
        if ($status !== 'ACTIVE') {
            throw new \Exception(
                'Only active employees can have a login account.'
            );
        }

        /*
         * ==========================================
         * EMPLOYEE CODE
         * ==========================================
         */

        $employeeCode = trim($data['employee_code'] ?? '');
        if ($employeeCode === '' || $employeeCode === 'Auto generated') {
            $employeeCode = $this->employeeRepository->getNextEmployeeCode();
        }

        /*
         * ==========================================
         * EMPLOYEE DATA
         * ==========================================
         */

        $employeeData = [

            'employee_code' =>
                $employeeCode,

            'full_name' =>
                $fullName,

            'first_name' =>
                $this->nullableString($firstName),

            'last_name' =>
                $this->nullableString($lastName),

            'date_of_birth' =>
                $dateOfBirth,

            'gender' =>
                $gender !== ''
                    ? $gender
                    : null,

            'marital_status' =>
                $this->nullableString($data['marital_status'] ?? null),

            'nationality' =>
                $this->nullableString($data['nationality'] ?? null),

            'profile_photo' =>
                $this->nullableString($data['profile_photo'] ?? null),

            'email' =>
                $email,

            'personal_email' =>
                $personalEmail !== ''
                    ? $personalEmail
                    : null,

            'phone' =>
                $phone !== ''
                    ? $phone
                    : null,

            'alternate_phone' =>
                $this->nullableString($alternatePhone),

            'department' =>
                $department,

            'designation' =>
                $designation,

            'joining_date' =>
                $joiningDate,

            'employment_type' =>
                $employmentType !== ''
                    ? $employmentType
                    : 'Full Time',

            'reporting_manager' =>
                $reportingManager,

            'work_location' =>
                $this->nullableString($data['work_location'] ?? null),

            'work_shift' =>
                $this->nullableString($data['work_shift'] ?? null),

            'probation_period' =>
                isset($data['probation_period']) && is_numeric($data['probation_period']) ? (int) $data['probation_period'] : null,

            'notice_period' =>
                isset($data['notice_period']) && is_numeric($data['notice_period']) ? (int) $data['notice_period'] : null,

            'basic_salary' =>
                isset($data['basic_salary']) && is_numeric($data['basic_salary']) ? (float) $data['basic_salary'] : null,

            'pay_grade' =>
                $this->nullableString($data['pay_grade'] ?? null),

            'status' =>
                $status,

            'address' =>
                $this->nullableString(
                    $data['address'] ?? null
                ),

            'city' =>
                $this->nullableString(
                    $data['city'] ?? null
                ),

            'state' =>
                $this->nullableString(
                    $data['state'] ?? null
                ),

            'pincode' =>
                $pincode !== ''
                    ? $pincode
                    : null,

            'emergency_contact_name' =>
                $this->nullableString(
                    $data['emergency_contact_name']
                        ?? null
                ),

            'emergency_contact_phone' =>
                $emergencyPhone !== ''
                    ? $emergencyPhone
                    : null,

            'emergency_contact_alternate_phone' =>
                $this->nullableString(
                    $data['emergency_contact_alternate_phone'] ?? null
                ),

            'emergency_contact_relationship' =>
                $this->nullableString(
                    $data[
                        'emergency_contact_relationship'
                    ] ?? null
                ),

            'qualification' =>
                $this->nullableString(
                    $data['qualification'] ?? null
                ),

            'experience_years' =>
                $experience,

            'documents' =>
                is_array($data['documents'] ?? null) ? $data['documents'] : []
        ];

        /*
         * ==========================================
         * DATABASE TRANSACTION
         * ==========================================
         *
         * Employee + User + QR must work together.
         */

        $this->db->beginTransaction();

        try {

            /*
             * --------------------------------------
             * 1. CREATE EMPLOYEE
             * --------------------------------------
             */

            $id =
                $this->employeeRepository->create(
                    $employeeData
                );

            if ($id <= 0) {
                throw new \Exception(
                    'Employee could not be created.'
                );
            }

            /*
             * --------------------------------------
             * 2. CREATE USER LOGIN ACCOUNT
             * --------------------------------------
             *
             * Employee logs in with:
             *
             * Email
             * Password
             *
             * Username is internal or specified.
             */

            $customUsername = trim($data['username'] ?? '');
            $username = $customUsername !== ''
                ? $customUsername
                : $this->generateUsername($email, $id);

            $this->userService->createUser([

                'employee_id' =>
                    $id,

                'company_id' =>
                    $data['company_id'] ?? null,

                'role_id' =>
                    !empty($data['role_id']) ? (int) $data['role_id'] : 4,

                'username' =>
                    $username,

                'email' =>
                    $email,

                'password' =>
                    $password,

                'confirm_password' =>
                    $confirmPassword,

                'status' =>
                    !empty($data['account_status']) ? $data['account_status'] : 'ACTIVE'
            ]);

            /*
             * --------------------------------------
             * 3. GENERATE QR
             * --------------------------------------
             */

            $qrPath =
                $this->qrService->generateEmployeeQr(
                    $employeeCode
                );

            /*
             * --------------------------------------
             * 4. SAVE QR PATH
             * --------------------------------------
             */

            $this->employeeRepository->updateQrPath(
                $id,
                $qrPath
            );

            /*
             * --------------------------------------
             * 5. EVERYTHING SUCCESSFUL
             * --------------------------------------
             */

            $this->db->commit();

        } catch (\Throwable $exception) {

            /*
             * If anything fails:
             *
             * Employee creation
             * User creation
             *
             * will be rolled back.
             */

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }

        /*
         * ==========================================
         * RETURN CREATED EMPLOYEE
         * ==========================================
         */

        $employee =
            $this->employeeRepository->findById(
                $id
            );

        if (!$employee) {
            throw new \Exception(
                'Employee was created but could not be retrieved.'
            );
        }

        return $employee;
    }

    /**
     * Generate internal username.
     *
     * Employee does NOT use this for login.
     * Employee logs in using email + password.
     */
    private function generateUsername(
        string $email,
        int $employeeId
    ): string {

        $emailPart =
            strtolower(
                trim(
                    explode('@', $email)[0]
                )
            );

        $emailPart =
            preg_replace(
                '/[^a-z0-9._-]/',
                '',
                $emailPart
            );

        if (!$emailPart) {
            $emailPart = 'employee';
        }

        /*
         * Employee ID makes it unique.
         *
         * Example:
         * john.123
         */
        return $emailPart . '.' . $employeeId;
    }

    /**
     * Update employee
     */
    public function updateEmployee(
        int $id,
        array $data
    ): array {

        if ($id <= 0) {
            throw new \Exception(
                'Invalid employee ID.'
            );
        }

        $employee =
            $this->employeeRepository->findById($id);

        if (!$employee) {
            throw new \Exception(
                'Employee not found.'
            );
        }

        /*
         * Full name
         */
        $fullName = trim(
            $data['full_name'] ?? ''
        );

        if ($fullName === '') {
            throw new \Exception(
                'Employee name is required.'
            );
        }

        /*
         * Official email
         */
        $email = trim(
            $data['email'] ?? ''
        );

        if (
            $email !== '' &&
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new \Exception(
                'Invalid official email address.'
            );
        }

        /*
         * Personal email
         */
        $personalEmail = trim(
            $data['personal_email'] ?? ''
        );

        if (
            $personalEmail !== '' &&
            !filter_var(
                $personalEmail,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new \Exception(
                'Invalid personal email address.'
            );
        }

        /*
         * Phone
         */
        $phone = trim(
            $data['phone'] ?? ''
        );

        if (
            $phone !== '' &&
            !preg_match(
                '/^[0-9]{10}$/',
                $phone
            )
        ) {
            throw new \Exception(
                'Phone number must contain exactly 10 digits.'
            );
        }

        /*
         * Date of birth
         */
        $dateOfBirth =
            trim($data['date_of_birth'] ?? '');

        $dateOfBirth = $this->normalizeAndValidateDate(
    $dateOfBirth,
    'date of birth'
);

        /*
         * Joining date
         */
        $joiningDate =
            trim($data['joining_date'] ?? '');

        if ($joiningDate === '') {
            throw new \Exception(
                'Joining date is required.'
            );
        }

       $joiningDate = $this->normalizeAndValidateDate(
    $joiningDate,
    'joining date'
);

        /*
         * Department
         */
        $department = trim(
            $data['department'] ?? ''
        );

        if ($department === '') {
            throw new \Exception(
                'Department is required.'
            );
        }

        /*
         * Designation
         */
        $designation = trim(
            $data['designation'] ?? ''
        );

        if ($designation === '') {
            throw new \Exception(
                'Designation is required.'
            );
        }

        /*
         * Status
         */
        if (
            array_key_exists(
                'status',
                $data
            )
        ) {

            $status = strtoupper(
                trim(
                    (string) $data['status']
                )
            );

        } else {

            $status = strtoupper(
                $employee['status'] ?? 'ACTIVE'
            );
        }

        $allowedStatuses = [
            'ACTIVE',
            'INACTIVE',
            'SUSPENDED'
        ];

        if (
            !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            throw new \Exception(
                'Invalid employee status.'
            );
        }

        /*
         * Experience
         */
        $experience =
            $data['experience_years'] ?? null;

        if (
            $experience !== null &&
            $experience !== ''
        ) {

            if (
                !is_numeric($experience) ||
                $experience < 0
            ) {
                throw new \Exception(
                    'Experience must be a valid number.'
                );
            }

            $experience =
                (float) $experience;

        } else {

            $experience = null;
        }

        /*
         * Emergency phone
         */
        $emergencyPhone = trim(
            $data['emergency_contact_phone'] ?? ''
        );

        if (
            $emergencyPhone !== '' &&
            !preg_match(
                '/^[0-9]{10}$/',
                $emergencyPhone
            )
        ) {
            throw new \Exception(
                'Emergency contact phone must contain exactly 10 digits.'
            );
        }

        /*
         * Pincode
         */
        $pincode = trim(
            $data['pincode'] ?? ''
        );

        if (
            $pincode !== '' &&
            !preg_match(
                '/^[0-9]{6}$/',
                $pincode
            )
        ) {
            throw new \Exception(
                'Pincode must contain exactly 6 digits.'
            );
        }

        /*
         * Reporting manager
         */
        $reportingManager =
            $data['reporting_manager'] ?? null;

        if (
            $reportingManager !== null &&
            $reportingManager !== ''
        ) {

            if (
                !filter_var(
                    $reportingManager,
                    FILTER_VALIDATE_INT
                )
            ) {
                throw new \Exception(
                    'Invalid reporting manager.'
                );
            }

            $reportingManager =
                (int) $reportingManager;

        } else {

            $reportingManager = null;
        }

        /*
         * Prepare update data
         */
        $employeeData = [

            'full_name' =>
                $fullName,

            'date_of_birth' =>
                $dateOfBirth !== ''
                    ? $dateOfBirth
                    : null,

            'gender' =>
                !empty($data['gender'])
                    ? strtoupper(
                        trim($data['gender'])
                    )
                    : null,

            'profile_photo' =>
                $data['profile_photo']
                    ?? $employee['profile_photo']
                    ?? null,

            'email' =>
                $email !== ''
                    ? $email
                    : null,

            'personal_email' =>
                $personalEmail !== ''
                    ? $personalEmail
                    : null,

            'phone' =>
                $phone !== ''
                    ? $phone
                    : null,

            'department' =>
                $department,

            'designation' =>
                $designation,

            'joining_date' =>
                $joiningDate,

            'employment_type' =>
                !empty($data['employment_type'])
                    ? trim(
                        $data['employment_type']
                    )
                    : null,

            'reporting_manager' =>
                $reportingManager,

            'status' =>
                $status,

            'address' =>
                $this->nullableString(
                    $data['address'] ?? null
                ),

            'city' =>
                $this->nullableString(
                    $data['city'] ?? null
                ),

            'state' =>
                $this->nullableString(
                    $data['state'] ?? null
                ),

            'pincode' =>
                $pincode !== ''
                    ? $pincode
                    : null,

            'emergency_contact_name' =>
                $this->nullableString(
                    $data[
                        'emergency_contact_name'
                    ] ?? null
                ),

            'emergency_contact_phone' =>
                $emergencyPhone !== ''
                    ? $emergencyPhone
                    : null,

            'emergency_contact_relationship' =>
                $this->nullableString(
                    $data[
                        'emergency_contact_relationship'
                    ] ?? null
                ),

            'qualification' =>
                $this->nullableString(
                    $data['qualification'] ?? null
                ),

            'experience_years' =>
                $experience
        ];

        /*
         * Update employee
         */
        $this->employeeRepository->update(
            $id,
            $employeeData
        );

        /*
         * Return updated employee
         */
        $updatedEmployee =
            $this->employeeRepository->findById(
                $id
            );

        if (!$updatedEmployee) {
            throw new \Exception(
                'Employee could not be retrieved after update.'
            );
        }

        return $updatedEmployee;
    }

    /**
     * Delete employee
     */
    public function deleteEmployee(
        int $id
    ): void {

        if ($id <= 0) {
            throw new \Exception(
                'Invalid employee ID.'
            );
        }

        $employee =
            $this->employeeRepository->findById($id);

        if (!$employee) {
            throw new \Exception(
                'Employee not found.'
            );
        }

        $this->employeeRepository->delete($id);
    }

    /**
     * Get employee by QR code
     */
    public function getEmployeeByQrCode(
        string $employeeCode
    ): array {

        $employeeCode =
            strtoupper(
                trim($employeeCode)
            );

        if ($employeeCode === '') {
            throw new \Exception(
                'Employee code is required.'
            );
        }

        $employee =
            $this->employeeRepository
                ->findByEmployeeCode(
                    $employeeCode
                );

        if (!$employee) {
            throw new \Exception(
                'Employee not found.'
            );
        }

        return $employee;
    }

    /**
     * Validate and normalize date to YYYY-MM-DD
     */
    private function normalizeAndValidateDate(
        string $date,
        string $fieldName
    ): ?string {
        $date = trim($date);
        if ($date === '') {
            return null;
        }

        // Try Y-m-d
        $parsed = \DateTime::createFromFormat('Y-m-d', $date);
        if ($parsed && $parsed->format('Y-m-d') === $date) {
            return $date;
        }

        // Try d/m/Y
        $parsed = \DateTime::createFromFormat('d/m/Y', $date);
        if ($parsed) {
            return $parsed->format('Y-m-d');
        }

        // Try d-m-Y
        $parsed = \DateTime::createFromFormat('d-m-Y', $date);
        if ($parsed) {
            return $parsed->format('Y-m-d');
        }

        // Try strtotime general fallback
        $ts = strtotime($date);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }

        throw new \Exception(
            "Invalid {$fieldName}. Please use YYYY-MM-DD or DD/MM/YYYY format."
        );
    }

    /**
     * Get next sequential employee code
     */
    public function getNextCode(): string
    {
        return $this->employeeRepository->getNextEmployeeCode();
    }

    /**
     * Get dropdown options for the Add Employee form
     */
    public function getFormDataOptions(): array
    {
        // 1. Departments
        $departments = [];
        try {
            $stmt = $this->db->query("SELECT id, name FROM departments ORDER BY name ASC");
            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {}

        // 2. Designations
        $designations = [];
        try {
            $stmt = $this->db->query("SELECT des.id, des.name, des.department_id, dep.name AS department_name FROM designations des LEFT JOIN departments dep ON dep.id = des.department_id ORDER BY des.name ASC");
            $designations = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            try {
                $stmt = $this->db->query("SELECT id, name, department_id FROM designations ORDER BY name ASC");
                $designations = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (\Throwable $e2) {}
        }

        // 3. Reporting Managers
        $managers = $this->employeeRepository->getManagersList();

        // 4. Roles
        $roles = [];
        try {
            $stmt = $this->db->query("SELECT id, name, description FROM roles ORDER BY id ASC");
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {}

        return [
            'departments' => $departments,
            'designations' => $designations,
            'managers' => $managers,
            'roles' => $roles,
            'employment_types' => [
                'Full Time',
                'Contract',
                'Intern',
                'Part Time'
            ],
            'work_shifts' => [
                'General Shift (9:00 AM - 6:00 PM)',
                'Morning Shift (6:00 AM - 2:30 PM)',
                'Evening Shift (2:00 PM - 10:30 PM)',
                'Night Shift (10:00 PM - 6:30 AM)'
            ],
            'work_locations' => [
                'Bangalore, India',
                'Mumbai, India',
                'Delhi, India',
                'Hyderabad, India',
                'Remote'
            ],
            'pay_grades' => [
                'Grade 1 (Junior / Associate)',
                'Grade 2 (Mid-Level / Specialist)',
                'Grade 3 (Senior / Lead)',
                'Grade 4 (Principal / Manager)',
                'Grade 5 (Executive / Director)'
            ],
            'next_code' => $this->employeeRepository->getNextEmployeeCode()
        ];
    }

    /**
     * Upload and validate employee profile photo
     */
    public function uploadPhoto(array $file): array
    {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new \Exception('No photo uploaded.');
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            throw new \Exception('Profile photo must not exceed 2MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (!in_array($mime, $allowed, true)) {
            throw new \Exception('Invalid photo format. Only JPG, PNG, and WebP are allowed.');
        }

        $uploadDir = __DIR__ . '/../../storage/uploads/employees/photos';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'jpg';
        if ($ext === 'jpeg') $ext = 'jpg';
        $filename = 'photo_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception('Failed to save uploaded photo.');
        }

        $relativeUrl = 'storage/uploads/employees/photos/' . $filename;

        return [
            'file_path' => $relativeUrl,
            'filename' => $filename,
            'url' => '../../backend/' . $relativeUrl
        ];
    }

    /**
     * Upload and validate employee document
     */
    public function uploadDocument(array $file, string $type = 'General'): array
    {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new \Exception('No document uploaded.');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new \Exception('Document must not exceed 5MB.');
        }

        $uploadDir = __DIR__ . '/../../storage/uploads/employees/documents';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $origName = basename($file['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION)) ?: 'pdf';
        $filename = 'doc_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception('Failed to save uploaded document.');
        }

        $relativeUrl = 'storage/uploads/employees/documents/' . $filename;

        return [
            'document_type' => $type,
            'document_name' => $origName,
            'file_path' => $relativeUrl,
            'file_size' => (int) $file['size'],
            'url' => '../../backend/' . $relativeUrl
        ];
    }

    /**
     * Convert empty strings to NULL
     */
    private function nullableString(
        mixed $value
    ): ?string {

        if ($value === null) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value !== ''
            ? $value
            : null;
    }
}