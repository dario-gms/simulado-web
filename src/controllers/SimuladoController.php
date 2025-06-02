<?php
require_once __DIR__ . '/QuestionController.php';

class SimuladoController {
    private $questionController;

    public function __construct() {
        $this->questionController = new QuestionController();
    }

    public function iniciarSimulado($areaIds) {
        try {
            $questions = $this->questionController->getQuestionsByAreas($areaIds);
            
            // Embaralha as questões
            shuffle($questions);
            
            // Limita a 10 questões para o simulado
            return array_slice($questions, 0, 10);
        } catch (Exception $e) {
            error_log("Erro ao iniciar simulado: " . $e->getMessage());
            return [];
        }
    }
}