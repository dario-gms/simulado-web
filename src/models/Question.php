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
    public $has_image;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 SET enunciado = :enunciado, 
                     resposta_correta = :resposta_correta, 
                     area_id = :area_id,
                     explicacao = :explicacao,
                     has_image = :has_image";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":enunciado", $this->enunciado);
        $stmt->bindParam(":resposta_correta", $this->resposta_correta);
        $stmt->bindParam(":area_id", $this->area_id);
        $stmt->bindParam(":explicacao", $this->explicacao);
        $stmt->bindParam(":has_image", $this->has_image, PDO::PARAM_BOOL);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getByAreas($area_ids) {
        $in = str_repeat('?,', count($area_ids) - 1) . '?';
        
        $query = "SELECT q.*, qi.image_data 
                 FROM " . $this->table . " q
                 LEFT JOIN question_images qi ON q.id = qi.question_id
                 WHERE q.area_id IN ($in) 
                 ORDER BY RAND()";
                 
        $stmt = $this->conn->prepare($query);
        $stmt->execute($area_ids);
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT q.*, qi.image_data 
                 FROM " . $this->table . " q
                 LEFT JOIN question_images qi ON q.id = qi.question_id
                 WHERE q.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}