<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: urlslist.class.php,v 1.9.4.2 2025/04/16 12:16:53 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");


class urlslist extends connector
{
	//Variables internes pour la progression de la récupération des notices
	public $del_old;				//Supression ou non des notices dejà existantes

	public $profile;				//Profil wikipedia
	public $match;					//Tableau des critères wikipedia
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
    	return "urlslist";
    }

    public function source_get_property_form($source_id) {
        global $charset, $libelle, $infobulle, $source_name, $width, $height, $cp_field;
  		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
    	$form ="
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='colonne3'><label for='libelle'>".$this->msg["urlslist_libelle"]."</label></div>
				<div class='colonne-suite'><input type='text' name='libelle' value='".htmlentities($libelle ?? "", ENT_QUOTES, $charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='infobulle'>".$this->msg["urlslist_infobulle"]."</label></div>
				<div class='colonne-suite'><input type='text' name='infobulle' value='".htmlentities($infobulle ?? "", ENT_QUOTES, $charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='source_name'>".$this->msg["urlslist_source_name"]."</label></div>
				<div class='colonne-suite'><input type='text' name='source_name' value='".htmlentities($source_name ?? "", ENT_QUOTES, $charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='width'>".$this->msg["urlslist_width"]."</label></div>
				<div class='colonne-suite'><input type='text' name='width' value='".htmlentities($width ?? "", ENT_QUOTES, $charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='height'>".$this->msg["urlslist_height"]."</label></div>
				<div class='colonne-suite'><input type='text' name='height' value='".htmlentities($height ?? "", ENT_QUOTES, $charset)."'/></div>
			</div>
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='colonne3'><label for='source_name'>".$this->msg["urlslist_source_field"]."</label></div>
				<div class='colonne-suite'>
					<select name='cp_field'>";
    	$query = "select idchamp, titre from notices_custom where type='url'";
    	$result = pmb_mysql_query($query);
    	if(pmb_mysql_num_rows($result)){
    		while($row = pmb_mysql_fetch_object($result)){
    			$form.="
    					<option value='".$row->idchamp."' ".($row->idchamp == $cp_field ? "selected='selected'" : "").">".htmlentities($row->titre,ENT_QUOTES,$charset)."</option>";
    		}
    	}else{
    		$form.="
    					<option value='0'>".$this->msg["urlslist_no_field"]."</option>";
    	}
    	$form.="
    				</select>
				</div>
			</div>
			<div class='row'>&nbsp;</div>";

		return $form;
    }

    public function make_serialized_source_properties($source_id) {
    	global $libelle,$infobulle,$source_name;
    	global $cp_field;
    	global $width,$height;
    	$t=array();
    	$t['libelle'] = $libelle;
    	$t['infobulle'] = $infobulle;
    	$t['source_name'] = $source_name;
    	$t['cp_field'] = $cp_field;
    	$t['width'] = $width;
    	$t['height'] = $height;
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

	public function getEnrichmentHeader($source_id){
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		$header= array();
		$header[] ="
		<script type='text/javascript'>
					function load_urlslist(url,parent){
						var frame = document.getElementById(parent+'_frame');
						if(!frame){
							var div = document.createElement('div');
							div.setAttribute('id',parent+'_frame');
							var iframe = document.createElement('iframe');
							iframe.setAttribute('src',url);
							iframe.setAttribute('style','margin:5px;width:"."$width"."px;height:".$height."px;');
							div.appendChild(iframe);
							document.getElementById(parent).appendChild(div);
						}else{
							document.getElementById(parent).removeChild(frame);
						}
					}
				</script>
		";
		return $header;
	}

	public function getTypeOfEnrichment($source_id){
	    global $charset, $libelle, $infobulle;

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

	public function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array()){
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
			case str_replace(array(" ","%","-","?","!",";",",",":"),"",strip_empty_chars(strtolower($libelle))) :
			default :
				$enrichment[str_replace(array(" ","%","-","?","!",";",",",":"),"",strip_empty_chars(strtolower($libelle)))]['content'] = $this->urlsInfos($notice_id,$source_id);
				break;
		}
		$enrichment['source_label']= sprintf($this->msg['urlslist_enrichment_source'],$source_name);
		return $enrichment;
	}



	public function urlsInfos($notice_id,$source_id){
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		if($cp_field){
			$query = "select datatype from notices_custom where idchamp = ".$cp_field;
			$datatype = pmb_mysql_result(pmb_mysql_query($query),0,0);
			$query = "select notices_custom_".$datatype." from notices_custom_values where notices_custom_champ = ".$cp_field." and notices_custom_origine= ".$notice_id;
			//return $query;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$html_to_return = "
				<table id='enrichment_urlslist_".$source_id."' class='enrichment_urlslist'>";
				$i=0;
				while($row = pmb_mysql_fetch_row($result)){
					$tab = explode("|",$row[0]);
						$html_to_return.= "
					<tr style='margin:2px;'>
						<td id='urlslist_".$source_id."_".$i."'>
							<a href='#' onclick='load_urlslist(\"".$tab[0]."\",\"urlslist_".$source_id."_".$i."\");return false' >".$tab[1]."</a>
						</td>
					</tr>";
						$i++;
				}
				$html_to_return.= "
				</table>
				";
			}else{
				$html_to_return = $this->msg['urlslist_no_informations'];
			}
		}else{
			$html_to_return = $this->msg['urlslist_no_informations'];
		}
		return $html_to_return;
	}
}
?>