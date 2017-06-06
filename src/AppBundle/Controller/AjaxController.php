<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AjaxController extends Controller
{
    /**
     * @Route("/set_tab/{tab}", name="set_tab", requirements={"tab"=".+"})
     */
    public function setDefaultTabAction(Request $request, $tab)
    {
        $session = $request->getSession();
        $session->set('tab', $tab);

        $response = new Response();
        return $response;

    }
}