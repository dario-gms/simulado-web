<?php
class Option {
    private $conn;
    private $table = 'options';

    public $id;
    public $question_id;
    public $letra;
    public $texto;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET question_id = :question_id, 
                     letra = :letra, 
                     texto = :texto";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":question_id", $this->question_id);
        $stmt->bindParam(":letra", $this->letra);
        $stmt->bindParam(":texto", $this->texto);
        
        return $stmt->execute();
    }

    public function getByQuestion($question_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE question_id = ? ORDER BY letra";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$question_id]);
        return $stmt;
    }
}
?>