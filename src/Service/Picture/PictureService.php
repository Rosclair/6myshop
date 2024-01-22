<?php

namespace App\Service\Picture;

//require 'vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $picture, ?string $folder, ?int $width = 640, ?int $height = 400)
    {
        // On donne un nouveau nom à l'image
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        // Chemin de sauvegarde de l'image
        $path = $this->params->get('images_directory') . $folder;

        $manager = new ImageManager(new Driver());

        $image = $manager->read($picture)
            ->resize($width, $height,
                function($constraint){
                    $constraint->aspectRatio();
                }
            )
            ->save($path . $fichier);
        return $fichier;
    }

    public function delete(string $fichier, ?string $folder, ?int $width = 600, ?int $height = 600)
    {
        // Chemin de l'image
        $path = $this->params->get('images_directory') . $folder;

        $fileToDelete = $path . $fichier;

        if(file_exists($fileToDelete)){
            unlink($fileToDelete);
            $success = true;
        }else{
            $success = false;
        }
        return $success;
    }
}