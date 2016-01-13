<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BaseController extends Controller
{
    public function searchFormAction()
    {
        $form = $this->createFormBuilder(null, ['csrf_protection' => false])
            ->setMethod('GET')
            ->setAction($this->generateUrl('blog_search'))
            ->setAttribute('name', '')
//            ->add('s', SearchType::class, [
//                'required' => false,
//                'label' => false,
//            ])
            ->add('search', SubmitType::class)
            ->getForm();

        return $this->render('BlogBundle::search_form.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    public function tagCloudAction()
    {
        $tags = $this->get('blog.tag.repository')->getAllTags();

        $tagsWithWeight = $this->get('blog.tag')->getTagsWithWeigh($tags);

        return $this->render('BlogBundle:Tag:tag_cloud.html.twig', [
            'tag_cloud' => $tagsWithWeight
        ]);
    }
}
