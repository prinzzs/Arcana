# Aplicação PHP

Todos os endpoints e a conexão compartilhada com o banco ficam nesta pasta.
Configure `DB_HOST`, `DB_USER`, `DB_PASSWORD` e `DB_NAME` no ambiente do servidor
PHP. Os padrões são `localhost`, `root`, senha vazia e `UserDB`.

O usuário do banco precisa ter permissão para criar o banco configurado e as
tabelas `usuarios` e `administradores`, que são verificadas em cada requisição.

Para criar o primeiro administrador, configure também `ADMIN_SETUP_KEY` com uma
chave forte e informe-a no formulário de cadastro. Depois do primeiro cadastro,
o formulário público é desativado.
