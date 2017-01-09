<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="homepage")
	 */
	public function indexAction(Request $request)
	{
        $set = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:BuildingKit')->findOneBy(['number' => '4488-1']);

	    $part = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Part')->findOneBy(['number' => '3006']);

		return $this->render('default/index.html.twig', [
		    'set' => $set,
		]);
	}

}
