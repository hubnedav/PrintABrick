<?php

namespace App\Repository;

use App\Entity\Color;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Color|null find($id, $lockMode = null, $lockVersion = null)
 * @method Color|null findOneBy(array $criteria, array $orderBy = null)
 * @method Color[]    findAll()
 * @method Color[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Color::class);
    }

    public function getOrCreate($id): Color
    {
        if ($color = $this->find($id)) {
            return $color;
        }

        $uow = $this->_em->getUnitOfWork()->getScheduledEntityInsertions();
        foreach ($uow as $scheduled) {
            if ($scheduled instanceof Color && $scheduled->getId() === $id) {
                return $scheduled;
            }
        }

        $color = new Color($id);

        return $color;
    }
}
