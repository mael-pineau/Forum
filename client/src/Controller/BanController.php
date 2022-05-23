<?php

namespace App\Controller;

use App\Form\form;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Services\SessionManager;
use App\Services\Sign\SignService;
use App\Services\Subject\SubjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Services\ApiLinker;

class BanController extends AbstractController
{
    # --------------------------------
    # Attributes

    protected SubjectService $service;
    protected ApiLinker $apiLinker;
    protected SessionManager $manager;

    # --------------------------------
    # Constructor

    /**
     * SubjectController constructor.
     *
     * @param SubjectService $service
     * @param ApiLinker $apiLinker
     * @param SessionManager $manager
     */
    public function __construct(SubjectService $service, ApiLinker $apiLinker, SessionManager $manager)
    {
        $this->service = $service;
        $this->apiLinker = $apiLinker;
        $this->manager = $manager;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # Display pages

    /**
     * Display the page to create ban
     *
     * @Route("/create-ban/{id}",
     *     name = "display-create-ban-page",
     *     methods={"GET"})
     */
    public function displayCreateBanPage($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get connected user
        $connectedUser = $this->manager->getUserFromSession();

        // Get targeted user
        $targedtedUser = json_decode($this->apiLinker->readData("user/".$id), true);

        // Check if connected user is admin
        if ($connectedUser["isAdmin"] == false) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        return $this->render('ban/display.ban.create.html.twig', [
            "connectedUser" => $connectedUser,
            "targetedUser" => $targedtedUser
        ]);
    }

    /**
     * Display the page wich contain all the formdeban reclamations
     *
     * @Route("/formdebans/",
     *     name = "display-formdeban-page",
     *     methods={"GET"})
     */
    public function displayAllFormDebanPage()
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get connected user
        $connectedUser = $this->manager->getUserFromSession();

        // Check if connected user is admin
        if ($connectedUser["isAdmin"] == false) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        // Informations to display in the template (for the header)
        $formdebans = json_decode($this->apiLinker->readData("formdebans"), true);

        return $this->render('formdeban/display.formdebans.html.twig', [
            "connectedUser" => $connectedUser,
            "formdebans" =>$formdebans
        ]);
    }

    /**
     * Display the page to fill a formdeban
     *
     * @Route("/create-formdeban/{id}",
     *     name = "create-formdeban-page",
     *     methods={"GET"})
     */
    public function displayCreateFormDebanPage($id)
    {
        // Get banned user
        $bannedUser = json_decode($this->apiLinker->readData("user/".$id), true);

        // Check if he really have a ban
        if ($bannedUser["ban"] == null) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        $ban = $bannedUser["ban"];

        return $this->render('formdeban/display.form.deban.create.html.twig', [
            "connectedUser" => null,
            "bannedUser" => $bannedUser,
            "ban" => $ban
        ]);
    }

    # ----------------
    # Create

    /**
     * Create a ban
     *
     * @Route("/create-ban/{id}",
     *     name = "create-ban",
     *     methods={"POST"})
     */
    public function createBan($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get connected user
        $user = $this->manager->getUserFromSession();

        // Check if user is admin
        if ($user["isAdmin"] == false) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        // Get data
        if ($_POST["time"] == "permanent") {
            $isPermanent = true;
            $dateDeban = 0;
        }
        else {
            $isPermanent = false;
            $dateDeban = time() + $_POST["time"];
        }

        $reason = htmlentities($_POST["content"], ENT_QUOTES, "UTF-8");

        $targetedUserId = $id;

        // Data to send
        $arrayDataToSend = array(
            "is_permanent" => $isPermanent,
            "reason" => $reason,
            "dateDeban" => $dateDeban,
            "userId" => $targetedUserId
        );
        $dataToSend = json_encode($arrayDataToSend);

        // Send the data to the api
        $this->apiLinker->createData("ban", $dataToSend);

        // Redirect to the created subject
        return $this->redirectToRoute("user-profil", ["id" => $targetedUserId, "success" => 2]);
    }

    /**
     * Create a formDeban
     *
     * @Route("/create-formdeban/{id}",
     *     name = "create-formban",
     *     methods={"POST"})
     */
    public function createFormDeBan($id)
    {
        // Get banned user
        $bannedUser = json_decode($this->apiLinker->readData("user/".$id), true);

        // Check if he really have a ban
        if ($bannedUser["ban"] == null) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        $message = htmlentities($_POST["content"], ENT_QUOTES, "UTF-8");
        $banId = $bannedUser["ban"]["id"];

        // Data to send
        $arrayDataToSend = array(
            "message" => $message,
            "userId" => $id,
            "banId" => $banId
        );
        $dataToSend = json_encode($arrayDataToSend);

        // Send the data to the api
        $this->apiLinker->createData("formdeban", $dataToSend);

        // Redirect to the sign-in page
        return $this->redirectToRoute("sign-user");
    }

    # ----------------
    # Delete

    /**
     * Delete a ban
     *
     * @Route("/delete-ban/{idBan}",
     *     name = "delete-ban",
     *     methods={"POST"})
     */
    public function deleteBan($idBan)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get connected user
        $user = $this->manager->getUserFromSession();

        // Check if user is admin
        if ($user["isAdmin"] == false) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        // Delete the ban
        $this->apiLinker->deleteData("ban/".$idBan);

        // Redirect to homePage
        return $this->redirectToRoute("display-formdeban-page");
    }
}