<?php

namespace UserBundle\AuthenticationHandler;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler as BaseAuthenticationSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class with the default authentication success handling logic.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
class CustomUserAuthenticationSuccessHandler extends BaseAuthenticationSuccessHandler
{
    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $request->getSession()->remove('custom_referer');
        return $this->httpUtils->createRedirectResponse($request, $this->determineTargetUrl($request));
    }
}
