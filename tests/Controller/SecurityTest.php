<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Requires the demo fixtures: php bin/console doctrine:fixtures:load --env=test
 */
class SecurityTest extends WebTestCase
{
	public function testAnonymousUserIsRedirectedToLogin(): void
	{
		$client = static::createClient();

		foreach (['/admin', '/account/'] as $url) {
			$client->request('GET', $url);
			$this->assertResponseRedirects('/login', null, $url);
		}
	}

	public function testValidatedMemberCanLogIn(): void
	{
		$client = static::createClient();
		$this->login($client, 'b.durand@demo.local');

		$client->request('GET', '/account/');
		$this->assertResponseIsSuccessful();
	}

	public function testMemberAwaitingValidationCannotLogIn(): void
	{
		$client = static::createClient();
		$this->login($client, 'd.moreau@demo.local');

		$this->assertResponseRedirects('/');
		$client->followRedirect();
		$this->assertSelectorTextContains('body', 'en attente de validation');

		$client->request('GET', '/account/');
		$this->assertResponseRedirects('/login');
	}

	public function testMemberCannotOpenTheAdmin(): void
	{
		$client = static::createClient();
		$this->login($client, 'b.durand@demo.local');

		$client->request('GET', '/admin');
		$this->assertResponseStatusCodeSame(403);
	}

	public function testAdminSeesTheValidationQueue(): void
	{
		$client = static::createClient();
		$this->login($client, 'admin@demo.local');

		$client->request('GET', '/admin');
		$this->assertResponseIsSuccessful();
		$this->assertSelectorTextContains('body', 'Moreau David');
		$this->assertSelectorTextContains('body', 'Appel à participation');
	}

	// Logs in through the real form, so the passport checks of CheckVerifiedUserSubscriber run
	private function login(KernelBrowser $client, string $email): void
	{
		$crawler = $client->request('GET', '/login');
		$form = $crawler->selectButton('Connexion')->form([
			'email' => $email,
			'password' => 'demo1234',
		]);
		$client->submit($form);
	}
}
