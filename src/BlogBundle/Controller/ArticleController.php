<?php

namespace BlogBundle\Controller;

use BlogBundle\ArticleEvents;
use BlogBundle\Event\FilterArticleEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ArticleController extends Controller
{
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $form = $this->get('form.factory')
            ->createBuilder($this->get('blog.form.type.article'))
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

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
        try {
            $articles = $this->get('blog.article.repository')->getAllArticles($sort);
            $pagerfanta = $this->get('blog.article')->getPagerfantaByArray($articles);
            $articles = $pagerfanta->getCurrentPageResults();
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return $this->render('BlogBundle:Article:list.html.twig', array(
            'articles' => $articles,
            'isNextPage' => $pagerfanta->hasNextPage(),
        ));
    }

    public function listMoreAction($page)
    {
        $request = Request::createFromGlobals();
        $sort = $request->get('sort');
        $search = $request->get('s');
        $type = $request->query->get('type');

        if ('tag' === $type) {
            // load articles in search by tag
            $articles = $this->get('blog.article.repository')->findArticlesByTag($search);
        } elseif (!empty($search)) {
            // load articles in global search
            $articles = $this->get('blog.article.repository')->findAllArticles($search);
        } else {
            // load all articles on main page
            $articles = $this->get('blog.article.repository')->getAllArticles($sort, false);
        }

        $pagerfanta = $this->get('blog.article')->getPagerfantaByArray($articles);
        $pagerfanta->setCurrentPage($page);

        $result = [
            'html' => $this->renderView(
                'BlogBundle:Article:list_li.html.twig',
                [
                    'articles' => $pagerfanta->getCurrentPageResults(),
                ]
            ),
            'isNextPage' => $pagerfanta->hasNextPage(),
        ];

        return JsonResponse::create($result);
    }

    public function editAction($slug, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $article = $this->get('blog.article.repository')->findOneBy(array('slug' => $slug));
        $articleOld = clone $article;

        $form = $this->createForm($this->get('blog.form.type.article'), $article)
            ->add('save', SubmitType::class, array('label' => 'Update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setTags(
                $this->get('blog.tag')->updateTags($article, $articleOld)
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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

        $pagerfanta = $this->get('blog.article')->getPagerfantaByArray($article->getComments());
        $comments = $pagerfanta->getCurrentPageResults();

        return $this->render('BlogBundle:Article:view.html.twig', array(
            'article' => $article,
            'comments' => $comments,
            'isNextPage' => $pagerfanta->hasNextPage(),
        ));
    }

    public function searchAction(Request $request)
    {
        $search = $request->query->get('s');
        $type = $request->query->get('type');

        if ('tag' === $type) {
            $articles = $this->get('blog.article.repository')->findArticlesByTag($search);
        } else {
            $articles = $this->get('blog.article.repository')->findAllArticles($search);
        }
        $pagerfanta = $this->get('blog.article')->getPagerfantaByArray($articles);
        $articles = $pagerfanta->getCurrentPageResults();

        return $this->render('BlogBundle:Article:search_list.html.twig', array(
            'search' => $search,
            'articles' => $articles,
            'isNextPage' => $pagerfanta->hasNextPage(),
        ));
    }
}
