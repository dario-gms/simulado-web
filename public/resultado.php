<?php
session_start();

if (empty($_SESSION['simulado_result'])) {
    header("Location: index.php");
    exit;
}

$result = $_SESSION['simulado_result'];
unset($_SESSION['simulado_result']);

require_once __DIR__ . '/../src/controllers/AreaController.php';
$areaController = new AreaController();
$allAreas = $areaController->getAll();

// Mapear IDs de áreas para nomes
$areaNames = [];
foreach ($allAreas as $area) {
    $areaNames[$area['id']] = $area['nome'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Simulado</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="result-title">Resultado do Simulado</h1>
        
        <div class="result-summary">
            <div class="result-card">
                <h3>Pontuação</h3>
                <div class="score"><?= $result['score'] ?>/<?= $result['total'] ?></div>
                <div class="percentage"><?= round(($result['score'] / $result['total']) * 100) ?>%</div>
            </div>
            
            <div class="result-card">
                <h3>Tempo Total</h3>
                <div class="time">
                    <?= floor($result['time'] / 60) ?> minutos e <?= $result['time'] % 60 ?> segundos
                </div>
            </div>
        </div>

        <div class="result-details">
            <h3>Desempenho por Área</h3>
            <div class="area-stats">
                <?php foreach ($result['area_stats'] as $areaId => $stats): ?>
                <div class="area-stat-card">
                    <h4><?= htmlspecialchars($areaNames[$areaId] ?? 'Área Desconhecida') ?></h4>
                    <div class="stat-score">
                        <?= $stats['correct'] ?>/<?= $stats['total'] ?>
                        (<?= round(($stats['correct'] / $stats['total']) * 100) ?>%)
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="actions">
            <a href="profile.php" class="btn btn-primary">Ver Meu Histórico</a>
            <a href="profile.php" class="btn btn-secondary">Novo Simulado</a>
        </div>
    </div>
</body>
</html>