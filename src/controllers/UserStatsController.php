<?php
require_once __DIR__ . '/../models/UserStats.php';
require_once __DIR__ . '/../config/database.php';

class UserStatsController {
    private $db;
    private $statsModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->statsModel = new UserStats($this->db);
    }

    public function updateUserStats($user_id, $area_id, $is_correct) {
        return $this->statsModel->updateStats($user_id, $area_id, $is_correct);
    }

    public function getUserStats($user_id) {
        $stmt = $this->statsModel->getUserStats($user_id);
        $stats = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats[] = $row;
        }
        return $stats;
    }

    public function getAllUsersStats() {
        $stmt = $this->statsModel->getAllUsersStats();
        $stats = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats[] = $row;
        }
        return $stats;
    }
}