<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dailymotion.class.php,v 1.11.4.2 2025/05/07 14:25:38 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");

require_once("sdk/Dailymotion.php");

class dailymotion extends connector {

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
    	return "dailymotion";
    }

    public function source_get_property_form($source_id) {
		global $charset;
		global $pmb_url_base;
		global $code;

//		$params=$this->get_source_params($source_id);
//		if ($params["PARAMETERS"]) {
//			//Affichage du formulaire avec $params["PARAMETERS"]
//			$vars=unserialize($params["PARAMETERS"]);
//			foreach ($vars as $key=>$val) {
//				global ${$key};
//				${$key}=$val;
//			}
//		}
//
//  	  if($source_id!=0){
//			$url = $pmb_url_base."admin.php?categ=connecteurs&sub=in&act=add_source&id=17&source_id=".$source_id;
//		}else{
//			$url = $this->msg['dailymotion_no_source'];
//		}
//
//
//		$form="<div class='row'>
//				<div class='colonne3'><label for='api_key'>".$this->msg["dailymotion_api_key"]."</label></div>
//				<div class='colonne-suite'><input type='text' name='api_key' value='".htmlentities($api_key,ENT_QUOTES,$charset)."'/></div>
//			</div>
//			<div class='row'>
//				<div class='colonne3'><label for='secret_key'>".$this->msg["dailymotion_secret_key"]."</label></div>
//				<div class='colonne-suite'><input type='text' name='secret_key' value='".htmlentities($secret_key,ENT_QUOTES,$charset)."'/></div>
//			</div>
//		<div class='row'>&nbsp;</div>
//		<div class='row'>
//			<div class='colonne3'>
//				<label for='callback_url'>".$this->msg["dailymotion_callback_url"]."</label>
//			</div>
//			<div class='colonne_suite'>
//				<span>".$url."</span>
//			</div>
//		</div>
//		<div class='row'>
//			<div class='colonne3'>
//				<label for='code'>".$this->msg["dailymotion_code"]."</label>
//			</div>
//			<div class='colonne_suite'>";
//		if($code != ""){
//			$form.="
//				<span>".$this->msg['dailymotion_ws_allow_in_progress']."</span>
//				<input type='hidden' name='code_saved' value='".$code."'/>";
//		}else if($code_saved!=""){
//			$form.="
//				<span>".$this->msg['dailymotion_ws_allowed']."</span>
//				<input type='hidden' name='code_saved' value='".$code_saved."'/>";
//		}else if($api_key != ""){
//			$form.="
//				<a href='https://api.dailymotion.com/oauth/authorize?response_type=code&client_id=".$api_key."&redirect_uri=".rawurlencode($url)."' >".$this->msg['dailymotion_link_allow_ws']."</a>";
//		}else{
//			$form.="
//				<span>".$this->msg['dailymotion_allow_need_api_key']."</span>";
//		}
//		$form.="
//			</div>
//		</div>
//		<div class='row'>&nbsp;</div>";
//		return $form;
		return "";
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

	public function getEnrichmentHeader($source_id){
		$header= array();
		$header[]= "<!-- Script d'enrichissement pour Dailymotion-->";
		return $header;
	}

	public function getTypeOfEnrichment($notice_id,$source_id){
		$type['type'] = array(
			array(
				'code' => "dailymotion",
				'label' => $this->msg['dailymotion']
			)

		);
		$type['source_id'] = $source_id;
		return $type;
	}

	public function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array(),$page=1){
		global $lang;

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
			case "dailymotion" :
				$api = new Dailymotion_api();
				$vars = array(
					'search' => encoding_normalize::utf8_normalize($infos['title']." ".$infos['author']),
					'language' => substr($lang,0,2),
					'fields' => array(
						'embed_html'
					)
				);
				$result = $api->call('/videos', $vars);

				if($result['has_more'] == 1){
					$aff_result = sprintf($this->msg['dailymotion_partial_results'],$result['limit'],$result['total']);
					$aff_result.= "<br/>
					<a target='_blank' href='http://www.dailymotion.com/relevance/search/".$vars['search']."'>".$this->msg['dailymotion_go_to_result_page']."</a>";
				}else{
					$aff_result = sprintf($this->msg['dailymotion_all_results'],$result['total']);
				}
				$enrichment['dailymotion']['content']= "<p style='padding:10px;'>".$aff_result."</p>";
				foreach($result['list'] as $elem){
					$enrichment['dailymotion']['content'].= "<span style='margin-right : 4px;'>".$elem['embed_html']."</span>";
				}
				break;
		}
		$enrichment['source_label']=$this->msg['dailymotion_enrichment_source'];
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
			//$infos['author'] = $author->display;
			$infos['author'] = ($author->rejete!= ""? $author->rejete." ":"").$author->name;
		}
		return $infos;
	}
}

?>