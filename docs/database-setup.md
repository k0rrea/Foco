## Banco de dados — XAMPP no Linux

Execute os comandos abaixo na raiz do projeto. O banco inicial usa MariaDB 10.4 ou MySQL 5.7/8.x e é utilizado pela autenticação e pelo CRUD de tarefas. Consulte também o [roteiro de testes](auth-and-tasks.md).

### 1. Iniciar o XAMPP

```bash
sudo /opt/lampp/lampp start
/opt/lampp/bin/php -m
```

Confira se `PDO` e `pdo_mysql` aparecem na lista de módulos.

### 2. Importar a estrutura

Use uma conta administrativa do banco (a senha será solicitada no terminal):

```bash
/opt/lampp/bin/mysql --host=127.0.0.1 --user=root --password < database/schema.sql
```

Em instalações locais do XAMPP sem senha de root, pressione Enter quando solicitada. Também é possível importar `database/schema.sql` pela aba **Importar** do phpMyAdmin em `http://localhost/phpmyadmin`.

O SQL cria `db_foco`, `usuarios` e `tarefas`. Cada tarefa pertence a um usuário; excluir um usuário exclui suas tarefas. E-mails são únicos, e `senha_hash` está reservado para hashes produzidos por `password_hash()` no cadastro. Status, prioridades e categorias mantêm os valores usados na interface; ausência de categoria será representada por `NULL`.

A importação pode ser repetida sem apagar registros. Ela não migra nem corrige tabelas preexistentes com estrutura diferente.

### 3. Configurar credenciais locais

Crie um usuário próprio para a aplicação usando o console SQL do phpMyAdmin ou o cliente MySQL, substituindo a senha de exemplo apenas localmente:

```sql
CREATE USER 'foco_app'@'127.0.0.1' IDENTIFIED BY 'SUBSTITUA_POR_UMA_SENHA_LOCAL';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_foco.* TO 'foco_app'@'127.0.0.1';
```

Copie o modelo:

```bash
cp .env.example .env
chmod 600 .env
```

Preencha `DB_USER=foco_app` e `DB_PASS` no `.env`. Ajuste host e porta caso necessário. A senha pode ser envolvida por aspas simples ou duplas; o conteúdo é literal, sem expansão de variáveis ou escapes. Escreva comentários em linhas próprias. Uma senha vazia é aceita somente quando configurada explicitamente como `DB_PASS=`.

O `.env` já é ignorado pelo Git. Não coloque credenciais reais em `.env.example`, no PHP ou em commits. Variáveis de ambiente `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASS` têm precedência sobre o arquivo local.

### 4. Testar a conexão pelo terminal

```bash
/opt/lampp/bin/php -r '$pdo = require "config/database.php"; echo $pdo->query("SELECT 1")->fetchColumn() === 1 ? "Conexão OK\n" : "Falha no teste\n";'
```

O resultado esperado é `Conexão OK`. O teste não altera registros. Na integração PHP, obtenha o PDO com `require` usando o caminho apropriado até `config/database.php`.

Falhas de inicialização encerram a execução com uma mensagem genérica e, em requisições web, status HTTP 503. O log do PHP recebe apenas uma mensagem genérica, sem a exceção original. Confira localmente o serviço, as credenciais e a importação ao investigar uma falha.

### 5. Servir somente a pasta pública

Configure um VirtualHost no Apache do XAMPP, em `/opt/lampp/etc/extra/httpd-vhosts.conf`, usando o caminho absoluto real do projeto:

```apache
<VirtualHost *:80>
    ServerName foco.local
    DocumentRoot "/CAMINHO/Foco/public"
    <Directory "/CAMINHO/Foco/public">
        Require all granted
        AllowOverride None
        Options -Indexes
        DirectoryIndex index.php
    </Directory>
</VirtualHost>
```

Verifique se `Include etc/extra/httpd-vhosts.conf` está habilitado em `/opt/lampp/etc/httpd.conf`. Adicione `127.0.0.1 foco.local` ao `/etc/hosts` e reinicie o Apache com `sudo /opt/lampp/lampp restartapache`. Acesse `http://foco.local`.

Mantenha a raiz do projeto fora de `/opt/lampp/htdocs` e evite aliases que exponham essa raiz: apenas `public/` deve ficar acessível pela web. No `php.ini` do XAMPP (`/opt/lampp/etc/php.ini`), configure `display_errors = Off` e `log_errors = On`, reiniciando o Apache após alterações. Isso também protege contra erros PHP anteriores à execução do tratamento de conexão.
