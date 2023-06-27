<?php
// +-------------------------------------------------+
//  2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_records.class.php,v 1.3 2019-01-24 16:51:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/import/import_entities.class.php");

class import_records extends import_entities {
	
	public static function get_hidden_form($form_name, $next_action) {
		global $current_module;
		global $filename, $to_file;
		global $recharge, $noticenumber, $j;
	
		$hidden_form ="<form class='form-$current_module' name=\"".$form_name."\" method=\"post\" action=\"iimport_expl.php\">\n";
		$hidden_form .= static::get_input_hidden_variable('name_func');
		$hidden_form .= static::get_input_hidden_text('categ', 'import');
		$hidden_form .= static::get_input_hidden_text('sub', 'import');
		$hidden_form .= static::get_input_hidden_text('action', $next_action);
		$hidden_form .= "<input type=\"hidden\" name=\"file_submit\" value=\"".($filename ? $filename : $to_file)."\" />";
		$hidden_form .= static::get_input_hidden_variable('filename');
		$hidden_form .= static::get_input_hidden_variable('from_file');
		$hidden_form .= static::get_input_hidden_variable('isbn_mandatory');
		$hidden_form .= static::get_input_hidden_variable('isbn_dedoublonnage');
		$hidden_form .= static::get_input_hidden_variable('isbn_only');
		$hidden_form .= static::get_input_hidden_variable('statutnot');
		$hidden_form .= static::get_input_hidden_variable('notice_is_new');
		if($next_action == 'load') {
			$hidden_form .= "<input type=\"hidden\" name=\"recharge\" value=\"YES\" />";
		} elseif($recharge) {
			$hidden_form .= "<input type=\"hidden\" name=\"recharge\" value=\"$recharge\" />";
		}
		if($noticenumber) {
			$hidden_form .= "<input type=\"hidden\" name=\"noticenumber\" value=\"".($noticenumber+$j)."\" />";
		}
		$hidden_form .= static::get_input_hidden_variable('reste');
		$hidden_form .= static::get_input_hidden_variable('nbtot_notice');
		$hidden_form .= static::get_input_hidden_variable('notice_deja_presente');
		$hidden_form .= static::get_input_hidden_variable('notice_rejetee');
		$hidden_form .= static::get_input_hidden_variable('que_faire');
		$hidden_form .= static::get_input_hidden_variable('link_generate');
		$hidden_form .= static::get_input_hidden_variable('authorities_notices');
		$hidden_form .= static::get_input_hidden_variable('authorities_default_origin');
		$hidden_form .= static::get_input_hidden_variable('import_force_notice_is_new');
		$hidden_form .= static::get_input_hidden_variable('import_notice_existing_replace');
		$hidden_form .= static::get_input_hidden_variable('notice_replace_links');
		$hidden_form .= static::get_hidden_caddies_form();
		$hidden_form .="</form>";
		return $hidden_form;
	}
	
	public static function get_caddies_form() {
		$caddies_form = static::get_caddie_form('NOTI', 'idcaddie', 'caddie');
		return $caddies_form;
	}
	
	public static function get_hidden_caddies_form() {
		$hidden_caddies_form = static::get_input_hidden_caddie_variable('NOTI');
		return $hidden_caddies_form;
	}
	
	public static function get_links_caddies() {
		$links_caddies = static::get_link_caddie('NOTI');
		return $links_caddies;
	}
	
	public static function get_mots_cles() {
		global $pmb_keyword_sep;
		global $info_600_a, $info_600_j, $info_600_x, $info_600_y, $info_600_z ;
		global $info_601_a, $info_601_j, $info_601_x, $info_601_y, $info_601_z ;
		global $info_602_a, $info_602_j, $info_602_x, $info_602_y, $info_602_z ;
		global $info_605_a, $info_605_j, $info_605_x, $info_605_y, $info_605_z ;
		global $info_606_a, $info_606_j, $info_606_x, $info_606_y, $info_606_z ;
		global $info_607_a, $info_607_j, $info_607_x, $info_607_y, $info_607_z ;
		
		$mots_cles = "";
		for ($a=0; $a<sizeof($info_600_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_600_a[$a][0] ;
			for ($j=0; $j<sizeof($info_600_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_600_j[$a][$j] ;
			for ($j=0; $j<sizeof($info_600_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_600_x[$a][$j] ;
			for ($j=0; $j<sizeof($info_600_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_600_y[$a][$j] ;
			for ($j=0; $j<sizeof($info_600_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_600_z[$a][$j] ;
		}
		for ($a=0; $a<sizeof($info_601_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_601_a[$a][0] ;
			for ($j=0; $j<sizeof($info_601_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_601_j[$a][$j] ;
			for ($j=0; $j<sizeof($info_601_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_601_x[$a][$j] ;
			for ($j=0; $j<sizeof($info_601_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_601_y[$a][$j] ;
			for ($j=0; $j<sizeof($info_601_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_601_z[$a][$j] ;
		}
		for ($a=0; $a<sizeof($info_602_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_602_a[$a][0] ;
			for ($j=0; $j<sizeof($info_602_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_602_j[$a][$j] ;
			for ($j=0; $j<sizeof($info_602_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_602_x[$a][$j] ;
			for ($j=0; $j<sizeof($info_602_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_602_y[$a][$j] ;
			for ($j=0; $j<sizeof($info_602_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_602_z[$a][$j] ;
		}
		for ($a=0; $a<sizeof($info_605_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_605_a[$a][0] ;
			for ($j=0; $j<sizeof($info_605_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_605_j[$a][$j] ;
			for ($j=0; $j<sizeof($info_605_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_605_x[$a][$j] ;
			for ($j=0; $j<sizeof($info_605_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_605_y[$a][$j] ;
			for ($j=0; $j<sizeof($info_605_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_605_z[$a][$j] ;
		}
		for ($a=0; $a<sizeof($info_606_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_606_a[$a][0] ;
			for ($j=0; $j<sizeof($info_606_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_606_j[$a][$j] ;
			for ($j=0; $j<sizeof($info_606_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_606_x[$a][$j] ;
			for ($j=0; $j<sizeof($info_606_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_606_y[$a][$j] ;
			for ($j=0; $j<sizeof($info_606_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_606_z[$a][$j] ;
		}
		for ($a=0; $a<sizeof($info_607_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_607_a[$a][0] ;
			for ($j=0; $j<sizeof($info_607_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_607_j[$a][$j] ;
			for ($j=0; $j<sizeof($info_607_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_607_x[$a][$j] ;
			for ($j=0; $j<sizeof($info_607_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_607_y[$a][$j] ;
			for ($j=0; $j<sizeof($info_607_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_607_z[$a][$j] ;
		}
		return $mots_cles;
	}
	
	public static function insert_value_custom_field($champ, $origine, $value, $datatype='small_text') {
		$rqt = "SELECT count(1) FROM notices_custom_values WHERE notices_custom_champ='".$champ."' AND notices_custom_origine='".$origine."' " ;
		if (!pmb_mysql_result(pmb_mysql_query($rqt),0,0)) {
			$rqt_ajout = "INSERT INTO notices_custom_values (notices_custom_champ, notices_custom_origine, notices_custom_".$datatype.") VALUES ('".$champ."', '".$origine."', '".addslashes($value)."')" ;
			$res_ajout = pmb_mysql_query($rqt_ajout);
			return $res_ajout;
		}
		return false;
	}
	
	public static function insert_list_integer_value_custom_field($idchamp, $origine, $lib, $n=0) {
		if($lib) {
			if(!$n) {
				$requete="SELECT max(notices_custom_list_value*1) FROM notices_custom_lists WHERE notices_custom_champ=".$idchamp;
				$resultat=pmb_mysql_query($requete);
				$max=@pmb_mysql_result($resultat,0,0);
				$n=$max+1;
			}
			$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($lib)."' and notices_custom_champ=".$idchamp;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$value=pmb_mysql_result($resultat,0,0);
			} else {
				$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values(".$idchamp.",$n,'".addslashes($lib)."')";
				pmb_mysql_query($requete);
				$value=$n;
				$n++;
			}
			$requete="insert into notices_custom_values (notices_custom_champ,notices_custom_origine,notices_custom_integer) values(".$idchamp.",$origine,$value)";
			pmb_mysql_query($requete);
		}
		return $n;
	}
	
	public static function insert_list_integer_values_custom_field($idchamp, $origine, $info_values, $debug=array()) {
		if (count($info_values)) {
			$requete="SELECT name,type,datatype FROM notices_custom WHERE idchamp=".$idchamp;
			$res=pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($res) && (pmb_mysql_result($res,0,1) == "list") && (pmb_mysql_result($res,0,2) == "integer")){
				$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=".$idchamp;
				$resultat=pmb_mysql_query($requete);
				$max=@pmb_mysql_result($resultat,0,0);
				$n=$max+1;
				for ($i=0; $i<count($info_values); $i++) {
					for ($j=0; $j<count($info_values[$i]); $j++) {
						$n = static::insert_list_integer_value_custom_field($idchamp, $origine, $info_values[$i][$j], $n);
					}
				}
			}else{
				if(count($debug) && $debug['field_code']) {
					pmb_mysql_query("insert into error_log (error_origin, error_text) values ('import_expl_".addslashes(SESSid).".inc', 'Il n\'y a pas de CP de notice avec l\'identifiant=".$idchamp." ou il n\'est pas de type liste entier : le ".$debug['field_code']." n\'est donc pas repris (".$debug['field_label'].")') ") ;
				}
			}
		}
	}
	
	public static function insert_list_integer_value_custom_field_from_name($namechamp, $origine, $lib) {
		if($lib) {
			$idchamp = static::get_id_from_name($namechamp);
			if($idchamp) {
				static::insert_list_integer_value_custom_field($idchamp, $origine, $lib);
			}	
		}
	}
	
	public static function insert_list_integer_values_custom_field_from_name($namechamp, $origine, $info_values, $debug=array()) {
		$idchamp = static::get_id_from_name($namechamp);
		if($idchamp) {
			static::insert_list_integer_values_custom_field($idchamp, $origine, $info_values, $debug);
		}
	}
	
	public static function get_id_from_name($name) {
		$id = 0;
		$rqt = "SELECT idchamp FROM notices_custom WHERE name='" . addslashes($name) . "'";
		$res = pmb_mysql_query($rqt);
		if (pmb_mysql_num_rows($res)) {
			$id = pmb_mysql_result($res, 0, 0);
		}
		return $id;
	}
}
