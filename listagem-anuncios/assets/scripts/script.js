document.addEventListener("DOMContentLoaded", () => {
  var paginaAtual = 1;
  var carregando = false;

  async function verificaSeEstaLogado() {
    try {
      let response = await fetch("../../../app/controlador.php?acao=estaLogado");

      let redirecionamento_url = "../../../login-usuarios/index.html";

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
  }

  async function carregarAnuncios() {
    if (carregando) return;

    carregando = true;

    try {
      let response = await fetch(
        `../../app/controlador.php?acao=listarAnuncios&pagina={paginaAtual}`
      );

      if (!response.ok) {
        alert("Erro ao carregar os anúncios");
        return;
      }

      let anuncios = await response.json();

      // if (anuncios.length === 0) return;

      console.log(anuncios);

      paginaAtual++;
      // exibirAnuncios(anuncios);
    } catch (error) {
      alert("Erro ao carregar os anúncios");
    } finally {
      carregando = false;
    }
  }

  window.addEventListener("scroll", () => {
    if (
      window.innerHeight + window.screenY >=
      document.body.offsetHeight - 100
    ) {
      carregarAnuncios();
    }
  });

  verificaSeEstaLogado();
  // carregarAnuncios();
});

function menu_hamburguer() {
  const nav_list = document.getElementById("nav-list");
  const body = document.body;
  const header = document.querySelector("header");

  if (nav_list.classList.contains("show")) {
    nav_list.classList.remove("show");
    body.classList.remove("no-scroll");
    header.classList.remove("menu-aberto");
  } else {
    nav_list.classList.add("show");
    body.classList.add("no-scroll");
    header.classList.add("menu-aberto");
  }
}

const menuHamburguer = document.getElementById("menu-hamburguer");
menuHamburguer.onclick = menu_hamburguer;
