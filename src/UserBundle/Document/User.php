<?php

namespace UserBundle\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as UniqueDocument;


/** @MongoDB\Document(
 *     collection="user",
 *     repositoryClass="UHome\UserBundle\Document\UserRepository"
 * )
 * @UniqueDocument(fields={"email"})
 */
class User extends BaseUser
{
    /**
     * @MongoDB\Id
     *
     * @var string
     */
    protected $id;

    /**
     * @MongoDB\String
     *
     * @var string
     */
    protected $oauthService;

    /**
     * @MongoDB\String
     *
     * @var string
     */
    protected $oauthAccessToken;

    /**
     * @MongoDB\String
     *
     * @var string
     */
    protected $oauthId;

    /**
     * Get id.
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set oauthService
     *
     * @param string $oauthService
     * @return self
     */
    public function setOAuthService($oauthService)
    {
        $this->oauthService = $oauthService;
        return $this;
    }

    /**
     * Get oauthService
     *
     * @return string $oauthService
     */
    public function getOAuthService()
    {
        return $this->oauthService;
    }

    /**
     * Set oauthAccessToken
     *
     * @param string $oauthAccessToken
     * @return self
     */
    public function setOAuthAccessToken($oauthAccessToken)
    {
        $this->oauthAccessToken = $oauthAccessToken;
        return $this;
    }

    /**
     * Get oauthAccessToken
     *
     * @return string $oauthAccessToken
     */
    public function getOAuthAccessToken()
    {
        return $this->oauthAccessToken;
    }

    /**
     * Set oauthId
     *
     * @param string $oauthId
     * @return self
     */
    public function setOAuthId($oauthId)
    {
        $this->oauthId = $oauthId;
        return $this;
    }

    /**
     * Get oauthId
     *
     * @return string $oauthId
     */
    public function getOAuthId()
    {
        return $this->oauthId;
    }
}
