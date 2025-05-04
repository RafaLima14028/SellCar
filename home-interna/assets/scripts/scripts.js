var paginaAtual = 1;
var carregando = false;

const listaAnuncios = document.getElementById("lista-anuncios");

document.addEventListener("DOMContentLoaded", async () => {
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

  let caminhoFoto = "../../app/" + img0;
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

async function exibirAnuncios(anuncios) {
  anuncios.forEach((anuncio) => {
    let marca = anuncio.marca;
    let modelo = anuncio.modelo;
    let ano = anuncio.ano;
    let cidade = anuncio.cidade;
    let valor = anuncio.valor;
    let img0 = anuncio.fotos[0];
    let anuncioId = anuncio.id;

    montaCard(img0, marca, modelo, ano, cidade, valor, anuncioId);
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
  if (window.innerHeight + window.screenY >= document.body.offsetHeight - 100) {
    carregarAnuncios();
  }
});

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
    let anuncioId = anuncio.id;

    montaCard(img0, marca, modelo, ano, cidade, valor, anuncioId);
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
