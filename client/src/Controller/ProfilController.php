<?php

namespace App\Controller;

use App\Services\Profil\ProfilService;
use App\Services\SessionManager;
use App\Services\Sign\SignService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Services\ApiLinker;

class ProfilController extends AbstractController
{
    # --------------------------------
    # Attributes

    protected ProfilService $service;
    protected SessionManager $manager;
    protected ApiLinker $apiLinker;

    # --------------------------------
    # Constructor

    /**
     * ProfilController constructor.
     *
     * @param ProfilService $service
     * @param ApiLinker $apiLinker
     * @param SessionManager $manager
     */
    public function __construct(ProfilService $service, ApiLinker $apiLinker, SessionManager $manager)
    {
        $this->manager = $manager;
        $this->service = $service;
        $this->apiLinker = $apiLinker;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # Display

    /**
     * Display a user profil page
     *
     * @Route("/user-profil/{id}",
     *     name = "user-profil",
     *     methods={"GET"})
     */
    public function displayUserProfil($id)
    {
        // Get user
        $user = json_decode($this->apiLinker->readData("user/".$id), true);
        if ($user == null) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        // To display success message
        $request = Request::createFromGlobals();
        $success = $request->query->get('success');

        // Get connected user
        $connectedUser = $this->manager->getUserFromSession();

        // Render the view
        return $this->render('profil/display.profil.html.twig', [
            "concernedUser" => $user,
            "connectedUser" => $connectedUser,
            "success" => $success
            ]
        );
    }

    /**
     * Display the user edit profil page
     *
     * @Route("/edit-profil",
     *     name = "edit-profil",
     *     methods={"GET"})
     */
    public function displayEditProfil()
    {

        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get connected user
        $user = $this->manager->getUserFromSession();

        // Render the view
        return $this->render('profil/display.profil.edit.html.twig', [
            "connectedUser" => $user,
        ]);
    }

    # ----------------
    # Update

    /**
     * Change a user profil picture
     *
     * @Route("/edit-profil-picture",
     *     name = "edit-profil-picture",
     *     methods={"POST"})
     */
    public function editProfilPicture()
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get file to upload
        $file = $_FILES["input-profil-picture"];

        // Add new image
        $errorMessage = $this->service->addProfilPicture($file);

        // If error when uploading
        if ($errorMessage != null) {

            // Get user
            $user = $this->manager->getUserFromSession();

            return $this->render('profil/display.profil.edit.html.twig', [
                "connectedUser" => $user,
                "editProfilPictureErrorMessage" => $errorMessage]
            );
        }
        else {

            // Get updated user
            $user = $this->manager->getUserFromSession();

            return $this->render('profil/display.profil.edit.html.twig', [
                "connectedUser" => $user,
                "editProfilPictureSuccessMessage" => "La photo de profil à bien été mis a jour"]
            );
        }
    }

    /**
     * Change a user username
     *
     * @Route("/edit-profil-username",
     *     name = "edit-profil-username",
     *     methods={"POST"})
     */
    public function editProfilUsername()
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get new username
        $username = $_POST["input-username"];

        // Update username
        $errorMessage = $this->service->updateUsername($username);

        // If error about the new username
        if ($errorMessage != null) {

            // Get user
            $user = $this->manager->getUserFromSession();

            return $this->render('profil/display.profil.edit.html.twig', [
                "connectedUser" => $user,
                "editProfilUsernameErrorMessage" => $errorMessage]
            );
        }
        else {

            // Get updated user
            $user = $this->manager->getUserFromSession();

            return $this->render('profil/display.profil.edit.html.twig', [
                "connectedUser" => $user,
                "editProfilUsernameSuccessMessage" => "Le pseudo a bien été mis à jour"]);
        }
    }

    /**
     * Change a user password
     *
     * @Route("/edit-profil-password",
     *     name = "edit-profil-password",
     *     methods={"POST"})
     */
    public function editProfilPassword()
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get data
        $currentPassword = $_POST["input-password"];
        $hashedCurrentPassword = hash("sha256", $currentPassword);
        $newPassword = $_POST["input-new-password"];
        $confirmNewPassword = $_POST["input-new-password-confirm"];

        // Call service
        $errorMessage = $this->service->updatePassword($hashedCurrentPassword, $newPassword, $confirmNewPassword);

        // If error about passwords
        if ($errorMessage != null) {

            // Get user
            $user = $this->manager->getUserFromSession();

            return $this->render('profil/display.profil.edit.html.twig', [
                "connectedUser" => $user,
                "inputedPassword" => $currentPassword,
                "inputedNewPassword" => $newPassword,
                "editProfilPasswordErrorMessage" => $errorMessage]
            );
        }

        // Get updated user
        $user = $this->manager->getUserFromSession();

        return $this->render('profil/display.profil.edit.html.twig', [
            "connectedUser" => $user,
            "editProfilPasswordSuccessMessage" => "Le mot de passe a bien été mis à jour"]);
    }
}