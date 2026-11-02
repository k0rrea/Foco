<p align="center">
  <picture>
    <source
      media="(prefers-color-scheme: dark)"
      srcset="public/assets/images/FocoEscritaBranco.png"
    >
    <source
      media="(prefers-color-scheme: light)"
      srcset="public/assets/images/FocoEscritaPreto.png"
    >
    <img
      src="public/assets/images/FocoEscritaPreto.png"
      alt="Foco - Gerenciador de tarefas"
      width="550"
    >
  </picture>
</p>

# Foco

Sistema web acadêmico de gerenciamento de tarefas, com HTML, CSS, JavaScript, PHP tradicional e MySQL/MariaDB. Possui cadastro, login, logout e tarefas privadas com criação, listagem, edição, exclusão, status, prazo, prioridade e categoria.

O projeto está finalizado e receberá apenas eventuais correções ou implementações. A arquitetura permanece em PHP tradicional, sem framework, API REST separada, Composer, npm ou etapa de compilação para executar o sistema.

O caminho principal deste guia usa o **servidor embutido do PHP**: prepare o banco, configure o `.env` e execute o projeto. Não exige Apache, VirtualHost nem edição do arquivo hosts. É útil para testes locais, inclusive quando o Apache está indisponível ou há restrições no computador.

## Requisitos

- Git para clonar o repositório. Alternativamente, extraia o ZIP do GitHub.
- PHP **8.2 ou superior como referência para instalação**; a execução já foi testada com PHP 8.2.4. Não há matriz de testes para todas as versões posteriores.
- Extensões PHP `PDO`, `pdo_mysql` e `mbstring`, além das extensões padrão usadas pelo PHP, como `session`, `ctype`, `filter` e `json`.
- MariaDB/MySQL em execução e uma conta autorizada a criar/importar o banco. O SQL foi preparado para MariaDB 10.4 e MySQL 5.7/8.x; há testes com MariaDB 10.4.28 e 10.4.32 e com MySQL 8.4.13 em Docker no Fedora Linux. Esses resultados não validam todas as versões do MySQL.
- Navegador atual com JavaScript e cookies habilitados. Modais, filtros, edição e confirmação de exclusão dependem de JavaScript.

Os exemplos usam o PHP e o MariaDB do **XAMPP**, instalado em `/opt/lampp` no Linux ou `C:\xampp` no Windows. Ajuste os caminhos se necessário. Linux usa Bash; Windows usa **PowerShell**, salvo indicação de **cmd.exe**. Um ambiente equivalente também pode ser usado.

Instalar o XAMPP ou iniciar serviços pode exigir permissões adicionais. Se o ambiente já estiver disponível, executar o PHP na porta 8000 não exige configurar Apache/hosts. Em computadores institucionais, use apenas os acessos autorizados e peça apoio ao responsável quando necessário.

## 1. Preparar PHP e iniciar o banco

Se necessário, baixe o XAMPP na [página oficial](https://www.apachefriends.org/download.html). Use seu executável PHP para evitar misturá-lo com outra instalação do sistema.

### Linux

Para instalar, na pasta do download, substitua `NOME_DO_INSTALADOR.run` pelo nome exato do arquivo:

```bash
chmod +x NOME_DO_INSTALADOR.run
sudo ./NOME_DO_INSTALADOR.run
```

Com o XAMPP instalado, inicie apenas o banco, caso ainda esteja parado, e confira o PHP:

```bash
sudo /opt/lampp/lampp startmysql
/opt/lampp/bin/php -v
/opt/lampp/bin/php -m
```

O instalador oficial usa `/opt/lampp`. Não é necessário instalar outro Apache, PHP ou MariaDB pelo gerenciador de pacotes. Se faltarem bibliotecas, consulte a mensagem do instalador e a [FAQ oficial para Linux](https://www.apachefriends.org/faq_linux.html).

### Windows

Instale em `C:\xampp`, com PHP e MySQL/MariaDB disponíveis. Abra `C:\xampp\xampp-control.exe` e clique em **Start** apenas para **MySQL**, se ainda estiver parado. O painel pode chamar o serviço de MySQL mesmo usando MariaDB. Apache e phpMyAdmin são opcionais neste caminho.

```powershell
& "C:\xampp\php\php.exe" -v
& "C:\xampp\php\php.exe" -m
```

Confira `PDO`, `pdo_mysql` e `mbstring` na lista. Se faltar uma extensão, veja [solução de problemas](docs/troubleshooting.md) e a [FAQ oficial para Windows](https://www.apachefriends.org/faq_windows.html).

Se as portas **80** (Apache) ou **3306** (banco) estiverem ocupadas, veja [conflitos de portas e permissões do painel](docs/troubleshooting.md#conflitos-de-portas-e-permissões-no-xampp-windows). A porta 80 não é usada pelo servidor PHP deste guia. No teste Windows, o banco já havia sido configurado na porta **3308**; utilize a porta real da sua instalação nos comandos e no `.env`.

## 2. Clonar o projeto

Escolha uma pasta **fora do diretório público do XAMPP** (`htdocs`). Exemplos:

Linux, usando uma pasta de projetos no seu diretório pessoal:

```bash
mkdir -p "$HOME/Projetos"
cd "$HOME/Projetos"
git clone https://github.com/k0rrea/Foco.git
cd Foco
pwd
```

Windows (PowerShell), usando uma pasta gravável no seu perfil:

```powershell
New-Item -ItemType Directory -Force "$env:USERPROFILE\Projetos" | Out-Null
Set-Location "$env:USERPROFILE\Projetos"
git clone https://github.com/k0rrea/Foco.git
Set-Location Foco
Get-Location
```

O clone por HTTPS evita exigir configuração prévia de chave SSH. Se o GitHub solicitar autenticação, utilize uma conta autorizada para o repositório. A alternativa SSH, para quem já configurou sua chave, é `git clone git@github.com:k0rrea/Foco.git`.

Os próximos comandos relativos devem ser executados na raiz de `Foco`, onde estão `README.md`, `.env.example`, `config/` e `database/`.

## 3. Criar e importar o banco

Execute os comandos na raiz de `Foco`. Os exemplos usam `root` como conta administrativa existente; substitua por outra conta autorizada, se necessário. `--password` solicita a senha sem incluí-la no comando. Pressione Enter somente se a conta realmente não tiver senha.

**Linux (Bash)**, com banco na porta padrão 3306:

```bash
/opt/lampp/bin/mysql --host=127.0.0.1 --port=3306 --user=root --password < database/schema.sql
/opt/lampp/bin/mysql --host=127.0.0.1 --port=3306 --user=root --password
```

**Windows (PowerShell)**, com porta **3308**, como no teste relatado (troque por 3306 se for a porta da sua instalação):

```powershell
& "C:\xampp\mysql\bin\mysql.exe" --host=127.0.0.1 --port=3308 --user=root --password
```

Após entrar no cliente MariaDB, execute **apenas no Windows**, para importar:

```sql
SOURCE database/schema.sql;
```

O PowerShell não aceita `<` como redirecionamento de entrada. `SOURCE` lê o arquivo pelo próprio cliente MariaDB, sem essa limitação. Para importar usando `cmd.exe` ou phpMyAdmin, veja o [guia do banco](docs/database-setup.md#importação-por-terminal-alternativa-ao-phpmyadmin).

Em **ambos os sistemas**, ainda no cliente MariaDB, confirme a importação e saia:

```sql
USE db_foco;
SHOW TABLES;
EXIT;
```

O resultado deve incluir `usuarios` e `tarefas`. O SQL já cria e seleciona `db_foco`; não é necessário criar o banco antes. Não há contas iniciais da aplicação: o primeiro usuário será criado pela página de cadastro.

Se já houver dados, faça backup antes de ajustes. A importação não apaga registros, mas **não atualiza tabelas com estrutura diferente**. Não exclua nem recrie tabelas para contornar erros; compare a estrutura com o [guia do banco](docs/database-setup.md).

Para um teste local isolado, você pode usar no `.env` uma conta existente, inclusive `root`, se permitido no seu ambiente. Essa conta pode ter acesso amplo ao servidor: use somente dados de teste e não a mantenha como credencial da aplicação em ambientes compartilhados ou persistentes. Nesses ambientes, recomenda-se um [usuário específico com privilégios limitados](docs/database-setup.md#usuário-específico-da-aplicação-opcional-recomendado).

## 4. Configurar o arquivo local .env

Se ainda não houver `.env`, copie o exemplo. Não sobrescreva um arquivo já configurado.

Linux:

```bash
cp .env.example .env
```

Windows (PowerShell):

```powershell
Copy-Item .env.example .env
```

Edite `.env` na raiz do clone e use a **porta real do seu banco**: `3306` é a porta padrão do MySQL/MariaDB; `3308` foi usada no teste com Windows 10; e `3307` foi a porta publicada pelo container Docker no teste com MySQL 8.4.13. O exemplo abaixo mantém `3306` e usa uma conta existente para um teste local:

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=db_foco
DB_USER=root
DB_PASS="SUBSTITUA_PELA_SENHA_LOCAL_DA_CONTA"
```

- `DB_HOST`: endereço do servidor do banco; `127.0.0.1` usa TCP na própria máquina.
- `DB_PORT`: porta real do banco. Use `3306` na configuração padrão, `3308` no ambiente Windows relatado ou `3307` para a porta publicada no teste Docker, conforme sua instalação. O código já aceita essa variável; não é necessário editar PHP.
- `DB_NAME`: `db_foco`, criado pelo SQL fornecido. Mudar apenas o `.env` não renomeia o banco.
- `DB_USER` e `DB_PASS`: usuário e senha válidos para esse host. Se criou `foco_app`, use suas credenciais. Use `DB_PASS=` vazio somente se a conta estiver realmente configurada sem senha.

Não publique credenciais reais. O projeto ignora `.env` no Git; mantenha-o fora da raiz web. No Windows, confirme que o editor não criou `.env.txt` e salve em UTF-8 sem BOM. A conta que executa PHP precisa ler o arquivo.

O carregador aceita aspas externas simples ou duplas e lê valores literalmente. Use comentários em linhas próprias, sem `export`, expansão `${VAR}` ou comentários ao final do valor. Variáveis `DB_*` do ambiente têm precedência. Não há `APP_URL`, `APP_KEY` ou comando de geração de chave. Consulte o [teste PDO](docs/database-setup.md#testar-a-conexão-sem-alterar-dados) se precisar diagnosticar a conexão.

## 5. Executar com o servidor embutido do PHP

Na raiz de `Foco`, execute e mantenha o terminal aberto:

Linux:

```bash
/opt/lampp/bin/php -S 127.0.0.1:8000 -t public
```

Windows (PowerShell):

```powershell
& "C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t public
```

Abra **http://127.0.0.1:8000** no navegador. `-t public` expõe somente a pasta pública; não sirva a raiz do clone. Não é necessário colocar o projeto em `htdocs`, iniciar Apache ou editar hosts. O banco deve continuar em execução, na porta definida em `DB_PORT`.

Se 8000 estiver ocupada, use, por exemplo, `8001` no comando e no endereço do navegador. Isso não altera a porta do banco. Para encerrar o servidor PHP, pressione **Ctrl+C** no terminal.

O [servidor embutido do PHP](https://www.php.net/manual/en/features.commandline.webserver.php) destina-se ao **desenvolvimento e a testes locais, não à produção**. O endereço `127.0.0.1` limita o acesso à própria máquina.

## 6. Primeiro acesso e teste rápido

Em **http://127.0.0.1:8000**, o dashboard privado deve redirecionar para `/pages/login.php`.

1. Clique em **Criar conta**, preencha nome, e-mail, senha de 8 a 72 bytes e confirmação. Não há usuário/senha padrão.
2. Após o cadastro, entre com os dados informados.
3. Crie uma tarefa, recarregue e confira a persistência.

Esses passos correspondem ao funcionamento básico relatado no Windows. Para ampliar sua própria validação, teste edição, status, filtros, exclusão de uma tarefa de teste e logout, conforme o [roteiro funcional e de segurança](docs/auth-and-tasks.md). Esse roteiro não significa que todos esses recursos foram testados no Windows.

Se o login abrir, mas o cadastro falhar, isso ainda pode indicar problema no banco: o formulário de login não testa a conexão até ser enviado. Consulte o [diagnóstico de conexão](docs/troubleshooting.md#falha-de-conexão--http-503).

## Alternativa opcional: Apache com VirtualHost

Para usar `http://foco.local/`, siga o [guia de Apache com VirtualHost](docs/apache-setup.md). Ele preserva as instruções de Linux e Windows para apontar somente para `public/`, configurar hosts, verificar permissões e validar/reiniciar o Apache. Requer autorização para essas configurações e não faz parte da execução básica acima.

## Estrutura real

```text
Foco/
├── app/
│   ├── auth.php          # Cadastro e login
│   ├── bootstrap.php     # Sessão, PDO, CSRF e escape
│   ├── task-list.php     # Renderização das tarefas
│   └── tasks.php         # Autorização, validação, CRUD e indicadores
├── config/database.php
├── database/schema.sql
├── docs/
│   ├── apache-setup.md
│   ├── auth-and-tasks.md
│   ├── database-setup.md
│   └── troubleshooting.md
├── public/
│   ├── assets/           # CSS, JavaScript e imagens
│   ├── pages/
│   │   ├── login.php
│   │   └── register.php
│   ├── index.php         # Dashboard privado
│   └── logout.php        # Somente POST
├── .env.example
├── .gitignore
├── .gitattributes
├── DESIGN.md
└── README.md
```

O `.env` local não vem no clone. Os diretórios `app/`, `config/` e `database/` não devem ser acessíveis pela web.

## Documentação e limites da validação

- [Banco: importação, configuração e modelo](docs/database-setup.md).
- [Apache: VirtualHost opcional em Linux e Windows](docs/apache-setup.md).
- [Testes de cadastro, login, CRUD e segurança](docs/auth-and-tasks.md).
- [Erros comuns: portas, permissões, banco e sessões](docs/troubleshooting.md).
- [Identidade visual e diretrizes de design](DESIGN.md).

O ambiente original de desenvolvimento foi **Fedora Linux com XAMPP**. Os testes anteriores com PHP 8.2.4 e MariaDB 10.4.28 estão descritos no [histórico de validação](docs/auth-and-tasks.md#histórico-e-limites-da-validação).

No teste adicional **relatado pelo responsável pelo projeto**, foram usados **Windows 10**, XAMPP em `C:\xampp`, **MariaDB 10.4.32 na porta 3308**, PHP fornecido pelo XAMPP (versão não informada), projeto fora de `htdocs` e servidor embutido na porta 8000. Foram confirmados a importação de `database/schema.sql`, as tabelas `usuarios` e `tarefas`, execução, acesso à interface, cadastro, login, criação de tarefas e persistência após recarregar.

Esse teste não valida edição, exclusão, filtros, isolamento entre usuários ou demais funcionalidades, nem o VirtualHost do Apache nesse computador.

Também foi relatado um teste com **MySQL 8.4.13 em um container Docker no Fedora Linux**, com porta publicada em `127.0.0.1:3307`. O banco `db_foco` foi importado com `database/schema.sql`, e a aplicação conectou-se por meio das variáveis do `.env`. Foram bem-sucedidos os testes de cadastro, login e operações CRUD de tarefas. Essa validação se limita à versão e ao ambiente informados; não comprova compatibilidade com todas as versões do MySQL.

A presente atualização confrontou documentação, implementação e referências oficiais; não repetiu a instalação, a importação ou os testes funcionais e não alterou serviços/configurações do sistema.

Este guia destina-se ao desenvolvimento local; o XAMPP não deve ser tratado como uma configuração pronta de produção.
