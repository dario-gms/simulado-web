<?php
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/controllers/ImageController.php';

header('Content-Type: image/webp');

$questionId = $_GET['id'] ?? 0;

if ($questionId > 0) {
    $imageController = new ImageController();
    $imageData = $imageController->getImageData($questionId);
    
    if ($imageData && !empty($imageData['image_data'])) {
        echo $imageData['image_data'];
        exit;
    }
}

// Imagem padrão se não encontrar
$defaultImage = imagecreatetruecolor(100, 100);
$bgColor = imagecolorallocate($defaultImage, 240, 240, 240);
imagefill($defaultImage, 0, 0, $bgColor);
imagewebp($defaultImage);
imagedestroy($defaultImage);