<?php
// +-------------------------------------------------+
//  2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_entities.class.php,v 1.7 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/caddie_root.class.php");

class import_entities {
	
	public function __construct(){
		
	}
	
	public function proceed(){
		
	}
	
	public static function get_encoding_selector() {
		global $msg, $charset;
		global $encodage_fic_source;
		
		if($encodage_fic_source){
			$_SESSION["encodage_fic_source"]=$encodage_fic_source;
		}elseif(isset($_SESSION["encodage_fic_source"])){
			$encodage_fic_source=$_SESSION["encodage_fic_source"];
		}
		return "
	       	<select name='encodage_fic_source' id='encodage_fic_source'>
				<option value='' ".(!$encodage_fic_source ? " selected='selected' ": "").">".htmlentities($msg["admin_import_encodage_fic_source_undefine"],ENT_QUOTES,$charset)."</option>
				<option value='iso5426' ".($encodage_fic_source == "iso5426" ? " selected='selected' ": "").">".htmlentities($msg["admin_import_encodage_fic_source_iso5426"],ENT_QUOTES,$charset)."</option>
				<option value='utf8' ".($encodage_fic_source == "utf8" ? " selected='selected' ": "").">".htmlentities($msg["admin_import_encodage_fic_source_utf8"],ENT_QUOTES,$charset)."</option>
				<option value='iso8859' ".($encodage_fic_source == "iso8859" ? " selected='selected' ": "").">".htmlentities($msg["admin_import_encodage_fic_source_iso8859"],ENT_QUOTES,$charset)."</option>
			</select>";
	}
	
	public static function is_custom_values_exists($prefix, $datatype, $idchamp, $entity_id, $value) {
		if ($value) {
			$requete="select count(".$prefix."_custom_origine) from ".$prefix."_custom_values where ".$prefix."_custom_".$datatype."='".addslashes($value)."' and ".$prefix."_custom_champ=".$idchamp." and ".$prefix."_custom_origine='".$entity_id."'";
			$resultat=pmb_mysql_query($requete);
			if (!pmb_mysql_result($resultat, 0, 0)) {
				$requete="insert into ".$prefix."_custom_values (".$prefix."_custom_champ,".$prefix."_custom_origine,".$prefix."_custom_".$datatype.") values(".$idchamp.",$entity_id,'".addslashes($value)."')";
				pmb_mysql_query($requete);
			}
		}
	}
	
	public static function get_input_hidden_text($name, $value) {
		return "<input name='".$name."' TYPE='hidden' value='".$value."' />";
	}
	
	public static function get_input_hidden_variable($name) {
		$global_variable = $name;
		global ${$global_variable};
	
		if(${$global_variable} !== '') {
			return "<input name='".$name."' TYPE='hidden' value='".${$global_variable}."' />";
		}
		return "";
	}
	
	public static function get_input_hidden_caddie_variable($caddie_type) {
		$input_hidden = static::get_input_hidden_variable(static::get_type()."ajt".$caddie_type);
		$input_hidden .= static::get_input_hidden_variable(static::get_type()."_caddie_".$caddie_type);
		return $input_hidden;
	}
	
	public static function get_caddie_form($caddie_type, $field_name, $table_name) {
		global $msg;
		global $PMBuserid;
	
		$caddie_form = "
			<div class='row'>
				<input type='checkbox' name='".static::get_type()."ajt".$caddie_type."' value='1'>&nbsp;".$msg['import_choix_caddie_'.strtolower($caddie_type)]."&nbsp;";
		$requetetmpcad = "SELECT ".$field_name.", name FROM ".$table_name." where type='".strtoupper($caddie_type)."' and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') order by name ";
		$caddie_form .= gen_liste ($requetetmpcad, $field_name, "name", static::get_type()."_caddie_".$caddie_type, "", "", "", "","","",0);
		$caddie_form .= "
			</div>";
		return $caddie_form;
	}
	
	public static function get_caddies_form() {
		return '';
	}
	
	public static function add_object_caddie($object_id, $object_type='NOTI', $idcaddie=0) {
		$myCart = caddie_root::get_instance_from_object_type($object_type, $idcaddie);
		$myCart->add_item($object_id, $object_type);
	}
	
	public static function get_link_caddie($caddie_type) {
		global $msg;
		
		$checkbox = static::get_type()."ajt".$caddie_type;
		global ${$checkbox};
		$idcaddie = static::get_type()."_caddie_".$caddie_type;
		global ${$idcaddie};
		
		$link_caddie = '';
		if(!empty(${$checkbox}) && !empty(${$idcaddie})) {
			$myCart = caddie_root::get_instance_from_object_type($caddie_type, ${$idcaddie});
			import_records::add_object_caddie($notice_id, 'NOTI', $import_records_caddie_NOTI);
			
			$link_caddie .= "
					<div class='row'>
						<b>".$msg['import_added_caddie_'.strtolower($caddie_type)]."</b>
						<a href='".caddie_controller::get_constructed_link('gestion', 'panier', '', ${$idcaddie})."' target='_blank'>".$myCart->name."</a>
					</div>";
		}
		return $link_caddie;
	}
	
	public static function get_advanced_form() {
		global $msg;
		
		$advanced_form = static::get_caddies_form();
		return gen_plus(static::get_type().'_advanced_form', $msg['import_advanced_form'], $advanced_form);
	}
	
	public static function get_type() {
	    return static::class;
	}
}
