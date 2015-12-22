<?php

namespace BlogBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class TagRepository extends DocumentRepository
{
    public function getTagByName($name = '')
    {
        $query = $this->createQueryBuilder()
            ->field('name')->equals($name);

        return $query->getQuery()->getSingleResult();
    }
} 