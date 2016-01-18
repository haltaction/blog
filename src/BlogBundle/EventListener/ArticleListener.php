<?php

namespace BlogBundle\EventListener;

use BlogBundle\Event\FilterArticleEvent;
use Doctrine\ODM\MongoDB\DocumentManager;

class ArticleListener
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @param FilterArticleEvent $event
     */
    public function onArticleView(FilterArticleEvent $event)
    {
        $user = $event->getUser();
        if (empty($user)) {
            $userInfo = array(
                'userIP' => $event->getRequest()->getClientIp(),
                'userAgent' => $event->getRequest()->headers->get('User-Agent'),
            );
        } else {
            $userInfo = array(
                'userId' => $user->getId(),
            );
        }

        $article = $event->getArticle();
        $viewUser = $article->findViewUser($userInfo);
        if (empty($viewUser)) {
            $article->addViewsUser($userInfo)->incrementViewsNumber();
//            $this->documentManager->persist($article);
            $this->documentManager->flush();
        }
    }
}
