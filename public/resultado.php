<?php
session_start();

if (empty($_SESSION['simulado_result'])) {
    header("Location: index.php");
    exit;
}

$result = $_SESSION['simulado_result'];
unset($_SESSION['simulado_result']);
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

        <div class="actions">
            <a href="index.php" class="btn btn-primary">Voltar ao Início</a>
        </div>
    </div>
</body>
</html>