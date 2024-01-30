<?php

namespace App\Controller;

use App\Request\CreateSpiceRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SystemController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->json(['message' => 'Welcome Home']);
    }

    #[Route('/{path}', name: 'page_not_found', requirements: ['path' => '.*'])]
    public function notFound(): Response
    {
        return $this->json(['error' => 'Page Not Found'], 404);
    }
}
