<?php
// Controlador compartilhado de login e cadastro; a página define o modo pela variável $register.
require_once __DIR__ . '/bootstrap.php';
// Uma sessão já autenticada segue diretamente para a área de tarefas.
if (!empty($_SESSION['user_id'])) { redirect('../index.php'); }
$error = '';
$name = $email = '';
// Processa apenas envios do formulário; em GET, prepara os valores iniciais da página.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = input('name');
    $email = input('email');
    $password = raw_password('password');
    // Valida origem do formulário, e-mail e regras específicas de cadastro antes de consultar o banco.
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
            // Parâmetros separados do SQL impedem que os dados recebidos sejam interpretados como instruções.
            $stmt = db()->prepare('SELECT id, senha_hash FROM usuarios WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            // No cadastro, impede e-mail repetido e armazena somente o hash da senha.
            if ($register) {
                if ($user) { $error = 'Este e-mail já está cadastrado.'; }
                else {
                    $stmt = db()->prepare('INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)');
                    $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
                    $_SESSION['flash'] = 'Conta criada. Entre com seu e-mail e senha.';
                    redirect('login.php');
                }
            } elseif ($user && password_verify($password, $user['senha_hash'])) {
                // Após verificar a senha, troca o identificador da sessão e cria um novo token CSRF.
                session_regenerate_id(true);
                $_SESSION = ['user_id' => (int) $user['id']];
                csrf();
                redirect('../index.php');
            } else { $error = 'E-mail ou senha inválidos.'; }
        // O código 1062 trata também duplicidade de e-mail entre requisições concorrentes.
        } catch (PDOException $exception) {
            $error = $register && ($exception->errorInfo[1] ?? null) === 1062
                ? 'Este e-mail já está cadastrado.' : failure();
        } catch (Throwable $exception) { $error = failure(); }
    }
}
// Prioriza erros e consome a mensagem de sessão uma única vez após o redirecionamento.
$notice = $error ?: ($_SESSION['flash'] ?? ($register ? 'Use uma senha de 8 a 72 bytes e confirme seus dados.' : 'Entre para acessar suas tarefas.'));
unset($_SESSION['flash']);
