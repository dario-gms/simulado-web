<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/controllers/AreaController.php';

$areaController = new AreaController();
$areas = $areaController->getAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado para Concursos</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="hero">
            <h1>Simulado para Concursos</h1>
            <p>Selecione as áreas para iniciar:</p>
        </header>
        
        <div class="card">
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
                    <button type="button" id="addQuestionBtn" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        Adicionar Questões
                    </button>
                </div>
                
                <div class="footer">
                    <a href="manage_areas.php" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM11 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/>
                        </svg>
                        Gerenciar Áreas
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>