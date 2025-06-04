document.addEventListener('DOMContentLoaded', () => {
    const questionForm = document.getElementById('questionForm');
    const backBtn = document.getElementById('backBtn');
    const submitBtn = questionForm.querySelector('button[type="submit"]');

    questionForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Mostrar estado de carregamento
        const originalBtnContent = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        submitBtn.disabled = true;

        try {
            // Criar FormData a partir do formulário
            const formData = new FormData(questionForm);

            // Validação dos campos
            const enunciado = formData.get('enunciado').trim();
            const resposta_correta = formData.get('resposta_correta');
            const area_id = formData.get('area_id');

            if (!enunciado) {
                throw new Error('O enunciado é obrigatório');
            }

            if (!resposta_correta) {
                throw new Error('Selecione a resposta correta');
            }

            if (!area_id) {
                throw new Error('Selecione a área de conhecimento');
            }

            // Validar opções
            const opcoes = {
                A: formData.get('opcoes[A]').trim(),
                B: formData.get('opcoes[B]').trim(),
                C: formData.get('opcoes[C]').trim(),
                D: formData.get('opcoes[D]').trim(),
                E: formData.get('opcoes[E]').trim()
            };

            for (const [letra, texto] of Object.entries(opcoes)) {
                if (!texto) {
                    throw new Error(`A opção ${letra} está vazia`);
                }
            }

            // Enviar para a API
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData // Usar FormData diretamente
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Erro ao adicionar questão');
            }

            // Feedback visual de sucesso
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Questão Adicionada!';
            submitBtn.classList.add('btn-success');

            // Resetar formulário após 1.5 segundos
            setTimeout(() => {
                questionForm.reset();
                submitBtn.innerHTML = originalBtnContent;
                submitBtn.classList.remove('btn-success');
            }, 1500);

        } catch (error) {
            console.error('Erro:', error);

            // Feedback visual de erro
            submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Erro';
            submitBtn.classList.add('btn-error');

            // Mostrar mensagem de erro
            const errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.textContent = error.message;

            // Inserir após o botão
            submitBtn.parentNode.insertBefore(errorElement, submitBtn.nextSibling);

            // Restaurar botão após 3 segundos
            setTimeout(() => {
                submitBtn.innerHTML = originalBtnContent;
                submitBtn.classList.remove('btn-error');
                errorElement.remove();
            }, 3000);
        } finally {
            // Garantir que o botão seja reativado
            submitBtn.disabled = false;
        }
    });

    backBtn.addEventListener('click', () => {
        window.location.href = 'index.php';
    });
});