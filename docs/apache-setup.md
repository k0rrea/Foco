# Apache com VirtualHost (opcional)

Esta é uma alternativa ao servidor embutido do PHP do [README](../README.md). Conclua os passos de clone, banco e `.env` antes de seguir este guia. Apache **2.4** com PHP configurado é necessário apenas para esta alternativa.

Use este procedimento somente se tiver autorização e permissões para configurar o Apache e o arquivo hosts. Em computadores institucionais, solicite apoio ao responsável ou use `http://127.0.0.1:8000` conforme o README, sem essas alterações. O teste relatado no Windows 10 utilizou o servidor embutido do PHP e **não validou este VirtualHost**.

No Windows, inicie o Apache pelo painel XAMPP. No Linux, para iniciar apenas o Apache: `sudo /opt/lampp/lampp startapache`. O comando `sudo /opt/lampp/lampp start` continua sendo uma alternativa para iniciar os serviços do XAMPP em conjunto. Se a porta 80 estiver ocupada, consulte [portas e permissões](troubleshooting.md) antes de prosseguir.

## 1. Apontar o Apache somente para public/

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

Acrescente o host Foco. No exemplo Linux, substitua `/CAMINHO/Foco/public` pelo caminho absoluto mostrado ao clonar o projeto **acrescido de `/public`**. Não coloque `$HOME`, `~` ou `/CAMINHO` literalmente na configuração:

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

No Windows, adapte os dois caminhos ao local real do clone. Por exemplo, se ele estiver em `C:\Projetos\Foco`, as linhas serão:

```apache
DocumentRoot "C:/Projetos/Foco/public"
<Directory "C:/Projetos/Foco/public">
```

Essas linhas substituem as correspondentes dentro do bloco completo acima; mantenha `</Directory>` e `</VirtualHost>`. Use barras `/` nos caminhos do Apache no Windows. A diretiva `Require local` limita este ambiente à própria máquina. Não são necessários `.htaccess`, URL rewriting ou `mod_rewrite`.

Acrescente ao arquivo hosts, sem remover entradas existentes:

```text
127.0.0.1 foco.local
```

No Linux, o usuário que executa o Apache precisa atravessar os diretórios pais e ler os arquivos do clone, inclusive `.env`. Pastas pessoais restritas e SELinux podem bloquear isso; veja [403 e permissões](troubleshooting.md). Não aplique `chmod 777` nem `chmod 600 .env` indiscriminadamente: se o proprietário for diferente do usuário do Apache, `600` impede a leitura necessária. No Windows, confira as permissões NTFS da conta do serviço.

No `php.ini` usado pelo Apache, mantenha:

```ini
display_errors = Off
log_errors = On
```

Verifique também se `session.save_path` aponta para um diretório existente e gravável pelo usuário do Apache. Não é preciso gravar arquivos dentro do clone para armazenar sessões.

## 2. Validar a configuração e reiniciar o Apache

Linux:

```bash
sudo /opt/lampp/bin/httpd -t
sudo /opt/lampp/bin/httpd -S
sudo /opt/lampp/lampp restartapache
```

Windows (PowerShell):

```powershell
& "C:\xampp\apache\bin\httpd.exe" -t
& "C:\xampp\apache\bin\httpd.exe" -S
```

Espere `Syntax OK` no teste de sintaxe e confira `foco.local` em `-S`. No Windows, reinicie apenas o Apache com **Stop/Start** no painel XAMPP. Corrija erros de sintaxe antes de reiniciar. O banco também deve continuar iniciado.

O projeto inteiro fica fora de `htdocs`; somente `public/` é exposto pelo VirtualHost. Não crie aliases para a raiz do clone.

## 3. Acessar e testar

Abra **http://foco.local/**, sem `/Foco` ou `/public`, e siga o [primeiro acesso e teste rápido](../README.md#6-primeiro-acesso-e-teste-rápido). Para acessar o phpMyAdmin, use `http://localhost/phpmyadmin/` se o Apache estiver na porta 80.

Este procedimento configura um ambiente local, não uma implantação de produção.

## Referências

- [Apache 2.4: VirtualHosts por nome](https://httpd.apache.org/docs/2.4/vhosts/name-based.html).
- [XAMPP: Linux](https://www.apachefriends.org/faq_linux.html) e [Windows](https://www.apachefriends.org/faq_windows.html).
