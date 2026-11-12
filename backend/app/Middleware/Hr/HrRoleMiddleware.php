<?php

namespace App\Middleware\Hr;

class HrRoleMiddleware
{
    public static function handle(): void
    {
        $user = $GLOBALS['authenticated_user'] ?? null;
        $role = strtoupper((string) ($user->role ?? ''));

        if ($role !== 'HR' && $role !== 'ADMIN') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'This dashboard is available to HR and Admin users only.',
            ]);
            exit;
        }
    }
}
