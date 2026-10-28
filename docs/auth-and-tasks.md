# Autenticação e tarefas — testes locais

## Antes de testar

Conclua o [guia de instalação](../README.md), incluindo banco, `.env`, Apache apontando para `public/` e sessões PHP. Use o endereço configurado, como `http://foco.local/`. Não é necessário reimportar o SQL se as tabelas já correspondem ao schema; uma instalação nova precisa da importação inicial.

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

Na implementação anterior, passaram as verificações de sintaxe PHP (XAMPP PHP 8.2.4), sintaxe JavaScript e `git diff --check`. Testes HTTP com o PHP do XAMPP e MariaDB 10.4.28 cobriram cadastro, e-mail duplicado, confirmação de senha, login correto/incorreto, regeneração da sessão, dashboard privado, CSRF, CRUD, persistência, indicadores, escape HTML, validações e isolamento entre duas contas.

Os testes HTTP utilizaram um servidor PHP local temporário e não validaram a renderização visual nem as interações JavaScript em navegador real. Execute o roteiro acima no Apache/XAMPP para conferir esses pontos. As contas temporárias criadas durante os testes foram removidas após a validação, sem alterar registros preexistentes.

A revisão documental não repetiu testes que gravam dados e não instalou ou reconfigurou serviços. Windows, instalação limpa e conferência visual em navegador real permanecem como validações manuais.
