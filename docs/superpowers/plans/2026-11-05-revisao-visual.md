# Revisão visual e acessibilidade

Objetivo: manter cores e recursos atuais, aprimorando navegação, formulários e leitura apenas com HTML e CSS.

Decisão: o usuário dispensou propostas visuais e confirmou a preservação do JavaScript existente.

- [x] Aplicar acabamento compartilhado após os estilos existentes, sem bibliotecas novas no produto.
- [x] Incluir painel nativo details/summary com ajustes locais de leitura, contraste e links.
- [x] Organizar seleção de personagens e atalhos laterais; empilhar no celular.
- [x] Neutralizar exemplos de campanha, mantendo Arcana opcional.
- [x] Incorporar mapa estático da Fatec Praia Grande e conferir endereço em fonte oficial.
- [x] Executar verificação de navegação, formulários, temas, persistência e larguras móveis.

Limites: dados e troca de fichas continuam no JavaScript existente. Preferências CSS não persistem entre páginas. Não há novos controles de adição dinâmica.

Verificação: suíte ui.cjs aprovada no Chrome instalado; accessibility-css.cjs aprovado com JavaScript desativado em 320, 390 e 1440 px. Capturas da inicial e das fichas revisadas.
