<?php
// Helpers internos: somente public/ deve ser servido pelo Apache.
// Inicializa sessão e recursos compartilhados antes de qualquer saída HTML.
declare(strict_types=1);
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', '0');
// Restringe o transporte da sessão a cookies e rejeita identificadores não inicializados.
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
// Protege o cookie contra leitura por JavaScript e ativa Secure quando a requisição usa HTTPS.
session_set_cookie_params([
    'httponly' => true, 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax', 'path' => '/',
]);
session_start();
// Evita cache de páginas privadas e restringe recursos e enquadramento à política da aplicação.
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
/** Escapa um valor para exibição em texto ou atributos HTML delimitados por aspas. */
function e($value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
/** Encerra a requisição com redirecionamento 303, permitindo GET após um POST. */
function redirect(string $url): void { header('Location: ' . $url, true, 303); exit; }
/** Obtém um campo textual do POST sem espaços externos; rejeita valores em formato de array. */
function input(string $key): string { return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : ''; }
/** Lê a senha sem trim para preservar os espaços que fazem parte da credencial. */
function raw_password(string $key): string { return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : ''; }
/** Reutiliza ou gera um token aleatório vinculado à sessão atual. */
function csrf(): string { return $_SESSION['csrf'] ?? ($_SESSION['csrf'] = bin2hex(random_bytes(32))); }
/** Retorna o campo oculto que transporta o token CSRF nos formulários. */
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf()) . '">'; }
/** Compara o token recebido com o da sessão usando comparação resistente a diferenças de tempo. */
function valid_csrf(): bool { return isset($_POST['csrf']) && is_string($_POST['csrf']) && hash_equals(csrf(), $_POST['csrf']); }
/** Carrega a conexão PDO na primeira chamada e a reutiliza durante a mesma requisição. */
function db(): PDO { static $pdo; return $pdo ?? ($pdo = require dirname(__DIR__) . '/config/database.php'); }
/** Conta caracteres UTF-8, em vez de bytes, para os limites dos campos textuais. */
function length(string $value): int { return mb_strlen($value, 'UTF-8'); }
/** Registra falha genérica e retorna mensagem pública sem detalhes internos da exceção. */
function failure(): string {
    error_log('[Foco] Não foi possível concluir a operação.');
    return 'Não foi possível concluir a operação. Tente novamente mais tarde.';
}
/** Exige sessão autenticada e retorna o identificador usado para restringir as consultas do usuário. */
function require_user(): int {
    if (empty($_SESSION['user_id'])) { redirect('pages/login.php'); }
    return (int) $_SESSION['user_id'];
}
