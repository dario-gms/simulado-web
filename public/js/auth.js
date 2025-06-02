// auth.js completo corrigido
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    async function handleFormSubmit(e, action) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            // Verifica se é registro e as senhas coincidem
            if (action === 'register' && data.password !== data.confirm_password) {
                throw new Error('As senhas não coincidem');
            }

            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action,
                    username: data.username,
                    email: action === 'register' ? data.email : undefined,
                    password: data.password
                })
            });

            // Verifica se a resposta é JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Resposta inválida do servidor: ${text.substring(0, 100)}`);
            }

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || `Erro ao ${action === 'login' ? 'fazer login' : 'registrar'}`);
            }

            if (action === 'login') {
                window.location.href = 'profile.php';
            } else {
                alert('Registro realizado com sucesso! Faça login para continuar.');
                window.location.href = 'login.php';
            }
        } catch (error) {
            alert(`Falha no ${action === 'login' ? 'login' : 'registro'}: ${error.message}`);
            console.error('Erro:', error);
        }
    }

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => handleFormSubmit(e, 'login'));
    }

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => handleFormSubmit(e, 'register'));
    }
});