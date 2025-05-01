// import { verifica_esta_logado } from "../assets/utils.js";

document.addEventListener("DOMContentLoaded", async () => {
  try {
    let response = await fetch("../app/controlador.php?acao=estaLogado");

    let dados = await response.json();

    console.log(dados);

    alert("chegou");

    if (!response.ok) {
      alert("Usuário deslogado");
      console.log(dados);
      // window.location.href = "./login-usuarios/index.html";
      return;
    }

    // let dados = await response.json();

    if (dados.status !== "logado") {
      // window.location.href = "./login-usuarios/index.html";
      console.log(dados);
      return;
    }

    console.log("logado");
  } catch (error) {
    // window.location.href = "../login-usuarios/index.html";
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

  btnEnviar.onclick = () => {
    if (!btnEnviar.disabled) formDados.submit();
    else alert("Complete os dados restantes");
  };
};
