<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
//    todo CRUD for pages
    public function showPageAction($slug)
    {
        $page = $this->get('doctrine.odm.mongodb.document_manager')
            ->getDocumentCollection('BlogBundle\Document\Page')
            ->findOne([
                'slug' => $slug
            ]);

        return $this->render('@Blog/Page/view.html.twig', [
            'page' => $page
        ]);
    }
} 