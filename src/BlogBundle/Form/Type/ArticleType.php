<?php

namespace BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array(
            'required' => false,
        ));
        $builder->add('content', TextareaType::class, array(
            'required' => false,
        ));
        $builder->add('tags', TextType::class, array(
            'required' => false,
        ));

        $builder->get('tags')
            ->addModelTransformer(new CallbackTransformer(
                // from Document to form element
                function ($array) {
                    return ($array) ? implode(',', $array) : null;
                },
                // from form element to mongo Hash with empty values(Tag ids)
                function ($tagsString) {
                    $tags = explode(',', $tagsString);
                    $tags = array_map('trim', $tags);
                    $tags = str_replace('.', '', $tags);
                    $tags = array_unique($tags);

                    return $tags;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BlogBundle\Document\Article',
        ));
    }

    public function getBlockPrefix()
    {
        return 'article';
    }
}
