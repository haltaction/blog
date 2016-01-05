<?php

namespace BlogBundle\Controller;

use BlogBundle\Document\Article;
use BlogBundle\Document\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Form;
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
        $this->denyAccessUnlessGranted("ROLE_USER");
        $comment = new Comment();
        $form = $this->createFormBuilder($comment)
            ->add('content', TextAreaType::class, [
                'required' => false,
                'label' => 'Comment'
            ])
            ->add('add', SubmitType::class)
            ->getForm();
        $article = $this->get('blog.article.repository')->findOneBy(array('slug' => $slug));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setUserId($this->getUser()->getId());

            $article->addComment($comment);

            $this->get('doctrine.odm.mongodb.document_manager')->persist($article);
            $this->get('doctrine.odm.mongodb.document_manager')->flush();

            return $this->redirectToRoute('blog_view_article', [
                'slug' => $article->getSlug()
            ]);
        }
        // can't pass form with error through controller, so for errors render other form, extended article view
        return $this->render('BlogBundle:Comment:form_error.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @param Article $article
     * @param Form $form
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
                    'label' => 'Comment'
                ])
                ->add('add', SubmitType::class)
                ->getForm();
        }

        return $this->render('BlogBundle:Comment:form.html.twig', [
            'slug' => $article->getSlug(),
            'commentForm' => $form->createView()
        ]);
    }
} 