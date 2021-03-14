<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * class for testing search popular github repositories filtered by language or created date with limits
 */

class TopRepoSearchTest extends WebTestCase {
    /**
     * testTopRepoSearch void to test basic searching the github repositories
     */
    public function testTopRepoSearch(): void {
        $client = static::createClient();
        $client->request('GET', '/top_repositories');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    /**
     * testTopRepoSearchLimits void to test search limits 10, 50, 100
     */
    public function testTopRepoSearchLimits(): void {
        $this->repoSearchLimit(10);
        $this->repoSearchLimit(50);
        $this->repoSearchLimit(100);
    }
    /**
     * _repoSearchLimit private helper void to test specific search limit
     * @param  int    $limit the choosen limit for test
     */
    private function repoSearchLimit(int $limit): void {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', '/top_repositories?itemsPerPage='. $limit);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertLessThanOrEqual($limit, count($result['items']));
    }
    /**
     * testTopRepoSearchFrom void to test searching repos created from specific date
     */
    public function testTopRepoSearchFrom(): void {
        $client = static::createClient();
        $client->request('GET', '/top_repositories?from=2019-01-10');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    /**
     * testTopRepoSearchLanguage void to test searching repos by language
     */
    public function testTopRepoSearchLanguage(): void {
        $client = static::createClient();
        $client->request('GET', '/top_repositories?language=php');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    /**
     * [testTopRepoSearchLimitValidation description]
     */
    public function testTopRepoSearchLimitValidation(): void {
        //return new Response(json_encode(array('code' => 403, 'message' => 'Please enter a valid status')), 403);
        $client = static::createClient();
        // validate that it is not accept an invalid date
        $client->request('GET', '/top_repositories?itemsPerPage=string');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        self::ensureKernelShutdown();
        $client = static::createClient();
        // validate that the limit is in the range
        $client->request('GET', '/top_repositories?itemsPerPage=10000');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
    /**
     * [testTopRepoSearchFromValidation description]
     */
    public function testTopRepoSearchFromValidation(): void {
        $client = static::createClient();
        // validate that from is date
        $client->request('GET', '/top_repositories?from=string');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        self::ensureKernelShutdown();
        $client = static::createClient();
        // validate that from is in the past
        $client->request('GET', '/top_repositories?from='. date("Y-m-d"));
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}