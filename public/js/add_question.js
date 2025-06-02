document.addEventListener('DOMContentLoaded', () => {
    const questionForm = document.getElementById('questionForm');
    const backBtn = document.getElementById('backBtn');

    questionForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        try {
            // Coletar dados do formulário de forma segura
            const formData = {
                action: 'add_question',
                enunciado: document.getElementById('enunciado').value.trim(),
                opcoes: {
                    A: document.querySelector('input[name="opcoes[A]"]').value.trim(),
                    B: document.querySelector('input[name="opcoes[B]"]').value.trim(),
                    C: document.querySelector('input[name="opcoes[C]"]').value.trim(),
                    D: document.querySelector('input[name="opcoes[D]"]').value.trim(),
                    E: document.querySelector('input[name="opcoes[E]"]').value.trim()
                },
                resposta_correta: document.getElementById('correctAnswer').value,
                area_id: document.getElementById('area').value
            };

            // Validar campos obrigatórios
            if (!formData.enunciado || !formData.resposta_correta || !formData.area_id) {
                throw new Error('Preencha todos os campos obrigatórios');
            }

            // Validar opções
            for (const [key, value] of Object.entries(formData.opcoes)) {
                if (!value) {
                    throw new Error(`Preencha a opção ${key}`);
                }
            }

            // Enviar para a API
            const response = await fetch('http://localhost/simulado/public/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Erro ao adicionar questão');
            }

            alert('Questão adicionada com sucesso!');
            questionForm.reset();
        } catch (error) {
            console.error('Erro:', error);
            alert('Falha ao adicionar: ' + error.message);
        }
    });

    backBtn.addEventListener('click', () => {
        window.location.href = 'index.php';
    });
});