<?php

namespace App\Service\Loader;

use App\Entity\LDraw\Model;
use App\Entity\Rebrickable\Part;
use App\Entity\Rebrickable\Set;
use App\Repository\LDraw\ModelRepository;
use App\Repository\Rebrickable\PartRepository;
use App\Repository\Rebrickable\SetRepository;
use App\Service\SetService;
use App\Util\RelationMapper;
use Doctrine\ORM\EntityManagerInterface;

class RelationLoader extends LoggerAwareLoader
{
    private RelationMapper $relationMapper;
    private SetService $setService;

    private EntityManagerInterface $em;
    private ModelRepository $modelRepository;
    private PartRepository $partRepository;
    private SetRepository $setRepository;

    /**
     * RelationLoader constructor.
     */
    public function __construct(EntityManagerInterface $em, SetService $setService, RelationMapper $relationMapper)
    {
        parent::__construct();
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger();
        $this->setService = $setService;
        $this->relationMapper = $relationMapper;
        $this->modelRepository = $this->em->getRepository(Model::class);
        $this->partRepository = $this->em->getRepository(Part::class);
        $this->setRepository = $this->em->getRepository(Set::class);

        ini_set('memory_limit', '1G');
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

    /**
     * @var Part[]
     */
    private function load($parts)
    {
        $this->output->progressStart(count($parts));

        foreach ($parts as $index => $part) {
            $model = $this->loadPartRelation($part);

            if ($model) {
                $part->setModel($model);
                $this->em->persist($part);
            }

            $this->output->progressAdvance();

            // clear managed objects to avoid memory issues
            if (0 === $index % 500) {
                $this->em->flush();
            }
        }

        $this->em->flush();

        $this->output->progressFinish();

//        $this->loadSetCompletness();
    }

    public function loadSetCompletness()
    {
        $sets = $this->setRepository->findAll();

        $this->output->progressStart(count($sets));
        /** @var Set $set */
        foreach ($sets as $set) {
            $missingCount = $this->setService->getPartCount($set, false, false);
            $partCount = $this->setService->getPartCount($set, false);
            $set->setCompleteness($partCount > 0 ? (1 - $missingCount / $partCount) * 100 : 0);

            $this->em->persist($set);
            $this->output->progressAdvance();
        }
        $this->em->flush();
        $this->output->progressFinish();
    }

    /**
     * Loads relations between Rebrickable part and ldraw models for $parts.
     */
    private function loadPartRelation(Part $part): ?Model
    {
        $number = $part->getId();

        // Find model by id or alias
        // Try to find relation from part_model.yml file
        $number = (string) $this->relationMapper->find($this->getPrintedParentId($number), 'part_model');

        $number = $this->getPrintedParentId($number);

        if ($model = $this->modelRepository->findOneByNumber($number)) {
            if ($model->getAliasOf()->first()) {
                return $model->getAliasOf()->first();
            }

            return $model;
        }

        foreach ($part->getParentParts('P') as $parentPart) {
            if ($model = $this->modelRepository->findOneByNumber($parentPart->getParent()->getId())) {
                return $model;
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
        if (preg_match('/(^970[c,x])([\da-z]*)$/', $number, $matches)) {
            return '970c00';
        }
        if (preg_match('/(^973)([c,p][\da-z]*)$/', $number, $matches)) {
            return '973c00';
        }
        if (preg_match('/(^.*)((pr[x]{0,1}\d{1,7}[a-z]{0,1})|(pat[\d{1,4}[a-z]{0,1}))$/', $number, $matches)) {
            return $matches[1];
        }
        if (preg_match('/(^.*)((pb\d{1,4}[a-z]{0,1}))$/', $number, $matches)) {
            return $matches[1];
        }
        if (preg_match('/(^.*)(p[x]{0,1}[\da-z]{2,4})$/', $number, $matches)) {
            return $matches[1];
        }
        if (preg_match('/(^.*)(pat[\d]*)$/', $number, $matches)) {
            return $matches[1];
        }

        return $number;
    }
}
