<?php

namespace FrontBundle\Controller;

use AppBundle\Service\ModelService;
use AppBundle\Service\SetService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(SetService $setService, ModelService $modelService)
    {
        return $this->render('default/index.html.twig', [
            'models' => $modelService->getTotalCount(),
            'sets' => $setService->getTotalCount(),
        ]);
    }
}
