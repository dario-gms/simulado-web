<?php
require_once __DIR__ . '/../src/controllers/AreaController.php';
require_once __DIR__ . '/../src/controllers/QuestionController.php';
require_once __DIR__ . '/../src/controllers/SimuladoController.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/UserStatsController.php';
require_once __DIR__ . '/../src/controllers/SimuladoResultsController.php';

// Configuração para evitar exibição de erros na resposta
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Inicia a sessão e configura o cabeçalho JSON
session_start();
header('Content-Type: application/json');

// Função para padronizar as respostas
function jsonResponse($success, $message = '', $data = []) {
    http_response_code($success ? 200 : 400);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Obtém os dados de entrada
    $input = json_decode(file_get_contents('php://input'), true) ?? $_REQUEST;
    $action = $input['action'] ?? '';
    
    // Verifica se a ação requer autenticação
    $protectedActions = [
        'start_simulado', 'submit_answer', 'finish_simulado', 
        'get_user_stats', 'add_question', 'get_all_users', 'get_all_stats'
    ];
    
    if (in_array($action, $protectedActions)) {
        if (empty($_SESSION['user'])) {
            jsonResponse(false, 'Acesso não autorizado');
        }
    }

    // Verifica se a ação requer privilégios de admin
    $adminActions = ['add_question', 'get_all_users', 'get_all_stats'];
    if (in_array($action, $adminActions)) {
        if (empty($_SESSION['user']['is_admin'])) {
            jsonResponse(false, 'Acesso restrito a administradores');
        }
    }

    switch ($action) {
        case 'register':
            $required = ['username', 'email', 'password'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    jsonResponse(false, "Campo {$field} é obrigatório");
                }
            }

            $authController = new AuthController();
            $userId = $authController->register(
                trim($input['username']),
                trim($input['email']),
                $input['password']
            );

            jsonResponse(true, 'Usuário registrado com sucesso', ['user_id' => $userId]);
            break;

        case 'login':
            $required = ['username', 'password'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    jsonResponse(false, "Campo {$field} é obrigatório");
                }
            }

            $authController = new AuthController();
            $user = $authController->login(trim($input['username']), $input['password']);
            
            $_SESSION['user'] = $user;
            jsonResponse(true, 'Login realizado com sucesso', ['user' => $user]);
            break;

        case 'logout':
            session_destroy();            
            jsonResponse(true, 'Logout realizado com sucesso');
            break;

        case 'check_auth':
            if (!empty($_SESSION['user'])) {
                jsonResponse(true, 'Usuário autenticado', ['user' => $_SESSION['user']]);
            } else {
                jsonResponse(false, 'Usuário não autenticado');
            }
            break;

        case 'add_question':
            $required = ['enunciado', 'opcoes', 'resposta_correta', 'area_id'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    jsonResponse(false, "Campo {$field} é obrigatório");
                }
            }

            // Handle file upload correctly
            $imagem = null;
            if (isset($_FILES['question_image']) && $_FILES['question_image']['error'] === UPLOAD_ERR_OK) {
                $imagem = $_FILES['question_image'];
            }

            // Convert options to proper array format
            $opcoesArray = [];
            foreach (['A', 'B', 'C', 'D', 'E'] as $letra) {
                if (!isset($input['opcoes'][$letra])) {
                    jsonResponse(false, "Opção {$letra} é obrigatória");
                }
                $opcoesArray[$letra] = $input['opcoes'][$letra];
            }

            $questionController = new QuestionController();
            $result = $questionController->addQuestion(
                $input['enunciado'],
                $opcoesArray,
                $input['resposta_correta'],
                $input['area_id'],
                $input['explicacao'] ?? null,
                $imagem
            );

            jsonResponse($result, $result ? 'Questão adicionada!' : 'Erro ao adicionar questão');
            break;

        case 'start_simulado':
            if (empty($input['areas'])) {
                jsonResponse(false, 'Selecione pelo menos uma área');
            }

            // Verifica se é modo imediato
            $immediate_mode = isset($input['immediate_mode']) ? (bool)$input['immediate_mode'] : false;
            
            // Obtém a quantidade de questões selecionada
            $questionCount = isset($input['question_count']) ? (int)$input['question_count'] : 10;
            $timerMode = $input['timer_mode'] ?? 'stopwatch';
            $countdownDuration = ($timerMode !== 'stopwatch') ? (int)$timerMode : null;

            $simuladoController = new SimuladoController();
            $questions = $simuladoController->iniciarSimulado($input['areas'], $questionCount);
            
            if (empty($questions)) {
                jsonResponse(false, 'Nenhuma questão encontrada');
            }

            $_SESSION['simulado'] = [
                'questions' => $questions,
                'current_index' => 0,
                'answers' => [],
                'start_time' => time(),
                'selected_areas' => $input['areas'],
                'immediate_mode' => $immediate_mode,
                'question_count' => $questionCount,
                'timer_mode' => $timerMode,
                'countdown_duration' => $countdownDuration,
                'countdown_end' => $countdownDuration ? time() + ($countdownDuration * 60) : null
            ];

            jsonResponse(true, 'Simulado iniciado', [
                'total_questions' => count($questions),
                'immediate_mode' => $immediate_mode,
                'timer_mode' => $timerMode,
                'countdown_duration' => $countdownDuration
            ]);
            break;

        case 'submit_answer':
            if (empty($_SESSION['simulado'])) {
                jsonResponse(false, 'Nenhum simulado em andamento');
            }

            $questionId = $input['question_id'] ?? null;
            $answer = $input['answer'] ?? null;

            if (!$questionId || !$answer) {
                jsonResponse(false, 'Dados incompletos');
            }

            $_SESSION['simulado']['answers'][$questionId] = $answer;
            jsonResponse(true, 'Resposta registrada');
            break;

        case 'finish_simulado':
            if (empty($_SESSION['simulado'])) {
                jsonResponse(false, 'Nenhum simulado em andamento');
            }

            $score = 0;
            $areaStats = [];
            
            foreach ($_SESSION['simulado']['answers'] as $qId => $userAnswer) {
                foreach ($_SESSION['simulado']['questions'] as $q) {
                    if ($q['id'] == $qId) {
                        $isCorrect = $userAnswer == $q['resposta_correta'];
                        if ($isCorrect) {
                            $score++;
                        }
                        
                        // Atualiza estatísticas por área
                        if (!isset($areaStats[$q['area_id']])) {
                            $areaStats[$q['area_id']] = ['total' => 0, 'correct' => 0];
                        }
                        $areaStats[$q['area_id']]['total']++;
                        if ($isCorrect) {
                            $areaStats[$q['area_id']]['correct']++;
                        }
                    }
                }
            }

            // Para questões não respondidas (em caso de contagem regressiva)
            if ($_SESSION['simulado']['timer_mode'] !== 'stopwatch') {
                foreach ($_SESSION['simulado']['questions'] as $q) {
                    if (!isset($_SESSION['simulado']['answers'][$q['id']])) {
                        // Conta como erro
                        if (!isset($areaStats[$q['area_id']])) {
                            $areaStats[$q['area_id']] = ['total' => 0, 'correct' => 0];
                        }
                        $areaStats[$q['area_id']]['total']++;
                    }
                }
            }

            // Salva estatísticas no banco de dados
            $statsController = new UserStatsController();
            $userId = $_SESSION['user']['id'];
            
            foreach ($areaStats as $areaId => $stats) {
                $statsController->updateUserStats(
                    $userId,
                    $areaId,
                    $stats['correct'] / $stats['total'] > 0.5
                );
            }

            // Salva o resultado completo do simulado
            $resultsController = new SimuladoResultsController();
            $saveResult = $resultsController->saveResult(
                $userId,
                $_SESSION['simulado']['selected_areas'],
                count($_SESSION['simulado']['questions']),
                $score,
                time() - $_SESSION['simulado']['start_time'],
                $areaStats,
                $_SESSION['simulado']['question_count'],
                $_SESSION['simulado']['timer_mode'],
                $_SESSION['simulado']['countdown_duration']
            );

            if (!$saveResult) {
                jsonResponse(false, 'Erro ao salvar resultado do simulado');
            }

            $_SESSION['simulado_result'] = [
                'score' => $score,
                'total' => count($_SESSION['simulado']['questions']),
                'time' => time() - $_SESSION['simulado']['start_time'],
                'areas' => $_SESSION['simulado']['selected_areas'],
                'area_stats' => $areaStats,
                'question_count' => $_SESSION['simulado']['question_count'],
                'timer_mode' => $_SESSION['simulado']['timer_mode'],
                'countdown_duration' => $_SESSION['simulado']['countdown_duration']
            ];

            // Limpa o simulado da sessão
            unset($_SESSION['simulado']);

            jsonResponse(true, 'Simulado finalizado');
            break;

        case 'get_user_stats':
            $userId = $_SESSION['user']['id'];
            $statsController = new UserStatsController();
            $stats = $statsController->getUserStats($userId);
            
            jsonResponse(true, 'Estatísticas recuperadas', ['stats' => $stats]);
            break;

        case 'get_all_users':
            $authController = new AuthController();
            $users = $authController->getAllUsers();
            
            jsonResponse(true, 'Usuários recuperados', ['users' => $users]);
            break;

        case 'get_all_stats':
            $statsController = new UserStatsController();
            $stats = $statsController->getAllUsersStats();
            
            jsonResponse(true, 'Estatísticas recuperadas', ['stats' => $stats]);
            break;

        default:
            jsonResponse(false, 'Ação inválida: ' . $action);
    }
} catch (Exception $e) {
    error_log('Erro na API: ' . $e->getMessage());
    jsonResponse(false, 'Erro no servidor');
}