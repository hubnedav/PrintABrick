<?php

namespace Tests\FrontBundle\Controller;

use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

abstract class BaseControllerTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();

        // If you are using the Doctrine Fixtures Bundle you could load these here
        $this->loadFixtures([
            LoadBaseData::class,
        ]);

        $this->filesystem->write('models/1.stl', 'abcd');

        $this->runCommand('fos:elastica:populate');
    }
}
