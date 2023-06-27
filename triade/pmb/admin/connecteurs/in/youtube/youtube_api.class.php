<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: youtube_api.class.php,v 1.3 2017-06-13 08:09:35 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/google-api-php-client/vendor/autoload.php");

class youtube_api {
	public $max_results = 10;
	
	public function search_videos($vars) {
		
		if(!$vars['max-results']){
			$vars['max-results'] = $this->max_results;
		}		
		$client = new Google_Client();
		$client->setDeveloperKey($vars['developer_key']);
		$youtube = new Google_Service_YouTube($client);
		$searchResponse = $youtube->search->listSearch('id,snippet', array(
				'q' => $vars['q'],
				'maxResults' => $vars['max-results'],
		));		
		return $searchResponse;
	}
	
}