<?php

namespace BlogBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class ArticleRepository extends DocumentRepository
{
    protected $map = [
        'newest' => 'updatedAt',
        'popular' => 'viewsNumber',
    ];

    /**
     * @param $sortBy
     * @param bool $hydrate
     * @return array
     */
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

    /**
     * @param $search
     * @return array
     */
    public function findAllArticles($search)
    {
        $query = $this->createQueryBuilder();

        $query
            ->addOr(
                $query->expr()->field('content')->equals(new \MongoRegex("/.*$search.*/i"))
            )->addOr(
                $query->expr()->field('tags.'.$search)->exists(true)
            )->addOr(
                $query->expr()->field('title')->equals(new \MongoRegex("/.*$search.*/i"))
            )->sort('updatedAt', 'desc')
        ;

        return $query->getQuery()->toArray();
    }

    /**
     * @param $tag
     * @return array
     */
    public function findArticlesByTag($tag)
    {
        return $this->createQueryBuilder()
            ->field('tags.'.$tag)->exists(true)
            ->sort('updatedAt', 'desc')
            ->getQuery()
            ->toArray();
    }
}
