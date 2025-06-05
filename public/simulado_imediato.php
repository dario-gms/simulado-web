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
$timerMode = $_SESSION['simulado']['timer_mode'] ?? 'stopwatch';
$countdownEnd = $_SESSION['simulado']['countdown_end'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado - Questão <?= $currentIndex + 1 ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 0.5rem;
            vertical-align: middle;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .btn[disabled] {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .btn-loading {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="simulado-header">
            <div class="progress-bar">
                <div class="progress" style="width: <?= ($currentIndex / $totalQuestions) * 100 ?>%"></div>
            </div>
            <div class="header-info">
                <span>Questão <?= $currentIndex + 1 ?> de <?= $totalQuestions ?></span>
                <div class="timer" id="timer">
                    <?= $timerMode === 'stopwatch' ? '00:00' : '--:--' ?>
                </div>
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
        // Configuração do timer
        const timerMode = '<?= $timerMode ?>';
        const countdownEnd = <?= $countdownEnd ?? 'null' ?>;
        let timerInterval;

        function updateTimer() {
            const now = Math.floor(Date.now() / 1000);
            const timerElement = document.getElementById('timer');
            
            if (timerMode === 'stopwatch') {
                // Modo cronômetro
                const elapsed = now - <?= $startTime ?>;
                const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
                const seconds = (elapsed % 60).toString().padStart(2, '0');
                timerElement.textContent = `${minutes}:${seconds}`;
            } else if (countdownEnd) {
                // Modo contagem regressiva
                const remaining = countdownEnd - now;
                
                if (remaining <= 0) {
                    // Tempo esgotado - finalizar simulado automaticamente
                    clearInterval(timerInterval);
                    timerElement.textContent = '00:00';
                    alert('O tempo acabou! O simulado será finalizado automaticamente.');
                    finishSimulado();
                    return;
                }
                
                const minutes = Math.floor(remaining / 60).toString().padStart(2, '0');
                const seconds = (remaining % 60).toString().padStart(2, '0');
                timerElement.textContent = `${minutes}:${seconds}`;
            }
        }

        // Iniciar timer
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);

        async function finishSimulado() {
            try {
                const finishBtn = document.getElementById('finishBtn');
                if (finishBtn) {
                    finishBtn.disabled = true;
                    finishBtn.innerHTML = '<span class="spinner"></span> Finalizando...';
                }

                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'finish_simulado'
                    })
                });

                // Verifica se a resposta é JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    throw new Error(`Resposta inválida do servidor: ${text.substring(0, 100)}`);
                }

                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message || 'Erro ao finalizar simulado');
                }

                window.location.href = 'resultado.php';
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao finalizar: ' + error.message);
                
                const finishBtn = document.getElementById('finishBtn');
                if (finishBtn) {
                    finishBtn.disabled = false;
                    finishBtn.innerHTML = 'Finalizar Simulado';
                }
            }
        }

        // Verificação de resposta
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
            checkBtn.innerHTML = '<span class="spinner"></span> Processando...';

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

                // Verificar se a resposta é JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    throw new Error(`Resposta inválida do servidor: ${text.substring(0, 100)}`);
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
            await finishSimulado();
        });
    </script>
</body>
</html>