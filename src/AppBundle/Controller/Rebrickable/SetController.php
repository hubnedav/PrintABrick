<?php

namespace AppBundle\Controller\Rebrickable;

use AppBundle\Api\Exception\EmptyResponseException;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Color;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Form\FilterSetType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/rebrickable/sets")
 */
class SetController extends Controller
{
    /**
     * @Route("/", name="set_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository(Set::class)->createQueryBuilder('s');

        $paginator = $this->get('knp_paginator');
        $sets = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 30)/*limit per page*/
        );

        return $this->render('rebrickable/set/index.html.twig', [
            'sets' => $sets,
        ]);
    }

    /**
     * @Route("/detail/{number}_{name}", name="set_detail")
     */
    public function detailAction(Request $request, $number, $name = null)
    {
        $brset = null;
        try {
            $brset = $this->get('api.manager.brickset')->getSetByNumber($number);
        } catch (EmptyResponseException $e) {
            $this->addFlash('warning', 'Set not found in Brickset database');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $set = $this->getDoctrine()->getManager()->getRepository(Set::class)->find($number);

        $rbset = $this->get('api.manager.rebrickable')->getSet($number);

        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Color::class)->findAll();
        $em->getRepository(Theme::class)->findAll();

        return $this->render('rebrickable/set/detail.html.twig', [
            'set' => $set,
            'brset' => $brset,
            'rbset' => $rbset,
            'parts' => $em->getRepository(Model::class)->findAllBySetNumber($number),
            'inventoryParts' => $em->getRepository(Inventory_Part::class)->findAllBySetNumber($number),
        ]);
    }


    /**
     * @Route("/download/{number}", name="set_download")
     */
    public function downloadZipAction(Request $request, $number) {
        $em = $this->getDoctrine()->getManager();

        $inventoryParts = $em->getRepository(Inventory_Part::class)->findAllBySetNumber($number);

        $zip = new \ZipArchive();
        $zipName = 'set_'.$number.'.zip';
        $zip->open($zipName,  \ZipArchive::CREATE);
        /** @var Inventory_Part $part */
        foreach ($inventoryParts as $part) {
            $filename = $part->getPart()->getNumber().'_('.$part->getColor()->getName().'_'.$part->getQuantity().'x).stl';

            try {
                if($part->getPart()->getModel()) {
                    $zip->addFromString($filename, $this->get('oneup_flysystem.media_filesystem')->read($part->getPart()->getModel()->getPath()));
                }
            } catch (\Exception $e) {
                dump($e);
            }
        }
        $zip->close();

        $response = new Response(file_get_contents($zipName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($zipName));

        return $response;
    }
}
