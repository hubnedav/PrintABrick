<?php

namespace App\Controller;

use App\Entity\LDraw\Model;
use App\Entity\Rebrickable\Set;
use App\Service\ZipService;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/files")
 */
class MediaController extends AbstractController
{
    /**
     * @Route("/media/{path}", name="media_file", requirements={"path"=".+"})
     */
    public function media(Request $request, $path, FilesystemInterface $mediaFilesystem): BinaryFileResponse
    {
        if ($this->isCsrfTokenValid('download-media', $request->get('token'))) {
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

        throw new AccessDeniedException();
    }

    /**
     * @Route("/brick/{id}", name="brick_zip", methods={"POST"})
     */
    public function brick(Request $request, Model $model, ZipService $zipService): BinaryFileResponse
    {
        if ($this->isCsrfTokenValid('download-brick', $request->request->get('token'))) {
            // escape forbidden characters from filename
            $filename = preg_replace('/[^a-z0-9()\-\.]/i', '_', "{$model->getId()}_{$model->getName()}");

            $zip = $zipService->createFromModel($model, $filename, true);

            $response = new BinaryFileResponse($zip);
            $response->headers->set('Content-Type', 'application/zip');

            // Create the disposition of the file
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename.'.zip'
            );

            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        throw new AccessDeniedException();
    }

    /**
     * @Route("/set/{id}", name="set_zip")
     */
    public function set(Request $request, Set $set, ZipService $zipService): BinaryFileResponse
    {
        if ($this->isCsrfTokenValid('download-set', $request->request->get('token'))) {
            $sorted = 1 === $request->get('sorted');
            $sort = $sorted ? 'Multi-Color' : 'Uni-Color';
            // escape forbidden characters from filename
            $filename = preg_replace('/[^a-z0-9()\-\.]/i', '_', "{$set->getId()}_{$set->getName()}({$sort})");

            $zip = $zipService->createFromSet($set, $filename, $sorted);

            $response = new BinaryFileResponse($zip);
            $response->headers->set('Content-Type', 'application/zip');

            // Create the disposition of the file
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename.'.zip'
            );

            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        throw new AccessDeniedException();
    }
}
