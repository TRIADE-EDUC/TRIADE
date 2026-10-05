<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_scenario.class.php,v 1.6 2024/03/22 15:31:04 qvarin Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/contribution_area/contribution_area.class.php');
require_once($class_path.'/onto/common/onto_common_uri.class.php');

/**
 * class contribution_area_scenario
 * Représente un scenario de contribution
 */
class contribution_area_scenario {
	
	/**
	 * Id du scénario de contribution
	 * 
	 * @access protected
	 */
	protected $id;
	
	protected static $graphstore;
	
	protected static function get_graphstore(){
		if(!isset(self::$graphstore)){
			self::$graphstore = contribution_area::get_graphstore();
		}
		return self::$graphstore;
	}
	
	public static function get_status($id) {
		$id = intval($id);
		$uri = onto_common_uri::get_uri($id);
		self::get_graphstore();
		$result = self::$graphstore->query('
			SELECT ?status_id WHERE {
				<'.$uri.'> rdf:type ca:Scenario .
				<'.$uri.'> pmb:status ?status_id
			}
		');
		$rows = self::$graphstore->get_result();
		return $rows[0]->status_id;
	}
	
	public static function get_entity_type($id) {
		$id = intval($id);
		$uri = onto_common_uri::get_uri($id);
		self::get_graphstore();
		$result = self::$graphstore->query('
			SELECT ?entity_type WHERE {
				<'.$uri.'> rdf:type ca:Scenario .
				<'.$uri.'> pmb:entity ?entity_type
			}
		');
		$rows = self::$graphstore->get_result();
		return $rows[0]->entity_type;
	}
	
	public static function save_rights($update, $id) {
		global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
		global $res_prf, $chk_rights, $prf_rad, $r_rad;
		
		// traitement des droits acces user_contribution_area
		if ($gestion_acces_active == 1 && $gestion_acces_empr_contribution_scenario == 1) {
			$ac = new acces();
			$dom_5 = $ac->setDomain(5);
			if ($update) {
				$dom_5->storeUserRights(1, $id, $res_prf, $chk_rights, $prf_rad, $r_rad);
			} else {
				$dom_5->storeUserRights(0, $id, $res_prf, $chk_rights, $prf_rad, $r_rad);
			}
		}
	}
	
	public static function get_rights_form($id = 0) {
		global $msg, $charset;
		global $gestion_acces_active, $gestion_acces_empr_contribution_scenario;
		global $gestion_acces_empr_contribution_scenario_def;
			
		if ($gestion_acces_active != 1)
			return '';
		$ac = new acces();
			
		$form = '';
		$c_form = "
			<div class='row'>
				<label class='etiquette'><!-- domain_name --></label>
			</div>
			<div class='row'>
				<div class='colonne3'>" . htmlentities($msg['dom_cur_prf'], ENT_QUOTES, $charset) . "</div>
				<div class='colonne_suite'><!-- prf_rad --></div>
			</div>
			<div class='row'>
				<div class='colonne3'>" . htmlentities($msg['dom_cur_rights'], ENT_QUOTES, $charset) . "</div>
				<div class='colonne_suite'><!-- r_rad --></div>
				<div class='row'><!-- rights_tab --></div>
			</div>";
			
		if ($gestion_acces_empr_contribution_scenario == 1) {
	
			$r_form = $c_form;
			$dom_5 = $ac->setDomain(5);
			$r_form = str_replace('<!-- domain_name -->', htmlentities($dom_5->getComment('long_name'), ENT_QUOTES, $charset), $r_form);
			if ($id) {
				// profil ressource
				$def_prf = $dom_5->getComment('res_prf_def_lib');
				$res_prf = $dom_5->getResourceProfile($id);
				$q = $dom_5->loadUsedResourceProfiles();
					
				// Recuperation droits generiques utilisateur
				$user_rights = $dom_5->getDomainRights(0, $res_prf);
					
				if ($user_rights & 2) {
					$p_sel = gen_liste($q, 'prf_id', 'prf_name', 'res_prf[5]', '', $res_prf, '0', $def_prf, '0', $def_prf);
					$p_rad = "<input type='radio' id='prf_rad_5_R' name='prf_rad[5]' value='R' ";
					if ($gestion_acces_empr_contribution_scenario_def != '1')
						$p_rad .= "checked='checked' ";
					$p_rad .= "><label for='prf_rad_5_R' >" . htmlentities($msg['dom_rad_calc'], ENT_QUOTES, $charset) . "</label></input><input type='radio' id='prf_rad_5_C' name='prf_rad[5]' value='C' ";
					if ($gestion_acces_empr_contribution_scenario_def == '1')
						$p_rad .= "checked='checked' ";
					$p_rad .= "><label for='prf_rad_5_C' >" . htmlentities($msg['dom_rad_def'], ENT_QUOTES, $charset) . $p_sel."</label></input>";
					$r_form = str_replace('<!-- prf_rad -->', $p_rad, $r_form);
				} else {
					$r_form = str_replace('<!-- prf_rad -->', htmlentities($dom_5->getResourceProfileName($res_prf), ENT_QUOTES, $charset), $r_form);
				}
					
				// droits/profils utilisateurs
				if ($user_rights & 1) {
					$r_rad = "<input type='radio' id='r_rad_5_R' name='r_rad[5]' value='R' ";
					if ($gestion_acces_empr_contribution_scenario_def != '1')
						$r_rad .= "checked='checked' ";
					$r_rad .= "><label for='r_rad_5_R' >" . htmlentities($msg['dom_rad_calc'], ENT_QUOTES, $charset) . "</label></input><input type='radio' id='r_rad_5_C' name='r_rad[5]' value='C' ";
					if ($gestion_acces_empr_contribution_scenario_def == '1')
						$r_rad .= "checked='checked' ";
					$r_rad .= "><label for='r_rad_5_C' >" . htmlentities($msg['dom_rad_def'], ENT_QUOTES, $charset) . "</label></input>";
					$r_form = str_replace('<!-- r_rad -->', $r_rad, $r_form);
				}
					
				// recuperation profils utilisateurs
				$t_u = array();
				$t_u[0] = $dom_5->getComment('user_prf_def_lib'); // niveau par defaut
				$qu = $dom_5->loadUsedUserProfiles();
				$ru = pmb_mysql_query($qu);
				if (pmb_mysql_num_rows($ru)) {
					while ( ($row = pmb_mysql_fetch_object($ru)) ) {
						$t_u[$row->prf_id] = $row->prf_name;
					}
				}
					
				// recuperation des controles dependants de l'utilisateur
				$t_ctl = $dom_5->getControls(0);
					
				// recuperation des droits
				$t_rights = $dom_5->getResourceRights($id);
					
				if (count($t_u)) {
	
					$h_tab = "<div class='dom_div'><table class='dom_tab'><tr>";
					foreach ( $t_u as $k => $v ) {
						$h_tab .= "<th class='dom_col'>" . htmlentities($v, ENT_QUOTES, $charset) . "</th>";
					}
					$h_tab .= "</tr><!-- rights_tab --></table></div>";
	
					$c_tab = '<tr>';
					foreach ( $t_u as $k => $v ) {
							
						$c_tab .= "<td><table style='border:1px solid;'><!-- rows --></table></td>";
						$t_rows = "";
						foreach ( $t_ctl as $k2 => $v2 ) {
	
							$t_rows .= "
								<tr>
									<td style='width:25px;' ><input type='checkbox' id='chk_rights_5_".$k."_".$k2."' name='chk_rights[5][" . $k . "][" . $k2 . "]' value='1' ";
							if (isset($t_rights[$k]) && isset($t_rights[$k][$res_prf]) && ($t_rights[$k][$res_prf] & (pow(2, $k2 - 1)))) {
								$t_rows .= "checked='checked' ";
							}
							if (($user_rights & 1) == 0) {
								$t_rows .= "disabled='disabled' /></td>
									<td>" . htmlentities($v2, ENT_QUOTES, $charset) . "</td>
								</tr>";
							} else {
							    $t_rows .= "/></td>
									<td><label for='chk_rights_5_".$k."_".$k2."' >" . htmlentities($v2, ENT_QUOTES, $charset) . "</label></td>
								</tr>";
							}
						}
						$c_tab = str_replace('<!-- rows -->', $t_rows, $c_tab);
					}
					$c_tab .= "</tr>";
				}
				$h_tab = str_replace('<!-- rights_tab -->', $c_tab, $h_tab);
				;
				$r_form = str_replace('<!-- rights_tab -->', $h_tab, $r_form);
			} else {
				$r_form = str_replace('<!-- prf_rad -->', htmlentities($msg['dom_prf_unknown'], ENT_QUOTES, $charset), $r_form);
				$r_form = str_replace('<!-- r_rad -->', htmlentities($msg['dom_rights_unknown'], ENT_QUOTES, $charset), $r_form);
			}
			$form .= $r_form;
		}
		return $form;
	}
	
	public static function save_current_scenario($current_scenario) {
		if (!empty($current_scenario)) {
			//on stocke pour l'uri du scenario pour calculer les droits
			$uri = 'http://www.pmbservices.fr/ca/Scenario#'.$current_scenario;
			$scenario_uri_id = onto_common_uri::set_new_uri($uri);
			contribution_area_scenario::save_rights(true, $scenario_uri_id);
		}
	}
	
	public static function delete($id) {
		self::get_graphstore();
		
		$id = intval($id);
		$uri = onto_common_uri::get_uri($id);
		
		$succes = self::$graphstore->query('
			delete {
				<'.$uri.'> ?p ?o
			}
		');
		if (!$succes) {
		    var_dump(self::$graphstore->get_errors());
		}
		
		$succes = self::$graphstore->query('
			delete {
				?s ?p <'.$uri.'>
			}
		');
		if (!$succes) {
		    var_dump(self::$graphstore->get_errors());
		}
		
		pmb_mysql_query('delete from onto_uri where uri_id = '.$id);
		
		//suppression des droits d'acces empr_contribution_area_scenario
		$query_acces = "show tables like 'acces_res_5'";
		$result_acces = pmb_mysql_query($query_acces);
		if($result_acces && pmb_mysql_num_rows($result_acces)) {
    		$requete = "delete from acces_res_5 where res_num=".$id;
    		@pmb_mysql_query($requete);
		}
	}
} // end of contribution_area_scenario
