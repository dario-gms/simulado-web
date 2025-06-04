<?php
session_start();
if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header("Location: ../login.php");
    exit;
}
require_once __DIR__ . '/../src/controllers/AreaController.php';
require_once __DIR__ . '/partials/menu.php';

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>Adicionar Nova Questão</h1>
        
        <form id="questionForm" action="api.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_question">
            
            <div class="form-group">
                <label for="enunciado">Enunciado:</label>
                <textarea id="enunciado" name="enunciado" required rows="5"></textarea>
            </div>
            
            <div class="form-group">
                <label for="questionImage">Imagem (Opcional):</label>
                <div class="image-upload-area">
                    <p><i class="fas fa-cloud-upload-alt" style="font-size: 24px;"></i></p>
                    <p>Arraste e solte uma imagem aqui ou clique para selecionar</p>
                </div>
                <input type="file" id="questionImage" name="question_image" accept="image/*" class="image-upload">
                <small></small>
                
                <!-- Container para preview -->
                <div id="imagePreviewContainer" style="margin-top: 15px; display: none;">
                    <p>Pré-visualização:</p>
                    <img id="imagePreview" src="#" alt="Pré-visualização da imagem" 
                         style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;">
                    <button type="button" id="removeImageBtn" class="btn btn-small btn-danger" style="margin-top: 5px;">
                        <i class="fas fa-times"></i> Remover Imagem
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label>Opções:</label>
                <div class="options-container">
                    <div class="option-item">
                        <div class="option-header">
                            <span class="option-letter">A)</span>
                        </div>
                        <textarea name="opcoes[A]" class="option-text" required rows="3"></textarea>
                    </div>
                    <div class="option-item">
                        <div class="option-header">
                            <span class="option-letter">B)</span>
                        </div>
                        <textarea name="opcoes[B]" class="option-text" required rows="3"></textarea>
                    </div>
                    <div class="option-item">
                        <div class="option-header">
                            <span class="option-letter">C)</span>
                        </div>
                        <textarea name="opcoes[C]" class="option-text" required rows="3"></textarea>
                    </div>
                    <div class="option-item">
                        <div class="option-header">
                            <span class="option-letter">D)</span>
                        </div>
                        <textarea name="opcoes[D]" class="option-text" required rows="3"></textarea>
                    </div>
                    <div class="option-item">
                        <div class="option-header">
                            <span class="option-letter">E)</span>
                        </div>
                        <textarea name="opcoes[E]" class="option-text" required rows="3"></textarea>
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

            <div class="form-group">
                <label for="explicacao">Explicação da Resposta (Opcional):</label>
                <textarea id="explicacao" name="explicacao" rows="5"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" id="backBtn" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Adicionar Questão
                </button>
            </div>
        </form>
    </div>

    <script src="js/add_question.js"></script>
</body>
</html>