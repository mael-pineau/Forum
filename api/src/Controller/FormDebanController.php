<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\Ban\BanService;
use App\Services\Comment\CommentService;
use App\Services\FormDeban\FormDebanService;
use App\Services\User\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FormDebanController extends AbstractController
{
    # --------------------------------
    # Attributes

    protected FormDebanService $service;

    # --------------------------------
    # Constructor

    /**
     * User Controller constructor.
     *
     * @param FormDebanService $service
     */
    public function __construct(FormDebanService $service)
    {
        $this->service = $service;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # GET

    /**
     * GET all formdebans
     *
     * @Route(
     *     "/api/formdebans",
     *     name = "formdeban-list",
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
        $formDebans = $this->service->list();

        // Return
        return new Response($formDebans, Response::HTTP_OK);
    }

    /**
     * GET a formDeban by id.
     *
     * @Route(
     *     "/api/formdeban/{id}",
     *     name = "formdeban-by-id",
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
        $formDeban = $this->service->detail($id);

        // Return
        return new Response($formDeban, Response::HTTP_OK);
    }

    # ----------------
    # POST

    /**
     * CREATE a formDeban
     *
     * @Route("/api/formdeban",
     *     name = "add-formDeban",
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
        $formDeban = $this->service->add($decodedData);

        echo 'hi3';

        // Return
        return new Response($formDeban, Response::HTTP_CREATED);

    }

    # ----------------
    # PUT

    /**
     * UPDATE a formDeban
     *
     * @Route("/api/formDeban/{id}",
     *     name = "update-formDeban",
     *     methods={"PUT"}
     *     )
     *
     * @param int $id
     * @return Response
     */
    public function update(int $id): Response
    {
        // ----------------
        // Vars

        // Get data
        $request = Request::createFromGlobals();
        $data = $request->getContent();

        $decodedData = json_decode($data, true);

        // Call service
        $formDeban = $this->service->edit($decodedData, $id);

        // Return
        return new Response($formDeban, Response::HTTP_OK);
    }

    # ----------------
    # DELETE

    /**
     * DELETE a formDeban
     *
     * @Route("/api/formDeban/{id}",
     *     name = "remove-formDeban",
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