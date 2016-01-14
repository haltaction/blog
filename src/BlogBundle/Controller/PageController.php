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
        if (is_null($page)) {
            throw $this->createNotFoundException();
        }

        return $this->render('@Blog/Page/view.html.twig', [
            'page' => $page
        ]);
    }
} 