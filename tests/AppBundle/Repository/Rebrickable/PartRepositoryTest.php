<?php

namespace Tests\AppBundle\Repository\Rebrickable;


use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Repository\Rebrickable\PartRepository;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class PartRepositoryTest extends BaseTest
{
    /** @var PartRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Part::class);
    }

    public function testFindAllNotPaired() {
        $this->assertCount(1,$this->repository->findAllNotPaired());
    }
}