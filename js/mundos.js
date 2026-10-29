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

    if (page === "create") {
        byId("world-nav").hidden = true;
        setupCampaignForm(null, profile);
        return;
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
            const data = campaignDetails(item);
            detail.textContent = data.system + " · " + item.characters.length + " fichas";
            const next = document.createElement("p");
            next.className = "campaign-next";
            next.textContent = "Próxima sessão · " + sessionLabel(data.nextSession);
            const cover = document.createElement("div");
            cover.className = "campaign-card-cover";
            cover.setAttribute("aria-hidden", "true");
            setCampaignCover(cover, data.cover);
            link.append(cover, role, title, detail, next);
            list.append(link);
        });
        if (!list.children.length) list.textContent = "Nenhuma história por aqui ainda. Crie sua mesa ou use um convite local.";
        if (!profile) say("Escolha um perfil ou crie o seu para começar.");
        byId("join").onsubmit = event => {
            event.preventDefault();
            if (!profile) return say("Crie um perfil primeiro.");
            const code = event.target.elements.code.value.trim().toUpperCase();
            const item = localData.campaigns.find(c => c.code === code);
            if (!item) return say("Este convite não foi encontrado neste navegador.");
            if (!item.members.includes(profile.id)) {
                const players = item.members.filter(id => id !== item.owner).length;
                if (players >= campaignDetails(item).maxPlayers) return say("Esta mesa atingiu o limite de jogadores definido pelo mestre.");
                item.members.push(profile.id);
            }
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
    if (["master", "shield", "prepare"].includes(page) && !master) {
        byId("world-content").hidden = true;
        say("Esta área pertence ao mestre. Abra sua jornada pelo menu da campanha.");
    }
    const navigation = [
        [home(), master ? "Visão do mestre" : "Minha jornada"],
        ["sessao.html", "Sessão"], ["personagens.html", master ? "Personagens" : "Minhas fichas"],
        ["diario.html", "Diário"], ["guia-campanha.html", "Guia da campanha"]
    ];
    if (master) navigation.push(["escudo.html", "Escudo"], ["preparar-campanha.html", "Preparação"]);
    navigation.forEach(([file, label]) => {
        const link = document.createElement("a");
        link.href = url(file);
        link.textContent = label;
        if (location.pathname.endsWith(file)) link.setAttribute("aria-current", "page");
        byId("world-nav").append(link);
    });
    const currentLink = byId("world-nav").querySelector('[aria-current="page"]');
    if (currentLink) byId("world-nav").scrollLeft = currentLink.offsetLeft - 12;
    if (["master", "shield", "prepare"].includes(page) && !master) return;
    if (page === "player" && master) {
        say("Você está visualizando a jornada como mestre. Para experimentar um jogador, troque o perfil na central.");
    }
    document.querySelectorAll("[data-destination]").forEach(link => {
        link.href = url(link.dataset.destination);
    });
    if (byId("campaign-title")) byId("campaign-title").textContent = campaign.name;
    if (byId("scene-title")) byId("scene-title").textContent = campaign.sceneTitle || "Aguardando o próximo capítulo";
    if (byId("scene-text")) byId("scene-text").textContent = campaign.sceneText || "O mestre ainda não revelou a cena desta sessão.";

    const details = campaignDetails(campaign);
    if (page === "prepare") {
        setupCampaignForm(campaign, profile);
        return;
    }
    if (byId("campaign-brief")) {
        byId("campaign-brief").textContent = details.synopsis || "O mestre ainda está preparando a apresentação da aventura.";
        for (const value of [details.system, "Próxima sessão · " + sessionLabel(details.nextSession), details.location]) {
            if (!value) continue;
            const item = document.createElement("span");
            item.textContent = value;
            byId("campaign-meta").append(item);
        }
    }
    if (page === "guide") {
        byId("guide-synopsis").textContent = details.synopsis || "A apresentação desta aventura ainda está sendo preparada.";
        setCampaignCover(byId("campaign-cover"), details.cover);
        const facts = [
            ["Sistema", details.system], ["Cenário", details.setting || "A definir"],
            ["Tom", details.tone || "A combinar"], ["Próxima sessão", sessionLabel(details.nextSession)],
            ["Local / plataforma", details.location || "A combinar"],
            ["Jogadores", campaign.members.filter(id => id !== campaign.owner).length + " de " + details.maxPlayers + " vagas"]
        ];
        facts.forEach(([label, value]) => {
            const item = document.createElement("div");
            const term = document.createElement("dt");
            const description = document.createElement("dd");
            term.textContent = label;
            description.textContent = value;
            item.append(term, description);
            byId("campaign-facts").append(item);
        });
        for (const key of ["objective", "rules", "safety"]) {
            byId("guide-" + key).textContent = details[key] || "O mestre ainda não registrou esta informação.";
        }
        byId("guide-starting").textContent = details.startingHp + " pontos de vida · Nível " + details.startingLevel + ". Valores iniciais definidos pela mesa para novas fichas.";
        byId("guide-template").textContent = details.sheetTemplate || "Preenchimento livre. Combine os atributos com o mestre.";
        byId("guide-inventory").textContent = details.startingInventory || "Nenhum equipamento inicial definido.";
        byId("edit-preparation").hidden = !master;
        byId("arcana-material").hidden = details.system.toLowerCase() !== "arcana";
    }

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
            if (label === "cena atual") number.id = "scene-state";
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
            if (save("Cena revelada. Ela já está disponível na jornada do jogador e na sessão.")) {
                byId("scene-state").textContent = campaign.sceneTitle ? "Pronta" : "Em preparo";
            }
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
        byId("sheet-guidance").textContent = details.system + " · Novas fichas começam com " +
            details.startingHp + " PV e nível " + details.startingLevel + ". Preencha os atributos conforme o guia da mesa.";
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
            form.elements.hp.value = details.startingHp;
            form.elements.level.value = details.startingLevel;
            form.elements.body.value = details.sheetTemplate;
            form.elements.inventory.value = details.startingInventory;
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
