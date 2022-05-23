<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\Comment\CommentService;
use App\Services\User\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CommentController extends AbstractController
{
    # --------------------------------
    # Attributes

    protected CommentService $service;

    # --------------------------------
    # Constructor

    /**
     * User Controller constructor.
     *
     * @param CommentService $service
     */
    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # GET

    /**
     * GET all comments.
     *
     * @Route(
     *     "/api/comment",
     *     name = "comment-list",
     *     methods = {"GET"}
     * )
     *
     * @return Response
     */
    public function getComments(): Response
    {
        // ----------------
        // Process

        // Call service
        $comments = $this->service->list();

        // Return
        return new Response($comments, Response::HTTP_OK);
    }

    /**
     * GET a comment by id.
     *
     * @Route(
     *     "/api/comment/{id}",
     *     name = "comment-by-id",
     *     methods = {"GET"}
     * )
     *
     * @param int $id
     * @return Response
     */
    public function getCommentById(int $id): Response
    {
        // ----------------
        // Process

        // Call service
        $user = $this->service->detailById($id);

        // Return
        return new Response($user, Response::HTTP_OK);
    }

    # ----------------
    # POST

    /**
     * CREATE a comment
     *
     * @Route("/api/comment",
     *     name = "add-comment",
     *     methods={"POST"}
     *     )
     *
     * @return Response
     */
    public function addComment(): Response
    {
        // ----------------
        // Vars

        // Get data
        $request = Request::createFromGlobals();

        //TODO check data

        // ----------------
        // Process

        // Prepare data
        $data = $request->getContent();
        $decodedData = json_decode($data, true);

        // Call service
        $comment = $this->service->add($decodedData);

        // Return
        return new Response($comment, Response::HTTP_CREATED);

    }

    # ----------------
    # PUT

    /**
     * UPDATE a comment
     *
     * @Route("/api/comment/{id}",
     *     name = "update-comment",
     *     methods={"PUT"}
     *     )
     *
     * @param int $id
     * @return Response
     */
    public function updateUser(int $id): Response
    {
        // ----------------
        // Vars

        // Get data
        $request = Request::createFromGlobals();
        $data = $request->getContent();

        $decodedData = json_decode($data, true);

        // Call service
        $comment = $this->service->edit($decodedData, $id);

        // Return
        return new Response($comment, Response::HTTP_OK);
    }

    # ----------------
    # DELETE

    /**
     * DELETE a comment
     *
     * @Route("/api/comment/{id}",
     *     name = "remove-comment",
     *     methods={"DELETE"}
     *     )
     *
     * @param $id
     * @return Response
     */
    public function removeComment($id): Response
    {
        // Call service
        $response = $this->service->remove($id);

        // Return
        return new Response(null, $response);
    }
}