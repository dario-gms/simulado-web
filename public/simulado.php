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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado - Questão <?= $currentIndex + 1 ?></title>
    <link rel="stylesheet" href="css/style.css">
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
        // Timer
        const startTime = <?= $_SESSION['simulado']['start_time'] ?? time() ?>;
        function updateTimer() {
            const now = Math.floor(Date.now() / 1000);
            const elapsed = now - startTime;
            const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
            const seconds = (elapsed % 60).toString().padStart(2, '0');
            document.getElementById('timer').textContent = `${minutes}:${seconds}`;
        }
        setInterval(updateTimer, 1000);
        updateTimer();

        // Navegação
        document.getElementById('nextBtn')?.addEventListener('click', async () => {
            const selectedOption = document.querySelector('input[name="answer"]:checked');
            
            if (!selectedOption) {
                alert('Selecione uma resposta antes de continuar');
                return;
            }

            try {
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
            }
        });

        document.getElementById('finishBtn')?.addEventListener('click', async () => {
            const selectedOption = document.querySelector('input[name="answer"]:checked');
            
            if (!selectedOption) {
                alert('Selecione uma resposta antes de finalizar');
                return;
            }

            try {
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

                const finishData = await finishResponse.json();
                
                if (!finishResponse.ok || !finishData.success) {
                    throw new Error(finishData.message || 'Erro ao finalizar simulado');
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