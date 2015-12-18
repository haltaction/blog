<?php

namespace UserBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class UserRepository extends DocumentRepository
{
    /**
     * @param string $serviceName
     * @param string $serviceId
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findUserByService($serviceName = '', $serviceId = '')
    {
        $query = $this->createQueryBuilder();
        $query
            ->field('socials.serviceName')->equals($serviceName)
            ->field('socials.userId')->equals((string) $serviceId)
        ;

         return $query->getQuery()->getSingleResult();
    }
} 