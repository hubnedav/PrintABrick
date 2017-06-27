<?php

namespace FrontBundle\Controller;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Model\ModelSearch;
use AppBundle\Service\ModelService;
use AppBundle\Service\SearchService;
use AppBundle\Service\SetService;
use AppBundle\Service\ZipService;
use FrontBundle\Form\Search\ModelSearchType;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Part controller.
 *
 * @Route("bricks")
 */
class ModelController extends Controller
{
    /**
     * Lists all part entities.
     *
     * @Route("/", name="model_index")
     */
    public function indexAction(Request $request, FormFactoryInterface $formFactory, SearchService $searchService)
    {
        $modelSearch = new ModelSearch();

        $form = $formFactory->createNamedBuilder('', ModelSearchType::class, $modelSearch)->getForm();
        $form->handleRequest($request);

        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $models = $paginator->paginate(
            $searchService->searchModels($modelSearch, 500),
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 30)/*limit per page*/
        );

        return $this->render('model/index.html.twig', [
            'models' => $models,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a model entity.
     *
     * @Route("/{id}", name="model_detail")
     * @Method("GET")
     */
    public function detailAction($id, ModelService $modelService, SetService $setService)
    {
        if ($model = $modelService->find($id)) {
            try {
                return $this->render('model/detail.html.twig', [
                    'model' => $model,
                    'siblings' => $modelService->getSiblings($model),
                    'submodels' => $modelService->getSubmodels($model),
                    'setCount' => count($setService->getAllByModel($model)),
                ]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('error/error.html.twig');
    }

    /**
     * @Route("/{id}/sets", name="model_sets")
     * @Method("GET")
     */
    public function setsAction(Request $request, Model $model, SetService $setService)
    {
        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $sets = $paginator->paginate(
            $setService->getAllByModel($model),
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 16)/*limit per page*/
        );

        return $this->render('model/tabs/sets.html.twig', [
            'sets' => $sets,
        ]);
    }

    /**
     * @Route("/{id}/zip", name="model_zip")
     * @Method("GET")
     */
    public function zipAction(Model $model, ZipService $zipService)
    {
        // escape forbidden characters from filename
        $filename = preg_replace('/[^a-z0-9()\-\.]/i', '_', "{$model->getId()}_{$model->getName()}");

        $zip = $zipService->createFromModel($model, $filename, true);

        $response = new BinaryFileResponse($zip);
        $response->headers->set('Content-Type', 'application/zip');

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename.'.zip'
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
