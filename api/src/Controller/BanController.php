<?php


namespace App\Controller;

use App\Entity\User;
use App\Services\Ban\BanService;
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

class BanController extends AbstractController
{
    # --------------------------------
    # Attributes

    protected BanService $service;

    # --------------------------------
    # Constructor

    /**
     * User Controller constructor.
     *
     * @param BanService $service
     */
    public function __construct(BanService $service)
    {
        $this->service = $service;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # GET

    /**
     * GET all bans.
     *
     * @Route(
     *     "/api/ban",
     *     name = "ban-list",
     *     methods = {"GET"}
     * )
     *
     * @return Response
     */
    public function list(): Response
    {
        // ----------------
        // Process

        // Call service
        $comments = $this->service->list();

        // Return
        return new Response($comments, Response::HTTP_OK);
    }

    /**
     * GET a ban by id.
     *
     * @Route(
     *     "/api/ban/{id}",
     *     name = "ban-by-id",
     *     methods = {"GET"}
     * )
     *
     * @param int $id
     * @return Response
     */
    public function detail(int $id): Response
    {
        // ----------------
        // Process

        // Call service
        $ban = $this->service->detail($id);

        // Return
        return new Response($ban, Response::HTTP_OK);
    }

//    /**
//     * GET a ban by userId.
//     *
//     * @Route(
//     *     "/api/ban/userid/{userId}",
//     *     name = "ban-by-userid",
//     *     methods = {"GET"}
//     * )
//     *
//     * @param int $userId
//     * @return Response
//     */
//    public function getBanByUserId(int $userId): Response
//    {
//        // ----------------
//        // Process
//
//        // Call service
//        $ban = $this->service->detailByField('user', $userId);
//
//        // Return
//        return new Response($ban, Response::HTTP_OK);
//    }

    # ----------------
    # POST

    /**
     * CREATE a ban
     *
     * @Route("/api/ban",
     *     name = "add-ban",
     *     methods={"POST"}
     *     )
     *
     * @return Response
     */
    public function add(): Response
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
        $ban = $this->service->add($decodedData);

        // Return
        return new Response($ban, Response::HTTP_CREATED);

    }

    # ----------------
    # PUT

    // Probably not useful

//    /**
//     * UPDATE a ban
//     *
//     * @Route("/api/comment/{id}",
//     *     name = "update-comment",
//     *     methods={"PUT"}
//     *     )
//     *
//     * @param int $id
//     * @return Response
//     */
//    public function updateUser(int $id): Response
//    {
//        // ----------------
//        // Vars
//
//        // Get data
//        $request = Request::createFromGlobals();
//        $data = $request->getContent();
//
//        $decodedData = json_decode($data, true);
//
//        // Call service
//        $comment = $this->service->edit($decodedData, $id);
//
//        // Return
//        return new Response($comment, Response::HTTP_OK);
//    }

    # ----------------
    # DELETE

    /**
     * DELETE a ban
     *
     * @Route("/api/ban/{id}",
     *     name = "remove-ban",
     *     methods={"DELETE"}
     *     )
     *
     * @param $id
     * @return Response
     */
    public function remove($id): Response
    {
        // Call service
        $response = $this->service->remove($id);

        // Return
        return new Response(null, $response);
    }
}