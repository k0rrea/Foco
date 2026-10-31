// Executado antes do CSS para aplicar a aparência antes da primeira pintura.
(() => {
    // Persiste somente o modo; a aparência efetiva de Sistema é calculada em tempo real.
    const storageKey = 'foco-theme';
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');
    const validModes = ['light', 'dark', 'system'];
    let mode = 'system';

    // Valores ausentes ou desconhecidos mantêm Sistema como padrão.
    try {
        const saved = localStorage.getItem(storageKey);
        if (validModes.includes(saved)) mode = saved;
    } catch { /* A aparência continua disponível sem armazenamento. */ }

    /** Aplica a paleta na raiz; o CSS atualiza componentes, controles nativos e logotipos. */
    function applyTheme() {
        document.documentElement.dataset.theme = mode === 'system'
            ? (systemTheme.matches ? 'dark' : 'light')
            : mode;
    }

    applyTheme();
    // Mudanças do sistema só afetam quem não escolheu explicitamente Claro ou Escuro.
    systemTheme.addEventListener('change', () => {
        if (mode === 'system') applyTheme();
    });

    // Aguarda o HTML para conectar o seletor; login e cadastro aplicam o tema mesmo sem esse controle.
    document.addEventListener('DOMContentLoaded', () => {
        const selector = document.querySelector('#theme-mode');
        if (!selector) return;
        selector.value = mode;
        selector.addEventListener('change', () => {
            if (!validModes.includes(selector.value)) return;
            mode = selector.value;
            applyTheme();
            try {
                localStorage.setItem(storageKey, mode);
            } catch { /* A escolha vale nesta página mesmo sem persistência. */ }
        });
    });
})();
