<?php

namespace App\Service;

class SignatureService
{
    private string $secretKey;
    private string $apiKey;

    public function __construct(string $secretKey, string $apiKey)
    {
        $this->secretKey = $secretKey;
        $this->apiKey = $apiKey;
    }

    public function sign(string $method, string $route, array $body, string $xDate): string
    {
        $bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES);
        $data = $bodyJson . ':' . $method . ':' . $route . ':' . $xDate . ':';
        return 'MYSIGN ' . hash_hmac('sha512', $data, $this->secretKey);
    }

    public function verifyResponse(string $responseBody, string $method, string $route, string $xDate, string $authorization): bool
    {
        if (!$authorization || !$xDate) {
            return false;
        }

        $expected = 'MYSIGN ' . hash_hmac('sha512', $responseBody . ':' . $method . ':' . $route . ':' . $xDate . ':', $this->secretKey);
        return hash_equals($expected, $authorization);
    }

    public function headers(string $method, string $route, array $body): array
    {
        $xDate = gmdate('Y-m-d\\TH:i:s\\Z');
        return [
            'X-DATE' => $xDate,
            'X-API-KEY' => $this->apiKey,
            'Authorization' => $this->sign($method, $route, $body, $xDate),
            'Content-Type' => 'application/json'
        ];
    }
}
