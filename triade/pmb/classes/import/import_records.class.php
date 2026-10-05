<?php
// +-------------------------------------------------+
//  2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_records.class.php,v 1.5.6.1.2.3 2025/05/15 13:58:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
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
		$hidden_form .= static::get_input_hidden_variable('import_without_index');
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
		for ($a=0; $a<count($info_600_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_600_a[$a][0] ;
			for ($j=0; $j<count($info_600_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_600_j[$a][$j] ;
			for ($j=0; $j<count($info_600_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_600_x[$a][$j] ;
			for ($j=0; $j<count($info_600_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_600_y[$a][$j] ;
			for ($j=0; $j<count($info_600_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_600_z[$a][$j] ;
		}
		for ($a=0; $a<count($info_601_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_601_a[$a][0] ;
			for ($j=0; $j<count($info_601_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_601_j[$a][$j] ;
			for ($j=0; $j<count($info_601_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_601_x[$a][$j] ;
			for ($j=0; $j<count($info_601_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_601_y[$a][$j] ;
			for ($j=0; $j<count($info_601_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_601_z[$a][$j] ;
		}
		for ($a=0; $a<count($info_602_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_602_a[$a][0] ;
			for ($j=0; $j<count($info_602_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_602_j[$a][$j] ;
			for ($j=0; $j<count($info_602_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_602_x[$a][$j] ;
			for ($j=0; $j<count($info_602_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_602_y[$a][$j] ;
			for ($j=0; $j<count($info_602_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_602_z[$a][$j] ;
		}
		for ($a=0; $a<count($info_605_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_605_a[$a][0] ;
			for ($j=0; $j<count($info_605_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_605_j[$a][$j] ;
			for ($j=0; $j<count($info_605_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_605_x[$a][$j] ;
			for ($j=0; $j<count($info_605_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_605_y[$a][$j] ;
			for ($j=0; $j<count($info_605_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_605_z[$a][$j] ;
		}
		for ($a=0; $a<count($info_606_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_606_a[$a][0] ;
			for ($j=0; $j<count($info_606_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_606_j[$a][$j] ;
			for ($j=0; $j<count($info_606_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_606_x[$a][$j] ;
			for ($j=0; $j<count($info_606_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_606_y[$a][$j] ;
			for ($j=0; $j<count($info_606_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_606_z[$a][$j] ;
		}
		for ($a=0; $a<count($info_607_a); $a++) {
			$mots_cles .= " $pmb_keyword_sep ".$info_607_a[$a][0] ;
			for ($j=0; $j<count($info_607_j[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_607_j[$a][$j] ;
			for ($j=0; $j<count($info_607_x[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_607_x[$a][$j] ;
			for ($j=0; $j<count($info_607_y[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_607_y[$a][$j] ;
			for ($j=0; $j<count($info_607_z[$a]); $j++) $mots_cles .= " $pmb_keyword_sep ".$info_607_z[$a][$j] ;
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
	
	/**
	 * Suppression de bulletin
	 * @param int $id_issue
	 * @return boolean
	 */
	public static function delete_bulletin($id_bulletin) {
		$id_bulletin = intval($id_bulletin);
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM bulletins WHERE bulletin_id = ".$id_bulletin))) {
			$bulletinage = new bulletinage($id_bulletin);
			$bulletinage->delete();
			return true;
		}
		return false;
	}
	
	/**
	 * Creation du lien Bulletin -> Article
	 * @param int $id_analysis Identifiant de l'article
	 * @param int $id_bulletin Identifiant du bulletin
	 * @return boolean
	 */
	public static function insert_relation_analysis_bulletin($id_analysis, $id_bulletin) {
		$id_analysis = intval($id_analysis);
		$id_bulletin = intval($id_bulletin);
		// On verifie que l'article est bien cree
		$query = "SELECT notice_id FROM notices WHERE notice_id = ".$id_analysis;
		$res = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($res)) {
			// On verifie que le lien entre le bulletin et l'article n'est pas deja present
			$query = "SELECT 1 FROM analysis WHERE analysis_notice = " . $id_analysis . " AND analysis_bulletin = " . $id_bulletin;
			$res = pmb_mysql_query($query);
			if (!pmb_mysql_num_rows($res)) {
				// On créer le lien entre le bulletin et l'article
				$query = "INSERT INTO analysis(analysis_bulletin, analysis_notice) VALUES('".$id_bulletin."', '".$id_analysis."' )";
				pmb_mysql_query($query);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Suppression du lien Bulletin -> Article
	 * @param int $id_analysis Identifiant de l'article
	 * @param int $id_bulletin Identifiant du bulletin
	 * @return boolean
	 */
	public static function delete_relation_analysis_bulletin($id_analysis, $id_bulletin) {
		$id_analysis = intval($id_analysis);
		$id_bulletin = intval($id_bulletin);
		// On verifie que l'article est bien cree
		$query = "SELECT notice_id FROM notices WHERE notice_id = ".$id_analysis;
		$res = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($res)) {
			// On verifie que le lien entre le bulletin et l'article est present
			$query = "SELECT 1 FROM analysis WHERE analysis_notice = ".$id_analysis." AND analysis_bulletin = ".$id_bulletin;
			$res = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($res)) {
				$query = "DELETE FROM analysis WHERE analysis_bulletin='".$id_bulletin."' AND analysis_notice='".$id_analysis."'";
				pmb_mysql_query($query);
				return true;
			}
		}
		return false;
	}
	
	public static function insert_relation_bulletin_num_notice($id_bulletin, $num_notice, $data = array()) {
		global $msg;
		
		$id_bulletin = intval($id_bulletin);
		$num_notice = intval($num_notice);
		// On contrôle que ce bulletin n'a pas déjà une notice
		$query = "SELECT num_notice FROM bulletins WHERE bulletin_id = '$id_bulletin'";
		if (!empty($num_notice)) {
			$query .= " AND num_notice != '$num_notice'";
		}
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result) && pmb_mysql_result($result, 0, 0)) {
			// Si j'ai déja une notice associé à ce bulletin je la récupère
			if (!empty($num_notice)) {
				// Si j'ai aussi un identifiant de notice de bulletin, je supprime le plus récent
				notice::del_notice($num_notice);
				pmb_mysql_query("INSERT INTO error_log(error_origin, error_text) VALUES('import_" . addslashes(SESSid) . ".inc', '" . $msg[542] . (!empty($data['code']) ? " ".$data['code']." " : "") . addslashes(clean_string(implode(" ; ", $data['titles']))) . "')");
			}
			return pmb_mysql_result($result, 0, 0);
		} else {
			if (empty($num_notice) && !empty($data['tit1'])) {
				// Si j'ai un titre je créé la notice de bulletin
				$query = "INSERT INTO notices(tit1, niveau_biblio, niveau_hierar, statut) VALUES('" . addslashes(clean_string($data['tit1'])) . "', 'b', '2', '".$data['statut']."')";
				pmb_mysql_query($query);
				$num_notice = pmb_mysql_insert_id();
				
				audit::insert_creation(AUDIT_NOTICE, $num_notice);
				// Calcul des droits d'accès s'ils sont activés
				notice::calc_access_rights($num_notice);
				
				// Mise à jour de tous les index de la notice
				notice::majNoticesTotal($num_notice);
			}
			
			// On créer le lien entre le bulletin et la notice de bulletin
			$query = "UPDATE bulletins SET num_notice = '".$num_notice."' WHERE bulletin_id = '".$id_bulletin."'";
			pmb_mysql_query($query);
			return $num_notice;
		}
	}
	
	public static function insert_caddie($id_notice,$libelle_panier,$type="NOTI"){
	    $error = false;
	    $error_message = '';
	    $id_notice = intval($id_notice);
	    $requete="select idcaddie from caddie where name='".addslashes($libelle_panier)."' AND type='".$type."' ";
	    $res = pmb_mysql_query($requete);
	    $id_panier = pmb_mysql_result($res,0);
	    if(!$id_panier){
	        $requete="insert into caddie(name,type,autorisations) values ('".addslashes($libelle_panier)."','".$type."','1')";
	        if(!pmb_mysql_query($requete)){
	            $error = true;
	            $error_message = "requete echouee : ".$requete;
	            $id_panier=1;
	        }else{
	            $id_panier=pmb_mysql_insert_id();
	        }
	    }
	    $requete="INSERT IGNORE INTO caddie_content(caddie_id,object_id) VALUES('".$id_panier."','".$id_notice."')";
	    if(!pmb_mysql_query($requete)){
	        $error = true;
	        $error_message .= "requete echouee : ".$requete;
	    }
	    return [
	        'error' => $error,
	        'error_message' => $error_message
	    ];
	}
}
