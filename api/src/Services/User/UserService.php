<?php

namespace App\Services\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\Checkers\UserChecker;
use Doctrine\ORM\Exception\RepositoryException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserService
{
    # --------------------------------
    # Attributes

    protected UserRepository $repository;
    protected ManagerRegistry $doctrine;
    protected UserChecker $checker;

    # --------------------------------
    # Constructor

    /**
     * @param UserRepository $repository
     * @param ManagerRegistry $doctrine
     */
    public function __construct(UserRepository $repository, ManagerRegistry $doctrine, UserChecker $checker)
    {
        $this->repository = $repository;
        $this->doctrine = $doctrine;
        $this->checker = $checker;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # GET

    /**
     * Get elements.
     *
     * @return string
     */
    public function list(): string
    {
        // ----------------
        // Process

        // Get results
        $users = $this->repository->findAll();

        if ($users) {
            // Serialize
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $users = $serializer->serialize($users, 'json', [
                'json_encode_options' => \JSON_UNESCAPED_UNICODE,
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);
        }

        return $users;
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
        $user = $this->repository->find($id);

            // Serialize
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $user = $serializer->serialize($user, 'json', [
                'json_encode_options' => \JSON_UNESCAPED_UNICODE,
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);


        return $user;
    }

    /**
     * Get an element by criteria.
     *
     * @param String $field
     * @param String $string
     * @return string|null
     */
    public function detailByField(String $field, String $string): ?string
    {
        // ----------------
        // Process

        // Get results
        $user = $this->repository->findOneBy(array($field => $string));

        if ($user) {
            // Serialize
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $user = $serializer->serialize($user, 'json', [
                'json_encode_options' => \JSON_UNESCAPED_UNICODE,
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);
        }
        else {
            $user = null;
        }

        return $user;
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

        $mail = $decodedData["mail"];
        $username = $decodedData["username"];
        $password = $decodedData["password"];

        // ----------------
        // Process

        // Check
        $this->checker->add($mail, $username, $password);

        // Create Entity
        $user = new User();
        $user->setMail($mail);
        $user->setUsername($username);
        $user->setPassword($password);
        // Default values
        $user->setImage("default.png");
        $user->setIsAdmin(false);

        // Apply modifications
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Return created object
        $createdId = $user->getId();
        $createdUser = $this->repository->find($createdId);

        // Serialize user
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $user = $serializer->serialize($createdUser, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $user;
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
        $mail = $decodedData["mail"];
        $username = $decodedData["username"];
        $password = $decodedData["password"];
        $image = $decodedData["image"];
        $is_admin = $decodedData["is_admin"];

        // ----------------
        // Process

        // Create Entity
        $entityManager = $this->doctrine->getManager();
        $user = $this->repository->find($id);

        // Apply modifications
        if ($user) {
            $user->setMail($mail);
            $user->setUsername($username);
            $user->setPassword($password);
            $user->setImage($image);
            $user->setIsAdmin($is_admin);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        // Get modified user
        $modifiedUser = $this->repository->find($id);

        // Serialize user
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $user = $serializer->serialize($modifiedUser, 'json', [
            'json_encode_options' => \JSON_UNESCAPED_UNICODE,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return $user;
    }

    # ----------------
    # DELETE

    /**
     * Delete an element.
     *
     * @param int $id
     * @return int @HttpResponse
     */
    public function remove(int $id)
    {
        // ----------------
        // Process

        // Create Entity
        $entityManager = $this->doctrine->getManager();
        $user = $this->repository->find($id);

        // Apply modififcations
        if ($user) {
            $entityManager->remove($user);
            $entityManager->flush();

            return Response::HTTP_NO_CONTENT;
        }

        return Response::HTTP_BAD_REQUEST;
    }
}