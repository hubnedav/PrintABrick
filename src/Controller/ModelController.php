<?php

namespace App\Controller;

use App\Entity\LDraw\Model;
use App\Form\Search\ModelSearchType;
use App\Model\ModelSearch;
use App\Service\ModelService;
use App\Service\SearchService;
use App\Service\SetService;
use App\Service\ZipService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Part controller.
 *
 * @Route("bricks")
 */
class ModelController extends AbstractController
{
    /**
     * Lists all part entities.
     *
     * @Route("/", name="model_index")
     */
    public function indexAction(Request $request, FormFactoryInterface $formFactory, SearchService $searchService, PaginatorInterface $paginator)
    {
        $modelSearch = new ModelSearch();

        $form = $formFactory->createNamedBuilder('', ModelSearchType::class, $modelSearch)->getForm();
        $form->handleRequest($request);

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
     * @Route("/{id}", name="model_detail", methods={"GET"})
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
     * @Route("/{id}/sets", name="model_sets", methods={"GET"})
     */
    public function setsAction(Request $request, Model $model, SetService $setService, PaginatorInterface $paginator)
    {
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
     * @Route("/{id}/zip", name="model_zip", methods={"GET"})
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
