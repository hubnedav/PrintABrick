<?php

namespace App\Controller;

use App\Entity\LDraw\Model;
use App\Entity\Rebrickable\Part;
use App\Form\Search\ModelSearchType;
use App\Model\ModelSearch;
use App\Service\ModelService;
use App\Service\SearchService;
use App\Service\SetService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Part controller.
 *
 * @Route("bricks")
 */
class BrickController extends AbstractController
{
    /**
     * Lists all part entities.
     *
     * @Route("/", name="brick_index")
     */
    public function index(Request $request, FormFactoryInterface $formFactory, SearchService $searchService, PaginatorInterface $paginator)
    {
        $modelSearch = new ModelSearch();

        $form = $formFactory->createNamedBuilder('', ModelSearchType::class, $modelSearch)->getForm();
        $form->handleRequest($request);

        $models = $paginator->paginate(
                $searchService->searchModels($modelSearch, 500),
                $request->query->getInt('page', 1)/*page number*/,
                $request->query->getInt('limit', 30)/*limit per page*/
            );

        return $this->render('brick/index.html.twig', [
                'models' => $models,
                'form' => $form->createView(),
            ]);
    }

    /**
     * Finds and displays a model entity.
     *
     * @Route("/{id}", name="brick_detail", methods={"GET"})
     */
    public function detail($id, ModelService $modelService, SetService $setService, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        if ($model = $modelService->find($id)) {
            if ($model->getId() !== $id) {
                $this->addFlash('info', $translator->trans('brick.redirect', ['%src%' => $id, '%dest%' => $model->getId()]));

                return $this->redirectToRoute('brick_detail', ['id' => $model->getId()], 302);
            }

            return $this->render('brick/detail.html.twig', [
                    'entity' => $model,
                    'setCount' => count($setService->getAllByModel($model)),
                ]);
        }

        if ($part = $em->getRepository(Part::class)->find($id)) {
            return $this->render('part/detail.html.twig', [
                    'entity' => $part,
                    'setCount' => count($setService->getAllByPart($part)),
                ]);
        }

        return $this->render('error/error.html.twig');
    }

    /**
     * @Route("/{id}/sets", name="brick_sets", methods={"GET"})
     */
    public function sets(Request $request, $id, ModelService $modelService, SetService $setService, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        if ($model = $modelService->find($id)) {
            $sets = $setService->getAllByModel($model);
        } elseif ($part = $em->getRepository(Part::class)->find($id)) {
            $sets = $setService->getAllByPart($part);
        }
        $sets = $paginator->paginate(
                $sets,
                $request->query->getInt('page', 1)/*page number*/,
                $request->query->getInt('limit', 16)/*limit per page*/
            );

        return $this->render('brick/tabs/sets.html.twig', [
                'sets' => $sets,
            ]);
    }

    /**
     * @Route("/{id}/related", name="brick_related")
     */
    public function related(Request $request, Model $model, ModelService $modelService)
    {
        $template = $this->render('brick/tabs/related.html.twig', [
            'entity' => $model,
            'siblings' => $modelService->getSiblings($model),
            'submodels' => $modelService->getSubmodels($model),
        ]);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($template->getContent());
        }

        return $template;
    }
}
