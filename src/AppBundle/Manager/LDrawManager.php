<?php

namespace AppBundle\Manager;

use AppBundle\Manager\LDraw\AliasManager;
use AppBundle\Manager\LDraw\CategoryManager;
use AppBundle\Manager\LDraw\KeywordManager;
use AppBundle\Manager\LDraw\ModelManager;
use AppBundle\Manager\LDraw\SubpartManager;
use AppBundle\Manager\LDraw\TypeManager;

class LDrawManager
{
    /** @var CategoryManager */
    private $categoryManager;

    /** @var KeywordManager */
    private $keywordManager;

    /** @var SubpartManager */
    private $subpartManager;

    /** @var ModelManager */
    private $modelManager;

    /** @var AliasManager */
    private $aliasManager;

    /**
     * LDrawService constructor.
     *
     * @param CategoryManager $categoryManager
     * @param KeywordManager  $keywordManager
     * @param SubpartManager  $subpartManager
     * @param ModelManager    $modelManager
     * @param AliasManager    $aliasManager
     */
    public function __construct(CategoryManager $categoryManager, KeywordManager $keywordManager, SubpartManager $subpartManager, ModelManager $modelManager, AliasManager $aliasManager)
    {
        $this->categoryManager = $categoryManager;
        $this->keywordManager = $keywordManager;
        $this->subpartManager = $subpartManager;
        $this->modelManager = $modelManager;
        $this->aliasManager = $aliasManager;
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
     * @return SubpartManager
     */
    public function getSubpartManager()
    {
        return $this->subpartManager;
    }

    /**
     * @return ModelManager
     */
    public function getModelManager()
    {
        return $this->modelManager;
    }

    /**
     * @return AliasManager
     */
    public function getAliasManager()
    {
        return $this->aliasManager;
    }
}
