<?php

namespace App\Middleware;

use App\Repositories\PermissionRepository;

class PermissionMiddleware
{
    public static function handle(
        PermissionRepository $permissionRepository,
        string $requiredPermission
    ): void {

        /*
         * Get authenticated user
         * from AuthMiddleware
         */
        $authenticatedUser =
            $GLOBALS['authenticated_user'] ?? null;

        /*
         * Authentication information missing
         */
        if (!$authenticatedUser) {

            self::forbidden(
                'Authentication information is missing.'
            );
        }

        /*
         * Get role from JWT
         */
        $role =
            $authenticatedUser->role ?? null;

        if (!$role) {

            self::forbidden(
                'User role is missing from authentication token.'
            );
        }

        /*
         * Check permission
         */
        $hasPermission =
            $permissionRepository->hasPermission(
                (string) $role,
                $requiredPermission
            );

        /*
         * Permission denied
         */
        if (!$hasPermission) {

            self::forbidden(
                'You do not have permission to perform this action.'
            );
        }
    }

    /**
     * Return forbidden response
     */
    private static function forbidden(
        string $message
    ): void {

        http_response_code(403);

        header(
            'Content-Type: application/json'
        );

        echo json_encode([
            'success' => false,
            'message' => $message
        ]);

        exit;
    }
}