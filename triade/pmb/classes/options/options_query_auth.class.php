<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_query_auth.class.php,v 1.2 2021/12/02 16:19:47 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");
require_once("$class_path/authperso.class.php");
require_once("$class_path/onto/common/onto_common_uri.class.php");
require_once("$class_path/onto/onto_store_arc2.class.php");
require_once("$class_path/onto/onto_handler.class.php");
require_once("$class_path/onto/onto_root_ui.class.php");
require_once("$class_path/onto/common/onto_common_ui.class.php");
require_once("$class_path/onto/common/onto_common_controler.class.php");
require_once("$class_path/onto/skos/onto_skos_concept_ui.class.php");
require_once("$class_path/onto/skos/onto_skos_controler.class.php");
require_once("$class_path/onto/onto_param.class.php");

class options_query_auth extends options {
    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters['MULTIPLE'][0]['value'] = '';
		$this->parameters["METHOD"]["0"]["value"] = '';
		$this->parameters["CATEG_SHOW"]["0"]["value"] = '';
		$this->parameters["ID_THES"][0]['value'] = '';
		$this->parameters["DATA_TYPE"]["0"]["value"]= '';
		$this->parameters["ID_SCHEME_CONCEP"][0]['value'] = '';
		$this->parameters["RECIPROC"][0]['value'] = '';
	}
	
	public function get_content_form() {
		global $msg, $charset, $class_path;
    	global $thesaurus_mode_pmb, $deflt_concept_scheme;
    	
    	$method_checked = array();
    	if($this->parameters["METHOD"]["0"]["value"])$method_checked[$this->parameters["METHOD"]["0"]["value"]]="checked";
    	else $method_checked[1]="checked";
    	$data_type_selected = array();
    	$data_type_selected[$this->parameters["DATA_TYPE"]["0"]["value"]]="selected";
    	$categ_show_checked = array();
    	if($this->parameters["CATEG_SHOW"]["0"]["value"])$categ_show_checked[$this->parameters["CATEG_SHOW"]["0"]["value"]]="checked";
    	else $categ_show_checked[0]="checked";
    	
    	$sel_thesaurus = '';
    	$opt_thesaurus = '';
    	if ($thesaurus_mode_pmb != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
    		$liste_thesaurus = thesaurus::getThesaurusList();
    		$sel_thesaurus = "<select class='saisie-20em' id='id_thes' name='ID_THES' >";
    		
    		//si on vient du form de categories, le choix du thesaurus n'est pas possible
    		foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
    			$sel_thesaurus.= "<option value='".$id_thesaurus."' "; ;
    			if ($id_thesaurus == $this->parameters["ID_THES"][0]['value']) $sel_thesaurus.= " selected";
    			$sel_thesaurus.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES,$charset)."</option>";
    		}
    		$sel_thesaurus.= "<option value=0 ";
    		if ($this->parameters["ID_THES"][0]['value'] == 0) $sel_thesaurus.= "selected ";
    		$sel_thesaurus.= ">".htmlentities($msg['thes_all'],ENT_QUOTES, $charset)."</option>";
    		$sel_thesaurus.= "</select>&nbsp;";
    	}
    	$opt_thesaurus = "<input type='radio' name='CATEG_SHOW' value='0' ".(isset($categ_show_checked[0]) ? $categ_show_checked[0] : '').">".$msg["cp_auth_show_all"];
    	$opt_thesaurus .= "<br><input type='radio' name='CATEG_SHOW' value='1' ".(isset($categ_show_checked[1]) ? $categ_show_checked[1] : '').">".$msg["cp_auth_show_last"];
    	
    	$options_authperso='';
    	$authpersos=authpersos::get_authpersos();
    	foreach ($authpersos as $authperso){
    		$options_authperso.="<option value='".($authperso['id'] + 1000)."' ".(isset($data_type_selected[($authperso['id'] + 1000)]) ? $data_type_selected[($authperso['id'] + 1000)] : '')." >".$authperso['name']."</option>";
    	}
    	
    	$onto_store_config = array(
    			/* db */
    			'db_name' => DATA_BASE,
    			'db_user' => USER_NAME,
    			'db_pwd' => USER_PASS,
    			'db_host' => SQL_SERVER,
    			/* store */
    			'store_name' => 'ontology',
    			/* stop after 100 errors */
    			'max_errors' => 100,
    			'store_strip_mb_comp_str' => 0
    	);
    	$data_store_config = array(
    			/* db */
    			'db_name' => DATA_BASE,
    			'db_user' => USER_NAME,
    			'db_pwd' => USER_PASS,
    			'db_host' => SQL_SERVER,
    			/* store */
    			'store_name' => 'rdfstore',
    			/* stop after 100 errors */
    			'max_errors' => 100,
    			'store_strip_mb_comp_str' => 0
    	);
    	
    	$tab_namespaces=array(
    			"skos"	=> "http://www.w3.org/2004/02/skos/core#",
    			"dc"	=> "http://purl.org/dc/elements/1.1",
    			"dct"	=> "http://purl.org/dc/terms/",
    			"owl"	=> "http://www.w3.org/2002/07/owl#",
    			"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
    			"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
    			"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
    			"pmb"	=> "http://www.pmbservices.fr/ontology#"
    	);
    	
    	$onto_handler = new onto_handler($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel','http://www.w3.org/2004/02/skos/core#ConceptScheme');
    	
    	$params=new onto_param();
    	if($this->parameters["DATA_TYPE"]["0"]["value"]==9 && $this->parameters["ID_SCHEME_CONCEP"][0]['value'] !== ''){
    		$params->concept_scheme = array();
    		for($i=0 ; $i<count($this->parameters["ID_SCHEME_CONCEP"]) ; $i++){
    			$params->concept_scheme[]=$this->parameters["ID_SCHEME_CONCEP"][$i]['value'];
    		}
    	}else{
    		$params->concept_scheme=[$deflt_concept_scheme];
    	}
    	$onto_controler=new onto_skos_controler($onto_handler, $params);
    	
    	$onto_scheme_list_selector=onto_skos_concept_ui::get_scheme_list_selector($onto_controler, $params,true,'','ID_SCHEME_CONCEP','',true);
    	
    	$content_form = "
		<tr>
			<td>".$msg['parperso_include_option_methode']."</td>
			<td>
				<table style='width:100%;vertical-align:center'>
					<tr><td class='center'>".$msg['parperso_include_option_selectors_id']."
					<br />
					<input type='radio' name='METHOD' value='1' ".(isset($method_checked[1]) ? $method_checked[1] : '').">
					</td>
					<td class='center'>".$msg['parperso_include_option_selectors_label']."
					<br />
					<input type='radio' name='METHOD' value='2' ".(isset($method_checked[2]) ? $method_checked[2] : '').">
					</td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>".$msg['include_option_type_donnees']."</td>
			<td>
				<select name='DATA_TYPE' onchange=\"option_data_type_change(this.value);\">
					<option value='1' ".(isset($data_type_selected[1]) ? $data_type_selected[1] : '')." >".$msg['133']."</option>
					<option value='2' ".(isset($data_type_selected[2]) ? $data_type_selected[2] : '')." >".$msg['134']."</option>
					<option value='3' ".(isset($data_type_selected[3]) ? $data_type_selected[3] : '')." >".$msg['135']."</option>
					<option value='4' ".(isset($data_type_selected[4]) ? $data_type_selected[4] : '')." >".$msg['136']."</option>
					<option value='5' ".(isset($data_type_selected[5]) ? $data_type_selected[5] : '')." >".$msg['137']."</option>
					<option value='6' ".(isset($data_type_selected[6]) ? $data_type_selected[6] : '')." >".$msg['333']."</option>
					<option value='7' ".(isset($data_type_selected[7]) ? $data_type_selected[7] : '')." >".$msg['indexint_menu']."</option>
					<option value='8' ".(isset($data_type_selected[8]) ? $data_type_selected[8] : '')." >".$msg['titre_uniforme_search']."</option>
					<option value='9' ".(isset($data_type_selected[9]) ? $data_type_selected[9] : '')." >".$msg['skos_view_concepts_concepts']."</option>
					$options_authperso
				</select>
				<div id='thesaurus_part'>
					$sel_thesaurus
					<br>$opt_thesaurus
					<br>
				</div>
				<div id='onto_scheme_part'>
					$onto_scheme_list_selector
				</div>
			</td>	
		</tr>
		".$this->get_line_content_form($msg['procs_options_liste_multi'], 'MULTIPLE', 'checkbox', 'yes');
		$content_form .= $this->get_line_content_form($msg['indexation_reciproque_option'], 'RECIPROC', 'checkbox', 'yes');
		
		return $content_form;
    }
    
    public function get_form() {
    	$form = parent::get_form();
    	$form .= "
		<script type'text/javascript'>
			function option_data_type_change(data_type){
				switch(data_type){
					case '2': 
						document.getElementById('onto_scheme_part').style.display = 'none';
						document.getElementById('thesaurus_part').style.display = 'block';
					break;
					case '9': 
						document.getElementById('onto_scheme_part').style.display = 'block';
						document.getElementById('thesaurus_part').style.display = 'none';
					break;
					default:
						document.getElementById('onto_scheme_part').style.display = 'none';
						document.getElementById('thesaurus_part').style.display = 'none';
					break;
				}
			}	
			option_data_type_change('".$this->parameters["DATA_TYPE"]["0"]["value"]."');	
		</script>";
    	return $form;
    }
    
    public function set_parameters_from_form() {
        global $METHOD, $DATA_TYPE, $ID_THES, $CATEG_SHOW, $MULTIPLE, $ID_SCHEME_CONCEP, $RECIPROC;
    	
    	parent::set_parameters_from_form();
    	$this->parameters["METHOD"][0]['value'] = stripslashes($METHOD);
    	$this->parameters["DATA_TYPE"][0]['value'] = $DATA_TYPE;
    	
    	$this->parameters["ID_THES"][0]['value'] = $ID_THES;
    	$this->parameters["CATEG_SHOW"][0]['value'] = $CATEG_SHOW;
    	if ($MULTIPLE=="yes")
    		$this->parameters['MULTIPLE'][0]['value']="yes";
		else
			$this->parameters['MULTIPLE'][0]['value']="no";

		if ($RECIPROC == "yes")
    		$this->parameters['RECIPROC'][0]['value']="yes";
		else
			$this->parameters['RECIPROC'][0]['value']="no";
    			
		for($i=0 ; $i<count($ID_SCHEME_CONCEP) ; $i++){
			$this->parameters["ID_SCHEME_CONCEP"][$i]['value'] = $ID_SCHEME_CONCEP[$i];
		}
    }
}
?>