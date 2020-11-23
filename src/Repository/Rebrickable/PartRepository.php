<?php

namespace App\Repository\Rebrickable;

use App\Entity\Rebrickable\Part;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Part|null find($id, $lockMode = null, $lockVersion = null)
 * @method Part|null findOneBy(array $criteria, array $orderBy = null)
 * @method Part[]    findAll()
 * @method Part[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Part::class);
    }

    public function findAllNotPaired()
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.model', 'm')
            ->where('m IS NULL');

        return $queryBuilder->getQuery()->getResult();
    }
}
