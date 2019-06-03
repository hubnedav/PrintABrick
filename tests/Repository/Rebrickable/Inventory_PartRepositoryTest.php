<?php

namespace App\Tests\Repository\Rebrickable;

use App\Entity\Rebrickable\Inventory_Part;
use App\Entity\Rebrickable\Set;
use App\Repository\Rebrickable\Inventory_PartRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class Inventory_PartRepositoryTest extends BaseTest
{
    /** @var Inventory_PartRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Inventory_Part::class);
    }

    public function testAllBySetNumber()
    {
        /** @var Set $set */
        $set = $this->em->getRepository(Set::class)->find('8049-1');

        $this->assertCount(4, $this->repository->getAllMatching($set));
        $this->assertCount(3, $this->repository->getAllMatching($set, false));
        $this->assertCount(1, $this->repository->getAllMatching($set, true));
        $this->assertCount(3, $this->repository->getAllMatching($set, null, true));
        $this->assertCount(1, $this->repository->getAllMatching($set, null, false));
    }

    public function testGetPartCount()
    {
        /** @var Set $set */
        $set = $this->em->getRepository(Set::class)->find('8049-1');

        $this->assertEquals(14, $this->repository->getPartCount($set));
        $this->assertEquals(8, $this->repository->getPartCount($set, false));
        $this->assertEquals(6, $this->repository->getPartCount($set, true));
        $this->assertEquals(11, $this->repository->getPartCount($set, null, true));
        $this->assertEquals(3, $this->repository->getPartCount($set, null, false));
    }
}
