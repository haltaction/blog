<?php

namespace BlogBundle\DataFixtures\ODM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader;

class BlogFixturesLoader extends AbstractLoader implements DependentFixtureInterface
{
    /**
     * Returns an array of file paths to fixtures. File paths can be relatives, specified with the `@Bundlename`
     * notation or being SplFileInfo instances.
     *
     * @return string[]|\SplFileInfo[]
     */
    public function getFixtures()
    {
        return [
            __DIR__.'/tag.yml',
            __DIR__.'/comment.yml',
            __DIR__.'/article.yml',
            __DIR__.'/page.yml',
        ];
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return [
            'UserBundle\DataFixtures\ODM\UserFixturesLoader'
        ];
    }
}