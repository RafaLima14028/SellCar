export async function verifica_esta_logado() {
  try {
    let response = await fetch("../app/controlador.php?acao=estaLogado", {
      mode: "no-cors",
    });

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

    if (dados.status === "deslogado") {
      // window.location.href = "./login-usuarios/index.html";
      console.log(dados);
      return;
    }

    console.log("logado");
  } catch (error) {
    // window.location.href = "../login-usuarios/index.html";
  }
}

async function logoff() {
  let response = await fetch("./app/controlador.php?acao=logoutUsuario");

  if (!response.ok) {
    alert("Erro no momento do logoff");
    return;
  }

  let dados = await response.json();

  if (dados.status !== "success") {
    alert("Erro no momento do logoff");
    return;
  }

  alert("Logoff realizado com sucesso!");
}
