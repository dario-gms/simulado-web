<?php
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Option.php';
require_once __DIR__ . '/../models/QuestionImage.php';
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

            // Cria as opções
            foreach ($opcoes as $letra => $texto) {
                $option = new Option($this->db);
                $option->question_id = $this->questionModel->id;
                $option->letra = $letra;
                $option->texto = $texto;
                
                if (!$option->create()) {
                    throw new Exception("Falha ao criar a opção {$letra}");
                }
            }

            // Processa imagem se existir
            if ($imagem && $imagem['error'] === UPLOAD_ERR_OK) {
                $imageModel = new QuestionImage($this->db);
                $imageController = new ImageController();
                $imageData = $imageController->processImage($imagem['tmp_name']);
                
                if (!$imageModel->saveImage($this->questionModel->id, $imageData)) {
                    throw new Exception("Falha ao salvar imagem");
                }
                
                // Atualiza flag de imagem
                $query = "UPDATE questions SET has_image = 1 WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':id', $this->questionModel->id);
                $stmt->execute();
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

            // Cria placeholders para a cláusula IN
            $placeholders = implode(',', array_fill(0, count($area_ids), '?'));
            
            $query = "SELECT q.*, qi.image_data 
                      FROM questions q
                      LEFT JOIN question_images qi ON q.id = qi.question_id
                      WHERE q.area_id IN ($placeholders)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($area_ids);
            
            $questions = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $question = $row;
                $optionsStmt = $this->optionModel->getByQuestion($question['id']);
                $question['opcoes'] = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
                $question['has_image'] = !empty($row['image_data']);
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

            $query = "SELECT q.*, qi.image_data 
                      FROM questions q
                      LEFT JOIN question_images qi ON q.id = qi.question_id
                      WHERE q.id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $question = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$question) {
                throw new Exception("Questão não encontrada");
            }

            $optionsStmt = $this->optionModel->getByQuestion($id);
            $question['opcoes'] = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
            $question['has_image'] = !empty($question['image_data']);
            
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