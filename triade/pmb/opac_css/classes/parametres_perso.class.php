<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: parametres_perso.class.php,v 1.45 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Gestion des champs personalisés simplifiée pour l'OPAC

require_once($include_path."/parser.inc.php");
require_once($include_path."/fields_empr.inc.php");
require_once($include_path."/datatype.inc.php");
require_once($class_path."/translation.class.php");


class parametres_perso {
	
	public $prefix;
	public $no_special_fields;
	public $values;
	
	public static $fields = array();
	public static $st_fields = array();
	protected static $out_values = array();
	
	//Créateur : passer dans $prefix le type de champs persos et dans $base_url l'url a appeller pour les formulaires de gestion	
	public function __construct($prefix) {
		global $_custom_prefixe_, $charset;

		$this->prefix=$prefix;
		$_custom_prefixe_=$prefix;
		
		//Lecture des champs
		$this->no_special_fields=0;
		$this->t_fields=array();
		if(!isset(self::$st_fields[$this->prefix])){
			$requete="select idchamp, name, titre, type, datatype, obligatoire, options, multiple, search, export, opac_sort, comment from ".$this->prefix."_custom order by ordre";
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)==0){
				self::$st_fields[$this->prefix] = false;
			}else {
				while ($r=pmb_mysql_fetch_object($resultat)) {
					self::$st_fields[$this->prefix][$r->idchamp]["idchamp"] = $r->idchamp;
					self::$st_fields[$this->prefix][$r->idchamp]["DATATYPE"]=$r->datatype;
					self::$st_fields[$this->prefix][$r->idchamp]["NAME"]=$r->name;
					self::$st_fields[$this->prefix][$r->idchamp]["TITRE"]= translation::get_text($r->idchamp, $this->prefix."_custom", 'titre',  $r->titre);
					self::$st_fields[$this->prefix][$r->idchamp]["TYPE"]=$r->type;
					self::$st_fields[$this->prefix][$r->idchamp]["OPTIONS"][0]=_parser_text_no_function_("<?xml version='1.0' encoding='".$charset."'?>\n".$r->options, "OPTIONS");
					if(!isset(self::$st_fields[$this->prefix][$r->idchamp]["OPTIONS"][0]["REPEATABLE"][0]["value"])) {
						self::$st_fields[$this->prefix][$r->idchamp]["OPTIONS"][0]["REPEATABLE"][0]["value"] = 0;
					}
					self::$st_fields[$this->prefix][$r->idchamp]["MANDATORY"]=$r->obligatoire;
					self::$st_fields[$this->prefix][$r->idchamp]["OPAC_SHOW"]=$r->multiple;
					self::$st_fields[$this->prefix][$r->idchamp]["SEARCH"]=$r->search;
					self::$st_fields[$this->prefix][$r->idchamp]["EXPORT"]=$r->export;
					self::$st_fields[$this->prefix][$r->idchamp]["OPAC_SORT"]=$r->opac_sort;
					self::$st_fields[$this->prefix][$r->idchamp]["COMMENT"]=$r->comment;
				}
			}
		}
		if(self::$st_fields[$this->prefix] == false){
			$this->no_special_fields=1;
		}else{
			$this->t_fields=self::$st_fields[$this->prefix];
		}
	}
	
	protected function _sort_values_by_format_values($a,$b) {
		if($a['order'] != $b['order']) {
			return ($a['order'] < $b['order']) ? -1 : 1;
		}
		if (strtolower(strip_tags($a['format_value'])) == strtolower(strip_tags($b['format_value']))) {
			return 0;
		}
		return (strtolower(strip_tags($a['format_value'])) < strtolower(strip_tags($b['format_value']))) ? -1 : 1;
	}
	
	protected function sort_values($fields) {
		$values = array();
		foreach ($fields as $field_id=>$field_values) {
			uasort($field_values, array($this, '_sort_values_by_format_values'));
			$values[$field_id] = array();
			foreach ($field_values as $value) {
				$values[$field_id][] = $value['value'];
			}
		}
		return $values;
	}
	
	//Récupération des valeurs stockées dans les base pour un emprunteur ou autre
	public function get_values($id) {
		//Récupération des valeurs stockées
		$this->values=array();
		
		if ((!$this->no_special_fields)&&($id)) {
			$requete="select ".$this->prefix."_custom_champ,".$this->prefix."_custom_origine,".$this->prefix."_custom_small_text, ".$this->prefix."_custom_text, ".$this->prefix."_custom_integer, ".$this->prefix."_custom_date, ".$this->prefix."_custom_float, ".$this->prefix."_custom_order from ".$this->prefix."_custom_values where ".$this->prefix."_custom_origine=".$id;
			$resultat=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($resultat)) {
				while ($r=pmb_mysql_fetch_array($resultat)) {
					$values[$r[$this->prefix."_custom_champ"]][]=array(
						'value' => $r[$this->prefix."_custom_".$this->t_fields[$r[$this->prefix."_custom_champ"]]["DATATYPE"]],
						'format_value' => $this->get_formatted_output(array($r[$this->prefix."_custom_".$this->t_fields[$r[$this->prefix."_custom_champ"]]["DATATYPE"]]),$r[$this->prefix."_custom_champ"],true),
						'order' => $r[$this->prefix."_custom_order"]
					);
				}
				$this->values = $this->sort_values($values);
			}
		}
	}

	//Affichage des champs à saisir dans le formulaire de modification/création d'un emprunteur ou autre
	public function show_editable_fields($id,$from_z3950=false) {
		global $aff_list_empr,$charset;
		$perso=array();
		
		if (!$this->no_special_fields) {
			if(!$from_z3950){
				$this->get_values($id);
			}
			$check_scripts="";
			reset($this->t_fields);
			foreach ($this->t_fields as $key => $val) {
				$t=array();
				$t["NAME"]=$val["NAME"];
				$t["TITRE"]=$val["TITRE"];
			
				$field=array();
				$field["ID"]=$key;
				$field["NAME"]=$this->t_fields[$key]["NAME"];
				$field["MANDATORY"]=$this->t_fields[$key]["MANDATORY"];				
				$field["SEARCH"]=$this->t_fields[$key]["SEARCH"];
				$field["EXPORT"]=$this->t_fields[$key]["EXPORT"];	
				$field["EXCLUSION"]=$this->t_fields[$key]["EXCLUSION"];	
				$field["OPAC_SORT"]=$this->t_fields[$key]["OPAC_SORT"];	
				$field["COMMENT"]=$this->t_fields[$key]["COMMENT"];
				$field["ALIAS"]=$this->t_fields[$key]["TITRE"];
				$field["DATATYPE"]=$this->t_fields[$key]["DATATYPE"];
				$field["OPTIONS"]=$this->t_fields[$key]["OPTIONS"];
				$field["VALUES"]=$this->values[$key];
				$field["PREFIX"]=$this->prefix;
				$field["ID_ORIGINE"]=$id;
				eval("\$aff=".$aff_list_empr[$this->t_fields[$key]['TYPE']]."(\$field,\$check_scripts);");
				$t["AFF"]=$aff;
				$t["NAME"]=$field["NAME"];
				$t["ID"]=$key;
				$perso["FIELDS"][]=$t;
			}
			//Compilation des javascripts de validité renvoyés par les fonctions d'affichage
			$check_scripts="<script>function cancel_submit(message) { alert(message); return false;}\nfunction check_form() {\n".$check_scripts."\nreturn true;\n}\n</script>";
			$perso["CHECK_SCRIPTS"]=$check_scripts;
		} else 
			$perso["CHECK_SCRIPTS"]="<script>function check_form() { return true; }</script>";
		return $perso;
	}
	
	//Affichage des champs en lecture seule pour visualisation d'un fiche emprunteur ou autre...
	public function show_fields($id) {
		global $val_list_empr;
		global $charset, $thesaurus_liste_trad;
		
		$perso=array();
		//Récupération des valeurs stockées pour l'emprunteur
		$this->get_values($id);
		if (!$this->no_special_fields) {
			//Affichage champs persos
			$c=0;
			reset($this->t_fields);
			foreach ($this->t_fields as $key => $val) {
				$t=array();
				$titre = translation::get_text($this->t_fields[$key]['idchamp'], $this->prefix."_custom", 'titre',  $val['TITRE']);
				$t['TITRE']='<b>'.htmlentities($titre,ENT_QUOTES,$charset).' : </b>';
				$t['TITRE_CLEAN']=htmlentities($titre,ENT_QUOTES,$charset);
				$t['OPAC_SHOW']=$val['OPAC_SHOW'];
				if(!isset($this->values[$key])) $this->values[$key] = array();
				if(!isset(static::$fields[$this->prefix][$key])){
					static::$fields[$this->prefix][$key]=array();
					static::$fields[$this->prefix][$key]['ID']=$key;
					static::$fields[$this->prefix][$key]['NAME']=$this->t_fields[$key]['NAME'];
					static::$fields[$this->prefix][$key]['MANDATORY']=$this->t_fields[$key]['MANDATORY'];
					static::$fields[$this->prefix][$key]['SEARCH']=$this->t_fields[$key]['SEARCH'];
					static::$fields[$this->prefix][$key]['OPAC_SORT']=$this->t_fields[$key]['OPAC_SORT'];
					static::$fields[$this->prefix][$key]['COMMENT']=$this->t_fields[$key]['COMMENT'];
					static::$fields[$this->prefix][$key]['ALIAS']=$this->t_fields[$key]['TITRE'];
					static::$fields[$this->prefix][$key]['DATATYPE']=$this->t_fields[$key]['DATATYPE'];
					static::$fields[$this->prefix][$key]['OPTIONS']=$this->t_fields[$key]['OPTIONS'];
					static::$fields[$this->prefix][$key]['VALUES']=$this->values[$key];
					static::$fields[$this->prefix][$key]['PREFIX']=$this->prefix;
				}
				$t['TYPE']=$this->t_fields[$key]['TYPE'];
				$aff=$val_list_empr[$this->t_fields[$key]['TYPE']](static::$fields[$this->prefix][$key],$this->values[$key]);
				if(is_array($aff) && $aff['ishtml'] == true){
				    $t['AFF'] = nl2br($aff['value']);
					if(isset($aff['details'])) {
						$t['DETAILS'] = $aff['details'];
					}
				} else {
				    $t['AFF'] = nl2br(htmlentities($aff,ENT_QUOTES,$charset));
				}
				$t['ID']=static::$fields[$this->prefix][$key]['ID'];
				$t['NAME']=static::$fields[$this->prefix][$key]['NAME'];
				$t['DATATYPE']=static::$fields[$this->prefix][$key]['DATATYPE'];
				$perso['FIELDS'][]=$t;
			}
		}
		return $perso;
	}
	
	public function get_formatted_output($values,$field_id) {
		global $val_list_empr, $charset;
		
		if(!isset(static::$fields[$this->prefix][$field_id])){
		    if(!empty($this->t_fields[$field_id])){
    			static::$fields[$this->prefix][$field_id]=array();
    			static::$fields[$this->prefix][$field_id]["ID"]=$field_id;
    			static::$fields[$this->prefix][$field_id]["NAME"]=$this->t_fields[$field_id]["NAME"];
    			static::$fields[$this->prefix][$field_id]["MANDATORY"]=$this->t_fields[$field_id]["MANDATORY"];
    			static::$fields[$this->prefix][$field_id]["SEARCH"]=$this->t_fields[$field_id]["SEARCH"];
    			static::$fields[$this->prefix][$field_id]["OPAC_SORT"]=$this->t_fields[$field_id]["OPAC_SORT"];
    			static::$fields[$this->prefix][$field_id]["COMMENT"]=$this->t_fields[$field_id]["COMMENT"];
    			static::$fields[$this->prefix][$field_id]["ALIAS"]=$this->t_fields[$field_id]["TITRE"];
    			static::$fields[$this->prefix][$field_id]["DATATYPE"]=$this->t_fields[$field_id]["DATATYPE"];
    			static::$fields[$this->prefix][$field_id]["OPTIONS"]=$this->t_fields[$field_id]["OPTIONS"];
    			static::$fields[$this->prefix][$field_id]["VALUES"]=$values;
    			static::$fields[$this->prefix][$field_id]["PREFIX"]=$this->prefix;
    		}
		}
		if(!empty($this->t_fields[$field_id])){
    		$aff=$val_list_empr[$this->t_fields[$field_id]["TYPE"]](static::$fields[$this->prefix][$field_id],$values);
		}else{
		    $aff='';
		}
		if(is_array($aff)) return $aff['withoutHTML']; 
		else return $aff;
	}

	//Appelé par sort_out_values
	protected function _sort_out_values_by_format_values($a,$b) {
		if($a['order'] != $b['order']) {
			return ($a['order'] < $b['order']) ? -1 : 1;
		}
		if (strtolower(strip_tags($a['format_value'])) == strtolower(strip_tags($b['format_value']))) {
			return 0;
		}
		return (strtolower(strip_tags($a['format_value'])) < strtolower(strip_tags($b['format_value']))) ? -1 : 1;
	}
	
	//Appelé dans get_out_values
	protected function sort_out_values() {
	
		$fields = $this->values;
		foreach ($fields as $name=>$field) {
			uasort($field['values'], array($this, '_sort_out_values_by_format_values'));
			$this->values[$name]['values'] = $field['values'];
		}
	}
	
	//Récupération des valeurs stockées dans les base pour un emprunteur ou autre
	public function get_out_values($id) {
		//Récupération des valeurs stockées 
		if(!isset(self::$out_values[$id])){
			if ((!$this->no_special_fields)&&($id)) {
				$this->values = array() ;
				$requete="select ".$this->prefix."_custom_champ,".$this->prefix."_custom_origine,".$this->prefix."_custom_small_text, ".$this->prefix."_custom_text, ".$this->prefix."_custom_integer, ".$this->prefix."_custom_date, ".$this->prefix."_custom_float, ".$this->prefix."_custom_order from ".$this->prefix."_custom_values join ".$this->prefix."_custom on idchamp=".$this->prefix."_custom_champ  where ".$this->prefix."_custom_origine=".$id;
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
	
	public function get_fields_recherche($id) {
		$return_val='';
		
		$this->get_values($id);
		if (!$this->no_special_fields) {
			reset($this->t_fields);
			foreach ($this->t_fields as $key => $val) {
				if($this->t_fields[$key]["SEARCH"] ) {
					for ($i=0; $i<count($this->values[$key]); $i++) {
						$return_val.=$this->values[$key][$i].' ';
					}
				}	
			}
		}		
		return stripslashes($return_val);
	}	

	public function get_ajax_list($name, $start) {
		global $charset,$dbh;

		$values=array();
		reset($this->t_fields);
		foreach ($this->t_fields as $key => $val) {
			if($val['NAME'] == $name) {
				switch ($val['TYPE']) {
					case 'list' :
						$q="select ".$this->prefix."_custom_list_value, ".$this->prefix."_custom_list_lib from ".$this->prefix."_custom_lists where ".$this->prefix."_custom_champ=".$key." order by ordre";
						$r=pmb_mysql_query($q,$dbh);	
						if(pmb_mysql_num_rows($r)) {
							while ($row=pmb_mysql_fetch_row($r)) {
								$values[$row[0]]=$row[1];
							}
						}
						break;
					case 'query_list' :
						$field['OPTIONS']=$val['OPTIONS'];
						$q=$field['OPTIONS'][0]['QUERY'][0]['value'];
						$r = pmb_mysql_query($q,$dbh);
						if(pmb_mysql_num_rows($r)) {
							while ($row=pmb_mysql_fetch_row($r)) {
								$values[$row[0]]=$row[1];
							}
						}
						break;
				}
				break;
			}	
		}
		if (count($values) && $start && $start!='%') {
			$filtered_values=array();
			foreach($values as $k=>$v) {
				if (strtolower(substr($v,0,strlen($start)))==strtolower($start)) {
					$filtered_values[$k]=$v;
				}
			}
			return $filtered_values;
		}
		return $values;
	}	
	
	public function get_val_field($id_elt,$name) {
		global $val_list_empr;
		global $charset;		
		if (!$this->no_special_fields) {	
			$this->get_values($id_elt);
			foreach($this->t_fields as $key=>$val){			
				if($val["NAME"] == $name){
					//$this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])
					return $this->get_formatted_output($this->values[$key],$key);					
				}	
			}			
		}		
		return "";
	}
	
	public function get_field_form($id,$field_name,$values){
		global $aff_list_empr_search,$charset;
		$field=array();
		$field['ID']=$id;
		$field['NAME']=$this->t_fields[$id]['NAME'];
		$field['MANDATORY']=$this->t_fields[$id]['MANDATORY'];
		$field['ALIAS']=$this->t_fields[$id]['TITRE'];
		$field['COMMENT']=$this->t_fields[$id]['COMMENT'];
		$field['DATATYPE']=$this->t_fields[$id]['DATATYPE'];
		$field['OPTIONS']=$this->t_fields[$id]['OPTIONS'];
		$field['VALUES']=$values;
		$field['PREFIX']=$this->prefix;
		eval("\$r=".$aff_list_empr_search[$this->t_fields[$id]['TYPE']]."(\$field,\$check_scripts,\$field_name);");
		return $r;
	}

	// Génére le champ perso éditable (pour formulaire), avec les données issues d'un formulaire
	public function get_field_form_whith_form_value($id){
		global $aff_list_empr, $charset;
		
		$field=array();
		$field['ID']=$id;
		$field['NAME']=$this->t_fields[$id]['NAME'];
		$field['MANDATORY']=$this->t_fields[$id]['MANDATORY'];
		$field['ALIAS']=$this->t_fields[$id]['TITRE'];
		$field['COMMENT']=$this->t_fields[$id]['COMMENT'];
		$field['DATATYPE']=$this->t_fields[$id]['DATATYPE'];
		$field['OPTIONS']=$this->t_fields[$id]['OPTIONS'];
		
		$name = $field['NAME'];		
		$values = array();
		global ${$name};
		$value=${$name};
		for ($i=0; $i<count($value); $i++) {
			if($value[$i]) {
				$values[] = $value[$i];
			}
		}
		$field["VALUES"] = $values;
		$field['PREFIX'] = $this->prefix;
		eval("\$aff=".$aff_list_empr[$this->t_fields[$id]['TYPE']]."(\$field,\$check_scripts);");
		
		return $aff;
	}
	
	//Lecture des champs de recherche
	public function read_search_fields_from_form() {
	
		$perso=array();
		reset($this->t_fields);
		foreach ($this->t_fields as $key => $val) {
			if($this->t_fields[$key]["SEARCH"]) {
				$t=array();
				$t["DATATYPE"]=$val["DATATYPE"];
				$t["NAME"]=$val["NAME"];
				$t["TITRE"]=$val["TITRE"];
				$t["COMMENT"]=$val["COMMENT"];
				$name = $this->t_fields[$key]["NAME"];
				$values = array();
				if($val["NAME"] == $name) {
					global ${$name};
					$value=${$name};
					for ($i=0; $i<count($value); $i++) {
						if($value[$i]) {
							$values[] = $value[$i];
						}
					}
				}
				$t["VALUE"]=$values;
				$perso["FIELDS"][]=$t;
			}
		}
		return $perso;
	}

	//Enregistrement des champs perso soumis lors de la saisie d'une fichie emprunteur ou autre...
	public function rec_fields_perso($id) {
		//Enregistrement des champs personalisés
		$requete="delete from ".$this->prefix."_custom_values where ".$this->prefix."_custom_origine=$id";
		pmb_mysql_query($requete);
		$requete = "delete from ".$this->prefix."_custom_dates where ".$this->prefix."_custom_origine=$id";
		pmb_mysql_query($requete);
		reset($this->t_fields);
		foreach ($this->t_fields as $key => $val) {
			$name=$val["NAME"];
			global ${$name};
			$value=${$name};
			for ($i=0; $i<count($value); $i++) {
				if (isset($value[$i]) && $value[$i]!=="") {
					$requete="insert into ".$this->prefix."_custom_values (".$this->prefix."_custom_champ,".$this->prefix."_custom_origine,".$this->prefix."_custom_".$val["DATATYPE"].",".$this->prefix."_custom_order) values($key,$id,'".$value[$i]."',$i)";
					pmb_mysql_query($requete);
						
					if ($this->t_fields[$key]["TYPE"] == 'date_flot') {
						$interval = explode("|||", $value[$i]);
						$date_type = $interval[0];
	
						$date_start_signe = 1;
						$date_end_signe = 1;
						if (substr($interval[1], 0, 1) == '-') {
							// date avant JC
							$date_start_signe = -1;
							$interval[1] = substr($interval[1], 1);
						}
						if (substr($interval[2], 0, 1) == '-') {
							// date avant JC
							$date_end_signe = -1;
							$interval[2] = substr($interval[2], 1);
						}
						// années saisie inférieures à 4 digit
						if (strlen($interval[1]) < 4)	$interval[1] = str_pad($interval[1], 4, '0', STR_PAD_LEFT);
						if ($interval[2] && strlen($interval[2]) < 4)	$interval[2] = str_pad($interval[2], 4, '0', STR_PAD_LEFT);
	
						$date_start = detectFormatDate($interval[1], 'min');
						$date_end = detectFormatDate($interval[2], 'max');
	
						if ($date_start == '0000-00-00') $date_start = '';
						if ($date_end == '0000-00-00') $date_end = '';
	
						if ($date_start || $date_end) {
							if (!$date_end) {
								$date_end = detectFormatDate($interval[1], 'max');
								$date_end_signe = $date_start_signe;
							}
							// format en integer
							$date_start = str_replace('-', '', $date_start) * $date_start_signe;
							$date_end = str_replace('-', '', $date_end) * $date_end_signe;
							if ($date_end < $date_start) {
								$date = $date_start;
								$date_start = $date_end;
								$date_end = $date;
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
	
}

?>