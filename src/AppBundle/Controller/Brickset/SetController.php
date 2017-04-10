<?php

namespace AppBundle\Controller\Brickset;

use AppBundle\Api\Exception\EmptyResponseException;
use AppBundle\Entity\Rebrickable\Color;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Form\FilterSetType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/brickset/sets")
 */
class SetController extends Controller
{
    /**
     * @Route("/", name="brickset_browse")
     */
    public function browseAction(Request $request)
    {
        $form = $this->createForm(FilterSetType::class);

        $form->handleRequest($request);

        $sets = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $sets = $this->get('api.client.brickset')->getSets([
                'theme' => $data['theme'] ? $data['theme']->getTheme() : '',
                'subtheme' => $data['subtheme'] ? $data['subtheme']->getSubtheme() : '',
                'year' => $data['years'] ? $data['years']->getYear() : '',
            ]);
        }

        return $this->render('brickset/browse.html.twig', [
            'form' => $form->createView(),
            'sets' => $sets,
        ]);
    }

    /**
     * @Route("/{id}/instructions", name="brickset_instructions")
     */
    public function instructionsAction(Request $request, $id)
    {
        $instructions = [];
        try {
            $instructions = $this->get('api.manager.brickset')->getSetInstructions($id);
        }  catch (EmptyResponseException $e) {
//            $this->addFlash('warning', 'No instruction found on Brickset.com');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('brickset/instructions.html.twig',[
            'instructions' => $instructions,
        ]);
    }

    /**
     * @Route("/{id}/reviews", name="brickset_reviews")
     */
    public function reviewsAction(Request $request, $id)
    {
        $reviews = [];
        try {
            $reviews = $this->get('api.manager.brickset')->getSetReviews($id);
        }  catch (EmptyResponseException $e) {
//            $this->addFlash('warning', 'No review found on Brickset.com');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('brickset/reviews.html.twig',[
            'reviews' => $reviews,
        ]);
    }

    /**
     * @Route("/{id}/images", name="brickset_images")
     */
    public function imagesAction(Request $request, $id)
    {
        $images = [];
        try {
            $images = $this->get('api.manager.brickset')->getAdditionalImages($id);
        }  catch (EmptyResponseException $e) {
//            $this->addFlash('warning', 'No images found on Brickset.com');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('brickset/images.html.twig',[
            'images' => $images,
        ]);
    }
}
