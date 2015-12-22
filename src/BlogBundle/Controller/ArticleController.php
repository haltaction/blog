<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    public function addAction(Request $request)
    {
        $form = $this->get('form.factory')
            ->createBuilder($this->get('blog.form.type.article'))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $article->setTags(
                $this->get('blog.tag')->getTagIds($article->getTags())
            );

            $this->get('doctrine.odm.mongodb.document_manager')->persist($article);
            $this->get('doctrine.odm.mongodb.document_manager')->flush();

            return $this->redirect($this->generateUrl('blog_add_article'));
        }

        return $this->render('BlogBundle:Article:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
