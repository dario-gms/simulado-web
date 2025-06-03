<?php
class Question {
    private $conn;
    private $table = 'questions';

    public $id;
    public $enunciado;
    public $resposta_correta;
    public $area_id;
    public $data_cadastro;
    public $opcoes = [];
    public $explicacao;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET enunciado = :enunciado, 
                     resposta_correta = :resposta_correta, 
                     area_id = :area_id,
                     explicacao = :explicacao";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":enunciado", $this->enunciado);
        $stmt->bindParam(":resposta_correta", $this->resposta_correta);
        $stmt->bindParam(":area_id", $this->area_id);
        $stmt->bindParam(":explicacao", $this->explicacao);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getByAreas($area_ids) {
        $in = str_repeat('?,', count($area_ids) - 1) . '?';
        $query = "SELECT * FROM " . $this->table . " WHERE area_id IN ($in) ORDER BY RAND()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($area_ids);
        return $stmt;
    }
}
?>