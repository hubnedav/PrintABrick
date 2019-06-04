<?php

namespace App\Menu;

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

        $models = $menu->addChild('page.brick.index', [
            'route' => 'brick_index',
        ]);

        $models->addChild('page.brick.detail', [
            'route' => 'brick_detail',
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

        return $menu;
    }
}
