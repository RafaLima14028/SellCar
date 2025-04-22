window.onload = () => {
  const btnEnviar = document.getElementById("btn-enviar");
  const inputImgsCarros = document.getElementById("imgs-carro");
  const formDados = document.querySelector("form");

  inputImgsCarros.onchange = () => {
    const files = inputImgsCarros.files;

    if (files && files.length < 3) {
      alert("Por favor, selecione pelo menos 3 imagens");
      btnEnviar.disabled = true;
    } else {
      btnEnviar.disabled = false;
    }
  };

  btnEnviar.onclick = () => {
    if (!btnEnviar.disabled) formDados.submit();
    else alert("Complete os dados restantes");
  };
};
