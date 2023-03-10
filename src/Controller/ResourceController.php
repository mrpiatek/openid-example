<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResourceController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin(): Response
    {
        return new JsonResponse([
            'page' => 'admin',
            'user' => $this->getUser()?->getUsername()
        ]);
    }

    /**
     * @Route("/resource", name="resource")
     */
    public function resource(): Response
    {
        return new JsonResponse([
            'page' => 'resource',
            'user' => $this->getUser()?->getUsername()
        ]);
    }
}
