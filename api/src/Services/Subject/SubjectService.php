<?php

namespace App\Services\Subject;

use App\Entity\Subject;
use App\Entity\User;
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

class SubjectService
{
    # --------------------------------
    # Attributes

    protected SubjectRepository $repository;
    protected ManagerRegistry $doctrine;

    # --------------------------------
    # Constructor

    /**
     * @param SubjectRepository $repository
     * @param ManagerRegistry $doctrine
     */
    public function __construct(SubjectRepository $repository, ManagerRegistry $doctrine)
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
    public function list($decodedData): ?string
    {
        // ----------------
        // Process

        // Check if subjects need to be sorted by a criteria
        if ($decodedData != null) {

            // If subjects need to be ordered by the most recent ones
            if ($decodedData["criteria"] == "mostRecents") {
                $subjects = $this->repository->createQueryBuilder('subject')
                    ->orderBy("subject.date", "DESC")
                    ->groupBy("subject")
                    ->getQuery()
                    ->getResult();
            }

            // If subjects need to be ordered by the oldest ones
            if ($decodedData["criteria"] == "oldests") {
                $subjects = $this->repository->createQueryBuilder('subject')
                    ->orderBy("subject.date", "ASC")
                    ->groupBy("subject")
                    ->getQuery()
                    ->getResult();
            }

            // If subjects need to be ordered by number of comments
            if ($decodedData["criteria"] == "mostComments") {
                $subjects = $this->repository->createQueryBuilder('subject')
                    ->leftJoin("App\Entity\Comment", "comment", "WITH", "subject.id = comment.subject")
                    ->orderBy("count(comment.id)", "DESC")
                    ->groupBy("subject")
                    ->getQuery()
                    ->getResult();
            }

            // If subjects need to be ordered by the least amount of comments
            if ($decodedData["criteria"] == "leastComments") {
                $subjects = $this->repository->createQueryBuilder('subject')
                    ->leftJoin("App\Entity\Comment", "comment", "WITH", "subject.id = comment.subject")
                    ->orderBy("count(comment.id)", "ASC")
                    ->groupBy("subject")
                    ->getQuery()
                    ->getResult();
            }
        }

        // By default sort them by the most recent ones
        else {

            // If subjects need to be ordered by the most recent ones
            $subjects = $this->repository->createQueryBuilder('subject')
                ->orderBy("subject.date", "DESC")
                ->groupBy("subject")
                ->getQuery()
                ->getResult();
        }

        // Serialize
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $subjects = $serializer->serialize($subjects, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $subjects;
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
        $subject = $this->repository->find($id);

        if ($subject) {

            // Serialize
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $subject = $serializer->serialize($subject, 'json', [
                'json_encode_options' => \JSON_UNESCAPED_UNICODE,
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);
        }

        return $subject;
    }

    /**
     * Search elements by string.
     *
     * @param String $str
     * @return string|null
     */
    public function searchByString(String $str): ?string
    {
        // ----------------
        // Process

//        $connexion = $this->doctrine->getConnection();

//        $sql = 'SELECT * FROM subject
//                WHERE subject.title
//                LIKE "%":string"%"';
//
//        $stmt = $connexion->prepare($sql);
//        $results = $stmt->executeQuery(['string' => $str]);
//        $subjects = $results->fetchAllAssociative();

        // Get results
        $subjects = $this->repository->createQueryBuilder('subject')
            ->where('subject.title LIKE :str')
            ->setParameter('str', "%".$str."%")
            ->getQuery()
            ->getResult();

        if ($subjects) {
            // Serialize
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $subjects = $serializer->serialize($subjects, 'json', [
                'json_encode_options' => \JSON_UNESCAPED_UNICODE,
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);
        }
        else {
            $subjects = null;
        }

        return $subjects;
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

        $title = $decodedData["title"];
        $description = $decodedData["description"];
        $userId = $decodedData["userId"];

        // ----------------
        // Process

        //Get entity
        $user = $this->doctrine->getRepository(User::class)->find($userId);

        // Create Entity
        $subject = new Subject();
        $subject->setTitle($title);
        $subject->setDescription($description);
        $subject->setDate(time());
         $subject->setIsClosed(false);
        $subject->setUser($user);

        // Apply modifications
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($subject);
        $entityManager->flush();

        // Return created object
        $createdId = $subject->getId();
        $createdSubject = $this->repository->find($createdId);

        // Serialize subject
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $subject = $serializer->serialize($createdSubject, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $subject;
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
        $title = $decodedData["title"];
        $description = $decodedData["description"];
        $is_closed = $decodedData["is_closed"];

        // ----------------
        // Process

        // Create Entity
        $entityManager = $this->doctrine->getManager();
        $subject = $this->repository->find($id);

        // Apply modifications
        if ($subject) {
            $subject->setTitle($title);
            $subject->setDescription($description);
            $subject->setIsClosed($is_closed);
            $entityManager->persist($subject);
            $entityManager->flush();
        }

        // Get modified subject
        $modifiedSubject = $this->repository->find($id);

        // Serialize user
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $subject = $serializer->serialize($modifiedSubject, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $subject;
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
        $subject = $this->repository->find($id);

        // Apply modififcations
        if ($subject) {
            $entityManager->remove($subject);
            $entityManager->flush();

            return Response::HTTP_NO_CONTENT;
        }

        return Response::HTTP_BAD_REQUEST;
    }
}