<?php

namespace App\Repository\LDraw;

use App\Entity\Color;
use App\Entity\LDraw\Model;
use App\Entity\LDraw\Subpart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subpart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subpart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subpart[]    findAll()
 * @method Subpart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubpartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subpart::class);
    }

    /**
     * Create new Subpart relation entity or retrieve one by foreign keys.
     *
     * @param $parent
     * @param $child
     * @param $count
     * @param $color
     */
    public function getOrCreate(Model $parent, Model $child, int $count, Color $color): Subpart
    {
        if ($subpart = $this->findOneBy(['parent' => $parent, 'child' => $child, 'color' => $color])) {
            $subpart->setCount($count);

            return $subpart;
        }

        $uow = $this->_em->getUnitOfWork()->getScheduledEntityInsertions();

        foreach ($uow as $scheduled) {
            if ($scheduled instanceof Subpart && $scheduled->getParent() === $parent && $scheduled->getChild() === $child && $scheduled->getColor() === $color) {
                $scheduled->setCount($count);

                return $scheduled;
            }
        }

        $subpart = new Subpart();
        $subpart
            ->setParent($parent)
            ->setChild($child)
            ->setCount($count)
            ->setColor($color);

        return $subpart;
    }
}
