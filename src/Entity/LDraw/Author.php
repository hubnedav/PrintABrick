<?php

namespace App\Entity\LDraw;

use App\Entity\Traits\IdentityTrait;
use App\Entity\Traits\UniqueNameTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Author.
 *
 * @ORM\Table(name="ldraw_author")
 * @ORM\Entity(repositoryClass="App\Repository\LDraw\AuthorRepository")
 */
class Author
{
    use IdentityTrait;
    use UniqueNameTrait;

    /**
     * @var Author
     *
     * @ORM\OneToMany(targetEntity="App\Entity\LDraw\Model", mappedBy="author")
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
