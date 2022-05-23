<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Entity\User;
use App\Services\Subject\SubjectService;
use App\Services\User\UserService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SubjectController extends AbstractController
{
    # --------------------------------
    # Attributes

    protected SubjectService $service;

    # --------------------------------
    # Constructor

    /**
     * Subject Controller constructor.
     *
     * @param SubjectService $service
     */
    public function __construct(SubjectService $service)
    {
        $this->service = $service;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # GET

    /**
     * GET all subjects.
     *
     * @Route(
     *     "/api/subject",
     *     name = "subject-list",
     *     methods = {"GET"}
     * )
     *
     * @return Response
     */
    public function getSubjects()
    {
        // ----------------
        // Process

        // Get data (order by criteria)
        $request = Request::createFromGlobals();

        // ----------------
        // Process

        // Prepare data
        $data = $request->getContent();
        $decodedData = json_decode($data, true);

        // Call service
        $subjects = $this->service->list($decodedData);

        // Return
        return new Response($subjects, Response::HTTP_OK);
    }

    /**
     * GET a subject by id.
     *
     * @Route(
     *     "/api/subject/{id}",
     *     name = "subject-by-id",
     *     methods = {"GET"}
     * )
     *
     * @param int $id
     * @return Response
     */
    public function getSubjectById(int $id): Response
    {
        // ----------------
        // Process

        // Call service
        $user = $this->service->detailById($id);

        // Return
        return new Response($user, Response::HTTP_OK);
    }

    /**
     * GET subjects that contains a certain string.
     *
     * @Route(
     *     "/api/subject/search/{str}",
     *     name = "subject-by-string",
     *     methods = {"GET"}
     * )
     *
     * @param String $str
     * @return Response
     */
    public function searchSubjectByString(String $str): Response
    {
        // ----------------
        // Process

        // Call service
        $subject = $this->service->searchByString($str);

        // Return
        return new Response($subject, Response::HTTP_OK);
    }

    # ----------------
    # POST

    /**
     * CREATE a subject
     *
     * @Route("/api/subject",
     *     name = "add-subject",
     *     methods={"POST"}
     *     )
     *
     * @return Response
     */
    public function addSubject(): Response
    {
        // ----------------
        // Vars

        // Get data
        $request = Request::createFromGlobals();

        // ----------------
        // Process

        // Prepare data
        $data = $request->getContent();
        $decodedData = json_decode($data, true);

        // Call service
        $user = $this->service->add($decodedData);

        // Return
        return new Response($user, Response::HTTP_CREATED);
    }

    # ----------------
    # PUT

    /**
     * UPDATE a subject
     *
     * @Route("/api/subject/{id}",
     *     name = "update-subject",
     *     methods={"PUT"}
     *     )
     *
     * @param int $id
     * @return Response
     */
    public function updateSubject(int $id): Response
    {
        // ----------------
        // Vars

        // Get data
        $request = Request::createFromGlobals();
        $data = $request->getContent();

        $decodedData = json_decode($data, true);

        // Call service
        $subject = $this->service->edit($decodedData, $id);

        // Return
        return new Response($subject, Response::HTTP_OK);
    }

    # ----------------
    # DELETE

    /**
     * DELETE a subject
     *
     * @Route("/api/subject/{id}",
     *     name = "remove-subject",
     *     methods={"DELETE"}
     *     )
     *
     * @param int $id
     * @return Response
     */
    public function removeSubject(int $id): Response
    {
        // Call service
        $response = $this->service->remove($id);

        // Return
        return new Response(null, $response);
    }
}