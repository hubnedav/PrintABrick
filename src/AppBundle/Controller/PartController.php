<?php

namespace AppBundle\Controller;

use AppBundle\Api\Exception\EmptyResponseException;
use AppBundle\Api\Manager\RebrickableManager;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Service\SetService;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Part controller.
 *
 * @Route("parts")
 */
class PartController extends Controller
{
    /**
     * Finds and displays a part entity.
     *
     * @Route("/{id}", name="part_detail")
     */
    public function detailAction(Part $part, RebrickableManager $rebrickableManager)
    {
        $apiPart = null;
        if ($part) {
            if ($model = $part->getModel()) {
                $this->redirectToRoute('model_detail', ['id' => $model->getId()]);
            }

            try {
                $apiPart = $rebrickableManager->getPart($part->getId());
            } catch (EmptyResponseException $e) {
                $this->addFlash('warning', 'Part not found');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->render('part/detail.html.twig', [
                'part' => $part,
                'apiPart' => $apiPart,
            ]);
        }

        return $this->render('error/error.html.twig');
    }

    /**
     * @Route("/{id}/sets", name="part_sets")
     */
    public function setsAction(Request $request, Part $part, SetService $setService)
    {
        /** @var Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        $sets = $paginator->paginate(
            $setService->getAllByPart($part),
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 16)/*limit per page*/
        );

        $template = $this->render('model/tabs/sets.html.twig', [
            'sets' => $sets,
        ]);

        if ($request->isXmlHttpRequest()) {
            $json = json_encode($template->getContent());
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return $template;
    }
}
