<?php

namespace BlogBundle\Controller;

use BlogBundle\Document\Article;
use BlogBundle\Document\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentController extends Controller
{
    /**
     * @param $slug string
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function addAction($slug, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $comment = new Comment();
        $form = $this->createFormBuilder($comment)
            ->setMethod('POST')
            ->setAction($this->generateUrl('blog_article_add_comment', [
                'slug' => $slug,
            ]))
            ->add('content', TextAreaType::class, [
                'required' => false,
                'label' => 'Comment',
            ])
            ->add('add', SubmitType::class)
            ->getForm();
        $article = $this->get('blog.article.repository')->findOneBy(array('slug' => $slug));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //            todo move to service
            $comment->setUserId($this->getUser()->getId());
            $comment->setUserName($this->getUser()->getUsername());
            $article->addComment($comment);

            $this->get('doctrine.odm.mongodb.document_manager')->persist($article);
            $this->get('doctrine.odm.mongodb.document_manager')->flush();

            return $this->redirectToRoute('blog_view_article', [
                'slug' => $article->getSlug(),
            ]);
        }
        // can't pass form with error through controller, so for errors render other form, extended article view
        return $this->render('BlogBundle:Comment:form_error.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView(),
        ]);
    }

    /**
     * @param Article $article
     * @param Form    $form
     *
     * @return Response
     */
    public function commentFormAction($article, Form $form = null)
    {
        if (empty($form)) {
            $comment = new Comment();
            $form = $this->createFormBuilder($comment)
                ->add('content', TextAreaType::class, [
                    'required' => false,
                    'label' => 'Comment',
                ])
                ->add('add', SubmitType::class)
                ->getForm();
        }

        return $this->render('BlogBundle:Comment:form.html.twig', [
            'slug' => $article->getSlug(),
            'commentForm' => $form->createView(),
        ]);
    }

    public function editAction($slug, $comment_id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $article = $this->get('blog.article.repository')->findOneBy(array('slug' => $slug));
        $comment = $article->findComment($comment_id);
        if (!$this->isGranted('edit', $comment)) {
            throw new AccessDeniedException();
        }
        $form = $this->createFormBuilder($comment)
            ->setMethod('POST')
            ->setAction($this->generateUrl('blog_article_edit_comment', [
                'slug' => $slug,
                'comment_id' => $comment_id,
            ]))
            ->add('content', TextAreaType::class, [
                'required' => false,
                'label' => 'Comment',
            ])
            ->add('edit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('doctrine.odm.mongodb.document_manager')->persist($comment);
            $this->get('doctrine.odm.mongodb.document_manager')->flush();

            return $this->redirectToRoute('blog_view_article', [
                'slug' => $article->getSlug(),
            ]);
        }

        $pagerfanta = $this->get('blog.article')->getPagerfantaByArray($article->getComments());
        $comments = $pagerfanta->getCurrentPageResults();

        // can't pass form with error through controller, so for errors render other form, extended article view
        return $this->render('BlogBundle:Comment:form_error.html.twig', [
            'article' => $article,
            'comments' => $comments,
            'isNextPage' => $pagerfanta->hasNextPage(),
            'comment_id' => $comment_id,
            'commentForm' => $form->createView(),
        ]);
    }

    public function deleteAction($slug, $comment_id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $article = $this->get('blog.article.repository')->findOneBy(array('slug' => $slug));
        $comment = $article->findComment($comment_id);
        if (!$this->isGranted('edit', $comment)) {
            throw new AccessDeniedException();
        }
        $article->deleteComment($comment);
        $this->get('doctrine.odm.mongodb.document_manager')->persist($article);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return $this->redirectToRoute('blog_view_article', [
            'slug' => $article->getSlug(),
        ]);
    }

    public function listMoreAction($slug, $page)
    {
        $article = $this->get('blog.article.repository')->findOneBy(array('slug' => $slug));
        $pagerfanta = $this->get('blog.article')->getPagerfantaByArray($article->getComments());
        $pagerfanta->setCurrentPage($page);

        $result = [
            'html' => $this->renderView(
                'BlogBundle:Comment:list_li.html.twig',
                [
                    'article' => $article,
                    'comments' => $pagerfanta->getCurrentPageResults(),
                ]
            ),
            'isNextPage' => $pagerfanta->hasNextPage(),
        ];

        return JsonResponse::create($result);
    }
}
