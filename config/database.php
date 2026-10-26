<?php
// Uso: $pdo = require __DIR__ . '/../config/database.php';
// Variáveis do ambiente têm precedência sobre o arquivo .env local.

try {
    $envPath = dirname(__DIR__) . '/.env';
    $local = [];

    if (is_file($envPath)) {
        // Leitura literal: não interpreta constantes PHP ou expande variáveis.
        $lines = @file($envPath, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            throw new RuntimeException('Configuração indisponível.');
        }
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                throw new RuntimeException('Configuração inválida.');
            }
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            if (strlen($value) >= 2
                && (($value[0] === '"' && substr($value, -1) === '"')
                    || ($value[0] === "'" && substr($value, -1) === "'"))) {
                $value = substr($value, 1, -1);
            }
            $local[$key] = $value;
        }
    }

    $setting = static function ($key, $default = null) use ($local) {
        $value = getenv($key);
        return $value !== false ? $value : ($local[$key] ?? $default);
    };

    $host = $setting('DB_HOST', '127.0.0.1');
    $port = $setting('DB_PORT', '3306');
    $name = $setting('DB_NAME', 'db_foco');
    $user = $setting('DB_USER');
    $password = $setting('DB_PASS');

    if ($user === null || $user === '' || $password === null
        || !ctype_digit($port) || (int) $port < 1 || (int) $port > 65535
        || !preg_match('/\A[a-zA-Z0-9_.:-]+\z/', $host)
        || !preg_match('/\A[a-zA-Z0-9_]+\z/', $name)) {
        throw new RuntimeException('Configuração inválida.');
    }

    return new PDO(
        "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (Throwable $exception) {
    // Não registrar a exceção original: pode conter dados da conexão.
    error_log('[Foco] Falha ao inicializar a conexão com o banco de dados.');
    if (PHP_SAPI !== 'cli') {
        http_response_code(503);
    }
    exit("Não foi possível conectar ao banco de dados. Tente novamente mais tarde.\n");
}
