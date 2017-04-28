<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Form\Filter\Model\ModelFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Part controller.
 *
 * @Route("models")
 */
class ModelController extends Controller
{
    /**
     * Lists all part entities.
     *
     * @Route("/", name="model_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $form = $this->get('form.factory')->create(ModelFilterType::class);

        $filterBuilder = $this->get('repository.ldraw.model')->getFilteredQueryBuilder();

        if ($request->query->has($form->getName())) {
            // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
        }

        $paginator = $this->get('knp_paginator');
        $models = $paginator->paginate(
            $filterBuilder->getQuery(),
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 40)/*limit per page*/
        );

        return $this->render('model/index.html.twig', [
            'models' => $models,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a model entity.
     *
     * @Route("/{number}", name="model_detail")
     * @Method("GET")
     */
    public function detailAction($number)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Model $model */
        if ($model = $this->get('repository.ldraw.model')->findOneByNumber($number)) {
            try {
                $rbParts = $model != null ? $this->get('repository.rebrickable.part')->findAllByModel($model) : null;
                $sets = $model != null ? $this->get('repository.rebrickable.set')->findAllByModel($model) : null;

                $related = $this->get('repository.ldraw.model')->findAllRelatedModels($model->getNumber());

                return $this->render('model/detail.html.twig', [
                    'model' => $model,
                    'rbParts' => $rbParts,
                    'sets' => $sets,
                    'related' => $related,
                ]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('error/error.html.twig');
    }

    /**
     * @Route("/{number}/zip", name="model_zip")
     * @Method("GET")
     */
    public function zipAction(Model $model)
    {
        $zip = $this->get('service.zip')->createFromModel($model);
    }
}
