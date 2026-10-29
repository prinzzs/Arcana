// Dados públicos da campanha. Os segredos continuam nos campos do escudo.
const campaignDefaults = {
    system: "Sistema da mesa", tone: "", setting: "", synopsis: "", cover: "sigil",
    objective: "", rules: "", safety: "", nextSession: "", location: "",
    maxPlayers: 6, startingHp: 20, startingLevel: 1, sheetTemplate: "", startingInventory: ""
};

const arcanaTemplate = {
    ...campaignDefaults,
    system: "Arcana", tone: "Investigação, tecnologia e horror sobrenatural",
    setting: "Arcana · Submundo, Nível das Ruas e Altíssimo", cover: "submundo",
    synopsis: "Há 50 anos, um experimento da Spectra Dynamics abriu uma fenda paranormal. O Blecaute trouxe as entidades Umbra e mudou a cidade. Entre túneis esquecidos, ruas em disputa e torres corporativas, a sua mesa decide o próximo capítulo de Arcana.",
    rules: "Consulte as mecânicas de Arcana na aba Sobre Arcana. Antes da sessão, combine com o grupo as regras, adaptações e opções de personagem. Registre aqui as decisões da sua mesa.",
    sheetTemplate: "Origem (humano, cyborg, Umbra ou androide):\nClasse (Combatente, Especialista ou Ocultista):\n\nForça:\nAgilidade:\nPercepção:\nVontade:\nPresença:\nVigor:\n\nHabilidades:"
};

function campaignDetails(campaign) {
    return { ...campaignDefaults, ...campaign.details };
}

// Uma data de sessão é o horário combinado pela mesa, sem converter o fuso.
function sessionLabel(value) {
    if (!/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(value || "")) return "A combinar";
    const [date, time] = value.split("T");
    const [year, month, day] = date.split("-");
    return day + "/" + month + "/" + year + " às " + time;
}

function setCampaignCover(element, cover) {
    // Só imagens do projeto podem ser usadas como capa.
    const allowed = ["submundo", "ruas", "altissimo"];
    element.dataset.cover = allowed.includes(cover) ? cover : "sigil";
}

function setupCampaignForm(campaign, profile) {
    const creating = !campaign;
    const form = document.getElementById("campaign-form");
    const status = document.getElementById("status");
    const feedback = document.getElementById("form-feedback");
    let details = creating ? { ...campaignDefaults, system: "" } : campaignDetails(campaign);

    function tell(message) {
        status.textContent = message;
        if (feedback) feedback.textContent = message;
    }
    function fill() {
        Object.keys(campaignDefaults).forEach(key => {
            if (form.elements[key]) form.elements[key].value = details[key];
        });
    }
    function useArcana() {
        details = { ...arcanaTemplate };
        if (!form.elements.name.value.trim()) form.elements.name.value = "Ecos de Arcana";
        fill();
        document.getElementById("template-status").textContent = "Base de Arcana aplicada. Personalize a sinopse e o cenário como quiser.";
    }

    fill();
    if (creating) {
        document.getElementById("use-arcana").onclick = useArcana;
        if (new URLSearchParams(location.search).get("modelo") === "arcana") useArcana();
        if (!profile) {
            document.getElementById("profile-gate").hidden = false;
            document.getElementById("campaign-fields").disabled = true;
            document.getElementById("save-campaign").disabled = true;
            const next = encodeURIComponent("criar-campanha.html" + (location.search.includes("modelo=arcana") ? "?modelo=arcana" : ""));
            document.getElementById("create-profile").href = "cadastro.html?next=" + next;
            document.getElementById("choose-profile").href = "login.html?next=" + next;
        }
    } else {
        form.elements.name.value = campaign.name;
        if (new URLSearchParams(location.search).get("nova")) tell("Campanha criada. Complete a preparação e salve para começar a jogar.");
    }

    form.onsubmit = event => {
        event.preventDefault();
        if (!profile || (!creating && campaign.owner !== profile.id)) return;
        const name = form.elements.name.value.trim();
        if (!name || !form.elements.system.value.trim()) {
            tell("Informe o nome da campanha e o sistema.");
            (!name ? form.elements.name : form.elements.system).focus();
            return;
        }
        const updated = { ...details };
        const numberFields = ["maxPlayers", "startingHp", "startingLevel"];
        for (const key of Object.keys(campaignDefaults)) {
            if (!form.elements[key]) continue;
            updated[key] = numberFields.includes(key) ? Number(form.elements[key].value) : form.elements[key].value.trim();
        }
        const players = creating ? 0 : campaign.members.filter(id => id !== campaign.owner).length;
        if (updated.maxPlayers < players) {
            tell("A campanha já tem " + players + " jogadores. O limite precisa incluir todos eles.");
            form.elements.maxPlayers.focus();
            return;
        }
        if (creating) {
            const item = {
                id: localId(), name, owner: profile.id, code: localId().toUpperCase(),
                members: [profile.id], characters: [], journal: "", shield: "", details: updated
            };
            localData.campaigns.push(item);
            if (saveLocal()) location.href = "preparar-campanha.html?campanha=" + encodeURIComponent(item.id) + "&nova=1";
            else localData.campaigns.pop();
        } else {
            const previous = { name: campaign.name, details: campaign.details };
            campaign.name = name;
            campaign.details = updated;
            if (saveLocal()) {
                details = updated;
                tell("Preparação salva. O guia dos jogadores e as novas fichas já usam estes dados.");
            } else {
                Object.assign(campaign, previous);
                if (feedback) feedback.textContent = status.textContent;
            }
        }
    };
}
