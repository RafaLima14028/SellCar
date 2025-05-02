var paginaAtual = 1;

var funcaoAtualDeAtualizacaoDoScroll = carregarAnuncios;

document.addEventListener("DOMContentLoaded", () => {
  funcaoAtualDeAtualizacaoDoScroll();
});

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
  }
}

window.addEventListener("scroll", () => {
  if (window.innerHeight + window.screenY >= document.body.offsetHeight - 100) {
    funcaoAtualDeAtualizacaoDoScroll();
  }
});

const listaAnuncios = document.getElementById("lista-anuncios");

function montaCard(img0, marca, modelo, ano, cidade, valor) {
  let divContainerCard = document.createElement("div");
  let divcontainerImg = document.createElement("div");
  let divDetalhesCarro = document.createElement("div");
  let divDetalhesCarroEsquerdo = document.createElement("div");
  let divDetalhesCarroDireito = document.createElement("div");
  let divDetalhesCarroDireitoCidade = document.createElement("div");
  let divDetalhesCarroValor = document.createElement("div");

  divContainerCard.className = "container-card";
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

  divcontainerImg.appendChild(img);
  divContainerCard.appendChild(divcontainerImg);

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
  divContainerCard.appendChild(divDetalhesCarro);

  let pValor = document.createElement("p");
  pValor.innerHTML = "<strong>R$ " + valor + "</strong>";

  divDetalhesCarroValor.appendChild(pValor);
  divContainerCard.appendChild(divDetalhesCarroValor);

  listaAnuncios.appendChild(divContainerCard);
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

async function carregaMarcasDistintas() {
  let marca = document.getElementById("marca");

  let response = await fetch("../../app/controlador.php?acao=marcasDistintas");

  let dados = await response.json();

  if (!response.ok) {
    alert(dados.message);
    return;
  }

  let option = document.createElement("option");
  option.value = "";
  option.textContent = "";

  marca.appendChild(option);

  dados.forEach((marcaItem) => {
    let option = document.createElement("option");
    option.value = marcaItem;
    option.textContent = marcaItem;

    marca.appendChild(option);
  });
}

async function carregaModelosDaMarcaDistintos() {
  let marca = document.getElementById("marca");

  if (marca.value === "") return;

  let response = await fetch(
    "../../app/controlador.php?acao=modelosDestintas&marca=" + marca.value
  );

  let dados = await response.json();

  if (!response.ok) {
    alert(dados.message);
    return;
  }

  let option = document.createElement("option");
  option.value = "";
  option.textContent = "";

  modeloSelect.appendChild(option);

  dados.forEach((marcaItem) => {
    let option = document.createElement("option");
    option.value = marcaItem;
    option.textContent = marcaItem;

    modeloSelect.appendChild(option);
  });
}

async function carregaCidades() {
  let marca = document.getElementById("marca");
  let modelo = document.getElementById("modelo");

  if (marca.value === "" || modelo.value === "") return;

  let response = await fetch(
    "../../app/controlador.php?acao=cidadesDestintas&marca=" +
      marca.value +
      "&modelo=" +
      modelo.value
  );

  let dados = await response.json();

  if (!response.ok) {
    alert(dados.message);
    return;
  }

  let option = document.createElement("option");
  option.value = "";
  option.textContent = "";

  cidadeSelect.appendChild(option);

  dados.forEach((marcaItem) => {
    let option = document.createElement("option");
    option.value = marcaItem;
    option.textContent = marcaItem;

    cidadeSelect.appendChild(option);
  });
}

async function carregaVeiculosDoModelo() {
  if (marca.value === "" || modelo.value === "" || cidade.value === "") {
    return;
  }

  let response = await fetch(
    "../../app/controlador.php?acao=filtrarAnuncios&marca=" +
      marca.value +
      "&modelo=" +
      modelo.value +
      "&cidade=" +
      cidade.value
  );

  let dados = await response.json();

  if (!response.ok) {
    alert(dados.message);
    return;
  }

  funcaoAtualDeAtualizacaoDoScroll = carregaVeiculosDoModelo;

  listaAnuncios.innerHTML = "";

  dados.forEach((anuncio) => {
    let marca = anuncio.marca;
    let modelo = anuncio.modelo;
    let ano = anuncio.ano;
    let cidade = anuncio.cidade;
    let valor = anuncio.valor;
    let img0 = anuncio.fotos[0];

    montaCard(img0, marca, modelo, ano, cidade, valor);
  });
}

window.onload = () => carregaMarcasDistintas();

const modeloSelect = document.getElementById("modelo");
modeloSelect.onchange = () => {
  cidadeSelect.innerHTML = "";
  carregaCidades();
};
const cidadeSelect = document.getElementById("cidade");

const marcaSelect = document.getElementById("marca");
marcaSelect.onchange = () => {
  modeloSelect.innerHTML = "";
  cidadeSelect.innerHTML = "";
  carregaModelosDaMarcaDistintos();
};

const btnBuscarCarro = document.getElementById("buscarCarro");
btnBuscarCarro.onclick = () => carregaVeiculosDoModelo();
