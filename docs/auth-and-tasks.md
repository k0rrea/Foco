# Autenticação e tarefas — testes locais

## Antes de testar

Conclua o [guia de instalação](../README.md), incluindo banco, `.env`, servidor PHP com raiz web em `public/` e sessões PHP. Use `http://127.0.0.1:8000` no caminho principal ou `http://foco.local/` se optar pelo [Apache com VirtualHost](apache-setup.md). Não é necessário reimportar o SQL se as tabelas já correspondem ao schema; uma instalação nova precisa da importação inicial.

Use contas e tarefas próprias de teste. Os procedimentos a seguir gravam dados. Não altere identificadores de registros de terceiros. Não existe rotina automática de testes ou limpeza incluída no repositório; remova tarefas pela interface após a verificação. A remoção de contas, se desejada, é administrativa e exige cuidado, pois a chave estrangeira exclui suas tarefas em cascata.

## Roteiro funcional

1. Sem sessão, abra `/index.php`: deve ser redirecionado para `/pages/login.php`.
2. Clique em criar conta. Informe nome, e-mail e senha com confirmação. A senha deve ter de 8 a 72 bytes (acentos podem ocupar mais de um byte). O cadastro redireciona para o login.
3. Tente cadastrar o mesmo e-mail, uma senha sem confirmação correspondente, e-mail inválido e nome vazio: devem aparecer mensagens de validação. Senhas nunca são preenchidas novamente no HTML.
4. Teste uma senha incorreta e depois entre com a correta. O login leva ao dashboard privado.
5. Crie tarefas com descrição, prazo, prioridades e categorias diferentes. Recarregue a página: os registros devem permanecer.
6. Use **Editar** para alterar título, descrição, prazo, prioridade e categoria; salve e confira os valores após recarregar. Cancele outra edição e confira que nada foi alterado.
7. Altere o seletor de status para pendente, em andamento e concluída. Confira os indicadores. Tarefas com prazo anterior à data atual e não concluídas contam como atrasadas. Os indicadores representam todas as suas tarefas, independentemente dos filtros.
8. Combine busca por título/descrição, status e prioridade. Teste também uma busca sem resultados. Os filtros são locais sobre as tarefas que o PHP carregou exclusivamente para sua conta. Edição, status e exclusão preservam os filtros quando sessionStorage está disponível; uma criação limpa os filtros para mostrar a nova tarefa.
9. Exclua uma tarefa: cancelar a confirmação deve preservá-la; confirmar deve removê-la do banco. Atualizar a página após uma operação bem-sucedida não deve repetir o POST.
10. Clique em **Sair da minha conta**. Acesso posterior ao dashboard exige login novamente.
11. Em janela anônima, crie uma segunda conta. Ela deve começar sem as tarefas da primeira conta. Alterar o campo oculto `id` de um formulário para o ID de uma tarefa da primeira conta não deve permitir edição, mudança de status ou exclusão. Faça esse teste somente com suas contas de teste.
12. Confira o modal de criação/edição, fechamento por Cancelar/×/Escape, foco, filtros e layout em larguras de desktop e celular.

## Verificações de segurança

- Remover ou alterar o campo oculto `csrf` deve impedir cadastro, login, gravações e logout. No dashboard e logout, CSRF inválido retorna HTTP 403.
- Crie uma tarefa com título `<script>alert(1)</script>`: deve aparecer como texto, sem executar JavaScript. Apóstrofos e aspas também devem ser preservados como conteúdo.
- Datas inexistentes, status, prioridades e categorias fora das opções devem ser rejeitados no servidor, mesmo contornando a validação do navegador.
- O logout aceita somente POST. Dados privados recebem `Cache-Control: no-store`; o ID da sessão é regenerado após login e o cookie usa HttpOnly e SameSite=Lax. Secure é habilitado quando o PHP recebe HTTPS.
- As consultas de tarefas incluem o usuário autenticado; o identificador do proprietário nunca vem do formulário. As operações usam prepared statements nativos.
- Mensagens de falha não incluem SQL, credenciais ou rastreamentos de exceções.

## Organização

- `app/bootstrap.php`: sessão, cabeçalhos, PDO e funções de CSRF/escape.
- `app/auth.php`: processamento de cadastro e login.
- `app/tasks.php`: autorização, validação, CRUD, listagem e indicadores.
- `app/task-list.php`: renderização das tarefas com escape HTML.
- `public/pages/register.php` e `login.php`: formulários tradicionais com POST.
- `public/index.php`: dashboard privado e formulário de criação/edição.
- `public/logout.php`: encerramento da sessão por POST.
- `public/assets/js/app.js`: filtros, modais, edição e envio dos formulários, sem API REST.

JavaScript é necessário para abrir os modais e usar os filtros. Os registros e indicadores são renderizados no servidor. O CSS e as imagens foram preservados.

## Histórico e limites da validação

Na implementação anterior, passaram as verificações de sintaxe PHP (XAMPP PHP 8.2.4), sintaxe JavaScript e `git diff --check`. Testes HTTP no ambiente original Fedora Linux, com o PHP do XAMPP e MariaDB 10.4.28 cobriram cadastro, e-mail duplicado, confirmação de senha, login correto/incorreto, regeneração da sessão, dashboard privado, CSRF, CRUD, persistência, indicadores, escape HTML, validações e isolamento entre duas contas.

Os testes HTTP utilizaram um servidor PHP local temporário e não validaram a renderização visual nem as interações JavaScript em navegador real. Execute o roteiro acima em navegador real, usando o servidor embutido do PHP ou a alternativa Apache, para conferir esses pontos. As contas temporárias criadas durante os testes foram removidas após a validação, sem alterar registros preexistentes.

No teste adicional relatado pelo responsável pelo projeto, em **Windows 10**, com XAMPP em `C:\xampp`, MariaDB **10.4.32** na porta **3308** e PHP fornecido pelo XAMPP (versão não informada), o projeto ficou fora de `htdocs` e foi servido por:

```powershell
& "C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t public
```

Em `http://127.0.0.1:8000`, foram confirmados execução, acesso à interface, criação de conta, login, criação de tarefas e persistência após atualizar a página. Edição, exclusão, filtros, isolamento entre usuários, demais funcionalidades e configuração de VirtualHost do Apache **não foram validados nesse teste**. O acesso à interface não equivale a uma conferência visual completa ou a teste de todas as interações JavaScript.

Também foi relatado pelo responsável pelo projeto um teste com **MySQL 8.4.13 em um container Docker no Fedora Linux**, com porta publicada em `127.0.0.1:3307`. O banco `db_foco` foi importado utilizando `database/schema.sql`, e a aplicação foi conectada pelas variáveis do `.env`, com `DB_HOST=127.0.0.1` e `DB_PORT=3307`. Foram realizados com sucesso testes de cadastro, login e operações CRUD de tarefas. Esses resultados se referem à versão e ao ambiente informados e não validam todas as versões do MySQL.

A presente revisão documental não repetiu testes que gravam dados, instalação ou configuração de serviços. A execução integral deste roteiro no Windows, a instalação limpa a partir das instruções atualizadas e a conferência visual completa permanecem pendentes.
