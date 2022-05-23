<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImageManager
{
    private $basePath = 'images/profil-pictures/';

    public function getUserImage($imageName)
    {
        $pathPhoto = $this->basePath . $imageName;

        if (empty($imageName) || !file_exists($pathPhoto))
        {
            $pathPhoto = $this->basePath . "default.png";
        }

        return $pathPhoto;
    }

    public function addUserImage($file): ?string
    {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $extensionsAutorisees = array('jpg', 'jpeg', 'png', 'PNG');

        // If file is not null
        if ($file["name"] != null) {

            // If file is in the array extentions authorized
            if (in_array($extension, $extensionsAutorisees)) {
                if (move_uploaded_file($file['tmp_name'], 'images/profil-pictures/' . $file['name'])) {
                    return null;
                }
                return "Une erreur est survenue lors de l'ajout de l'image au serveur. Veuillez réessayer plus tard";
            }

            return "Le format de fichier n'est pas correct. Veuillez choisir un fichier jpg, jpeg ou png";
        }

        return "Veuillez choisir un fichier";
    }
}
