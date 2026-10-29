const story = [
    {
        text: "Você desperta em uma caverna. Uma tocha ilumina três caminhos.",
        options: ["Túnel da esquerda", "Túnel central", "Túnel da direita"]
    },
    {
        text: "Você encontra um baú trancado. Como vai abri-lo?",
        options: ["Tentar abrir com força", "Procurar uma chave", "Rolar um dado"]
    }
];
let currentStep = 0;

function loadStep() {
    const chat = document.getElementById("chat");
    const options = document.getElementById("options");
    chat.textContent = story[currentStep].text;
    options.replaceChildren();
    story[currentStep].options.forEach((text, index) => {
        const button = document.createElement("button");
        button.textContent = text;
        button.onclick = () => {
            if (currentStep === 0) {
                currentStep = 1;
                loadStep();
                chat.prepend("Você escolheu: " + text + ". ");
            } else {
                chat.textContent = index === 2
                    ? "Você rolou " + (Math.floor(Math.random() * 20) + 1) + ". O baú se abre e revela uma pista!"
                    : "Você encontra uma pista dentro do baú. A aventura continua…";
                options.replaceChildren();
                const restart = document.createElement("button");
                restart.textContent = "Recomeçar";
                restart.onclick = () => { currentStep = 0; loadStep(); };
                options.append(restart);
            }
        };
        options.append(button);
    });
}

function previousStep() {
    currentStep = Math.max(0, currentStep - 1);
    loadStep();
}
loadStep();
