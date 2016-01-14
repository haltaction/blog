<?php

namespace BlogBundle\Twig;

class TwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sentencesLimit', array($this, 'sentencesLimit'))
        );
    }

    public function sentencesLimit($string, $sentencesNumber = 1)
    {
        $sentences = preg_split('/(?<=[.?!(...)])\s+(?=[a-z\p{L}\p{Nd}])/iu', $string);
        $sentences = array_slice($sentences, 0, $sentencesNumber);
        $result = implode(' ', $sentences);
        return $result;
    }

    public function getName()
    {
        return 'twig_extension';
    }
}