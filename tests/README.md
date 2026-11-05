# Verificação da versão estática

Execute `npm install`, `npx playwright install chromium` e `npm test`.

O teste abre todas as páginas HTML e verifica recursos, links locais e erros de JavaScript.
Confere a posição do botão Voltar, o menu móvel, o foco por teclado e a ausência de rolagem horizontal
em 320, 390 e 1440 px. Os dois caminhos da inicial são percorridos, incluindo retorno ao criar
um perfil e criação de campanha própria sem alterar as campanhas existentes.
Também verifica modos claro/escuro, formulário do mapa,
criação de campanha com base Arcana, retorno após cadastro, preparação, limite de jogadores,
guia público, valores iniciais das fichas, preservação de fichas e campanhas antigas, convites locais,
navegação mestre/jogador, cena revelada, fichas, diário, escudo, rolagem privada e persistência.
As telas preenchidas são verificadas em 390 e 1440 px nos dois temas, mantendo Nevan.
Textos de campanha são exibidos como texto, sem interpretar HTML.
Não utiliza PHP ou banco de dados. A separação de papéis é apenas local.

Para usar Chromium já instalado, defina `CHROMIUM_EXECUTABLE_PATH`.
Para salvar capturas de tela para revisão visual, defina `ARCANA_SCREENSHOTS` com a pasta de destino.
