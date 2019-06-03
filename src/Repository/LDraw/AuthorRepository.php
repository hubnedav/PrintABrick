<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\Author;
use App\Repository\BaseRepository;

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
        if (null == ($author = $this->findOneByName($name))) {
            $author = new Author();
            $author->setName($name);
        }

        return $author;
    }
}
