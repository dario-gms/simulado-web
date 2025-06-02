<?php
class Area {
    private $conn;
    private $table = 'areas';

    public $id;
    public $nome;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function checkConnection() {
        try {
            $this->conn->query("SELECT 1")->execute();
        } catch (PDOException $e) {
            throw new Exception("Conexão com o banco perdida: " . $e->getMessage());
        }
    }

    public function getAll() {
        $this->checkConnection();
        $query = "SELECT id, nome FROM " . $this->table . " ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $this->checkConnection();
        $query = "INSERT INTO " . $this->table . " (nome) VALUES (:nome)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nome", $this->nome);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->checkConnection();
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function getById($id) {
        $this->checkConnection();
        $query = "SELECT id, nome FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>