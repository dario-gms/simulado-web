<?php
session_start();

if (empty($_SESSION['simulado']['questions'])) {
    header("Location: index.php");
    exit;
}

$currentIndex = isset($_GET['index']) ? (int)$_GET['index'] : 0;
$questions = $_SESSION['simulado']['questions'];
$totalQuestions = count($questions);

if ($currentIndex >= $totalQuestions) {
    header("Location: resultado.php");
    exit;
}

$currentQuestion = $questions[$currentIndex];
$startTime = $_SESSION['simulado']['start_time'] ?? time();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado - Questão <?= $currentIndex + 1 ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="simulado-header">
            <div class="progress-bar">
                <div class="progress" style="width: <?= ($currentIndex / $totalQuestions) * 100 ?>%"></div>
            </div>
            <div class="header-info">
                <span>Questão <?= $currentIndex + 1 ?> de <?= $totalQuestions ?></span>
                <div class="timer" id="timer">00:00</div>
            </div>
        </header>

        <main class="simulado-main">
            <form id="questionForm">
                <input type="hidden" name="question_id" value="<?= $currentQuestion['id'] ?>">
                
                <div class="question-text">
                    <?= nl2br(htmlspecialchars($currentQuestion['enunciado'])) ?>
                </div>
                
                <?php if ($currentQuestion['has_image']): ?>
                <div class="question-image">
                    <img src="get_image.php?id=<?= $currentQuestion['id'] ?>" 
                         alt="Imagem da questão" 
                         style="max-width: 100%; max-height: 400px;">
                </div>
                <?php endif; ?>
                
                <div class="options">
                    <?php foreach ($currentQuestion['opcoes'] as $option): ?>
                    <div class="option" id="option_<?= $option['letra'] ?>">
                        <input type="radio" name="answer" id="option_input_<?= $option['letra'] ?>" 
                               value="<?= $option['letra'] ?>">
                        <label for="option_input_<?= $option['letra'] ?>">
                            <span class="option-letter"><?= $option['letra'] ?></span>
                            <span class="option-text"><?= htmlspecialchars($option['texto']) ?></span>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($currentQuestion['explicacao'])): ?>
                <div class="explanation-container" id="explanationContainer" style="display: none;">
                    <div class="explanation-title">Explicação:</div>
                    <div class="explanation-text"><?= nl2br(htmlspecialchars($currentQuestion['explicacao'])) ?></div>
                </div>
                <?php endif; ?>
            </form>
        </main>

        <footer class="simulado-footer">
            <button id="checkAnswerBtn" class="btn btn-primary">Verificar Resposta</button>
            <?php if ($currentIndex < $totalQuestions - 1): ?>
                <button id="nextBtn" class="btn btn-primary" style="display: none;">Próxima Questão</button>
            <?php else: ?>
                <button id="finishBtn" class="btn btn-finish" style="display: none;">Finalizar Simulado</button>
            <?php endif; ?>
        </footer>
    </div>

    <script>
        // Timer - Solução definitiva
        const startTimestamp = <?= $startTime ?>;
        
        function updateTimer() {
            const now = Math.floor(Date.now() / 1000);
            const elapsedSeconds = now - startTimestamp;
            const minutes = Math.floor(elapsedSeconds / 60).toString().padStart(2, '0');
            const seconds = (elapsedSeconds % 60).toString().padStart(2, '0');
            document.getElementById('timer').textContent = `${minutes}:${seconds}`;
        }
        
        // Iniciar timer imediatamente e atualizar a cada segundo
        updateTimer();
        setInterval(updateTimer, 1000);

        // Verificação de resposta - Solução completa
        document.getElementById('checkAnswerBtn').addEventListener('click', async function() {
            const selectedOption = document.querySelector('input[name="answer"]:checked');
            
            if (!selectedOption) {
                alert('Por favor, selecione uma resposta antes de verificar.');
                return;
            }

            const checkBtn = this;
            const originalText = checkBtn.innerHTML;
            
            // Mostrar estado de carregamento
            checkBtn.classList.add('btn-loading');
            checkBtn.disabled = true;
            checkBtn.innerHTML = 'Processando...';

            try {
                // 1. Enviar resposta para o servidor
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'submit_answer',
                        question_id: document.querySelector('input[name="question_id"]').value,
                        answer: selectedOption.value
                    })
                });

                if (!response.ok) {
                    throw new Error('Erro na comunicação com o servidor');
                }

                const result = await response.json();
                if (!result.success) {
                    throw new Error(result.message || 'Erro ao processar resposta');
                }

                // 2. Processar visualização
                const selectedValue = selectedOption.value;
                const correctAnswer = '<?= $currentQuestion['resposta_correta'] ?>';
                const isCorrect = selectedValue === correctAnswer;

                // Remover estilos anteriores
                document.querySelectorAll('.option').forEach(opt => {
                    opt.classList.remove('correct-answer', 'wrong-answer', 'show-correct-answer');
                });

                // Marcar resposta do usuário
                const userOption = document.getElementById(`option_${selectedValue}`);
                userOption.classList.add(isCorrect ? 'correct-answer' : 'wrong-answer');

                // Marcar resposta correta se necessário
                if (!isCorrect) {
                    const correctOption = document.getElementById(`option_${correctAnswer}`);
                    correctOption.classList.add('show-correct-answer');
                }

                // Mostrar explicação se existir
                const explanation = document.getElementById('explanationContainer');
                if (explanation) {
                    explanation.style.display = 'block';
                }

                // 3. Atualizar interface
                checkBtn.style.display = 'none';
                const nextBtn = document.getElementById('nextBtn');
                const finishBtn = document.getElementById('finishBtn');
                
                if (nextBtn) nextBtn.style.display = 'inline-block';
                if (finishBtn) finishBtn.style.display = 'inline-block';

            } catch (error) {
                console.error('Erro:', error);
                alert(error.message);
            } finally {
                // Restaurar botão
                checkBtn.classList.remove('btn-loading');
                checkBtn.disabled = false;
                checkBtn.innerHTML = originalText;
            }
        });

        // Navegação entre questões
        document.getElementById('nextBtn')?.addEventListener('click', () => {
            window.location.href = `simulado_imediato.php?index=<?= $currentIndex + 1 ?>`;
        });

        document.getElementById('finishBtn')?.addEventListener('click', async () => {
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'finish_simulado'
                    })
                });

                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message || 'Erro ao finalizar simulado');
                }

                window.location.href = 'resultado.php';
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao finalizar: ' + error.message);
            }
        });
    </script>
</body>
</html>