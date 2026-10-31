<?php
// Controlador da área privada: valida operações, persiste tarefas e prepara dados para o HTML.
require_once __DIR__ . '/bootstrap.php';
$userId = require_user();
// Mapas de valores aceitos também fornecem os rótulos usados na interface.
$categories = ['studies' => 'Estudos', 'work' => 'Trabalho', 'personal' => 'Pessoal'];
$priorities = ['low' => 'Baixa', 'medium' => 'Média', 'high' => 'Alta'];
$statuses = ['pending' => 'Pendente', 'in-progress' => 'Em andamento', 'completed' => 'Concluída'];
$error = '';
$old = [];
// Alterações exigem POST e token CSRF; a identificação do usuário vem da sessão.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = input('action');
    if (!valid_csrf()) { http_response_code(403); $error = 'Formulário expirado. Atualize a página e tente novamente.'; }
    else {
        try {
            $id = filter_var(input('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if (!in_array($action, ['create', 'update', 'status', 'delete'], true)) { throw new InvalidArgumentException('Operação inválida.'); }
            // Antes de alterar ou excluir, confirma que a tarefa pertence ao usuário autenticado.
            if ($action !== 'create') {
                if (!$id) { throw new InvalidArgumentException('Tarefa indisponível.'); }
                $stmt = db()->prepare('SELECT id FROM tarefas WHERE id = ? AND usuario_id = ?');
                $stmt->execute([$id, $userId]);
                if (!$stmt->fetch()) { throw new InvalidArgumentException('Tarefa indisponível.'); }
            }
            // Criação e edição compartilham validações; $old permite repor o formulário se houver erro.
            if ($action === 'create' || $action === 'update') {
                $old = ['title' => input('title'), 'description' => input('description'), 'due_date' => input('due_date'), 'priority' => input('priority'), 'category' => input('category'), 'id' => $id ?: '', 'action' => $action];
                if ($old['title'] === '' || length($old['title']) > 150) { throw new InvalidArgumentException('Informe um título de até 150 caracteres.'); }
                if (strlen($old['description']) > 65535) { throw new InvalidArgumentException('A descrição deve ter até 65535 bytes.'); }
                if (!isset($priorities[$old['priority']]) || ($old['category'] !== '' && !isset($categories[$old['category']]))) { throw new InvalidArgumentException('Prioridade ou categoria inválida.'); }
                // A conversão de ida e volta rejeita datas normalizadas, como dias inexistentes no mês.
                $date = DateTimeImmutable::createFromFormat('!Y-m-d', $old['due_date']);
                if ($old['due_date'] !== '' && (!$date || $date->format('Y-m-d') !== $old['due_date'] || $old['due_date'] < '1000-01-01' || $old['due_date'] > '9999-12-31')) { throw new InvalidArgumentException('Informe um prazo válido.'); }
                // Campos opcionais vazios são enviados como NULL, seguindo o esquema do banco.
                $values = [$old['title'], $old['description'] ?: null, $old['due_date'] ?: null, $old['priority'], $old['category'] ?: null];
                // Os parâmetros mantêm os dados fora do SQL; a edição também restringe usuario_id.
                if ($action === 'create') {
                    $stmt = db()->prepare('INSERT INTO tarefas (titulo, descricao, prazo, prioridade, categoria, usuario_id) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute(array_merge($values, [$userId]));
                } else {
                    $stmt = db()->prepare('UPDATE tarefas SET titulo = ?, descricao = ?, prazo = ?, prioridade = ?, categoria = ? WHERE id = ? AND usuario_id = ?');
                    $stmt->execute(array_merge($values, [$id, $userId]));
                }
            // A atualização de status aceita apenas os valores definidos no mapa do controlador.
            } elseif ($action === 'status') {
                $status = input('status');
                if (!isset($statuses[$status])) { throw new InvalidArgumentException('Status inválido.'); }
                $stmt = db()->prepare('UPDATE tarefas SET status = ? WHERE id = ? AND usuario_id = ?');
                $stmt->execute([$status, $id, $userId]);
            } else {
                // A exclusão mantém a condição de propriedade mesmo após a verificação inicial.
                $stmt = db()->prepare('DELETE FROM tarefas WHERE id = ? AND usuario_id = ?');
                $stmt->execute([$id, $userId]);
            }
            // Redireciona após sucesso para evitar repetição do POST ao atualizar a página.
            $_SESSION['flash'] = 'Tarefa ' . ($action === 'delete' ? 'excluída' : 'salva') . ' com sucesso.';
            redirect('index.php');
        } catch (InvalidArgumentException $exception) { $error = $exception->getMessage(); }
        catch (Throwable $exception) { $error = failure(); }
    }
}
// A listagem consulta somente tarefas da sessão atual, das mais recentes para as mais antigas.
$tasks = [];
try {
    $stmt = db()->prepare('SELECT id, titulo, descricao, prazo, prioridade, categoria, status FROM tarefas WHERE usuario_id = ? ORDER BY id DESC');
    $stmt->execute([$userId]);
    $tasks = $stmt->fetchAll();
} catch (Throwable $exception) { $error = failure(); }
// A mensagem temporária é exibida uma vez; erros têm prioridade sobre confirmações.
$notice = $error ?: ($_SESSION['flash'] ?? 'Suas tarefas são salvas na sua conta.');
unset($_SESSION['flash']);
// Os indicadores consideram todas as tarefas carregadas, independentemente dos filtros do navegador.
$today = date('Y-m-d');
$counts = ['total' => count($tasks), 'active' => 0, 'completed' => 0, 'overdue' => 0];
// Uma tarefa com prazo anterior a hoje só conta como atrasada se ainda não foi concluída.
foreach ($tasks as $task) {
    $counts['active'] += $task['status'] === 'in-progress';
    $counts['completed'] += $task['status'] === 'completed';
    $counts['overdue'] += $task['prazo'] && $task['prazo'] < $today && $task['status'] !== 'completed';
}
