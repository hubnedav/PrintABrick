<?php

namespace AppBundle\Controller\Rebrickable;

use AppBundle\Api\Exception\EmptyResponseException;
use AppBundle\Entity\Rebrickable\Category;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Part controller.
 *
 * @Route("rebrickable/parts")
 */
class PartController extends Controller
{
    /**
     * Finds and displays a part entity.
     *
     * @Route("/{number}", name="reb_part_detail")
     * @Method("GET")
     */
    public function detailAction(Part $part)
    {
        $em = $this->getDoctrine()->getManager();

        $apiPart = null;

        if ($part) {
            try {
                $apiPart = $this->get('api.manager.rebrickable')->getPart($part->getNumber());
            } catch (EmptyResponseException $e) {
                $this->addFlash('warning', 'Part not found');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }

            $sets = $part != null ? $em->getRepository(Set::class)->findAllByPartNumber($part->getNumber()) : null;

            return $this->render('rebrickable/part/detail.html.twig', [
                'part' => $part,
                'apiPart' => $apiPart,
                'sets' => $sets,
            ]);
        }

        return $this->render('error/error.html.twig');
    }
}
