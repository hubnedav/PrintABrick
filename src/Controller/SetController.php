<?php

namespace App\Controller;

use App\Api\Exception\ApiException;
use App\Api\Manager\BricksetManager;
use App\Entity\Rebrickable\Set;
use App\Form\Search\SetSearchType;
use App\Model\SetSearch;
use App\Service\SearchService;
use App\Service\SetService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sets")
 */
class SetController extends AbstractController
{
    /**
     * @Route("/", name="set_index")
     *
     * @param Request              $request
     * @param FormFactoryInterface $formFactory
     * @param SearchService        $searchService
     * @param PaginatorInterface   $paginator
     *
     * @return Response
     */
    public function index(Request $request, FormFactoryInterface $formFactory, SearchService $searchService, PaginatorInterface $paginator): Response
    {
        $setSearch = new SetSearch();

        $form = $formFactory->createNamedBuilder('', SetSearchType::class, $setSearch)->getForm();
        $form->handleRequest($request);

        $sets = $paginator->paginate(
            $searchService->searchSets($setSearch),
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 20)/*limit per page*/
        );

        return $this->render('set/index.html.twig', [
            'sets' => $sets,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="set_detail")
     *
     * @param Set             $set
     * @param SetService      $setService
     * @param BricksetManager $bricksetManager
     *
     * @return Response
     */
    public function detail(Set $set, SetService $setService, BricksetManager $bricksetManager): Response
    {
        $bricksetSet = null;

        try {
            if (!($bricksetSet = $bricksetManager->getSetByNumber($set->getId()))) {
                $this->addFlash('warning', "{$set->getId()} not found in Brickset database");
            }
        } catch (ApiException $e) {
            $this->addFlash('error', $e->getService());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('set/detail.html.twig', [
            'set' => $set,
            'brset' => $bricksetSet,
            'partCount' => $setService->getPartCount($set),
        ]);
    }

    /**
     * @Route("/{id}/inventory", name="set_inventory")
     *
     * @param Request    $request
     * @param Set        $set
     * @param SetService $setService
     *
     * @return Response
     */
    public function inventory(Request $request, Set $set, SetService $setService): Response
    {
        $template = $this->render('set/tabs/inventory.html.twig', [
            'inventorySets' => $setService->getAllSubSets($set),
            'set' => $set,
            'missing' => $setService->getParts($set, false, false),
            'models' => $setService->getModels($set, false),
            'missingCount' => $setService->getPartCount($set, false, false),
            'partCount' => $setService->getPartCount($set, false),
        ]);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($template->getContent());
        }

        return $template;
    }

    /**
     * @Route("/{id}/models", name="set_models")
     *
     * @param Request    $request
     * @param Set        $set
     * @param SetService $setService
     *
     * @return Response
     */
    public function models(Request $request, Set $set, SetService $setService): Response
    {
        $template = $this->render('set/tabs/models.html.twig', [
            'set' => $set,
            'missing' => $setService->getParts($set, false, false),
            'models' => $setService->getModels($set, false),
        ]);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($template->getContent());
        }

        return $template;
    }

    /**
     * @Route("/{id}/colors", name="set_colors")

     *
     * @param Request    $request
     * @param Set        $set
     * @param SetService $setService
     *
     * @return Response
     */
    public function colors(Request $request, Set $set, SetService $setService): Response
    {
        $template = $this->render('set/tabs/colors.html.twig', [
            'set' => $set,
            'colors' => $setService->getModelsGroupedByColor($set, false),
            'missing' => $setService->getParts($set, false, false),
        ]);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($template->getContent());
        }

        return $template;
    }
}
