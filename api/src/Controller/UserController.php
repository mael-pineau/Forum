<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\User\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserController extends AbstractController
{
    # --------------------------------
    # Attributes

    protected UserService $service;

    # --------------------------------
    # Constructor

    /**
     * User Controller constructor.
     *
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # GET

    /**
     * GET all users.
     *
     * @Route(
     *     "/api/user",
     *     name = "user-list",
     *     methods = {"GET"}
     * )
     *
     * @return Response
     */
    public function getUsers(): Response
    {
        // ----------------
        // Process

        // Call service
        $users = $this->service->list();

        // Return
        return new Response($users, Response::HTTP_OK);
    }

    /**
     * GET a user by id.
     *
     * @Route(
     *     "/api/user/{id}",
     *     name = "user-by-id",
     *     methods = {"GET"}
     * )
     *
     * @param int $id
     * @return Response
     */
    public function getUserById(int $id): Response
    {
        // ----------------
        // Process

        // Call service
        $user = $this->service->detailById($id);

        // Return
        return new Response($user, Response::HTTP_OK);
    }

    /**
     * GET a user by mail.
     * Note : a mail address contain a ".", it's not possible to pass it directly in the url because it cause an error
     *
     * @Route(
     *     "/api/user/mail/{mail}",
     *     name = "user-by-mail",
     *     methods = {"GET"}
     * )
     *
     * @return Response
     */
    public function getUserByMail(String $mail): Response
    {
        // ----------------
        // Process

        // Call service
        $user = $this->service->detailByField('mail', $mail);

        // Return
        return new Response($user, Response::HTTP_OK);
    }

    /**
     * Get a user by username.
     *
     * @Route(
     *     "/api/user/username/{username}",
     *     name = "check-username",
     *     methods = {"GET"}
     * )
     *
     * @param String $username
     * @return Response
     */
    public function getUserByUsername(String $username): Response
    {
        // ----------------
        // Process

        // Call service
        $user = $this->service->detailByField('username', $username);

        // Return
        return new Response($user, Response::HTTP_OK);
    }

    # ----------------
    # POST

    /**
     * CREATE a user
     *
     * @Route("/api/user",
     *     name = "add-user",
     *     methods={"POST"}
     *     )
     *
     * @return Response
     */
    public function addUser(): Response
    {
        // ----------------
        // Vars

        // Get data
        $request = Request::createFromGlobals();

        // ----------------
        // Process

        // Prepare data
        $data = $request->getContent();
        echo $data;
        $decodedData = json_decode($data, true);

        // Call service
        $user = $this->service->add($decodedData);

        // Return
        return new Response($user, Response::HTTP_CREATED);

    }

    # ----------------
    # PUT

    /**
     * UPDATE a user
     *
     * @Route("/api/user/{id}",
     *     name = "update-user",
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
        $user = $this->service->edit($decodedData, $id);

        // Return
        return new Response($user, Response::HTTP_OK);
    }

    # ----------------
    # DELETE

    /**
     * DELETE a user
     *
     * @Route("/api/user/{id}",
     *     name = "remove-user",
     *     methods={"DELETE"}
     *     )
     *
     * @param ManagerRegistry $doctrine
     * @param $id
     * @return Response
     */
    public function removeUser(ManagerRegistry $doctrine, $id): Response
    {
        // Call service
        $response = $this->service->remove($id);

        // Return
        return new Response(null, $response);
    }
}