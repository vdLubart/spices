<?php

namespace App\Controller;

use App\Request\CreateSpiceRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SystemController extends AbstractController
{
    #[Route('/{path}', name: 'page_not_found', requirements: ['path' => '.*'])]
    public function notFound(CreateSpiceRequest $request): Response
    {
        return $this->json('', 404);
    }
}
