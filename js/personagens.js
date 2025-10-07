// Dados fictícios dos personagens
const personagens = [
    {
        nome: 'Lyra Nyx',
        imagem: 'img/nyx.png',
        ataque: 120,
        defesa: 85,
        cura: 75,
        destreza: 70,
        nivel: 1
    },
    {
        nome: 'Alder Vex',
        imagem: 'img/vex.png',
        ataque: 160,
        defesa: 60,
        cura: 59,
        destreza: 90,
        nivel: 1
    },
    
];

// Função para renderizar os personagens
function renderizarPersonagens() {
    const charList = document.querySelector('.char-list');
    charList.innerHTML = ''; // Limpar lista anterior

    personagens.forEach((personagem, index) => {
        const charItem = document.createElement('div');
        charItem.classList.add('char-item');

        // Adiciona imagem e informações do personagem
        charItem.innerHTML = `
            <div class="char-img">
                <img src="${personagem.imagem}" alt="${personagem.nome}">
            </div>
            <div class="char-info">
                <p><strong>${personagem.nome}</strong></p>
                <p>Nível: ${personagem.nivel}</p>
            </div>
        `;

        // Adicionar evento de clique para mostrar os detalhes
        charItem.addEventListener('click', () => mostrarDetalhes(index));

        // Adicionar personagem à lista
        charList.appendChild(charItem);
    });
}

// Função para mostrar os detalhes do personagem
function mostrarDetalhes(index) {
    const personagem = personagens[index];
    document.querySelector('.nomeRobo').textContent = personagem.nome;
    document.querySelector('#ataqueValor').textContent = personagem.ataque;
    document.querySelector('#defesaValor').textContent = personagem.defesa;
    document.querySelector('#curaValor').textContent = personagem.cura;
    document.querySelector('#destrezaValor').textContent = personagem.destreza;
    document.querySelector('#nivelNumero').textContent = personagem.nivel;

    // Atualizar barras de progresso
    document.querySelector('#ataque progress').value = personagem.ataque;
    document.querySelector('#defesa progress').value = personagem.defesa;
    document.querySelector('#cura progress').value = personagem.cura;
    document.querySelector('#destreza progress').value = personagem.destreza;

    // Alterar imagem de fundo
    alterarImagemDeFundo(index);
}

// Função para alterar a imagem de fundo
function alterarImagemDeFundo(index) {
    const backgroundImageDiv = document.querySelector('.background-image');
    const personagem = personagens[index];

    // Define a nova imagem de fundo
    if (personagem.imagem) {
        backgroundImageDiv.style.backgroundImage = `url('${personagem.imagem}')`;
    }
}

// Inicializar a página
document.addEventListener('DOMContentLoaded', () => {
    renderizarPersonagens();

    // Define o personagem inicial e imagem de fundo
    mostrarDetalhes(0);
});
