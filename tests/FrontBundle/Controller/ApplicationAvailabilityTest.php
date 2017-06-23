<?php

namespace Tests\FrontBundle;

use Tests\FrontBundle\Controller\BaseControllerTest;

class ApplicationAvailabilityTest extends BaseControllerTest
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        return [
            ['/'],
            ['/colors/'],
            ['/bricks/'],
            ['/bricks/1'],
            ['/bricks/1/zip'],
            ['/bricks/1/sets'],
            ['/sets/'],
            ['/sets/?query=name&partCount[from]=620'],
            ['/sets/8049-1'],
            ['/sets/8049-1/zip'],
            ['/sets/8049-1/inventory'],
            ['/sets/8049-1/models'],
            ['/sets/8049-1/colors'],
            ['/parts/1'],
            ['/search/autocomplete?query=name'],
            ['/search/?query=name'],
            ['/sets/brickset/8540/reviews'],
            ['/sets/brickset/8540/instructions'],
            ['/sets/brickset/8540/description'],
            ['/sets/brickset/8540/images'],
        ];
    }
}
