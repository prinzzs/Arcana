# Verificação da versão estática

Execute `npm install`, `npx playwright install chromium` e `npm test`.

O teste abre todas as páginas HTML e verifica recursos, links locais e erros de JavaScript.
Também verifica perfis, campanhas, fichas, edição pelo mestre, escudo, persistência,
dados D4–D100 e largura da central no celular. Não utiliza PHP ou banco de dados.

Para usar Chromium já instalado, defina `CHROMIUM_EXECUTABLE_PATH`.
