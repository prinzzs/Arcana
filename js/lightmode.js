// Apenas dois temas. Preferências antigas são convertidas para claro ou escuro.
(function() {
    let tema = "light";
    try {
        const salvo = localStorage.getItem("arcana-theme");
        if (salvo === "dark" || salvo === "red") tema = "dark";
    } catch (erro) {}
    function aplicarTema() {
        document.documentElement.dataset.theme = tema;
        document.documentElement.dataset.mode = tema;
        document.documentElement.removeAttribute("data-palette");
        const botao = document.getElementById("lightModeToggle");
        if (botao) {
            botao.textContent = tema === "light" ? "Modo escuro" : "Modo claro";
            botao.setAttribute("aria-label", "Ativar " + botao.textContent.toLowerCase());
        }
    }
    aplicarTema();
    document.addEventListener("DOMContentLoaded", function() {
        const botao = document.createElement("button");
        botao.id = "lightModeToggle";
        botao.type = "button";
        document.querySelector(".site-toolbar").append(botao);
        botao.addEventListener("click", function() {
            tema = tema === "light" ? "dark" : "light";
            try { localStorage.setItem("arcana-theme", tema); } catch (erro) {}
            aplicarTema();
        });
        aplicarTema();
    });
})();
