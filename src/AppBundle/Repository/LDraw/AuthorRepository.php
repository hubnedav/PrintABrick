<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Author;
use AppBundle\Repository\BaseRepository;

class AuthorRepository extends BaseRepository
{
    public function findOneByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * Get existing entity or create new.
     *
     * @param $name
     *
     * @return Author
     */
    public function getOrCreate($name)
    {
        if (($author = $this->findOneByName($name)) == null) {
            $author = new Author();
            $author->setName($name);
        }

        return $author;
    }
}
