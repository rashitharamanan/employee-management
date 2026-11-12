<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secret;
    private int $expiration;

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'] ?? '';

        $this->expiration = (int) (
            $_ENV['JWT_EXPIRATION'] ?? 3600
        );

        if ($this->secret === '') {
            throw new \RuntimeException(
                'JWT_SECRET is not configured.'
            );
        }
    }

    /**
     * Generate JWT token
     */
    public function generateToken(array $user): string
    {
        $issuedAt = time();

        $payload = [
            'iss' => 'employee-qr-system',

            'iat' => $issuedAt,

            'exp' => $issuedAt + $this->expiration,

            // User ID
            'sub' => (string) $user['id'],

            // User email
            'email' => $user['email'],

            // Role ID
            'role_id' => (int) $user['role_id'],

            // Role name
            'role' => $user['role'] ?? ($user['role_name'] ?? 'EMPLOYEE'),

            // Employee ID if linked
            'employee_id' => !empty($user['employee_id']) ? (int) $user['employee_id'] : null,

            // Scope information
            'scope_type' => $user['scope_type'] ?? null,

            'scope_id' => $user['scope_id'] ?? null
        ];

        return JWT::encode(
            $payload,
            $this->secret,
            'HS256'
        );
    }

    /**
     * Validate JWT token
     */
    public function validateToken(string $token): object
    {
        return JWT::decode(
            $token,
            new Key(
                $this->secret,
                'HS256'
            )
        );
    }
}