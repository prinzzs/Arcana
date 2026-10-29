// Campanhas locais. Nenhum pedido ao servidor é necessário.
const byId = id => document.getElementById(id);
let currentCampaign = null;
const profileSelect = byId("activeProfile");

function message(text) {
    byId("status").textContent = text;
}

function isMaster() {
    return currentCampaign && currentCampaign.owner === localData.active;
}

function listCampaigns() {
    profileSelect.replaceChildren();
    localData.profiles.forEach(profile => {
        const option = document.createElement("option");
        option.value = profile.id;
        option.textContent = profile.name;
        profileSelect.append(option);
    });
    profileSelect.value = localData.active;
    const list = byId("campaigns");
    list.replaceChildren();
    localData.campaigns.filter(campaign =>
        campaign.members.includes(localData.active)
    ).forEach(campaign => {
        list.append(makeButton(campaign.name, () => openCampaign(campaign)));
    });
    if (!activeProfile()) message("Crie ou escolha um perfil na página de entrada.");
}

function renderSheets() {
    const list = byId("sheets");
    list.replaceChildren();
    currentCampaign.characters.filter(character =>
        isMaster() || character.owner === localData.active
    ).forEach(character => {
        list.append(makeButton(character.name + " · " + character.hp + " PV", () => {
            const form = byId("character");
            for (const key of ["id", "name", "hp", "level", "body"]) {
                form.elements[key].value = character[key];
            }
        }));
    });
    if (!list.children.length) list.textContent = "Nenhuma ficha para este perfil. Crie a primeira abaixo.";
}

function openCampaign(campaign) {
    currentCampaign = campaign;
    byId("room").hidden = false;
    byId("title").textContent = campaign.name;
    byId("invite").textContent = "Código local: " + campaign.code;
    byId("shieldTab").hidden = !isMaster();
    byId("shieldForm").elements.body.value = isMaster() ? campaign.shield : "";
    byId("journalForm").elements.body.value = campaign.journal;
    byId("journalForm").elements.body.readOnly = !isMaster();
    byId("journalForm").querySelector("button").hidden = !isMaster();
    secretLabel.hidden = !isMaster();
    byId("secretRoll").checked = false;
    byId("character").reset();
    byId("character").elements.id.value = "";
    showTab("session");
    renderSheets();
    renderRoll();
    message(isMaster() ? "Você é o mestre desta campanha local." : "Você está usando um perfil de jogador.");
}

function showTab(id) {
    document.querySelectorAll(".tab").forEach(tab => tab.hidden = tab.id !== id);
    document.querySelectorAll("[data-tab]").forEach(button => {
        button.setAttribute("aria-pressed", String(button.dataset.tab === id));
    });
}

profileSelect.addEventListener("change", () => {
    localData.active = profileSelect.value;
    if (!saveLocal()) return;
    currentCampaign = null;
    byId("room").hidden = true;
    byId("shieldForm").reset();
    byId("character").reset();
    listCampaigns();
});

byId("create").addEventListener("submit", event => {
    event.preventDefault();
    if (!activeProfile()) return message("Escolha um perfil primeiro.");
    const name = event.target.elements.name.value.trim();
    if (!name) return;
    const campaign = {
        id: localId(),
        name,
        owner: localData.active,
        code: localId().toUpperCase(),
        members: [localData.active],
        characters: [],
        journal: "",
        shield: ""
    };
    localData.campaigns.push(campaign);
    if (!saveLocal()) return;
    event.target.reset();
    listCampaigns();
    openCampaign(campaign);
});

byId("join").addEventListener("submit", event => {
    event.preventDefault();
    if (!activeProfile()) return message("Escolha um perfil primeiro.");
    const code = event.target.elements.code.value.trim().toUpperCase();
    const campaign = localData.campaigns.find(item => item.code === code);
    if (!campaign) return message("Convite não encontrado neste navegador.");
    if (!campaign.members.includes(localData.active)) campaign.members.push(localData.active);
    if (!saveLocal()) return;
    listCampaigns();
    openCampaign(campaign);
});

byId("character").addEventListener("submit", event => {
    event.preventDefault();
    if (!currentCampaign) return;
    const form = event.target;
    const id = form.elements.id.value;
    let character = currentCampaign.characters.find(item => item.id === id);
    if (id && (!character || (!isMaster() && character.owner !== localData.active))) {
        return message("Abra uma ficha do perfil atual.");
    }
    if (!character) {
        character = { id: localId(), owner: localData.active };
        currentCampaign.characters.push(character);
    }
    character.name = form.elements.name.value.trim();
    character.hp = Number(form.elements.hp.value);
    character.level = Number(form.elements.level.value);
    character.body = form.elements.body.value;
    if (!saveLocal()) return;
    form.reset();
    form.elements.id.value = "";
    renderSheets();
    message("Ficha salva neste navegador.");
});

byId("newCharacter").onclick = () => {
    byId("character").reset();
    byId("character").elements.id.value = "";
};

for (const [formId, field] of [["journalForm", "journal"], ["shieldForm", "shield"]]) {
    byId(formId).addEventListener("submit", event => {
        event.preventDefault();
        if (!isMaster()) return message("Use o perfil do mestre para editar.");
        currentCampaign[field] = event.target.elements.body.value;
        if (saveLocal()) message("Anotações salvas neste navegador.");
    });
}

document.querySelectorAll("[data-tab]").forEach(button => {
    button.onclick = () => showTab(button.dataset.tab);
});

function renderRoll() {
    byId("rolls").textContent = currentCampaign.lastRoll || "Nenhuma rolagem pública nesta campanha.";
}

const dice = document.querySelector("[data-dice]");
arcanaDice(dice, (sides, count) => {
    const result = rollDice(sides, count);
    if (currentCampaign && !byId("secretRoll").checked) {
        currentCampaign.lastRoll = activeProfile().name + " · D" + sides + ": " +
            result.values.join(" + ") + " = " + result.total;
        saveLocal();
        renderRoll();
    }
    return result;
});
const secretLabel = document.createElement("label");
secretLabel.innerHTML = '<input id="secretRoll" type="checkbox"> Rolagem privada (não salva)';
secretLabel.hidden = true;
dice.append(secretLabel);
listCampaigns();
