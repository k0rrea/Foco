<?php
// Helpers internos: somente public/ deve ser servido pelo Apache.
declare(strict_types=1);
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', '0');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params([
    'httponly' => true, 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax', 'path' => '/',
]);
session_start();
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
function e($value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function redirect(string $url): void { header('Location: ' . $url, true, 303); exit; }
function input(string $key): string { return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : ''; }
function raw_password(string $key): string { return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : ''; }
function csrf(): string { return $_SESSION['csrf'] ?? ($_SESSION['csrf'] = bin2hex(random_bytes(32))); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf()) . '">'; }
function valid_csrf(): bool { return isset($_POST['csrf']) && is_string($_POST['csrf']) && hash_equals(csrf(), $_POST['csrf']); }
function db(): PDO { static $pdo; return $pdo ?? ($pdo = require dirname(__DIR__) . '/config/database.php'); }
function length(string $value): int { return mb_strlen($value, 'UTF-8'); }
function failure(): string {
    error_log('[Foco] Não foi possível concluir a operação.');
    return 'Não foi possível concluir a operação. Tente novamente mais tarde.';
}
function require_user(): int {
    if (empty($_SESSION['user_id'])) { redirect('pages/login.php'); }
    return (int) $_SESSION['user_id'];
}
