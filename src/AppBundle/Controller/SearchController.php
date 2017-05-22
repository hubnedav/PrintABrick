<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\Search\ModelRepository;
use AppBundle\Repository\Search\SetRepository;
use FOS\ElasticaBundle\HybridResult;
use FOS\ElasticaBundle\Repository;
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
        /** @var Repository $modelRepository */
        $modelRepository = $repositoryManager->getRepository(Model::class);

        // Option 1. Returns all users who have example.net in any of their mapped fields
        $setsResult = $setRepository->find($query, 8);
        $modelResult = $modelRepository->find($query, 8);

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

        /** var FOS\ElasticaBundle\Manager\RepositoryManager */
        $repositoryManager = $this->get('fos_elastica.manager');

        /** @var SetRepository $setRepository */
        $setRepository = $repositoryManager->getRepository(Set::class);
        /** @var ModelRepository $modelRepository */
        $modelRepository = $repositoryManager->getRepository(Model::class);

        // Option 1. Returns all users who have example.net in any of their mapped fields
        $setsResult = $setRepository->findHighlighted($query, 5);
        $modelResult = $modelRepository->findHighlighted($query, 5);

        $models = [];
        /** @var HybridResult $model */
        foreach ($modelResult as $model) {
            $id = isset($model->getResult()->getHighlights()['id']) ? $model->getResult()->getHighlights()['id'][0] : $model->getTransformed()->getId();
            $name = isset($model->getResult()->getHighlights()['name']) ? $model->getResult()->getHighlights()['name'][0] : $model->getTransformed()->getName();

            $models[] = [
                'id' => $id,
                'name' => $name,
                'url' => $this->generateUrl('model_detail', ['id' => $model->getTransformed()->getId()]),
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
