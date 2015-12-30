<?php

namespace BlogBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ArticleRepository extends DocumentRepository
{
    protected $map = array(
        'newest' => 'createdAt',
        'popular' => 'viewsNumber'
    );

    public function getAllArticles($sortBy, $limit = 10)
    {
        $sortBy = (!empty($sortBy)) ? $sortBy : 'newest';

        return $this->createQueryBuilder()
            ->sort($this->sortMap($sortBy), 'DESC')
            ->limit($limit)
            ->getQuery()
            ->toArray();
    }

    private function sortMap($sortBy)
    {
        return $this->map[$sortBy];
    }
} 