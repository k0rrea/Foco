// Executado antes do CSS para aplicar a aparência antes da primeira pintura.
(() => {
    const storageKey = 'foco-theme';
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');
    const validModes = ['light', 'dark', 'system'];
    let mode = 'system';

    try {
        const saved = localStorage.getItem(storageKey);
        if (validModes.includes(saved)) mode = saved;
    } catch { /* A aparência continua disponível sem armazenamento. */ }

    function applyTheme() {
        document.documentElement.dataset.theme = mode === 'system'
            ? (systemTheme.matches ? 'dark' : 'light')
            : mode;
    }

    applyTheme();
    systemTheme.addEventListener('change', () => {
        if (mode === 'system') applyTheme();
    });

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
