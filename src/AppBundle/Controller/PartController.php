<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/rebrickable/part")
 */
class PartController extends Controller
{
    /**
     * @Route("/{id}", name="part_detail")
     */
    public function detailAction($id)
    {
        $part = $this->get('manager.rebrickable')->getPart($id);

        $em = $this->getDoctrine()->getManager();
        $localPart = $em->getRepository('AppBundle:Part')->findOneBy(['number' => $id]);

        return $this->render('part/detail.html.twig', [
            'part' => $part,
            'localPart' => $localPart,
        ]);
    }
}
