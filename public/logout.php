<?php
// Encerra a autenticação somente por POST com CSRF válido, antes de produzir saída HTML.
require dirname(__DIR__) . '/app/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); header('Allow: POST'); exit; }
if (!valid_csrf()) { http_response_code(403); exit('Formulário expirado. Atualize a página.'); }
// Limpa os dados e expira o cookie com os mesmos atributos de caminho e segurança da sessão.
$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $params['path'], 'secure' => $params['secure'], 'httponly' => true, 'samesite' => 'Lax']);
// Invalida a sessão no servidor e retorna à página de login.
session_destroy();
redirect('pages/login.php');
