<?php

namespace FrontBundle\Controller\Set;

use AppBundle\Api\Exception\ApiException;
use AppBundle\Api\Manager\BricksetManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("sets/brickset")
 */
class BricksetController extends Controller
{
    /**
     * @Route("/{id}/instructions", name="brickset_instructions")
     * @Method("GET")
     */
    public function instructionsAction(Request $request, $id, BricksetManager $bricksetManager)
    {
        $instructions = [];
        try {
            $instructions = $bricksetManager->getSetInstructions($id);
        } catch (ApiException $e) {
            $this->addFlash('error', $e->getService());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $template = $this->render('brickset/instructions.html.twig', [
            'instructions' => $instructions,
        ]);

        if ($request->isXmlHttpRequest()) {
            $json = json_encode($template->getContent());
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return $template;
    }

    /**
     * @Route("/{id}/reviews", name="brickset_reviews")
     * @Method("GET")
     */
    public function reviewsAction(Request $request, $id, BricksetManager $bricksetManager)
    {
        $reviews = [];
        $number = null;
        try {
            $reviews = $bricksetManager->getSetReviews($id);
            $number = $bricksetManager->getSetById($id)->getLegoSetID();
        } catch (ApiException $e) {
            $this->addFlash('error', $e->getService());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $template = $this->render('brickset/reviews.html.twig', [
            'reviews' => $reviews,
            'id' => $number,
        ]);

        if ($request->isXmlHttpRequest()) {
            $json = json_encode($template->getContent());
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return $template;
    }

    /**
     * @Route("/{id}/images", name="brickset_images")
     * @Method("GET")
     */
    public function imagesAction(Request $request, $id, BricksetManager $bricksetManager)
    {
        $images = [];
        try {
            $images = $bricksetManager->getAdditionalImages($id);
        } catch (ApiException $e) {
            $this->addFlash('error', $e->getService());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $template = $this->render('brickset/images.html.twig', [
            'images' => $images,
        ]);

        if ($request->isXmlHttpRequest()) {
            $json = json_encode($template->getContent());
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return $template;
    }

    /**
     * @Route("/{id}/description", name="brickset_description")
     * @Method("GET")
     */
    public function descriptionAction(Request $request, $id, BricksetManager $bricksetManager)
    {
        $desription = null;
        try {
            $desription = $bricksetManager->getSetById($id)->getDescription();
        } catch (ApiException $e) {
            $this->addFlash('error', $e->getService());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $template = $this->render('brickset/description.html.twig', [
            'description' => $desription,
        ]);

        if ($request->isXmlHttpRequest()) {
            $json = json_encode($template->getContent());
            $response = new Response($json, 200);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        return $template;
    }
}
