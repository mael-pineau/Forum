<?php

namespace App\Controller;

use App\Services\SessionManager;
use App\Services\Sign\SignService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Services\ApiLinker;

class SignController extends AbstractController
{
    # --------------------------------
    # Attributes

    protected SignService $service;
    protected ApiLinker $apiLinker;
    protected RequestStack $requestStack;
    protected SessionManager $manager;

    # --------------------------------
    # Constructor

    /**
     * SignController constructor.
     *
     * @param SignService $service
     * @param ApiLinker $apiLinker
     * @param RequestStack $requestStack
     * @param SessionManager $manager
     */
    public function __construct(SignService $service, ApiLinker $apiLinker, RequestStack $requestStack, SessionManager $manager)
    {
        $this->requestStack = $requestStack;
        $this->service = $service;
        $this->apiLinker = $apiLinker;
        $this->manager = $manager;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # Display

    /**
     * Display the sign in/up page
     *
     * @Route("/sign",
     *     name = "sign-user",
     *     methods={"GET"})
     */
    public function displaySignInUp()
    {
        // Check if user is already logged in
        $isSessionInitialized = $this->manager->isSessionInitialized();

        if (!$isSessionInitialized) {
            return $this->render('sign/display.sign.html.twig');
        }
        else {
            return $this->redirectToRoute('display-subjects');
        }
    }

    # ----------------
    # Validation

    /**
     * Sign out a user
     *
     * @Route("/sign-out",
     *     name = "sign-out-user",
     *     methods={"GET"})
     */
    public function signOut()
    {
        // Get current session
        $isSessionInitialized = $this->manager->isSessionInitialized();

        if ($isSessionInitialized) {
            $session = $this->manager->getSession();

            // Destroy the session
            $session->invalidate();
        }

        return $this->redirectToRoute("sign-user");
    }

    /**
     * Connect a user
     *
     * @Route("/sign-in",
     *     name = "sign-in-user",
     *     methods={"POST"})
     */
    public function validateSignIn()
    {
        // Check if user is already logged in
        $session = $this->requestStack->getSession();
        if ($session->get("idUser", null) == null) {

            // ----------------
            // Vars

            // Get data
            $mail = $_POST["sign-in-input-mail"];
            $password = $_POST["sign-in-input-password"];
            $hashedPassword = hash('sha256', $password);

            // ----------------
            // Process

            // Call service
            $user = $this->service->validateSignIn($mail, $hashedPassword);

            // If a user was found with this name and password
            if ($user != null) {

                // Check if user have a ban
                $ban = $this->service->checkBan($user);

                if ($ban != null) {
                    $banInfoArray = $this->service->getBanInfo($ban, $user);

                    // Display template with corresponding error messages
                    return $this->render('sign/display.sign.html.twig', [
                            "userId" => $user["id"],
                            "signInError" => $banInfoArray["signInError"],
                            "showFormDebanMessage" => $banInfoArray["showFormDebanMessage"],
                            "formDebanMessage" => $banInfoArray["formDebanMessage"],
                            "showFormDebanButton" => $banInfoArray["showFormDebanButton"],
                            "showTimeUntilDeban" => $banInfoArray["showTimeUntilDeban"],
                            "timeUntilDeban" => $banInfoArray["timeUntilDeban"],
                            "signInInputedMail" => $mail,
                        ]
                    );
                }

                // Create a connected session for the user
                $session = $this->requestStack->getSession();

                // stores an attribute in the session for later reuse
                $session->set('idUser', $user["id"]);

                return $this->redirectToRoute('display-subjects');

            } else {
                return $this->render('sign/display.sign.html.twig', [
                    "signInError" => "identifiant ou mot de passe incorrect",
                    "signInInputedMail" => $mail,
                    "signInInputedPassword" => $password]);
            }
        }
        else {
            return $this->redirectToRoute('display-subjects');
        }
    }

    /**
     * Register a user
     *
     * @Route("/sign-up",
     *     name = "sign-up-user",
     *     methods={"POST"})
     */
    public function validateSignUp()
    {
        // Check if user is already logged in
        $session = $this->requestStack->getSession();
        if ($session->get("idUser", null) == null) {
            // ----------------
            // Vars

            // Get data
            $mail = $_POST["sign-up-input-mail"];
            $username = $_POST["sign-up-input-username"];
            $password = $_POST["sign-up-input-password"];
            $confirmPassword = $_POST["sign-up-input-password-confirm"];

            // ----------------
            // Process

            // Call service
            $dataError = $this->service->validateSignUp($mail, $username, $password, $confirmPassword);

            // If an error was found, return the template with error messages
            if ($dataError) {
                return $this->render('sign/display.sign.html.twig', [
                        "signUpDefault" => true,
                        "signUpError" => $dataError,
                        "signUpInputedMail" => $mail,
                        "signUpInputedUsername" => $username,
                        "signUpInputedPassword" => $password,
                    ]
                );
            }

            // Else send user info to database
            $arrayDataToSend = array(
                "mail" => $mail,
                "username" => $username,
                "password" => hash('sha256', $password)
            );
            $dataToSend = json_encode($arrayDataToSend);

            // Add user to the database
            $resultRequest = $this->apiLinker->createData("user", $dataToSend);
            if ($resultRequest) {
                return $this->render('sign/display.sign.html.twig', ["signUpSuccessMessage" => "Vous êtes désormais bien inscrit! Vous pouvez désormais vous connecter afin d'accédé à la plateforme"]);
            }

            // If error when adding the user
            return $this->render('sign/display.sign.html.twig', [
                    "signUpDefault" => true,
                    "signUpError" => "Une erreur est survenue lors de votre inscription. Veuillez réessayé plus tard",
                    "signUpInputedMail" => $mail,
                    "signUpInputedUsername" => $username,
                    "signUpInputedPassword" => $password,
                ]
            );
        }
        else {
            return $this->redirectToRoute('display-subjects');
        }
    }
}