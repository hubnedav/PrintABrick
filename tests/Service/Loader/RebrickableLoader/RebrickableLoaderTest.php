<?php

namespace App\Tests\Service\Loader;

use App\DataFixtures\LoadColors;
use App\Entity\Rebrickable\Set;
use App\Service\Loader\RebrickableLoader;
use App\Tests\BaseTest;
use Psr\Log\NullLogger;

class RebrickableLoaderTest extends BaseTest
{
    /**
     * @var RebrickableLoader
     */
    private $rebrickableLoader;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadColors::class]);

        $this->rebrickableLoader = new RebrickableLoader($this->em, new NullLogger(), __DIR__.'/fixtures/');
    }

    public function testLoadAll()
    {
        $this->assertCount(0, $this->em->getRepository(Set::class)->findAll());
        $this->rebrickableLoader->loadAll();

        $this->assertCount(1, $this->em->getRepository(Set::class)->findAll());
    }
}
