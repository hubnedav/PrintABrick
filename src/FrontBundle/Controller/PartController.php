<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Service\SetService;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Part controller.
 *
 * @Route("parts")
 */
class PartController extends Controller
{
    /**
     * Finds and displays a part entity.
     *
     * @Route("/{id}", name="part_detail")
     */
    public function detailAction(Part $part, SetService $setService)
    {
        return $this->render('part/detail.html.twig', [
            'part' => $part,
            'setCount' => count($setService->getAllByPart($part)),
        ]);
    }

    /**
     * @Route("/{id}/sets", name="part_sets")
     */
    public function setsAction(Request $request, Part $part, SetService $setService)
    {
        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $sets = $paginator->paginate(
            $setService->getAllByPart($part),
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 16)/*limit per page*/
        );

        return $this->render('model/tabs/sets.html.twig', [
            'sets' => $sets,
        ]);
    }
}
