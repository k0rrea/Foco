# Solução de problemas

Use este guia após seguir a [instalação no README](../README.md). Os caminhos assumem `/opt/lampp` no Linux e `C:\xampp` no Windows. Ajuste-os se necessário. Não exponha credenciais nem publique páginas `phpinfo()` para diagnosticar o sistema.

## Apache não inicia ou endereço não abre

- Confira se Apache e MySQL estão iniciados no XAMPP. No Linux: `sudo /opt/lampp/lampp start`; no Windows: painel XAMPP.
- Verifique os logs do painel e do Apache. Outro Apache, Nginx, IIS ou banco local pode estar usando as portas. Não inicie duas instalações na mesma porta.
- A porta HTTP do guia é 80. Se escolher 8080, ajuste `Listen`, os blocos `<VirtualHost *:8080>` e o endereço para `http://foco.local:8080/`. Isso não muda `DB_PORT`, que é a porta do banco. A entrada hosts não contém porta.
- Confirme a entrada `127.0.0.1 foco.local` no hosts e a inclusão do arquivo de VirtualHosts. `httpd -S`, com o executável do XAMPP indicado no README, mostra os hosts carregados.
- Se o nome não resolver, confira se o hosts foi salvo sem extensão adicional e se o navegador/proxy está usando a resolução local. Não trate o nome `foco.local` como um domínio público.

## 403 Forbidden

Há duas situações distintas:

1. **Página genérica do Apache**, inclusive ao abrir login: confira o caminho em `DocumentRoot` e `<Directory>`, a regra `Require local` e as permissões de leitura/travessia. `Require local` admite apenas acesso da própria máquina. Abrir um diretório sem índice também pode resultar em 403 porque listagem está desabilitada.
2. **Mensagem “Formulário expirado”** ao enviar dashboard/logout: é a proteção CSRF. Reabra a página, mantenha cookies habilitados e envie o formulário recém-carregado. Não remova a proteção CSRF. Sessões sem permissão de gravação podem causar repetição desse problema.

No Linux, o Apache precisa atravessar **cada pasta pai** e ler arquivos públicos, `app/`, `config/` e `.env`. Descubra o usuário/grupo efetivos na configuração/processos do Apache; não presuma que sejam `apache`, `www-data` ou `daemon`. Conceda acesso somente à conta/grupo necessário, por permissões ou ACLs. `chmod 600 .env` só funciona se o processo que o lê for o proprietário. Não use `chmod -R 777`.

Em Fedora e outras distribuições com SELinux, permissões Unix corretas podem não ser suficientes. Confira os registros de negação e a política aplicável ao processo/caminho antes de ajustar contextos ou regras. Não desabilite SELinux como solução padrão. No Windows, verifique as permissões NTFS da conta que executa o Apache e as permissões dos diretórios de sessão.

## 404 ou página errada

- O endereço do guia é `http://foco.local/`, sem acrescentar `/public` ou `/Foco`. A raiz web já é `public/`.
- O formulário de login é `/pages/login.php`, e o cadastro é `/pages/register.php`.
- Confirme que o caminho do host termina em `/Foco/public` e que `public/index.php` existe. No Linux, nomes distinguem maiúsculas e minúsculas.
- Confirme `Include`, `ServerName` e hosts com `httpd -S`; reinicie o Apache após corrigir. Não é necessário `mod_rewrite` ou `.htaccess`.
- Se abrir o painel XAMPP em vez do Foco, a requisição provavelmente chegou ao host errado. Se phpMyAdmin deixou de abrir após adicionar o Foco, preserve um host `localhost` antes dele, conforme o README.
- CSS/JS/imagens com 404: confira o clone completo e o DocumentRoot. Não altere URLs para incluir o caminho absoluto do seu computador.

## Falha de conexão / HTTP 503

A mensagem genérica é intencional. Siga esta ordem:

1. Confirme o serviço MariaDB/MySQL e a porta real.
2. Confira `.env` na raiz do clone, nome exato e leitura pelo Apache. No Windows, evite `.env.txt` e BOM. Não publique seu conteúdo.
3. Confira os nomes `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`. O guia usa `127.0.0.1`, `3306` e `db_foco`. A configuração não aceita um caminho de socket em `DB_HOST`.
4. Verifique variáveis de ambiente `DB_*`, que têm precedência sobre o arquivo. Reinicie o processo apropriado após mudar seu ambiente.
5. No phpMyAdmin, confirme banco, tabelas e permissões. A conta da aplicação precisa de SELECT, INSERT, UPDATE e DELETE. A conta usada no phpMyAdmin pode ser diferente da conta da aplicação.
6. Execute o [teste PDO](database-setup.md) com o PHP do XAMPP. Abra também uma conexão pelo cliente do banco usando o mesmo host, porta e usuário; isso ajuda a distinguir credenciais e serviço, pois o código não registra detalhes da exceção.

Exemplo Linux, solicitando a senha no terminal:

```bash
/opt/lampp/bin/mysql --host=127.0.0.1 --port=3306 --user=foco_app --password db_foco
```

Windows (PowerShell):

```powershell
& C:\xampp\mysql\bin\mysql.exe --host=127.0.0.1 --port=3306 --user=foco_app --password db_foco
```

Não passe a senha diretamente na linha de comando. Se houver `Access denied`, confirme a senha e o host da conta cadastrada. Se conectar pelo terminal, mas falhar no Apache, compare usuário de execução, permissões, ambiente e `php.ini`. O projeto não aceita login com senha em texto puro na coluna de hash.

## PHP, extensão ausente ou resposta vazia/500

- Use `/opt/lampp/bin/php --ini` ou, no PowerShell, `& C:\xampp\php\php.exe --ini` para descobrir o ini da CLI. O Apache pode usar outro ini; confira a configuração do módulo PHP do XAMPP.
- Liste módulos com `php -m` usando esse mesmo executável. A aplicação usa `mb_strlen`, portanto `mbstring` é necessária, além de `PDO` e `pdo_mysql`.
- Habilite a extensão ausente no `php.ini` da instalação correta. No Windows, pode haver linhas comentadas como `;extension=mbstring` ou `;extension=pdo_mysql`; descomente a existente se necessário. Não carregue DLLs de outra versão do PHP e não duplique entradas já habilitadas. Reinicie o Apache e confira os logs se ele não iniciar.
- No Linux, não instale extensões do PHP da distribuição esperando que alterem automaticamente o PHP do XAMPP. Confira a instalação usada.
- Se o navegador baixar PHP ou mostrar seu código, pare o acesso e corrija o processamento PHP no Apache. Abrir arquivos com `file://` ou usar um servidor estático não executa PHP.

Logs comuns do Apache: `/opt/lampp/logs/error_log` no Linux e `C:\xampp\apache\logs\error.log` no Windows. O destino do log PHP depende de `error_log` no ini; se não definido, ele pode usar o log do servidor. As falhas tratadas pelo Foco registram mensagens genéricas, sem SQL ou credenciais.

## Login não permanece ou formulário sempre expira

- Habilite cookies; use sempre o mesmo host e protocolo durante o fluxo. Não alterne entre `localhost`, `127.0.0.1` e `foco.local`.
- Confira `session.save_path`: diretório existente e gravável pelo usuário do Apache, não apenas pelo usuário do terminal.
- O cookie é HttpOnly e SameSite=Lax. Secure é habilitado quando o PHP identifica HTTPS; se houver proxy ou configuração HTTPS, confira essa identificação.
- Um logout em outra aba invalida formulários antigos. Atualize antes de reenviar. O logout direto por GET retorna HTTP 405: use o botão **Sair da minha conta**.
- Cookies de outros projetos PHP no mesmo host podem interferir na sessão. Usar o host dedicado `foco.local` evita compartilhá-lo com aplicações em `localhost`.

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

- [XAMPP: Linux](https://www.apachefriends.org/faq_linux.html).
- [XAMPP: Windows](https://www.apachefriends.org/faq_windows.html).
- [Apache 2.4: VirtualHosts por nome](https://httpd.apache.org/docs/2.4/vhosts/name-based.html).

A revisão deste guia não alterou configurações do computador. Ajustes de permissões, SELinux, Windows e instalação limpa devem ser validados no ambiente de quem estiver seguindo o procedimento.
