<?php

namespace FrontBundle\Controller;

use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/files")
 */
class MediaController extends Controller
{
    /**
     * @Route("/{path}", name="media_file", requirements={"path"=".+"})
     *
     * @return Response
     */
    public function fileAction($path, FilesystemInterface $mediaFilesystem)
    {
        if ($mediaFilesystem->has($path)) {
            $response = new BinaryFileResponse($mediaFilesystem->getAdapter()->getPathPrefix().DIRECTORY_SEPARATOR.$path);
            $response->headers->set('Content-Type', $mediaFilesystem->getMimetype($path));

            // Create the disposition of the file
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                basename($path)
            );

            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }
        throw new NotFoundHttpException($path);
    }
}
