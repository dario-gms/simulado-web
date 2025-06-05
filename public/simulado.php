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
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .btn[disabled] {
            opacity: 0.7;
            cursor: not-allowed;
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
                    <div class="option">
                        <input type="radio" name="answer" id="option_<?= $option['id'] ?>" 
                               value="<?= $option['letra'] ?>">
                        <label for="option_<?= $option['id'] ?>">
                            <span class="option-letter"><?= $option['letra'] ?></span>
                            <span class="option-text"><?= htmlspecialchars($option['texto']) ?></span>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </form>
        </main>

        <footer class="simulado-footer">
            <?php if ($currentIndex < $totalQuestions - 1): ?>
                <button id="nextBtn" class="btn btn-primary">Próxima Questão</button>
            <?php else: ?>
                <button id="finishBtn" class="btn btn-finish">Finalizar Simulado</button>
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
                const elapsed = now - <?= $_SESSION['simulado']['start_time'] ?? time() ?>;
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
                // Mostrar estado de carregamento
                const finishBtn = document.getElementById('finishBtn');
                if (finishBtn) {
                    finishBtn.disabled = true;
                    finishBtn.innerHTML = '<span class="spinner"></span> Finalizando...';
                }

                // Finaliza o simulado
                const finishResponse = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'finish_simulado'
                    })
                });

                // Verifica se a resposta é JSON
                const contentType = finishResponse.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await finishResponse.text();
                    throw new Error(`Resposta inválida do servidor: ${text.substring(0, 100)}`);
                }

                const finishData = await finishResponse.json();
                
                if (!finishResponse.ok || !finishData.success) {
                    throw new Error(finishData.message || 'Erro ao finalizar simulado');
                }

                window.location.href = 'resultado.php';
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao finalizar: ' + error.message);
                
                // Restaurar botão
                const finishBtn = document.getElementById('finishBtn');
                if (finishBtn) {
                    finishBtn.disabled = false;
                    finishBtn.innerHTML = 'Finalizar Simulado';
                }
            }
        }

        // Navegação
        document.getElementById('nextBtn')?.addEventListener('click', async () => {
            const selectedOption = document.querySelector('input[name="answer"]:checked');
            
            if (!selectedOption) {
                alert('Selecione uma resposta antes de continuar');
                return;
            }

            try {
                const nextBtn = document.getElementById('nextBtn');
                if (nextBtn) {
                    nextBtn.disabled = true;
                    nextBtn.innerHTML = '<span class="spinner"></span> Carregando...';
                }

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

                const data = await response.json();
                
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Erro ao salvar resposta');
                }

                window.location.href = `simulado.php?index=<?= $currentIndex + 1 ?>`;
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao avançar: ' + error.message);
                
                const nextBtn = document.getElementById('nextBtn');
                if (nextBtn) {
                    nextBtn.disabled = false;
                    nextBtn.innerHTML = 'Próxima Questão';
                }
            }
        });

        document.getElementById('finishBtn')?.addEventListener('click', async () => {
            const selectedOption = document.querySelector('input[name="answer"]:checked');
            
            if (!selectedOption) {
                alert('Selecione uma resposta antes de finalizar');
                return;
            }

            try {
                const finishBtn = document.getElementById('finishBtn');
                if (finishBtn) {
                    finishBtn.disabled = true;
                    finishBtn.innerHTML = '<span class="spinner"></span> Salvando...';
                }

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

                const data = await response.json();
                
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Erro ao salvar resposta');
                }

                await finishSimulado();
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao finalizar: ' + error.message);
                
                const finishBtn = document.getElementById('finishBtn');
                if (finishBtn) {
                    finishBtn.disabled = false;
                    finishBtn.innerHTML = 'Finalizar Simulado';
                }
            }
        });
    </script>
</body>
</html>