<?php

namespace AppBundle\Controller\Brickset;

use AppBundle\Api\Exception\ApiException;
use AppBundle\Api\Exception\EmptyResponseException;
use AppBundle\Entity\Rebrickable\Set;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/brickset/sets")
 */
class SetController extends Controller
{
    /**
     * @Route("/{id}/instructions", name="brickset_instructions")
     * @Method("GET")
     */
    public function instructionsAction(Request $request, $id)
    {
        $instructions = [];
        try {
            $instructions = $this->get('api.manager.brickset')->getSetInstructions($id);
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
    public function reviewsAction(Request $request, $id)
    {
        $reviews = [];
        try {
            $reviews = $this->get('api.manager.brickset')->getSetReviews($id);
        } catch (ApiException $e) {
            $this->addFlash('error', $e->getService());
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $template = $this->render('brickset/reviews.html.twig', [
            'reviews' => $reviews,
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
    public function imagesAction(Request $request, $id)
    {
        $images = [];
        try {
            $images = $this->get('api.manager.brickset')->getAdditionalImages($id);
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
    public function descriptionAction(Request $request, $id)
    {
        $desription = null;
        try {
            $desription = $this->get('api.manager.brickset')->getSetById($id)->getDescription();
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
