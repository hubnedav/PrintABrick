<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Form\Search\ModelSearchType;
use AppBundle\Model\ModelSearch;
use AppBundle\Repository\Rebrickable\SetRepository;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function indexAction(Request $request)
    {
        $modelSearch = new ModelSearch();

        $form = $this->get('form.factory')->createNamedBuilder('', ModelSearchType::class, $modelSearch)->getForm();
        $form->handleRequest($request);

        $elasticaManager = $this->get('fos_elastica.manager');
        $results = $elasticaManager->getRepository(Model::class)->search($modelSearch, 500);

        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $models = $paginator->paginate(
            $results,
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
    public function detailAction($id)
    {
        /** @var Model $model */
        if ($model = $this->get('repository.ldraw.model')->findOneByNumber($id)) {
            try {
                $subparts = $this->get('service.model')->getAllSubparts($model);
                $sets = $model != null ? $this->get('repository.rebrickable.set')->findAllByModel($model) : null;

                $related = $this->get('repository.ldraw.model')->findAllRelatedModels($model->getId());

                return $this->render('model/detail.html.twig', [
                    'model' => $model,
                    'sets' => $sets,
                    'related' => $related,
                    'subparts' => $subparts,
                ]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('error/error.html.twig');
    }

    /**
     * @Route("/{id}/zip", name="model_zip")
     * @Method("GET")
     */
    public function zipAction(Request $request, Model $model)
    {
        // escape forbidden characters from filename
        $filename = preg_replace('/[^a-z0-9()\-\.]/i', '_', "{$model->getId()}_{$model->getName()}");

        $zip = $this->get('service.zip')->createFromModel($model, $filename, true);

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
