<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
        $rebrickable_part = $this->get('manager.rebrickable')->getPart($id);
        $part = $this->get('app.collection_service')->getPart($id);

        return $this->render('parts/detail.html.twig', [
            'part' => $part,
            'reb_part' => $rebrickable_part,
        ]);
    }
}
