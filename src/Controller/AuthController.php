<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends Controller
{
    /**
     * @Route("/auth", name="auth")
     */
    public function index(): Response
    {
        return new JsonResponse([
            'success' => true,
            'user' => $this->getUser()?->getUsername()
        ]);
    }
}
