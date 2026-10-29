// Identidade e luminosidade são preferências independentes.
(() => {
    const system = matchMedia("(prefers-color-scheme: dark)");
    let mode = "system";
    let palette = "purple";

    function readPreferences() {
        try {
            const saved = localStorage.getItem("arcana-theme");
            const savedPalette = localStorage.getItem("arcana-palette");
            mode = ["light", "dark", "system"].includes(saved) ? saved : saved === "red" ? "dark" : "system";
            palette = savedPalette === "red" || (!savedPalette && saved === "red") ? "red" : "purple";
        } catch (error) {}
    }
    function apply() {
        const resolved = mode === "system" ? (system.matches ? "dark" : "light") : mode;
        const root = document.documentElement;
        root.dataset.theme = palette === "red" ? (resolved === "light" ? "red-light" : "red") : resolved;
        root.dataset.mode = resolved;
        root.dataset.palette = palette;
        root.dataset.themePreference = mode;
        document.querySelectorAll("[data-choice]").forEach(button =>
            button.setAttribute("aria-pressed", String(button.dataset.choice === mode)));
        document.querySelectorAll("[data-palette-choice]").forEach(button =>
            button.setAttribute("aria-pressed", String(button.dataset.paletteChoice === palette)));
    }
    function save() {
        try {
            localStorage.setItem("arcana-theme", mode);
            localStorage.setItem("arcana-palette", palette);
        } catch (error) {}
        apply();
    }
    readPreferences();
    apply();

    document.addEventListener("DOMContentLoaded", () => {
        const widget = document.createElement("div");
        widget.className = "theme-float";
        widget.innerHTML = '<button id="lightModeToggle" type="button" aria-label="Escolher aparência" aria-expanded="false" aria-controls="themePanel">◐</button><div id="themePanel" class="theme-panel" hidden><p class="theme-heading">Identidade</p><div id="palette-options" role="group" aria-label="Identidade visual"></div><p class="theme-heading">Luminosidade</p><div id="mode-options" role="group" aria-label="Luminosidade"></div></div>';
        document.body.append(widget);
        const toggle = widget.querySelector("button");
        const panel = widget.querySelector(".theme-panel");
        const close = () => { panel.hidden = true; toggle.setAttribute("aria-expanded", "false"); };
        function options(id, items, attribute, choose) {
            for (const [value, label] of items) {
                const button = document.createElement("button");
                button.type = "button";
                button.textContent = label;
                button.dataset[attribute] = value;
                button.onclick = () => { choose(value); save(); };
                widget.querySelector(id).append(button);
            }
        }
        options("#palette-options", [["purple", "✦ Arcana · roxo"], ["red", "◈ Ritual · vermelho"]], "paletteChoice", value => { palette = value; });
        options("#mode-options", [["light", "☀ Claro"], ["dark", "☾ Escuro"], ["system", "◐ Sistema"]], "choice", value => { mode = value; });
        toggle.onclick = () => {
            panel.hidden = !panel.hidden;
            toggle.setAttribute("aria-expanded", String(!panel.hidden));
            if (!panel.hidden) panel.querySelector("button").focus();
        };
        document.addEventListener("click", event => { if (!widget.contains(event.target)) close(); });
        widget.addEventListener("keydown", event => {
            if (event.key === "Escape") { close(); toggle.focus(); }
        });
        apply();
    });
    system.addEventListener("change", () => { if (mode === "system") apply(); });
    window.addEventListener("storage", event => {
        if (["arcana-theme", "arcana-palette", null].includes(event.key)) { readPreferences(); apply(); }
    });
})();

