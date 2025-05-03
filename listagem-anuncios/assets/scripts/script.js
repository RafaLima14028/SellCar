document.addEventListener("DOMContentLoaded", async () => {
  await verificaSeEstaLogado();
  carregarAnuncios();
});

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

async function exibirAnuncios(anuncios) {
  anuncios.forEach((anuncio) => {
    let marca = anuncio.marca;
    let modelo = anuncio.modelo;
    let ano = anuncio.ano;
    let cidade = anuncio.cidade;
    let valor = anuncio.valor;
    let img0 = anuncio.fotos[0];
    let anuncioId = anuncio.anuncioId;

    montaCard(img0, marca, modelo, ano, cidade, valor, anuncioId);
  });
}

async function carregarAnuncios() {
  try {
    let response = await fetch(
      "../../../app/controladorPrivado.php?acao=buscaCarrosDoUsuario"
    );

    if (!response.ok) {
      alert("Erro ao carregar os anúncios");
      return;
    }

    let anuncios = await response.json();

    if (anuncios.length === 0) return;

    console.log(anuncios);

    exibirAnuncios(anuncios);
  } catch (error) {
    alert("Erro ao carregar os anúncios");
  }
}

async function excluirAnuncio(anuncioId) {
  let response = await fetch(
    "../../../app/controladorPrivado.php?acao=excluirCarro&idAnuncio=" +
      anuncioId
  );

  if (!response.ok) {
    alert("Problemas ao exlcuir o veículo");
    return;
  }
}

const listaAnuncios = document.querySelector("main");

function montaCard(img0, marca, modelo, ano, cidade, valor, anuncioId) {
  let sectionContainerCard = document.createElement("section");
  let divcontainerImg = document.createElement("div");
  let divDetalhesCarro = document.createElement("div");
  let divDetalhesCarroEsquerdo = document.createElement("div");
  let divDetalhesCarroDireito = document.createElement("div");
  let divDetalhesCarroDireitoIcones = document.createElement("div");
  let divDetalhesCarroDireitoCidade = document.createElement("div");
  let divDetalhesCarroValor = document.createElement("div");

  sectionContainerCard.className = "container-card";
  divcontainerImg.className = "container-img";
  divDetalhesCarro.className = "detalhes-carro";
  divDetalhesCarroEsquerdo.className = "detalhes-carro-esquerda";
  divDetalhesCarroDireito.className = "detalhes-carro-direto";
  divDetalhesCarroDireitoIcones.className = "detalhes-carro-direto-icones";
  divDetalhesCarroDireitoCidade.className = "detalhes-carro-direto-cidade";
  divDetalhesCarroValor.className = "detalhes-carro-valor";

  let caminhoFoto = "../../app/uploads/anuncios/" + anuncioId + "/" + img0;
  let img = document.createElement("img");
  img.src = caminhoFoto;
  img.alt = "Imagem do carro";

  let aImagem = document.createElement("a");
  aImagem.href = "../../../detalhes-anuncio/index.html?id=" + anuncioId;

  divcontainerImg.appendChild(img);
  aImagem.appendChild(divcontainerImg);
  sectionContainerCard.appendChild(aImagem);

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

  let aBtnInteresse = document.createElement("a");
  aBtnInteresse.className = "a-btn-interesse";
  aBtnInteresse.href = "../../../listagem-interesses/index.html";
  let spanIconeInteresse = document.createElement("span");
  spanIconeInteresse.className = "material-icons mode_comment";
  spanIconeInteresse.innerText = "mode_comment";

  aBtnInteresse.appendChild(spanIconeInteresse);

  let aBtnDelete = document.createElement("a");
  aBtnDelete.className = "a-btn-delete";
  aBtnDelete.href = "#";
  let spanIconeDelte = document.createElement("span");
  spanIconeDelte.className = "material-icons mode_comment";
  spanIconeDelte.innerText = "delete";

  aBtnDelete.onclick = () => {
    excluirAnuncio(anuncioId);
    window.location.reload();
  };

  aBtnDelete.appendChild(spanIconeDelte);

  divDetalhesCarroDireitoIcones.appendChild(aBtnInteresse);
  divDetalhesCarroDireitoIcones.appendChild(aBtnDelete);
  divDetalhesCarroDireito.appendChild(divDetalhesCarroDireitoIcones);

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
