<?php foreach ($tasks as $task):
// Renderiza cada tarefa com escape HTML; o controlador fornece tarefas, mapas de rótulos e data atual.
$overdue = $task['prazo'] && $task['prazo'] < $today && $task['status'] !== 'completed';
// O JSON do botão de edição é escapado no atributo e lido pelo JavaScript para preencher o modal.
$edit = ['id' => $task['id'], 'title' => $task['titulo'], 'description' => $task['descricao'] ?? '', 'due_date' => $task['prazo'] ?? '', 'priority' => $task['prioridade'], 'category' => $task['categoria'] ?? '']; ?>
<li class="task-row <?= $task['status'] === 'completed' ? 'is-completed' : '' ?>" data-status="<?= e($task['status']) ?>" data-priority="<?= e($task['prioridade']) ?>">
    <div class="task-info">
        <h3 class="task-title"><?= e($task['titulo']) ?></h3>
        <p class="task-description" <?= !$task['descricao'] ? 'hidden' : '' ?>><?= e($task['descricao']) ?></p>
        <div class="task-meta"><span><?= e($categories[$task['categoria']] ?? 'Sem categoria') ?></span><span>Prioridade <?= e($priorities[$task['prioridade']]) ?></span><span class="<?= $overdue ? 'is-overdue' : '' ?>"><?= $task['prazo'] ? ($overdue ? 'Em atraso · ' : 'Prazo: ') . e(date('d/m/Y', strtotime($task['prazo']))) : 'Sem prazo' ?></span></div>
    </div>
    <!-- O status usa POST com CSRF; sem JavaScript, o botão noscript permite enviar a alteração. -->
    <form method="post" class="field task-status-field" data-status-form>
        <?= csrf_field() ?><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= e($task['id']) ?>">
        <label>Status da tarefa<select class="task-status" name="status" aria-label="Status de <?= e($task['titulo']) ?>">
        <?php foreach ($statuses as $value => $label): ?><option value="<?= e($value) ?>" <?= $value === $task['status'] ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
        </select></label><noscript><button class="button" type="submit">Atualizar</button></noscript>
    </form>
    <button class="button button-secondary" type="button" data-edit-task="<?= e(json_encode($edit, JSON_INVALID_UTF8_SUBSTITUTE)) ?>" aria-haspopup="dialog" aria-controls="nova-tarefa">Editar</button>
    <!-- A exclusão identifica a tarefa no POST; a autorização é verificada novamente no servidor. -->
    <form method="post" data-delete-task>
        <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($task['id']) ?>">
        <button class="button task-delete" type="submit" aria-label="Excluir <?= e($task['titulo']) ?>">Excluir</button>
    </form>
</li>
<?php endforeach; ?>
