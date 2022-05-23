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

class SubjectController extends AbstractController
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
     * Display the subjects page (home page)
     *
     * @Route("/",
     *     name = "display-subjects",
     *     methods={"GET"})
     */
    public function displaySubjects()
    {
        // Get content that was sent
        if (!empty($_GET["sortCriteria"]) || !empty($_GET["search"])) {

            // Check if subject list need to be sorted by a criteria
            if (!empty($_GET["sortCriteria"])) {
                $resultArray = $this->service->sortSubjectsByCriteria($_GET["sortCriteria"]);

                $subjects = $resultArray["subjects"];
                $selectedOption = $resultArray["selectedOption"];
                $searchedWords = null;
            }

            // Check if subject list need to be sorted by a word searched
            else {
                $subjects = $this->service->searchSubjectsByName($_GET["search"]);
                $selectedOption = "mostRecents";
                $searchedWords = $_GET["search"];
            }
        }
        else {
            // Default
            $subjects = json_decode($this->apiLinker->readData("subject"), true);
            $selectedOption = "mostRecents";
            $searchedWords = null;
        }

        $connectedUser = $this->manager->getUserFromSession();

        // Else
        return $this->render('subject/display.subjects.html.twig', [
            "connectedUser" => $connectedUser,
            "subjects" => $subjects,
            "showAdditionalOption" => true,
            "selectedOption" => $selectedOption,
            "searchedWords" => $searchedWords
        ]);
    }

    /**
     * Display the page to create a new subject
     *
     * @Route("/create-subject",
     *     name = "create-subject-page",
     *     methods={"GET"})
     */
    public function displayCreateSubjectPage()
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get connected user
        $user = $this->manager->getUserFromSession();

        return $this->render('subject/display.subject.create.html.twig', [
            "connectedUser" => $user
        ]);
    }

    /**
     * Display the edit subject page
     *
     * @Route("/edit-subject/{id}",
     *     name = "edit-subject",
     *     methods={"GET"})
     */
    public function displayEditSubjectPage($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get corresponding subject
        $subject = json_decode($this->apiLinker->readData("subject/".$id), true);

        // Check if connected user is the author of the subject
        $user = $this->manager->getUserFromSession();
        if ($subject == null) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }
        elseif ($user["id"] != $subject["user"]["id"]) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        return $this->render('subject/display.subject.edit.html.twig', [
            "concernedSubject" => $subject,
            "connectedUser" => $user
        ]);
    }

    /**
     * Display a subject in detail
     *
     * @Route("/subject/{id}",
     *     name = "subject-detail",
     *     methods={"GET"})
     */
    public function displaysubject($id)
    {
        // Get subject
        $subject = json_decode($this->apiLinker->readData("subject/".$id), true);
        if ($subject == null) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            $connectedUser = null;
        }
        else {

            // Get connected user
            $connectedUser = $this->manager->getUserFromSession();
        }

        // Render the view
        return $this->render('subject/display.subject.detail.html.twig', [
            "connectedUser" => $connectedUser,
            "concernedSubject" => $subject,
            ]);
    }

    # ----------------
    # Create

    /**
     * Create a subject
     *
     * @Route("/create-subject",
     *     name = "create-subject",
     *     methods={"POST"})
     */
    public function createSubject()
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get connected user
        $user = $this->manager->getUserFromSession();

        // Get data
        $title = $_POST["input-title"];
        $description = htmlentities($_POST["content"], ENT_QUOTES, "UTF-8");
        $userId = $user["id"];

        // Data to send
        $arrayDataToSend = array(
            "title" => $title,
            "description" => $description,
            "userId" => $userId
        );
        $dataToSend = json_encode($arrayDataToSend);

        // Send the data to the api
        $createdSubject = json_decode($this->apiLinker->createData("subject", $dataToSend), true);

        // Get the id of the created subject
        $createdSubjectId = $createdSubject["id"];

        // Redirect to the created subject
        return $this->redirectToRoute("subject-detail", ["id" => $createdSubjectId]);
    }

    # ----------------
    # Update

    /**
     * Close a subject
     *
     * @Route("/close-subject/{id}",
     *     name = "close-subject",
     *     methods={"POST"})
     */
    public function closeSubject($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get subject
        $subject = json_decode($this->apiLinker->readData("subject/".$id), true);

        // Get user
        $user = $this->manager->getUserFromSession();

        // Call service
        $this->service->closeSubject($user, $subject);

        return $this->redirectToRoute("subject-detail", ["id" => $id]);
    }

    /**
     * Re-open a subject
     *
     * @Route("/open-subject/{id}",
     *     name = "open-subject",
     *     methods={"POST"})
     */
    public function openSubject($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get subject
        $subject = json_decode($this->apiLinker->readData("subject/".$id), true);

        // Get user
        $user = $this->manager->getUserFromSession();

        // Call service
        $this->service->openSubject($user, $subject);

        return $this->redirectToRoute("subject-detail", ["id" => $id]);
    }

    /**
     * Update a subject
     *
     * @Route("/update-subject/{id}",
     *     name = "update-subject",
     *     methods={"POST"})
     */
    public function updateSubject($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get subject
        $subject = json_decode($this->apiLinker->readData("subject/".$id), true);

        // Get user
        $user = $this->manager->getUserFromSession();

        // Get data
        $title = $_POST["input-title"];
        $description = htmlentities($_POST["content"], ENT_QUOTES, "UTF-8");
        $is_closed = false;

        // Check if connected user is the author of the subject
        if ($user["id"] != $subject["user"]["id"]) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        // Data to send
        $arrayDataToSend = array(
            "title" => $title,
            "description" => $description,
            "is_closed" => $is_closed
        );
        $dataToSend = json_encode($arrayDataToSend);

        // Send the data to the api
        $this->apiLinker->updateData("subject/".$id, $dataToSend);

        // Redirect to the created subject
        return $this->redirectToRoute("subject-detail", ["id" => $id]);
    }

    # ----------------
    # Delete

    /**
     * Delete a subject
     *
     * @Route("/delete-subject/{id}",
     *     name = "delete-subject",
     *     methods={"POST"})
     */
    public function deleteSubject($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get subject
        $subject = json_decode($this->apiLinker->readData("subject/".$id), true);

        // Get user
        $user = $this->manager->getUserFromSession();

        // Call service
        $this->service->deleteSubject($user, $subject);

        // Redirect to homePage
        return $this->redirectToRoute("display-subjects");
    }
}