<?php

namespace AppBundle\Controller\Rebrickable;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Color;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Inventory_Set;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/rebrickable/sets")
 */
class SetController extends Controller
{
    /**
     * @Route("/{number}/parts", name="rebrickable_set_parts")
     */
    public function partsAction(Set $set)
    {
        $em = $this->getDoctrine()->getManager();

        $em->getRepository(Color::class)->findAll();
        $em->getRepository(Part::class)->findAllBySetNumber($set->getNumber());

        $regularParts = $em->getRepository(Inventory_Part::class)->findAllRegularBySetNumber($set->getNumber());
        $spareParts = $em->getRepository(Inventory_Part::class)->findAllSpareBySetNumber($set->getNumber());
        $models = $em->getRepository(Model::class)->findAllBySetNumber($set->getNumber());

        $template = $this->render('rebrickable/set/inventory.html.twig', [
            'regularParts' => $regularParts,
            'spareParts' => $spareParts,
            'models' => $models,
        ]);

//        $json = json_encode($template->getContent());
//        $response = new Response($json, 200);
//        $response->headers->set('Content-Type', 'application/json');
//
//        return $response;

        return $template;
    }

    /**
     * @Route("/{number}/models", name="rebrickable_set_models")
     */
    public function modelsAction(Set $set)
    {
        $models = null;

        try {
            $this->get('repository.ldraw.model')->findAllBySetNumber($set->getNumber());
            $models = $this->get('service.set')->getModels($set);
            $spareModels = $this->get('service.set')->getSpareModels($set);

//            $models = $this->get('repository.ldraw.model')->findAllRegularBySetNumber($set->getNumber());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('rebrickable/set/models.html.twig', [
            'set' => $set,
            'models' => $models,
            'spareModels' => $spareModels
        ]);
    }

    /**
     * @Route("/{number}/sets", name="rebrickable_set_sets")
     */
    public function setsAction(Set $set)
    {
        $em = $this->getDoctrine()->getManager();

        $inventorySets = $em->getRepository(Inventory_Set::class)->findAllBySetNumber($set->getNumber());

        $template = $this->render('rebrickable/set/sets.html.twig', [
            'inventorySets' => $inventorySets,
        ]);

        $json = json_encode($template->getContent());
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
