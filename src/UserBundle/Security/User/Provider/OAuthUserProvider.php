<?php

namespace UserBundle\Security\User\Provider;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseProvider;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use UserBundle\Document\User;

class OAuthUserProvider extends BaseProvider
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $userId = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $userId.''));
        $email = $response->getEmail();
        $username = $response->getNickname() ?: $response->getRealName();
        if (null === $user) {
            $user = $this->userManager->findUserByEmail($email);
            if (null === $user || !$user instanceof UserInterface) {
                $user = new User();
                $username = str_replace(' ', '', $username);
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setPassword('');
                $user->setEnabled(true);
                $user->setOAuthService($response->getResourceOwner()->getName());
                $user->setOAuthId($userId);
                $user->setOAuthAccessToken($response->getAccessToken());
                $this->userManager->updateUser($user);
            } else {
                throw new AuthenticationException('Username or email has been already used.');
            }
        } else {
            $checker = new UserChecker();
            $checker->checkPreAuth($user);
        }
        return $user;
    }
}
