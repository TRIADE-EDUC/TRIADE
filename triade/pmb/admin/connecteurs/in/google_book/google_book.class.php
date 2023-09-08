<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: google_book.class.php,v 1.9 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/curl.class.php");

class google_book extends connector {
	//Variables internes pour la progression de la récupération des notices
	public $del_old;				//Supression ou non des notices dejà existantes
	
	public $profile;				//Profil Amazon
	public $match;					//Tableau des critères UNIMARC / AMAZON
	public $current_site;			//Site courant du profile (n°)
	public $searchindexes;			//Liste des indexes de recherche possibles pour le site
	public $current_searchindex;	//Numéro de l'index de recherche de la classe
	public $match_index;			//Type de recherche (power ou simple)
	public $types;					//Types de documents pour la conversino des notices
	
	//Résultat de la synchro
	public $error;					//Y-a-t-il eu une erreur	
	public $error_message;			//Si oui, message correspondant
	
    public function __construct($connector_path="") {
    	parent::__construct($connector_path);
    }
    
    public function get_id() {
    	return "google_book";
    }
    
    //Est-ce un entrepot ?
	public function is_repository() {
		return 2;
	}
    
    public function source_get_property_form($source_id) {
		global $charset;
		
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}	
		}
		if (!isset($width))
			$width = "500";
		if (!isset($height))
			$height = "500";
			
		$form="<div class='row'>
				<div class='colonne3'><label for='width'>".$this->msg["gbooks_width"]."</label></div>
				<div class='colonne-suite'><input type='text' name='width' value='".htmlentities($width,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='mdp'>".$this->msg["gbooks_height"]."</label></div>
				<div class='colonne-suite'><input type='text' name='height' value='".htmlentities($height,ENT_QUOTES,$charset)."'/></div>
			</div>";
		return $form;
    }
    
    public function make_serialized_source_properties($source_id) {
    	global $width,$height;
    	$t["width"] = (int) $width;
    	$t["height"] = (int) $height;
    	
    	$this->sources[$source_id]["PARAMETERS"]=serialize($t);
	}
    
    public function make_serialized_properties() {
    	global $accesskey, $secretkey;
		//Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
		$keys = array();
    	
    	$keys['accesskey']=$accesskey;
		$keys['secretkey']=$secretkey;
		$this->parameters = serialize($keys);
	}

	public function enrichment_is_allow(){
		return true;
	}
	
	public function getEnrichmentHeader(){
		$header= array();
		$header[]= "<!-- Script d'enrichissement pour Google Book-->";
		$header[]= "<script type='text/javascript' src='https://www.google.com/jsapi'></script>";
		$header[]= "<script type='text/javascript'>google.load('books','0');</script>";
		return $header;
	}
	
	public function getTypeOfEnrichment($source_id){
		$type['type'] = array(
			"books"
		);		
		$type['source_id'] = $source_id;
		return $type;
	}
	
	public function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array(),$page=1){
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
		
		//on renvoi ce qui est demandé... si on demande rien, on renvoi tout..
		switch ($type){
			case "books" :
			default :
				$rqt="select code from notices where notice_id = '$notice_id'";
				$res=pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($res)){
					$code = pmb_mysql_result($res,0,0);
					$code = preg_replace('/-|\.| /', '', $code);
					if($code && $this->checkIfEmbeddable($code)){
						$enrichment['books']['content'] = "
						<div id='gbook$notice_id' style='width: ".$width."px; height: ".$height."px;margin-bottom:0.5em;'></div>";
						$enrichment['books']['callback'] = "
						var viewer = new google.books.DefaultViewer(document.getElementById('gbook$notice_id'));
						viewer.load('ISBN:$code');";
					}else{
						$enrichment['books']['content'] = "<span>".$this->msg["gbook_no_preview"]."</span>";
					}
				}
				break;
		}		
		$enrichment['source_label']=$this->msg['gbooks_enrichment_source'];
		return $enrichment;
	}
	
	public function checkIfEmbeddable($isbn){
		$identifiers = array();
		$curl = new Curl();
		$xmlToParse = $curl->get("http://www.google.com/books/feeds/volumes?q=ISBN".$isbn);	
		$xml = _parser_text_no_function_($xmlToParse,"FEED");
		if($xml['ENTRY'][0]){
			//on regarde quand meme si on est le bon livre...
			foreach($xml['ENTRY'][0]['DC:IDENTIFIER'] as $identifier){
				if(substr($identifier['value'],0,4) == "ISBN"){
					$identifiers[]=substr($identifier['value'],5);
				}
			}
			//si le feuillatage est disponible...
			if(in_array($isbn,$identifiers) && substr($xml['ENTRY'][0]['GBS:EMBEDDABILITY'][0]['VALUE'],strpos($xml['ENTRY'][0]['GBS:EMBEDDABILITY'][0]['VALUE'],"#")+1) == "embeddable"){
				return true;
			}else return false;
		}
		
	}
}
?>