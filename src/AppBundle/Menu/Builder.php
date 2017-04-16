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
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', [
            'route' => 'homepage',
        ]);

        $menu->addChild('Models', [
            'route' => 'model_index',
        ]);

        $menu->addChild('Sets', [
            'route' => 'set_index',
        ]);

        $menu->addChild('Colors', [
            'route' => 'color_index',
        ]);

        return $menu;
    }
}
