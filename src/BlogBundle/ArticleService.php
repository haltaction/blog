<?php

namespace BlogBundle;

use BlogBundle\Document\ArticleRepository;
use BlogBundle\Model\ArticleList;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class ArticleService
{
    const PER_PAGE = 10;

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
     * @param Shifter           $shifter
     */
    public function __construct(ArticleRepository $articleRepository, Shifter $shifter)
    {
        $this->articleRepository = $articleRepository;
        $this->shifter = $shifter;
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
        $this->pagerfanta->setMaxPerPage(self::PER_PAGE);

        return $this->pagerfanta;
    }

    /**
     * @param $sort
     *
     * @return array
     */
    public function getArticlesListDtoBy($sort)
    {
        // todo add DTO type as param
        $articles = $this->articleRepository->getObjectsOfArticles($sort, true);
        $articleDto = [];
        foreach ($articles as $article) {
            $articleDto[] = $this->shifter->toDto($article, new ArticleList());
        }

        return $articleDto;
    }
}
