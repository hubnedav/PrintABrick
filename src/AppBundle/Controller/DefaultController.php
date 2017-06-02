<?php

namespace AppBundle\Controller;

use AppBundle\Repository\LDraw\ModelRepository;
use AppBundle\Repository\Rebrickable\SetRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var ModelRepository $modelRepository */
        $modelRepository = $this->get('repository.ldraw.model');

        /** @var SetRepository $setRepository */
        $setRepository = $this->get('repository.rebrickable.set');

        return $this->render('default/index.html.twig', [
            'models' => $modelRepository->count(),
            'sets' => $setRepository->count(),
        ]);
    }
}
