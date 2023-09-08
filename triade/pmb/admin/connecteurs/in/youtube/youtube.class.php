<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: youtube.class.php,v 1.8 2017-07-12 15:15:01 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");

require_once("youtube_api.class.php");

class youtube extends connector {
	
    public function __construct($connector_path="") {
    	parent::__construct($connector_path);
    }
    
    public function get_id() {
    	return "youtube";
    }
    
    //Est-ce un entrepot ?
	public function is_repository() {
		return 2;
	}

	public function source_get_property_form($source_id) {
		global $charset;
		 
		$params = $this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars = unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key} = $val;
			}
		}
		//developer_key see https://cloud.google.com/console
		if (!isset($developer_key)) {
			$developer_key = '';
		}
		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label for='developer_key'>".$this->msg["youtube_google_developer_key"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='developer_key' id='developer_key' class='saisie-30em' value='".htmlentities($developer_key,ENT_QUOTES,$charset)."'/>
			</div>
		</div>
		<div class='row'></div>";	
		return $form;
	}
	
    public function make_serialized_source_properties($source_id) {
    	global $developer_key;
    	
    	$t = array();
    	$t["developer_key"] = stripslashes($developer_key);
		$this->sources[$source_id]["PARAMETERS"] = serialize($t);
	}
			
	public function enrichment_is_allow(){
		return true;
	}
	
	public function getEnrichmentHeader(){
		$header= array();
		$header[]= "<!-- Script d'enrichissement pour Youtube-->";
		return $header;
	}
	
	public function getTypeOfEnrichment($source_id){
		$type['type'] = array(
			array(
				'code' => "youtube",
				'label' => $this->msg['youtube']
			)
			
		);		
		$type['source_id'] = $source_id;
		return $type;
	}
	
	public function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array(),$page=1){
		global $lang, $charset;
		
		$this->noticeToEnrich= $notice_id;
	
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		$enrichment= array();
	
		$infos = $this->get_notice_infos();
		//on renvoi ce qui est demandé... si on demande rien, on renvoi tout..
		switch ($type){
			case "youtube" :
				$api = new youtube_api();
				$vars ['q'] = $infos['title']." ".$infos['author'];
				if($charset != 'utf-8') $vars ['q'] = utf8_encode($vars ['q']);
				$result = $api->search_videos($vars);
				
				if($result['pageInfo']['resultsPerPage'] < $result['pageInfo']['totalResults']){
					$aff_result = sprintf($this->msg['youtube_partial_results'],$result['pageInfo']['resultsPerPage'],$result['pageInfo']['totalResults']);
					$aff_result.= "<br/>
					<a target='_blank' href='http://www.youtube.com/results?search_query=".$vars['q']."'>".$this->msg['youtube_go_to_result_page']."</a>";
				}else{
					$aff_result = sprintf($this->msg['youtube_all_results'],$result['pageInfo']['totalResults']);
				}
				$enrichment['youtube']['content']= "<p style='padding:10px;'>".$aff_result."</p>";
				
				foreach ($result['items'] as $searchResult) {
					$enrichment['youtube']['content'].= "
						<span style='margin-right : 4px;'>";
					switch ($searchResult['id']['kind']) {
						case 'youtube#video':
							$enrichment['youtube']['content'].= sprintf('<li>%s (%s)</li>',
							$searchResult['snippet']['title'], $searchResult['id']['videoId'])."
							<iframe style='width:480px;height:360px;' src='https://www.youtube.com/embed/".$searchResult['id']['videoId']."' frameborder='0' allowfullscreen></iframe>";
							break;
						case 'youtube#channel':
							$enrichment['youtube']['content'].= sprintf('<li>%s (%s)</li>',
							$searchResult['snippet']['title'], $searchResult['id']['channelId'])."
							<iframe style='width:480px;height:360px;' src='https://www.youtube.com/embed/".$searchResult['id']['channelId']."' frameborder='0' allowfullscreen></iframe>";
							break;
						case 'youtube#playlist':
							$playlists .= sprintf('<li>%s (%s)</li>',
							$searchResult['snippet']['title'], $searchResult['id']['playlistId'])."
							<iframe style='width:480px;height:360px;' src='https://www.youtube.com/embed/".$searchResult['id']['playlistId']."' frameborder='0' allowfullscreen></iframe>";
							break;
					}
					$enrichment['youtube']['content'].= "
						</span>";
				}
				break;
		}
		$enrichment['source_label']=$this->msg['youtube_enrichment_source'];
		return $enrichment;
	}
	
	public function get_notice_infos(){
		$infos = array();
		//on va chercher le titre de la notice...
		$query = "select tit1 from notices where notice_id = ".$this->noticeToEnrich;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$infos['title'] = pmb_mysql_result($result,0,0);
		}
		//on va chercher l'auteur principal...
		$query = "select responsability_author from responsability where responsability_notice =".$this->noticeToEnrich." and responsability_type=0";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$author_id = pmb_mysql_result($result,0,0);
			$author = new auteur($author_id);
			$infos['author'] = ($author->rejete!= ""? $author->rejete." ":"").$author->name;
		}
		return $infos;
	}
}

?>