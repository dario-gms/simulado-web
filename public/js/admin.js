document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('logoutBtn');

    // Logout
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            try {
                const response = await fetch('../api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'logout'
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Erro ao fazer logout');
                }

                // Redireciona após receber resposta de sucesso
                window.location.href = '../login.php';
            } catch (error) {
                alert('Erro ao fazer logout: ' + error.message);
                console.error('Erro:', error);
            }
        });
    }

    // Carregar usuários (na página users.php)
    async function loadUsers() {
        try {
            const response = await fetch('../api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get_all_users'
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Erro ao carregar usuários');
            }

            const usersTable = document.getElementById('usersTable');
            if (usersTable) {
                const tbody = usersTable.querySelector('tbody');
                tbody.innerHTML = '';

                if (data.data.users.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5">Nenhum usuário encontrado</td></tr>';
                    return;
                }

                data.data.users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>${user.is_admin ? 'Administrador' : 'Usuário'}</td>
                        <td>${new Date(user.created_at).toLocaleDateString()}</td>
                    `;
                    tbody.appendChild(row);
                });
            }
        } catch (error) {
            console.error('Erro:', error);
            const tbody = document.querySelector('#usersTable tbody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="5">Erro ao carregar usuários: ' + error.message + '</td></tr>';
            }
        }
    }

    // Carregar estatísticas (na página stats.php)
    async function loadStats() {
        try {
            const response = await fetch('../api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get_all_stats'
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Erro ao carregar estatísticas');
            }

            const statsTable = document.getElementById('statsTable');
            if (statsTable) {
                const tbody = statsTable.querySelector('tbody');
                tbody.innerHTML = '';

                if (data.data.stats.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6">Nenhuma estatística encontrada</td></tr>';
                    return;
                }

                data.data.stats.forEach(stat => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${stat.username}</td>
                        <td>${stat.area_name}</td>
                        <td>${stat.total_questions}</td>
                        <td>${stat.correct_answers}</td>
                        <td>${stat.percentage}%</td>
                        <td>${new Date(stat.last_attempt).toLocaleDateString()}</td>
                    `;
                    tbody.appendChild(row);
                });
            }
        } catch (error) {
            console.error('Erro:', error);
            const tbody = document.querySelector('#statsTable tbody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="6">Erro ao carregar estatísticas: ' + error.message + '</td></tr>';
            }
        }
    }

    // Verificar autenticação e carregar dados apropriados
    async function checkAuthAndLoadData() {
        try {
            const response = await fetch('../api.php', {
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
                window.location.href = '../login.php';
                return;
            }

            if (!data.data.user.is_admin) {
                window.location.href = '../profile.php';
                return;
            }

            // Carregar dados específicos da página
            if (window.location.pathname.includes('users.php')) {
                loadUsers();
            } else if (window.location.pathname.includes('stats.php')) {
                loadStats();
            }
        } catch (error) {
            console.error('Erro:', error);
            window.location.href = '../login.php';
        }
    }

    checkAuthAndLoadData();
});