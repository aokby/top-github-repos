<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\GithubSearchService;
use OpenApi\Annotations as OA;
/**
 *
 * default controller for discovering popular repositories on GitHub.
 * 
 */
class DefaultController
{

    /**
     * default action to return empty 200 response
     * it is used to test initial installation only
     * 
     * @return Response empty 200 response
     */
    public function index(): Response {
        return new Response();
    }

    /**
     * List most popular GitHub Repositories.
     *
     * 
     * @OA\Get(
     *   path="/top_repositories",
     *   summary="This call search for popular GitHub Repositories and filter it by language and date.",
     *   operationId="topRepositories",
     *   @OA\Parameter(
     *     name="from",
     *     in="query",
     *     description="The field is a date used to filter repos after this creation date yyyy-mm-dd default 2007-10-19",
     *     @OA\Schema(type="date")
     *   ),
     *   @OA\Parameter(
     *     name="itemsPerPage",
     *     in="query",
     *     description="The field is used to specify the number of needed results for example 100 or 50 or default 10",
     *     @OA\Schema(type="int")
     *   ),
     *   @OA\Parameter(
     *     name="language",
     *     in="query",
     *     description="The field is a string used to filter repos by langauge name for example 'php' default ''",
     *     @OA\Schema(type="string")
     *   ),
     *     @OA\Response(
     *         response="200",
     *         description="The list of matched Github Repositories",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="total_count",
     *                         type="integer",
     *                         description="The count of total matched repos."
     *                     ),
     *                     @OA\Property(
     *                         property="incomplete_results",
     *                         type="boolen",
     *                         description="this shows if GitHub fully searched its data or partially"
     *                     ),
     *                     @OA\Property(
     *                         property="items",
     *                         type="array",
     *                         description="List of the matched repos",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                         "total_count": 12345,
     *                         "incomplete_results": "true",
     *                         "data": {}
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Validation Error - or not allowed request",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="boolen",
     *                         description="success flag."
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="The error message description"
     *                     ),
     *                     example={
     *                         "status": false,
     *                         "message": "invalid request"
     *                     }
     *                 )
     *             )
     *         }
     *     )
     * )
     * 
     * 
     */
    public function topRepositories(Request $request, GithubSearchService $githubSearchService): Response {
        // default filter values
        $from = '2007-10-19'; // first commit on GitHub ;)
        $itemsPerPage = '10';
        $language = '';
        $filterCriteria = $request->query->all();
        // validate date
        if (isset($filterCriteria['from'])) {
            if (!\DateTime::createFromFormat('Y-m-d', $filterCriteria['from'])
                || strtotime(date('Y-m-d')) <= strtotime($filterCriteria['from']) ) {
              return new Response(json_encode(['status' => false, 'message' => 'param \'from\' should be a valid date in the past with format yyyy-mm-dd']), 403);
            }
            $from = $filterCriteria['from'];
        }
        // validate limit
        if (isset($filterCriteria['itemsPerPage'])) {
            if (!is_numeric($filterCriteria['itemsPerPage'])
                || !is_int($filterCriteria['itemsPerPage'] + 0 ) 
                || $filterCriteria['itemsPerPage'] <= 0 
                || $filterCriteria['itemsPerPage'] > 100) {
              return new Response(json_encode(['status' => false, 'message' => 'param \'itemsPerPage\' should be an interger from min 1 to max 100']), 403);
            }
            $itemsPerPage = $filterCriteria['itemsPerPage'];
        }
        // validate language
        if (isset($filterCriteria['language'])) {
            if (!is_string($filterCriteria['language'])) {
              return new Response(json_encode(['status' => false, 'message' => 'param \'language\' should be a string']), 403);
            }
            $language = $filterCriteria['language'];
        }
        // using the service to access the 3rd party API
        $topRepos = $githubSearchService->getGithub($from, $itemsPerPage, $language);
        // check response
        if ($topRepos['status']) {
            $topReposArr = json_decode($topRepos['content'], true);
            if (!json_last_error()) {
                // success
                return new Response($topRepos['content']);
            }
            // if we get unexpected response from the 3rd party api
            return new Response(json_encode(['status' => false, 'message' => 'GitHub Returned unexpected invalid json']), 403);
        }
        // failed
        return new Response(json_encode($topRepos), 403);
    }
}