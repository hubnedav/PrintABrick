<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Set;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Search controller.
 *
 * @Route("search")
 */
class SearchController extends Controller
{
    /**
     * @Route("/", name="search_result")
     */
    public function searchAction(Request $request)
    {
        $query = trim(strip_tags($request->get('query')));

        $modelsResult = $this->get('repository.ldraw.model')->findByQuery($query);
        $setsResult = $this->get('repository.rebrickable.set')->findByQuery($query);

        return $this->render('search/index.html.twig', [
            'sets' => $setsResult,
            'models' => $modelsResult,
        ]);
    }

    /**
     * @Route("/autocomplete", name="search_autocomplete")
     */
    public function autocompleteAction(Request $request)
    {
        $query = trim(strip_tags($request->get('query')));

        $modelsResult = $this->get('repository.ldraw.model')->findByQuery($query,7);

        $models = [];
        /** @var Model $model */
        foreach ($modelsResult as $model) {
            $models[] = [
                'title' => $model->getNumber().' '.$model->getName(),
                'url' => $this->generateUrl('model_detail',['number' => $model->getNumber()]),
            ];
        }

        $setsResult = $this->get('repository.rebrickable.set')->findByQuery($query,7);

        $sets = [];
        /** @var Set $set */
        foreach ($setsResult as $set) {
            $sets[] = [
                'title' => $set->getNumber().' '.$set->getName(),
                'url' => $this->generateUrl('set_detail',['number' => $set->getNumber()]),
            ];
        }

        $response = new JsonResponse();
        $response->setData([
            'results' => [
                'category' => [
                    'name' => 'Sets',
                    'results' => $sets,
                ],
                'category1' => [
                    'name' => 'Models',
                    'results' => $models,
                ]
            ]
        ]);


        return $response;
    }
}