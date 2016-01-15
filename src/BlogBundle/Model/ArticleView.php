<?php

namespace BlogBundle\Model;

class ArticleView
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    public $content;

    /**
     * @var
     */
    public $viewsNumber;

    /**
     * @var
     */
    public $tags;

    /**
     * @var string
     */
    public $updatedAt;
} 