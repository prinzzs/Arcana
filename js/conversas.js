let story = [
    {
        text: ["Você desperta em uma caverna úmida e escura.", "Sua única fonte de luz é uma tocha fraca.", "Diante de você, há três caminhos."],
        options: ["Seguir pelo túnel da esquerda", "Seguir pelo túnel central", "Seguir pelo túnel da direita"]
    },
    {
        text: ["Você encontra um baú trancado.", "Parece que há uma fechadura complexa."],
        options: ["Tentar abrir com força", "Procurar uma chave", "Rolar um dado para tentar abrir"]
    }
];

let currentStep = 0;

function loadStep() {
    let chat = document.getElementById("chat");
    chat.innerHTML = "";
    let messages = story[currentStep].text;
    messages.forEach((msgText, index) => {
        setTimeout(() => {
            let msg = document.createElement("div");
            msg.classList.add("message", "received");
            msg.textContent = msgText;
            chat.appendChild(msg);
            chat.scrollTop = chat.scrollHeight;
        }, index * 1000);
    });
    
    setTimeout(() => {
        let optionsDiv = document.getElementById("options");
        optionsDiv.innerHTML = "";
        story[currentStep].options.forEach((option, index) => {
            let btn = document.createElement("button");
            btn.textContent = option;
            btn.onclick = () => {
                if (option.includes("Rolar o dado")) {
                    rollDice();
                } else {
                    nextStep();
                }
            };
            optionsDiv.appendChild(btn);
        });
    }, messages.length * 1000);
}

function rollDice() {
    let diceRoll = Math.floor(Math.random() * 20) + 1;
    let chat = document.getElementById("chat");
    let msg = document.createElement("div");
    msg.classList.add("message", "received");
    msg.textContent = `Você rolou um ${diceRoll}.`;
    chat.appendChild(msg);
    chat.scrollTop = chat.scrollHeight;
    setTimeout(() => nextStep(), 2000);
}

function nextStep() {
    if (currentStep < story.length - 1) {
        currentStep++;
        loadStep();
    }
}

function previousStep() {
    if (currentStep > 0) {
        currentStep--;
        loadStep();
    }
}

loadStep();