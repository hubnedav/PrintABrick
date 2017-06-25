<?php

namespace Tests\AppBundle\Repository\Rebrickable;


use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Repository\Rebrickable\InventoryRepository;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class InventoryRepositoryTest extends BaseTest
{
    /** @var InventoryRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Inventory::class);
    }

    public function testFindNewestInventoryBySetNumber()
    {
        $this->assertEquals('2',$this->repository->findNewestInventoryBySetNumber('8049-1')->getVersion());
    }
}