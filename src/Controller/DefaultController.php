<?php

namespace App\Controller;

use App\Service\ModelService;
use App\Service\SetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(SetService $setService, ModelService $modelService): Response
    {
        return $this->render('default/index.html.twig', [
            'models' => $modelService->getTotalCount(),
            'sets' => $setService->getTotalCount(),
        ]);
    }
}
