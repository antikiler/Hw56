<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChatControllerTest extends WebTestCase
{
    public function testChat()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
    }

    public function testSavemessage()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/send-message');
    }

    public function testGetmessageslist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/get-messages');
    }

}
