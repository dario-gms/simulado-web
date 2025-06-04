<?php
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Option.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ImageController.php';

class QuestionController {
    private $db;
    private $questionModel;
    private $optionModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->questionModel = new Question($this->db);
        $this->optionModel = new Option($this->db);
    }

    public function addQuestion($enunciado, $opcoes, $resposta_correta, $area_id, $explicacao = null, $imagem = null) {
        try {
            // Inicia transação
            $this->db->beginTransaction();

            // Validações
            if (empty($enunciado) || empty($opcoes) || empty($resposta_correta) || empty($area_id)) {
                throw new Exception("Todos os campos obrigatórios devem ser preenchidos");
            }

            if (!in_array($resposta_correta, ['A', 'B', 'C', 'D', 'E'])) {
                throw new Exception("Resposta correta inválida");
            }

            // Configura a questão
            $this->questionModel->enunciado = $enunciado;
            $this->questionModel->resposta_correta = $resposta_correta;
            $this->questionModel->area_id = $area_id;
            $this->questionModel->explicacao = $explicacao;
            
            // Cria a questão principal
            if (!$this->questionModel->create()) {
                throw new Exception("Falha ao criar a questão no banco de dados");
            }
            if ($imagem && $imagem['error'] === UPLOAD_ERR_OK) {
                $imageController = new ImageController();
                if ($imageController->processAndSaveImage($imagem, $this->questionModel->id)) {
            // Atualizar flag de imagem
                    $query = "UPDATE questions SET has_image = 1 WHERE id = :id";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':id', $this->questionModel->id);
                    $stmt->execute();
                }
            }

            // Cria as opções
            foreach ($opcoes as $letra => $texto) {
                $this->optionModel->question_id = $this->questionModel->id;
                $this->optionModel->letra = $letra;
                $this->optionModel->texto = $texto;
                
                if (!$this->optionModel->create()) {
                    throw new Exception("Falha ao criar a opção {$letra}");
                }
            }

            // Commit da transação
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Rollback em caso de erro
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            error_log("Erro no QuestionController: " . $e->getMessage());
            throw $e; // Re-lança a exceção para ser tratada pelo chamador
        }
    }

    public function getQuestionsByAreas($area_ids) {
        try {
            if (empty($area_ids)) {
                throw new Exception("Nenhuma área especificada");
            }

            $stmt = $this->questionModel->getByAreas($area_ids);
            $questions = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $question = $row;
                $optionsStmt = $this->optionModel->getByQuestion($question['id']);
                $question['opcoes'] = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
                $questions[] = $question;
            }
            
            return $questions;
        } catch (Exception $e) {
            error_log("Erro ao buscar questões por área: " . $e->getMessage());
            throw $e;
        }
    }

    public function getQuestionById($id) {
        try {
            if (empty($id)) {
                throw new Exception("ID da questão não fornecido");
            }

            $question = $this->questionModel->getById($id);
            
            if (!$question) {
                throw new Exception("Questão não encontrada");
            }

            $optionsStmt = $this->optionModel->getByQuestion($id);
            $question['opcoes'] = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $question;
        } catch (Exception $e) {
            error_log("Erro ao buscar questão por ID: " . $e->getMessage());
            throw $e;
        }
    }

    public function getQuestionsCount() {
        try {
            $query = "SELECT COUNT(*) as count FROM questions";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Erro ao contar questões: " . $e->getMessage());
            return 0;
        }
    }
}