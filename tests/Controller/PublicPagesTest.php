<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Requires the demo fixtures: php bin/console doctrine:fixtures:load --env=test
 */
class PublicPagesTest extends WebTestCase
{
	/**
	 * @dataProvider publicUrls
	 */
	public function testPublicPageIsReachable(string $url): void
	{
		$client = static::createClient();
		$client->request('GET', $url);

		$this->assertResponseIsSuccessful();
	}

	public function publicUrls(): iterable
	{
		yield 'home' => ['/'];
		yield 'about' => ['/apropos'];
		yield 'news' => ['/news'];
		yield 'events' => ['/events'];
		yield 'documents' => ['/documents'];
		yield 'videos' => ['/videos'];
		yield 'partners' => ['/partners'];
		yield 'calendar' => ['/calendar/'];
		yield 'login' => ['/login'];
		yield 'register' => ['/register'];
		yield 'news article' => ['/news/lancement-officiel-du-projet-sqvald'];
		yield 'event' => ['/event/reunion-de-lancement'];
		yield 'document' => ['/document/rapport-wp2-etat-de-l-art'];
	}

	public function testNewsListShowsOnlyPublishedNews(): void
	{
		$client = static::createClient();
		$client->request('GET', '/news');

		$this->assertSelectorTextContains('body', 'Ouverture de l\'espace membres');
		$this->assertSelectorTextNotContains('body', 'Appel à participation');
	}

	public function testCalendarListsEventsOfTheChosenYear(): void
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/calendar/');
		$form = $crawler->filter('form')->form();
		$form['calendar[year]']->select('2022');
		$client->submit($form);

		$this->assertSelectorTextContains('body', 'Comité de pilotage');
	}
}
