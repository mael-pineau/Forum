<?php


namespace App\Services\FormDeban;

use App\Entity\Ban;
use App\Entity\Comment;
use App\Entity\FormDeban;
use App\Entity\Subject;
use App\Entity\User;
use App\Repository\BanRepository;
use App\Repository\CommentRepository;
use App\Repository\FormDebanRepository;
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

class FormDebanService
{
    # --------------------------------
    # Attributes

    protected FormDebanRepository $repository;
    protected ManagerRegistry $doctrine;

    # --------------------------------
    # Constructor

    /**
     * @param FormDebanRepository $repository
     * @param ManagerRegistry $doctrine
     */
    public function __construct(FormDebanRepository $repository, ManagerRegistry $doctrine)
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
    public function list()
    {
        // ----------------
        // Process

        // Get results
        $formDebans = $this->repository->findAll();

        // Serialize
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $formDebans = $serializer->serialize($formDebans, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $formDebans;
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
        $formDeban = $this->repository->find($id);

        // Serialize
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $formDeban = $serializer->serialize($formDeban, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $formDeban;
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
        $banId = $decodedData["banId"];

        // ----------------
        // Process

        //Get entity
        $user = $this->doctrine->getRepository(User::class)->find($userId);
        $ban = $this->doctrine->getRepository(Ban::class)->find($banId);

        // Create Entity
        $formDeban = new FormDeban();
        $formDeban->setMessage($message);
        $formDeban->setDate(time());
        //$formDeban->setIsRefused(null);
        $formDeban->setUser($user);
        $formDeban->setBan($ban);

        // Apply modifications
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($formDeban);
        $entityManager->flush();

        // Return created object
        $createdId = $formDeban->getId();
        $createdFormDeban = $this->repository->find($createdId);

        // Serialize user
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $formDeban = $serializer->serialize($createdFormDeban, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $formDeban;
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
        $is_refused = $decodedData["is_refused"];

        // ----------------
        // Process

        // Create Entity
        $entityManager = $this->doctrine->getManager();
        $formDeban = $this->repository->find($id);

        // Apply modifications
        if ($formDeban) {
            $formDeban->setIsRefused($is_refused);
            $entityManager->persist($formDeban);
            $entityManager->flush();
        }

        // Get modified comment
        $modifiedFormDeban = $this->repository->find($id);

        // Serialize user
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $formDeban = $serializer->serialize($modifiedFormDeban, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $formDeban;
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
        $formDeban = $this->repository->find($id);

        // Apply modififcations
        if ($formDeban) {
            $entityManager->remove($formDeban);
            $entityManager->flush();

            return Response::HTTP_NO_CONTENT;
        }

        return Response::HTTP_BAD_REQUEST;
    }
}