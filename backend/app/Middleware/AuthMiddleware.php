<?php

namespace App\Middleware;

use App\Services\JwtService;
use Firebase\JWT\ExpiredException;

class AuthMiddleware
{
    public static function handle(): void
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Get Authorization Header
            |--------------------------------------------------------------------------
            */

            $authorizationHeader =
                $_SERVER['HTTP_AUTHORIZATION']
                ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
                ?? '';

            if ($authorizationHeader === '' && function_exists('getallheaders')) {
                $headers = getallheaders();
                $authorizationHeader = $headers['Authorization']
                    ?? $headers['authorization']
                    ?? '';
            }

            /*
            |--------------------------------------------------------------------------
            | Check Authorization Header
            |--------------------------------------------------------------------------
            */

            if (empty($authorizationHeader)) {

                self::unauthorized(
                    'Authorization token is required.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Check Bearer Token Format
            |--------------------------------------------------------------------------
            */

            if (
                !preg_match(
                    '/Bearer\s+(\S+)/i',
                    $authorizationHeader,
                    $matches
                )
            ) {

                self::unauthorized(
                    'Invalid authorization format. Use Bearer token.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Extract Token
            |--------------------------------------------------------------------------
            */

            $token = trim($matches[1], " \t\n\r\0\x0B\"'");

            if ($token === '') {
                self::unauthorized(
                    'Authentication token is empty.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validate JWT
            |--------------------------------------------------------------------------
            */

            $jwtService = new JwtService();

            $decodedToken =
                $jwtService->validateToken($token);

            /*
            |--------------------------------------------------------------------------
            | Store Authenticated User Information
            |--------------------------------------------------------------------------
            |
            | This can be used later by controllers/services.
            |
            */

            $GLOBALS['authenticated_user'] = $decodedToken;

        } catch (ExpiredException $exception) {

            self::unauthorized(
                'Token has expired. Please login again.'
            );

        } catch (\Throwable $exception) {

            self::unauthorized(
                'Invalid or expired authentication token.'
            );
        }
    }

    /**
     * Return unauthorized response
     */
    private static function unauthorized(
        string $message
    ): void {

        http_response_code(401);

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