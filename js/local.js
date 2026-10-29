// Um único arquivo de dados para a demonstração, sem guardar senhas.
const localKey = "arcana-local-v1";
let localData;
try {
    localData = JSON.parse(localStorage.getItem(localKey));
} catch (error) {
    localData = null;
}
if (!localData || !Array.isArray(localData.profiles) || !Array.isArray(localData.campaigns)) {
    localData = { profiles: [], campaigns: [], active: "" };
}

function localId() {
    return Date.now().toString(36) + Math.random().toString(36).slice(2, 9);
}

function saveLocal() {
    try {
        localStorage.setItem(localKey, JSON.stringify(localData));
        return true;
    } catch (error) {
        document.getElementById("status").textContent =
            "Não foi possível salvar. Verifique o espaço e a permissão de armazenamento do navegador.";
        return false;
    }
}

function activeProfile() {
    return localData.profiles.find(profile => profile.id === localData.active);
}

function makeButton(text, action) {
    const button = document.createElement("button");
    button.type = "button";
    button.textContent = text;
    button.addEventListener("click", action);
    return button;
}
