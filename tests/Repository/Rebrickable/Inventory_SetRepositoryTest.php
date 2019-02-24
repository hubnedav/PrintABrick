<?php

namespace App\Tests\Repository\Rebrickable;

use App\Entity\Rebrickable\Inventory_Set;
use App\Repository\Rebrickable\Inventory_SetRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class Inventory_SetRepositoryTest extends BaseTest
{
    /** @var Inventory_SetRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Inventory_Set::class);
    }

    public function testFindAllBySetNumber()
    {
        $this->assertCount(1, $this->repository->findAllBySetNumber('8049-1'));
        $this->assertNull($this->repository->findAllBySetNumber('8055-1'));
    }
}
