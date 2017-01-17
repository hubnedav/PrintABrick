<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("rebrickable/parts")
 */
class PartsController extends Controller
{
    /**
     * @Route("/{id}", name="part_detail")
     */
    public function detailAction($id)
    {
        $part = $this->get('manager.rebrickable')->getPartById($id);

        return $this->render('parts/detail.html.twig', [
            'part' => $part,
        ]);
    }
}
