<?php
// +-------------------------------------------------+
//  2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_empr.class.php,v 1.2 2019-06-05 13:57:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class import_empr extends import_entities {
	
	/**
	 * Création du login OPAC
	 * @param unknown $nom
	 * @param unknown $prenom
	 */
	public static function cre_login($nom, $prenom) {
		$empr_login = substr($prenom,0,1).$nom ;
		$empr_login = strtolower($empr_login);
		$empr_login = clean_string($empr_login) ;
		$empr_login = convert_diacrit(strtolower($empr_login)) ;
		$empr_login = preg_replace('/[^a-z0-9\.]/', '', $empr_login);
		$pb = 1 ;
		$num_login=1 ;
		while ($pb==1) {
			$requete = "SELECT empr_login FROM empr WHERE empr_login='$empr_login' AND empr_nom <> '$nom' AND empr_prenom <> '$prenom' LIMIT 1 ";
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_num_rows($res);
			if ($nbr_lignes) {
				$empr_login .= $num_login ;
				$num_login++;
			}
			else $pb = 0 ;
		}
		return $empr_login;
	}
	
	public static function gestion_groupe($lib_groupe, $empr_cb) {
		$lib_groupe = trim($lib_groupe);
		if(!$lib_groupe) {
			return;
		}
		$sel = pmb_mysql_query("SELECT id_groupe from groupe WHERE libelle_groupe = '".addslashes($lib_groupe)."'");
		$nb_enreg_grpe = pmb_mysql_num_rows($sel);
	
		if (!$nb_enreg_grpe) {
			//insertion dans la table groupe
			pmb_mysql_query("INSERT INTO groupe(libelle_groupe) VALUES('".addslashes($lib_groupe)."')");
			$groupe=pmb_mysql_insert_id();
		} else {
			$grpobj = pmb_mysql_fetch_object($sel) ;
			$groupe = $grpobj->id_groupe ;
		}
	
		//insertion dans la table empr_groupe
		$sel_empr = pmb_mysql_query("SELECT id_empr FROM empr WHERE empr_cb = '".addslashes($empr_cb)."'");
		$empr = pmb_mysql_fetch_array($sel_empr);
		pmb_mysql_query("INSERT INTO empr_groupe(empr_id, groupe_id) VALUES ('$empr[id_empr]','$groupe')");
	}
	
	public static function get_update_type_radio() {
		return "
		<input type=radio name='type_import' value='nouveau_lect' checked />
        <label class='etiquette' for='form_import_lec'>Nouveaux lecteurs</label>
        (ajoute ou modifie les lecteurs pr&eacute;sents dans le fichier)
        <br />
        <input type=radio name='type_import' value='maj_complete' />
        <label class='etiquette' for='form_import_lec'>Mise &agrave; jour compl&egrave;te</label>
        (modifie les lecteurs pr&eacute;sents, supprime les lecteurs absents du fichier)
		";
	}
	
	public static function get_locations_selector($name) {
		global $msg;
		global $pmb_lecteurs_localises;
		global $deflt2docs_location;
		
		$selector = "";
		if ($pmb_lecteurs_localises) {
			$selector .= "<label>".$msg['editions_filter_empr_location']."</label> ";
			$selector .= "<select name='".$name."'>";
			
			$query = "SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle";
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_array($result)) {
				$selector .= "<option value='".$row["idlocation"]."' >".$row["location_libelle"]."</option>";
			}
			$selector .= "</select>";
			
		} else {
			$selector .= "<input type='hidden' name='".$name."' value='".$deflt2docs_location."' />";
		}
		
		return $selector;
	}
	
	public static function get_categories_selector($name) {
		global $msg;
	
		$selector = "<label>".$msg['editions_filter_empr_categ']."</label> ";
		$selector .= "<select name='".$name."'>";
	
		$query = "SELECT id_categ_empr, libelle FROM empr_categ ORDER BY libelle";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_array($result)) {
			$selector .= "<option value='".$row["id_categ_empr"]."' >".$row["libelle"]."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	public static function get_codestat_selector($name) {
		global $msg;
		
		$selector = "<label>".$msg['editions_filter_empr_codestat']."</label> ";
		$selector .= "<select name='".$name."'>";
		
		$query = "SELECT idcode, libelle FROM empr_codestat ORDER BY libelle";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_array($result)) {
			$selector .= "<option value='".$row["idcode"]."' >".$row["libelle"]."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	public static function get_encoded_buffer($buffer) {
		global $charset;
		global $encodage_fic_source;
		
		if($encodage_fic_source){//On a forcé l'encodage
			switch ($encodage_fic_source) {
				case 'iso8859':
					if($charset == 'utf-8') {
						if(function_exists("mb_convert_encoding") && ((strpos($buffer,chr(0x92)) !== false) || (strpos($buffer,chr(0x93)) !== false) || (strpos($buffer,chr(0x9c)) !== false) || (strpos($buffer,chr(0x8c)) !== false))){//Pour les caractères windows
							$buffer = mb_convert_encoding($buffer,"UTF-8","Windows-1252");
						}else{
							$buffer = utf8_encode($buffer);
						}
					}
					break;
				case 'iso5426':
					$buffer=iso2709_record::ISO_646_5426_decode($buffer);
					if($charset == 'utf-8') {
						if(function_exists("mb_convert_encoding") && ((strpos($buffer,chr(0x92)) !== false) || (strpos($buffer,chr(0x93)) !== false) || (strpos($buffer,chr(0x9c)) !== false) || (strpos($buffer,chr(0x8c)) !== false))){//Pour les caractères windows
							$buffer = mb_convert_encoding($buffer,"UTF-8","Windows-1252");
						}else{
							$buffer = utf8_encode($buffer);
						}
					}
					break;
				case 'utf-8':
					if($charset == 'iso-8859-1') {
						$buffer = utf8_decode($buffer);
					}
					break;
			}
		}
		return $buffer;
	}
}
