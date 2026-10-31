<?php $register = false; require dirname(__DIR__, 2) . "/app/auth.php"; ?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Entre no Foco e encontre espaço para organizar suas tarefas e seu dia.">
    <title>Entrar | Foco</title>
    <link rel="icon" type="image/png" href="../assets/images/FocoApp.png">
    <script src="../assets/js/theme.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="auth-page">
    <a class="skip-link" href="#login">Pular para o formulário</a>

    <div class="auth-layout">
        <main id="login" class="auth-main" tabindex="-1">
            <section class="auth-card" aria-labelledby="login-titulo">
                <a class="brand auth-brand" href="../index.php" aria-label="Foco — página principal">
                    <img class="logo-light" src="../assets/images/FocoEscritaPreto.png" alt="Foco" width="2193"
                        height="717">
                    <img class="logo-dark" src="../assets/images/FocoEscritaBranco.png" alt="Foco" width="2172"
                        height="724">
                </a>

                <header class="auth-heading">
                    <h1 id="login-titulo">Bom ter você de volta.</h1>
                    <p>Entre na sua conta para continuar.</p>
                </header>
                <form class="auth-form" method="post" aria-describedby="login-notice">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label for="email">E-mail</label>
                        <input id="email" name="email" value="<?= e($email) ?>" type="email" autocomplete="username"
                            placeholder="voce@exemplo.com" maxlength="254" required>
                    </div>
                    <div class="field">
                        <label for="senha">Senha</label>
                        <input id="senha" name="password" type="password" autocomplete="current-password"
                            placeholder="Digite sua senha" required>
                    </div>
                    <!-- Mensagens de validação do servidor poderão ser inseridas próximas aos campos. -->
                    <button class="button button-primary auth-submit" type="submit">Entrar na minha
                        conta</button>
                    <p id="login-notice" class="auth-notice" role="status"><?= e($notice) ?></p>
                </form>

                <p class="auth-register">Ainda não tem uma conta? <a href="register.php">Criar conta</a></p>

            </section>

            <a class="auth-back" href="../index.php"><span aria-hidden="true">←</span> Voltar ao meu espaço</a>
        </main>
    </div>
</body>

</html>
