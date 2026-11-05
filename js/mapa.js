// Troque este valor para definir a localização inicial do site.
const localizacaoInicial = "Brasil";
const formularioMapa = document.getElementById("map-form");
const enderecoMapa = document.getElementById("map-address");

function mostrarMapa(local) {
    const endereco = encodeURIComponent(local);
    document.getElementById("location-map").src = "https://www.google.com/maps?q=" + endereco + "&output=embed";
    document.getElementById("map-link").href = "https://www.google.com/maps?q=" + endereco;
    document.getElementById("map-status").textContent = "Local selecionado: " + local + ". O mapa precisa de internet.";
}
enderecoMapa.value = localizacaoInicial;
mostrarMapa(localizacaoInicial);
formularioMapa.addEventListener("submit", function(evento) {
    evento.preventDefault();
    const local = enderecoMapa.value.trim();
    if (!local) {
        enderecoMapa.setCustomValidity("Digite um endereço ou cidade.");
        enderecoMapa.reportValidity();
        return;
    }
    mostrarMapa(local);
});
enderecoMapa.addEventListener("input", function() { enderecoMapa.setCustomValidity(""); });
