<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/bootstrap.php';
require_once __DIR__ . '/../includes/contact_forms.php';

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) {
        http_response_code(405);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
        exit;
    }
    header('Location: /');
    exit;
}

// Honeypot spam check
if (!empty($_POST['website_hp'])) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
        exit;
    }
    header('Location: /?newsletter=subscribed#newsletter');
    exit;
}

$rawEmail = (string) ($_POST['email'] ?? '');
$email = strtolower(trim($rawEmail));
$source = trim((string) ($_POST['source'] ?? 'homepage'));
if (strlen($source) > 60) {
    $source = substr($source, 0, 60);
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
    if ($isAjax) {
        http_response_code(422);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
        exit;
    }
    header('Location: /?newsletter=invalid#newsletter');
    exit;
}

// Database Insertion
try {
    $connection = db();
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : null;

    $stmt = $connection->prepare(
        'INSERT INTO newsletter_subscribers (email, source, status, ip_address, user_agent)
         VALUES (:email, :source, :status, :ip, :ua)
         ON DUPLICATE KEY UPDATE status = "subscribed", updated_at = CURRENT_TIMESTAMP'
    );
    $stmt->execute([
        'email' => $email,
        'source' => $source !== '' ? $source : 'website',
        'status' => 'subscribed',
        'ip' => $ip,
        'ua' => $ua,
    ]);

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for subscribing! You will receive our latest project insights and updates.',
        ]);
        exit;
    }

    header('Location: /?newsletter=subscribed#newsletter');
    exit;
} catch (PDOException $error) {
    error_log('Newsletter subscription failed: ' . $error->getMessage());
    if ($isAjax) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Unable to save subscription. Please try again.']);
        exit;
    }
    header('Location: /?newsletter=error#newsletter');
    exit;
}
