<?php

namespace BlogBundle\Document;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @MongoDB\EmbeddedDocument()
 */
class Comment
{
    /**
     * @MongoDB\Id(
     *  strategy="auto"
     * )
     */
    protected $id;

    /**
     * @MongoDB\String()
     */
    protected $userId;

    /**
     * @MongoDB\String()
     */
    protected $userName;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=1000)
     * @Assert\Regex("/[<>\[\]{}@#%*&;]+/u",
     *      message="Special characters not allowed",
     *      match=false
     * )
     *
     * @MongoDB\String()
     */
    protected $content;

    /**
     * @MongoDB\Timestamp()
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @MongoDB\Timestamp()
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
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
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
