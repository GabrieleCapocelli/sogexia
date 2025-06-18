<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Service\SignatureService;

$apiUrl = $_ENV['API_BASE_URL'];
$secretKey = $_ENV['APP_SECRET_KEY'];
$apiKey = $_ENV['APP_API_KEY'];

$signatureService = new SignatureService($secretKey, $apiKey);
$client = new Client();

$id = (int) $_POST['id'];
$route = "/products/$id";
$url = "$apiUrl$route";

// Prepare body
$body = [
    'status' => $_POST['status'],
    'price' => (float) $_POST['price'],
    'name' => $_POST['name'],
    'stock' => [
        'sold' => (int) $_POST['sold'],
        'available' => (int) $_POST['available'],
    ],
    'description' => $_POST['description'],
];

// Generate signed headers
$headers = $signatureService->headers('PUT', $route, $body);

try {
    $response = $client->put($url, [
        'headers' => $headers,
        'body' => json_encode($body, JSON_UNESCAPED_SLASHES),
        'http_errors' => false,
    ]);

    $statusCode = $response->getStatusCode();
    $responseBody = $response->getBody()->getContents();
    $responseHeaders = $response->getHeaders();

    if ($statusCode === 200) {
        // Verify response signature
        $auth = $responseHeaders['Authorization'][0] ?? '';
        $xDate = $responseHeaders['X-DATE'][0] ?? '';

        if (!$signatureService->verifyResponse($responseBody, 'PUT', $route, $xDate, $auth)) {
            header('Location: index.php?error=Signature%20mismatch%20in%20response');
            exit;
        }

        header('Location: index.php?success=Product%20updated%20successfully');
        exit;
    }

    // Handle structured error from API
    $error = json_decode($responseBody, true)['error'] ?? null;
    $message = $error['title'] ?? 'Unexpected error';
    $detail = $error['detail'] ?? 'No further details';

    header('Location: index.php?error=' . urlencode($message . ': ' . $detail));
    exit;

} catch (RequestException $e) {
    header('Location: index.php?error=' . urlencode('Connection error: ' . $e->getMessage()));
    exit;
} catch (Throwable $e) {
    header('Location: index.php?error=' . urlencode('Unexpected error: ' . $e->getMessage()));
    exit;
}
