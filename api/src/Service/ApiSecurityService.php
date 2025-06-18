<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class ApiSecurityService
{
    private string $secretKey;
    private string $apiKey;

    public function __construct(string $secretKey, string $apiKey)
    {
        $this->secretKey = $secretKey;
        $this->apiKey = $apiKey;
    }

    public function validateRequest(Request $request, string $route): ?string
    {
        $apiKey = $request->headers->get('X-API-KEY');
        if (empty($apiKey)) {
            return 'Missing API key';
        }
        if ($apiKey !== $this->apiKey) {
            return 'Invalid API key';
        }

        $xDate = $request->headers->get('X-DATE');
        if (empty($xDate)) {
            return 'Missing X-DATE header';
        }

        $authorization = $request->headers->get('Authorization');
        if (empty($authorization)) {
            return 'Missing Authorization header';
        }

        $method = $request->getMethod();
        $body = $request->getContent();

        $data = $body . ':' . $method . ':' . $route . ':' . $xDate . ':';
        $expectedSignature = hash_hmac('sha512', $data, $this->secretKey);
        $expectedAuthorization = 'MYSIGN ' . $expectedSignature;

        if ($authorization !== $expectedAuthorization) {
            return 'Invalid HMAC signature';
        }

        return null;
    }

    public function buildSignedResponse(array $responseData, string $route, string $method): array
    {
        $responseBody = json_encode($responseData);
        $xDate = gmdate('Y-m-d\TH:i:s\Z');
        $data = $responseBody . ':' . $method . ':' . $route . ':' . $xDate . ':';
        $signature = hash_hmac('sha512', $data, $this->secretKey);

        return [
            'body' => $responseData,
            'headers' => [
                'X-DATE' => $xDate,
                'Authorization' => 'MYSIGN ' . $signature,
            ]
        ];
    }
}
