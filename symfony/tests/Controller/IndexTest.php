<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * 
 */

class IndexTest extends WebTestCase {
    /**
     * [testIndex description]
     */
    public function testIndex(): void {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}