<?php
require_once __DIR__ . '/../src/controllers/AreaController.php';

$areaController = new AreaController();
$areas = $areaController->getAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Questão</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Adicionar Nova Questão</h1>
        
        <form id="questionForm" action="api.php" method="POST">
            <input type="hidden" name="action" value="add_question">
            
            <div class="form-group">
                <label for="enunciado">Enunciado:</label>
                <textarea id="enunciado" name="enunciado" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Opções:</label>
                <div class="options-container">
                    <div class="option-input">
                        <label>A)</label>
                        <input type="text" name="opcoes[A]" class="option-text" required>
                    </div>
                    <div class="option-input">
                        <label>B)</label>
                        <input type="text" name="opcoes[B]" class="option-text" required>
                    </div>
                    <div class="option-input">
                        <label>C)</label>
                        <input type="text" name="opcoes[C]" class="option-text" required>
                    </div>
                    <div class="option-input">
                        <label>D)</label>
                        <input type="text" name="opcoes[D]" class="option-text" required>
                    </div>
                    <div class="option-input">
                        <label>E)</label>
                        <input type="text" name="opcoes[E]" class="option-text" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="correctAnswer">Resposta Correta:</label>
                <select id="correctAnswer" name="resposta_correta" required>
                    <option value="">Selecione</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="area">Área de Conhecimento:</label>
                <select id="area" name="area_id" required>
                    <option value="">Selecione</option>
                    <?php foreach ($areas as $area): ?>
                    <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="buttons">
                <button type="button" id="backBtn" class="btn btn-secondary">Voltar</button>
                <button type="submit" class="btn btn-primary">Adicionar Questão</button>
            </div>
        </form>
    </div>

    <script src="js/add_question.js"></script>
</body>
</html>