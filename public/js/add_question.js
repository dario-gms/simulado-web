document.addEventListener('DOMContentLoaded', () => {
    const questionForm = document.getElementById('questionForm');
    const backBtn = document.getElementById('backBtn');

    questionForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        try {
            // Coletar dados do formulário
            const formData = {
                action: 'add_question',
                enunciado: document.getElementById('enunciado').value.trim(),
                opcoes: {
                    A: document.querySelector('textarea[name="opcoes[A]"]').value.trim(),
                    B: document.querySelector('textarea[name="opcoes[B]"]').value.trim(),
                    C: document.querySelector('textarea[name="opcoes[C]"]').value.trim(),
                    D: document.querySelector('textarea[name="opcoes[D]"]').value.trim(),
                    E: document.querySelector('textarea[name="opcoes[E]"]').value.trim()
                },
                resposta_correta: document.getElementById('correctAnswer').value,
                area_id: document.getElementById('area').value
            };

            // Validar campos obrigatórios
            if (!formData.enunciado) {
                throw new Error('O enunciado é obrigatório');
            }

            if (!formData.resposta_correta) {
                throw new Error('Selecione a resposta correta');
            }

            if (!formData.area_id) {
                throw new Error('Selecione a área de conhecimento');
            }

            // Validar opções
            for (const [key, value] of Object.entries(formData.opcoes)) {
                if (!value) {
                    throw new Error(`A opção ${key} está vazia`);
                }
            }

            // Mostrar loading
            const submitBtn = questionForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            submitBtn.disabled = true;

            // Enviar para a API
            const response = await fetch('api.php', {
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

            // Sucesso
            alert('Questão adicionada com sucesso!');
            questionForm.reset();

        } catch (error) {
            console.error('Erro:', error);
            alert('Erro: ' + error.message);
        } finally {
            // Restaurar botão
            const submitBtn = questionForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Adicionar Questão';
                submitBtn.disabled = false;
            }
        }
    });

    backBtn.addEventListener('click', () => {
        window.location.href = 'index.php';
    });
});