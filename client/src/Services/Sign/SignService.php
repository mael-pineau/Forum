<?php

namespace App\Services\Sign;

use App\Services\ApiLinker;
use App\Services\Checkers\UserChecker;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SignService {

    # --------------------------------
    # Attributes

    protected ApiLinker $apiLinker;
    protected UserChecker $checker;

    # --------------------------------
    # Constructor

    /**
     * SignService constructor.
     *
     */
    public function __construct(ApiLinker $apiLinker, UserChecker $checker)
    {
        $this->apiLinker = $apiLinker;
        $this->checker = $checker;
    }

    # --------------------------------
    # Core methods

    # ----------------
    # Validations

    /**
     * Check if a user exist with those informations.
     *
     * @param String $mail
     * @param String $hashedPassword
     * @return mixed
     */
    public function validateSignIn(String $mail, String $hashedPassword)
    {

        $user = json_decode($this->apiLinker->readData("user/mail/".$mail), true);
        if ($user != null) {

            $userPassword = $user["password"];

            if ($hashedPassword == $userPassword) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Validation process when a user sign-up
     *
     * @param String $mail
     * @param String $username
     * @param String $password
     * @param String $confirmPassword
     * @return String|null
     */
    public function validateSignUp(String $mail, String $username, String $password, String $confirmPassword): ?String
    {
        $errorMessage = $this->checker->signUp($mail, $username, $password, $confirmPassword);

        if (!$errorMessage) {

            // Check if mail already exist in database
            $user = json_decode($this->apiLinker->readData("user/mail/".$mail), true);

            if ($user != null) {
                $errorMessage = "Un compte existe déja avec cette adresse mail";
                return $errorMessage;
            }

            // Check if username already exist in database
            $user = json_decode($this->apiLinker->readData("user/username/".$username), true);
            if ($user != null) {
                $errorMessage = "Ce pseudo est déja pris";
                return $errorMessage;
            }

            if ($password != $confirmPassword) {
                $errorMessage = "Les mots de passe ne sont pas les mêmes";
                return $errorMessage;
            }
        }

        return $errorMessage;
    }

    /**
     * Check if the user is banned
     *
     * @param $user
     * @return array|null
     */
    public function checkBan($user): ?array
    {
        $ban = $user["ban"];

        // If user have a ban
        if ($ban != null) {

            if ($ban["dateDeban"] != null) {
                // If currentDate is past dateDeban => delete the ban
                if (time() > $ban["dateDeban"]) {
                    $idBan = $user["ban"]["id"];
                    $this->apiLinker->deleteData("ban/" . $idBan);
                    $ban = null;
                }
            }
        }

        return $ban;
    }

    # ----------------
    # Ban

    /**
     * Get the general ban informations to return to the template
     *
     * @param $user
     * @return array
     */
    public function getBanInfo($ban, $user): array
    {
        // Default values
        $banErrorMessage = "Votre compte a été suspendu de manière temporaire";
        $formDebanMessage = "Vous pouvez, si vous le souhaiter, écrire un formulaire de réclamation";
        $showFormDebanMessage = true;
        $showFormDebanButton = true;
        $showTimeUntilDeban = true;
        $timeUntilDeban = null;

        // Date when the user will be debanned
        $dateDeban = $ban["dateDeban"];

        if ($dateDeban != null) {
            $currentTime = time();

            // Seconds between now and the deban date
            $timeUntilDeban = $dateDeban - $currentTime;

            // If user is banned for not too long, he can't send a FormDeban
            if ($timeUntilDeban < 43200) {
                $formDebanMessage = "Le temps de suspension de votre compte est trop court pour que vous puissiez faire une réclamation. Veuillez patienter le temps restant indiqué";
                $showFormDebanMessage = true;
                $showFormDebanButton = false;
            }
            elseif ($timeUntilDeban < 15) {
                $formDebanMessage = "Votre compte sera bientôt de nouveau accessible, veuillez patientez encore quelque secondes";
                $showFormDebanMessage = true;
                $showTimeUntilDeban = false;
                $showFormDebanButton = false;
            }
        }

        if ($ban["isPermanent"] == true) {
            $banErrorMessage = "Votre compte a été suspendu de manière définitive";
            $showFormDebanMessage = true;
            $showFormDebanButton = true;
            $showTimeUntilDeban = false;
        }

        // Check if user has already send a FormDeban
        $formDeban = $user["formDeban"];

        if ($formDeban != null) {
            $isFormRefused = $formDeban["isRefused"];

            if ($isFormRefused == true) {
                $formDebanMessage = "Votre demande de réclamation a été rejeté";
                $showFormDebanMessage = true;
                $showFormDebanButton = false;
            }
            elseif ($isFormRefused == null) {
                $formDebanMessage = "Votre demande de réclamation n'a pas encore été étudiée";
                $showFormDebanMessage = true;
                $showFormDebanButton = false;
            }
            elseif ($isFormRefused == false) {
                $formDebanMessage = "Votre demande de réclamation a été accepté. Vous pourrez bientôt vous connecter à votre compte";
                $showFormDebanMessage = true;
                $showFormDebanButton = false;
            }
        }

        if ($timeUntilDeban != null) {

            // Transform time into a string message to display
            $timeUntilDeban = $this->secondsToString($timeUntilDeban);
        }

        $infoToReturn = array(
            "signInError" => $banErrorMessage,
            "showFormDebanMessage" => $showFormDebanMessage,
            "formDebanMessage" => $formDebanMessage,
            "showFormDebanButton" => $showFormDebanButton,
            "showTimeUntilDeban" => $showTimeUntilDeban,
            "timeUntilDeban" => $timeUntilDeban
        );

        return $infoToReturn;
    }

    /**
     * Transform time of a ban into string
     *
     * @param $user
     * @return string
     */
    public function secondsToString($time)
    {
        $str = "";
        $minutes = floor(($time%3600)/60);
        $hours = floor(($time%86400)/3600);
        $days = floor(($time%2592000)/86400);
        $month = floor($time/2592000);

        if ($month != 0) {
            $str = "$month mois $days jours $hours h $minutes min";
        }
        elseif ($days != 0) {
            $str = "$days jours $hours h $minutes min";
        }
        elseif ($hours != 0) {
            $str = "$hours h $minutes minutes ";
        }
        elseif ($minutes != 0) {
            $minutes = $minutes + 1;
            $str = "$minutes m";
        }
        elseif ($minutes == 0) {
            $str = "1 m ";
        }

        return $str;
    }
}