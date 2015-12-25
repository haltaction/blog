<?php

namespace BlogBundle\DataFixtures\ODM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Nelmio\Alice\Fixtures;

class LoadFixtureData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        Fixtures::load(__DIR__.'/article.yml', $manager, ['providers' => [$this]]);
    }

    public function mongoHash(array $mongoHash)
    {
        foreach ($mongoHash as $tag) {
            $hash[$tag->getName()] = $tag->getId();
        }

        return $hash;
    }
}