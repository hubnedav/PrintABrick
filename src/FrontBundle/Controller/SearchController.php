<?php

namespace FrontBundle\Controller;

use AppBundle\Service\SearchService;
use FOS\ElasticaBundle\HybridResult;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
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
     * @Route("/", name="search_results")
     */
    public function searchAction(Request $request, SearchService $searchService)
    {
        $query = trim(strip_tags($request->get('query')));

        return $this->render('search/index.html.twig', [
            'sets' => $searchService->searchSetsByQuery($query),
            'models' => $searchService->searchModelsByQuery($query),
            'query' => $query,
        ]);
    }

    /**
     * @Route("/autocomplete", name="search_autocomplete")
     */
    public function autocompleteAction(Request $request, SearchService $searchService)
    {
        $query = trim(strip_tags($request->get('query')));

        /** @var CacheManager $liip */
        $liip = $this->get('liip_imagine.cache.manager');

        $setsResult = $searchService->searchSetsHighlightedByQuery($query, 4);
        $modelResult = $searchService->searchModelsHighlightedByQuery($query, 4);

        $models = [];
        /** @var HybridResult $model */
        foreach ($modelResult as $model) {
            $id = isset($model->getResult()->getHighlights()['id']) ? $model->getResult()->getHighlights()['id'][0] : $model->getTransformed()->getId();
            $name = isset($model->getResult()->getHighlights()['name']) ? $model->getResult()->getHighlights()['name'][0] : $model->getTransformed()->getName();

            $models[] = [
                'id' => $id,
                'name' => $name,
                'url' => $this->generateUrl('model_detail', ['id' => $model->getTransformed()->getId()]),
                'img' => $liip->getBrowserPath('-1/'.$model->getTransformed()->getId().'.png', 'part_min'),
            ];
        }

        $sets = [];
        /** @var HybridResult $set */
        foreach ($setsResult as $set) {
            $id = isset($set->getResult()->getHighlights()['id']) ? $set->getResult()->getHighlights()['id'][0] : $set->getTransformed()->getId();
            $name = isset($set->getResult()->getHighlights()['name']) ? $set->getResult()->getHighlights()['name'][0] : $set->getTransformed()->getName();

            $sets[] = [
                'id' => $id,
                'name' => $name,
                'url' => $this->generateUrl('set_detail', ['id' => $set->getTransformed()->getId()]),
                'img' => $liip->getBrowserPath($set->getTransformed()->getId().'.jpg', 'set_min'),
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
                ],
            ],
            // optional action below results
            'action' => [
                'url' => $this->generateUrl('search_results', ['query' => $query]),
                'text' => 'View results',
            ],
        ]);

        return $response;
    }
}
