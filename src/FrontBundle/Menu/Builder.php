<?php

namespace FrontBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Builder
{
    /** @var FactoryInterface */
    private $factory;

    /** @var RequestStack */
    private $requestStack;

    /**
     * Builder constructor.
     *
     * @param FactoryInterface $factory
     * @param RequestStack     $requestStack
     */
    public function __construct(FactoryInterface $factory, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
    }

    public function mainMenu(array $options)
    {
        $request = $this->requestStack->getCurrentRequest();
        $menu = $this->factory->createItem('page.home', [
            'route' => 'homepage',
            'extras' => [
//                'icon' => 'home',
            ],
        ]);

        $models = $menu->addChild('page.model.index', [
            'route' => 'model_index',
        ]);

        $models->addChild('page.model.detail', [
            'route' => 'model_detail',
            'routeParameters' => ['id' => $request->get('id', 0)],
            'display' => false,
            'extras' => [
                'value' => $request->get('id', 0),
            ],
        ]);

        $sets = $menu->addChild('page.set.index', [
            'route' => 'set_index',
            'options' => [
//                'icon' => 'edit',
            ],
        ]);

        $sets->addChild('page.set.detail', [
            'route' => 'set_detail',
            'routeParameters' => ['id' => $request->get('id', 0)],
            'display' => false,
            'extras' => [
                'value' => $request->get('id', 0),
            ],
        ]);

        $menu->addChild('page.search', [
            'route' => 'search_results',
            'display' => false,
            'extras' => [
                'value' => $request->get('query', 0),
            ],
        ]);

        $menu->addChild('page.colors', [
            'route' => 'color_index',
        ]);

        $parts = $menu->addChild('page.part.index', [
            'route' => null,
            'display' => false,
            'options' => [
//                'icon' => 'edit',
            ],
        ]);

        $parts->addChild('page.part.detail', [
            'route' => 'part_detail',
            'routeParameters' => ['id' => $request->get('id', 0)],
            'display' => false,
            'extras' => [
                'value' => $request->get('id', 0),
            ],
        ]);

        return $menu;
    }
}
