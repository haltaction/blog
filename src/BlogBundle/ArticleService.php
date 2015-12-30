<?php

namespace BlogBundle;

use BlogBundle\Document\ArticleRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class ArticleService
{
    /**
     * @var ArticleRepository
     */
    protected $articleRepository;

    /**
     * @var Pagerfanta
     */
    public $pagerfanta;

    /**
     * @var array
     */
    protected $neededKeys = [
        'title' => '',
        'slug' => '',
        'content' => '',
    ];

    /**
     * @param ArticleRepository $articleRepository
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param $array
     *
     * @return Pagerfanta
     */
    public function getPagerfantaByArray($array)
    {
        $adapter = new ArrayAdapter($array);
        $this->pagerfanta = new Pagerfanta($adapter);

        return $this->pagerfanta;
    }

//    /**
//     * @param $articles array
//     *
//     * @return array
//     */
//    public function filteredArticlesFields($articles)
//    {
//        $articlesArray = [];
//        foreach ($articles as $article) {
//            $articlesArray[] = array_intersect_key($article, $this->neededKeys);
//        }
//
//        return $articlesArray;
//    }

//    public function getArticlesByPage($page, $sortBy)
//    {
//        $articles = $this->articleRepository->getAllArticles($sortBy);
//        $this->getPagerfantaByArray($articles);
//        $this->pagerfanta->setCurrentPage($page);
//
//        return $this->pagerfanta->getCurrentPageResults();
//    }
}
