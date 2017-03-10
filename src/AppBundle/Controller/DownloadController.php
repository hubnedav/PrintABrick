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
 * @Route("/download")
 */
class DownloadController extends Controller
{
    /**
     * @Route("/model/{id}", name="download_model")
     *
     * @return Response
     */
    public function modelAction(Model $model)
    {
        $ldraw_filesystem = $this->get('oneup_flysystem.ldraw_filesystem');

        if ($ldraw_filesystem->has($model->getFile())) {
            $response = new BinaryFileResponse($ldraw_filesystem->getAdapter()->getPathPrefix().$model->getFile());
            $response->headers->set('Content-Type', 'application/vnd.ms-pki.stl');

            // Create the disposition of the file
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $model->getFile()
            );

            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }
        throw new FileNotFoundException($model->getFile());
    }
}
