<?php
require_once __DIR__ . '/QuestionController.php';

class SimuladoController {
    private $questionController;

    public function __construct() {
        $this->questionController = new QuestionController();
    }

    public function iniciarSimulado($areaIds, $questionCount = 10) {
        try {
            $questions = $this->questionController->getQuestionsByAreas($areaIds);
            
            // Embaralha as questões
            shuffle($questions);
            
            // Limita ao número de questões selecionado
            return array_slice($questions, 0, $questionCount);
        } catch (Exception $e) {
            error_log("Erro ao iniciar simulado: " . $e->getMessage());
            return [];
        }
    }

    public function getAvailableQuestionCounts() {
        return [10, 20, 30, 40, 50, 100];
    }

    public function getAvailableCountdownOptions() {
        return [
            'stopwatch' => 'Cronômetro (padrão)',
            '5' => '5 minutos',
            '10' => '10 minutos',
            '15' => '15 minutos',
            '20' => '20 minutos',
            '30' => '30 minutos',
            '45' => '45 minutos',
            '60' => '1 hora',
            '120' => '2 horas',
            '240' => '4 horas'
        ];
    }
}