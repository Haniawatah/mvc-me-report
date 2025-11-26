<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function testGameLandingPage(): void
    {
        $client = static::createClient();
        // Adjust the URL to match your game landing route
        $client->request('GET', '/game');

        $this->assertResponseIsSuccessful();
        // Check for a specific element that exists on your game page
        $this->assertSelectorExists('h1');
    }

    public function testGameDocPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/game/doc');

        $this->assertResponseIsSuccessful();
    }
}
