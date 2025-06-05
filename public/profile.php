<?php
session_start();
if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../src/controllers/AreaController.php';
$areaController = new AreaController();
$areas = $areaController->getAll();

$base_url = '/simulado/public/'; // Adicione esta linha para consistência
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil</title>
    <link rel="stylesheet" href="<?= $base_url ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/partials/menu.php'; ?>
    
    <div class="container profile-container">
        <h1 class="profile-title">Meu Perfil</h1>
        
        <div class="profile-grid">
            <!-- Seção de Estatísticas -->
            <div class="profile-card">
                <div class="card-header">
                    <h2><i class="fas fa-chart-line"></i> Histórico de Desempenho</h2>
                </div>
                <div id="statsContainer" class="stats-container">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Carregando estatísticas...</span>
                    </div>
                </div>
            </div>
            
            <!-- Seção de Informações do Usuário -->
            <div class="profile-card">
                <div class="card-header">
                    <h2><i class="fas fa-user"></i> Minhas Informações</h2>
                </div>
                <div class="user-info">         
                    <p><strong>Email:</strong> <span id="userEmail"><?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?></span></p>
                    <p><strong>Cadastrado em:</strong> <span id="userCreatedAt"><?= 
                        isset($_SESSION['user']['created_at']) ? 
                        date('d/m/Y', strtotime($_SESSION['user']['created_at'])) : '' 
                    ?></span></p>
                </div>
                <div class="action-buttons">
                    <a href="<?= $base_url ?>index.php" class="btn btn-primary">
                        <i class="fas fa-play"></i> Ir para Simulados
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= $base_url ?>js/profile.js"></script>
</body>
</html>