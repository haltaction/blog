<?php

namespace BlogBundle\Controller;

use BlogBundle\ArticleEvents;
use BlogBundle\Event\FilterArticleEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    public function addAction(Request $request)
    {
//        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $form = $this->get('form.factory')
            ->createBuilder($this->get('blog.form.type.article'))
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $article->setTags(
                $this->get('blog.tag')->getTagIds($article->getTags())
            );

            $this->get('doctrine.odm.mongodb.document_manager')->persist($article);
            $this->get('doctrine.odm.mongodb.document_manager')->flush();

            return $this->redirect($this->generateUrl('blog_list_articles'));
        }

        return $this->render('BlogBundle:Article:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function listAction()
    {
        $request = Request::createFromGlobals();
        $sort = $request->get('sort');
        if (("newest" == $sort) || empty($sort)) {
            $articles = $this->get('blog.article.repository')->getAllArticlesByCreated();
        } elseif ("popular" == $sort) {
            $articles = $this->get('blog.article.repository')->getAllArticlesByViews();
        }

        return $this->render('BlogBundle:Article:list.html.twig', array(
            'articles' => $articles
        ));
    }

    public function editAction($slug, Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $article = $this->get('blog.article.repository')->findOneBy(array('slug' => $slug));
        $articleOld = clone $article;


        $form = $this->createForm($this->get('blog.form.type.article'), $article)
            ->add('save', SubmitType::class, array('label' => "Update"));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setTags(
                $this->get('blog.tag')->updateTagsIds($article, $articleOld)
            );

            $this->get('doctrine.odm.mongodb.document_manager')->persist($article);
            $this->get('doctrine.odm.mongodb.document_manager')->flush();

            return $this->redirect($this->generateUrl('blog_list_articles'));
        }

        return $this->render('BlogBundle:Article:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction($slug)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $article = $this->get('blog.article.repository')->findOneBy(array('slug' => $slug));
        $this->get('blog.tag')->removeTags($article);
        $this->get('doctrine.odm.mongodb.document_manager')->remove($article);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return $this->redirect($this->generateUrl('blog_list_articles'));
    }

    public function viewAction($slug)
    {
        $article = $this->get('blog.article.repository')->findOneBy(array('slug' => $slug));
        if (!$article) {
            throw $this->createNotFoundException();
        }

        // dispatch the article view event
        $request = Request::createFromGlobals();
        $user = $this->getUser();
        $event = new FilterArticleEvent($article, $request, $user);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(ArticleEvents::ARTICLE_VIEW, $event);

        return $this->render('@Blog/Article/view.html.twig', array(
            'article' => $article
        ));
    }
}
