window.addEventListener("DOMContentLoaded", async () => {
  try {
    let response = await fetch("../../../app/controlador.php?acao=estaLogado");

    let redirecionamento_url = "../../../home-interna/index.html";

    let dados = await response.json();

    if (dados.status === "logado") {
      window.location.href = redirecionamento_url;
      return;
    }
  } catch (error) {}
});

const loginForm = document.querySelector("form");

loginForm.addEventListener("submit", async function (e) {
  e.preventDefault();

  let formData = new FormData(loginForm);

  let response = await fetch("../../../app/controlador.php?acao=loginUsuario", {
    method: "POST",
    body: formData,
  });

  if (!response.ok) {
    alert("Erro no login");
    return;
  }

  let dados = await response.json();

  if (dados["status"] !== "success") {
    alert(dados["message"]);
    return;
  }

  alert("Login realizado com sucesso");

  window.location.href = "../../../home-interna/index.html";
});
