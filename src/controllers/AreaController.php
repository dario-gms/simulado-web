<?php
require_once __DIR__ . '/../models/Area.php';
require_once __DIR__ . '/../config/database.php';

class AreaController {
    private $db;
    private $areaModel;
    public $lastInsertId;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->areaModel = new Area($this->db);
    }

    public function getAll() {
        try {
            $stmt = $this->areaModel->getAll();
            $areas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $areas[] = $row;
            }
            return $areas;
        } catch (PDOException $e) {
            error_log("Erro ao buscar áreas: " . $e->getMessage());
            return [];
        } catch (Exception $e) {
            error_log("Erro: " . $e->getMessage());
            return [];
        }
    }

    public function create($nome) {
        try {
            $this->areaModel->nome = trim($nome);
            
            if (empty($this->areaModel->nome)) {
                throw new Exception("Nome da área não pode ser vazio");
            }

            if ($this->areaModel->create()) {
                $this->lastInsertId = $this->areaModel->id;
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao criar área: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erro: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            return $this->areaModel->delete($id);
        } catch (PDOException $e) {
            error_log("Erro ao deletar área: " . $e->getMessage());
            return false;
        }
    }

    public function areaExists($nome) {
        try {
            $query = "SELECT COUNT(*) FROM areas WHERE nome = :nome";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":nome", $nome);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar área: " . $e->getMessage());
            return false;
        }
    }

    public function getById($id) {
        try {
            return $this->areaModel->getById($id);
        } catch (PDOException $e) {
            error_log("Erro ao buscar área: " . $e->getMessage());
            return null;
        }
    }
}
?>