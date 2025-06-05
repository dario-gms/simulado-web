<?php
require_once __DIR__ . '/../config/database.php';

class SimuladoResultsController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function saveResult($user_id, $areas, $total_questions, $correct_answers, $time_spent, $area_stats, $question_count, $timer_mode, $countdown_duration = null) {
        try {
            $this->db->beginTransaction();

            // Salva o resultado geral do simulado
            $query = "INSERT INTO simulado_results 
                      (user_id, areas, total_questions, correct_answers, time_spent, 
                       question_count, timer_mode, countdown_duration)
                      VALUES (:user_id, :areas, :total_questions, :correct_answers, :time_spent,
                              :question_count, :timer_mode, :countdown_duration)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":areas", json_encode($areas));
            $stmt->bindParam(":total_questions", $total_questions);
            $stmt->bindParam(":correct_answers", $correct_answers);
            $stmt->bindParam(":time_spent", $time_spent);
            $stmt->bindParam(":question_count", $question_count);
            $stmt->bindParam(":timer_mode", $timer_mode);
            $stmt->bindParam(":countdown_duration", $countdown_duration);
            $stmt->execute();
            
            $simulado_id = $this->db->lastInsertId();

            // Salva as estatísticas por área
            foreach ($area_stats as $area_id => $stats) {
                $query = "INSERT INTO simulado_area_stats 
                         (simulado_id, area_id, total_questions, correct_answers)
                         VALUES (:simulado_id, :area_id, :total_questions, :correct_answers)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":simulado_id", $simulado_id);
                $stmt->bindParam(":area_id", $area_id);
                $stmt->bindParam(":total_questions", $stats['total']);
                $stmt->bindParam(":correct_answers", $stats['correct']);
                $stmt->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Erro ao salvar resultado do simulado: " . $e->getMessage());
            return false;
        }
    }

    public function getUserResults($user_id, $filters = []) {
        $query = "SELECT sr.*, 
                 GROUP_CONCAT(DISTINCT a.nome SEPARATOR ', ') as area_names
                 FROM simulado_results sr
                 LEFT JOIN simulado_area_stats sas ON sr.id = sas.simulado_id
                 LEFT JOIN areas a ON sas.area_id = a.id
                 WHERE sr.user_id = :user_id";

        $params = [":user_id" => $user_id];

        // Filtros
        if (!empty($filters['area_id'])) {
            $query .= " AND sas.area_id = :area_id";
            $params[":area_id"] = $filters['area_id'];
        }

        if (!empty($filters['start_date'])) {
            $query .= " AND sr.created_at >= :start_date";
            $params[":start_date"] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND sr.created_at <= :end_date";
            $params[":end_date"] = $filters['end_date'];
        }

        $query .= " GROUP BY sr.id ORDER BY sr.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row;
        }
        return $results;
    }

    public function getUserAreaStats($user_id) {
        $query = "SELECT a.id, a.nome, 
                 COUNT(sas.id) as total_simulados,
                 SUM(sas.correct_answers) as total_correct,
                 SUM(sas.total_questions) as total_questions,
                 ROUND(SUM(sas.correct_answers) / SUM(sas.total_questions) * 100, 2) as avg_percentage
                 FROM simulado_area_stats sas
                 JOIN areas a ON sas.area_id = a.id
                 JOIN simulado_results sr ON sas.simulado_id = sr.id
                 WHERE sr.user_id = :user_id
                 GROUP BY a.id, a.nome
                 ORDER BY a.nome";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $stats = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats[] = $row;
        }
        return $stats;
    }
}