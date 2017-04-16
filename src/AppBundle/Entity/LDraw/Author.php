<?php

namespace AppBundle\Entity\LDraw;

use AppBundle\Entity\Traits\IdentityTrait;
use AppBundle\Entity\Traits\UniqueNameTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Author.
 *
 * @ORM\Table(name="ldraw_author")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LDraw\AuthorRepository")
 */
class Author
{
    use IdentityTrait;
    use UniqueNameTrait;

    /**
     * @var Author
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LDraw\Model", mappedBy="author")
     */
    private $models;

    /**
     * @return Author
     */
    public function getModels()
    {
        return $this->models;
    }
}
