<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: youtube_api.class.php,v 1.4.12.1 2025/03/28 12:57:10 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

use Google\Service\YouTube;

class youtube_api
{

    protected $max_results = 10;

    public function search_videos($vars)
    {
        $searchResponse = [
            'pageInfo' => [
                'resultsPerPage' => 0,
                'totalResults' => 0,
            ],
            'items' => [],
        ];

        if (empty($vars['developer_key'])) {
            return $searchResponse;
        }
        if (empty($vars['max-results'])) {
            $vars['max-results'] = $this->max_results;
        }
        try {
            $client = new Google_Client();
            $client->setDeveloperKey($vars['developer_key']);
            $youtube = new Google\Service\YouTube($client);
            $searchResponse = $youtube->search->listSearch('id,snippet', array(
                'q' => $vars['q'],
                'maxResults' => $vars['max-results']
            ));
        } catch(Exception $e) {
        }
        return $searchResponse;
    }
}