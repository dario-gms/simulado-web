document.addEventListener('DOMContentLoaded', () => {
    const timerElement = document.getElementById('timer');
    const answerForm = document.getElementById('answerForm');
    const nextBtn = document.getElementById('nextBtn');
    const finishBtn = document.getElementById('finishBtn');

    let startTime = new Date();
    let timerInterval;

    // Iniciar cronômetro
    function startTimer() {
        timerInterval = setInterval(() => {
            const now = new Date();
            const diff = new Date(now - startTime);

            const hours = diff.getUTCHours().toString().padStart(2, '0');
            const minutes = diff.getUTCMinutes().toString().padStart(2, '0');
            const seconds = diff.getUTCSeconds().toString().padStart(2, '0');

            timerElement.textContent = `${hours}:${minutes}:${seconds}`;
        }, 1000);
    }

    startTimer();

    // Registrar resposta
    answerForm?.addEventListener('change', (e) => {
        const questionId = answerForm.dataset.questionId;
        const answer = e.target.value;

        // Salvar resposta temporariamente
        if (!sessionStorage.getItem('answers')) {
            sessionStorage.setItem('answers', JSON.stringify({}));
        }

        const answers = JSON.parse(sessionStorage.getItem('answers'));
        answers[questionId] = {
            resposta: answer,
            tempo_gasto: timerElement.textContent
        };

        sessionStorage.setItem('answers', JSON.stringify(answers));
    });

    // Navegação entre questões
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const currentIndex = parseInt(urlParams.get('index'));
            window.location.href = `simulado.php?index=${currentIndex + 1}`;
        });
    }

    // Finalizar simulado
    if (finishBtn) {
        finishBtn.addEventListener('click', () => {
            clearInterval(timerInterval);

            const answers = JSON.parse(sessionStorage.getItem('answers')) || {};
            const questions = <?= json_encode($_SESSION['questions']) ?>;

            // Calcular resultados
            const result = {
                tempo_total: timerElement.textContent,
                pontuacao: 0,
                respostas: []
            };

            let correctCount = 0;

            questions.forEach(question => {
                const answer = answers[question.id] || { resposta: '', tempo_gasto: '00:00:00' };
                const isCorrect = answer.resposta === question.resposta_correta;

                if (isCorrect) correctCount++;

                result.respostas.push({
                    question_id: question.id,
                    resposta: answer.resposta,
                    acertou: isCorrect,
                    tempo_gasto: answer.tempo_gasto
                });
            });

            result.pontuacao = Math.round((correctCount / questions.length) * 1000);

            // Enviar resultados para o servidor
            fetch('api.php?action=finish_simulado', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        sessionStorage.removeItem('answers');
                        window.location.href = 'resultado.php';
                    } else {
                        alert('Erro ao salvar resultado: ' + data.message);
                    }
                });
        });
    }
});