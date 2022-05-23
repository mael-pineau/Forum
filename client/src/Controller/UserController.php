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

class UserController extends AbstractController
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
    # update

    /**
     * Make a user admin
     *
     * @Route("/make-user-admin/{id}",
     *     name = "make-user-admin",
     *     methods={"GET"})
     */
    public function makeUserAdmin($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        $connectedUser = $this->manager->getUserFromSession();

        // Check if user that make the request is admin
        if ($connectedUser["isAdmin"] == true) {

            // Get corresponding user
            $user = $this->apiLinker->readData("user/".$id);
            $user = json_decode($user, true);

            $arrayDataToSend = array(
                "mail" => $user["mail"],
                "username" => $user["username"],
                "password" => $user["password"],
                "image" => $user["image"],
                "is_admin" => true
            );
            $dataToSend = json_encode($arrayDataToSend);

            // Update the subject
            $this->apiLinker->updateData("user/".$id, $dataToSend);
        }

        return $this->redirectToRoute("user-profil", ["id" => $id, "success" => true]);
    }

    # ----------------
    # delete

    /**
     * Delete a user
     *
     * @Route("/delete-user/{id}",
     *     name = "mdelete-user",
     *     methods={"POST"})
     */
    public function deleteUser($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        $connectedUser = $this->manager->getUserFromSession();

        // Check if user that make the request is the connectedUser
        if ($connectedUser["id"] == $id) {

            // Delete the user
            $this->apiLinker->deleteData("user/".$id);

            return $this->redirectToRoute("sign-out-user");
        }

        // Else just redirect to the error page
        else {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }
    }
}