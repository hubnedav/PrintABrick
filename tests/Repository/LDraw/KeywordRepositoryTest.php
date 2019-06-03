<?php

namespace App\Tests\Repository\LDraw;

use App\Entity\LDraw\Keyword;
use App\Repository\LDraw\KeywordRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class KeywordRepositoryTest extends BaseTest
{
    /** @var KeywordRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Keyword::class);
    }

    public function testGetOrCreate()
    {
        $this->assertCount(0, $this->repository->findAll());

        $keyword = $this->repository->getOrCreate('Keyword');
        $this->repository->save($keyword);
        $this->assertCount(1, $this->repository->findAll());

        $keyword = $this->repository->getOrCreate('Keyword');
        $this->repository->save($keyword);
        $this->assertCount(1, $this->repository->findAll());
    }
}
