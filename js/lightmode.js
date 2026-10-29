// Aplica o tema antes de desenhar a página e cria um único seletor flutuante.
(() => {
    const key = "arcana-theme";
    const themes = ["light", "dark", "red", "system"];
    const system = window.matchMedia("(prefers-color-scheme: dark)");
    let preference = "system";
    try {
        const saved = localStorage.getItem(key);
        if (themes.includes(saved)) preference = saved;
    } catch (error) { /* O seletor continua funcionando sem armazenamento. */ }

    function apply() {
        const theme = preference === "system" ? (system.matches ? "dark" : "light") : preference;
        document.documentElement.dataset.theme = theme;
        document.documentElement.dataset.themePreference = preference;
        document.querySelectorAll("[data-choice]").forEach(button => {
            button.setAttribute("aria-pressed", String(button.dataset.choice === preference));
        });
    }
    apply();

    document.addEventListener("DOMContentLoaded", () => {
        const widget = document.createElement("div");
        widget.className = "theme-float";
        widget.innerHTML = '<button id="lightModeToggle" type="button" aria-label="Escolher aparência" aria-expanded="false" aria-controls="themePanel">◐</button><div id="themePanel" class="theme-panel" role="group" aria-label="Aparência" hidden></div>';
        document.body.append(widget);
        const toggle = widget.querySelector("button");
        const panel = widget.querySelector(".theme-panel");
        function close() {
            panel.hidden = true;
            toggle.setAttribute("aria-expanded", "false");
        }
        for (const [value, label] of [["light", "☀ Claro"], ["dark", "☾ Escuro"], ["red", "◆ Vermelho"], ["system", "◐ Sistema"]]) {
            const button = document.createElement("button");
            button.type = "button";
            button.dataset.choice = value;
            button.textContent = label;
            button.onclick = () => {
                preference = value;
                try { localStorage.setItem(key, value); } catch (error) {}
                apply();
                close();
                toggle.focus();
            };
            panel.append(button);
        }
        toggle.onclick = () => {
            panel.hidden = !panel.hidden;
            toggle.setAttribute("aria-expanded", String(!panel.hidden));
            if (!panel.hidden) panel.querySelector("button").focus();
        };
        document.addEventListener("click", event => {
            if (!widget.contains(event.target)) close();
        });
        widget.addEventListener("keydown", event => {
            if (event.key === "Escape") { close(); toggle.focus(); }
        });
        apply();
    });
    system.addEventListener("change", () => { if (preference === "system") apply(); });
    window.addEventListener("storage", event => {
        if (event.key === key || event.key === null) {
            preference = themes.includes(event.newValue) ? event.newValue : "system";
            apply();
        }
    });
})();

