<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_dsi_ui.class.php,v 1.1.10.1.2.1 2025/03/27 10:03:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_dsi_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'diffuser':
				switch(static::$sub) {
					case 'lancer':
						$title .= $msg['dsi_dif_auto_titre'];
						break;
					case 'auto':
						$title .= $msg['dsi_dif_auto'];
						break;
					case 'manu':
						$title .= $msg['dsi_dif_manu'];
						break;
					case 'history':
					    $title .= $msg['dsi_menu_dif_history'];
						break;
					default:
						break;
				}
				break;
			case 'bannettes':
				switch (static::$sub) {
					case 'pro':
						$title .= $msg['dsi_ban_pro'];
						break;
					case 'abo':
						$title .= $msg['dsi_ban_abo'];
						global $id_empr;
						if ($id_empr) {
							$result_empr = pmb_mysql_query("select concat(ifnull(concat(empr_nom,' '),''),empr_prenom) as nom_prenom from empr where id_empr=$id_empr") ;
							$nom_prenom_abo = pmb_mysql_result($result_empr, '0', 'nom_prenom');
							if ($nom_prenom_abo) {
								$title .= " : ".$nom_prenom_abo;
							}
						}
						break;
					default:
						break;
				}
				break;
			case 'equations':
				$title .= $msg['dsi_equ_gestion'];
				break;
			case 'options':
				$title .= $msg['dsi_opt_class'];
				break;
			case 'fluxrss':
				$title .= $msg['dsi_rss_titre'];
				break;
			case 'docwatch':
				break;
			default:
				break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		
		$sub_title = "";
		switch (static::$categ) {
			case 'diffuser':
			case 'bannettes':
				break;
			default:
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		
	}
}