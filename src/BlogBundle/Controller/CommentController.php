<?php

namespace BlogBundle\Controller;

use BlogBundle\Document\Article;
use BlogBundle\Document\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    /**
     * @param $slug string
     * @param Request $request
     * @return RedirectResponse
     */
    public function addAction($slug, Request $request)
    {
        $comment = new Comment();
        $form = $this->createFormBuilder($comment)
            ->add('content', TextAreaType::class, [
                'required' => false,
                'label' => 'Comment'
            ])
            ->add('add', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setUserId($this->getUser()->getId());
            $article = $this->get('blog.article.repository')->findOneBy(array('slug' => $slug));
            $article->addComment($comment);

            $this->get('doctrine.odm.mongodb.document_manager')->persist($article);
            $this->get('doctrine.odm.mongodb.document_manager')->flush();

            return $this->redirect($this->generateUrl('blog_view_article', $article->getSlug()));
        }
        // todo return if form invalid
    }

    /**
     * @param Article $article
     *
     * @return Response
     */
    public function commentFormAction($article)
    {
        $comment = new Comment();
        $form = $this->createFormBuilder($comment)
            ->add('content', TextAreaType::class, [
                'required' => false,
                'label' => 'Comment'
            ])
            ->add('add', SubmitType::class)
            ->getForm();

        return $this->render('BlogBundle:Comment:form.html.twig', [
            'slug' => $article->getSlug(),
            'commentForm' => $form->createView()
        ]);
    }
} 