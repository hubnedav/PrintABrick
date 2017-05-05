<?php

namespace AppBundle\Menu;

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
        $menu = $this->factory->createItem('root', [
            'route' => 'homepage',
        ]);

        $menu->addChild('homepage', [
            'route' => 'homepage',
        ]);

        $models = $menu->addChild('Models', [
            'route' => 'model_index',
        ]);

        $models->addChild('Colors', [
            'route' => 'color_index',
        ]);

        $menu->addChild('Sets', [
            'route' => 'set_index',
        ]);

        return $menu;
    }
}
