document.addEventListener('DOMContentLoaded', () => {
    const simuladoForm = document.getElementById('simuladoForm');
    const baseUrl = window.location.pathname.includes('/admin/') ? '../' : '';

    // Elementos do seletor de modo (se existirem)
    const modeSelector = document.querySelector('.simulado-mode-selector');
    const modeButtons = document.querySelectorAll('.simulado-mode-btn');

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

        // Verifica o modo ativo
        const activeModeBtn = document.querySelector('.simulado-mode-btn.active');
        const immediateMode = activeModeBtn && activeModeBtn.dataset.mode === 'imediato';

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
                    areas: areas,
                    immediate_mode: immediateMode
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Erro ao iniciar simulado');
            }

            // Redireciona para a página correta baseada no modo
            const targetPage = immediateMode ? 'simulado_imediato.php' : 'simulado.php';
            window.location.href = `${baseUrl}${targetPage}?index=0`;

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

    // Lógica para o seletor de modo (se existir na página)
    if (modeButtons.length > 0) {
        modeButtons.forEach(button => {
            button.addEventListener('click', () => {
                modeButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            });
        });
    }

    // Verificar autenticação (código permanece o mesmo)
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

const modeButtons = document.querySelectorAll('.simulado-mode-btn');
if (modeButtons.length > 0) {
    modeButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            modeButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
        });
    });
}