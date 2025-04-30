document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form');
    
    if(loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            
            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    senha: senha
                })
            })
            .then(response => {
                if(!response.ok) {
                    throw response;
                }
                return response.json();
            })
            .then(data => {
                if(data.status === "success") {
                    window.location.href = 'dashboard.php'; // Redireciona para área restrita
                } else {
                    showError(data.message || "Erro durante o login");
                }
            })
            .catch(error => {
                if(typeof error.json === 'function') {
                    error.json().then(errorData => {
                        showError(errorData.message || "Erro durante o login");
                    });
                } else {
                    showError("Erro durante o login");
                }
            });
        });
    }
    
    function showError(message) {
        // Remove mensagens de erro anteriores
        const oldError = document.querySelector('.error-message');
        if(oldError) oldError.remove();
        
        // Cria nova mensagem de erro
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.style.color = 'red';
        errorElement.style.margin = '10px 0';
        errorElement.style.textAlign = 'center';
        errorElement.textContent = message;
        
        // Insere antes do botão de submit
        const submitButton = document.getElementById('btn-enviar');
        submitButton.parentNode.insertBefore(errorElement, submitButton);
    }
});