<?php

namespace BlogBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(
 *  collection="Tags",
 *  repositoryClass="BlogBundle\Document\TagRepository"
 * )
 */
class Tag
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\String()
     */
    protected $name;

    /**
     * @MongoDB\Integer()
     */
    protected $numberArticles;

    /**
     * Constructor. Set default data.
     */
    public function __construct()
    {
        $this->setNumberArticles(0);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumberArticles()
    {
        return $this->numberArticles;
    }

    /**
     * @param mixed $numberArticles
     *
     * @return self
     */
    public function setNumberArticles($numberArticles)
    {
        $this->numberArticles = $numberArticles;

        return $this;
    }

    /**
     * @return self
     */
    public function incrementNumberArticles()
    {
        ++$this->numberArticles;

        return $this;
    }

    /**
     * @return self
     */
    public function decrementNumberArticles()
    {
        --$this->numberArticles;

        return $this;
    }
}
