<?php

namespace UserBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument()
 */
class Social
{
    /**
     * @MongoDB\String
     */
    protected $serviceName;

    /**
     * @MongoDB\String
     */
    protected $userId;

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->serviceName;
    }

    /**
     * @param mixed serviceName
     * @return self
     */
    public function setService($serviceName)
    {
        $this->serviceName = $serviceName;
        return $this;
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
     * @return self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

} 