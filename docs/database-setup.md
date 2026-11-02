# Banco de dados

Para instalar desde o clone, configurar `.env` e executar com o servidor embutido do PHP, siga o [README](../README.md). Apache é uma [alternativa opcional](apache-setup.md). Este documento detalha a importação e o modelo existente. Execute comandos relativos na raiz do clone.

## Importação por terminal (alternativa ao phpMyAdmin)

Use uma conta administrativa do banco, com credenciais da sua instalação. `--password` solicita a senha sem colocá-la no comando; pressione Enter somente se a conta realmente tiver senha vazia.

Linux (Bash):

```bash
/opt/lampp/bin/mysql --host=127.0.0.1 --port=3306 --user=root --password < database/schema.sql
```

Windows (**PowerShell**), usando a porta 3308 do teste relatado. Troque por 3306 se essa for a porta da sua instalação:

```powershell
& "C:\xampp\mysql\bin\mysql.exe" --host=127.0.0.1 --port=3308 --user=root --password
```

Depois de informar a senha, no **cliente MariaDB**, importe e confira:

```sql
SOURCE database/schema.sql;
USE db_foco;
SHOW TABLES;
EXIT;
```

`SOURCE` é um comando do cliente MariaDB e lê o caminho relativo à pasta de onde ele foi iniciado: a raiz do clone. O resultado deve incluir `usuarios` e `tarefas`. No Linux, após importar, abra o cliente com os mesmos parâmetros, sem `< database/schema.sql`, e execute `USE db_foco;` e `SHOW TABLES;` para conferir.

O [PowerShell não oferece `<` como redirecionamento de entrada](https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_redirection). Evite substituir a leitura do arquivo por um pipeline de `Get-Content` sem controlar a codificação, que varia entre versões do PowerShell. O procedimento com [SOURCE do cliente MariaDB](https://mariadb.com/docs/server/clients-and-utilities/mariadb-client/mariadb-command-line-client) evita essa conversão pelo PowerShell.

Como alternativa, no **cmd.exe**, vá à raiz do clone e use `<`. Este exemplo assume o clone em `C:\Projetos\Foco`; ajuste para a pasta real:

```bat
cd /d C:\Projetos\Foco
"C:\xampp\mysql\bin\mysql.exe" --host=127.0.0.1 --port=3308 --user=root --password < database\schema.sql
```

Ajuste usuário, porta e caminhos conforme a instalação. `mysql.exe` é o nome do cliente fornecido pelo XAMPP mesmo quando o servidor é MariaDB. Para um teste local isolado, uma conta existente, inclusive `root`, pode ser usada se autorizada. Para uso persistente ou compartilhado, prefira a conta limitada descrita abaixo.

Se preferir **phpMyAdmin**, ele exige Apache disponível: abra `http://localhost/phpmyadmin/` (ajuste a porta HTTP se necessário), entre com uma conta administrativa válida, selecione **Importar** e envie `database/schema.sql`. Essa alternativa não é necessária para usar o servidor embutido do PHP, que não hospeda o phpMyAdmin.

O arquivo cria `db_foco` e seleciona esse banco com `USE`. Não substitua apenas `DB_NAME` por outro nome esperando que o SQL acompanhe a mudança. O esquema usa InnoDB e `utf8mb4_unicode_ci`, compatíveis com MariaDB 10.4 e MySQL 5.7/8.x.

### Banco já existente

`IF NOT EXISTS` evita recriar tabelas, mas não altera colunas ou converte dados existentes. Confira a estrutura antes de integrar uma instalação antiga. No cliente MariaDB ou na aba SQL do phpMyAdmin, execute:

```sql
USE db_foco;
SHOW TABLES;
SHOW CREATE TABLE usuarios;
SHOW CREATE TABLE tarefas;
```

Compare com `database/schema.sql`. Se houver divergência, faça backup e planeje uma migração; não exclua tabelas ou registros. Reimportar o SQL não resolve colunas ausentes.

## Usuário específico da aplicação (opcional, recomendado)

A aplicação precisa apenas de SELECT, INSERT, UPDATE e DELETE nas tabelas de `db_foco`, sem permissões administrativas ou de criação de tabelas. Após importar o SQL, execute uma vez com uma conta autorizada a criar usuários e conceder privilégios:

```sql
CREATE USER 'foco_app'@'127.0.0.1' IDENTIFIED BY 'SUBSTITUA_POR_UMA_SENHA_LOCAL';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_foco.* TO 'foco_app'@'127.0.0.1';
```

Substitua a senha por uma própria e mantenha-a apenas localmente. Se a conta já existir, confira sua configuração em vez de repetir `CREATE USER`. O exemplo combina com `DB_HOST=127.0.0.1`; contas e regras de autenticação podem variar entre instalações. No `.env`, preencha `DB_USER=foco_app` e `DB_PASS` com a senha dessa conta. Não use a senha de cadastro da interface do Foco: são contas distintas.

## Variáveis reconhecidas pelo carregador

| Variável | Valor padrão se ausente | Observação |
|---|---|---|
| `DB_HOST` | `127.0.0.1` | Use TCP com este endereço no guia; não aceita caminho de socket. |
| `DB_PORT` | `3306` | Número de 1 a 65535. |
| `DB_NAME` | `db_foco` | O schema fornecido cria esse banco. |
| `DB_USER` | Nenhum | Obrigatória e não vazia. |
| `DB_PASS` | Nenhum | Obrigatória; valor vazio explícito é aceito se a conta permitir. |

A cópia de `.env.example` contém `DB_USER=` e `DB_PASS=` vazios: preencha o usuário e ajuste a senha à conta real antes do uso. Senha vazia explícita só é válida se a conta permitir. A porta padrão do exemplo é 3306; no teste Windows foi usada `DB_PORT=3308`, já suportada por `config/database.php`. `getenv()` tem precedência sobre o `.env`; inclusive variáveis vazias no ambiente podem sobrepor valores do arquivo.

O arquivo é lido literalmente por `config/database.php`: uma entrada `CHAVE=valor` por linha; comentários em linhas próprias começando com `#`; aspas externas opcionais; sem interpolação, escapes de shell ou `export`. Espaços fora das aspas são removidos. Use UTF-8 sem BOM.

## Testar a conexão sem alterar dados

Estes comandos abrem o PDO e executam `SELECT 1`. O resultado esperado é `Conexão OK`.

Linux (Bash):

```bash
/opt/lampp/bin/php -r '$pdo = require "config/database.php"; echo $pdo->query("SELECT 1")->fetchColumn() == 1 ? "Conexão OK" . PHP_EOL : "Falha no teste" . PHP_EOL;'
```

Windows (PowerShell), enviando o código PHP pela entrada padrão para evitar diferenças de escape de aspas de `-r` entre versões do PowerShell:

```powershell
@'
<?php
$pdo = require 'config/database.php';
echo $pdo->query('SELECT 1')->fetchColumn() == 1 ? 'Conexão OK' . PHP_EOL : 'Falha no teste' . PHP_EOL;
'@ | & "C:\xampp\php\php.exe"
```

O delimitador final `'@` deve ficar no início da linha. Se o terminal exibir caracteres estranhos, confirme sua codificação; isso não altera as credenciais no arquivo.

Confirme também cadastro/login no navegador. Se optar pelo Apache, um teste CLI bem-sucedido não garante suas permissões: ele pode executar com outro usuário, outro ambiente e outro `php.ini`. Falhas PDO exibem mensagem genérica; em requisições web a inicialização da conexão retorna HTTP 503. O log registra apenas uma mensagem genérica, sem a exceção original. Veja [diagnóstico de conexão](troubleshooting.md).

## Modelo utilizado pela aplicação

- `usuarios`: `id`, `nome` (150 caracteres), `email` (254, único), `senha_hash` (255) e `criado_em`. O cadastro grava `password_hash()`, e o login verifica com `password_verify()`.
- `tarefas`: `id`, `usuario_id`, `titulo` (150), `descricao` opcional, `prazo` opcional, `prioridade`, `categoria`, `status`, `criado_em` e `atualizado_em`.
- `usuario_id` é chave estrangeira para `usuarios.id`, com índice e exclusão em cascata. Excluir um usuário no banco exclui suas tarefas; não há interface de exclusão de conta.
- Prioridade: `low`, `medium`, `high`; padrão `medium`.
- Categoria: `studies`, `work`, `personal`; sem categoria usa `NULL`.
- Status: `pending`, `in-progress`, `completed`; padrão `pending`.
- Todas as operações de tarefas restringem `usuario_id` à sessão autenticada. Os formulários não escolhem o proprietário.

Datas de prazo são datas sem horário. O PHP usa `America/Sao_Paulo` para decidir o dia atual e os atrasos; esse fuso está definido em `app/bootstrap.php`, não no `.env`. Os timestamps automáticos do banco seguem a configuração do servidor de banco.

## Referência de validação

A implementação anterior foi testada com PHP 8.2.4 e MariaDB 10.4.28. Nesta revisão de documentação não houve importação, mudança de dados ou teste adicional de conexão. O responsável pelo projeto relatou importação bem-sucedida de `database/schema.sql` pelo terminal no Windows 10, com MariaDB 10.4.32 na porta 3308, e confirmou `usuarios` e `tarefas` com `USE db_foco; SHOW TABLES;`. Isso não estabelece se o banco estava vazio antes da importação nem se cada variante de comando deste guia foi executada. Os exemplos PowerShell/cmd.exe foram revisados documentalmente, sem nova execução no Windows nesta atualização.
