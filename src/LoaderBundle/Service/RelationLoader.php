<?php

namespace LoaderBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Repository\LDraw\ModelRepository;
use AppBundle\Repository\Rebrickable\PartRepository;
use Doctrine\ORM\EntityManagerInterface;
use LoaderBundle\Util\RelationMapper;
use Psr\Log\LoggerInterface;

class RelationLoader extends BaseLoader
{
    /** @var RelationMapper */
    private $relationMapper;

    /** @var ModelRepository */
    private $modelRepository;

    /** @var PartRepository */
    private $partRepository;

    /**
     * RelationLoader constructor.
     *
     * @param EntityManagerInterface $em
     * @param LoggerInterface        $logger
     * @param RelationMapper         $relationMapper
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, RelationMapper $relationMapper)
    {
        $this->relationMapper = $relationMapper;
        $this->modelRepository = $em->getRepository(Model::class);
        $this->partRepository = $em->getRepository(Part::class);

        parent::__construct($em, $logger);
    }

    public function loadAll()
    {
        $parts = $this->partRepository->findAll();
        $this->load($parts);
    }

    public function loadNotPaired()
    {
        $parts = $this->partRepository->findAllNotPaired();
        $this->load($parts);
    }

    private function load($parts)
    {
        $this->initProgressBar(count($parts));
        /** @var Part $part */
        foreach ($parts as $part) {
            $model = $this->loadPartRelation($part);

            if ($model) {
                $part->setModel($model);
                $this->partRepository->save($part);
            }

            $this->progressBar->setMessage($part->getId());
            $this->progressBar->advance();
        }
        $this->progressBar->finish();
    }

    /**
     * Loads relations between Rebrickable part and ldraw models for $parts.
     *
     * @param Part $part
     *
     * @return Model|null
     */
    private function loadPartRelation(Part $part)
    {
        $number = $part->getId();
        // Find model by id or alias
        $model = $this->modelRepository->findOneByNumber($number);
        if (!$model) {
            // Try to find relation from part_model.yml file
            $number = $this->relationMapper->find($this->getPrintedParentId($number), 'part_model');
            // Find model by id or alias
            $model = $this->modelRepository->findOneByNumber($number);

            if (!$model) {
                // If model not found, try to find by identical model name
                $model = $this->modelRepository->findOneByName($part->getName());
            }
        }

        return $model;
    }

    /**
     * Get id of parent for printed parts form part id.
     *
     * @param $number
     *
     * @return string|null LDraw number of printed part parent
     */
    private function getPrintedParentId($number)
    {
        if (preg_match('/(^970[c,x])([0-9a-z]*)$/', $number, $matches)) {
            return '970c00';
        } elseif (preg_match('/(^973)([c,p][0-9a-z]*)$/', $number, $matches)) {
            return '973c00';
        } elseif (preg_match('/(^.*)((pr[x]{0,1}[0-9]{1,7}[a-z]{0,1})|(pat[[0-9]{1,4}[a-z]{0,1}))$/', $number, $matches)) {
            return $matches[1];
        } elseif (preg_match('/(^.*)((pb[0-9]{1,4}[a-z]{0,1}))$/', $number, $matches)) {
            return $matches[1];
        } elseif (preg_match('/(^.*)(p[x]{0,1}[0-9a-z]{2,4})$/', $number, $matches)) {
            return $matches[1];
        }

        return $number;
    }
}
