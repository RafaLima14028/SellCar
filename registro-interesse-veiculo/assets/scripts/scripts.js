const urlParams = new URLSearchParams(window.location.search);
const idAnuncio = urlParams.get("id");

document.addEventListener("DOMContentLoaded", async function () {
  if (!idAnuncio) {
    alert("Anúncio não especificado");
    window.location.href = "../index.html";
    return;
  }

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

  carregarDadosAnuncio(idAnuncio);

  document
    .getElementById("formInteresse")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      enviarInteresse();
    });
});

// Função para carregar os dados do anúncio
async function carregarDadosAnuncio(idAnuncio) {
  try {
    const response = await fetch(
      "../../../app/controladorPrivado.php?acao=buscaAnuncioComFotoPeloId&idAnuncio=" +
        idAnuncio
    );

    if (!response.ok) {
      throw new Error("Erro ao carregar dados do anúncio");
    }

    const anuncio = await response.json();

    const containerCard = document.querySelector(".container-card");

    let divImg = document.createElement("div");
    divImg.className = "container-img";

    let img = document.createElement("img");
    img.src =
      "../../../app/uploads/anuncios/" + idAnuncio + "/" + anuncio.fotos[0];
    img.alt = "Imagem do veículo";

    divImg.appendChild(img);
    containerCard.appendChild(divImg);

    let div_detalhes_carro = document.createElement("div");
    div_detalhes_carro.className = "detalhes-carro";

    let div_detalhes_carro_esquerdo = document.createElement("div");
    div_detalhes_carro_esquerdo.className = "detalhes-carro-esquerda";

    let pMarca = document.createElement("p");
    pMarca.innerText = "Marca: " + anuncio.marca;

    let pModelo = document.createElement("p");
    pModelo.innerText = "Modelo: " + anuncio.modelo;

    let pAno = document.createElement("p");
    pAno.innerText = "Ano: " + anuncio.ano;

    div_detalhes_carro_esquerdo.appendChild(pMarca);
    div_detalhes_carro_esquerdo.appendChild(pModelo);
    div_detalhes_carro_esquerdo.appendChild(pAno);
    div_detalhes_carro.appendChild(div_detalhes_carro_esquerdo);

    let div_detalhes_carro_direto = document.createElement("div");
    div_detalhes_carro_direto.className = "detalhes-carro-direto";

    let div_detalhes_carro_direto_cidade = document.createElement("div");
    div_detalhes_carro_direto_cidade.className = "detalhes-carro-direto-cidade";

    let pCidade = document.createElement("p");
    pCidade.innerText = "Em " + anuncio.cidade;

    div_detalhes_carro_direto_cidade.appendChild(pCidade);
    div_detalhes_carro_direto.appendChild(div_detalhes_carro_direto_cidade);
    div_detalhes_carro.appendChild(div_detalhes_carro_direto);

    let detalhes_carro_valor = document.createElement("div");
    detalhes_carro_valor.className = "detalhes-carro-valor";

    let pValor = document.createElement("p");
    pValor.innerHTML = "<strong>R$ " + anuncio.valor + "</strong>";

    detalhes_carro_valor.appendChild(pValor);
    div_detalhes_carro.appendChild(detalhes_carro_valor);

    containerCard.appendChild(div_detalhes_carro);
  } catch (error) {
    console.error("Erro:", error);
    alert("Não foi possível carregar os dados do anúncio");
  }
}

async function enviarInteresse() {
  const form = document.getElementById("formInteresse");
  const btnEnviar = document.getElementById("btn-enviar");
  const formData = new FormData(form);

  btnEnviar.disabled = true;
  btnEnviar.value = "Enviando...";

  try {
    formData.append("idAnuncio", idAnuncio);

    const response = await fetch(
      "../../../app/controladorPrivado.php?acao=registrarInteresse",
      {
        method: "POST",
        body: formData,
      }
    );

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || "Erro ao registrar interesse");
    }

    alert(data.message || "Interesse registrado com sucesso!");

    window.location.href = "../../../home-interna/index.html";
  } catch (error) {
    console.error("Erro:", error);
    alert(
      error.message ||
        "Erro ao enviar o formulário. Por favor, tente novamente."
    );
  } finally {
    btnEnviar.disabled = false;
    btnEnviar.value = "Enviar";
  }
}
