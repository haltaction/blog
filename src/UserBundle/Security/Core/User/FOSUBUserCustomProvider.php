<?php

namespace UserBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use UserBundle\Document\Social;
use UserBundle\Document\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class FOSUBUserCustomProvider extends BaseClass
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $userManager = $this->userManager;
        $user = $userManager->findUserByUsername($username);

        if (null === $user) {
            $email = $response->getEmail();
            $user = $userManager->findUserByEmail($email);

            if (null === $user) {
                $service = $response->getResourceOwner()->getName();
//                $serviceId = $response['id']; todo get social ID
                $user = $userManager->createUser();
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setPassword('');
                $user->setEnabled(true);

                $social = new Social();
                // todo find id in social and return user if isset
                $user->addSocial($social->setService($service)->setUserId($username));

                $userManager->updateUser($user);

                return $user;
            }
            return $user;
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected an instance of FOS\UserBundle\Model\User, but got "%s".', get_class($user)));
        }

        $property = $this->getProperty($response);

        // Symfony <2.5 BC
        if (method_exists($this->accessor, 'isWritable') && !$this->accessor->isWritable($user, $property)
            || !method_exists($this->accessor, 'isWritable') && !method_exists($user, 'set'.ucfirst($property))) {
            throw new \RuntimeException(sprintf("Class '%s' must have defined setter method for property: '%s'.", get_class($user), $property));
        }

        $username = $response->getUsername();

        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $this->disconnect($previousUser, $response);
        }

        $this->accessor->setValue($user, $property, $username);

        $this->userManager->updateUser($user);
    }


} 