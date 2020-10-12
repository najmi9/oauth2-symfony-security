<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use App\Services\ProviderService;

class GithubAuthenticator extends AbstractGuardAuthenticator
{   

    private $provider;
    function __construct(ProviderService $provider)
    {
        $this->provider = $provider;
    }

    public function supports(Request $request)
    {

       return $request->query->get('code');

    }

    public function getCredentials(Request $request)
    {
        return [
        "code"=>$request->query->get('code'),
        "state"=>$request->query->get('state')
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->provider->loadUser($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
       return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // todo
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // todo
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // todo
    }

    public function supportsRememberMe()
    {
        // todo
    }
}
