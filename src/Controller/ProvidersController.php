<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProvidersController extends AbstractController
{
    function __construct($clientId, $facebookId, $googleId)
    {
        $this->googleId = $googleId;
        $this->clientId = $clientId;
        $this->facebookId = $facebookId;
    }

    /**
     * @Route("/login/github", name="github")
     */
    public function github(UrlGeneratorInterface $generator): Response
    {
        $url = $generator->generate("user", [], UrlGeneratorInterface::ABSOLUTE_URL);
        return new RedirectResponse("http://github.com/login/oauth/authorize?client_id=" . $this->clientId . "&redirect_uri=" . $url . " &state=github");
    }

    /**
     * @Route("/login/facbook", name="facebook")
     */
    public function facebook(UrlGeneratorInterface $generator): Response
    {
        $url = $generator->generate("user", [], UrlGeneratorInterface::ABSOLUTE_URL);

        return new RedirectResponse("https://www.facebook.com/v7.0/dialog/oauth?client_id=" . $this->facebookId . "&redirect_uri=" . $url . " &state=facebook");
    }

    /**
     * @Route("/login/google", name="google")
     */
    public function google(UrlGeneratorInterface $generator): Response
    {
        $url = $generator->generate("user", [], UrlGeneratorInterface::ABSOLUTE_URL);
        return new RedirectResponse("https://accounts.google.com/o/oauth2/v2/auth?client_id=" . $this->googleId . "&redirect_uri=" . $url . "&response_type=code&scope=openid email profile&state=google&access_type=offline");
    }
}
