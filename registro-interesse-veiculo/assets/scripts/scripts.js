// assets/scripts/scripts.js

// Quando a página carrega, pega o ID do anúncio da URL e carrega os dados
document.addEventListener('DOMContentLoaded', function() {
    // Obtém o ID do anúncio da URL
    const urlParams = new URLSearchParams(window.location.search);
    const idAnuncio = urlParams.get('id');

    if (!idAnuncio) {
        alert('Anúncio não especificado');
        window.location.href = '../index.html';
        return;
    }

    // Define o ID do anúncio no campo hidden do formulário
    document.getElementById('idAnuncio').value = idAnuncio;

    // Carrega os dados do anúncio
    carregarDadosAnuncio(idAnuncio);

    // Configura o evento de submit do formulário
    document.getElementById('formInteresse').addEventListener('submit', function(e) {
        e.preventDefault();
        enviarInteresse();
    });
});

// Função para carregar os dados do anúncio
async function carregarDadosAnuncio(idAnuncio) {
    try {
        const response = await fetch(`../app/controlador.php?acao=obterAnuncio&id=${idAnuncio}`);
        
        if (!response.ok) {
            throw new Error('Erro ao carregar dados do anúncio');
        }

        const anuncio = await response.json();

        // Atualiza a imagem do veículo
        const imgVeiculo = document.querySelector('.container-img img');
        if (anuncio.fotos && anuncio.fotos.length > 0) {
            imgVeiculo.src = `../assets/img/${anuncio.fotos[0]}`;
        }

        // Atualiza os detalhes do veículo
        document.querySelector('.detalhes-carro-esquerda p:nth-child(1)').textContent = `Marca: ${anuncio.marca}`;
        document.querySelector('.detalhes-carro-esquerda p:nth-child(2)').textContent = `Modelo: ${anuncio.modelo}`;
        document.querySelector('.detalhes-carro-esquerda p:nth-child(3)').textContent = `Ano: ${anuncio.ano}`;
        document.querySelector('.detalhes-carro-direto-cidade p').textContent = `Em ${anuncio.cidade}`;
        document.querySelector('.detalhes-carro-valor strong').textContent = `R$ ${formatarValor(anuncio.valor)}`;

    } catch (error) {
        console.error('Erro:', error);
        alert('Não foi possível carregar os dados do anúncio');
    }
}

// Função para formatar o valor no padrão brasileiro
function formatarValor(valor) {
    return parseFloat(valor).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Função para enviar o formulário de interesse
async function enviarInteresse() {
    const form = document.getElementById('formInteresse');
    const btnEnviar = document.getElementById('btn-enviar');
    const formData = new FormData(form);

    // Desabilita o botão durante o envio
    btnEnviar.disabled = true;
    btnEnviar.value = 'Enviando...';

    try {
        const response = await fetch('../app/controlador.php?acao=registrarInteresse', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao registrar interesse');
        }

        // Se chegou aqui, o registro foi bem-sucedido
        alert(data.message || 'Interesse registrado com sucesso!');
        form.reset();

    } catch (error) {
        console.error('Erro:', error);
        alert(error.message || 'Erro ao enviar o formulário. Por favor, tente novamente.');
    } finally {
        // Reabilita o botão
        btnEnviar.disabled = false;
        btnEnviar.value = 'Enviar';
    }
}