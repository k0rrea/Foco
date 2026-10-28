<?php
require_once __DIR__ . '/bootstrap.php';
if (!empty($_SESSION['user_id'])) { redirect('../index.php'); }
$error = '';
$name = $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = input('name');
    $email = input('email');
    $password = raw_password('password');
    if (!valid_csrf()) {
        $error = 'Formulário expirado. Atualize a página e tente novamente.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || length($email) > 254) {
        $error = 'Informe um e-mail válido de até 254 caracteres.';
    } elseif ($register && ($name === '' || length($name) > 150)) {
        $error = 'Informe um nome de até 150 caracteres.';
    } elseif ($register && (strlen($password) < 8 || strlen($password) > 72 || strpos($password, "\0") !== false)) {
        $error = 'Use uma senha de 8 a 72 bytes (acentos podem ocupar mais de um byte).';
    } elseif ($register && $password !== raw_password('password_confirmation')) {
        $error = 'As senhas não coincidem.';
    } elseif ($password === '' || strlen($password) > 72 || strpos($password, "\0") !== false) {
        $error = 'E-mail ou senha inválidos.';
    } else {
        try {
            $stmt = db()->prepare('SELECT id, senha_hash FROM usuarios WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($register) {
                if ($user) { $error = 'Este e-mail já está cadastrado.'; }
                else {
                    $stmt = db()->prepare('INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)');
                    $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
                    $_SESSION['flash'] = 'Conta criada. Entre com seu e-mail e senha.';
                    redirect('login.php');
                }
            } elseif ($user && password_verify($password, $user['senha_hash'])) {
                session_regenerate_id(true);
                $_SESSION = ['user_id' => (int) $user['id']];
                csrf();
                redirect('../index.php');
            } else { $error = 'E-mail ou senha inválidos.'; }
        } catch (PDOException $exception) {
            $error = $register && ($exception->errorInfo[1] ?? null) === 1062
                ? 'Este e-mail já está cadastrado.' : failure();
        } catch (Throwable $exception) { $error = failure(); }
    }
}
$notice = $error ?: ($_SESSION['flash'] ?? ($register ? 'Use uma senha de 8 a 72 bytes e confirme seus dados.' : 'Entre para acessar suas tarefas.'));
unset($_SESSION['flash']);
