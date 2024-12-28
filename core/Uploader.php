<?php
namespace core;

use SplFileInfo;

class Uploader{
    public static string $watermarkImage;

    public static function upload($files, $inputName, $targetDirectory, $watermark = false){
        if (!file_exists($targetDirectory)) {
            if (mkdir($targetDirectory,0777,true)) {
                return Uploader::runUpload($files, $inputName, $targetDirectory, $watermark);
            } else {
                return false;
            }
        }else{
            return Uploader::runUpload($files, $inputName, $targetDirectory, $watermark);
        }
    }

    private static function runUpload($files, $inputName, $targetDirectory, $watermark): array
    {
        $result = [
            'uploaded' => [],
            'failed' => []
        ];
        foreach ($files[$inputName]["name"] as $key => $fileName) {


            $img_info = new SplFileInfo($fileName);
            $img_ex = $img_info->getExtension();
            $img_ex_lc = strtolower($img_ex);

            $targetFile = $targetDirectory .'/'. uniqid("FILE-", true) . '.' . $img_ex_lc;

            // Перевірте, чи файл існує вже
            if (!file_exists($targetFile)) {
                // Завантажте файл до вказаного каталогу
                if (move_uploaded_file($files[$inputName]["tmp_name"][$key], $targetFile)) {
                    $result['uploaded'][] = $fileName;

                    if ($watermark){
                        Uploader::addWatermark($targetFile, $targetFile);
                    }

                }else{
                    $result['failed'][] = $fileName;
                }
            }
        }
        return $result;
    }

    public static function uploadSingle($files, $inputName, $targetDirectory, $name = null): string
    {
        if (!file_exists($targetDirectory)){
            mkdir($targetDirectory,0777,true);
        }

        $img_name = $files[$inputName]['name'];
        $tmp_name = $files[$inputName]['tmp_name'];
        $img_info = new SplFileInfo($img_name);
        $img_ex = $img_info->getExtension();
        $img_ex_lc = strtolower($img_ex);

        $file_name = $name ? "IMG-{$name}.{$img_ex_lc}" : uniqid("IMG-", true) . '.' . $img_ex_lc;

        $img_upload_path = $targetDirectory.'/'.$file_name;
        if (file_exists($img_upload_path)) unlink($img_upload_path);

        move_uploaded_file($tmp_name, $img_upload_path);
        return $file_name;
    }


    private static function addWatermark($sourceImage, $outputImage) {
        $img_info = new SplFileInfo($sourceImage);
        $img_ex = $img_info->getExtension();
        $img_ex_lc = strtolower($img_ex);


        // Отримуємо розміри зображень
        list($sourceWidth, $sourceHeight) = getimagesize($sourceImage);
        list($watermarkWidth, $watermarkHeight) = getimagesize(Uploader::$watermarkImage);

        // Створюємо об'єкт зображення з оригінального зображення та водяного знаку
        $source = null;

        if ($img_ex_lc == 'png'){
            $source = imagecreatefrompng($sourceImage);
        }else{
            $source = imagecreatefromjpeg($sourceImage);
        }

        $watermark = imagecreatefrompng(Uploader::$watermarkImage);


        // Зменшуємо розмір водяного знаку
        $newHeight = $sourceHeight * 0.05; // 5% від висоти оригінального зображення
        $ratio = $newHeight / $watermarkHeight;
        $newWidth = $watermarkWidth * $ratio;
        $resizedWatermark = imagescale($watermark, $newWidth, $newHeight);

        // Встановлюємо позицію водяного знаку (у цьому випадку - правий нижній кут)
        $destX = $sourceWidth - $newWidth - 10;
        $destY = $sourceHeight - $newHeight - 10;

        // Копіюємо водяний знак на оригінальне зображення
        imagecopy($source, $resizedWatermark, $destX, $destY, 0, 0, $newWidth, $newHeight);

        // Зберігаємо зображення з водяним знаком
        imagepng($source, $outputImage);

        // Звільняємо ресурси пам'яті
        imagedestroy($source);
        imagedestroy($resizedWatermark);
    }
}

