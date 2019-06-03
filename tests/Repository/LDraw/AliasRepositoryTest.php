<?php

namespace App\Tests\Repository\LDraw;

use App\Entity\LDraw\Alias;
use App\Entity\LDraw\Model;
use App\Repository\LDraw\AliasRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class AliasRepositoryTest extends BaseTest
{
    /** @var AliasRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);
        $this->repository = $this->em->getRepository(Alias::class);
    }

    public function testGetOrCreate()
    {
        $this->assertCount(2, $this->repository->findAll());

        /** @var Model $model */
        $model = $this->em->getRepository(Model::class)->find(1);

        $alias = $this->repository->getOrCreate(25, $model);
        $this->repository->save($alias);
        $this->assertCount(2, $this->repository->findAll());

        $alias = $this->repository->getOrCreate(33, $model);
        $this->repository->save($alias);
        $this->assertCount(3, $this->repository->findAll());
    }
}
