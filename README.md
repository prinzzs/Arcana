# Arcana

Site em HTML, CSS e JavaScript puro. Abra `index.html` no navegador.
Para manter os dados locais no mesmo endereço, use a mesma pasta e o mesmo navegador.

- Todas as páginas usam a extensão .html.
- A inicial apresenta dois caminhos: conhecer Arcana ou criar uma campanha com sistema próprio, sem preencher a história de Arcana.
- O menu principal é igual em todas as telas. O botão Voltar fica abaixo dele; dentro de uma campanha, retorna à visão do mestre ou do jogador mantendo a campanha selecionada.
- No celular, o botão Menu abre a navegação. Escape fecha o menu e devolve o foco; os links continuam disponíveis sem JavaScript.
- A central fica em campanhas.html. Mestre e jogador entram em mestre.html e jogador.html.
- Conheça o RPG e suas ferramentas em arcana.html. O botão “Criar campanha de Arcana” preenche uma base do cenário.
- Crie a identidade da campanha em criar-campanha.html e complete a preparação em preparar-campanha.html.
- O mestre define sinopse, sistema, cenário, objetivo, regras, combinados, agenda, vagas e valores iniciais das fichas. Os jogadores consultam tudo em guia-campanha.html.
- Vida, nível, modelo de atributos e inventário da preparação são aplicados apenas a novas fichas. Fichas e campanhas antigas são preservadas.
- Fichas, escudo, diário e sessão têm páginas próprias: personagens.html, escudo.html, diario.html e sessao.html.
- O mestre pode revelar uma cena, publicar o diário e editar as fichas do grupo. O jogador acessa suas fichas e os materiais revelados.
- O botão do cabeçalho alterna apenas entre claro e escuro.
- Estilos ficam em css/ e comportamentos ficam em js/.
- Não precisa instalar dependências para usar o site.
- Campanhas, fichas, diário e perfis ficam no localStorage.
- Login e administração são demonstrações locais, sem senha ou segurança de servidor.
- Convites só encontram campanhas deste navegador. Não há sincronização entre dispositivos.
- Trocar de perfil permite experimentar o mestre e os jogadores.
- O dado guarda apenas o último resultado público por campanha. Rolagens privadas não são salvas.
- Limpar os dados do navegador apaga os dados locais.
- A agenda guarda o horário combinado pela mesa, sem conversão automática de fuso.
- PHP e banco de dados poderão ser adicionados depois. O código antigo permanece no histórico do Git.

## Verificação

Para os testes: `npm install`, `npx playwright install chromium` e `npm test`.

## Versão simplificada

A aparência agora usa apenas claro e escuro. O botão fica no cabeçalho.
O rodapé da inicial contém o mapa da Fatec Praia Grande, na Praça 19 de Janeiro, 144,
Boqueirão, Praia Grande/SP, incorporado diretamente em HTML. O mapa depende de internet.
O botão redondo Aa, no canto inferior direito, abre opções de texto ampliado,
alto contraste e destaque de links. Usa HTML e CSS, sem JavaScript novo;
as opções valem apenas na página atual e não são salvas entre páginas.
O acabamento compartilhado está em css/refinamento.css. Na tela de personagens,
a lateral reúne seleção de fichas e atalhos para as seções do formulário;
no celular ela aparece acima do editor. Atributos e itens são adicionados por linha,
nos campos existentes, e salvos pelo comportamento já disponível.
Os campos de campanha podem ser preenchidos sem perfil; salvar continua exigindo
um perfil local. Os CSS estão divididos em blocos com uma propriedade por linha.

Verificação adicional das opções nativas: `node tests/accessibility-css.cjs`.
Use `CHROMIUM_EXECUTABLE_PATH` para indicar um Chrome instalado, se necessário.
