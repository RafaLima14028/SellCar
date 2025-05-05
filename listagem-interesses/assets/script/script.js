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

document.addEventListener("DOMContentLoaded", async () => {
  await verificaSeEstaLogado();
  carregaListagemInteresses();
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

async function carregaListagemInteresses() {
  let response = await fetch(
    "../../../app/controladorPrivado.php?acao=buscarInteresseUsuario"
  );

  if (!response.ok) {
    alert("Erro ao carregar a listagem de interesses");
    return;
  }

  let anuncios = await response.json();

  montaLayoutCompleto(anuncios);
}

const main = document.querySelector("main");

function montaLayoutCompleto(anuncios) {
  anuncios.forEach((anuncio) => {
    montaCard(
      anuncio.fotos[0],
      anuncio.marca,
      anuncio.modelo,
      anuncio.valor,
      anuncio.cidade,
      anuncio.idAnuncio,
      anuncio.ano
    );

    montaInteresses(anuncio.interesses);
  });
}

function montaCard(img0, marca, modelo, valor, cidade, idAnuncio, ano) {
  let caminhoFoto = "../../../app/uploads/anuncios/" + idAnuncio + "/" + img0;

  let img = document.createElement("img");
  img.src = caminhoFoto;
  img.alt = "Foto do carro";

  img.style.width = "100%";
  img.style.height = "100%";
  img.style.objectFit = "cover";
  img.style.objectPosition = "center";

  let divContainerImg = document.createElement("div");
  divContainerImg.className = "container-img";

  let sectionContainerCard = document.createElement("section");
  sectionContainerCard.className = "container-card";

  divContainerImg.appendChild(img);
  sectionContainerCard.appendChild(divContainerImg);

  let divContainerDetalhesCarro = document.createElement("div");
  divContainerDetalhesCarro.className = "container-detalhes-carro";

  let divDetalhesCarro = document.createElement("div");
  divDetalhesCarro.className = "detalhes-carro";

  let divDetalhesCarroEsquerda = document.createElement("div");
  divDetalhesCarroEsquerda.className = "detalhes-carro-esquerda";

  let divDetalhesCarroDireto = document.createElement("div");
  divDetalhesCarroDireto.className = "detalhes-carro-direto";

  let divDetalhesCarroDiretoCidade = document.createElement("div");
  divDetalhesCarroDiretoCidade.className = "detalhes-carro-direto-cidade";

  let divDetalhesCarroValor = document.createElement("div");
  divDetalhesCarroValor.className = "detalhes-carro-valor";

  let pMarca = document.createElement("p");
  pMarca.innerText = "Marca: " + marca;

  let pModelo = document.createElement("p");
  pModelo.innerText = "Modelo: " + modelo;

  let pAno = document.createElement("p");
  pAno.innerText = "Ano: " + ano;

  let pCidade = document.createElement("p");
  pCidade.innerText = "Em " + cidade;

  let pValor = document.createElement("p");
  pValor.innerHTML = "<strong>R$ " + valor + "</strong>";

  divDetalhesCarroEsquerda.appendChild(pMarca);
  divDetalhesCarroEsquerda.appendChild(pModelo);
  divDetalhesCarroEsquerda.appendChild(pAno);
  divDetalhesCarro.appendChild(divDetalhesCarroEsquerda);
  divContainerDetalhesCarro.appendChild(divDetalhesCarro);
  sectionContainerCard.appendChild(divContainerDetalhesCarro);

  divDetalhesCarroDiretoCidade.appendChild(pCidade);
  divDetalhesCarroDireto.appendChild(divDetalhesCarroDiretoCidade);
  divDetalhesCarro.appendChild(divDetalhesCarroDireto);

  divDetalhesCarroValor.appendChild(pValor);
  divContainerDetalhesCarro.appendChild(divDetalhesCarroValor);

  main.appendChild(sectionContainerCard);
}

function montaInteresses(interesses) {
  var sectionInteresses = document.createElement("section");
  sectionInteresses.className = "interesses";

  var divContainerInteresse = document.createElement("div");
  divContainerInteresse.className = "container-interesse";

  interesses.forEach((interesse) => {
    let divParagrafo = document.createElement("div");
    divParagrafo.className = "paragrafo";

    let pNome = document.createElement("p");
    pNome.innerHTML = "<strong>Nome:</strong> " + interesse.nome;

    let pTelefone = document.createElement("p");
    pTelefone.innerHTML = "<strong>Telefone:</strong> " + interesse.telefone;

    let pMensagem = document.createElement("p");
    pMensagem.innerHTML = "<strong>Mensagem:</strong> " + interesse.mensagem;

    divParagrafo.appendChild(pNome);
    divParagrafo.appendChild(pTelefone);
    divParagrafo.appendChild(pMensagem);

    divContainerInteresse.appendChild(divParagrafo);
  });

  sectionInteresses.appendChild(divContainerInteresse);
  main.appendChild(sectionInteresses);
}
