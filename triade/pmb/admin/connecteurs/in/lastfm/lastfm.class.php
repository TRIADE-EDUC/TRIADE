<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lastfm.class.php,v 1.15.4.3 2025/05/13 14:36:05 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path,$base_path, $include_path;

require_once $class_path."/connecteurs.class.php" ;
require_once $class_path."/curl.class.php" ;

class lastfm extends connector {

	//propriétés internes
	public $api;
	public $enrichpage;	//page d'enrichissement pour enrichissement paginable

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
    	return "lastfm";
    }

    public function source_get_property_form($source_id) {
        global $charset, $api_key, $pmb_url_base, $token, $secret_key, $token_saved;

    	$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		if($source_id!=0){
			$url = $pmb_url_base."admin.php?categ=connecteurs&sub=in&act=add_source&id=15&source_id=".$source_id;
		}else{
			$url = $this->msg['lastfm_no_source'];
		}

		$form="
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='api_key'>".$this->msg["lastfm_api_key"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' class='saisie-50em' name='api_key' value='".$api_key."'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='secret_key'>".$this->msg["lastfm_secret_key"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' class='saisie-50em' name='secret_key' value='".$secret_key."'/>
			</div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='callback_url'>".$this->msg["lastfm_callback_url"]."</label>
			</div>
			<div class='colonne_suite'>
				<span>".$url."</span>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='token'>".$this->msg["lastfm_token"]."</label>
			</div>
			<div class='colonne_suite'>";
		if($token != ""){
			$form.="
				<span>".$this->msg['lastfm_ws_allow_in_progress']."</span>
				<input type='hidden' name='token_saved' value='".$token."'/>";
		}else if($token_saved!=""){
			$form.="
				<span>".$this->msg['lastfm_ws_allowed']."</span>
				<input type='hidden' name='token_saved' value='".$token_saved."'/>";
		}else if($api_key != ""){
			$form.="
				<a href='http://www.last.fm/api/auth/?api_key=".$api_key."'>".$this->msg['lastfm_link_allow_ws']."</a>";
		}else{
			$form.="
				<span>".$this->msg['lastfm_allow_need_api_key']."</span>";
		}
		$form.="
			</div>
		</div>
		<div class='row'>&nbsp;</div>

		<div class='row'>&nbsp;</div>
		";
		return $form;
    }

    public function make_serialized_source_properties($source_id) {
    	global $api_key,$secret_key,$token_saved;
    	$t=array();
  		$t["api_key"]=$api_key;
  		$t["secret_key"]=$secret_key;
  		$t["token_saved"]=$token_saved;
    	$this->sources[$source_id]["PARAMETERS"]=serialize($t);
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::enrichment_is_allow()
	 */
	public function enrichment_is_allow()
	{
	    return connector::ENRICHMENT_YES;
	}

	public function getEnrichmentHeader(){
$header= array();
		$header[]= "<!-- Script d'enrichissement LastFM-->";
		$header[]= "<script type='text/javascript'>
		function switch_lastfm_page(notice_id,type,page,action){
			var pagin= new http_request();
			var content = document.getElementById('div_'+type+notice_id);
			var patience= document.createElement('img');
			patience.setAttribute('src','".get_url_icon('patience.gif')."');
			patience.setAttribute('align','middle');
			patience.setAttribute('id','patience'+notice_id);
			content.innerHTML = '';
			document.getElementById('onglet_'+type+notice_id).appendChild(patience);
			page = page*1;
			switch (action){
				case 'next' :
					page++;
					break;
				case 'previous' :
					page--;
					break;
			}
			pagin.request('./ajax.php?module=ajax&categ=enrichment&action=enrichment&type='+type+'&id='+notice_id+'&enrichPage='+page,false,'',true,gotEnrichment);
		}
		</script>";
		return $header;
	}

	public function getTypeOfEnrichment($source_id){
		$type['type'] = array(
			"bio",
			array(
				"code" => "similar_artists",
				"label" => "Artistes Similaires"
			),
			array(
				"code" => "pictures",
				"label" => "Photos"
			)
		);
		$type['source_id'] = $source_id;
		return $type;
	}

	public function getEnrichment($notice_id,$source_id,$type="",$params=array(),$page=1){
		$enrichment= array();
		$this->enrichPage = $page;
		$this->noticeToEnrich = $notice_id;
		$this->typeOfEnrichment = $type;
		//on renvoi ce qui est demandé... si on demande rien, on renvoi tout..
		switch ($type){
			case "bio" :
				$enrichment['bio']['content'] = $this->get_artist_biography($source_id);
				break;
			case "events" :
				if (method_exists($this, 'get_artist_events')) {
					$enrichment['events']['content'] = $this->get_artist_events($source_id);
				} else {
					$enrichment['events']['content'] = '';
				}
				break;
			case "similar_artists" :
				$enrichment['similar_artists']['content'] = $this->get_similar_artists($source_id);
				break;
			case "pictures" :
				$enrichment['pictures']['content'] = $this->get_pictures($source_id);
				break;
		}
		$enrichment['source_label']=$this->msg['lastfm_enrichment_source'];
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
			$infos['author'] = $author->get_isbd();
		}
		return $infos;
	}


	public function get_artist_biography($source_id){
		$this->init_ws($source_id);
		$bio = $this->api->get_artist_biography();
	//	highlight_string(print_r($bio,true));
		if ($bio['content'] != ""){
			return encoding_normalize::utf8_decode(nl2br($bio['content']));
		}else{
			return $this->msg['lastfm_no_informations'];
		}
	}

	public function get_similar_artists($source_id){
		$this->init_ws($source_id);
		$similar = $this->api->get_similar_artists();
	//	highlight_string(print_r($similar,true));
		$html = "
		<table>";
		for($i=0 ; $i<count($similar) ; $i++){
			if($i%3 == 0){
				$html.="
			<tr>";
			}
			$html.= "
				<td style='text-align:center;'>
					<a href='".$similar[$i]['url']."' target='_blank'>
						<img src='".$similar[$i]['image']['large']."'/><br/>
						<span>".encoding_normalize::utf8_decode($similar[$i]['name'])."</span>
					</a>
				</td>";

			if($i%3 == 2){
				$html.="
			</tr>";
			}
		}
		$html .= "
		</table>";
		return $html;
	}

	public function get_pictures($source_id){
		global $charset;

		$this->init_ws($source_id);
		$pictures = $this->api->get_pictures($this->enrichPage);
		if($pictures['total']>0){
			$html = "
			<table>";
			for($i=0 ; $i<count($pictures['images']) ; $i++){
				if($i%4 == 0){
					$html.="
				<tr>";
				}
				$html.= "
					<td style='text-align:center;'>
						<a href='".$pictures['images'][$i]['url']."' target='_blank' alt='".htmlentities($this->msg['lastfm_see_picture'],ENT_QUOTES,$charset)."' title='".htmlentities($this->msg['lastfm_see_picture'],ENT_QUOTES,$charset)."'>
							<img src='".$pictures['images'][$i]['sizes']['largesquare']['url']."'/>
						</a>
					</td>";

				if($i%4 == 3){
					$html.="
				</tr>";
				}
			}
			$html .= "
			</table>";
			$html.=$this->get_pagin_form($pictures);
		}else{
			$html = $this->msg['lastfm_no_informations'];
		}
		return $html;
	}

	public function init_ws($source_id){

		$api_key = '';
		$secret_key = '';
		$token_saved = '';

		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		$authVars['apiKey'] = $api_key ?? "";
		$authVars['secret'] = $secret_key ?? "";
		$authVars['token'] = $token_saved ?? "";

		$this->api = new lastfm_api($authVars);
		$this->api->set_notice_infos($this->get_notice_infos());
	}

	public function get_pagin_form($infos){
		$current = $infos['page'];
		$ret = "";
		if($current>0){
			$nb_page = ceil($infos['total']/20);
			if($current > 1) $ret .= "<img src='".get_url_icon('prev.png')."' onclick='switch_lastfm_page(\"".$this->noticeToEnrich."\",\"".$this->typeOfEnrichment."\",\"".$current."\",\"previous\");'/>";
			else $ret .= "<img src='".get_url_icon('prev-grey.png')."'/>";
			$ret .="&nbsp;".$current."/$nb_page&nbsp;";
			if($current < $nb_page) $ret .= "<img src='".get_url_icon('next.png')."' onclick='switch_lastfm_page(\"".$this->noticeToEnrich."\",\"".$this->typeOfEnrichment."\",\"".$current."\",\"next\");' style='cursor:pointer;'/>";
			else $ret .= "<img src='".get_url_icon('next-grey.png')."'/>";
			$ret = "<div class='row'><span style='text-align:center'>".$ret."</span></div>";
		}
		return $ret;
	}
}
?>