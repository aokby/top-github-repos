<?php

namespace App\Service;

use GuzzleHttp\Client;
/**
 *
 * GithubSearchService service for handling the 3rd party API
 */
class GithubSearchService
{
    /**
     * getGithub search and filter GitHub repositories
     * @param  string      $from         date string to filter by creation date
     * @param  int|integer $itemsPerPage optional limit response records
     * @param  string      $language     optional programming language filter
     * @return array                    ['status'=> boolen, 'message' => string, 'content' => string]
     */
    public function getGithub(string $from, int $itemsPerPage = 10, string $language = ''): array {
        // as a service it is not limited to our controller logic
        // so here we should handle if $from or $language or both exists
        $query = [];
        // preparing query parts
        if (!empty($from)) {
            $query[] = 'created:>'. $from;
        }
        if (!empty($language)) {
            $query[] = 'language:'. $language;
        }
        // handling the 3rd party for unexpected situations
        try {
            $client = new Client(['base_uri' => 'https://api.github.com/search/']);
            $githubRes = $client->request(
                'GET',
                'repositories',
                ['query' => [
                    // compining the query parts
                    'q' => urldecode(implode('+', $query)),
                    'sort' => 'stars',
                    'order' => 'desc',
                    'per_page' => $itemsPerPage
                    ]
                ]
            );
            $topRepos = $githubRes->getBody()->getContents();
            return array('status' => true, 'message' => 'success', 'content' => $topRepos);
        }
        catch  (\GuzzleHttp\Exception\RequestException $ex) {
            return array('status' => false, 'message' => $ex->getMessage(), 'content' => '');
        }
    }
}