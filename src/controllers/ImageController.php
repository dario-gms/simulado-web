<?php
require_once __DIR__ . '/../models/QuestionImage.php';
require_once __DIR__ . '/../config/database.php';

class ImageController {
    private $db;
    private $imageModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->imageModel = new QuestionImage($this->db);
    }

    public function processImage($tmpPath) {
        // Verificar se é uma imagem válida
        $validTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
        $detectedType = exif_imagetype($tmpPath);
        
        if (!in_array($detectedType, $validTypes)) {
            throw new Exception('Formato de imagem inválido');
        }

        // Carregar imagem
        switch ($detectedType) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($tmpPath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($tmpPath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($tmpPath);
                break;
            default:
                throw new Exception('Tipo de imagem não suportado');
        }

        // Redimensionar e converter para WEBP
        list($width, $height) = getimagesize($tmpPath);
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
        
        // Preserve transparency for PNG and GIF
        if ($detectedType == IMAGETYPE_PNG || $detectedType == IMAGETYPE_GIF) {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);

        // Capturar dados WEBP
        ob_start();
        imagewebp($newImage, null, 80);
        $imageData = ob_get_clean();
        imagedestroy($newImage);

        return $imageData;
    }

    public function processAndSaveImage($file, $questionId) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erro no upload da imagem');
        }

        $imageData = $this->processImage($file['tmp_name']);
        return $this->imageModel->saveImage($questionId, $imageData);
    }

    public function getImageData($questionId) {
        return $this->imageModel->getImageByQuestion($questionId);
    }
}