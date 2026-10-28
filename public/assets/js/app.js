// O PHP renderiza e persiste as tarefas; o JavaScript cuida das interações.
const taskModal = document.querySelector('#nova-tarefa');
if (taskModal) {
    const form = taskModal.querySelector('form');
    const rows = [...document.querySelectorAll('.task-row')];
    const search = document.querySelector('#busca');
    const statusFilter = document.querySelector('#status');
    const priorityFilter = document.querySelector('#prioridade-filtro');
    // Preserva os filtros nas recargas causadas por edição, status e exclusão.
    const filterKey = `foco-filters-${document.querySelector('#task-list').dataset.user}`;
    try {
        const saved = JSON.parse(sessionStorage.getItem(filterKey) || 'null');
        sessionStorage.removeItem(filterKey);
        if (saved) [search.value, statusFilter.value, priorityFilter.value] = saved;
    } catch { /* Armazenamento opcional: filtros continuam funcionando sem ele. */ }
    document.querySelectorAll('form').forEach(postForm => postForm.addEventListener('submit', () => {
        if (postForm.getAttribute('action') === 'logout.php') return;
        try {
            const create = postForm === form && form.elements.namedItem('action').value === 'create';
            sessionStorage.setItem(filterKey, JSON.stringify(create ? ['', '', ''] : [search.value, statusFilter.value, priorityFilter.value]));
        } catch { /* O envio do formulário independe do armazenamento. */ }
    }));
    function filterTasks() {
        const query = search.value.trim().toLocaleLowerCase('pt-BR');
        let count = 0;
        rows.forEach(row => {
            const text = `${row.querySelector('.task-title').textContent} ${row.querySelector('.task-description').textContent}`.toLocaleLowerCase('pt-BR');
            row.hidden = !(text.includes(query) && (!statusFilter.value || row.dataset.status === statusFilter.value) && (!priorityFilter.value || row.dataset.priority === priorityFilter.value));
            if (!row.hidden) count++;
        });
        document.querySelector('#task-list').hidden = count === 0;
        document.querySelector('#no-results').hidden = !rows.length || count > 0;
        document.querySelector('#task-count').textContent = rows.length ? `${count} de ${rows.length} tarefa(s)` : 'Nenhuma tarefa cadastrada';
    }
    [search, statusFilter, priorityFilter].forEach(control => control.addEventListener('input', filterTasks));
    function openTask(values = {}) {
        form.reset();
        form.elements.namedItem('action').value = values.action || (values.id ? 'update' : 'create');
        form.elements.namedItem('id').value = values.id || '';
        ['title', 'description', 'due_date', 'priority', 'category'].forEach(key => {
            if (values[key] !== undefined) form.elements.namedItem(key).value = values[key];
        });
        document.querySelector('#nova-tarefa-titulo').textContent = values.id ? 'Editar tarefa' : 'Nova tarefa';
        form.setAttribute('aria-label', values.id ? 'Editar tarefa' : 'Nova tarefa');
        form.elements.namedItem('title').setCustomValidity('');
        taskModal.showModal();
        document.body.classList.add('has-modal');
    }
    document.querySelectorAll('[data-open-task]').forEach(button => button.addEventListener('click', () => openTask()));
    document.querySelectorAll('[data-edit-task]').forEach(button => button.addEventListener('click', () => openTask(JSON.parse(button.dataset.editTask))));
    taskModal.querySelectorAll('[data-close-task]').forEach(button => button.addEventListener('click', () => taskModal.close()));
    taskModal.addEventListener('close', () => document.body.classList.remove('has-modal'));
    document.querySelectorAll('[data-status-form]').forEach(statusForm => statusForm.querySelector('select').addEventListener('change', () => statusForm.requestSubmit()));
    document.querySelectorAll('[data-delete-task]').forEach(deleteForm => deleteForm.addEventListener('submit', event => {
        if (!window.confirm('Excluir esta tarefa? Essa ação não pode ser desfeita.')) event.preventDefault();
    }));
    const title = form.elements.namedItem('title');
    title.addEventListener('input', () => title.setCustomValidity(''));
    form.addEventListener('submit', event => {
        title.setCustomValidity(title.value.trim() ? '' : 'Digite um título para a tarefa.');
        if (!form.reportValidity()) event.preventDefault();
    });
    const old = JSON.parse(form.dataset.old);
    if (old.action) openTask(old);
    filterTasks();
}
