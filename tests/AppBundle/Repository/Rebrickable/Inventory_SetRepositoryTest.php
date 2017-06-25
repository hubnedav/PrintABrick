<?php

namespace Tests\AppBundle\Repository\Rebrickable;

use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Inventory_Set;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\Rebrickable\Inventory_PartRepository;
use AppBundle\Repository\Rebrickable\Inventory_SetRepository;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class Inventory_SetRepositoryTest extends BaseTest
{
    /** @var Inventory_SetRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Inventory_Set::class);
    }

    public function testFindAllBySetNumber() {
        $this->assertCount(1,$this->repository->findAllBySetNumber('8049-1'));
        $this->assertNull($this->repository->findAllBySetNumber('8055-1'));
    }
}
