<?php

namespace App\Controller;

use App\Repository\ColorRepository;
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(ColorRepository $colorRepository)
    {
        return $this->render('color/index.html.twig', [
            'colors' => $colorRepository->findAll(),
        ]);
    }
}
