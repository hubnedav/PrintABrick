<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Set;
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

        /** @var Repository $setRepository */
        $setRepository = $repositoryManager->getRepository(Set::class);
        /** @var Repository $modelRepository */
        $modelRepository = $repositoryManager->getRepository(Model::class);

        // Option 1. Returns all users who have example.net in any of their mapped fields
        $setsResult = $setRepository->find($query, 5000);
        $modelResult = $modelRepository->find($query, 5000);

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

        /** @var Repository $setRepository */
        $setRepository = $repositoryManager->getRepository(Set::class);
        /** @var Repository $modelRepository */
        $modelRepository = $repositoryManager->getRepository(Model::class);

        // Option 1. Returns all users who have example.net in any of their mapped fields
        $setsResult = $setRepository->find($query, 5);
        $modelResult = $modelRepository->find($query, 5);

        $models = [];
        /** @var Model $model */
        foreach ($modelResult as $model) {
            $models[] = [
                'title' => $model->getId().' '.$model->getName(),
                'url' => $this->generateUrl('model_detail', ['id' => $model->getId()]),
            ];
        }

        $sets = [];
        /** @var Set $set */
        foreach ($setsResult as $set) {
            $sets[] = [
                'title' => $set->getId().' '.$set->getName(),
                'url' => $this->generateUrl('set_detail', ['id' => $set->getId()]),
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
