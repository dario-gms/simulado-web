<?php
session_start();
if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../src/controllers/AreaController.php';

$areaController = new AreaController();
$areas = $areaController->getAll();

// Define a URL base para links
$base_url = '/simulado/public/';
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = !empty($_SESSION['user']['is_admin']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado para Concursos</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Inclui o menu -->
    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="container">
        <header class="hero">
            <h1>Simulado para Concursos</h1>
            <p>Teste seus conhecimentos e prepare-se para os melhores concursos públicos</p>
        </header>
        
        <div class="card">
            <h2>Iniciar Novo Simulado</h2>
            <form id="simuladoForm">
                <div class="areas-list">
                    <?php foreach ($areas as $area): ?>
                    <div class="area-item">
                        <input type="checkbox" id="area-<?= $area['id'] ?>" 
                               name="areas[]" value="<?= $area['id'] ?>">
                        <label for="area-<?= $area['id'] ?>"><?= htmlspecialchars($area['nome']) ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                        </svg>
                        Iniciar Simulado
                    </button>
                </div>
            </form>
        </div>

        <?php if ($is_admin): ?>
        <div class="admin-quick-stats">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><i class="fas fa-users"></i> Usuários</h3>
                    <div class="stat-value" id="total-users">--</div>
                    <a href="<?= $base_url ?>admin/users.php" class="btn btn-small">Gerenciar</a>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-question-circle"></i> Questões</h3>
                    <div class="stat-value" id="total-questions">--</div>
                    <a href="<?= $base_url ?>add_question.php" class="btn btn-small">Adicionar</a>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-layer-group"></i> Áreas</h3>
                    <div class="stat-value"><?= count($areas) ?></div>
                    <a href="<?= $base_url ?>manage_areas.php" class="btn btn-small">Gerenciar</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="<?= $base_url ?>js/main.js"></script>
    <script>
    // Carrega estatísticas rápidas para admin
    <?php if ($is_admin): ?>
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            // Carrega totais de usuários e questões
            const responses = await Promise.all([
                fetch('<?= $base_url ?>api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_all_users' })
                }),
                fetch('<?= $base_url ?>api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_questions_count' })
                })
            ]);

            const [usersRes, questionsRes] = await Promise.all(responses.map(r => r.json()));

            if (usersRes.success) {
                document.getElementById('total-users').textContent = usersRes.data.users.length;
            }
            if (questionsRes.success) {
                document.getElementById('total-questions').textContent = questionsRes.data.count;
            }
        } catch (error) {
            console.error('Erro ao carregar estatísticas:', error);
        }
    });
    <?php endif; ?>
    </script>
</body>
</html>