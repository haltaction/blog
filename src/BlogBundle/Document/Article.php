<?php

namespace BlogBundle\Document;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @MongoDB\Document(
 *  collection="Articles",
 *  repositoryClass="BlogBundle\Document\ArticleRepository"
 * )
 * @Gedmo\SoftDeleteable(
*   fieldName="deletedAt"
 * )
 */
class Article
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=250)
     * @Assert\Regex("/^[\w\d\p{L} .,-]*$/u",
     *      message="Special characters not allowed"
     * )
     *
     * @MongoDB\String()
     */
    protected $title;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @MongoDB\String()
     */
    protected $slug;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=1000)
     *
     * @MongoDB\String()
     */
    protected $content;

    /**
     * @Assert\Regex("/^[\w\d\p{L} .,-]*$/u",
     *      message="Special characters not allowed"
     * )
     *
     * @MongoDB\Hash()
     */
    protected $tags;

    /**
     * @MongoDB\Integer()
     */
    protected $viewsNumber;

    /**
     * @MongoDB\Hash()
     */
    protected $viewsUsers;

//    protected $comments;

    /**
     * @MongoDB\Date(nullable=true)
     */
    protected $deletedAt;

    /**
     * @MongoDB\Timestamp()
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * Constructor. Set default data.
     */
    public function __construct()
    {
        $this->setViewsNumber(0);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getTagsString()
    {
        return implode(",", array_keys($this->tags));
    }

    /**
     * @param mixed $tags
     *
     * @return self
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getViewsNumber()
    {
        return $this->viewsNumber;
    }

    /**
     * @param mixed $viewsNumber
     *
     * @return self
     */
    public function setViewsNumber($viewsNumber)
    {
        $this->viewsNumber = $viewsNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getViewsUsers()
    {
        return $this->viewsUsers;
    }

    /**
     * @param mixed $viewsUsers
     *
     * @return self
     */
    public function setViewsUsers($viewsUsers)
    {
        $this->viewsUsers = $viewsUsers;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param mixed $deletedAt
     *
     * @return self
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
