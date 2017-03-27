<?php

namespace AppBundle\Controller\LDraw;

use AppBundle\Entity\LDraw\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Form\Filter\PartFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Part controller.
 *
 * @Route("ldraw_part")
 */
class PartController extends Controller
{
    /**
     * Lists all part entities.
     *
     * @Route("/", name="ldraw_part_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->get('form.factory')->create(PartFilterType::class);

        $filterBuilder = $this->get('repository.ldraw.part')
            ->createQueryBuilder('part');

//        $filterBuilder->where('part.type = 1');

        if ($request->query->has($form->getName())) {
            // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
        }

        $paginator = $this->get('knp_paginator');
        $parts = $paginator->paginate(
            $filterBuilder->getQuery(),
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 100)/*limit per page*/
        );

        return $this->render('ldraw/part/index.html.twig', [
            'parts' => $parts,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a part entity.
     *
     * @Route("/{number}", name="ldraw_part_show")
     * @Method("GET")
     */
    public function showAction($number)
    {
        $em = $this->getDoctrine()->getManager();

        $rbPart = $em->getRepository(\AppBundle\Entity\Rebrickable\Part::class)->find($number);

        $part = $em->getRepository(Part::class)->find($number);

        $apiPart = null;
        try {
            $apiPart = $this->get('manager.rebrickable')->getPart($number);
        } catch (\Exception $e) {
            dump($e);
        }

        $sets = $em->getRepository(Set::class)->findAllByPartNumber($number);

        return $this->render('ldraw/part/show.html.twig', [
            'part' => $part,
            'rbPart' => $rbPart,
            'apiPart' => $apiPart,
            'sets' => $sets,
        ]);
    }
}
