<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ApiErrorService
{
    public function createErrorResponse(string|ConstraintViolationListInterface|null $validationError): ?JsonResponse
    {
        if ($validationError === null) {
            return null;
        }

        if ($validationError instanceof ConstraintViolationListInterface) {
            $violations = [];

            foreach ($validationError as $violation) {
                $field = $violation->getPropertyPath();
                if (!$field) {
                    $field = 'unknown';
                }
                $violations[] = [
                    'field' => $field,
                    'message' => $violation->getMessage(),
                ];
            }

            return new JsonResponse([
                'error' => [
                    'code' => 'PAR-001',
                    'title' => 'Missing or invalid parameters',
                    'detail' => 'Some parameters are missing or invalid.',
                    'violations' => $violations,
                ]
            ], 400);
        }

        $errorMap = match ($validationError) {
            'Missing API key',
            'Missing X-DATE header',
            'Missing Authorization header',
            'Invalid HMAC signature' => [
                'code' => 'AUTH-001',
                'title' => 'Authentication error',
                'detail' => 'The request does not come from authenticated user',
                'status' => 401,
            ],
            'Invalid API key' => [
                'code' => 'FORB-001',
                'title' => 'Forbidden',
                'detail' => 'Access denied for authenticated user',
                'status' => 403,
            ],
            default => [
                'code' => 'AUTH-000',
                'title' => 'Authentication error',
                'detail' => 'An unknown authentication error occurred',
                'status' => 401,
            ],
        };

        return new JsonResponse([
            'error' => [
                'code' => $errorMap['code'],
                'title' => $errorMap['title'],
                'detail' => $errorMap['detail'],
            ]
        ], $errorMap['status']);
    }
}
