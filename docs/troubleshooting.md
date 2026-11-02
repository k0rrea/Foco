# Solução de problemas

Use este guia após seguir a [instalação no README](../README.md). Os caminhos assumem `/opt/lampp` no Linux e `C:\xampp` no Windows. Ajuste-os se necessário. Não exponha credenciais nem publique páginas `phpinfo()` para diagnosticar o sistema.

## Servidor embutido do PHP não inicia ou endereço não abre

- Execute o comando do README na raiz do clone; `-t public` precisa encontrar `public/index.php`. Use o PHP do XAMPP e mantenha o terminal aberto enquanto acessa a aplicação.
- Abra `http://127.0.0.1:8000`, sem `/Foco` ou `/public`. Se 8000 estiver ocupada, use `8001` tanto no comando `-S` quanto no navegador. Isso não muda `DB_PORT`.
- Apache pode ficar parado neste método. O serviço MariaDB/MySQL precisa estar iniciado, e sua porta deve coincidir com o `.env`.
- Leia as mensagens no terminal do servidor PHP. Se não puder iniciar o banco ou ler os arquivos por restrições institucionais, solicite apoio ao responsável; o servidor embutido não contorna essas permissões.

## Conflitos de portas e permissões no XAMPP (Windows)

No teste relatado, as portas 80 e 3306 estavam ocupadas. A aplicação foi executada com PHP na porta **8000**, sem Apache, e MariaDB na porta **3308**. Essas portas atendem serviços diferentes:

| Serviço | Porta do exemplo | Onde conferir |
|---|---|---|
| Servidor PHP | 8000 | Argumento `-S 127.0.0.1:8000` e URL do navegador |
| MariaDB | 3308 no teste; 3306 é o padrão | Configuração real do servidor, cliente e `DB_PORT` |
| Apache (opcional) | 80 | `Listen`, VirtualHost e URL; não usado pelo servidor PHP |

Para identificar processos que já escutam nessas portas, sem encerrá-los, use no PowerShell:

```powershell
Get-NetTCPConnection -State Listen | Where-Object { $_.LocalPort -in 80, 3306, 3308, 8000 } | Select-Object LocalAddress, LocalPort, OwningProcess
```

O campo `OwningProcess` informa o PID; consulte-o com `Get-Process -Id NUMERO_DO_PID`, substituindo pelo número encontrado. Se o comando for restringido, consulte os logs do XAMPP ou peça auxílio ao responsável. Não encerre processos ou serviços desconhecidos para liberar portas.

### Porta real do MariaDB

Confira `C:\xampp\mysql\bin\my.ini` (no painel, **Config** do módulo MySQL). No ambiente testado, as seções correspondentes continham:

```ini
[client]
port=3308

[mysqld]
port=3308
```

`[mysqld]` configura o servidor; `[client]` define opções para clientes que leem esse arquivo. A opção **Service and Port Settings** do XAMPP Control Panel não altera necessariamente o `my.ini` nem a porta em que o servidor realmente escuta. Confira também se o serviço foi iniciado usando outro arquivo de configuração.

Se houver autorização para mudar a porta, faça backup do arquivo, ajuste as entradas existentes (sem duplicar seções) e reinicie somente o banco pelo procedimento permitido no computador. Sem permissão, solicite o ajuste ao responsável ou use uma instância já disponível e autorizada. Não é necessário modificar a porta do Apache para executar com PHP em 8000.

Use a mesma porta no cliente (`--port=3308`) e no `.env` (`DB_PORT=3308`). `config/database.php` já suporta essa variável; não edite o código PHP. Após conectar ao banco pelo terminal, a consulta abaixo confirma a porta informada pelo servidor:

```sql
SHOW VARIABLES LIKE 'port';
```

### Access is denied ao salvar xampp-control.ini

Esse erro indica que o painel não conseguiu gravar sua configuração, geralmente por permissões no arquivo ou na pasta `C:\xampp`. Ele não confirma que o `my.ini` foi alterado nem que o servidor mudou de porta. Confira o caminho exibido e o estado real do serviço antes de tentar novamente.

Não é obrigatório salvar as preferências do painel para executar o Foco com PHP. Se o banco já estiver disponível, configure apenas o `.env` local e prossiga. Se precisar alterar arquivos protegidos, peça ao responsável; não remova proteções ou presuma acesso administrativo. Executar o painel com elevação só é uma opção para quem tem essa autorização.

## Apache não inicia ou endereço não abre (alternativa opcional)

- Esta seção aplica-se apenas ao [guia de VirtualHost](apache-setup.md). Confira se Apache e MySQL estão iniciados no XAMPP. No Linux: `sudo /opt/lampp/lampp start`; no Windows: painel XAMPP. Se não puder configurar Apache, use o servidor embutido do PHP do README.
- Verifique os logs do painel e do Apache. Outro Apache, Nginx, IIS ou banco local pode estar usando as portas. Não inicie duas instalações na mesma porta.
- A porta HTTP do guia é 80. Se escolher 8080, ajuste `Listen`, os blocos `<VirtualHost *:8080>` e o endereço para `http://foco.local:8080/`. Isso não muda `DB_PORT`, que é a porta do banco. A entrada hosts não contém porta.
- Confirme a entrada `127.0.0.1 foco.local` no hosts e a inclusão do arquivo de VirtualHosts. `httpd -S`, com o executável do XAMPP indicado no guia de Apache, mostra os hosts carregados.
- Se o nome não resolver, confira se o hosts foi salvo sem extensão adicional e se o navegador/proxy está usando a resolução local. Não trate o nome `foco.local` como um domínio público.

## 403 Forbidden

Há duas situações distintas:

1. **Página genérica do Apache**, inclusive ao abrir login: confira o caminho em `DocumentRoot` e `<Directory>`, a regra `Require local` e as permissões de leitura/travessia. `Require local` admite apenas acesso da própria máquina. Abrir um diretório sem índice também pode resultar em 403 porque listagem está desabilitada.
2. **Mensagem “Formulário expirado”** ao enviar dashboard/logout: é a proteção CSRF. Reabra a página, mantenha cookies habilitados e envie o formulário recém-carregado. Não remova a proteção CSRF. Sessões sem permissão de gravação podem causar repetição desse problema.

No Linux, o Apache precisa atravessar **cada pasta pai** e ler arquivos públicos, `app/`, `config/` e `.env`. Descubra o usuário/grupo efetivos na configuração/processos do Apache; não presuma que sejam `apache`, `www-data` ou `daemon`. Conceda acesso somente à conta/grupo necessário, por permissões ou ACLs. `chmod 600 .env` só funciona se o processo que o lê for o proprietário. Não use `chmod -R 777`.

Em Fedora e outras distribuições com SELinux, permissões Unix corretas podem não ser suficientes. Confira os registros de negação e a política aplicável ao processo/caminho antes de ajustar contextos ou regras. Não desabilite SELinux como solução padrão. No Windows, verifique as permissões NTFS da conta que executa o Apache e as permissões dos diretórios de sessão.

## 404 ou página errada

- O endereço principal é `http://127.0.0.1:8000/`; com o VirtualHost opcional, é `http://foco.local/`. Não acrescente `/public` ou `/Foco`. A raiz web já é `public/`.
- O formulário de login é `/pages/login.php`, e o cadastro é `/pages/register.php`.
- Com o servidor embutido, confirme `-t public` na raiz do clone. No Apache, confirme que o caminho do host termina em `/Foco/public` e que `public/index.php` existe. No Linux, nomes distinguem maiúsculas e minúsculas.
- Apenas no Apache: confirme `Include`, `ServerName` e hosts com `httpd -S`; reinicie o Apache após corrigir. Não é necessário `mod_rewrite` ou `.htaccess`.
- Se abrir o painel XAMPP em vez do Foco, a requisição provavelmente chegou ao host errado. Se phpMyAdmin deixou de abrir após adicionar o Foco, preserve um host `localhost` antes dele, conforme o [guia de Apache](apache-setup.md).
- CSS/JS/imagens com 404: confira o clone completo e o DocumentRoot. Não altere URLs para incluir o caminho absoluto do seu computador.

## Falha de conexão / HTTP 503

A mensagem genérica é intencional. Siga esta ordem:

1. Confirme o serviço MariaDB/MySQL e a porta real.
2. Confira `.env` na raiz do clone, nome exato e leitura pela conta que executa PHP (usuário do terminal ou do Apache). No Windows, evite `.env.txt` e BOM. Não publique seu conteúdo.
3. Confira os nomes `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`. O guia usa `127.0.0.1` e `db_foco`, com porta 3306 no exemplo Linux e 3308 no teste Windows. Use a porta real da instalação. A configuração não aceita um caminho de socket em `DB_HOST`.
4. Verifique variáveis de ambiente `DB_*`, que têm precedência sobre o arquivo. Reinicie o processo apropriado após mudar seu ambiente.
5. No cliente MariaDB ou no phpMyAdmin, confirme banco, tabelas e permissões. A conta da aplicação precisa de SELECT, INSERT, UPDATE e DELETE. A conta usada no phpMyAdmin pode ser diferente da conta da aplicação.
6. Execute o [teste PDO](database-setup.md) com o PHP do XAMPP. Abra também uma conexão pelo cliente do banco usando o mesmo host, porta e usuário; isso ajuda a distinguir credenciais e serviço, pois o código não registra detalhes da exceção.

Exemplo Linux, solicitando a senha no terminal:

```bash
/opt/lampp/bin/mysql --host=127.0.0.1 --port=3306 --user=foco_app --password db_foco
```

Windows (PowerShell):

```powershell
& "C:\xampp\mysql\bin\mysql.exe" --host=127.0.0.1 --port=3308 --user=foco_app --password db_foco
```

Os exemplos usam `foco_app`; substitua pela conta configurada no `.env` e ajuste a porta. Não passe a senha diretamente na linha de comando. Se houver `Access denied`, confirme a senha e o host da conta cadastrada. Se conectar pelo cliente do banco, mas falhar no PHP, compare usuário de execução, permissões, ambiente e `php.ini`. O projeto não aceita login com senha em texto puro na coluna de hash.

## PHP, extensão ausente ou resposta vazia/500

- Use `/opt/lampp/bin/php --ini` ou, no PowerShell, `& "C:\xampp\php\php.exe" --ini` para descobrir o ini da CLI. O servidor embutido usa a configuração do executável PHP indicado. O Apache pode usar outro ini; confira a configuração do módulo PHP do XAMPP.
- Liste módulos com `php -m` usando esse mesmo executável. A aplicação usa `mb_strlen`, portanto `mbstring` é necessária, além de `PDO` e `pdo_mysql`.
- Habilite a extensão ausente no `php.ini` da instalação correta. No Windows, pode haver linhas comentadas como `;extension=mbstring` ou `;extension=pdo_mysql`; descomente a existente se necessário. Não carregue DLLs de outra versão do PHP e não duplique entradas já habilitadas. Após uma alteração, reinicie o servidor embutido do PHP (Ctrl+C e o comando do README) ou o Apache, conforme o método em uso, e confira os logs se ele não iniciar.
- No Linux, não instale extensões do PHP da distribuição esperando que alterem automaticamente o PHP do XAMPP. Confira a instalação usada.
- Se o navegador baixar PHP ou mostrar seu código, pare o acesso e corrija o processamento PHP no Apache. Abrir arquivos com `file://` ou usar um servidor estático não executa PHP.

Logs comuns do Apache: `/opt/lampp/logs/error_log` no Linux e `C:\xampp\apache\logs\error.log` no Windows. O destino do log PHP depende de `error_log` no ini; se não definido, ele pode usar o log do servidor. No servidor embutido, confira também o terminal em que o comando `-S` está em execução. As falhas tratadas pelo Foco registram mensagens genéricas, sem SQL ou credenciais.

## Login não permanece ou formulário sempre expira

- Habilite cookies; use sempre o mesmo host e protocolo durante o fluxo. Não alterne entre `localhost`, `127.0.0.1` e `foco.local`.
- Confira `session.save_path`: diretório existente e gravável pela conta que executa PHP: usuário do terminal no servidor embutido ou usuário do Apache nessa alternativa. Não é necessário gravar sessões dentro do clone.
- O cookie é HttpOnly e SameSite=Lax. Secure é habilitado quando o PHP identifica HTTPS; se houver proxy ou configuração HTTPS, confira essa identificação.
- Um logout em outra aba invalida formulários antigos. Atualize antes de reenviar. O logout direto por GET retorna HTTP 405: use o botão **Sair da minha conta**.
- Cookies de outros projetos PHP no mesmo host podem interferir na sessão. Cookies não são isolados por porta: outras aplicações em `127.0.0.1`, mesmo em portas diferentes, também podem interferir. Se isso ocorrer, remova apenas os cookies locais envolvidos ou use um perfil de navegador separado. O host dedicado `foco.local` da alternativa Apache separa os cookies dos usados em `localhost` e `127.0.0.1`.

## Cadastro/CRUD falha, mas a conexão funciona

- Compare `SHOW CREATE TABLE usuarios` e `SHOW CREATE TABLE tarefas` com `database/schema.sql`. A importação com `IF NOT EXISTS` não migra estruturas antigas.
- Verifique as permissões de escrita da conta do banco. Não conceda privilégios administrativos para contornar um erro de validação.
- Nome/título têm limite de 150 caracteres; e-mail de 254. Senha de cadastro deve ter 8 a 72 bytes, sem byte nulo, e a confirmação precisa coincidir. Descrição aceita até 65535 bytes; prazo deve ser uma data válida entre 1000-01-01 e 9999-12-31.
- Um e-mail duplicado é rejeitado. Contas antigas precisam ter hash compatível com `password_verify()`; cadastrar uma conta nova pela interface é a forma normal de começar.
- “Tarefa indisponível” pode indicar ID inexistente ou tarefa de outro usuário. Não remova o filtro por proprietário.

## Modal/filtros não funcionam ou tarefas parecem sumir

- JavaScript precisa estar habilitado. Confira se `/assets/js/app.js` carrega sem erro, se o navegador suporta `<dialog>` e se o console mostra erros. Faça recarga completa após atualizar os arquivos.
- Limpe busca, status e prioridade antes de concluir que uma tarefa desapareceu. Os filtros são aplicados às tarefas do usuário já carregadas; os indicadores contam todas elas.
- A edição/status/exclusão preserva filtros quando sessionStorage está disponível. Criar tarefa limpa os filtros. Bloquear sessionStorage não impede salvar no banco.
- Ao ocorrer erro no formulário de tarefa, o modal reabre com os dados e a mensagem. Uma ação só é persistida quando o servidor aceita o POST.
- Datas de atraso seguem `America/Sao_Paulo` no PHP. Uma tarefa concluída não é contada como atrasada.

## Referências oficiais

- [PHP: servidor embutido](https://www.php.net/manual/en/features.commandline.webserver.php).
- [XAMPP: Linux](https://www.apachefriends.org/faq_linux.html).
- [XAMPP: Windows](https://www.apachefriends.org/faq_windows.html).
- [Apache 2.4: VirtualHosts por nome](https://httpd.apache.org/docs/2.4/vhosts/name-based.html).

A revisão deste guia não alterou configurações do computador. O teste Windows relatado está limitado ao [escopo registrado no README](../README.md#documentação-e-limites-da-validação). Ajustes de permissões, SELinux e instalação limpa devem ser validados no ambiente de quem estiver seguindo o procedimento.
