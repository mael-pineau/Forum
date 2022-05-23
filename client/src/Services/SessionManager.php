<?php

namespace App\Services;

use App\Services\Profil\ProfilService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SessionManager
{
    # --------------------------------
    # Attributes

    protected ApiLinker $apiLinker;
    protected RequestStack $requestStack;

    # --------------------------------
    # Constructor

    /**
     * SessionManager constructor.
     *
     * @param RequestStack $requestStack
     * @param ApiLinker $apiLinker
     */
    public function __construct(RequestStack $requestStack, ApiLinker $apiLinker)
    {
        $this->requestStack = $requestStack;
        $this->apiLinker = $apiLinker;
    }

    /**
     * Return the current Session
     *
     * @return mixed
     */
    public function getSession()
    {
        $session = $this->requestStack->getSession();

        return $session;
    }

    /**
     * Get a user based on the session
     *
     * @return mixed
     */
    public function getUserFromSession() {
        $session = $this->getSession();
        $userId = $session->get("idUser", null);

        // If there is no user connected return null;
        if ($userId == null) {
            return null;
        }
        $user = json_decode($this->apiLinker->readData("user/".$userId), true);

        return $user;
    }

    /**
     * Tell if the session has been initialized
     *
     * @return bool
     */
    public function isSessionInitialized() {

        // Get user
        $session = $this->requestStack->getSession();
        $userId = $session->get("idUser", null);

        // Check if session exist
        if ($userId == null) {
            return false;
        }

        return true;
    }
}
