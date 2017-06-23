<?php

namespace Tests\LoaderBundle\Service;

use AppBundle\DataFixtures\ORM\LoadColors;
use AppBundle\Entity\Rebrickable\Set;
use LoaderBundle\Service\RebrickableLoader;
use Tests\AppBundle\BaseTest;

class RebrickableLoaderTest extends BaseTest
{
    /**
     * @var RebrickableLoader
     */
    private $rebrickableLoader;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadColors::class]);

        $this->rebrickableLoader = new RebrickableLoader($this->em, $this->get('monolog.logger.event'), __DIR__.'/fixtures/');
    }

    public function testLoadAll()
    {
        $this->assertCount(0, $this->em->getRepository(Set::class)->findAll());
        $this->rebrickableLoader->loadAll();

        $this->assertCount(1, $this->em->getRepository(Set::class)->findAll());
    }

//    /**
//     * @expectedException LoaderBundle\Exception\Loader\LoadingRebrickableFailedException
//     */
//    public function testRollback() {
//        $this->rebrickableLoader = new RebrickableLoader($this->em,$this->get('monolog.logger.event'),__DIR__.'/corrupt/');
//
//        $this->assertCount(0,$this->em->getRepository(Set::class)->findAll());
//
//        $this->rebrickableLoader->loadAll();
//
//        $this->assertCount(0,$this->em->getRepository(Set::class)->findAll());
//    }
}
