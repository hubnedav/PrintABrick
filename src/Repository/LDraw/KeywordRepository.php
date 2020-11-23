<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\Keyword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class KeywordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Keyword::class);
    }

    public function findByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * Create new Keyword entity with $name or retrieve one.
     *
     * @param $name
     *
     * @return Keyword
     */
    public function getOrCreate($name)
    {
        if ($keyword = $this->findByName($name)) {
            return $keyword;
        }

        $uow = $this->_em->getUnitOfWork()->getScheduledEntityInsertions();

        foreach ($uow as $scheduled) {
            if ($scheduled instanceof Keyword) {
                if ($scheduled->getName() == $name) {
                    return $scheduled;
                }
            }
        }

        $keyword = new Keyword();
        $keyword->setName($name);

        return $keyword;
    }
}
