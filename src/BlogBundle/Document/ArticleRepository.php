<?php

namespace BlogBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ArticleRepository extends DocumentRepository
{
    public function getAllArticlesByUpdated()
    {
        return $this->createQueryBuilder()
            ->sort('updatedAt', 'DESC')
            ->getQuery()
            ->execute();
    }
} 