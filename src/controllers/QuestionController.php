<?php
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Option.php';
require_once __DIR__ . '/../config/database.php';

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

    public function addQuestion($enunciado, $opcoes, $resposta_correta, $area_id) {
        try {
            if (empty($enunciado) || empty($opcoes) || empty($resposta_correta) || empty($area_id)) {
                throw new Exception("Todos os campos são obrigatórios");
            }

            $this->questionModel->enunciado = $enunciado;
            $this->questionModel->resposta_correta = $resposta_correta;
            $this->questionModel->area_id = $area_id;
            
            if ($this->questionModel->create()) {
                foreach ($opcoes as $letra => $texto) {
                    $this->optionModel->question_id = $this->questionModel->id;
                    $this->optionModel->letra = $letra;
                    $this->optionModel->texto = $texto;
                    
                    if (!$this->optionModel->create()) {
                        throw new Exception("Falha ao criar opção {$letra}");
                    }
                }
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Erro ao adicionar questão: " . $e->getMessage());
            return false;
        }
    }

    public function getQuestionsByAreas($area_ids) {
        try {
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
            error_log("Erro ao buscar questões: " . $e->getMessage());
            return [];
        }
    }

    public function getQuestionById($id) {
        try {
            $question = $this->questionModel->getById($id);
            if ($question) {
                $optionsStmt = $this->optionModel->getByQuestion($id);
                $question['opcoes'] = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
            }
            return $question;
        } catch (Exception $e) {
            error_log("Erro ao buscar questão: " . $e->getMessage());
            return null;
        }
    }
}
?>