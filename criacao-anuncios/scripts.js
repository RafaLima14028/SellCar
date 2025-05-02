document.addEventListener("DOMContentLoaded", async () => {
  try {
    let response = await fetch("../app/controlador.php?acao=estaLogado");

    let redirecionamento_url = "../login-usuarios/index.html";

    if (!response.ok) {
      alert("Usuário deslogado");
      window.location.href = redirecionamento_url;
      return;
    }

    let dados = await response.json();

    if (dados.status !== "logado") {
      window.location.href = redirecionamento_url;
      return;
    }
  } catch (error) {
    window.location.href = redirecionamento_url;
  }
});

window.onload = () => {
  const btnEnviar = document.getElementById("btn-enviar");
  const inputImgsCarros = document.getElementById("imgs-carro");
  const formDados = document.querySelector("form");

  inputImgsCarros.onchange = () => {
    const files = inputImgsCarros.files;

    if (files && files.length < 3) {
      alert("Por favor, selecione pelo menos 3 imagens");
      btnEnviar.disabled = true;
    } else {
      btnEnviar.disabled = false;
    }
  };

  btnEnviar.onclick = async () => {
    if (!btnEnviar.disabled) {
      let formData = new FormData(formDados);

      let response = await fetch(
        "../app/controlador.php?acao=criacaoAnuncios",
        {
          method: "POST",
          body: formData,
        }
      );

      let dados = await response.json();

      if (!response.ok) {
        alert(dados.message);
        return;
      }

      if (dados.status === "success") alert(dados.message);
    } else alert("Complete os dados restantes");
  };
};
