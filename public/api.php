<?php
require_once __DIR__ . '/../src/controllers/AreaController.php';
require_once __DIR__ . '/../src/controllers/QuestionController.php';
require_once __DIR__ . '/../src/controllers/SimuladoController.php';

session_start();
header('Content-Type: application/json');

function jsonResponse($success, $message = '', $data = []) {
    return json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
}

try {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? '';

    switch ($action) {
        case 'add_question':
            $required = ['enunciado', 'opcoes', 'resposta_correta', 'area_id'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    echo jsonResponse(false, "Campo {$field} é obrigatório");
                    exit;
                }
            }

            $questionController = new QuestionController();
            $result = $questionController->addQuestion(
                $input['enunciado'],
                $input['opcoes'],
                $input['resposta_correta'],
                $input['area_id']
            );

            echo jsonResponse($result, $result ? 'Questão adicionada!' : 'Erro ao adicionar questão');
            break;

        case 'start_simulado':
            if (empty($input['areas'])) {
                echo jsonResponse(false, 'Selecione pelo menos uma área');
                exit;
            }

            $simuladoController = new SimuladoController();
            $questions = $simuladoController->iniciarSimulado($input['areas']);
            
            if (empty($questions)) {
                echo jsonResponse(false, 'Nenhuma questão encontrada');
                exit;
            }

            $_SESSION['simulado'] = [
                'questions' => $questions,
                'current_index' => 0,
                'answers' => [],
                'start_time' => time()
            ];

            echo jsonResponse(true, 'Simulado iniciado', [
                'total_questions' => count($questions)
            ]);
            break;

        case 'submit_answer':
            if (empty($_SESSION['simulado'])) {
                echo jsonResponse(false, 'Nenhum simulado em andamento');
                exit;
            }

            $questionId = $input['question_id'] ?? null;
            $answer = $input['answer'] ?? null;

            if (!$questionId || !$answer) {
                echo jsonResponse(false, 'Dados incompletos');
                exit;
            }

            $_SESSION['simulado']['answers'][$questionId] = $answer;
            echo jsonResponse(true, 'Resposta registrada');
            break;

        case 'finish_simulado':
            if (empty($_SESSION['simulado'])) {
                echo jsonResponse(false, 'Nenhum simulado em andamento');
                exit;
            }

            $score = 0;
            foreach ($_SESSION['simulado']['answers'] as $qId => $userAnswer) {
                foreach ($_SESSION['simulado']['questions'] as $q) {
                    if ($q['id'] == $qId && $userAnswer == $q['resposta_correta']) {
                        $score++;
                    }
                }
            }

            $_SESSION['simulado_result'] = [
                'score' => $score,
                'total' => count($_SESSION['simulado']['questions']),
                'time' => time() - $_SESSION['simulado']['start_time']
            ];

            echo jsonResponse(true, 'Simulado finalizado');
            break;

        default:
            echo jsonResponse(false, 'Ação inválida: ' . $action);
    }
} catch (Exception $e) {
    echo jsonResponse(false, 'Erro no servidor: ' . $e->getMessage());
}
?>