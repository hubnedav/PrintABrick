<?php

namespace Tests\AppBundle;

use Tests\AppBundle\Controller\BaseControllerTest;

class ApplicationAvailabilityTest extends BaseControllerTest
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url) {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertTrue( $client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        return array(
            array('/'),
            array('/colors/'),
            array('/bricks/'),
            array('/bricks/1'),
            array('/bricks/1/sets'),
            array('/sets/'),
            array('/sets/8049-1'),
            array('/sets/8049-1/inventory'),
            array('/sets/8049-1/models'),
            array('/sets/8049-1/colors'),
            array('/parts/1')
        );
    }
}