<?php
require_once __DIR__ . '/bootstrap.php';
$userId = require_user();
$categories = ['studies' => 'Estudos', 'work' => 'Trabalho', 'personal' => 'Pessoal'];
$priorities = ['low' => 'Baixa', 'medium' => 'Média', 'high' => 'Alta'];
$statuses = ['pending' => 'Pendente', 'in-progress' => 'Em andamento', 'completed' => 'Concluída'];
$error = '';
$old = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = input('action');
    if (!valid_csrf()) { http_response_code(403); $error = 'Formulário expirado. Atualize a página e tente novamente.'; }
    else {
        try {
            $id = filter_var(input('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if (!in_array($action, ['create', 'update', 'status', 'delete'], true)) { throw new InvalidArgumentException('Operação inválida.'); }
            if ($action !== 'create') {
                if (!$id) { throw new InvalidArgumentException('Tarefa indisponível.'); }
                $stmt = db()->prepare('SELECT id FROM tarefas WHERE id = ? AND usuario_id = ?');
                $stmt->execute([$id, $userId]);
                if (!$stmt->fetch()) { throw new InvalidArgumentException('Tarefa indisponível.'); }
            }
            if ($action === 'create' || $action === 'update') {
                $old = ['title' => input('title'), 'description' => input('description'), 'due_date' => input('due_date'), 'priority' => input('priority'), 'category' => input('category'), 'id' => $id ?: '', 'action' => $action];
                if ($old['title'] === '' || length($old['title']) > 150) { throw new InvalidArgumentException('Informe um título de até 150 caracteres.'); }
                if (strlen($old['description']) > 65535) { throw new InvalidArgumentException('A descrição deve ter até 65535 bytes.'); }
                if (!isset($priorities[$old['priority']]) || ($old['category'] !== '' && !isset($categories[$old['category']]))) { throw new InvalidArgumentException('Prioridade ou categoria inválida.'); }
                $date = DateTimeImmutable::createFromFormat('!Y-m-d', $old['due_date']);
                if ($old['due_date'] !== '' && (!$date || $date->format('Y-m-d') !== $old['due_date'] || $old['due_date'] < '1000-01-01' || $old['due_date'] > '9999-12-31')) { throw new InvalidArgumentException('Informe um prazo válido.'); }
                $values = [$old['title'], $old['description'] ?: null, $old['due_date'] ?: null, $old['priority'], $old['category'] ?: null];
                if ($action === 'create') {
                    $stmt = db()->prepare('INSERT INTO tarefas (titulo, descricao, prazo, prioridade, categoria, usuario_id) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute(array_merge($values, [$userId]));
                } else {
                    $stmt = db()->prepare('UPDATE tarefas SET titulo = ?, descricao = ?, prazo = ?, prioridade = ?, categoria = ? WHERE id = ? AND usuario_id = ?');
                    $stmt->execute(array_merge($values, [$id, $userId]));
                }
            } elseif ($action === 'status') {
                $status = input('status');
                if (!isset($statuses[$status])) { throw new InvalidArgumentException('Status inválido.'); }
                $stmt = db()->prepare('UPDATE tarefas SET status = ? WHERE id = ? AND usuario_id = ?');
                $stmt->execute([$status, $id, $userId]);
            } else {
                $stmt = db()->prepare('DELETE FROM tarefas WHERE id = ? AND usuario_id = ?');
                $stmt->execute([$id, $userId]);
            }
            $_SESSION['flash'] = 'Tarefa ' . ($action === 'delete' ? 'excluída' : 'salva') . ' com sucesso.';
            redirect('index.php');
        } catch (InvalidArgumentException $exception) { $error = $exception->getMessage(); }
        catch (Throwable $exception) { $error = failure(); }
    }
}
$tasks = [];
try {
    $stmt = db()->prepare('SELECT id, titulo, descricao, prazo, prioridade, categoria, status FROM tarefas WHERE usuario_id = ? ORDER BY id DESC');
    $stmt->execute([$userId]);
    $tasks = $stmt->fetchAll();
} catch (Throwable $exception) { $error = failure(); }
$notice = $error ?: ($_SESSION['flash'] ?? 'Suas tarefas são salvas na sua conta.');
unset($_SESSION['flash']);
$today = date('Y-m-d');
$counts = ['total' => count($tasks), 'active' => 0, 'completed' => 0, 'overdue' => 0];
foreach ($tasks as $task) {
    $counts['active'] += $task['status'] === 'in-progress';
    $counts['completed'] += $task['status'] === 'completed';
    $counts['overdue'] += $task['prazo'] && $task['prazo'] < $today && $task['status'] !== 'completed';
}
