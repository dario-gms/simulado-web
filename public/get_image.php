<?php
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/models/QuestionImage.php';

$questionId = (int)($_GET['id'] ?? 0);

if ($questionId > 0) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $imageModel = new QuestionImage($db);
        $imageData = $imageModel->getImageByQuestion($questionId);
        
        if ($imageData && !empty($imageData['image_data'])) {
            header('Content-Type: image/webp');
            echo $imageData['image_data'];
            exit;
        }
    } catch (Exception $e) {
        error_log("Erro ao buscar imagem: " . $e->getMessage());
    }
}

// Fallback para imagem padrão
header('Content-Type: image/webp');
echo base64_decode('UklGRigAAABXRUJQVlA4IBwAAAAwAQCdASoBAAEAAgA0JaQAA3AA/vuUAAA=');