# Verificação da interface

```sh
npm install
npx playwright install chromium
npm test
```

O teste usa as páginas reais como templates e substitui os trechos PHP por dados de exemplo. Intercepta as requisições no navegador, sem iniciar um servidor e sem acessar um banco de dados. Isso valida a apresentação, os caminhos dos assets e as interações de interface; não valida login, cadastro, permissões ou persistência no MySQL.

São verificadas as 17 páginas em 320, 390 e 1440 px, nos dois temas, incluindo ausência de rolagem horizontal, labels dos campos e erros de JavaScript/recursos. Também são cobertos contraste dos tokens, foco de teclado, troca de tema, recarga, navegação, preferência do sistema, sincronização entre abas, migração da preferência antiga, storage bloqueado, página sem botão, fallback sem JavaScript, filtros, seções das entidades, lore e dados.

Para salvar capturas, defina `ARCANA_SCREENSHOTS=test-results`. Para usar uma instalação de Chromium existente, defina `CHROMIUM_EXECUTABLE_PATH` com o caminho do executável.

As cores ficam em `css/global.css`: superfícies e texto usam tokens semânticos; roxo de marca e botões continuam com as cores originais. A preferência fica em `localStorage['arcana-theme']` (`light`, `dark` ou `system`). O script de tema deve continuar antes dos estilos no `<head>` para evitar o flash do tema incorreto.
