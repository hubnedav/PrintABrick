<?php

namespace AppBundle\Controller;

use AppBundle\Api\Exception\ApiException;
use AppBundle\Entity\Rebrickable\Inventory_Set;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Form\Search\SetSearchType;
use AppBundle\Model\SetSearch;
use AppBundle\Repository\Rebrickable\Inventory_PartRepository;
use AppBundle\Repository\Search\SetRepository;
use AppBundle\Service\SetService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     */
    public function indexAction(Request $request)
    {
        $setSearch = new SetSearch();

        $form = $this->get('form.factory')->createNamedBuilder('s', SetSearchType::class, $setSearch)->getForm();
        $form->handleRequest($request);

        /** @var SetRepository $setRepository */
        $setRepository = $this->get('fos_elastica.manager')->getRepository(Set::class);
        $results = $setRepository->search($setSearch, 5000);

        $paginator = $this->get('knp_paginator');
        $sets = $paginator->paginate(
            $results,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 24)/*limit per page*/
        );

        return $this->render('set/index.html.twig', [
            'sets' => $sets,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="set_detail")
     */
    public function detailAction(Request $request, Set $set)
    {
        /** @var Inventory_PartRepository $inventoryPartRepository */
        $inventoryPartRepository = $this->get('repository.rebrickable.inventoryPart');
        /** @var SetService $setService */
        $setService = $this->get('service.set');

        $bricksetSet = null;
        $partCount = $inventoryPartRepository->getPartCount($set, false);

        try {
            if (!($bricksetSet = $this->get('api.manager.brickset')->getSetByNumber($set->getId()))) {
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
            'partCount' => $partCount,
        ]);
    }

    /**
     * @Route("/{id}/inventory", name="set_inventory")
     */
    public function inventoryAction(Request $request, Set $set)
    {
        $em = $this->getDoctrine()->getManager();

        $inventorySets = $em->getRepository(Inventory_Set::class)->findAllBySetNumber($set->getId());
        $setService = $this->get('service.set');
        $inventoryPartRepository = $this->get('repository.rebrickable.inventoryPart');

        $models = $setService->getModels($set, false);
        $missing = $setService->getParts($set, false, false);
        $missingCount = $inventoryPartRepository->getPartCount($set, false, false);
        $partCount = $inventoryPartRepository->getPartCount($set, false);

        $template = $this->render('set/tabs/inventory.html.twig', [
            'inventorySets' => $inventorySets,
            'set' => $set,
            'missing' => $missing,
            'models' => $models,
            'missingCount' => $missingCount,
            'partCount' => $partCount,
        ]);

        if ($request->isXmlHttpRequest()) {
            $json = json_encode($template->getContent());
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return $template;
    }

    /**
     * @Route("/{id}/models", name="set_models")
     */
    public function modelsAction(Request $request, Set $set)
    {
        $setService = $this->get('service.set');

        $models = null;
        $missing = null;

        try {
            $models = $setService->getModels($set, false);
            $missing = $setService->getParts($set, false, false);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $template = $this->render('set/tabs/models.html.twig', [
            'set' => $set,
            'missing' => $missing,
            'models' => $models,
        ]);

        if ($request->isXmlHttpRequest()) {
            $json = json_encode($template->getContent());
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return $template;
    }

    /**
     * @Route("/{id}/colors", name="set_colors")
     */
    public function colorsAction(Request $request, Set $set)
    {
        /** @var SetService $setService */
        $setService = $this->get('service.set');

        $colors = null;
        $missing = null;

        try {
            $colors = $setService->getModelsGroupedByColor($set, false);
            $missing = $setService->getParts($set, false, false);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $template = $this->render('set/tabs/colors.html.twig', [
            'set' => $set,
            'colors' => $colors,
            'missing' => $missing,
        ]);

        if ($request->isXmlHttpRequest()) {
            $json = json_encode($template->getContent());
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return $template;
    }

    /**
     * @Route("/{id}/zip", name="set_zip")
     */
    public function zipAction(Request $request, Set $set)
    {
        $sorted = $request->query->get('sorted') == 1 ? true : false;
        $sort = $sorted ? 'Multi-Color' : 'Uni-Color';
        // escape forbidden characters from filename
        $filename = preg_replace('/[^a-z0-9()\-\.]/i', '_', "{$set->getId()}_{$set->getName()}({$sort})");

        $zip = $this->get('service.zip')->createFromSet($set, $filename, $sorted);

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
