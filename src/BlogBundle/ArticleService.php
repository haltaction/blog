<?php

namespace BlogBundle;

use BlogBundle\Document\ArticleRepository;
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
        $this->pagerfanta->setMaxPerPage(self::PER_PAGE);

        return $this->pagerfanta;
    }
}
