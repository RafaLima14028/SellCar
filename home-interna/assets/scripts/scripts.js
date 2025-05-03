document.addEventListener("DOMContentLoaded", async () => {
  var paginaAtual = 1;
  var carregando = false;

  const listaAnuncios = document.getElementById("lista-anuncios");

  async function verificaSeEstaLogado() {
    try {
      let response = await fetch(
        "../../../app/controlador.php?acao=estaLogado"
      );

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

  function montaCard(img0, marca, modelo, ano, cidade, valor) {
    let sectionContainerCard = document.createElement("section");
    let divcontainerImg = document.createElement("div");
    let divDetalhesCarro = document.createElement("div");
    let divDetalhesCarroEsquerdo = document.createElement("div");
    let divDetalhesCarroDireito = document.createElement("div");
    let divDetalhesCarroDireitoCidade = document.createElement("div");
    let divDetalhesCarroValor = document.createElement("div");

    sectionContainerCard.className = "container-card";
    divcontainerImg.className = "container-img";
    divDetalhesCarro.className = "detalhes-carro";
    divDetalhesCarroEsquerdo.className = "detalhes-carro-esquerda";
    divDetalhesCarroDireito.className = "detalhes-carro-direto";
    divDetalhesCarroDireitoCidade.className = "detalhes-carro-direto-cidade";
    divDetalhesCarroValor.className = "detalhes-carro-valor";

    let caminhoFoto = "../../app/" + img0;
    let img = document.createElement("img");
    img.src = caminhoFoto;
    img.alt = "Imagem do carro";

    let aRedireciona = document.createElement("a");
    aRedireciona.href = "../detalhes-anuncio/index.html";

    divcontainerImg.appendChild(img);
    aRedireciona.appendChild(divcontainerImg);
    sectionContainerCard.appendChild(aRedireciona);

    let pMarca = document.createElement("p");
    pMarca.innerText = "Marca: " + marca;
    let pModelo = document.createElement("p");
    pModelo.innerText = "Modelo: " + modelo;
    let pAno = document.createElement("p");
    pAno.innerText = "Ano: " + ano;

    divDetalhesCarroEsquerdo.appendChild(pMarca);
    divDetalhesCarroEsquerdo.appendChild(pModelo);
    divDetalhesCarroEsquerdo.appendChild(pAno);
    divDetalhesCarro.appendChild(divDetalhesCarroEsquerdo);

    let pCidade = document.createElement("p");
    pCidade.innerText = "Em " + cidade;

    divDetalhesCarroDireitoCidade.appendChild(pCidade);
    divDetalhesCarroDireito.appendChild(divDetalhesCarroDireitoCidade);
    divDetalhesCarro.appendChild(divDetalhesCarroDireito);
    sectionContainerCard.appendChild(divDetalhesCarro);

    let pValor = document.createElement("p");
    pValor.innerHTML = "<strong>R$ " + valor + "</strong>";

    divDetalhesCarroValor.appendChild(pValor);
    sectionContainerCard.appendChild(divDetalhesCarroValor);

    listaAnuncios.appendChild(sectionContainerCard);
  }

  async function exibirAnuncios(anuncios) {
    anuncios.forEach((anuncio) => {
      let marca = anuncio.marca;
      let modelo = anuncio.modelo;
      let ano = anuncio.ano;
      let cidade = anuncio.cidade;
      let valor = anuncio.valor;
      let img0 = anuncio.fotos[0];

      montaCard(img0, marca, modelo, ano, cidade, valor);
    });
  }

  async function carregarAnuncios() {
    if (carregando) return;

    carregando = true;

    try {
      let response = await fetch(
        "../../app/controlador.php?acao=listarAnuncios&pagina=" + paginaAtual
      );

      if (!response.ok) {
        alert("Erro ao carregar os anúncios");
        return;
      }

      let anuncios = await response.json();

      if (anuncios.length === 0) return;

      paginaAtual++;

      exibirAnuncios(anuncios);
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

  await verificaSeEstaLogado();
  carregarAnuncios();
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
menuHamburguer.onclick = menuHamburguer;

const a_logoff = document.getElementById("a-logoff");
a_logoff.onclick = async () => {
  let response = await fetch("../../../app/controlador.php?acao=logoutUsuario");

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
  window.location.href = "../../../index.html";
};
