<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LDraw\Part;
use Doctrine\DBAL\Query\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/parts")
 */
class PartController extends Controller
{
    /**
     * @Route("/detail/{id}", name="part_detail")
     */
    public function showAction(Request $request, Part $part)
    {
        return $this->render('part/detail.html.twig', [
            'part' => $part,
        ]);
    }

    /**
     * @Route("/", name="parts_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository(Part::class)->createQueryBuilder('p');

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder->where('p.model is not null');

        $query = $queryBuilder->getQuery();

        $paginator  = $this->get('knp_paginator');

        $parts = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 30)/*limit per page*/
        );

        return $this->render('part/index.html.twig', [
            'parts' => $parts,
            'part' => null
        ]);
    }
}
