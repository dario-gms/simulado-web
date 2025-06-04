document.addEventListener('DOMContentLoaded', () => {
    const questionForm = document.getElementById('questionForm');
    const backBtn = document.getElementById('backBtn');
    const submitBtn = questionForm.querySelector('button[type="submit"]');
    const imageUpload = document.querySelector('.image-upload');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const removeImageBtn = document.getElementById('removeImageBtn');
    const imageUploadArea = document.querySelector('.image-upload-area');

    // Formatos suportados (incluindo os novos)
    const supportedFormats = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        'image/tiff',
        'image/heic',
        'image/heif',
        'image/avif'
    ];

    // Configurar eventos de drag and drop
    imageUploadArea.addEventListener('click', () => imageUpload.click());
    imageUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        imageUploadArea.classList.add('dragover');
    });
    imageUploadArea.addEventListener('dragleave', () => {
        imageUploadArea.classList.remove('dragover');
    });
    imageUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        imageUploadArea.classList.remove('dragover');

        if (e.dataTransfer.files.length) {
            const file = e.dataTransfer.files[0];
            if (validateImageFile(file)) {
                imageUpload.files = e.dataTransfer.files;
                const event = new Event('change');
                imageUpload.dispatchEvent(event);
            }
        }
    });

    // Função para validar arquivo de imagem
    function validateImageFile(file) {
        // Verificar se é um tipo de imagem suportado
        if (!supportedFormats.includes(file.type)) {
            alert('Formato de imagem não suportado. Formatos aceitos: JPEG, PNG, GIF, WEBP, BMP, TIFF, HEIC, HEIF, AVIF');
            return false;
        }

        // Verificar tamanho (5MB máximo)
        if (file.size > 5 * 1024 * 1024) {
            alert('A imagem deve ter menos de 5MB');
            return false;
        }

        return true;
    }

    // Configurar preview de imagem
    imageUpload.addEventListener('change', function (e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];

            if (!validateImageFile(file)) {
                this.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreviewContainer.style.display = 'block';
            }

            reader.onerror = function () {
                alert('Erro ao ler o arquivo de imagem');
                imageUpload.value = '';
            };

            reader.readAsDataURL(file);
        }
    });

    // Remover imagem
    removeImageBtn.addEventListener('click', function () {
        imageUpload.value = '';
        imagePreview.src = '#';
        imagePreviewContainer.style.display = 'none';
    });

    // Envio do formulário
    questionForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Mostrar estado de carregamento
        const originalBtnContent = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        submitBtn.disabled = true;

        try {
            // Criar FormData e configurar ação
            const formData = new FormData(questionForm);
            formData.set('action', 'add_question');

            // Coletar e validar opções corretamente
            const opcoes = {
                A: document.querySelector('textarea[name="opcoes[A]"]').value.trim(),
                B: document.querySelector('textarea[name="opcoes[B]"]').value.trim(),
                C: document.querySelector('textarea[name="opcoes[C]"]').value.trim(),
                D: document.querySelector('textarea[name="opcoes[D]"]').value.trim(),
                E: document.querySelector('textarea[name="opcoes[E]"]').value.trim()
            };

            // Adicionar opções ao FormData
            for (const [key, value] of Object.entries(opcoes)) {
                formData.set(`opcoes[${key}]`, value);
            }

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
            for (const [letra, texto] of Object.entries(opcoes)) {
                if (!texto) {
                    throw new Error(`A opção ${letra} está vazia`);
                }
            }

            // Verificar se há imagem e se é válida
            if (imageUpload.files.length > 0) {
                const imageFile = imageUpload.files[0];
                if (!validateImageFile(imageFile)) {
                    throw new Error('Imagem inválida');
                }
            }

            // Enviar para a API
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
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
                imagePreview.src = '#';
                imagePreviewContainer.style.display = 'none';

                // Redirecionar ou fazer outra ação se necessário
                if (result.redirect) {
                    window.location.href = result.redirect;
                }
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