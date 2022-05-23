<?php

namespace App\Controller;

use App\Services\SessionManager;
use App\Services\Sign\SignService;
use App\Services\Subject\SubjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Services\ApiLinker;

class CommentController extends AbstractController
{
    # --------------------------------
    # Attributes

    protected SubjectService $service;
    protected ApiLinker $apiLinker;
    protected SessionManager $manager;

    # --------------------------------
    # Constructor

    /**
     * CommentController constructor.
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
    # Display

    /**
     * Display the edit comment page
     *
     * @Route("/edit-comment/{id}",
     *     name = "edit-comment",
     *     methods={"GET"})
     */
    public function displayEditCommentPage($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get corresponding comment
        $concernedComment = json_decode($this->apiLinker->readData("comment/".$id), true);

        // Check if connected user is the author of the comment
        $connectedUser = $this->manager->getUserFromSession();
        if ($connectedUser["id"] != $concernedComment["user"]["id"]) {
            return $this->render("bundles/TwigBundle/Exception/error404.html.twig");
        }

        $concernedSubject = $concernedComment["subject"];

        return $this->render('comment/display.comment.edit.html.twig', [
            "connectedUser" => $connectedUser,
            "concernedComment" => $concernedComment,
            "concernedSubject" => $concernedSubject
        ]);
    }

    # ----------------
    # Create

    /**
     * Create a comment
     *
     * @Route("/create-comment",
     *     name = "create-comment",
     *     methods={"POST"})
     */
    public function createComment()
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get connected user
        $user = $this->manager->getUserFromSession();

        // Get data
        $message = htmlentities($_POST["content"], ENT_QUOTES, "UTF-8");
        $subjectId = $_POST["subjectId"];
        $userId = $user["id"];

        // Data to send
        $arrayDataToSend = array(
            "message" => $message,
            "userId" => $userId,
            "subjectId" => $subjectId
        );
        $dataToSend = json_encode($arrayDataToSend);

        // Send the data to the api
        $this->apiLinker->createData("comment", $dataToSend);

        // Redirect to the created subject
        return $this->redirectToRoute("subject-detail", ["id" => $subjectId]);
    }

    /**
     * Update a comment
     *
     * @Route("/update-comment/{id}",
     *     name = "update-comment",
     *     methods={"POST"})
     */
    public function updateComment($id)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        // Get data
        $subjectId = $_POST["subjectId"];
        $message = htmlentities($_POST["content"], ENT_QUOTES, "UTF-8");

        // Data to send
        $arrayDataToSend = array(
            "message" => $message,
        );
        $dataToSend = json_encode($arrayDataToSend);

        // Send the data to the api
        $this->apiLinker->updateData("comment/".$id, $dataToSend);

        // Redirect to the created subject
        return $this->redirectToRoute("subject-detail", ["id" => $subjectId]);
    }

    # ----------------
    # Delete

    /**
     * Delete a comment
     *
     * @Route("/delete-comment/{idComment}",
     *     name = "delete-comment",
     *     methods={"POST"})
     */
    public function deleteComment($idComment)
    {
        // Check if user is signed-in
        if ($this->manager->isSessionInitialized() == false) {
            return $this->redirectToRoute('sign-user');
        }

        $subjectId = $_POST["subjectId"];

        // Delete the comment
        $this->apiLinker->deleteData("comment/".$idComment);

        // Redirect to homePage
        return $this->redirectToRoute("subject-detail", ["id" => $subjectId]);
    }
}