<?php

namespace BlogBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ArticleRepository extends DocumentRepository
{
    protected $map = [
        'newest' => 'createdAt',
        'popular' => 'viewsNumber',
    ];

    public function getAllArticles($sortBy, $hydrate = true)
    {
        $sortBy = (!empty($sortBy)) ? $sortBy : 'newest';

        return $this->createQueryBuilder()
            ->sort($this->sortMap($sortBy), 'DESC')
            ->hydrate($hydrate)
            ->getQuery()
            ->toArray();
    }

    private function sortMap($sortBy)
    {
        return $this->map[$sortBy];
    }
}
