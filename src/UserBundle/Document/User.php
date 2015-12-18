<?php

namespace UserBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(
 *  collection="User",
 *  repositoryClass="UserBundle\Document\UserRepository"
 * )
 */
class User extends BaseUser
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\EmbedMany(
     *       targetDocument="UserBundle\Document\Social"
     * )
     */
    protected $socials = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getSocials()
    {
        return $this->socials ?: $this->socials = new ArrayCollection();
    }

    /**
     * @param mixed $socials
     * @return self
     */
    public function setSocials(array $socials)
    {
        $this->socials = new ArrayCollection($socials);

        return $this;
    }

    /**
     * @param mixed $social
     */
    public function addSocial(Social $social)
    {
        $this->socials[] = $social;
    }
}