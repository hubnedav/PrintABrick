<?php

namespace AppBundle\Controller;

use AppBundle\Api\Exception\ApiException;
use AppBundle\Api\Exception\EmptyResponseException;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Form\Filter\Set\SetFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/sets")
 */
class SetController extends Controller
{
    /**
     * @Route("/", name="set_index")
     */
    public function indexAction(Request $request)
    {
        $form = $this->get('form.factory')->create(SetFilterType::class);

        $filterBuilder = $this->get('repository.rebrickable.set')
            ->createQueryBuilder('s')
            ->orderBy('s.year','DESC');

        if ($request->query->has($form->getName())) {
            // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
        }

        $paginator = $this->get('knp_paginator');
        $sets = $paginator->paginate(
            $filterBuilder->getQuery(),
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 30)/*limit per page*/
        );

        return $this->render('set/index.html.twig', [
            'sets' => $sets,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{number}", name="set_detail")
     */
    public function detailAction(Request $request, $number)
    {
        $rebrickableSet = null;
        $bricksetSet = null;
        $colors = null;

        try {
            if (($rebrickableSet = $this->get('repository.rebrickable.set')->find($number)) == null) {
                $this->addFlash('warning', 'Set not found in Rebrickable database');
            }

            $bricksetSet = $this->get('api.manager.brickset')->getSetByNumber($number);

        } catch (EmptyResponseException $e) {
            $this->addFlash('warning', 'Set not found in Brickset database');
        } catch (ApiException $e) {
            $this->addFlash('error', $e->getService());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        if (!$rebrickableSet && !$bricksetSet) {
            return $this->render('error/error.html.twig');
        }

        return $this->render('set/detail.html.twig', [
            'rbset' => $rebrickableSet,
            'brset' => $bricksetSet,
        ]);
    }
}
