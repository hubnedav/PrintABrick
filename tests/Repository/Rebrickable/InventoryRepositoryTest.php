<?php

namespace App\Tests\Repository\Rebrickable;

use App\Entity\Rebrickable\Inventory;
use App\Repository\Rebrickable\InventoryRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class InventoryRepositoryTest extends BaseTest
{
    /** @var InventoryRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Inventory::class);
    }

    public function testFindNewestInventoryBySetNumber()
    {
        $this->assertEquals('2', $this->repository->findNewestInventoryBySetNumber('8049-1')->getVersion());
    }
}
