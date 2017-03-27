<?php

namespace AppBundle\Service;

use AppBundle\Manager\LDraw\CategoryManager;
use AppBundle\Manager\LDraw\KeywordManager;
use AppBundle\Manager\LDraw\Part_RelationManager;
use AppBundle\Manager\LDraw\PartManager;
use AppBundle\Manager\LDraw\TypeManager;

class LDrawService
{
    /** @var CategoryManager */
    private $categoryManager;

    /** @var KeywordManager */
    private $keywordManager;

    /** @var TypeManager */
    private $typeManager;

    /** @var PartManager */
    private $partManager;

    /** @var Part_RelationManager */
    private $partRelationManager;

    /**
     * LDrawService constructor.
     *
     * @param CategoryManager      $categoryManager
     * @param KeywordManager       $keywordManager
     * @param TypeManager          $typeManager
     * @param PartManager          $partManager
     * @param Part_RelationManager $partRelationManager
     */
    public function __construct(CategoryManager $categoryManager, KeywordManager $keywordManager, TypeManager $typeManager, PartManager $partManager, Part_RelationManager $partRelationManager)
    {
        $this->categoryManager = $categoryManager;
        $this->keywordManager = $keywordManager;
        $this->typeManager = $typeManager;
        $this->partManager = $partManager;
        $this->partRelationManager = $partRelationManager;
    }

    /**
     * @return mixed
     */
    public function getCategoryManager()
    {
        return $this->categoryManager;
    }

    /**
     * @return mixed
     */
    public function getKeywordManager()
    {
        return $this->keywordManager;
    }

    /**
     * @return TypeManager
     */
    public function getTypeManager()
    {
        return $this->typeManager;
    }

    /**
     * @return PartManager
     */
    public function getPartManager()
    {
        return $this->partManager;
    }

    /**
     * @return Part_RelationManager
     */
    public function getPartRelationManager()
    {
        return $this->partRelationManager;
    }
}
