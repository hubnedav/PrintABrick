<?php

namespace App\Controller;

use App\Service\ModelService;
use App\Service\SetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        ]);
    }

    /**
     * @Route("/set_tab/{tab}", name="set_tab", requirements={"tab"=".+"}, methods={"POST"})
     *
     * @param $tab
     */
    public function setDefaultTabAction(Request $request, $tab): Response
    {
        $session = $request->getSession();
        $session->set('tab', $tab);

        return new Response('OK');
    }
}
