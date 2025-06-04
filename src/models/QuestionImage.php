<?php
class QuestionImage {
    private $conn;
    private $table = 'question_images';

    public $id;
    public $question_id;
    public $image_data;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function saveImage($questionId, $imageData) {
        $query = "INSERT INTO $this->table (question_id, image_data) VALUES (:question_id, :image_data)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':question_id', $questionId);
        $stmt->bindParam(':image_data', $imageData, PDO::PARAM_LOB);
        
        return $stmt->execute();
    }

    public function getImageByQuestion($questionId) {
        $query = "SELECT image_data FROM $this->table WHERE question_id = :question_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':question_id', $questionId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}