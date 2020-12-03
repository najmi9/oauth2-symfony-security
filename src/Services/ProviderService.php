<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Security\User;

class ProviderService
{
	private  $httpClient, $urlGenerator;

	function __construct(string $facebookSecret, string  $facebookId, string $googleSecret, string  $googleId, string $clientSecret, string  $clientId, HttpClientInterface $httpClient, UrlGeneratorInterface $urlGenerator)
	{
		$this->clientSecret = $clientSecret;
		$this->clientId = $clientId;
		$this->googleSecret = $googleSecret;
		$this->facebookId = $facebookId;
		$this->facebookSecret = $facebookSecret;
		$this->googleId = $googleId;
		$this->httpClient = $httpClient;
		$this->urlGenerator = $urlGenerator;
	}

	public function loadUser(array $credantials)
	{
		$code = $credantials['code'];
		$state = $credantials['state'];
		//-----------facebook-----------------------------
		if ($state === "facebook") {
			$redirectUri = $this->urlGenerator->generate("user", [], UrlGeneratorInterface::ABSOLUTE_URL);
			$url = sprintf(
				"https://graph.facebook.com/v7.0/oauth/access_token?client_id=%s&client_secret=%s&code=%s&redirect_uri=%s",
				$this->facebookId,
				$this->facebookSecret,
				$code,
				$redirectUri
			);
			$response = $this->httpClient->request("GET", $url, [
				"headers" => [
					"Accept" => "application/json"
				]
			]);
			$token = $response->toArray()['access_token'];
			$url = sprintf("https://graph.facebook.com/me/accounts?access_token=%s", $token);
			$response = $this->httpClient->request("GET", $url, [
				"headers" => [
					"Accept" => "application/json"
				]
			]);
			$data = $response->toArray();
			$data['picture'] = "";
			return new User($data);
		}
		//---------Github------------------------------------------
		if ($state == "github") {
			$url = sprintf(
				"https://github.com/login/oauth/access_token?client_id=%s&client_secret=%s&code=%s",
				$this->clientId,
				$this->clientSecret,
				$code
			);
			$response = $this->httpClient->request("POST", $url, [
				"headers" => [
					"Accept" => "application/json"
				]
			]);

			if (!isset($response->toArray()['access_token'])) {
				throw new \Exception("The code passed is incorrect or expired", 1);
			}
			$token = $response->toArray()['access_token'];
			$userInfo = $this->httpClient->request(
				"GET",
				"https://api.github.com/user",
				[
					"headers" => [
						"Authorization" => "token " . $token
					]
				]
			);
			$data = $userInfo->toArray();
			return new User($data);
		}
		//----------Google-----------------------    
		if ($state === "google") {
			$redirectUri = $this->urlGenerator->generate("user", [], UrlGeneratorInterface::ABSOLUTE_URL);
			$url = sprintf("https://oauth2.googleapis.com/token?client_id=%s&client_secret=%s&code=%s&grant_type=authorization_code&redirect_uri=%s", $this->googleId, $this->googleSecret, $code, $redirectUri);
			$response = $this->httpClient->request("POST", $url, [
				"headers" => [
					"Content-Type" => "application/x-www-form-urlencoded",
					'Content-Length' => 0,
					"Accept" => "application/json"
				]
			]);
			$info = $response->toArray()['id_token'];
			$jwt = explode('.', $info);
			$data = json_decode(base64_decode($jwt[1]), true);
			return new User($data);
		}
	}
}
