document.addEventListener('DOMContentLoaded', () => {
    const simuladoForm = document.getElementById('simuladoForm');
    const baseUrl = window.location.pathname.includes('/admin/') ? '../' : '';

    // Iniciar Simulado
    simuladoForm?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const areas = Array.from(
            document.querySelectorAll('input[name="areas[]"]:checked')
        ).map(input => parseInt(input.value));

        if (areas.length === 0) {
            alert('Selecione pelo menos uma área!');
            return;
        }

        try {
            const submitBtn = simuladoForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Preparando...';

            const response = await fetch(`${baseUrl}api.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'start_simulado',
                    areas: areas
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Erro ao iniciar simulado');
            }

            window.location.href = `${baseUrl}simulado.php?index=0`;
        } catch (error) {
            console.error('Erro:', error);
            alert(error.message);
            const submitBtn = simuladoForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Iniciar Simulado';
            }
        }
    });

    // Verificar autenticação
    async function checkAuth() {
        try {
            const response = await fetch(`${baseUrl}api.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'check_auth'
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                window.location.href = `${baseUrl}login.php`;
            }
        } catch (error) {
            console.error('Erro:', error);
            window.location.href = `${baseUrl}login.php`;
        }
    }

    checkAuth();
});