<?php

namespace AppBundle\Controller;

use AppBundle\Api\Exception\ApiException;
use AppBundle\Entity\Rebrickable\Inventory_Set;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Form\Filter\Set\SetFilterType;
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
        $form = $this->get('form.factory')->create(SetFilterType::class);

        $filterBuilder = $this->get('repository.rebrickable.set')
            ->createQueryBuilder('s')
            ->orderBy('s.year', 'DESC');

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
    public function detailAction(Request $request, Set $set)
    {
        $bricksetSet = null;
        $partCount = $this->get('repository.rebrickable.inventoryPart')->getPartCount($set->getNumber(), false);

        try {
            if (!($bricksetSet = $this->get('api.manager.brickset')->getSetByNumber($set->getNumber()))) {
                $this->addFlash('warning', "{$set->getNumber()} not found in Brickset database");
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
     * @Route("/{number}/parts", name="set_parts")
     */
    public function partsAction(Request $request, Set $set)
    {
        $inventoryPartRepository = $this->get('repository.rebrickable.inventorypart');

        $regularParts = $inventoryPartRepository->findAllBySetNumber($set->getNumber(), false, true);
        $spareParts = $inventoryPartRepository->findAllBySetNumber($set->getNumber(), true);

        $missing = $inventoryPartRepository->findAllBySetNumber($set->getNumber(), false, false);

        $template = $this->render('set/tabs/inventory.html.twig', [
            'regularParts' => $regularParts,
            'missing' => $missing,
            'spareParts' => $spareParts,
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
     * @Route("/{number}/models", name="set_models")
     */
    public function modelsAction(Request $request, Set $set)
    {
        $models = null;
        $spareModels = null;
        $missing = null;
        $missingSpare = null;

        try {
            $models = $this->get('service.set')->getModels($set, false);
            $spareModels = $this->get('service.set')->getModels($set, true);
            $missing = $this->get('service.set')->getParts($set, false, false);
//            $missing = $this->get('repository.rebrickable.inventorypart')->findAllBySetNumber($set->getNumber(), false, false);
            $missingSpare = $this->get('repository.rebrickable.inventorypart')->findAllBySetNumber($set->getNumber(), true, false);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $template = $this->render('set/tabs/models.html.twig', [
            'set' => $set,
            'missing' => $missing,
            'models' => $models,
            'spareModels' => $spareModels,
            'missingSpare' => $missingSpare,
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
     * @Route("/{number}/colors", name="set_colors")
     */
    public function colorsAction(Request $request, Set $set)
    {
        $colors = null;

        try {
            $colors = $this->get('service.set')->getModelsGroupedByColor($set, false);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $template = $this->render('set/tabs/colors.html.twig', [
            'set' => $set,
            'colors' => $colors,
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
     * @Route("/{number}/sets", name="set_sets")
     */
    public function setsAction(Request $request, Set $set)
    {
        $em = $this->getDoctrine()->getManager();

        $inventorySets = $em->getRepository(Inventory_Set::class)->findAllBySetNumber($set->getNumber());

        $template = $this->render('set/tabs/sets.html.twig', [
            'inventorySets' => $inventorySets,
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
     * @Route("/{number}/zip", name="set_zip")
     */
    public function zipAction(Request $request, Set $set)
    {
        $sorted = $request->query->get('sorted') == 1 ? true : false;

        $sort = $sorted ? 'sorted' : 'unsorted';

        $zip = $this->get('service.zip')->createFromSet($set, $sorted);

        $response = new BinaryFileResponse($zip);
        $response->headers->set('Content-Type', 'application/zip');

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "set_{$set->getNumber()}_{$set->getName()}({$sort}).zip"
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
