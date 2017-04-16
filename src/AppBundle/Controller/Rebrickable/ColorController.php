<?php

namespace AppBundle\Controller\Rebrickable;

use AppBundle\Entity\Rebrickable\Color;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Part controller.
 *
 * @Route("rebrickable/colors")
 */
class ColorController extends Controller
{
    /**
     * @Route("/", name="color_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $colors = $em->getRepository(Color::class)->findAll();

        return $this->render('rebrickable/color/index.html.twig', [
            'colors' => $colors,
        ]);
    }
}
