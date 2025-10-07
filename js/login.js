function validaLogin() {
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;

    // Validação de e-mail
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showModal('Por favor, insira um e-mail válido.', 'erro');
        return false;
    }

    // Validação de senha (mínimo 8 caracteres)
    if (senha.length < 8) {
        showModal('A senha deve ter pelo menos 8 caracteres.', 'erro');
        return false;
    }

    return true; // Permite enviar o formulário
}
