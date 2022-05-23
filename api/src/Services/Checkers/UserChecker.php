<?php

namespace App\Services\Checkers;

use Symfony\Component\Config\Definition\Exception\Exception;

class UserChecker {

    /**
     * Check data : add.
     *
     * @param $mail
     * @param $username
     * @param $password
     * @return string|null
     */
    public function add($mail, $username, $password): ?string
    {
        $errorMessage = null;

        // Data - name
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("L'adresse mail n'est pas valide");
        }

        // Data - username
        if (preg_match('/^[\w\d ]{2,20}$/', $username) == 0) {
            throw new Exception($errorMessage = "Un pseudo ne peut contenir que des lettres et des nombres avec 20 caractères maximum. Veuillez entrer un pseudo valide");
        }

        // Data - password
        if (preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d ]{5,}$/', $password) == 0) {
            throw new Exception($errorMessage = "Le mot de passe doit contenir au minimum une lettre, un chiffre et 5 caractères");
        }

        return $errorMessage;
    }
}