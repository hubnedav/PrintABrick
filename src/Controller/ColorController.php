<?php

namespace App\Controller;

use App\Service\ColorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Part controller.
 *
 * @Route("colors")
 */
class ColorController extends AbstractController
{
    /**
     * @Route("/", name="color_index")
     *
     * @param ColorService $colorService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(ColorService $colorService)
    {
        return $this->render('color/index.html.twig', [
            'colors' => $colorService->getAll(),
        ]);
    }
}
