# Arcana

Site em HTML, CSS e JavaScript puro. Abra `index.html` no navegador.
Para manter os dados locais no mesmo endereço, use a mesma pasta e o mesmo navegador.

- Todas as páginas usam a extensão .html.
- Estilos ficam em css/ e comportamentos ficam em js/.
- Não precisa instalar dependências para usar o site.
- Campanhas, fichas, diário e perfis ficam no localStorage.
- Login e administração são demonstrações locais, sem senha ou segurança de servidor.
- Convites só encontram campanhas deste navegador. Não há sincronização entre dispositivos.
- Trocar de perfil permite experimentar o mestre e os jogadores.
- O dado guarda apenas o último resultado público por campanha. Rolagens privadas não são salvas.
- Limpar os dados do navegador apaga os dados locais.
- PHP e banco de dados poderão ser adicionados depois. O código antigo permanece no histórico do Git.

## Verificação

Para os testes: `npm install`, `npx playwright install chromium` e `npm test`.
