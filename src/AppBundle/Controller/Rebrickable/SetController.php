<?php

namespace AppBundle\Controller\Rebrickable;

use AppBundle\Api\Exception\EmptyResponseException;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Color;
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
 * @Route("/rebrickable/sets")
 */
class SetController extends Controller
{
    /**
     * @Route("/{number}/parts", name="rebrickable_set_parts")
     */
    public function partsAction(Set $set) {
        $em = $this->getDoctrine()->getManager();

        $em->getRepository(Color::class)->findAll();
        $em->getRepository(Part::class)->findAllBySetNumber($set->getNumber());

        $regularParts = $em->getRepository(Inventory_Part::class)->findAllRegularBySetNumber($set->getNumber());
        $spareParts = $em->getRepository(Inventory_Part::class)->findAllSpareBySetNumber($set->getNumber());

        return $this->render('rebrickable/set/parts.html.twig', [
            'regularParts' => $regularParts,
            'spareParts' => $spareParts,
        ]);
    }


//    /**
//     * @Route("/download/{number}", name="set_download")
//     */
//    public function downloadZipAction(Request $request, $number) {
//        $em = $this->getDoctrine()->getManager();
//
//        $inventoryParts = $em->getRepository(Inventory_Part::class)->findAllBySetNumber($number);
//
//        $zip = new \ZipArchive();
//        $zipName = 'set_'.$number.'.zip';
//        $zip->open($zipName,  \ZipArchive::CREATE);
//        /** @var Inventory_Part $part */
//        foreach ($inventoryParts as $part) {
//            $filename = $part->getPart()->getNumber().'_('.$part->getColor()->getName().'_'.$part->getQuantity().'x).stl';
//
//            try {
//                if($part->getPart()->getModel()) {
//                    $zip->addFromString($filename, $this->get('oneup_flysystem.media_filesystem')->read($part->getPart()->getModel()->getPath()));
//                }
//            } catch (\Exception $e) {
//                dump($e);
//            }
//        }
//        $zip->close();
//
//        $response = new Response(file_get_contents($zipName));
//        $response->headers->set('Content-Type', 'application/zip');
//        $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
//        $response->headers->set('Content-length', filesize($zipName));
//
//        return $response;
//    }

}
