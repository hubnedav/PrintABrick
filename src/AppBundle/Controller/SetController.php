<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Rebrickable\Color;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Form\FilterSetType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/sets")
 */
class SetController extends Controller
{
    /**
     * @Route("/", name="set_browse")
     */
    public function browseAction(Request $request)
    {
        $form = $this->createForm(FilterSetType::class);

        $form->handleRequest($request);

        $sets = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $sets = $this->get('client.brickset')->getSets([
                'theme' => $data['theme'] ? $data['theme']->getTheme() : '',
                'subtheme' => $data['subtheme'] ? $data['subtheme']->getSubtheme() : '',
                'year' => $data['years'] ? $data['years']->getYear() : '',
            ]);
        }

        return $this->render('set/browse.html.twig', [
            'form' => $form->createView(),
            'sets' => $sets,
        ]);
    }

    /**
     * @Route("/detail/{number}_{name}", name="set_detail")
     */
    public function detailAction(Request $request, $number, $name = null)
    {
        $brset = $this->get('manager.brickset')->getSetByNumber($number);

        $set = $this->get('doctrine.orm.default_entity_manager')->getRepository(Set::class)->find($number);

        $em = $this->getDoctrine()->getManager();

        $em->getRepository(Color::class)->findAll();

        $em->getRepository(Theme::class)->findAll();

        return $this->render('set/detail.html.twig', [
            'set' => $set,
            'brset' => $brset,
            'parts' => $em->getRepository(Part::class)->findAllBySetNumber($number),
            'inventoryParts' => $em->getRepository(Inventory_Part::class)->findAllBySetNumber($number),
        ]);
    }
}
