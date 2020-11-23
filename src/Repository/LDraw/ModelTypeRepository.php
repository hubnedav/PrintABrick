<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\ModelType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModelType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModelType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModelType[]    findAll()
 * @method ModelType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModelType::class);
    }

    /**
     * Get existing entity or create new.
     *
     * @param $name
     */
    public function getOrCreate($name): ModelType
    {
        if ($modelType = $this->findOneBy(['name' => $name])) {
            return $modelType;
        }

        $uow = $this->_em->getUnitOfWork()->getScheduledEntityInsertions();

        foreach ($uow as $scheduled) {
            if ($scheduled instanceof ModelType && $scheduled->getName() === $name) {
                return $scheduled;
            }
        }

        $modelType = new ModelType();
        $modelType->setName($name);

        return $modelType;
    }
}
