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
        // Mapeamento completo de tipos de imagem
        $supportedTypes = [
            IMAGETYPE_JPEG => ['create' => 'imagecreatefromjpeg', 'ext' => 'jpg'],
            IMAGETYPE_PNG => ['create' => 'imagecreatefrompng', 'ext' => 'png'],
            IMAGETYPE_GIF => ['create' => 'imagecreatefromgif', 'ext' => 'gif'],
            IMAGETYPE_WEBP => ['create' => 'imagecreatefromwebp', 'ext' => 'webp'],
            IMAGETYPE_BMP => ['create' => 'imagecreatefrombmp', 'ext' => 'bmp'],
            IMAGETYPE_TIFF_II => ['handler' => 'imagick', 'ext' => 'tiff'],
            IMAGETYPE_TIFF_MM => ['handler' => 'imagick', 'ext' => 'tiff'],
            19 => ['handler' => 'imagick', 'ext' => 'heic'], // IMAGETYPE_HEIC (PHP 8.1+)
            20 => ['handler' => 'imagick', 'ext' => 'heif'], // IMAGETYPE_HEIF (PHP 8.1+)
            21 => ['handler' => 'imagick', 'ext' => 'avif']  // IMAGETYPE_AVIF (PHP 8.1+)
        ];

        $detectedType = exif_imagetype($tmpPath);
        
        if (!array_key_exists($detectedType, $supportedTypes)) {
            throw new Exception('Formato de imagem não suportado');
        }

        $imageConfig = $supportedTypes[$detectedType];
        $source = null;

        // Processamento com GD ou Imagick
        if (isset($imageConfig['create'])) {
            // Processamento com GD
            $createFunction = $imageConfig['create'];
            $source = @$createFunction($tmpPath);
            
            if (!$source) {
                throw new Exception('Falha ao processar imagem com GD');
            }
        } elseif (isset($imageConfig['handler']) && $imageConfig['handler'] === 'imagick') {
            // Processamento com Imagick
            if (!extension_loaded('imagick')) {
                throw new Exception('Formato requer Imagick: ' . $imageConfig['ext']);
            }
            
            try {
                $imagick = new Imagick($tmpPath);
                $imagick->setImageFormat('webp');
                $imagick->setImageCompressionQuality(80);
                
                // Redimensionar se necessário
                if ($imagick->getImageWidth() > 800 || $imagick->getImageHeight() > 800) {
                    $imagick->resizeImage(800, 800, Imagick::FILTER_LANCZOS, 1, true);
                }
                
                return $imagick->getImageBlob();
            } catch (Exception $e) {
                throw new Exception('Falha ao processar imagem com Imagick: ' . $e->getMessage());
            }
        }

        // Redimensionamento para GD
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
        
        // Preservar transparência para PNG/GIF
        if ($detectedType === IMAGETYPE_PNG || $detectedType === IMAGETYPE_GIF) {
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

    public function getImageData($questionId) {
        return $this->imageModel->getImageByQuestion($questionId);
    }
}