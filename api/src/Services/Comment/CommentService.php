<?php

namespace App\Services\Comment;

use App\Entity\Comment;
use App\Entity\Subject;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Exception\RepositoryException;
use Doctrine\ORM\Query\AST\Subselect;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CommentService
{
    # --------------------------------
    # Attributes

    protected CommentRepository $repository;
    protected ManagerRegistry $doctrine;

    # --------------------------------
    # Constructor

    /**
     * @param CommentRepository $repository
     * @param ManagerRegistry $doctrine
     */
    public function __construct(CommentRepository $repository, ManagerRegistry $doctrine)
    {
        $this->repository = $repository;
        $this->doctrine = $doctrine;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # GET

    /**
     * Get elements.
     *
     * @return string|null
     */
    public function list(): ?string
    {
        // ----------------
        // Process

        // Get results
        $comments = $this->repository->findAll();

        if ($comments) {

            // Serialize
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $comments = $serializer->serialize($comments, 'json', [
                'json_encode_options' => \JSON_UNESCAPED_UNICODE,
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);
        }

        return $comments;
    }

    /**
     * Get an element by id.
     *
     * @param int $id
     * @return string|null
     */
    public function detailById(int $id): ?string
    {
        // ----------------
        // Process

        // Get results
        $comment = $this->repository->find($id);

        if ($comment) {

            // Serialize
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $comment = $serializer->serialize($comment, 'json', [
                'json_encode_options' => \JSON_UNESCAPED_UNICODE,
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);
        }

        return $comment;
    }

    # ----------------
    # POST

    /**
     * Add an element.
     *
     * @param array $decodedData
     * @return String
     */
    public function add(array $decodedData): string
    {
        // ----------------
        // Vars

        $message = $decodedData["message"];
        $userId = $decodedData["userId"];
        $subjectId = $decodedData["subjectId"];

        // ----------------
        // Process

        //Get entity
        $user = $this->doctrine->getRepository(User::class)->find($userId);
        $subject = $this->doctrine->getRepository(Subject::class)->find($subjectId);

        // Create Entity
        $comment = new Comment();
        $comment->setMessage($message);
        $comment->setDate(time());
        $comment->setUser($user);
        $comment->setSubject($subject);

        // Apply modifications
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();

        // Return created object
        $createdId = $comment->getId();
        $createdComment = $this->repository->find($createdId);

        // Serialize user
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $comment = $serializer->serialize($createdComment, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $comment;
    }

    # ----------------
    # PUT

    /**
     * Update an element.
     *
     * @param array $decodedData
     * @param int $id
     * @return String
     */
    public function edit(array $decodedData, int $id): string
    {
        // ----------------
        // Vars

        // Fields
        $message = $decodedData["message"];

        // ----------------
        // Process

        // Create Entity
        $entityManager = $this->doctrine->getManager();
        $comment = $this->repository->find($id);

        // Apply modifications
        if ($comment) {
            $comment->setMessage($message);
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        // Get modified comment
        $modifiedComment = $this->repository->find($id);

        // Serialize user
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $comment = $serializer->serialize($modifiedComment, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $comment;
    }

    # ----------------
    # DELETE

    /**
     * Delete an element.
     *
     * @param int $id
     * @return int @HttpResponse
     */
    public function remove(int $id): int
    {
        // ----------------
        // Process

        // Create Entity
        $entityManager = $this->doctrine->getManager();
        $comment = $this->repository->find($id);

        // Apply modififcations
        if ($comment) {
            $entityManager->remove($comment);
            $entityManager->flush();

            return Response::HTTP_NO_CONTENT;
        }

        return Response::HTTP_BAD_REQUEST;
    }
}