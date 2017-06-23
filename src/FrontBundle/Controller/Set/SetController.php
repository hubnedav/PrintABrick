<?php

namespace FrontBundle\Controller\Set;

use AppBundle\Api\Exception\ApiException;
use AppBundle\Api\Manager\BricksetManager;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Model\SetSearch;
use AppBundle\Service\SearchService;
use AppBundle\Service\SetService;
use AppBundle\Service\ZipService;
use Elastica\Index;
use FrontBundle\Form\Search\SetSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("/sets")
 */
class SetController extends Controller
{
    /**
     * @Route("/", name="set_index")
     *
     * @param Request $request
     *
     * @param FormFactoryInterface $formFactory
     * @param SearchService $searchService
     * @return Response
     */
    public function indexAction(Request $request, FormFactoryInterface $formFactory, SearchService $searchService)
    {
        $setSearch = new SetSearch();

        $form = $formFactory->createNamedBuilder('', SetSearchType::class, $setSearch)->getForm();
        $form->handleRequest($request);

        $paginator = $this->get('knp_paginator');
        $sets = $paginator->paginate(
            $searchService->searchSets($setSearch, 500),
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
     * @param Set $set
     *
     * @param SetService $setService
     * @param BricksetManager $bricksetManager
     * @return Response
     */
    public function detailAction(Set $set, SetService $setService, BricksetManager $bricksetManager)
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
     * @param Request $request
     * @param Set $set
     * @param SetService $setService
     * @return Response
     */
    public function inventoryAction(Request $request, Set $set, SetService $setService)
    {
        return $this->render('set/tabs/inventory.html.twig', [
            'inventorySets' => $setService->getAllSubSets($set),
            'set' => $set,
            'missing' => $setService->getParts($set, false, false),
            'models' => $setService->getModels($set, false),
            'missingCount' => $setService->getPartCount($set, false, false),
            'partCount' => $setService->getPartCount($set, false),
        ]);
    }

    /**
     * @Route("/{id}/models", name="set_models")
     * @param Set $set
     * @param SetService $setService
     * @return Response
     */
    public function modelsAction(Set $set, SetService $setService)
    {
        return $this->render('set/tabs/models.html.twig', [
            'set' => $set,
            'missing' => $setService->getParts($set, false, false),
            'models' => $setService->getModels($set, false),
        ]);
    }

    /**
     * @Route("/{id}/colors", name="set_colors")
     * @param Set $set
     * @param SetService $setService
     * @return Response
     */
    public function colorsAction(Set $set, SetService $setService)
    {
        return $this->render('set/tabs/colors.html.twig', [
            'set' => $set,
            'colors' => $setService->getModelsGroupedByColor($set, false),
            'missing' => $setService->getParts($set, false, false),
        ]);
    }

    /**
     * @Route("/{id}/zip", name="set_zip")
     * @param Request $request
     * @param Set $set
     * @param ZipService $zipService
     * @return BinaryFileResponse
     */
    public function zipAction(Request $request, Set $set, ZipService $zipService)
    {
        $sorted = $request->query->get('sorted') == 1 ? true : false;
        $sort = $sorted ? 'Multi-Color' : 'Uni-Color';
        // escape forbidden characters from filename
        $filename = preg_replace('/[^a-z0-9()\-\.]/i', '_', "{$set->getId()}_{$set->getName()}({$sort})");

        $zip = $zipService->createFromSet($set, $filename, $sorted);

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
