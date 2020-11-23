<?php

namespace App\Controller;

use App\Entity\LDraw\Model;
use App\Entity\Rebrickable\Part;
use App\Form\Search\ModelSearchType;
use App\Model\ModelSearch;
use App\Service\ModelService;
use App\Service\SearchService;
use App\Service\SetService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Part controller.
 *
 * @Route("bricks", name="brick_")
 */
class BrickController extends AbstractController
{
    /**
     * Lists all part entities.
     *
     * @Route("/", name="index")
     */
    public function index(Request $request, FormFactoryInterface $formFactory, SearchService $searchService, PaginatorInterface $paginator)
    {
        $modelSearch = new ModelSearch();

        $form = $formFactory->createNamedBuilder('', ModelSearchType::class, $modelSearch)->getForm();
        $form->handleRequest($request);

        $models = $paginator->paginate(
            $searchService->searchModels($modelSearch),
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
     * @Route("/{id}", name="detail", methods={"GET"})
     */
    public function detail($id, SetService $setService, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();

        if ($model = $em->getRepository(Model::class)->findOneByPartOrModelNumber($id)) {
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

        throw new NotFoundHttpException();
    }

    /**
     * @Route("/{id}/sets", name="sets", methods={"GET"})
     */
    public function sets(Request $request, $id, SetService $setService, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();

        if ($model = $em->getRepository(Model::class)->find($id)) {
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
     * @Route("/{id}/related", name="related")
     */
    public function related(Request $request, Model $model, ModelService $modelService)
    {
        $template = $this->render('brick/tabs/related.html.twig', [
            'entity' => $model,
            'siblings' => $modelService->getSiblings($model),
        ]);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($template->getContent());
        }

        return $template;
    }
}
