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

## Requisitos

- Git para clonar o repositório. Alternativamente, extraia o ZIP do GitHub.
- XAMPP com Apache 2.4, PHP e MariaDB, ou ambiente equivalente.
- PHP **8.2 ou superior como referência para instalação**; a execução já foi testada com PHP 8.2.4. Não há matriz de testes para todas as versões posteriores.
- Extensões PHP `PDO`, `pdo_mysql` e `mbstring`, além das extensões padrão usadas pelo PHP, como `session`, `ctype`, `filter` e `json`.
- MariaDB 10.4 ou MySQL 5.7/8.x para o SQL fornecido. Testes anteriores usaram MariaDB 10.4.28; compatibilidade com MySQL foi revisada pelo SQL, sem teste de execução neste ambiente.
- Navegador atual com JavaScript e cookies habilitados. Modais, filtros, edição e confirmação de exclusão dependem de JavaScript.
- Permissão administrativa para instalar o XAMPP e editar Apache/hosts; acesso administrativo ao banco para a configuração inicial.

Os caminhos abaixo assumem XAMPP em `/opt/lampp` no Linux e `C:\xampp` no Windows. Ajuste-os se sua instalação for diferente. Comandos Linux usam Bash; comandos Windows usam **PowerShell**, salvo onde indicado **Prompt de Comando (cmd.exe)**.

## 1. Instalar e iniciar o XAMPP

Baixe o instalador para seu sistema na [página oficial do XAMPP](https://www.apachefriends.org/download.html). Use o PHP do XAMPP nos comandos para evitar misturá-lo com outra instalação do sistema.

### Linux

Na pasta do download, substitua `NOME_DO_INSTALADOR.run` pelo nome exato do arquivo baixado:

```bash
chmod +x NOME_DO_INSTALADOR.run
sudo ./NOME_DO_INSTALADOR.run
sudo /opt/lampp/lampp start
/opt/lampp/bin/php -v
/opt/lampp/bin/php -m
```

O instalador oficial usa `/opt/lampp`. Não é necessário instalar outro Apache, PHP ou MariaDB pelo gerenciador de pacotes para seguir este guia. Distribuições podem exigir bibliotecas adicionais: se o instalador falhar, consulte a mensagem e a [FAQ oficial para Linux](https://www.apachefriends.org/faq_linux.html).

### Windows

Execute o instalador e selecione Apache, MySQL/MariaDB, PHP e phpMyAdmin. Este guia usa `C:\xampp` como destino. Abra `C:\xampp\xampp-control.exe` e clique em **Start** para **Apache** e **MySQL**. O painel pode chamar o serviço de MySQL mesmo usando MariaDB.

No PowerShell:

```powershell
& C:\xampp\php\php.exe -v
& C:\xampp\php\php.exe -m
```

Confira `PDO`, `pdo_mysql` e `mbstring` na lista. Se faltar uma extensão, veja [solução de problemas](docs/troubleshooting.md). Consulte também a [FAQ oficial para Windows](https://www.apachefriends.org/faq_windows.html).

Abra `http://localhost/` para confirmar o Apache e `http://localhost/phpmyadmin/` para acessar o banco. Se as portas padrão 80/3306 estiverem ocupadas, resolva o conflito ou adapte as portas conforme o [guia de erros comuns](docs/troubleshooting.md).

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

Windows, usando `C:\Projetos`:

```powershell
New-Item -ItemType Directory -Force C:\Projetos | Out-Null
Set-Location C:\Projetos
git clone https://github.com/k0rrea/Foco.git
Set-Location Foco
Get-Location
```

O clone por HTTPS evita exigir configuração prévia de chave SSH. Se o GitHub solicitar autenticação, utilize uma conta autorizada para o repositório. A alternativa SSH, para quem já configurou sua chave, é `git clone git@github.com:k0rrea/Foco.git`.

Os próximos comandos relativos devem ser executados na raiz de `Foco`, onde estão `README.md`, `.env.example`, `config/` e `database/`.

## 3. Criar e importar o banco

No phpMyAdmin (`http://localhost/phpmyadmin/`), entre com uma conta administrativa válida da sua instalação, abra **Importar**, selecione `database/schema.sql` do clone e execute a importação. Não precisa criar o banco manualmente: o arquivo contém `CREATE DATABASE IF NOT EXISTS db_foco` e `USE db_foco`.

O resultado esperado é o banco `db_foco`, com `usuarios` e `tarefas`. Não há contas iniciais: o primeiro usuário será criado pela página de cadastro.

Se já houver dados, faça backup antes de qualquer ajuste. A importação não apaga registros, mas **não atualiza tabelas já existentes com estrutura diferente**. Não exclua nem recrie tabelas para contornar erros; compare a estrutura com o [guia do banco](docs/database-setup.md).

Na aba **SQL**, crie uma conta para a aplicação, substituindo a senha abaixo por uma senha local própria. Não salve a senha real no repositório:

```sql
CREATE USER 'foco_app'@'127.0.0.1' IDENTIFIED BY 'SUBSTITUA_POR_UMA_SENHA_LOCAL';
GRANT SELECT, INSERT, UPDATE, DELETE ON db_foco.* TO 'foco_app'@'127.0.0.1';
```

Execute uma vez. Se a conta já existir, verifique sua configuração em vez de repetir `CREATE USER`. O exemplo combina com `DB_HOST=127.0.0.1`; contas e regras de autenticação do servidor podem variar. Não presuma que root esteja sem senha. A aplicação não precisa de permissões administrativas ou de criação de tabelas.

## 4. Configurar o arquivo local .env

Linux:

```bash
cp .env.example .env
```

Windows (PowerShell):

```powershell
Copy-Item .env.example .env
```

Edite `.env` na raiz do clone. Exemplo, usando a mesma senha escolhida no passo anterior:

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=db_foco
DB_USER=foco_app
DB_PASS="SUBSTITUA_POR_UMA_SENHA_LOCAL"
```

Não sobrescreva um `.env` já configurado. No Windows, confirme que o editor não criou `.env.txt`; salve em UTF-8 sem BOM. O projeto já ignora `.env` no Git. O Apache precisa ler esse arquivo, mas ele não deve ficar acessível por URL.

O carregador é próprio do projeto: valores podem ser envolvidos por aspas simples ou duplas, são literais, e comentários devem ocupar linhas próprias. Não use `export`, expansão `${VAR}` nem comentários ao final do valor. Variáveis `DB_*` definidas no ambiente têm precedência. Não há `APP_URL`, `APP_KEY` ou comando de geração de chave.

## 5. Apontar o Apache somente para public/

Edite os arquivos com permissão administrativa e faça backup da configuração antes de ajustar. Não substitua configurações de outros projetos.

| Arquivo | Linux | Windows |
|---|---|---|
| Configuração principal | `/opt/lampp/etc/httpd.conf` | `C:\xampp\apache\conf\httpd.conf` |
| VirtualHosts | `/opt/lampp/etc/extra/httpd-vhosts.conf` | `C:\xampp\apache\conf\extra\httpd-vhosts.conf` |
| Hosts do sistema | `/etc/hosts` | `C:\Windows\System32\drivers\etc\hosts` |
| PHP | `/opt/lampp/etc/php.ini` | `C:\xampp\php\php.ini` |

Na configuração principal, confira se a inclusão de VirtualHosts está habilitada (sem `#` no início):

Linux:

```apache
Include etc/extra/httpd-vhosts.conf
```

Windows:

```apache
Include conf/extra/httpd-vhosts.conf
```

No arquivo de VirtualHosts, preserve os hosts existentes. Se ainda não houver um host explícito para `localhost`, coloque este **antes** do host Foco para manter o acesso ao painel/phpMyAdmin. Use o DocumentRoot real do XAMPP; no Windows, substitua os dois caminhos por `C:/xampp/htdocs`:

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot "/opt/lampp/htdocs"
    <Directory "/opt/lampp/htdocs">
        Require local
        DirectoryIndex index.php index.html
    </Directory>
</VirtualHost>
```

Acrescente o host Foco. No exemplo Linux, substitua `/CAMINHO/Foco/public` pelo caminho absoluto mostrado no passo 2 **acrescido de `/public`**. Não coloque `$HOME`, `~` ou `/CAMINHO` literalmente na configuração:

```apache
<VirtualHost *:80>
    ServerName foco.local
    DocumentRoot "/CAMINHO/Foco/public"
    <Directory "/CAMINHO/Foco/public">
        Require local
        AllowOverride None
        Options -Indexes
        DirectoryIndex index.php
    </Directory>
</VirtualHost>
```

No Windows, as duas linhas de caminho desse host serão:

```apache
DocumentRoot "C:/Projetos/Foco/public"
<Directory "C:/Projetos/Foco/public">
```

Essas linhas substituem as correspondentes dentro do bloco completo acima; mantenha `</Directory>` e `</VirtualHost>`. Use barras `/` nos caminhos do Apache no Windows. A diretiva `Require local` limita este ambiente à própria máquina. Não são necessários `.htaccess`, URL rewriting ou `mod_rewrite`.

Acrescente ao arquivo hosts, sem remover entradas existentes:

```text
127.0.0.1 foco.local
```

No Linux, o usuário que executa o Apache precisa atravessar os diretórios pais e ler os arquivos do clone, inclusive `.env`. Pastas pessoais restritas e SELinux podem bloquear isso; veja [403 e permissões](docs/troubleshooting.md). Não aplique `chmod 777` nem `chmod 600 .env` indiscriminadamente: se o proprietário for diferente do usuário do Apache, `600` impede a leitura necessária. No Windows, confira as permissões NTFS da conta do serviço.

No `php.ini` usado pelo Apache, mantenha:

```ini
display_errors = Off
log_errors = On
```

Verifique também se `session.save_path` aponta para um diretório existente e gravável pelo usuário do Apache. Não é preciso gravar arquivos dentro do clone para armazenar sessões.

## 6. Validar a configuração e reiniciar o Apache

Linux:

```bash
sudo /opt/lampp/bin/httpd -t
sudo /opt/lampp/bin/httpd -S
sudo /opt/lampp/lampp restartapache
```

Windows (PowerShell):

```powershell
& C:\xampp\apache\bin\httpd.exe -t
& C:\xampp\apache\bin\httpd.exe -S
```

Espere `Syntax OK` no teste de sintaxe e confira `foco.local` em `-S`. No Windows, reinicie apenas o Apache com **Stop/Start** no painel XAMPP. Corrija erros de sintaxe antes de reiniciar. O banco também deve continuar iniciado.

O projeto inteiro fica fora de `htdocs`; somente `public/` é exposto pelo VirtualHost. Não crie aliases para a raiz do clone.

## 7. Primeiro acesso e teste rápido

Abra **http://foco.local/** (sem `/Foco` ou `/public` no endereço). O dashboard privado deve redirecionar para `/pages/login.php`.

1. Clique em **Criar conta**, preencha nome, e-mail, senha de 8 a 72 bytes e confirmação. Não há usuário/senha padrão.
2. Após o cadastro, entre com os dados informados.
3. Crie uma tarefa, recarregue e confira a persistência.
4. Edite os campos, altere o status, experimente os filtros e exclua uma tarefa de teste.
5. Clique em **Sair da minha conta** e confira que o dashboard exige login novamente.

Se o login abrir, mas o cadastro falhar, isso ainda pode indicar problema no banco: o formulário de login não testa a conexão até ser enviado. Confira o teste PDO e as consultas de diagnóstico em [Banco de dados](docs/database-setup.md).

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

- [Banco: importação por terminal, configuração e modelo](docs/database-setup.md).
- [Testes de cadastro, login, CRUD e segurança](docs/auth-and-tasks.md).
- [Erros comuns: 403, 404, banco, sessões e portas](docs/troubleshooting.md).
- [Identidade visual e diretrizes de design](DESIGN.md).

Nesta revisão documental, os exemplos foram confrontados com os arquivos reais e com as referências oficiais de XAMPP/Apache; foram verificados PHP/extensões disponíveis, sintaxe PHP/JavaScript e links locais da documentação. Não foi executada instalação limpa, clone remoto, importação nova, alteração do Apache/hosts ou teste no Windows. Testes HTTP realizados na implementação anterior estão descritos separadamente no roteiro de testes.

Este guia destina-se ao desenvolvimento local; o XAMPP não deve ser tratado como uma configuração pronta de produção.
