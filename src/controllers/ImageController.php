<?php
require_once __DIR__ . '/../models/QuestionImage.php';

class ImageController {
    private $db;
    private $imageModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->imageModel = new QuestionImage($this->db);
    }

    public function processAndSaveImage($file, $questionId) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erro no upload da imagem');
        }

        // Verificar se é uma imagem válida
        $validTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
        $detectedType = exif_imagetype($file['tmp_name']);
        
        if (!in_array($detectedType, $validTypes)) {
            throw new Exception('Formato de imagem inválido');
        }

        // Carregar imagem
        switch ($detectedType) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($file['tmp_name']);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($file['tmp_name']);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($file['tmp_name']);
                break;
            default:
                throw new Exception('Tipo de imagem não suportado');
        }

        // Redimensionar e converter para WEBP
        list($width, $height) = getimagesize($file['tmp_name']);
        $maxSize = 800;
        $ratio = $width / $height;
        
        if ($width > $height) {
            $newWidth = min($width, $maxSize);
            $newHeight = $newWidth / $ratio;
        } else {
            $newHeight = min($height, $maxSize);
            $newWidth = $newHeight * $ratio;
        }

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);

        // Capturar dados WEBP
        ob_start();
        imagewebp($newImage, null, 80);
        $imageData = ob_get_clean();
        imagedestroy($newImage);

        // Salvar no banco
        return $this->imageModel->saveImage($questionId, $imageData);
    }

    public function getImageData($questionId) {
        return $this->imageModel->getImageByQuestion($questionId);
    }
}