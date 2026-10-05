<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: es_list.class.php,v 1.13.14.2 2025/05/07 14:25:38 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/curl.class.php");
require_once($include_path."/notice_affichage.inc.php");

class es_list extends connector {

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

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
	public function get_id()
	{
    	return "es_list";
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

		if (!isset($es_selected)) $es_selected = array();
		if (!isset($use_in_a2z)) $use_in_a2z = 0;
		if (!isset($libelle)) $libelle = "External";
		if (!isset($infobulle)) $infobulle = "";
		if (!isset($source_as_origine)) $source_as_origine="";

		$form ="
		<div class='row'>
			<div class='colonne3'><label for='libelle'>".$this->msg["es_list_libelle"]."</label></div>
			<div class='colonne-suite'><input type='text' name='libelle' value='".htmlentities($libelle,ENT_QUOTES,$charset)."'/></div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='infobulle'>".$this->msg["es_list_infobulle"]."</label></div>
			<div class='colonne-suite'><input type='text' name='infobulle' value='".htmlentities($infobulle,ENT_QUOTES,$charset)."'/></div>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='source_as_origine'>".$this->msg["es_list_source_as_origine"]."</label>
			</div>
			<div class='colonne-suite'>
				<input type='radio' name='source_as_origine' value='0'".($source_as_origine==0 ? "checked='checked'" : "")."/>".$this->msg['es_list_source_as_origine_this']."
				<input type='radio' name='source_as_origine' value='1'".($source_as_origine==1 ? "checked='checked'" : "")."/>".$this->msg['es_list_source_as_origine_record']."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='use_in_a2z'>".$this->msg["es_list_use_in_a2z"]."</label>
			</div>
			<div class='colonne-suite'>
				<input type='radio' name='use_in_a2z' value='0'".($use_in_a2z==0 ? "checked='checked'" : "")."/>".$this->msg['no']."
				<input type='radio' name='use_in_a2z' value='1'".($use_in_a2z==1 ? "checked='checked'" : "")."/>".$this->msg['yes']."
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'><label for='es_selected'>".$this->msg["es_list_list"]."</label></div>
			<div class='colonne-suite'>
				<select name='es_selected[]' multiple='yes' size='6' class='saisie-30em'>";


		// on regarde les connecteurs existants !
		$query = "select source_id, name from connectors_sources where id_connector != 'es_list' order by name";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$form.="
					<option value='".htmlentities($row->source_id,ENT_QUOTES,$charset)."'".(in_array($row->source_id,$es_selected) ? " selected='selected'" : "").">".htmlentities($row->name,ENT_QUOTES,$charset)."</option>";
			}
		}
		$form.="
				</select>
			</div>
		</div>";

		return $form;
    }

    public function make_serialized_source_properties($source_id) {
    	global $es_selected;
    	global $use_in_a2z;
    	global $libelle;
    	global $infobulle;
    	global $source_as_origine;
    	$t['es_selected'] = $es_selected;
    	$t['use_in_a2z'] = $use_in_a2z;
    	$t['libelle'] = $libelle;
    	$t['infobulle'] = $infobulle;
    	$t['source_as_origine'] = $source_as_origine;
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
		return $header;
	}

	public function getTypeOfEnrichment($notice_id,$source_id){
	    global $libelle, $infobulle;
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		$type['type'] = array(
			array(
				'code' => str_replace(array(" ","%","-","?","!",";",",",":"),"",strip_empty_chars(strtolower($libelle ?? ""))),
				'label' => $libelle,
				'infobulle' => $infobulle
			)
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
			case "external" :
			default :
				$rqt="select code from notices where notice_id = '$notice_id'";
				$res=pmb_mysql_query($rqt);
				$es_source_id = $source_id;
				$nb_result = 0;
				if(pmb_mysql_num_rows($res)){
					$code = pmb_mysql_result($res,0,0);
					if($code){
						$queries = array();
						for($i=0 ; $i<count($es_selected) ; $i++){
							$queries[] = "select recid,source_id from entrepot_source_".$es_selected[$i]." where (ufield = '011' or ufield ='010')  and usubfield = 'a' and value = '".addslashes($code)."'";
						}
						$query = "select recid,source_id from ((".implode(") union (",$queries).")) as subs";
						$result = pmb_mysql_query($query);
						$nb_result = pmb_mysql_num_rows($result);
						if(pmb_mysql_num_rows($result)){
							while($row = pmb_mysql_fetch_object($result)){
								$es_source_id = $row->source_id;
								$enrichment[$type]['content'].= aff_notice_unimarc($row->recid);
							}
						}else{
							$enrichment['type']['content'] = $query."<span>".$this->msg["es_list_no_preview"]."</span>";
						}
					}else{
						$enrichment[$type]['content'] ="<span>".$this->msg["es_list_no_preview"]."</span>";
					}
				}
				break;
		}
		if($nb_result <= 1){
			switch($source_as_origine){
				//Cette source
				case 0 :
					$enrichment['source_label']=sprintf($this->msg['es_list_enrichment_source'],$params['NAME']);
					break;
				//source de la notice
				case 1 :
					$query="select name from connectors_sources where source_id=".$es_source_id."";
					$result =pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$name = pmb_mysql_result($result,0,0);
					}else{
						$name = $params['NAME'];
					}
					$enrichment['source_label']=sprintf($this->msg['es_list_enrichment_source'],$name);
					break;
			}
		}else{
			$enrichment['source_label']=sprintf($this->msg['es_list_enrichment_source'],$params['NAME']);
		}

		return $enrichment;
	}
}
?>