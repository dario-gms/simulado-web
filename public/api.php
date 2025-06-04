<?php
require_once __DIR__ . '/../src/controllers/AreaController.php';
require_once __DIR__ . '/../src/controllers/QuestionController.php';
require_once __DIR__ . '/../src/controllers/SimuladoController.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/UserStatsController.php';

// Inicia a sessão e configura o cabeçalho JSON
session_start();
header('Content-Type: application/json');

// Função para padronizar as respostas
function jsonResponse($success, $message = '', $data = []) {
    http_response_code($success ? 200 : 400);
    return json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
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
            echo jsonResponse(false, 'Acesso não autorizado');
            exit;
        }
    }

    // Verifica se a ação requer privilégios de admin
    $adminActions = ['add_question', 'get_all_users', 'get_all_stats'];
    if (in_array($action, $adminActions)) {
        if (empty($_SESSION['user']['is_admin'])) {
            echo jsonResponse(false, 'Acesso restrito a administradores');
            exit;
        }
    }

    switch ($action) {
        case 'register':
            $required = ['username', 'email', 'password'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    echo jsonResponse(false, "Campo {$field} é obrigatório");
                    exit;
                }
            }

            $authController = new AuthController();
            $userId = $authController->register(
                trim($input['username']),
                trim($input['email']),
                $input['password']
            );

            echo jsonResponse(true, 'Usuário registrado com sucesso', ['user_id' => $userId]);
            break;

        case 'login':
            $required = ['username', 'password'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    echo jsonResponse(false, "Campo {$field} é obrigatório");
                    exit;
                }
            }

            $authController = new AuthController();
            $user = $authController->login(trim($input['username']), $input['password']);
            
            $_SESSION['user'] = $user;
            echo jsonResponse(true, 'Login realizado com sucesso', ['user' => $user]);
            break;

        case 'logout':
            session_destroy();            
            echo jsonResponse(true, 'Logout realizado com sucesso');
            exit;    
            break;

        case 'check_auth':
            if (!empty($_SESSION['user'])) {
                echo jsonResponse(true, 'Usuário autenticado', ['user' => $_SESSION['user']]);
            } else {
                echo jsonResponse(false, 'Usuário não autenticado');
            }
            break;

        case 'add_question':
            $required = ['enunciado', 'opcoes', 'resposta_correta', 'area_id'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    echo jsonResponse(false, "Campo {$field} é obrigatório");
                    exit;
                }
            }
            $imagem = $_FILES['question_image'] ?? null;
            $questionController = new QuestionController();
            $result = $questionController->addQuestion(
                $input['enunciado'],
                $input['opcoes'],
                $input['resposta_correta'],
                $input['area_id'],
                $input['explicacao'] ?? null,
                $imagem
            );

            echo jsonResponse($result, $result ? 'Questão adicionada!' : 'Erro ao adicionar questão');
            break;

        case 'start_simulado':
            if (empty($input['areas'])) {
                echo jsonResponse(false, 'Selecione pelo menos uma área');
                exit;
            }

            // Verifica se é modo imediato
            $immediate_mode = isset($input['immediate_mode']) ? (bool)$input['immediate_mode'] : false;

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
                'start_time' => time(),
                'selected_areas' => $input['areas'],
                'immediate_mode' => $immediate_mode
            ];

            echo jsonResponse(true, 'Simulado iniciado', [
                'total_questions' => count($questions),
                'immediate_mode' => $immediate_mode
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

            $_SESSION['simulado_result'] = [
                'score' => $score,
                'total' => count($_SESSION['simulado']['questions']),
                'time' => time() - $_SESSION['simulado']['start_time'],
                'areas' => $_SESSION['simulado']['selected_areas'],
                'area_stats' => $areaStats
            ];

            echo jsonResponse(true, 'Simulado finalizado');
            break;

        case 'get_user_stats':
            $userId = $_SESSION['user']['id'];
            $statsController = new UserStatsController();
            $stats = $statsController->getUserStats($userId);
            
            echo jsonResponse(true, 'Estatísticas recuperadas', ['stats' => $stats]);
            break;

        case 'get_all_users':
            $authController = new AuthController();
            $users = $authController->getAllUsers();
            
            echo jsonResponse(true, 'Usuários recuperados', ['users' => $users]);
            break;

        case 'get_all_stats':
            $statsController = new UserStatsController();
            $stats = $statsController->getAllUsersStats();
            
            echo jsonResponse(true, 'Estatísticas recuperadas', ['stats' => $stats]);
            break;

        default:
            echo jsonResponse(false, 'Ação inválida: ' . $action);
    }
} catch (Exception $e) {
    error_log('Erro na API: ' . $e->getMessage());
    echo jsonResponse(false, 'Erro no servidor: ' . $e->getMessage());
}