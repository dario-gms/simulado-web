<?php
class UserStats {
    private $conn;
    private $table = 'user_area_stats';

    public $id;
    public $user_id;
    public $area_id;
    public $total_questions;
    public $correct_answers;
    public $last_attempt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function updateStats($user_id, $area_id, $is_correct) {
        // Verifica se já existe registro para esse usuário e área
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE user_id = :user_id AND area_id = :area_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":area_id", $area_id);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Atualiza o registro existente
            $query = "UPDATE " . $this->table . " 
                     SET total_questions = total_questions + 1,
                         correct_answers = correct_answers + :correct_increment,
                         last_attempt = NOW()
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $correct_increment = $is_correct ? 1 : 0;
            $stmt->bindParam(":correct_increment", $correct_increment, PDO::PARAM_INT);
            $stmt->bindParam(":id", $existing['id']);
            return $stmt->execute();
        } else {
            // Cria um novo registro
            $query = "INSERT INTO " . $this->table . " 
                     (user_id, area_id, total_questions, correct_answers, last_attempt)
                     VALUES (:user_id, :area_id, 1, :correct_answers, NOW())";
            $stmt = $this->conn->prepare($query);
            $correct_answers = $is_correct ? 1 : 0;
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":area_id", $area_id);
            $stmt->bindParam(":correct_answers", $correct_answers, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }

    public function getUserStats($user_id) {
        $query = "SELECT a.nome, s.total_questions, s.correct_answers, 
                  ROUND((s.correct_answers / s.total_questions) * 100, 2) as percentage,
                  s.last_attempt
                  FROM " . $this->table . " s
                  JOIN areas a ON s.area_id = a.id
                  WHERE s.user_id = :user_id
                  ORDER BY s.last_attempt DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function getAllUsersStats() {
        $query = "SELECT u.username, a.nome as area_name, 
                  s.total_questions, s.correct_answers,
                  ROUND((s.correct_answers / s.total_questions) * 100, 2) as percentage,
                  s.last_attempt
                  FROM " . $this->table . " s
                  JOIN users u ON s.user_id = u.id
                  JOIN areas a ON s.area_id = a.id
                  ORDER BY s.last_attempt DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}