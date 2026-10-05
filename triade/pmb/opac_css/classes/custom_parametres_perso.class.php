<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_parametres_perso.class.php,v 1.22.4.1 2025/01/28 15:28:30 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/parametres_perso.class.php");

class custom_parametres_perso extends parametres_perso {
	public $num_type;

	public $option_navigation;

	public $custom_prefixe;

	public function  __construct($prefix,$custom_prefixe,$type,$base_url="",$option_navigation=array(), $option_visibilite=array()) {
		global $msg;
		global $aff_list_empr;
		global $aff_list_empr_search;
		global $aff_filter_list_empr;
		global $chk_list_empr;
		global $val_list_empr;
		global $type_list_empr;
		global $options_list_empr;

		$this->option_navigation =$option_navigation;

		if(!count($option_visibilite))
			$this->option_visibilite = array(
				'multiple' => "none",
				'opac_sort' => "none",
				'exclusion' => "none"
			);
		else $this->option_visibilite =$option_visibilite;

		$this->prefix=$prefix;
		$this->custom_prefixe=$custom_prefixe;
		$this->num_type = intval($type);
		$this->base_url=$base_url;

		//Lecture des champs
		$this->no_special_fields=0;
		$this->t_fields=array();
		$requete="select idchamp, name, titre, custom_prefixe, type, datatype, obligatoire, options, multiple, search, export, exclusion_obligatoire, pond, opac_sort from ".$this->prefix."_custom where custom_prefixe = '".$this->custom_prefixe."' and num_type = '".$this->num_type."' order by ordre";

		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)==0)
			$this->no_special_fields=1;
		else {
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$this->t_fields[$r->idchamp]["idchamp"] = $r->idchamp;
				$this->t_fields[$r->idchamp]["DATATYPE"]=$r->datatype;
				$this->t_fields[$r->idchamp]["NAME"]=$r->name;
				$this->t_fields[$r->idchamp]["TITRE"]=$r->titre;
				$this->t_fields[$r->idchamp]["TYPE"]=$r->type;
				$this->t_fields[$r->idchamp]["OPTIONS"]=$r->options;
				$this->t_fields[$r->idchamp]["MANDATORY"]=$r->obligatoire;
				$this->t_fields[$r->idchamp]["OPAC_SHOW"]=$r->multiple;
				$this->t_fields[$r->idchamp]["SEARCH"]=$r->search;
				$this->t_fields[$r->idchamp]["EXPORT"]=$r->export;
				$this->t_fields[$r->idchamp]["EXCLUSION"]=$r->exclusion_obligatoire;
				$this->t_fields[$r->idchamp]["POND"]=$r->pond;
				$this->t_fields[$r->idchamp]["OPAC_SORT"]=$r->opac_sort;
			}
		}
	}

	//Gestion des actions en administration
	public function proceed() {
		global $action;
		global $name,$titre,$type,$datatype,$_options,$multiple,$obligatoire,$search,$export,$exclusion,$ordre,$idchamp,$id,$pond,$opac_sort;

		switch ($action) {
			case "nouv":
				$this->show_edit_form();
				break;
			case "edit":
				$this->show_edit_form($id);
				break;
			case "create":
				$this->check_form();
				$requete="select max(ordre) from ".$this->prefix."_custom where custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type;
				$resultat=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($resultat)!=0)
					$ordre=pmb_mysql_result($resultat,0,0)+1;
				else
					$ordre=1;

				$requete="insert into ".$this->prefix."_custom set custom_prefixe = '".$this->custom_prefixe."', num_type = '".$this->num_type."', name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple=$multiple, obligatoire=$obligatoire, ordre=".intval($ordre).", search=$search, export=$export, exclusion_obligatoire=$exclusion, opac_sort=$opac_sort ";
				pmb_mysql_query($requete);
				echo $this->show_field_list();
				break;
			case "update":
				$this->check_form();
				$requete="update ".$this->prefix."_custom set name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple=$multiple, obligatoire=$obligatoire, ordre=".intval($ordre).", search=$search, export=$export, exclusion_obligatoire=$exclusion, pond=$pond, opac_sort=$opac_sort where idchamp=$idchamp";
				pmb_mysql_query($requete);
				echo $this->show_field_list();
				break;
			case "up":
				$requete="select ordre from ".$this->prefix."_custom where idchamp=$id";
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select max(ordre) as ordre from ".$this->prefix."_custom where ordre<$ordre and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type;
				$resultat=pmb_mysql_query($requete);
				$ordre_max=@pmb_mysql_result($resultat,0,0);
				if ($ordre_max) {
					$requete="select idchamp from ".$this->prefix."_custom where ordre=$ordre_max and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type." limit 1";
					$resultat=pmb_mysql_query($requete);
					$idchamp_max=pmb_mysql_result($resultat,0,0);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre_max."' where idchamp=$id and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type;
					pmb_mysql_query($requete);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre."' where idchamp=".$idchamp_max." and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type;
					pmb_mysql_query($requete);
				}
				echo $this->show_field_list();
				break;
			case "down":
				$requete="select ordre from ".$this->prefix."_custom where idchamp=$id";
				$resultat=pmb_mysql_query($requete);
				$ordre=pmb_mysql_result($resultat,0,0);
				$requete="select min(ordre) as ordre from ".$this->prefix."_custom where ordre>$ordre and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type;
				$resultat=pmb_mysql_query($requete);
				$ordre_min=@pmb_mysql_result($resultat,0,0);
				if ($ordre_min) {
					$requete="select idchamp from ".$this->prefix."_custom where ordre=$ordre_min and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type." limit 1";
					$resultat=pmb_mysql_query($requete);
					$idchamp_min=pmb_mysql_result($resultat,0,0);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre_min."' where idchamp=$id and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type;
					pmb_mysql_query($requete);
					$requete="update ".$this->prefix."_custom set ordre='".$ordre."' where idchamp=".$idchamp_min." and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type;
					pmb_mysql_query($requete);
				}
				echo $this->show_field_list();
				break;
			case "delete":
				$requete="delete from ".$this->prefix."_custom where idchamp=$idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_values where ".$this->prefix."_custom_champ=$idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_lists where ".$this->prefix."_custom_champ=$idchamp";
				pmb_mysql_query($requete);
				echo $this->show_field_list();
				break;
			default:
				echo $this->show_field_list();
		}
	}
	public function get_selector_options($selected=0){
		global $msg,$charset;
		$options = "";

		$options.= "
			<option value='0'".(!$selected ? "selected='selected'" : "").">".$msg['cms_editorial_form_type_field_choice']."</option>";
		foreach($this->t_fields as $id=>$field){
			$options.= "
			<option value='".$id."'".($id==$selected ? "selected='selected'" : "").">".htmlentities($field["TITRE"],ENT_QUOTES,$charset)."</option>";
		}
		return $options;
	}

	//Affichage de l'écran de gestion des paramètres perso (la liste de tous les champs définis)
	public function show_field_list() {
		global $type_list_empr;
		global $datatype_list;
		global $form_list;
		global $msg;
		if($this->option_navigation["msg_title"])
			$res="<h3>".$this->option_navigation["msg_title"]."</h3>";

		$res.="
		<div class='row'>&nbsp;<div>";
		$requete="select idchamp, name, titre, type, datatype, multiple, obligatoire, ordre ,search, export,exclusion_obligatoire, opac_sort from ".$this->prefix."_custom where custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type." order by ordre";
		$resultat=pmb_mysql_query($requete);
		/*if(!$resultat)
		{
			echo "ya pas de res : ".pmb_mysql_num_rows($resultat)."<br />";
		}
		echo "nombre : ".pmb_mysql_num_rows($resultat)."<br />";*/
		if (pmb_mysql_num_rows($resultat)==0) {
			$res.="<br /><br />".$msg["parperso_no_field"]."<br />";
			$form_list=str_replace("!!liste_champs_perso!!",$res,$form_list);
			$form_list=str_replace("!!base_url!!",$this->base_url,$form_list);
		} else {
			$res.="<table style='width:100%'>\n";
			$res.="<tr><th></th><th>".$msg["parperso_field_name"]."</th><th>".$msg["parperso_field_title"]."</th><th>".$msg["parperso_input_type"]."</th><th>".$msg["parperso_data_type"]."</th>";
			if($this->option_visibilite["multiple"] == "block") $res.= "<th>".((strpos($this->prefix,"gestfic")!==false) ? $msg["parperso_fiche_visibility"] : $msg["parperso_opac_visibility"])."</th>" ;
			if($this->option_visibilite["opac_sort"] == "block") $res.= "<th>".$msg["parperso_opac_sort"]."</th>" ;
			if($this->option_visibilite["obligatoire"] == "block") $res.= "<th>".$msg["parperso_mandatory"]."</th>" ;
			if($this->option_visibilite["search"] == "block") $res.= "<th>".$msg["parperso_field_search_tableau"]."</th>" ;
			if($this->option_visibilite["export"] == "block") $res.= "<th>".$msg["parperso_exportable"]."</th>" ;
			if($this->option_visibilite["exclusion"] == "block") $res.= "<th>".$msg["parperso_exclusion_entete"]."</th></tr>\n" ;
			else $res .= "</tr>\n";
			$parity=1;
			$n=0;
			while ($r=pmb_mysql_fetch_object($resultat)) {
				if ($parity % 2) {
					$pair_impair = "even";
				} else {
					$pair_impair = "odd";
				}
				$parity+=1;
				$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
				$action_td=" onmousedown=\"document.location='".$this->base_url."&action=edit&id=$r->idchamp';\" ";
				$res.="<tr class='$pair_impair' style='cursor: pointer' $tr_javascript>";
				$res.="<td>";
				$res.="<input type='button' class='bouton_small' value='-' onClick='document.location=\"".$this->base_url."&action=up&id=".$r->idchamp."\"'/></a><input type='button' class='bouton_small' value='+' onClick='document.location=\"".$this->base_url."&action=down&id=".$r->idchamp."\"'/>";
				$res.="</td>";
				$res.="<td $action_td><b>".$r->name."</b></td><td $action_td>".$r->titre."</td><td $action_td>".$type_list_empr[$r->type]."</td><td $action_td>".$datatype_list[$r->datatype]."</td>";
				if($this->option_visibilite["multiple"] == "block") {
					$res.="<td $action_td>";
					if ($r->multiple==1) $res.=$msg["40"]; else $res.=$msg["39"];
					$res.="</td>";
				}
				if($this->option_visibilite["opac_sort"] == "block") {
					$res.="<td $action_td>";
					if ($r->opac_sort==1) $res.=$msg["40"]; else $res.=$msg["39"];
					$res.="</td>";
				}
				if($this->option_visibilite["obligatoire"] == "block") {
					$res.="<td $action_td>";
					if ($r->obligatoire==1) $res.=$msg["40"]; else $res.=$msg["39"];
					$res.="</td>";
				}
				if($this->option_visibilite["search"] == "block") {
					$res.="<td $action_td>";
					if ($r->search==1) $res.=$msg["40"]; else $res.=$msg["39"];
					$res.="</td>";
				}
				if($this->option_visibilite["export"] == "block") {
					$res.="<td $action_td>";
					if ($r->export==1) $res.=$msg["40"]; else $res.=$msg["39"];
					$res.="</td>";
				}
				if($this->option_visibilite["exclusion"] == "block"){
					$res.="<td $action_td>";
					if ($r->exclusion_obligatoire==1) $res.=$msg["40"];
					else $res.=$msg["39"];
					$res.="</td>";
				}
				$res.="</tr>\n";
			}
			$res.="</table>";
			$form_list=str_replace("!!liste_champs_perso!!",$res,$form_list);
			$form_list=str_replace("!!base_url!!",$this->base_url,$form_list);
		}
		//ajout d'un bouton retour à la liste...
		if( $this->option_navigation["url_return_list"]){
			$form_list.= "&nbsp;<input type='button' class='bouton' value=' ".$this->option_navigation["msg_return_list"]." ' onclick='document.location=\"".$this->option_navigation["url_return_list"]."\"'/>";
		}
		return $form_list;
	}

	//Récupération des valeurs stockées dans les base pour un emprunteur ou autre
	public function get_out_values($id) {
		//Récupération des valeurs stockées
		if ((!$this->no_special_fields)&&($id)) {
			$this->values = array() ;
			$requete="select ".$this->prefix."_custom_champ,".$this->prefix."_custom_origine,".$this->prefix."_custom_small_text, ".$this->prefix."_custom_text, ".$this->prefix."_custom_integer, ".$this->prefix."_custom_date, ".$this->prefix."_custom_float, ".$this->prefix."_custom_order from ".$this->prefix."_custom_values join ".$this->prefix."_custom on idchamp=".$this->prefix."_custom_champ and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type."  where ".$this->prefix."_custom_origine=".$id." order by ordre";
			$resultat=pmb_mysql_query($requete);
			while ($r=pmb_mysql_fetch_array($resultat)) {
				$this->values[$this->t_fields[$r[$this->prefix."_custom_champ"]]["NAME"]]['label'] = $this->t_fields[$r[$this->prefix."_custom_champ"]]["TITRE"];
				$this->values[$this->t_fields[$r[$this->prefix."_custom_champ"]]["NAME"]]['id'] = $r[$this->prefix."_custom_champ"];
				$format_value=$this->get_formatted_output(array($r[$this->prefix."_custom_".$this->t_fields[$r[$this->prefix."_custom_champ"]]["DATATYPE"]]),$r[$this->prefix."_custom_champ"],true);
				$this->values[$this->t_fields[$r[$this->prefix."_custom_champ"]]["NAME"]]['values'][] = array(
					'value' => $r[$this->prefix."_custom_".$this->t_fields[$r[$this->prefix."_custom_champ"]]["DATATYPE"]],
					'format_value' => 	$format_value,
					'order' => $r[$this->prefix."_custom_order"]
				);
				if(!isset($this->values[$this->t_fields[$r[$this->prefix."_custom_champ"]]["NAME"]]['all_format_values'])) {
					$this->values[$this->t_fields[$r[$this->prefix."_custom_champ"]]["NAME"]]['all_format_values'] = '';
				}
				$this->values[$this->t_fields[$r[$this->prefix."_custom_champ"]]["NAME"]]['all_format_values'].=$format_value.' ';
			}
			$this->sort_out_values();
		} else $this->values=array();
		return $this->values;
	}

	public function delete_all(){
		$query = "select idchamp from ".$this->prefix."_custom where custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			while ($row = pmb_mysql_fetch_object($result)){
				$requete="delete from ".$this->prefix."_custom where idchamp=$row->idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_values where ".$this->prefix."_custom_champ=$row->idchamp";
				pmb_mysql_query($requete);
				$requete="delete from ".$this->prefix."_custom_lists where ".$this->prefix."_custom_champ=$row->idchamp";
				pmb_mysql_query($requete);
			}
		}
	}

	//Suppression de la base des valeurs d'un emprunteur ou autre...
	public function delete_values($id) {
//		on récupère la liste des champs associés...
		$query = "select idchamp from ".$this->prefix."_custom where custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type;
		$result = pmb_mysql_query($query);
		$idchamp = "";
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if($idchamp) $idchamp.=",";
				$idchamp.=$row->idchamp;
			}
		}
		if(!$idchamp) $idchamp="''";

		$requete = "DELETE FROM ".$this->prefix."_custom_values where ".$this->prefix."_custom_champ in (".$idchamp.") and ".$this->prefix."_custom_origine=$id";
		$res = pmb_mysql_query($requete);
	}

	//Récupération des valeurs stockées dans les base pour un emprunteur ou autre
	public function get_values($id) {
		//Récupération des valeurs stockées pour l'emprunteur
		$this->values=$this->list_values=array();

		if ((!$this->no_special_fields)&&($id)) {
			$requete="select ".$this->prefix."_custom_champ,".$this->prefix."_custom_origine,".$this->prefix."_custom_small_text, ".$this->prefix."_custom_text, ".$this->prefix."_custom_integer, ".$this->prefix."_custom_date, ".$this->prefix."_custom_float, ".$this->prefix."_custom_order from ".$this->prefix."_custom_values join ".$this->prefix."_custom on idchamp=".$this->prefix."_custom_champ and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type." where ".$this->prefix."_custom_origine=".$id;
			$resultat=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($resultat)) {
				$values = array();
				while ($r=pmb_mysql_fetch_array($resultat)) {
					$values[$r[$this->prefix."_custom_champ"]][]=array(
						'value' => $r[$this->prefix."_custom_".$this->t_fields[$r[$this->prefix."_custom_champ"]]["DATATYPE"]],
						'format_value' => $this->get_formatted_output(array($r[$this->prefix."_custom_".$this->t_fields[$r[$this->prefix."_custom_champ"]]["DATATYPE"]]),$r[$this->prefix."_custom_champ"],true),
						'order' => $r[$this->prefix."_custom_order"]
					);
					$this->list_values[]=$r[$this->prefix."_custom_".$this->t_fields[$r[$this->prefix."_custom_champ"]]["DATATYPE"]];
				}
				$this->values = $this->sort_values($values);
			}
		}
	}

	public static function get_values_from_name($prefix, $name) {
	    global $charset;

	    $value = array();

	    $query = "SELECT * FROM ".$prefix."_custom WHERE name='".$name."'";
	    $results=pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($results)) {
	        while ($r=pmb_mysql_fetch_object($results)) {
	            $value["ID"]=$r->idchamp;
	            $value["NAME"]=$r->name;
	            $value["PREFIX"]= (!empty($r->custom_prefixe) ? $r->custom_prefixe : $prefix);
	            $value["DATATYPE"]=$r->datatype;
	            $value["TITRE"]=$r->titre;
	            $value["TYPE"]=$r->type;
	            $value["OPTIONS"] =_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$r->options, "OPTIONS");
	            $value["MANDATORY"]=$r->obligatoire;
	            $value["OPAC_SHOW"]=$r->multiple;
	            $value["SEARCH"]=$r->search;
	            $value["EXPORT"]=$r->export;
	            $value["EXCLUSION"]=$r->exclusion_obligatoire;
	            $value["POND"]=$r->pond;
	            $value["OPAC_SORT"]=$r->opac_sort;
	            $value["COMMENT"]=$r->comment;
	        }
	    }

	    return $value;
	}

	//Affichage des champs à saisir dans le formulaire de modification/création d'un emprunteur ou autre
	public function show_editable_fields($id,$type="") {
		global $aff_list_empr,$charset;
		$perso=array();
		//on va chercher les champs génériques
		$generic_type = $this->get_generic_type($type);
		if($generic_type){
			$generic = new cms_editorial_parametres_perso($generic_type,$this->base_url);
			$p = $generic->show_editable_fields($id);
			$perso['FIELDS'] = $p['FIELDS'];
			$perso['CHECK_SCRIPTS'] = $p['CHECK_SCRIPTS'];
		}
		if (!$this->no_special_fields) {
			$this->get_values($id);
			$check_scripts="";
			reset($this->t_fields);
			foreach ($this->t_fields as $key => $val) {
				$t=array();
				$t["NAME"]=$val["NAME"];
				$t["TITRE"]=$val["TITRE"];

				$field=array();
				$field["ID"]=$key;
				$field["NAME"]=$val["NAME"];
				$field["MANDATORY"]=$val["MANDATORY"];
				$field["SEARCH"]=$val["SEARCH"];
				$field["EXPORT"]=$val["EXPORT"];
				$field["EXCLUSION"]=$val["EXCLUSION"];
				$field["OPAC_SORT"]=$val["OPAC_SORT"];
				$field["ALIAS"]=$val["TITRE"];
				$field["DATATYPE"]=$val["DATATYPE"];
				$field["OPTIONS"][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$val["OPTIONS"], "OPTIONS");
				$field["VALUES"]=$this->values[$key];
				$field["PREFIX"]=$this->prefix;
				$field["ID_ORIGINE"]=$id;
				eval("\$aff=".$aff_list_empr[$val["TYPE"]]."(\$field,\$check_scripts);");
				$t["AFF"]=$aff;
				$t["NAME"]=$field["NAME"];
				$perso["FIELDS"][]=$t;
			}

			//Compilation des javascripts de validité renvoyés par les fonctions d'affichage
			$check_scripts="<script>function cancel_submit(message) { alert(message); return false;}\nfunction check_form() {\n".$check_scripts."\nreturn true;\n}\n</script>";
			$perso["CHECK_SCRIPTS"]=$check_scripts;
		} else
			$perso["CHECK_SCRIPTS"]="<script>function check_form() { return true; }</script>";
		return $perso;
	}

	//Enregistrement des champs perso soumis lors de la saisie d'une fichie emprunteur ou autre...
	public function rec_fields_perso($id,$type="") {

		$this->check_submited_fields();
		$requete="delete ".$this->prefix."_custom_values from ".$this->prefix."_custom_values where ".$this->prefix."_custom_origine=$id";
		pmb_mysql_query($requete);

		reset($this->t_fields);
		foreach ($this->t_fields as $key => $val) {
			$name=$val["NAME"];
			global ${$name};
			$value=${$name};
			for ($i=0; $i<count($value); $i++) {
				if ($value[$i]!=="") {
					$requete="insert into ".$this->prefix."_custom_values (".$this->prefix."_custom_champ,".$this->prefix."_custom_origine,".$this->prefix."_custom_".$val["DATATYPE"].",".$this->prefix."_custom_order) values($key,$id,'".$value[$i]."',$i)";
					pmb_mysql_query($requete);
				}
			}
		}
	}

	public function get_generic_type($type){
		$generic_type = 0;
		if($type){
			$query = "select id_editorial_type from cms_editorial_types where editorial_type_element like '".$type."_generic'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$generic_type = $row->id_editorial_type;
			}
		}
		return $generic_type;
	}

	public function get_formatted_output($values, $field_id, $keep_html=false){
		global $val_list_empr, $charset;

		if(!empty($this->t_fields[$field_id])){
    		$field=array();
    		$field["ID"]=$field_id;
    		$field["NAME"]=$this->t_fields[$field_id]["NAME"];
    		$field["MANDATORY"]=$this->t_fields[$field_id]["MANDATORY"];
    		$field["OPAC_SORT"]=$this->t_fields[$field_id]["OPAC_SORT"];
    		$field["ALIAS"]=$this->t_fields[$field_id]["TITRE"];
    		$field["DATATYPE"]=$this->t_fields[$field_id]["DATATYPE"];
    		$field["OPTIONS"][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$this->t_fields[$field_id]["OPTIONS"], "OPTIONS");
    		$field["VALUES"]=$values;
    		$field["PREFIX"]=$this->prefix;
    		$aff = $val_list_empr[$this->t_fields[$field_id]["TYPE"]]($field, $values);
		}
		if (isset($aff)) {
    		if (is_array($aff)) {
    			if ($keep_html) {
    				return $aff['value'];
    			}
    			return $aff['withoutHTML'];
    		}
    		return $aff;
		}
		return '';
	}
}