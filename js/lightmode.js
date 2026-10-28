/* Carregado no <head>, antes do CSS: aplica o tema antes da primeira pintura. */
(() => {
    'use strict';

    const root = document.documentElement;
    const storageKey = 'arcana-theme';
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');
    const validPreferences = ['light', 'dark', 'system'];
    let preference = 'system';

    try {
        const saved = localStorage.getItem(storageKey);
        if (validPreferences.includes(saved)) {
            preference = saved;
        } else if (localStorage.getItem('lightMode') === 'enabled') {
            // Na versão anterior, "lightMode=enabled" aplicava o tema escuro.
            preference = 'dark';
            localStorage.setItem(storageKey, preference);
            localStorage.removeItem('lightMode');
        }
    } catch (_) {
        // Storage bloqueado: a troca continua válida nesta página.
    }

    const icons = {
        light: '<svg class="theme-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5m11 11L19 19M5 19l1.5-1.5m11-11L19 5"/></svg>',
        dark: '<svg class="theme-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20.5 14A8.5 8.5 0 0 1 10 3.5 8.5 8.5 0 1 0 20.5 14Z"/></svg>'
    };

    function applyTheme() {
        const theme = preference === 'system' ? (systemTheme.matches ? 'dark' : 'light') : preference;
        root.dataset.theme = theme;
        root.dataset.themePreference = preference;
        const toggle = document.getElementById('lightModeToggle');
        if (toggle) {
            const next = theme === 'dark' ? 'light' : 'dark';
            const label = next === 'light' ? 'Modo claro' : 'Modo escuro';
            toggle.innerHTML = icons[next] + '<span>' + label + '</span>';
            toggle.setAttribute('aria-label', 'Ativar ' + label.toLowerCase());
            toggle.setAttribute('aria-pressed', String(theme === 'dark'));
            toggle.title = 'Tema atual: ' + (theme === 'dark' ? 'escuro' : 'claro');
        }
        document.querySelectorAll('[data-theme-system]').forEach(button => {
            button.setAttribute('aria-pressed', String(preference === 'system'));
            button.title = preference === 'system' ? 'Acompanhando o tema do dispositivo' : 'Usar tema do dispositivo';
        });
    }

    function chooseTheme(value) {
        preference = value;
        try {
            localStorage.setItem(storageKey, value);
            localStorage.removeItem('lightMode');
        } catch (_) { /* O tema continua funcionando sem persistência. */ }
        applyTheme();
    }

    applyTheme();

    function bindControls() {
        const toggle = document.getElementById('lightModeToggle');
        if (toggle) toggle.addEventListener('click', () => chooseTheme(root.dataset.theme === 'dark' ? 'light' : 'dark'));
        document.querySelectorAll('[data-theme-system]').forEach(button => {
            button.addEventListener('click', () => chooseTheme('system'));
        });
        applyTheme();
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bindControls, { once: true });
    else bindControls();

    const followSystem = () => { if (preference === 'system') applyTheme(); };
    if (systemTheme.addEventListener) systemTheme.addEventListener('change', followSystem);
    else systemTheme.addListener(followSystem);

    window.addEventListener('storage', event => {
        if (event.key === storageKey || event.key === null) {
            preference = validPreferences.includes(event.newValue) ? event.newValue : 'system';
            applyTheme();
        }
    });
})();

