# Banco de dados

Para instalar desde o clone e configurar Apache/`.env`, siga o [README](../README.md). Este documento detalha a importação e o modelo existente. Execute comandos relativos na raiz do clone.

## Importação por terminal (alternativa ao phpMyAdmin)

Use uma conta administrativa do banco, com credenciais da sua instalação. `--password` solicita a senha sem colocá-la no comando; pressione Enter somente se a conta realmente tiver senha vazia.

Linux (Bash):

```bash
/opt/lampp/bin/mysql --host=127.0.0.1 --port=3306 --user=root --password < database/schema.sql
```

Windows: este exemplo é para **cmd.exe**, pois o PowerShell não usa `<` dessa forma. Abra o Prompt de Comando, vá à raiz do clone e execute:

```bat
cd /d C:\Projetos\Foco
"C:\xampp\mysql\bin\mysql.exe" --host=127.0.0.1 --port=3306 --user=root --password < database\schema.sql
```

Ajuste usuário, porta e caminhos conforme a instalação. A aplicação usa uma conta própria com permissões limitadas, conforme o README; root é apenas um exemplo de conta administrativa para a importação.

O arquivo cria `db_foco` e seleciona esse banco com `USE`. Não substitua apenas `DB_NAME` por outro nome esperando que o SQL acompanhe a mudança. O esquema usa InnoDB e `utf8mb4_unicode_ci`, compatíveis com MariaDB 10.4 e MySQL 5.7/8.x.

### Banco já existente

`IF NOT EXISTS` evita recriar tabelas, mas não altera colunas ou converte dados existentes. Confira a estrutura antes de integrar uma instalação antiga. No phpMyAdmin, selecione `db_foco` e execute:

```sql
SHOW TABLES;
SHOW CREATE TABLE usuarios;
SHOW CREATE TABLE tarefas;
```

Compare com `database/schema.sql`. Se houver divergência, faça backup e planeje uma migração; não exclua tabelas ou registros. Reimportar o SQL não resolve colunas ausentes.

## Variáveis reconhecidas pelo carregador

| Variável | Valor padrão se ausente | Observação |
|---|---|---|
| `DB_HOST` | `127.0.0.1` | Use TCP com este endereço no guia; não aceita caminho de socket. |
| `DB_PORT` | `3306` | Número de 1 a 65535. |
| `DB_NAME` | `db_foco` | O schema fornecido cria esse banco. |
| `DB_USER` | Nenhum | Obrigatória e não vazia. |
| `DB_PASS` | Nenhum | Obrigatória; valor vazio explícito é aceito se a conta permitir. |

A cópia de `.env.example` contém `DB_USER=` e `DB_PASS=` vazios: precisa ser preenchida antes do uso. `getenv()` tem precedência sobre o `.env`; inclusive variáveis vazias no ambiente podem sobrepor valores do arquivo.

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
'@ | & C:\xampp\php\php.exe
```

O delimitador final `'@` deve ficar no início da linha. Se o terminal exibir caracteres estranhos, confirme sua codificação; isso não altera as credenciais no arquivo.

Um teste CLI bem-sucedido não garante as permissões do Apache: ele pode executar com outro usuário, outro ambiente e outro `php.ini`. Confirme também cadastro/login no navegador. Falhas PDO exibem mensagem genérica; em requisições web a inicialização da conexão retorna HTTP 503. O log registra apenas uma mensagem genérica, sem a exceção original. Veja [diagnóstico de conexão](troubleshooting.md).

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

A implementação anterior foi testada com PHP 8.2.4 e MariaDB 10.4.28. Nesta revisão de documentação não houve importação, mudança de dados ou teste adicional de conexão. A importação em banco vazio e os comandos Windows ainda precisam ser reproduzidos em ambiente dedicado.
