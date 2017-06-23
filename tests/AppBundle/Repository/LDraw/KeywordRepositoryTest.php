<?php

namespace Tests\AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Keyword;
use AppBundle\Repository\LDraw\KeywordRepository;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class KeywordRepositoryTest extends BaseTest
{
    /** @var KeywordRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadBaseData::class]);

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
