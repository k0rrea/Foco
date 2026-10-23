// DEMONSTRAÇÃO TEMPORÁRIA: dados somente em memória, sem banco ou armazenamento local.
// A integração definitiva poderá substituir este bloco mantendo o HTML da interface.
const taskModal = document.querySelector('#nova-tarefa');

if (taskModal) {
    const tasks = [];
    const form = taskModal.querySelector('form');
    const list = document.querySelector('#task-list');
    const template = document.querySelector('#task-template');
    const search = document.querySelector('#busca');
    const statusFilter = document.querySelector('#status');
    const priorityFilter = document.querySelector('#prioridade-filtro');
    const categories = { studies: 'Estudos', work: 'Trabalho', personal: 'Pessoal' };
    const priorities = { low: 'Baixa', medium: 'Média', high: 'Alta' };
    let nextId = 1;

    // Comparação por data local, evitando deslocamento de dia por fuso horário.
    function isOverdue(task) {
        const now = new Date();
        const today = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
        return Boolean(task.due && task.due < today && task.status !== 'completed');
    }

    function renderTasks() {
        const query = search.value.trim().toLocaleLowerCase('pt-BR');
        const visible = tasks.filter((task) =>
            `${task.title} ${task.description}`.toLocaleLowerCase('pt-BR').includes(query)
            && (!statusFilter.value || task.status === statusFilter.value)
            && (!priorityFilter.value || task.priority === priorityFilter.value)
        );
        list.replaceChildren();
        visible.forEach((task) => {
            const row = template.content.firstElementChild.cloneNode(true);
            row.classList.toggle('is-completed', task.status === 'completed');
            row.querySelector('.task-title').textContent = task.title;
            const description = row.querySelector('.task-description');
            description.textContent = task.description;
            description.hidden = !task.description;
            row.querySelector('.task-category').textContent = categories[task.category] || 'Sem categoria';
            row.querySelector('.task-priority').textContent = `Prioridade ${priorities[task.priority].toLowerCase()}`;
            const due = row.querySelector('.task-due');
            const overdue = isOverdue(task);
            due.textContent = task.due
                ? `${overdue ? 'Em atraso · ' : 'Prazo: '}${new Intl.DateTimeFormat('pt-BR').format(new Date(`${task.due}T12:00:00`))}`
                : 'Sem prazo';
            due.classList.toggle('is-overdue', overdue);
            const status = row.querySelector('.task-status');
            status.value = task.status;
            status.dataset.taskId = task.id;
            status.setAttribute('aria-label', `Status de ${task.title}`);
            status.addEventListener('change', () => {
                task.status = status.value;
                renderTasks();
                const replacement = list.querySelector(`[data-task-id="${task.id}"]`);
                (replacement || statusFilter).focus();
                document.querySelector('#task-feedback').textContent = `Status de ${task.title} atualizado.`;
            });
            const deleteButton = row.querySelector('.task-delete');
            deleteButton.setAttribute('aria-label', `Excluir tarefa: ${task.title}`);
            deleteButton.addEventListener('click', () => {
                if (!window.confirm(`Excluir a tarefa “${task.title}”? Essa ação não pode ser desfeita.`)) return;
                const index = tasks.findIndex((item) => item.id === task.id);
                if (index === -1) return;
                const visibleIndex = visible.findIndex((item) => item.id === task.id);
                tasks.splice(index, 1);
                renderTasks();
                // Mantém a navegação por teclado próxima da tarefa removida.
                const remainingButtons = list.querySelectorAll('.task-delete');
                const nextButton = remainingButtons[Math.min(visibleIndex, remainingButtons.length - 1)];
                (nextButton || document.querySelector('[data-open-task]')).focus();
                document.querySelector('#task-feedback').textContent = `Tarefa ${task.title} excluída.`;
            });
            list.append(row);
        });
        list.hidden = visible.length === 0;
        document.querySelector('.empty-state').hidden = tasks.length > 0;
        document.querySelector('#no-results').hidden = !tasks.length || visible.length > 0;
        const counts = {
            total: tasks.length,
            active: tasks.filter((task) => task.status === 'in-progress').length,
            completed: tasks.filter((task) => task.status === 'completed').length,
            overdue: tasks.filter(isOverdue).length,
        };
        Object.entries(counts).forEach(([key, value]) => {
            document.querySelector(`[data-stat="${key}"]`).textContent = value;
        });
        document.querySelectorAll('.nav-count, .count-badge').forEach((badge) => {
            badge.textContent = tasks.length;
        });
        document.querySelector('#task-count').textContent = tasks.length
            ? `${visible.length} de ${tasks.length} tarefa(s) · demonstração temporária`
            : 'Nenhuma tarefa cadastrada';
    }

    document.querySelectorAll('[data-open-task]').forEach((button) => {
        button.addEventListener('click', () => {
            taskModal.showModal();
            document.body.classList.add('has-modal');
        });
    });
    taskModal.querySelectorAll('[data-close-task]').forEach((button) => {
        button.addEventListener('click', () => taskModal.close());
    });
    taskModal.addEventListener('close', () => document.body.classList.remove('has-modal'));

    const titleInput = form.elements.namedItem('title');
    titleInput.addEventListener('input', () => titleInput.setCustomValidity(''));
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        titleInput.setCustomValidity(titleInput.value.trim() ? '' : 'Digite um título para a tarefa.');
        if (!form.reportValidity()) return;
        const data = new FormData(form);
        tasks.unshift({
            id: nextId++,
            title: data.get('title').trim(),
            description: data.get('description').trim(),
            due: data.get('due_date'),
            priority: data.get('priority'),
            category: data.get('category'),
            status: 'pending',
        });
        // Exibe a tarefa recém-criada mesmo que houvesse filtros ativos.
        search.value = statusFilter.value = priorityFilter.value = '';
        renderTasks();
        form.reset();
        taskModal.close();
        document.querySelector('#task-feedback').textContent = 'Tarefa criada temporariamente.';
    });
    search.addEventListener('input', renderTasks);
    statusFilter.addEventListener('change', renderTasks);
    priorityFilter.addEventListener('change', renderTasks);
    renderTasks();
}
