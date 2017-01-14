<?php

namespace AppBundle\Controller;

use AppBundle\Form\FilterSetType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/brickset/sets")
 */
class SetsController extends Controller
{
    /**
     * @Route("/", name="sets_browse")
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

        return $this->render('sets/browse.html.twig', [
            'form' => $form->createView(),
            'sets' => $sets,
        ]);
    }

    /**
     * @Route("/{id}_{name}", name="set_detail")
     */
    public function detailAction(Request $request, $id, $name = null)
    {
        $set = $this->get('manager.brickset')->getSetById($id);;

        $parts = $this->get('sevice.collection')->getSet($set->getNumber().'-'.$set->getNumberVariant())->getParts();

        return $this->render('sets/detail.html.twig', [
            'set' => $set,
            'parts' => $parts,
        ]);
    }
}
