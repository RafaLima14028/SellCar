document.addEventListener("DOMContentLoaded", () => {
  async function verificaSeEstaLogado() {
    try {
      let response = await fetch(
        "../../../app/controlador.php?acao=estaLogado"
      );

      let redirecionamento_url = "../../../home-interna/index.html";

      let dados = await response.json();

      if (dados.status === "logado") {
        window.location.href = redirecionamento_url;
        return;
      }
    } catch (error) {
      window.location.href = redirecionamento_url;
    }
  }

  async function buscaPeloAnuncio(idAnuncio) {
    let response = await fetch(
      `../../../app/controladorPrivado.php?acao=buscaAnuncioComFotoPeloId&idAnuncio=` +
        idAnuncio
    );

    if (!response.ok) {
      alert("Erro ao carregar o anúncio");
      return;
    }

    let dados = await response.json();

    await montaLayout(dados);
  }

  function montaP(texto, descricao) {
    let p = document.createElement("p");

    p.innerHTML = "<b>" + texto + ":</b> " + descricao;

    return p;
  }

  function montaImagensLayout(fotos, idAnuncio) {
    fotos.forEach((foto) => {
      let caminhoFoto =
        "../../../app/uploads/anuncios/" + idAnuncio + "/" + foto;

      const imgs_carros = document.getElementById("imgs-carro");

      let img = document.createElement("img");
      img.src = caminhoFoto;
      img.alt = "Foto do carro";
      img.style.marginLeft = "auto";
      img.style.marginRight = "auto";

      imgs_carros.appendChild(img);
    });
  }

  function montaLayout(anuncio) {
    let marca = anuncio.marca;
    let modelo = anuncio.modelo;
    let cor = anuncio.cor;
    let ano = anuncio.ano;
    let quilometragem = anuncio.quilometragem;
    let cidade = anuncio.cidade;
    let estado = anuncio.estado;
    let valor = anuncio.valor;
    let descricao = anuncio.descricao;
    let fotos = anuncio.fotos;

    const descricao_carro_p = document.getElementById("descricao-carro-p");
    const detalhes_carro_lado_direito = document.getElementById(
      "detalhes-carro-lado-direito"
    );
    const detalhes_carro_lado_esquerdo = document.getElementById(
      "detalhes-carro-lado-esquerdo"
    );

    detalhes_carro_lado_esquerdo.appendChild(montaP("Marca", marca));
    detalhes_carro_lado_esquerdo.appendChild(montaP("Modelo", modelo));
    detalhes_carro_lado_esquerdo.appendChild(montaP("Cor", cor));
    detalhes_carro_lado_esquerdo.appendChild(montaP("Ano", ano));
    detalhes_carro_lado_esquerdo.appendChild(
      montaP("Quilometragem", quilometragem)
    );

    let p1 = document.createElement("p");
    p1.id = "detalhe-carro-cidade";
    p1.innerHTML = "Em " + cidade + ", " + estado;

    let p2 = document.createElement("p");
    p2.id = "detalhe-carro-valor";
    p2.innerHTML = "<strong>R$ " + valor + "</strong>";

    detalhes_carro_lado_direito.appendChild(p1);
    detalhes_carro_lado_direito.appendChild(p2);

    let p3 = document.createElement("p");
    p3.innerText = descricao;

    descricao_carro_p.appendChild(p3);

    montaImagensLayout(fotos, anuncio.id);
  }

  verificaSeEstaLogado();
  buscaPeloAnuncio(1);
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
