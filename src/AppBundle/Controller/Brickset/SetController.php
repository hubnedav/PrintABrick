<?php

namespace AppBundle\Controller\Rebrickable;

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
 * @Route("/brickset")
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

            $sets = $this->get('api.client.brickset')->getSets([
                'theme' => $data['theme'] ? $data['theme']->getTheme() : '',
                'subtheme' => $data['subtheme'] ? $data['subtheme']->getSubtheme() : '',
                'year' => $data['years'] ? $data['years']->getYear() : '',
            ]);
        }

        return $this->render('brickset/browse.html.twig', [
            'form' => $form->createView(),
            'sets' => $sets,
        ]);
    }
}
