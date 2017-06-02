<?php

namespace AppBundle\Controller;

use AppBundle\Api\Exception\EmptyResponseException;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function detailAction(Part $part)
    {
        $apiPart = null;
        if ($part) {
            if($model = $part->getModel()) {
                $this->redirectToRoute('model_detail',['id' => $model->getId()]);
            }

            try {
                $apiPart = $this->get('api.manager.rebrickable')->getPart($part->getId());
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
}
