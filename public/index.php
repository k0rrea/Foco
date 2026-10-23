<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Organize suas tarefas e abra espaço para o que importa com o Foco.">
    <title>Minhas tarefas | Foco</title>
    <link rel="icon" type="image/png" href="assets/images/FocoApp.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/app.js" defer></script>
</head>

<body>
    <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

    <div class="app-layout">
        <aside class="sidebar" aria-label="Menu lateral">
            <a class="brand" href="index.php" aria-label="Foco — página principal">
                <img class="logo-light" src="assets/images/FocoEscritaPreto.png" alt="Foco" width="2193" height="717">
                <img class="logo-dark" src="assets/images/FocoEscritaBranco.png" alt="Foco" width="2172" height="724">
            </a>

            <div class="workspace">
                <span class="workspace-icon" aria-hidden="true">F</span>
                <div><strong>Meu espaço</strong><span>Organização pessoal</span></div>
            </div>

            <nav class="main-nav" aria-label="Navegação principal">
                <p class="eyebrow">Seu planejamento</p>
                <a class="nav-link" href="#resumo"><span aria-hidden="true">◫</span> Visão geral</a>
                <a class="nav-link is-active" href="#tarefas" aria-current="page"><span aria-hidden="true">☷</span>
                    Minhas tarefas <span class="nav-count">0</span></a>
                <button class="nav-link" type="button" data-open-task aria-haspopup="dialog"
                    aria-controls="nova-tarefa"><span aria-hidden="true">＋</span> Criar tarefa</button>
            </nav>

            <div class="sidebar-note">
                <span class="note-mark" aria-hidden="true">◎</span>
                <strong>Um passo de cada vez.</strong>
                <p>Você não precisa fazer tudo agora. Comece pelo que importa.</p>
            </div>

            <div class="account-links">
                <a href="pages/login.php">Entrar na minha conta <span aria-hidden="true">↗</span></a>
                <p>Novo por aqui? <a href="pages/register.php">Criar conta</a></p>
            </div>
        </aside>

        <div class="main-layout">
            <header class="topbar">
                <p>Meu espaço <span aria-hidden="true">/</span> <strong>Minhas tarefas</strong></p>
                <span class="preview-label">Prévia da interface</span>
            </header>

            <main id="conteudo" class="main-content" tabindex="-1">
                <header class="page-heading">
                    <div>
                        <p class="eyebrow text-primary">Menos distrações, mais direção</p>
                        <h1>Espaço para o que importa.</h1>
                        <p>Organize suas tarefas e dê o próximo passo no seu ritmo.</p>
                    </div>
                    <button class="button button-primary" type="button" data-open-task aria-haspopup="dialog"
                        aria-controls="nova-tarefa"><span aria-hidden="true">＋</span> Nova tarefa</button>
                </header>

                <!-- Prévia temporária: indicadores calculados no JavaScript, sem persistência. -->
                <section id="resumo" class="overview" aria-labelledby="resumo-titulo">
                    <h2 id="resumo-titulo" class="sr-only">Resumo das tarefas</h2>
                    <dl class="stats-grid">
                        <div class="stat-card">
                            <dt>Total de tarefas <span class="stat-icon" aria-hidden="true">☷</span></dt>
                            <dd data-stat="total">0</dd>
                            <dd class="stat-description">Tudo no seu radar</dd>
                        </div>
                        <div class="stat-card">
                            <dt>Em andamento <span class="stat-icon" aria-hidden="true">◷</span></dt>
                            <dd data-stat="active">0</dd>
                            <dd class="stat-description">Um passo de cada vez</dd>
                        </div>
                        <div class="stat-card">
                            <dt>Concluídas <span class="stat-icon" aria-hidden="true">✓</span></dt>
                            <dd data-stat="completed">0</dd>
                            <dd class="stat-description">Cada conquista conta</dd>
                        </div>
                        <div class="stat-card">
                            <dt>Em atraso <span class="stat-icon" aria-hidden="true">⚑</span></dt>
                            <dd data-stat="overdue">0</dd>
                            <dd class="stat-description">Precisam de atenção</dd>
                        </div>
                    </dl>
                </section>

                <section id="tarefas" class="panel tasks-panel" aria-labelledby="tarefas-titulo">
                    <header class="section-heading">
                        <div>
                            <h2 id="tarefas-titulo">Minhas tarefas <span class="count-badge">0</span></h2>
                            <p>Pequenas ações. Novas possibilidades.</p>
                        </div>
                        <span class="muted">Visualização em lista</span>
                    </header>

                    <p id="temporary-note" class="temporary-note">Modo de demonstração: as tarefas são apagadas ao
                        recarregar ou fechar esta página.</p>
                    <fieldset class="task-filters" aria-describedby="temporary-note">
                        <legend class="sr-only">Buscar e filtrar tarefas</legend>
                        <div class="field search-field"><label for="busca">Buscar tarefa</label><input id="busca"
                                name="busca" type="search" placeholder="O que você procura?"></div>
                        <div class="field"><label for="status">Status</label><select id="status" name="status">
                                <option value="">Todos os status</option>
                                <option value="pending">Pendente</option>
                                <option value="in-progress">Em andamento</option>
                                <option value="completed">Concluída</option>
                            </select></div>
                        <div class="field"><label for="prioridade-filtro">Prioridade</label><select
                                id="prioridade-filtro" name="prioridade">
                                <option value="">Todas</option>
                                <option value="high">Alta</option>
                                <option value="medium">Média</option>
                                <option value="low">Baixa</option>
                            </select></div>
                    </fieldset>

                    <ul id="task-list" class="task-list" aria-label="Tarefas cadastradas" hidden></ul>
                    <p id="no-results" class="no-results" hidden>Nenhuma tarefa corresponde aos filtros escolhidos.</p>
                    <p id="task-feedback" class="sr-only" role="status"></p>
                    <template id="task-template">
                        <li class="task-row">
                            <div class="task-info">
                                <h3 class="task-title"></h3>
                                <p class="task-description"></p>
                                <div class="task-meta"><span class="task-category"></span><span
                                        class="task-priority"></span><span class="task-due"></span></div>
                            </div>
                            <div class="field task-status-field"><label>Status da tarefa<select class="task-status">
                                        <option value="pending">Pendente</option>
                                        <option value="in-progress">Em andamento</option>
                                        <option value="completed">Concluída</option>
                                    </select></label></div>
                            <button class="button task-delete" type="button">Excluir</button>
                        </li>
                    </template>
                    <div class="empty-state">
                        <span class="empty-icon" aria-hidden="true">✓</span>
                        <h3>Seu próximo passo começa aqui.</h3>
                        <p>Uma ideia, uma entrega ou algo que você não quer esquecer.<br>Abra espaço para sua primeira
                            tarefa.</p>
                        <button class="button button-secondary" type="button" data-open-task aria-haspopup="dialog"
                            aria-controls="nova-tarefa">＋ Preparar minha primeira tarefa</button>
                    </div>
                    <footer class="panel-footer"><span id="task-count">Nenhuma tarefa cadastrada</span><span>Seu ritmo.
                            Suas conquistas.</span></footer>
                </section>

                <!-- Demonstração em memória. Substituir pela integração de backend futuramente. -->
                <dialog id="nova-tarefa" class="panel task-modal" aria-labelledby="nova-tarefa-titulo">
                    <header class="modal-heading">
                        <div>
                            <p class="eyebrow text-primary">Um novo passo</p>
                            <h2 id="nova-tarefa-titulo">Nova tarefa</h2>
                        </div>
                        <button class="modal-close" type="button" data-close-task
                            aria-label="Fechar nova tarefa">×</button>
                    </header>
                    <div class="editor-content">
                        <p id="preview-note" class="preview-note">Você pode criar tarefas para testar a interface. Elas
                            ficam disponíveis apenas até recarregar ou fechar esta página.</p>
                        <form aria-label="Nova tarefa" aria-describedby="preview-note">
                            <div class="field"><label for="titulo">Título <span
                                        class="muted">(obrigatório)</span></label><input id="titulo" name="title"
                                    type="text" placeholder="Ex.: revisar o conteúdo da aula" maxlength="150" required
                                    autofocus></div>
                            <div class="field"><label for="descricao">Descrição <span
                                        class="muted">(opcional)</span></label><textarea id="descricao"
                                    name="description" rows="3"
                                    placeholder="Adicione os detalhes que ajudam você a começar."></textarea></div>
                            <div class="form-grid">
                                <div class="field"><label for="prazo">Prazo</label><input id="prazo" name="due_date"
                                        type="date"></div>
                                <div class="field"><label for="prioridade">Prioridade</label><select id="prioridade"
                                        name="priority">
                                        <option value="low">Baixa</option>
                                        <option value="medium" selected>Média</option>
                                        <option value="high">Alta</option>
                                    </select></div>
                                <div class="field"><label for="categoria">Categoria</label><select id="categoria"
                                        name="category">
                                        <option value="">Sem categoria</option>
                                        <option value="studies">Estudos</option>
                                        <option value="work">Trabalho</option>
                                        <option value="personal">Pessoal</option>
                                    </select></div>
                            </div>
                            <div class="form-actions">
                                <p class="muted">Salvamento temporário para demonstração.</p>
                                <div class="modal-actions"><button class="button button-secondary" type="button"
                                        data-close-task>Cancelar</button><button class="button button-primary"
                                        type="submit">Salvar tarefa</button></div>
                            </div>
                        </form>
                    </div>
                </dialog>

                <footer class="page-footer"><span>Foco no presente. Um passo adiante.</span></footer>
            </main>
        </div>
    </div>
</body>

</html>