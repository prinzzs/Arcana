const status = document.getElementById("status");
const adminPage = location.pathname.includes("Adm");
const profileForm = document.querySelector("[data-profile]");

if (profileForm) {
    profileForm.addEventListener("submit", event => {
        event.preventDefault();
        const values = new FormData(profileForm);
        const name = values.get("name").trim();
        const email = values.get("email").trim().toLowerCase();
        if (!name) return;
        if (localData.profiles.some(profile => profile.email === email)) {
            status.textContent = "Já existe um perfil com esse e-mail neste navegador.";
            return;
        }
        const profile = { id: localId(), name, email };
        localData.profiles.push(profile);
        localData.active = profile.id;
        if (saveLocal()) location.href = adminPage ? "painelAdm.html" : "campanhas.html";
    });
}

const profiles = document.getElementById("profiles");
if (profiles) {
    if (!localData.profiles.length) status.textContent = "Crie seu primeiro perfil local.";
    localData.profiles.forEach(profile => {
        profiles.append(makeButton(profile.name, () => {
            localData.active = profile.id;
            if (saveLocal()) location.href = adminPage ? "painelAdm.html" : "campanhas.html";
        }));
    });
}

const userId = new URLSearchParams(location.search).get("id");
const editing = document.getElementById("edit-user") || document.getElementById("edit-profile");
if (editing) {
    const profile = editing.id === "edit-user"
        ? localData.profiles.find(item => item.id === userId)
        : activeProfile();
    if (!profile) {
        status.textContent = "Selecione um perfil na página de entrada.";
        editing.hidden = true;
    } else {
        editing.elements.name.value = profile.name;
        editing.elements.email.value = profile.email;
        editing.addEventListener("submit", event => {
            event.preventDefault();
            const email = editing.elements.email.value.trim().toLowerCase();
            if (localData.profiles.some(item => item.id !== profile.id && item.email === email)) {
                status.textContent = "Este e-mail já está em outro perfil.";
                return;
            }
            profile.name = editing.elements.name.value.trim();
            profile.email = email;
            if (saveLocal()) status.textContent = "Perfil salvo neste navegador.";
        });
    }
}

const logout = document.getElementById("logout");
if (logout) logout.onclick = () => {
    localData.active = "";
    if (saveLocal()) location.href = "login.html";
};

const users = document.getElementById("users");
function renderUsers() {
    const search = document.getElementById("search").value.toLowerCase();
    users.replaceChildren();
    localData.profiles.filter(profile =>
        (profile.name + " " + profile.email).toLowerCase().includes(search)
    ).forEach(profile => {
        const row = document.createElement("div");
        row.className = "profile-row";
        const text = document.createElement("span");
        text.textContent = profile.name + " · " + profile.email;
        row.append(text);
        for (const [label, page] of [["Editar", "editarUsuario"], ["Excluir", "excluirUsuario"]]) {
            const link = document.createElement("a");
            link.textContent = label;
            link.href = page + ".html?id=" + encodeURIComponent(profile.id);
            row.append(link);
        }
        users.append(row);
    });
    if (!users.children.length) users.textContent = "Nenhum perfil encontrado.";
}
if (users) {
    document.getElementById("search").addEventListener("input", renderUsers);
    renderUsers();
}

const deleteButton = document.getElementById("delete-user");
if (deleteButton) {
    const profile = localData.profiles.find(item => item.id === userId);
    document.getElementById("delete-name").textContent = profile
        ? "Excluir " + profile.name + " deste navegador?"
        : "Perfil não encontrado.";
    deleteButton.disabled = !profile;
    deleteButton.onclick = () => {
        localData.profiles = localData.profiles.filter(item => item.id !== userId);
        if (localData.active === userId) localData.active = "";
        if (saveLocal()) location.href = "painelAdm.html";
    };
}
