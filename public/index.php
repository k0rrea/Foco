<?php require dirname(__DIR__) . "/app/tasks.php"; ?>
<!-- Área privada: o controlador acima exige autenticação e prepara tarefas, indicadores e mensagens. -->
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Organize suas tarefas e abra espaço para o que importa com o Foco.">
    <title>Minhas tarefas | Foco</title>
    <link rel="icon" type="image/png" href="assets/images/FocoApp.png">
    <!-- O tema precede o CSS; as interações de tarefas aguardam o HTML com defer. -->
    <script src="assets/js/theme.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/app.js" defer></script>
</head>

<body>
    <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

    <div class="app-layout">
        <!-- Navegação, aparência e saída da conta compartilham a coluna lateral. -->
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
                    Minhas tarefas <span class="nav-count"><?= count($tasks) ?></span></a>
                <button class="nav-link" type="button" data-open-task aria-haspopup="dialog"
                    aria-controls="nova-tarefa"><span aria-hidden="true">＋</span> Criar tarefa</button>
            </nav>

            <div class="sidebar-note">
                <span class="note-mark" aria-hidden="true">◎</span>
                <strong>Um passo de cada vez.</strong>
                <p>Você não precisa fazer tudo agora. Comece pelo que importa.</p>
            </div>

            <!-- Aparência é uma preferência do navegador; logout usa POST protegido por CSRF. -->
            <div class="account-links">
                <div class="field theme-field">
                    <label for="theme-mode">Aparência</label>
                    <select id="theme-mode">
                        <option value="light">Claro</option>
                        <option value="dark">Escuro</option>
                        <option value="system" selected>Sistema</option>
                    </select>
                </div>
                <form action="logout.php" method="post"><?= csrf_field() ?><button class="nav-link" type="submit">Sair da minha conta <span aria-hidden="true">↗</span></button></form>
            </div>
        </aside>

        <!-- Coluna com rolagem própria no desktop e cabeçalho mantido no topo pelo CSS. -->
        <div class="main-layout">
            <header class="topbar">
                <p>Meu espaço <span aria-hidden="true">/</span> <strong>Minhas tarefas</strong></p>
                <span class="preview-label">Meu planejamento</span>
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

                <!-- Indicadores de todas as tarefas do usuário. -->
                <section id="resumo" class="overview" aria-labelledby="resumo-titulo">
                    <h2 id="resumo-titulo" class="sr-only">Resumo das tarefas</h2>
                    <dl class="stats-grid">
                        <div class="stat-card">
                            <dt>Total de tarefas <span class="stat-icon" aria-hidden="true">☷</span></dt>
                            <dd data-stat="total"><?= $counts["total"] ?></dd>
                            <dd class="stat-description">Tudo no seu radar</dd>
                        </div>
                        <div class="stat-card">
                            <dt>Em andamento <span class="stat-icon" aria-hidden="true">◷</span></dt>
                            <dd data-stat="active"><?= $counts["active"] ?></dd>
                            <dd class="stat-description">Um passo de cada vez</dd>
                        </div>
                        <div class="stat-card">
                            <dt>Concluídas <span class="stat-icon" aria-hidden="true">✓</span></dt>
                            <dd data-stat="completed"><?= $counts["completed"] ?></dd>
                            <dd class="stat-description">Cada conquista conta</dd>
                        </div>
                        <div class="stat-card">
                            <dt>Em atraso <span class="stat-icon" aria-hidden="true">⚑</span></dt>
                            <dd data-stat="overdue"><?= $counts["overdue"] ?></dd>
                            <dd class="stat-description">Precisam de atenção</dd>
                        </div>
                    </dl>
                </section>

                <section id="tarefas" class="panel tasks-panel" aria-labelledby="tarefas-titulo">
                    <header class="section-heading">
                        <div>
                            <h2 id="tarefas-titulo">Minhas tarefas <span class="count-badge"><?= count($tasks) ?></span></h2>
                            <p>Pequenas ações. Novas possibilidades.</p>
                        </div>
                        <span class="muted">Visualização em lista</span>
                    </header>

                    <p id="temporary-note" class="temporary-note" role="status"><?= e($notice) ?></p>
                    <!-- Os filtros atuam nas linhas renderizadas, sem alterar a consulta ou os indicadores. -->
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

                    <!-- O parcial gera as linhas; data-user distingue o estado dos filtros por usuário. -->
                    <ul id="task-list" class="task-list" aria-label="Tarefas cadastradas" data-user="<?= $userId ?>" <?= !$tasks ? "hidden" : "" ?>><?php require dirname(__DIR__) . "/app/task-list.php"; ?></ul>
                    <p id="no-results" class="no-results" hidden>Nenhuma tarefa corresponde aos filtros escolhidos.</p>
                    <p id="task-feedback" class="sr-only" role="status"></p>
                    <div class="empty-state" <?= $tasks ? "hidden" : "" ?>>
                        <span class="empty-icon" aria-hidden="true">✓</span>
                        <h3>Seu próximo passo começa aqui.</h3>
                        <p>Uma ideia, uma entrega ou algo que você não quer esquecer.<br>Abra espaço para sua primeira
                            tarefa.</p>
                        <button class="button button-secondary" type="button" data-open-task aria-haspopup="dialog"
                            aria-controls="nova-tarefa">＋ Preparar minha primeira tarefa</button>
                    </div>
                    <footer class="panel-footer"><span id="task-count"><?= count($tasks) ?> tarefa(s) cadastrada(s)</span><span>Seu ritmo.
                            Suas conquistas.</span></footer>
                </section>

                <!-- O mesmo formulário é utilizado para criação e edição. -->
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
                        <p id="preview-note" class="preview-note"><?= e($error ?: 'Preencha os dados para salvar a tarefa na sua conta.') ?></p>
                        <!-- data-old repõe dados após erro; action e id distinguem criação de edição no servidor. -->
                        <form method="post" aria-label="Nova tarefa" aria-describedby="preview-note" data-old="<?= e(json_encode($old, JSON_INVALID_UTF8_SUBSTITUTE)) ?>">
                            <?= csrf_field() ?><input type="hidden" name="action" value="create"><input type="hidden" name="id" value="">
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
                                <p class="muted">Salvamento na sua conta.</p>
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
