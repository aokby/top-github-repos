<?php

namespace App\Controller;

use Symfony\Component\{HttpFoundation\Request, Routing\Annotation\Route, HttpFoundation\Response};

/**
 *
 * default controller for discovering popular repositories on GitHub.
 * 
 */
class DefaultController
{
    /**
     * default action to return empty 200 response
     * it is used to test initial installation only
     * 
     * @return Response empty 200 response
     */
    public function index(): Response {
        return new Response();
    }
}