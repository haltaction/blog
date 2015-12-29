<?php

namespace BlogBundle\Event;

use BlogBundle\Document\Article;
use FOS\UserBundle\Propel\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class FilterArticleEvent extends Event
{
    /**
     * @var Article
     */
    protected $article;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var User
     */
    protected $user;

    public function __construct(Article $article, Request $request, $user)
    {
        $this->article = $article;
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return User or null
     */
    public function getUser()
    {
        return $this->user;
    }

}
