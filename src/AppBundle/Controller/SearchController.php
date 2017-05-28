<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Model\ModelSearch;
use AppBundle\Model\SetSearch;
use AppBundle\Repository\Search\ModelRepository;
use AppBundle\Repository\Search\SetRepository;
use FOS\ElasticaBundle\HybridResult;
use FOS\ElasticaBundle\Repository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Cache\Resolver\CacheResolver;
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
    public function searchAction(Request $request)
    {
        $query = trim(strip_tags($request->get('query')));

        /** var FOS\ElasticaBundle\Manager\RepositoryManager */
        $repositoryManager = $this->get('fos_elastica.manager');

        /** @var SetRepository $setRepository */
        $setRepository = $repositoryManager->getRepository(Set::class);
        /** @var ModelRepository $modelRepository */
        $modelRepository = $repositoryManager->getRepository(Model::class);

        $setsResult = $setRepository->search(new SetSearch($query), 1000);
        $modelResult = $modelRepository->search(new ModelSearch($query), 1000);

        return $this->render('search/index.html.twig', [
            'sets' => $setsResult,
            'models' => $modelResult,
            'query' => $query,
        ]);
    }

    /**
     * @Route("/autocomplete", name="search_autocomplete")
     */
    public function autocompleteAction(Request $request)
    {
        $query = trim(strip_tags($request->get('query')));

        /** @var CacheManager $liip */
        $liip = $this->get('liip_imagine.cache.manager');

        /** var FOS\ElasticaBundle\Manager\RepositoryManager */
        $repositoryManager = $this->get('fos_elastica.manager');

        /** @var SetRepository $setRepository */
        $setRepository = $repositoryManager->getRepository(Set::class);
        /** @var ModelRepository $modelRepository */
        $modelRepository = $repositoryManager->getRepository(Model::class);

        // Option 1. Returns all users who have example.net in any of their mapped fields
        $setsResult = $setRepository->findHighlighted($query, 4);
        $modelResult = $modelRepository->findHighlighted($query, 4);

        $models = [];
        /** @var HybridResult $model */
        foreach ($modelResult as $model) {
            $id = isset($model->getResult()->getHighlights()['id']) ? $model->getResult()->getHighlights()['id'][0] : $model->getTransformed()->getId();
            $name = isset($model->getResult()->getHighlights()['name']) ? $model->getResult()->getHighlights()['name'][0] : $model->getTransformed()->getName();

            $models[] = [
                'id' => $id,
                'name' => $name,
                'url' => $this->generateUrl('model_detail', ['id' => $model->getTransformed()->getId()]),
                'img' =>  $liip->getBrowserPath('-1/'.$model->getTransformed()->getId().'.png','part_min'),
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
                'img' =>  $liip->getBrowserPath($set->getTransformed()->getId().'.jpg','set_min'),
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
