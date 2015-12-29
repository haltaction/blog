<?php

namespace BlogBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ArticleRepository extends DocumentRepository
{
    public function getAllArticlesByCreated($limit = 10)
    {
        return $this->createQueryBuilder()
            ->sort('createdAt', 'DESC')
            ->limit($limit)
            ->getQuery()
            ->toArray();
    }

    public function getAllArticlesByViews($limit = 10)
    {
        return $this->createQueryBuilder()
            ->sort('viewsNumber', 'DESK')
            ->sort('createdAt', 'DESK')
            ->limit($limit)
            ->getQuery()
            ->toArray();
    }
} 