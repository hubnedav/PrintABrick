<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\Color;
use AppBundle\Service\ColorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
