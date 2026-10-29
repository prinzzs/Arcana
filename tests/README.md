# Verificação da versão estática

Execute `npm install`, `npx playwright install chromium` e `npm test`.

O teste abre todas as páginas HTML e verifica recursos, links locais e erros de JavaScript.
Também verifica identidades roxa/vermelha com luminosidade clara/escura, migração de preferências,
criação de campanha com base Arcana, retorno após cadastro, preparação, limite de jogadores,
guia público, valores iniciais das fichas, preservação de fichas e campanhas antigas, convites locais,
navegação mestre/jogador, cena revelada, fichas, diário, escudo, rolagem privada e persistência.
As telas preenchidas são verificadas em 390 e 1440 px nas quatro combinações de tema, mantendo Nevan.
Textos de campanha são exibidos como texto, sem interpretar HTML.
Não utiliza PHP ou banco de dados. A separação de papéis é apenas local.

Para usar Chromium já instalado, defina `CHROMIUM_EXECUTABLE_PATH`.
