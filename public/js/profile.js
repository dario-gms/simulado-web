document.addEventListener('DOMContentLoaded', () => {
    // Elementos da página
    const logoutForm = document.getElementById('logoutForm');
    const statsContainer = document.getElementById('statsContainer');

    // Carregar estatísticas do usuário
    const loadUserStats = async () => {
        try {
            statsContainer.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i><span>Carregando estatísticas...</span></div>';

            const response = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'get_user_stats' })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Erro ao carregar estatísticas');
            }

            if (data.data.stats.length === 0) {
                statsContainer.innerHTML = '<p class="no-data">Nenhum dado de desempenho disponível ainda.</p>';
                return;
            }

            let html = '<div class="stats-grid">';
            data.data.stats.forEach(stat => {
                const percentageColor = stat.percentage >= 70 ? 'high' :
                    stat.percentage >= 40 ? 'medium' : 'low';

                html += `
                <div class="stat-item">
                    <h3>${stat.nome}</h3>
                    <div class="stat-value">${stat.correct_answers}/${stat.total_questions}</div>
                    <div class="stat-percentage ${percentageColor}">${stat.percentage}%</div>
                    <div class="stat-date">Última tentativa: ${formatDate(stat.last_attempt)}</div>
                </div>
                `;
            });
            html += '</div>';

            statsContainer.innerHTML = html;
        } catch (error) {
            console.error('Erro:', error);
            statsContainer.innerHTML = `<p class="error">Erro ao carregar estatísticas: ${error.message}</p>`;
        }
    };

    // Formatador de data
    const formatDate = (dateString) => {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('pt-BR', options);
    };

    // Logout
    if (logoutForm) {
        logoutForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Erro ao fazer logout');
                }

                // Redireciona após logout
                window.location.href = 'login.php';
            } catch (error) {
                console.error('Erro no logout:', error);
                alert('Erro ao fazer logout: ' + error.message);
            }
        });
    }

    // Mostrar alerta
    const showAlert = (message, type) => {
        const alert = document.createElement('div');
        alert.className = `alert ${type}`;
        alert.textContent = message;
        document.body.prepend(alert);

        setTimeout(() => alert.remove(), 5000);
    };

    // Verificar autenticação e carregar dados
    const init = async () => {
        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'check_auth' })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                window.location.href = 'login.php';
                return;
            }

            await loadUserStats();
        } catch (error) {
            console.error('Erro na verificação de autenticação:', error);
            window.location.href = 'login.php';
        }
    };

    init();
});