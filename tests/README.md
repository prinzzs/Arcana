# Verificação da versão estática

Execute `npm install`, `npx playwright install chromium` e `npm test`.

O teste abre todas as páginas HTML e verifica recursos, links locais e erros de JavaScript.
Também verifica identidades roxa/vermelha com luminosidade clara/escura, migração de preferências,
criação de campanha, convites locais, navegação mestre/jogador, cena revelada, fichas, diário,
escudo, rolagem privada, persistência e telas preenchidas em 390 e 1440 px.
Não utiliza PHP ou banco de dados. A separação de papéis é apenas local.

Para usar Chromium já instalado, defina `CHROMIUM_EXECUTABLE_PATH`.
