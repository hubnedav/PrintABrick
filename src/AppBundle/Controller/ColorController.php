<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Part controller.
 *
 * @Route("colors")
 */
class ColorController extends Controller
{
    /**
     * @Route("/", name="color_index")
     */
    public function indexAction()
    {
        $colors = $this->get('repository.color')->findAll();

        return $this->render('color/index.html.twig', [
            'colors' => $colors,
        ]);
    }
}
