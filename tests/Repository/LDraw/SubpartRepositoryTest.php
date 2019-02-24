<?php

namespace App\Tests\Repository\LDraw;

use App\Entity\LDraw\Model;
use App\Entity\LDraw\Subpart;
use App\Repository\LDraw\SubpartRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class SubpartRepositoryTest extends BaseTest
{
    /** @var SubpartRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Subpart::class);
    }

    public function testGetOrCreate()
    {
        $this->assertCount(3, $this->repository->findAll());

        /** @var Model $model */
        $model = $this->em->getRepository(Model::class)->find(1);
        /** @var Model $child */
        $child = $this->em->getRepository(Model::class)->find(2);

        $subpart = $this->repository->getOrCreate($model, $child, 2, 1);
        $this->repository->save($subpart);
        $this->assertCount(3, $this->repository->findAll());

        $subpart = $this->repository->getOrCreate($model, $child, 2, 2);
        $this->repository->save($subpart);
        $this->assertCount(4, $this->repository->findAll());

        $subpart = $this->repository->getOrCreate($model, $child, 2, 3);
        $this->repository->save($subpart);
        $this->assertCount(5, $this->repository->findAll());
    }
}
