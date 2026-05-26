<?php
// contact.php — VajraDefend contact form handler

// ── Config ────────────────────────────────
define('NOTIFY_EMAIL', 'surarajasekhar@proton.me');   // Change to your real email
define('LOG_FILE',     '/var/log/vajradefend_leads.log');
define('SITE_NAME',    'VajraDefend');

// ── Rate limiting (simple IP-based) ───────
session_start();
$ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$key = 'form_submit_' . md5($ip);

if (!isset($_SESSION[$key])) $_SESSION[$key] = 0;
if ($_SESSION[$key] >= 5) {
    http_response_code(429);
    die(json_encode(['error' => 'Too many submissions. Please try again later.']));
}

// ── Only accept POST ───────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// ── Sanitize inputs ────────────────────────
function clean(string $val): string {
    return htmlspecialchars(trim(strip_tags($val)), ENT_QUOTES, 'UTF-8');
}

$name    = clean($_POST['name']    ?? '');
$company = clean($_POST['company'] ?? '');
$email   = clean($_POST['email']   ?? '');
$phone   = clean($_POST['phone']   ?? '');
$service = clean($_POST['service'] ?? '');
$message = clean($_POST['message'] ?? '');

// ── Validate ───────────────────────────────
$errors = [];

if (strlen($name) < 2)                         $errors[] = 'Full name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
if (strlen($message) < 10)                     $errors[] = 'Please provide a brief description.';

// Honeypot check (add hidden field 'website' to form)
if (!empty($_POST['website'])) {
    // Silently discard bots
    header('Location: index.html?submitted=1');
    exit;
}

if ($errors) {
    // Return to form with errors
    $err_str = urlencode(implode(' ', $errors));
    header("Location: index.html?error=$err_str");
    exit;
}

// ── Log to file ────────────────────────────
$timestamp = date('Y-m-d H:i:s T');
$log_entry = <<<LOG
=====================================
Date     : {$timestamp}
IP       : {$ip}
Name     : {$name}
Company  : {$company}
Email    : {$email}
Phone    : {$phone}
Service  : {$service}
Message  :
{$message}
=====================================

LOG;

@file_put_contents(LOG_FILE, $log_entry, FILE_APPEND | LOCK_EX);

// ── Send email notification ────────────────
$subject  = "[VajraDefend Lead] {$name} — {$service}";
$body     = "New contact form submission from {$name} ({$email})\n\n{$log_entry}";
$headers  = "From: surarajasekhar@proton.me\r\nReply-To: {$email}\r\nX-Mailer: PHP/".phpversion();

@mail(NOTIFY_EMAIL, $subject, $body, $headers);

// ── Increment rate limit ───────────────────
$_SESSION[$key]++;

// ── Redirect with success ──────────────────
header('Location: index.html?submitted=1');
exit;
