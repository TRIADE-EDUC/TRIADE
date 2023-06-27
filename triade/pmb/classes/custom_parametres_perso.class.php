<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_parametres_perso.class.php,v 1.36 2019-06-10 08:57:11 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/translation.class.php");

class custom_parametres_perso extends parametres_perso {
	public $num_type;
	protected static $definitions = array();
	protected static $out_values = array();
	
	public function  __construct($prefix,$custom_prefixe,$type,$base_url="",$option_navigation=array(), $option_visibilite=array()) {
		global $msg;
		global $aff_list_empr;
		global $aff_list_empr_search;
		global $aff_filter_list_empr;
		global $chk_list_empr;
		global $val_list_empr;
		global $type_list_empr;
		global $options_list_empr;
		global $charset;
		
		$this->set_option_navigation($option_navigation);
		$this->set_option_visibilite($option_visibilite);
		$this->set_base_url($base_url);

		$this->prefix=$prefix;
		$this->custom_prefixe=$custom_prefixe;
		$this->num_type = $type*1;

		
		if(!isset(self::$definitions[$prefix.'_'.$custom_prefixe.'_'.$type])){
			//Lecture des champs
			$this->no_special_fields=0;
			$this->t_fields=array();
			$requete="select idchamp, name, titre, custom_prefixe, type, datatype, obligatoire, options, multiple, search, export, exclusion_obligatoire, pond, opac_sort, comment from ".$this->prefix."_custom where custom_prefixe = '".$this->custom_prefixe."' and num_type = '".$this->num_type."' order by ordre";
	
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)==0)
				$this->no_special_fields=1;
			else {
				while ($r=pmb_mysql_fetch_object($resultat)) {
					$this->t_fields[$r->idchamp]["DATATYPE"]=$r->datatype;
					$this->t_fields[$r->idchamp]["NAME"]=$r->name;
					$this->t_fields[$r->idchamp]["TITRE"]=$r->titre;
					$this->t_fields[$r->idchamp]["TYPE"]=$r->type;
					$this->t_fields[$r->idchamp]["OPTIONS"][0] =_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$r->options, "OPTIONS");
					$this->t_fields[$r->idchamp]["MANDATORY"]=$r->obligatoire;
					$this->t_fields[$r->idchamp]["OPAC_SHOW"]=$r->multiple;
					$this->t_fields[$r->idchamp]["SEARCH"]=$r->search;
					$this->t_fields[$r->idchamp]["EXPORT"]=$r->export;
					$this->t_fields[$r->idchamp]["EXCLUSION"]=$r->exclusion_obligatoire;
					$this->t_fields[$r->idchamp]["POND"]=$r->pond;
					$this->t_fields[$r->idchamp]["OPAC_SORT"]=$r->opac_sort;
					$this->t_fields[$r->idchamp]["COMMENT"]=$r->comment;
				}
			}
			self::$definitions[$prefix.'_'.$custom_prefixe.'_'.$type]['no_fields'] = $this->no_special_fields;
			self::$definitions[$prefix.'_'.$custom_prefixe.'_'.$type]['t_fields'] = $this->t_fields;
		}else{
			$this->no_special_fields = self::$definitions[$prefix.'_'.$custom_prefixe.'_'.$type]['no_fields'];
			$this->t_fields = self::$definitions[$prefix.'_'.$custom_prefixe.'_'.$type]['t_fields'];
		}
	}	
	
	public function set_base_url($base_url){
		$this->base_url = $base_url;
	}
	
	public function set_option_navigation($option_navigation){
		$this->option_navigation =$option_navigation;
	}
	public function set_option_visibilite($option_visibilite){
		if(empty($option_visibilite))
			$this->option_visibilite = array(
					'multiple' => "none",
					'opac_sort' => "none",
					'exclusion' => "none"
			);
		else $this->option_visibilite =$option_visibilite;
	}
	
	//Gestion des actions en administration
	public function proceed() {
		global $action;
		global $name,$titre,$type,$datatype,$_options,$multiple,$obligatoire,$search,$export,$exclusion,$ordre,$idchamp,$id,$pond,$opac_sort,$comment,$classement;
		
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
	
				$requete="insert into ".$this->prefix."_custom set custom_prefixe = '".$this->custom_prefixe."', num_type = '".$this->num_type."', name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple=$multiple, obligatoire=$obligatoire, ordre=$ordre, search=$search, export=$export, exclusion_obligatoire=$exclusion, opac_sort=$opac_sort, comment='".$comment."', custom_classement='".$classement."' ";
				pmb_mysql_query($requete);
				$idchamp = pmb_mysql_insert_id();
				$translation = new translation($idchamp, $this->prefix."_custom");
				$translation->update("titre");
				echo $this->show_field_list();
				break;
			case "update":
				$this->check_form();
				$requete="update ".$this->prefix."_custom set name='$name', titre='$titre', type='$type', datatype='$datatype', options='$_options', multiple=$multiple, obligatoire=$obligatoire, ordre=$ordre, search=$search, export=$export, exclusion_obligatoire=$exclusion, pond=$pond, opac_sort=$opac_sort, comment='".$comment."', custom_classement='".$classement."' where idchamp=$idchamp";
				pmb_mysql_query($requete);
				$translation = new translation($idchamp, $this->prefix."_custom");
				$translation->update("titre");
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
				translation::delete($idchamp, $this->prefix."_custom", "titre");			
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
			<option value='".$id."'".($id==$selected ? "selected='selected'" : "").">".htmlentities($field["TITRE"],ENT_QUOTES,$charset)." : ".htmlentities($field["NAME"],ENT_QUOTES,$charset)."</option>";	
		}
		return $options;
	}
	public function get_selector_options_1($selected=0){
		global $msg,$charset;
		$options = "";
	
		$options.= "
		<option value=''>".$msg['cms_editorial_form_type_field_choice']."</option>";
		foreach($this->t_fields as $id=>$field){
			$options.= "
			<option value='{{".$field["NAME"].".values.0.format_value}}'>".htmlentities($field["TITRE"],ENT_QUOTES,$charset)." : ".htmlentities($field["NAME"],ENT_QUOTES,$charset)."</option>";
		}
		return $options;
	}
		
	//Affichage de l'écran de gestion des paramètres perso (la liste de tous les champs définis)
	public function show_field_list() {
		global $msg;
		
		$display = '';
		if($this->option_navigation["msg_title"])
			$display .= "<h3>".$this->option_navigation["msg_title"]."</h3>";
		
		$this->load_class('/list/custom_fields/list_custom_fields_custom_ui.class.php');
		list_custom_fields_custom_ui::set_prefix($this->prefix);
		list_custom_fields_custom_ui::set_custom_prefixe($this->custom_prefixe);
		list_custom_fields_custom_ui::set_num_type($this->num_type);
		list_custom_fields_custom_ui::set_option_visibilite($this->option_visibilite);
		$list_custom_fields_custom_ui = new list_custom_fields_custom_ui();
		$display .= $list_custom_fields_custom_ui->get_display_list();

		$display .= "<br />";
		//ajout d'un bouton retour à la liste...
		if( $this->option_navigation["url_return_list"]){		
			$display .= "&nbsp;<input type='button' class='bouton' value=' ".$this->option_navigation["msg_return_list"]." ' onclick='document.location=\"".$this->option_navigation["url_return_list"]."\"'/>";
		}
		//ajout d'un bouton indexer
		if( $this->option_navigation["url_update_global_index"]){		
			$display .= "&nbsp;<input type='button' class='bouton' value=' ".$this->option_navigation["msg_update_global_index"]." ' onclick='document.location=\"".$this->option_navigation["url_update_global_index"]."\"'/>";
		}
		return $display;
	}
	
	//Récupération des valeurs stockées dans les base pour un emprunteur ou autre
	public function get_out_values($id) {
		//Récupération des valeurs stockées 
		if(!isset(self::$out_values[$id])){
			if ((!$this->no_special_fields)&&($id)) {
				$this->values = array() ;
				$requete="select ".$this->prefix."_custom_champ,".$this->prefix."_custom_origine,".$this->prefix."_custom_small_text, ".$this->prefix."_custom_text, ".$this->prefix."_custom_integer, ".$this->prefix."_custom_date, ".$this->prefix."_custom_float, ".$this->prefix."_custom_order from ".$this->prefix."_custom_values join ".$this->prefix."_custom on idchamp=".$this->prefix."_custom_champ and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type."  where ".$this->prefix."_custom_origine=".$id;
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
 			self::$out_values[$id] = $this->values;
		}else {
			$this->values = self::$out_values[$id];
		}
		return self::$out_values[$id];
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
		$query = "delete from ".$this->prefix."_custom where custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type;
		$result = pmb_mysql_query($query);		
		
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
			$requete="select ".$this->prefix."_custom_champ,".$this->prefix."_custom_origine,".$this->prefix."_custom_small_text, ".$this->prefix."_custom_text, ".$this->prefix."_custom_integer, ".$this->prefix."_custom_date, ".$this->prefix."_custom_float, ".$this->prefix."_custom_order from ".$this->prefix."_custom_values 
					join ".$this->prefix."_custom on idchamp=".$this->prefix."_custom_champ and custom_prefixe = '".$this->custom_prefixe."' and num_type = ".$this->num_type." where ".$this->prefix."_custom_origine=" . $id ." order by ordre ";
			$resultat=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($resultat)) {
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
	
	//Affichage des champs à saisir dans le formulaire de modification/création d'un emprunteur ou autre
	public function show_editable_fields($id,$type="") {
		global $aff_list_empr,$charset;
		$perso=array();

		if (!$this->no_special_fields) {
			$this->get_values($id);
			$check_scripts="";
			reset($this->t_fields);
			foreach ($this->t_fields as $key => $val) {
				if(!isset($this->values[$key])) $this->values[$key] = array();
				$t=array();
				$t["ID"]=$key;
				$t["NAME"]=$val["NAME"];
				$t["TITRE"]=$val["TITRE"];
				$t["COMMENT"]=$val["COMMENT"];
				if($t["COMMENT"]){
					$t["COMMENT_DISPLAY"]="&nbsp;<span class='pperso_comment' title='".htmlentities($t["COMMENT"],ENT_QUOTES, $charset)."' >".nl2br(htmlentities($t["COMMENT"],ENT_QUOTES, $charset))."</span>";
				} else {
					$t["COMMENT_DISPLAY"]="";
				}	
				$field=array();
				$field["ID"]=$key;
				$field["NAME"]=$this->t_fields[$key]["NAME"];
				$field["COMMENT"]=$this->t_fields[$key]["COMMENT"];
				$field["MANDATORY"]=$this->t_fields[$key]["MANDATORY"];
				$field["SEARCH"]=$this->t_fields[$key]["SEARCH"];
				$field["EXPORT"]=$this->t_fields[$key]["EXPORT"];
				$field["EXCLUSION"]=$this->t_fields[$key]["EXCLUSION"];
				$field["OPAC_SORT"]=$this->t_fields[$key]["OPAC_SORT"];
				$field["ALIAS"]=$this->t_fields[$key]["TITRE"];
				$field["DATATYPE"]=$this->t_fields[$key]["DATATYPE"];
				$field["OPTIONS"]=$this->t_fields[$key]["OPTIONS"];
				$field["VALUES"]=$this->values[$key];
				$field["PREFIX"]=$this->prefix;
				$field["ID_ORIGINE"]=$id;
				eval("\$aff=".$aff_list_empr[$this->t_fields[$key]['TYPE']]."(\$field,\$check_scripts);");
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
	function rec_fields_perso($id,$type="") {
		
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
					if ($this->t_fields[$key]["TYPE"] == 'date_flot') {
						$interval = explode("|||", $value[$i]);
						$date_type = $interval[0];
						$date_start = detectFormatDate($interval[1], 'min');
						$date_end = detectFormatDate($interval[2], 'max');	
						if ($date_start == '0000-00-00') $date_start = '';
						if ($date_end == '0000-00-00') $date_end = '';
						if ($date_start || $date_end) {
							if (!$date_end) {
								$date_end = detectFormatDate($interval[1], 'max');	
							}
							$requete = "insert into ".$this->prefix."_custom_dates (".$this->prefix."_custom_champ,".$this->prefix."_custom_origine,
									".$this->prefix."_custom_date_type,".$this->prefix."_custom_date_start,".$this->prefix."_custom_date_end,".$this->prefix."_custom_order) 
									values($key,$id,$date_type,'".$date_start."','".$date_end."',$i)";			
							pmb_mysql_query($requete);
						}
					}
				}
			}
		}
	}
	
	function check_mandatory_fields_value() {
		$error_list = array();
		reset($this->t_fields);
		foreach ($this->t_fields as $key => $val) {
			$name=$val["NAME"];
			global ${$name};
			$value=${$name};
			$flag_value=false;
			for ($i=0; $i<count($value); $i++) {
				if ($value[$i]!=="") {
					$flag_value=true;
				}
			}
			if($val["MANDATORY"] && !$flag_value){
				$error_list[]=array(					
					'field' => $val,					
					'id' => $key,						
				);
			}
		}
		return $error_list;
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
    		$field["COMMENT"]=$this->t_fields[$field_id]["COMMENT"];
    		$field["MANDATORY"]=$this->t_fields[$field_id]["MANDATORY"];
    		$field["OPAC_SORT"]=$this->t_fields[$field_id]["OPAC_SORT"];
    		$field["ALIAS"]=$this->t_fields[$field_id]["TITRE"];
    		$field["DATATYPE"]=$this->t_fields[$field_id]["DATATYPE"];
    		$field["OPTIONS"]=$this->t_fields[$field_id]["OPTIONS"];
    		$field["VALUES"]=$values;
    		$field["PREFIX"]=$this->prefix;
    		$aff=$val_list_empr[$this->t_fields[$field_id]["TYPE"]]($field,$values);
		}
		if (isset($aff)) {
		    if (is_array($aff)) {		
    		    if ($keep_html) {
    		        return $aff['value'];
    		    } else {
    		        return $aff['withoutHTML'];
    		    }
            } else return $aff;
		}
		return '';
	}
}