<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BaseRepository extends EntityRepository
{
    public function save($entity, $flush = true)
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush($entity);
        }
    }

    public function delete($entity, $flush = true)
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush($entity);
        }
    }
}
