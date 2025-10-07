// Validação do formulário de cadastro
function validaFormulario() {
    const nomeUsuario = document.getElementById('nome').value;
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const checkbox = document.getElementById('termos');

    if (!/^[a-zA-Z\u00C0-\u00FF\s]{8,40}$/.test(nomeUsuario)) {
        alert('Nome inválido. Deve conter entre 8 e 40 caracteres.');
        return false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert('Email inválido.');
        return false;
    }

    if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(senha)) {
        alert('Senha inválida. Deve conter pelo menos 8 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial.');
        return false;
    }

    if (!checkbox.checked) {
        alert('Você precisa aceitar os termos e condições.');
        return false;
    }

    alert('Cadastro realizado com sucesso!');
    return true;
}

// Login com Google
function initGoogleLogin() {
    google.accounts.id.initialize({
        client_id: "SEU_CLIENT_ID",
        callback: handleGoogleLogin
    });

    google.accounts.id.prompt(); // Mostra a UI do Google
}

function handleGoogleLogin(response) {
    console.log("Token ID do Google:", response.credential);
    // Envie o token para o servidor para validação
    alert("Login com Google realizado com sucesso!");
}

// Login com Facebook
window.fbAsyncInit = function () {
    FB.init({
        appId: 'SEU_APP_ID',
        cookie: true,
        xfbml: true,
        version: 'v16.0',
    });
};

function loginWithFacebook() {
    FB.login(function (response) {
        if (response.authResponse) {
            console.log("Token de Acesso do Facebook:", response.authResponse.accessToken);
            alert("Login com Facebook realizado com sucesso!");
        } else {
            alert("Login com Facebook falhou.");
        }
    }, { scope: 'email' });
}

// Login com Apple
function loginWithApple() {
    AppleID.auth.init({
        clientId: 'SEU_CLIENT_ID',
        scope: 'name email',
        redirectURI: 'URL_DO_SEU_BACKEND',
    });

    AppleID.auth.signIn().then((response) => {
        console.log("Dados da Apple:", response);
        alert("Login com Apple realizado com sucesso!");
    }).catch((error) => {
        alert("Login com Apple falhou.");
    });
}
