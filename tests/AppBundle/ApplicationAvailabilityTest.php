<?php

namespace Tests\AppBundle;

use Tests\AppBundle\Fixtures\LoadBaseData;

class ApplicationAvailabilityTest extends BaseTest
{

    protected function setUp()
    {
        $this->setUpDb();

        $this->loadFixtures([
            LoadBaseData::class
        ]);

        parent::setUp();
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = $this->makeClient();
        $client->request('GET', $url);

        $this->assertTrue( $client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        return array(
            array('/'),
            array('/models')
        );
    }
}