document.addEventListener('DOMContentLoaded', () => {
    const simuladoForm = document.getElementById('simuladoForm');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const manageAreasBtn = document.getElementById('manageAreasBtn');

    // Navegação
    addQuestionBtn?.addEventListener('click', () => {
        window.location.href = 'add_question.php';
    });

    manageAreasBtn?.addEventListener('click', () => {
        window.location.href = 'manage_areas.php';
    });

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
            const response = await fetch('http://localhost/simulado/public/api.php', {
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

            window.location.href = `simulado.php?index=0`;
        } catch (error) {
            console.error('Erro:', error);
            alert(error.message);
        }
    });
});