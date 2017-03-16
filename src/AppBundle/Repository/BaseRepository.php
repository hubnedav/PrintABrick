<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class BaseRepository extends EntityRepository
{
    public function save($entity) {
        $this->_em->persist($entity);
        $this->_em->flush($entity);
    }

    public function delete($entity) {
        $this->_em->remove($entity);
        $this->_em->flush($entity);
    }
}