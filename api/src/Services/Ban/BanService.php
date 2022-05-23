<?php


namespace App\Services\Ban;

use App\Entity\Ban;
use App\Entity\Comment;
use App\Entity\Subject;
use App\Entity\User;
use App\Repository\BanRepository;
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

class BanService
{
    # --------------------------------
    # Attributes

    protected BanRepository $repository;
    protected ManagerRegistry $doctrine;

    # --------------------------------
    # Constructor

    /**
     * @param BanRepository $repository
     * @param ManagerRegistry $doctrine
     */
    public function __construct(BanRepository $repository, ManagerRegistry $doctrine)
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
        $bans = $this->repository->findAll();

        if ($bans) {


            // Serialize
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $bans = $serializer->serialize($bans, 'json', [
                'json_encode_options' => \JSON_UNESCAPED_UNICODE,
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);
        }

        return $bans;
    }

    /**
     * Get an element by id.
     *
     * @param int $id
     * @return string|null
     */
    public function detail(int $id): ?string
    {
        // ----------------
        // Process

        // Get results
        $ban = $this->repository->find($id);

        if ($ban) {

            // Serialize
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $ban = $serializer->serialize($ban, 'json', [
                'json_encode_options' => \JSON_UNESCAPED_UNICODE,
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);
        }

        return $ban;
    }

//    /**
//     * Get an element by criteria.
//     *
//     * @param String $field
//     * @param int $int
//     * @return string|null
//     */
//    public function detailByField(String $field, int $int): ?string
//    {
//        // ----------------
//        // Process
//
//        //$array = array("user");
//
//        // Get results
//        $user = $this->repository->findOneBy(array($field => $int));
//
//        if ($user) {
//            // Serialize
//            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
//            $user = $serializer->serialize($user, 'json', [
//                'json_encode_options' => \JSON_UNESCAPED_UNICODE,
//                'circular_reference_handler' => function ($object) {
//                    return $object->getId();
//                }
//            ]);
//        }
//        else {
//            $user = null;
//        }
//
//        return $user;
//    }

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

        $is_permanent = $decodedData["is_permanent"];
        $reason = $decodedData["reason"];
        $dateDeban = $decodedData["dateDeban"];
        $userId = $decodedData["userId"];

        // ----------------
        // Process

        //Get entity
        $user = $this->doctrine->getRepository(User::class)->find($userId);

        // Create Entity
        $ban = new Ban();
        $ban->setDate(time());
        $ban->setIsPermanent($is_permanent);
        $ban->setReason($reason);
        $ban->setDateDeban($dateDeban);
        $ban->setUser($user);

        // Apply modifications
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($ban);
        $entityManager->flush();

        // Return created object
        $createdId = $ban->getId();
        $createdBan = $this->repository->find($createdId);

        // Serialize user
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $ban = $serializer->serialize($createdBan, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $ban;
    }

    # ----------------
    # PUT

    // Porbably not useful

//    /**
//     * Update an element.
//     *
//     * @param array $decodedData
//     * @param int $id
//     * @return String
//     */
//    public function edit(array $decodedData, int $id): string
//    {
//        // ----------------
//        // Vars
//
//        // Fields
//        $message = $decodedData["message"];
//
//        // ----------------
//        // Process
//
//        // Create Entity
//        $entityManager = $this->doctrine->getManager();
//        $comment = $this->repository->find($id);
//
//        // Apply modifications
//        if ($comment) {
//            $comment->setMessage($message);
//            $entityManager->persist($comment);
//            $entityManager->flush();
//        }
//
//        // Get modified comment
//        $modifiedComment = $this->repository->find($id);
//
//        // Serialize user
//        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
//        $comment = $serializer->serialize($modifiedComment, 'json', [
//            'circular_reference_handler' => function ($object) {
//                return $object->getId();
//            }
//        ]);
//
//        return $comment;
//    }

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
        $ban = $this->repository->find($id);

        // Apply modififcations
        if ($ban) {
            $entityManager->remove($ban);
            $entityManager->flush();

            return Response::HTTP_NO_CONTENT;
        }

        return Response::HTTP_BAD_REQUEST;
    }
}