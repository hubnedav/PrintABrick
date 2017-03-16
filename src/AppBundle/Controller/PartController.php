<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LDraw\Part;
use AppBundle\Entity\LDraw\Type;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/parts")
 */
class PartController extends Controller
{
    /**
     * @Route("/detail/{id}", name="part_detail")
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');

        $part = $em->getRepository(Part::class)->find($id);
        $rbPart = $em->getRepository(\AppBundle\Entity\Rebrickable\Part::class)->find($id);

        $apiPart = null;
        try {
            $apiPart = $this->get('manager.rebrickable')->getPart($id);
        } catch (\Exception $e) {
            dump($e);
        }

        $qb = $em->getRepository('AppBundle:Rebrickable\Inventory')->createQueryBuilder('i');

        $qb->innerJoin(Inventory_Part::class, 'ip', Join::WITH, 'i.id = ip.inventory')
            ->where('ip.part = :part')
            ->setParameter('part', $id)->distinct(true);

        $inventries = $qb->getQuery()->getResult();

        return $this->render('part/detail.html.twig', [
            'part' => $part,
            'rbPart' => $rbPart,
            'apiPart' => $apiPart,
            'inventories' => $inventries,
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
//        $queryBuilder->where('p.model is not null');
        $queryBuilder->join(Type::class,'type', JOIN::WITH, 'p.type = type.id')->where( $queryBuilder->expr()->notIn('type.name', ['Alias', 'Obsolete/Subpart']));

        $query = $queryBuilder->getQuery();

        $paginator = $this->get('knp_paginator');

        $parts = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 100)/*limit per page*/
        );

        return $this->render('part/index.html.twig', [
            'parts' => $parts,
        ]);
    }
}
