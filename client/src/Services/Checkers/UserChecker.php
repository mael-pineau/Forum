<?php

namespace App\Services\Checkers;

class UserChecker {

    /**
     * Check data : sign-up.
     *
     * @param $mail
     * @param $username
     * @param $password
     * @param $confirmPassword
     * @return string|null
     */
    public function signUp($mail, $username, $password, $confirmPassword): ?string
    {
        $errorMessage = null;

        // Data - mail
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "L'adresse mail renseignée n'est pas valide";
            return $errorMessage;
        }

        // Data - username
        if (preg_match('/^[\w\d ]{2,20}$/', $username) == 0) {
            $errorMessage = "Un pseudo ne peut contenir que des lettres et des nombres avec 20 caractères maximum. Veuillez entrer un pseudo valide";
            return $errorMessage;
        }

        // Data - password
        if (preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d ]{5,}$/', $password) == 0) {
            $errorMessage = "Le mot de passe doit contenir au minimum 5 caractères dont une lettre et un chiffre";
            return $errorMessage;
        }

        return $errorMessage;
    }

    /**
     * Check data : username
     *
     * @param $username
     * @return string|null
     */
    public function checkUsername($username): ?string
    {
        $errorMessage = null;

        // Data - username
        if (preg_match('/^[\w\d ]{2,20}$/', $username) == 0) {
            $errorMessage = "Un pseudo ne peut contenir que des lettres et des nombres avec 20 caractères maximum. Veuillez entrer un pseudo valide";
            return $errorMessage;
        }

        return $errorMessage;
    }

    /**
     * Check data : password
     *
     * @param $password
     * @return string|null
     */
    public function checkPassword($password): ?string
    {
        $errorMessage = null;

        // Data - password
        if (preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d ]{5,}$/', $password) == 0) {
            $errorMessage = "Le mot de passe doit contenir au minimum 5 caractères dont une lettre et un chiffre";
            return $errorMessage;
        }

        return $errorMessage;
    }
}