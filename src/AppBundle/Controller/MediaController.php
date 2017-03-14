<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Part;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * @Route("/model/{id}", name="model_stl")
     *
     * @return Response
     */
    public function modelAction(Model $model)
    {
        $mediaFilesystem = $this->get('oneup_flysystem.media_filesystem');

        if ($mediaFilesystem->has($model->getFile())) {
            $response = new BinaryFileResponse($mediaFilesystem->getAdapter()->getPathPrefix().DIRECTORY_SEPARATOR.$model->getFile());
            $response->headers->set('Content-Type', 'application/vnd.ms-pki.stl');

            // Create the disposition of the file
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $model->getId().'.stl'
            );

            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }
        throw new FileNotFoundException($model->getFile());
    }

    /**
     * @Route("/part/{id}", name="part_image")
     *
     * @return Response
     */
    public function PartImageAction(Part $part)
    {
        $mediaFilesystem = $this->get('oneup_flysystem.media_filesystem');

        if ($mediaFilesystem->has('ldraw'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$part->getId().'.png')) {
            $response = new BinaryFileResponse($mediaFilesystem->getAdapter()->getPathPrefix().DIRECTORY_SEPARATOR.'ldraw'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$part->getId().'.png');
            $response->headers->set('Content-Type', 'image/png');

            // Create the disposition of the file
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $part->getId().'png'
            );

            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }
        throw new FileNotFoundException($part->getId().'png');
    }
}
