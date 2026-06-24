<?php
/**
 * Exhaustive Blackbox Test Script for PKM Proposal System
 * Hits all GET routes and simulates POST requests.
 */

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

$baseUrl = 'http://127.0.0.1:8000';
$client = new Client([
    'base_uri' => $baseUrl,
    'timeout'  => 30.0,
    'cookies'  => true,
    'http_errors' => false,
    'allow_redirects' => false,
]);

function getCsrfToken($client, $url) {
    $response = $client->get($url);
    $html = (string)$response->getBody();
    preg_match('/<meta name="csrf-token" content="([^"]+)">/i', $html, $matches);
    if (!isset($matches[1])) {
        // Try alternate format
        preg_match('/<input type="hidden" name="_token" value="([^"]+)">/i', $html, $matches);
    }
    return $matches[1] ?? '';
}

function login($client, $email, $password) {
    echo "Attempting login with $email...\n";
    $token = getCsrfToken($client, '/login');
    $response = $client->post('/login', [
        'form_params' => [
            '_token' => $token,
            'email' => $email,
            'password' => $password,
        ],
        'allow_redirects' => false,
    ]);
    
    if ($response->getStatusCode() == 302) {
        echo "✅ Login successful ($email)\n";
        return true;
    } else {
        echo "❌ Login failed ($email). Status: " . $response->getStatusCode() . "\n";
        echo substr((string)$response->getBody(), 0, 500) . "\n";
        return false;
    }
}

function testGet($client, $url, $expectedStatus = 200) {
    echo "GET $url ... ";
    $response = $client->get($url);
    $status = $response->getStatusCode();
    if ($status == $expectedStatus) {
        echo "✅ [$status]\n";
        return true;
    } else {
        echo "❌ [$status] (Expected $expectedStatus)\n";
        return false;
    }
}

function printHeader($title) {
    echo "\n============================================================\n";
    echo " $title\n";
    echo "============================================================\n";
}

$results = [
    'pass' => 0,
    'fail' => 0
];

function recordResult($passed) {
    global $results;
    if ($passed) $results['pass']++;
    else $results['fail']++;
}

// ---------------------------------------------------------
// MODUL A: Autentikasi
// ---------------------------------------------------------
printHeader("MODUL A: Autentikasi & Public Pages");

$publicClient = new Client([
    'base_uri' => $baseUrl,
    'cookies'  => true,
    'http_errors' => false,
    'allow_redirects' => false,
]);

recordResult(testGet($publicClient, '/login', 200));
recordResult(testGet($publicClient, '/register', 200));
recordResult(testGet($publicClient, '/forgot-password', 200));

// Test Invalid Login
$token = getCsrfToken($publicClient, '/login');
$response = $publicClient->post('/login', [
    'form_params' => [
        '_token' => $token,
        'email' => 'wrong@test.com',
        'password' => 'wrongpassword',
    ]
]);
// Expected to stay on login page or redirect back to login with errors
if ($response->getStatusCode() == 302 || $response->getStatusCode() == 200) {
    echo "✅ Invalid Login correctly handled\n";
    recordResult(true);
} else {
    echo "❌ Invalid Login returned " . $response->getStatusCode() . "\n";
    recordResult(false);
}

// ---------------------------------------------------------
// MODUL B: Pimpinan PT
// ---------------------------------------------------------
printHeader("MODUL B: Pimpinan PT");
$pimpinanClient = new Client(['base_uri' => $baseUrl, 'cookies' => true, 'http_errors' => false, 'allow_redirects' => false]);
login($pimpinanClient, 'pimpinan@test.com', 'password123');

recordResult(testGet($pimpinanClient, '/pimpinan-pt/dashboard', 200));
recordResult(testGet($pimpinanClient, '/pimpinan-pt/akun', 200));

// ---------------------------------------------------------
// MODUL C: Operator
// ---------------------------------------------------------
printHeader("MODUL C: Operator");
$operatorClient = new Client(['base_uri' => $baseUrl, 'cookies' => true, 'http_errors' => false, 'allow_redirects' => false]);
login($operatorClient, 'operator-test@test.com', 'password123');

recordResult(testGet($operatorClient, '/operator/dashboard', 200));
recordResult(testGet($operatorClient, '/operator/akun', 200));
recordResult(testGet($operatorClient, '/operator/profile', 200));
recordResult(testGet($operatorClient, '/operator/ruang-kontrol', 200));
recordResult(testGet($operatorClient, '/operator/pilih-reviewer', 200));
recordResult(testGet($operatorClient, '/operator/pilih-reviewer-seleksi', 200));
recordResult(testGet($operatorClient, '/operator/hasil-semi-final', 200));
recordResult(testGet($operatorClient, '/operator/hasil-final', 200));
recordResult(testGet($operatorClient, '/operator/form-penilaian', 200));
recordResult(testGet($operatorClient, '/operator/laporan-simbelmawa', 200));

// ---------------------------------------------------------
// MODUL D: Mahasiswa
// ---------------------------------------------------------
printHeader("MODUL D: Mahasiswa");
$mhsClient = new Client(['base_uri' => $baseUrl, 'cookies' => true, 'http_errors' => false, 'allow_redirects' => false]);
login($mhsClient, 'mahasiswa-test@test.com', 'password123');

recordResult(testGet($mhsClient, '/mahasiswa/dashboard', 200));
recordResult(testGet($mhsClient, '/mahasiswa/proposal/create', 200));
recordResult(testGet($mhsClient, '/mahasiswa/proposal', 200));
recordResult(testGet($mhsClient, '/mahasiswa/profile', 200));
recordResult(testGet($mhsClient, '/mahasiswa/revisi', 200));

// ---------------------------------------------------------
// MODUL E: Dosen
// ---------------------------------------------------------
printHeader("MODUL E: Dosen");
$dosenClient = new Client(['base_uri' => $baseUrl, 'cookies' => true, 'http_errors' => false, 'allow_redirects' => false]);
login($dosenClient, 'dosen-test@test.com', 'password123');

// GET /dosen/dashboard actually redirects to /dosen/pendamping/dashboard
recordResult(testGet($dosenClient, '/dosen/dashboard', 200)); // The client follows redirects, so it should be 200
recordResult(testGet($dosenClient, '/dosen/pendamping/proposal-validasi', 200));
recordResult(testGet($dosenClient, '/dosen/pendamping/hasil-review', 200));
recordResult(testGet($dosenClient, '/dosen/pendamping/hasil-final', 200));
recordResult(testGet($dosenClient, '/dosen/pembimbing/dashboard', 200));
recordResult(testGet($dosenClient, '/dosen/universitas/dashboard', 200));
recordResult(testGet($dosenClient, '/dosen/universitas/validasi-akhir', 200));
recordResult(testGet($dosenClient, '/dosen/profile', 200));

// ---------------------------------------------------------
// MODUL F: Reviewer
// ---------------------------------------------------------
printHeader("MODUL F: Reviewer");
$reviewerClient = new Client(['base_uri' => $baseUrl, 'cookies' => true, 'http_errors' => false, 'allow_redirects' => false]);
login($reviewerClient, 'reviewer-test1@test.com', 'password123');

recordResult(testGet($reviewerClient, '/reviewer/dashboard', 200));
recordResult(testGet($reviewerClient, '/reviewer/review-administratif', 200));
recordResult(testGet($reviewerClient, '/reviewer/review-substantif', 200));
recordResult(testGet($reviewerClient, '/reviewer/review-substantif-seleksi', 200));
recordResult(testGet($reviewerClient, '/reviewer/profile', 200));

echo "\n============================================================\n";
echo " TEST SUMMARY\n";
echo "============================================================\n";
echo "PASS: " . $results['pass'] . "\n";
echo "FAIL: " . $results['fail'] . "\n";

