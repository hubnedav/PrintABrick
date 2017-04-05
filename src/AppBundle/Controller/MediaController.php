<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LDraw\Model;
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
     * @Route("/model/stl/{number}", name="model_stl")
     *
     * @return Response
     */
    public function stlAction(Model $model)
    {
        $mediaFilesystem = $this->get('oneup_flysystem.media_filesystem');

        if ($mediaFilesystem->has($model->getPath())) {
            $response = new BinaryFileResponse($mediaFilesystem->getAdapter()->getPathPrefix().DIRECTORY_SEPARATOR.$model->getPath());
            $response->headers->set('Content-Type', 'application/vnd.ms-pki.stl');

            // Create the disposition of the file
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $model->getNumber().'.stl'
            );

            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }
        throw new FileNotFoundException($model->getPath());
    }

    /**
     * @Route("/model/image/{number}", name="model_image")
     *
     * @return Response
     */
    public function imageAction(Model $model)
    {
        $mediaFilesystem = $this->get('oneup_flysystem.media_filesystem');

        if ($mediaFilesystem->has('ldraw'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$model->getNumber().'.png')) {
            $response = new BinaryFileResponse($mediaFilesystem->getAdapter()->getPathPrefix().DIRECTORY_SEPARATOR.'ldraw'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$model->getNumber().'.png');
            $response->headers->set('Content-Type', 'image/png');

            // Create the disposition of the file
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $model->getNumber().'png'
            );

            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }
        throw new FileNotFoundException($model->getNumber().'png');
    }
}
