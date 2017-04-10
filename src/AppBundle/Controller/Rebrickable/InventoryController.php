<?php

namespace AppBundle\Controller\Rebrickable;

use AppBundle\Api\Exception\EmptyResponseException;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Color;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Inventory_Set;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Form\FilterSetType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/rebrickable/inventory")
 */
class InventoryController extends Controller
{
    /**
     * @Route("/{number}/inventory_sets", name="rebrickable_inventory_sets")
     */
    public function inventoryAction(Inventory $inventory) {
        $em = $this->getDoctrine()->getManager();

        $inventorySets = $em->getRepository(Set::class)->findAllByInventory($inventory);

        return $this->render('rebrickable/set/inventory_sets.html.twig', [
            'inventorySets' => $inventorySets,
        ]);
    }
}
