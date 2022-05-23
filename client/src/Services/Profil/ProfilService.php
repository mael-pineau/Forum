<?php

namespace App\Services\Profil;

use App\Services\ApiLinker;
use App\Services\Checkers\UserChecker;
use App\Services\ImageManager;
use App\Services\SessionManager;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProfilService
{

    # --------------------------------
    # Attributes

    protected ApiLinker $apiLinker;
    protected ImageManager $imageManager;
    protected SessionManager $sessionManager;
    protected RequestStack $requestStack;
    protected UserChecker $checker;

    # --------------------------------
    # Constructor

    /**
     * ProfilService constructor.
     *
     */
    public function __construct(ApiLinker $apiLinker, ImageManager $imageManager, SessionManager $sessionManager, RequestStack $requestStack, UserChecker $checker)
    {
        $this->apiLinker = $apiLinker;
        $this->imageManager = $imageManager;
        $this->sessionManager = $sessionManager;
        $this->requestStack = $requestStack;
        $this->checker = $checker;
    }

    # --------------------------------
    # Core methods

    /**
     * Get the user currently connected
     *
     * @param string $imageName
     * @return mixed
     */
    public function getLoggedUser()
    {
        $session = $this->requestStack->getSession();
        $idUser = $session->get("idUser", null);
        return null;
    }

    /**
     * Get user profil picture
     *
     * @param string $imageName
     * @return mixed
     */
    public function getProfilPic(string $imageName)
    {
        $pathImage = $this->imageManager->getUserImage($imageName);

        return $pathImage;
    }

    /**
     * Update the user profil picture
     *
     * @param $file
     * @return mixed
     */
    public function addProfilPicture($file)
    {
        // Get connected user
        $user = $this->sessionManager->getUserFromSession();
        $userId = $user["id"];

        if ($file["name"] != $user["image"]) {
            // Add profil picture to image folder
            $errorMessage = $this->imageManager->addUserImage($file);
        }
        else {
            $errorMessage = "Veuillez choisir une image différente de celle que vous avez actuellement";
        }

        // If no errors
        if ($errorMessage == null) {

            $arrayDataToSend = array(
                "mail" => $user["mail"],
                "username" => $user["username"],
                "password" => $user["password"],
                "image" => $file["name"],
                "is_admin" => $user["isAdmin"]
            );
            $dataToSend = json_encode($arrayDataToSend);

            // Update the user profil pic
            $this->apiLinker->updateData("user/".$userId, $dataToSend);
        }

        return $errorMessage;
    }

    /**
     * Update the user username
     *
     * @param $file
     * @return mixed
     */
    public function updateUsername($username)
    {
        // Check if username is conform
        $errorMessage = $this->checker->checkUsername($username);

        // Get connected user
        $connectedUser = $this->sessionManager->getUserFromSession();
        $userId = $connectedUser["id"];

        if (!$errorMessage) {

            // Check if username already exist in database
            $user = json_decode($this->apiLinker->readData("user/username/".$username), true);

            if ($user != null) {
                if ($user != $connectedUser) {
                    $errorMessage = "Ce pseudo est déja pris";
                    return $errorMessage;
                }
                else {
                    $errorMessage = "Veuillez entrer un pseudo différent de votre pseudo actuel";
                    return $errorMessage;
                }
            }

            // Else update the user username
            else {

                $arrayDataToSend = array(
                    "mail" => $connectedUser["mail"],
                    "username" => $username,
                    "password" => $connectedUser["password"],
                    "image" => $connectedUser["image"],
                    "is_admin" => $connectedUser["isAdmin"]
                );
                $dataToSend = json_encode($arrayDataToSend);

                $this->apiLinker->updateData("user/".$userId, $dataToSend);
            }
        }

        return $errorMessage;
    }

    /**
     * Validate passwords
     *
     * @param $hashedCurrentPassword
     * @param $newPassword
     * @param $confirmNewPassword
     * @return string|null
     */
    public function updatePassword($hashedCurrentPassword, $newPassword, $confirmNewPassword)
    {
        // Get connected user
        $user = $this->sessionManager->getUserFromSession();
        $userId = $user["id"];

        // Check if current password is the same as the user
        if ($hashedCurrentPassword != $user["password"]) {
            $errorMessage = "Le mot de passe actuel n'est pas correct";
            return $errorMessage;
        }

        // Check if new password is conform
        $errorMessage = $this->checker->checkPassword($newPassword);

        if (!$errorMessage) {

            // Check if both new passwords are the sames
            if ($newPassword != $confirmNewPassword) {
                $errorMessage = "Les mot de passes ne sont pas les mêmes";
                return $errorMessage;
            }
            // Check if new password is the same as the old
            elseif (hash("sha256", $newPassword) == $hashedCurrentPassword) {
                $errorMessage = "Le nouveau mot de passe est le même que l'ancien";
                return $errorMessage;
            }
            else {

                // Update the password
                $hashedNewPassword = hash("sha256", $newPassword);

                $arrayDataToSend = array(
                    "mail" => $user["mail"],
                    "username" => $user["username"],
                    "password" => $hashedNewPassword,
                    "image" => $user["image"],
                    "is_admin" => $user["isAdmin"]
                );

                $dataToSend = json_encode($arrayDataToSend);

                $this->apiLinker->updateData("user/" . $userId, $dataToSend);
            }
        }

        return $errorMessage;
    }
}