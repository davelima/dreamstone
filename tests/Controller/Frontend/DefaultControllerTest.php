<?php

namespace App\Tests\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Default Controller feature test
 */
class DefaultControllerTest extends WebTestCase
{
    public function testShowInitialPage()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Couldn't load Home Page");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Dreamstone")')->count(), "Couldn't find Dreamstone text");
    }
}