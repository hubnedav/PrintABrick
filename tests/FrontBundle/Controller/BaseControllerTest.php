<?php

namespace Tests\FrontBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\AppBundle\Fixtures\LoadBaseData;

abstract class BaseControllerTest extends WebTestCase
{
    public function setUp()
    {
        // If you are using the Doctrine Fixtures Bundle you could load these here
        $this->loadFixtures([
            LoadBaseData::class
        ]);

        $this->runCommand('fos:elastica:populate');
    }
}