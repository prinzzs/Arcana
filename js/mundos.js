// Páginas da campanha. Os dados continuam na mesma chave da versão anterior.
(() => {
    const page = document.body.dataset.worldPage;
    const byId = id => document.getElementById(id);
    const status = byId("status");
    const profile = activeProfile();
    const campaignId = new URLSearchParams(location.search).get("campanha");
    const campaign = localData.campaigns.find(item => item.id === campaignId);
    const master = !!(campaign && profile && campaign.owner === profile.id);
    const url = file => file + "?campanha=" + encodeURIComponent(campaign.id);
    const home = () => master ? "mestre.html" : "jogador.html";
    const say = text => { status.textContent = text; };
    byId("profile-label").textContent = profile ? profile.name : "Nenhum perfil selecionado";

    function save(message) {
        if (!saveLocal()) return false;
        say(message);
        return true;
    }

    if (page === "hub") {
        const select = byId("activeProfile");
        localData.profiles.forEach(item => {
            const option = document.createElement("option");
            option.value = item.id;
            option.textContent = item.name;
            select.append(option);
        });
        select.value = localData.active;
        select.onchange = () => {
            localData.active = select.value;
            if (saveLocal()) location.reload();
        };
        const list = byId("campaigns");
        localData.campaigns.filter(item => item.members.includes(localData.active)).forEach(item => {
            const link = document.createElement("a");
            link.className = "campaign-card";
            link.href = (item.owner === localData.active ? "mestre.html" : "jogador.html") +
                "?campanha=" + encodeURIComponent(item.id);
            const role = document.createElement("span");
            role.className = "eyebrow";
            role.textContent = item.owner === localData.active ? "MESTRE / BASTIDORES" : "JOGADOR / SUA JORNADA";
            const title = document.createElement("h3");
            title.textContent = item.name;
            const detail = document.createElement("p");
            detail.textContent = item.characters.length + " fichas · Abrir campanha ↗";
            link.append(role, title, detail);
            list.append(link);
        });
        if (!list.children.length) list.textContent = "Nenhuma história por aqui ainda. Crie sua mesa ou use um convite local.";
        if (!profile) say("Escolha um perfil ou crie o seu para começar.");
        byId("create").onsubmit = event => {
            event.preventDefault();
            if (!profile) return say("Crie um perfil primeiro.");
            const name = event.target.elements.name.value.trim();
            if (!name) return say("Dê um nome à sua campanha.");
            const item = {
                id: localId(), name, owner: profile.id, code: localId().toUpperCase(),
                members: [profile.id], characters: [], journal: "", shield: ""
            };
            localData.campaigns.push(item);
            if (saveLocal()) location.href = "mestre.html?campanha=" + encodeURIComponent(item.id);
        };
        byId("join").onsubmit = event => {
            event.preventDefault();
            if (!profile) return say("Crie um perfil primeiro.");
            const code = event.target.elements.code.value.trim().toUpperCase();
            const item = localData.campaigns.find(c => c.code === code);
            if (!item) return say("Este convite não foi encontrado neste navegador.");
            if (!item.members.includes(profile.id)) item.members.push(profile.id);
            if (saveLocal()) location.href = (item.owner === profile.id ? "mestre.html" : "jogador.html") +
                "?campanha=" + encodeURIComponent(item.id);
        };
        return;
    }

    if (!profile || !campaign || !campaign.members.includes(profile.id)) {
        byId("world-content").hidden = true;
        say("Escolha uma campanha do seu perfil na central para acessar esta página.");
        return;
    }
    if (["master", "shield"].includes(page) && !master) {
        byId("world-content").hidden = true;
        say("Esta área pertence ao mestre. Abra sua jornada pelo menu da campanha.");
    }
    const navigation = [
        [home(), master ? "Visão do mestre" : "Minha jornada"],
        ["sessao.html", "Sessão"], ["personagens.html", master ? "Personagens" : "Minhas fichas"],
        ["diario.html", "Diário"]
    ];
    if (master) navigation.push(["escudo.html", "Escudo"]);
    navigation.forEach(([file, label]) => {
        const link = document.createElement("a");
        link.href = url(file);
        link.textContent = label;
        if (location.pathname.endsWith(file)) link.setAttribute("aria-current", "page");
        byId("world-nav").append(link);
    });
    if (["master", "shield"].includes(page) && !master) return;
    if (page === "player" && master) {
        say("Você está visualizando a jornada como mestre. Para experimentar um jogador, troque o perfil na central.");
    }
    document.querySelectorAll("[data-destination]").forEach(link => {
        link.href = url(link.dataset.destination);
    });
    if (byId("campaign-title")) byId("campaign-title").textContent = campaign.name;
    if (byId("scene-title")) byId("scene-title").textContent = campaign.sceneTitle || "Aguardando o próximo capítulo";
    if (byId("scene-text")) byId("scene-text").textContent = campaign.sceneText || "O mestre ainda não revelou a cena desta sessão.";

    if (page === "master") {
        const stats = [
            [campaign.members.length, "participantes"],
            [campaign.characters.length, "personagens"],
            [campaign.sceneTitle ? "Pronta" : "Em preparo", "cena atual"]
        ];
        stats.forEach(([value, label]) => {
            const card = document.createElement("div");
            const number = document.createElement("strong");
            number.textContent = value;
            const caption = document.createElement("span");
            caption.textContent = label;
            card.append(number, caption);
            byId("campaign-stats").append(card);
        });
        byId("invite").textContent = campaign.code;
        byId("copy-invite").onclick = async () => {
            try {
                await navigator.clipboard.writeText(campaign.code);
                say("Código copiado. Ele funciona entre perfis deste navegador.");
            } catch (error) {
                say("Selecione o código acima para copiar manualmente.");
            }
        };
        const form = byId("scene-form");
        form.elements.title.value = campaign.sceneTitle || "";
        form.elements.text.value = campaign.sceneText || "";
        form.onsubmit = event => {
            event.preventDefault();
            campaign.sceneTitle = form.elements.title.value.trim();
            campaign.sceneText = form.elements.text.value.trim();
            save("Cena revelada. Ela já está disponível na jornada do jogador e na sessão.");
        };
    }

    if (page === "player") {
        const list = byId("my-characters");
        campaign.characters.filter(c => c.owner === profile.id).forEach(character => {
            const link = document.createElement("a");
            link.href = url("personagens.html") + "&ficha=" + encodeURIComponent(character.id);
            link.className = "character-summary";
            link.textContent = character.name + " · " + character.hp + " PV · nível " + character.level;
            list.append(link);
        });
        if (!list.children.length) list.textContent = "Sua primeira ficha está esperando por você.";
        byId("journal-preview").textContent = campaign.journal
            ? campaign.journal.slice(0, 350) + (campaign.journal.length > 350 ? "…" : "")
            : "Ainda não há registros revelados pelo mestre.";
    }

    if (page === "shield") {
        const form = byId("shield-form");
        for (const key of ["shield", "encounters", "reminders"]) form.elements[key].value = campaign[key] || "";
        form.onsubmit = event => {
            event.preventDefault();
            for (const key of ["shield", "encounters", "reminders"]) campaign[key] = form.elements[key].value;
            save("Escudo salvo. Estas notas ficam fora das páginas do jogador.");
        };
    }

    if (page === "journal") {
        const form = byId("journal-form");
        if (master) {
            form.elements.journal.value = campaign.journal;
            form.onsubmit = event => {
                event.preventDefault();
                campaign.journal = form.elements.journal.value;
                save("Diário publicado para os participantes desta campanha local.");
            };
        } else {
            form.hidden = true;
            byId("journal-reading").hidden = false;
            byId("journal-reading").textContent = campaign.journal || "O mestre ainda não publicou o diário.";
        }
    }

    if (page === "characters") {
        const form = byId("character-form");
        byId("sheet-description").textContent = master
            ? "Como mestre, você acompanha todas as fichas e pode criar personagens para o grupo."
            : "Aqui ficam as suas fichas. O mestre também pode acompanhá-las e editá-las.";
        const owners = master
            ? localData.profiles.filter(p => campaign.members.includes(p.id))
            : [profile];
        owners.forEach(owner => {
            const option = document.createElement("option");
            option.value = owner.id;
            option.textContent = owner.name;
            form.elements.owner.append(option);
        });
        function editable(character) { return master || character.owner === profile.id; }
        function edit(character) {
            if (!editable(character)) return;
            for (const key of ["id", "name", "owner", "hp", "level", "body", "inventory", "bio"]) {
                form.elements[key].value = character[key] ?? "";
            }
            byId("editor-title").textContent = character.name;
        }
        function listSheets() {
            byId("sheets").replaceChildren();
            campaign.characters.filter(editable).forEach(character => {
                byId("sheets").append(makeButton(character.name + " · " + character.hp + " PV", () => edit(character)));
            });
            if (!byId("sheets").children.length) byId("sheets").textContent = "Nenhuma ficha criada ainda.";
        }
        function reset() {
            form.reset();
            form.elements.id.value = "";
            form.elements.owner.value = profile.id;
            byId("editor-title").textContent = "Uma nova história";
        }
        byId("new-character").onclick = () => { reset(); form.elements.name.focus(); };
        form.onsubmit = event => {
            event.preventDefault();
            const id = form.elements.id.value;
            let character = campaign.characters.find(c => c.id === id);
            if (id && (!character || !editable(character))) return say("Esta ficha não pertence ao seu perfil.");
            const name = form.elements.name.value.trim();
            if (!name) return say("Informe o nome do personagem.");
            if (!character) {
                character = { id: localId(), owner: profile.id };
                campaign.characters.push(character);
            }
            const owner = form.elements.owner.value;
            if (master && owners.some(p => p.id === owner)) character.owner = owner;
            character.name = name;
            character.hp = Number(form.elements.hp.value);
            character.level = Number(form.elements.level.value);
            for (const key of ["body", "inventory", "bio"]) character[key] = form.elements[key].value;
            if (save("Ficha salva.")) { listSheets(); edit(character); }
        };
        listSheets();
        reset();
        const requested = campaign.characters.find(c => c.id === new URLSearchParams(location.search).get("ficha"));
        if (requested && editable(requested)) edit(requested);
    }

    if (page === "session") {
        campaign.members.forEach(id => {
            const member = localData.profiles.find(p => p.id === id);
            const item = document.createElement("span");
            item.textContent = (member ? member.name : "Perfil removido") + (id === campaign.owner ? " · Mestre" : "");
            byId("participants").append(item);
        });
        byId("private-roll-label").hidden = !master;
        byId("last-roll").textContent = campaign.lastRoll || "O resultado público mais recente aparecerá aqui.";
        arcanaDice(document.querySelector("[data-dice]"), (sides, count) => {
            const result = rollDice(sides, count);
            if (!master || !byId("private-roll").checked) {
                campaign.lastRoll = profile.name + " · D" + sides + ": " + result.values.join(" + ") + " = " + result.total;
                if (saveLocal()) byId("last-roll").textContent = campaign.lastRoll;
            }
            return result;
        });
    }
})();
