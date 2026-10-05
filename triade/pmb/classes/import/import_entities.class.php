<?php
// +-------------------------------------------------+
//  2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_entities.class.php,v 1.9.4.1 2025/05/15 13:58:15 dgoron Exp $

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
	
	public static function get_encoded_buffer($buffer) {
		global $charset;
		global $encodage_fic_source;
		
		if(isset($encodage_fic_source)){
			$_SESSION["encodage_fic_source"]=$encodage_fic_source;
		}elseif(isset($_SESSION["encodage_fic_source"])){
			$encodage_fic_source=$_SESSION["encodage_fic_source"];
		}
		if($encodage_fic_source){//On a forcé l'encodage
			switch ($encodage_fic_source) {
				case 'iso8859':
					if($charset == 'utf-8') {
						if(function_exists("mb_convert_encoding") && ((strpos($buffer,chr(0x92)) !== false) || (strpos($buffer,chr(0x93)) !== false) || (strpos($buffer,chr(0x9c)) !== false) || (strpos($buffer,chr(0x8c)) !== false))){//Pour les caractères windows
							$buffer = mb_convert_encoding($buffer,"UTF-8","Windows-1252");
						}else{
							$buffer = encoding_normalize::utf8_normalize($buffer);
						}
					}
					break;
				case 'iso5426':
					$buffer=iso2709_record::ISO_646_5426_decode($buffer);
					if($charset == 'utf-8') {
						if(function_exists("mb_convert_encoding") && ((strpos($buffer,chr(0x92)) !== false) || (strpos($buffer,chr(0x93)) !== false) || (strpos($buffer,chr(0x9c)) !== false) || (strpos($buffer,chr(0x8c)) !== false))){//Pour les caractères windows
							$buffer = mb_convert_encoding($buffer,"UTF-8","Windows-1252");
						}else{
							$buffer = encoding_normalize::utf8_normalize($buffer);
						}
					}
					break;
				case 'utf-8':
					if($charset == 'iso-8859-1') {
						$buffer = encoding_normalize::utf8_decode($buffer);
					}
					break;
			}
		}
		return $buffer;
	}
	
	public static function decoupe_date($date_non_formate,$annee_seule=false,$complement="01"){
	    $date="";
	    $tab=preg_split("/\D/",$date_non_formate);
	    
	    switch(count($tab)){
	        case 3 :
	            if(strlen($tab[0]) == 4){
	                $date=$tab[0]."-".$tab[1]."-".$tab[2];
	            }elseif(strlen($tab[2]) == 4){
	                $date=$tab[2]."-".$tab[1]."-".$tab[0];
	            }elseif($tab[0] > 31){
	                $date="19".$tab[0]."-".$tab[1]."-".$tab[2];
	            }elseif($tab[2] > 31){
	                $date="19".$tab[2]."-".$tab[1]."-".$tab[0];
	            }
	            break;
	        case 2 :
	            if(strlen($tab[0]) == 4){
	                $date=$tab[0]."-".$tab[1]."-".$complement;
	            }elseif(strlen($tab[1]) == 4){
	                $date=$tab[1]."-".$tab[0]."-".$complement;
	            }elseif($tab[0] > 31){
	                $date="19".$tab[0]."-".$tab[1]."-".$complement;
	            }elseif($tab[1] > 31){
	                $date="19".$tab[1]."-".$tab[0]."-".$complement;
	            }
	            break;
	        case 1 :
	            if(strlen($tab[0]) == 8){
	                $date=substr($tab[0],0,4)."-".substr($tab[0],4,2)."-".substr($tab[0],6,2);
	            }elseif(strlen($tab[0]) == 6){
	                $date=substr($tab[0],0,4)."-".substr($tab[0],4,2)."-".$complement;
	            }elseif(strlen($tab[0]) == 4){
	                $date=substr($tab[0],0,4)."-".$complement."-".$complement;
	            }
	    }
	    
	    if($annee_seule){
	        return substr($date,0,4);
	    }else{
	        return $date;
	    }
	}
	
	//trouve un champ perso et renvoi son id
	public static function trouve_champ_perso($nom,$table="notices") {
	    $rqt = "SELECT idchamp FROM ".$table."_custom WHERE name='" . addslashes($nom) . "'";
	    $res = pmb_mysql_query($rqt);
	    if (pmb_mysql_num_rows($res)>0) {
	        return pmb_mysql_result($res,0);
	    }
	    return 0;
	}
	
	//Pour renseigner les champs perso
	public static function renseigne_champ_perso($nom,$type,$value,$notice_id,$table="notices", $decoupe_date=true) {
	    if(!trim($value) or !trim($nom) or !trim($notice_id)  )return false; // On sort si la valeur ou le nom du champ sont vide
	    $mon_champ = static::trouve_champ_perso($nom,$table);
	    if ($mon_champ){
	        switch ($type) {
	            case "small_text":
	                $requete="insert into ".$table."_custom_values (".$table."_custom_champ,".$table."_custom_origine,".$table."_custom_small_text) values('".$mon_champ."','".$notice_id."','".addslashes(trim($value))."')";
	                if(!pmb_mysql_query($requete)) return false;
	                break;
	            case "integer":
	                $requete="insert into ".$table."_custom_values (".$table."_custom_champ,".$table."_custom_origine,".$table."_custom_integer) values('".$mon_champ."','".$notice_id."','".addslashes(trim($value))."')";
	                if(!pmb_mysql_query($requete)) return false;
	                break;
	            case "text":
	                $rqt = "select datatype from ".$table."_custom where idchamp = $mon_champ";
	                $res = pmb_mysql_query($rqt);
	                $datatype = @pmb_mysql_result($res,0,0);
	                if($datatype == "small_text"){
	                    $requete="insert into ".$table."_custom_values (".$table."_custom_champ,".$table."_custom_origine,".$table."_custom_small_text) values('".$mon_champ."','".$notice_id."','".addslashes(trim($value))."')";
	                }else{
	                    $requete="insert into ".$table."_custom_values (".$table."_custom_champ,".$table."_custom_origine,".$table."_custom_text) values('".$mon_champ."','".$notice_id."','".addslashes(trim($value))."')";
	                }
	                if(!pmb_mysql_query($requete)) return false;
	                break;
	            case "date":
	                if ($decoupe_date) {
	                    $value = static::decoupe_date($value);
	                }
	                $requete="insert into ".$table."_custom_values (".$table."_custom_champ,".$table."_custom_origine,".$table."_custom_date) values('".$mon_champ."','".$notice_id."','".addslashes(trim($value))."')";
	                if(!pmb_mysql_query($requete)){
	                    echo "requete : ".$requete."<br>";
	                    return false;
	                }
	                break;
	            case "list":
	                $requete="select ".$table."_custom_list_value from ".$table."_custom_lists where ".$table."_custom_list_lib='".addslashes(trim($value))."' and ".$table."_custom_champ='".$mon_champ."' ";
	                $resultat=pmb_mysql_query($requete);
	                if (pmb_mysql_num_rows($resultat)) {
	                    $value2=pmb_mysql_result($resultat,0,0);
	                } else {
	                    $requete="select max(".$table."_custom_list_value*1) from ".$table."_custom_lists where ".$table."_custom_champ='".$mon_champ."' ";
	                    $resultat=pmb_mysql_query($requete);
	                    $max=@pmb_mysql_result($resultat,0,0);
	                    $n=$max+1;
	                    $requete="insert into ".$table."_custom_lists (".$table."_custom_champ,".$table."_custom_list_value,".$table."_custom_list_lib) values('".$mon_champ."',$n,'".addslashes(trim($value))."')";
	                    if(!pmb_mysql_query($requete)) return false;
	                    $value2=$n;
	                }
	                $requete="insert into ".$table."_custom_values (".$table."_custom_champ,".$table."_custom_origine,".$table."_custom_integer) values('".$mon_champ."',$notice_id,$value2)";
	                if(!pmb_mysql_query($requete)) return false;
	                break;
	            case "list_text"://apport d'une modif
	                $requete="select ".$table."_custom_list_value from ".$table."_custom_lists where ".$table."_custom_list_lib='".addslashes(trim($value))."' and ".$table."_custom_champ='".$mon_champ."' ";
	                $resultat=pmb_mysql_query($requete);
	                if (pmb_mysql_num_rows($resultat)) {
	                    $value2=pmb_mysql_result($resultat,0,0);
	                } else {
	                    $requete="select max(".$table."_custom_list_value*1) from ".$table."_custom_lists where ".$table."_custom_champ='".$mon_champ."' ";
	                    $resultat=pmb_mysql_query($requete);
	                    $max=@pmb_mysql_result($resultat,0,0);
	                    $n=$max+1;
	                    $requete="insert into ".$table."_custom_lists (".$table."_custom_champ,".$table."_custom_list_value,".$table."_custom_list_lib) values('".$mon_champ."',$n,'".addslashes(trim($value))."')";
	                    if(!pmb_mysql_query($requete)) return false;
	                    $value2=$n;
	                }
	                $requete="insert into ".$table."_custom_values (".$table."_custom_champ,".$table."_custom_origine,".$table."_custom_small_text) values('".$mon_champ."',$notice_id,'".$value2."')";
	                if(!pmb_mysql_query($requete)) return false;
	                break;
	            default:
	                return false;
	                break;
	        }
	    }else{
	        return false;
	    }
	    return true;
	    
	}
}
