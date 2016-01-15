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
     * @return mixed
     * @throws \Exception
     */
    public function getObjectsOfArticles($sortBy, $hydrate = true)
    {
        $sortBy = (!empty($sortBy)) ? $sortBy : 'newest';

        return $this->createQueryBuilder()
            ->sort($this->sortMap($sortBy), 'DESC')
            ->hydrate($hydrate)
            ->getQuery();
    }

    /**
     * @param $sortBy
     * @param bool $hydrate
     *
     * @return array
     */
    public function getAllArticles($sortBy, $hydrate = true)
    {
        return $this->getObjectsOfArticles($sortBy, $hydrate)
            ->toArray();
    }

    /**
     * @param $sortBy
     * @return mixed
     * @throws \Exception
     */
    private function sortMap($sortBy)
    {
        if (!array_key_exists($sortBy, $this->map)) {
            throw new \Exception("Wrong \"sortBy\" value \"$sortBy\".");
        }

        return $this->map[$sortBy];
    }

    /**
     * @param $search
     *
     * @return array
     */
    public function findAllArticles($search)
    {
        $query = $this->createQueryBuilder();

        $query
            ->addOr(
                $query->expr()->field('content')->equals(new \MongoRegex("/.*$search.*/i"))
            )->addOr(
                $query->expr()->field('tags')->equals(new \MongoRegex("/.*$search.*/i"))
            )->addOr(
                $query->expr()->field('title')->equals(new \MongoRegex("/.*$search.*/i"))
            )->sort('updatedAt', 'desc')
        ;

        return $query->getQuery()->toArray();
    }

    /**
     * @param $tag
     *
     * @return array
     */
    public function findArticlesByTag($tag)
    {
        return $this->createQueryBuilder()
            ->field('tags')->equals(new \MongoRegex("/.*$tag.*/i"))
            ->sort('updatedAt', 'desc')
            ->getQuery()
            ->toArray();
    }
}
