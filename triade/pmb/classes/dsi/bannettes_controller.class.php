<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannettes_controller.class.php,v 1.3 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/bannette.class.php");
require_once($class_path."/list/bannettes/list_bannettes_diffusion_ui.class.php");

class bannettes_controller{
	
	protected $id;
	
	public function __construct($id=0) {
	    $this->id = (int) $id;
	}
	
	public static function proceed_module_diffusion($suite) {
		global $msg;
		global $sub;
		global $form_cb;
		global $id_classement;
		global $liste_bannette;
		global $database_window_title;
		
		$action_diff_aff = '';
		// récupérer les bannettes cochées
		if (!$liste_bannette) $liste_bannette = array() ;
		for ($iba=0 ; $iba < sizeof($liste_bannette) ; $iba++) {
			$bannette = new bannette($liste_bannette[$iba]) ;
			switch($suite) {
				case "vider" :
					$action_diff_aff .= $msg['dsi_dif_vidage'].": ".$bannette->nom_bannette."<br />" ;
					$bannette->vider();
					break ;
				case "remplir" :
					$action_diff_aff .= $msg['dsi_dif_remplissage'].": ".$bannette->nom_bannette ;
					$action_diff_aff .= $bannette->remplir();
					$bannette->purger();
					break ;
				case "diffuser" :
					$action_diff_aff .= "<strong>".$msg['dsi_dif_diffusion'].": ".$bannette->nom_bannette."</strong><br />" ;
					$action_diff_aff .= $bannette->diffuser();
					break ;
				case "visualiser" :
					$action_diff_aff .= "<h3>".$msg['dsi_dif_ban_contenu'].": ".$bannette->nom_bannette."</h3>" ;
					$action_diff_aff .= $bannette->aff_contenu_bannette("./dsi.php?categ=diffuser&sub=auto", 0);
					break ;
				case "full_auto" :
					$action_diff_aff .= $msg['dsi_dif_vidage'].": ".$bannette->nom_bannette."<br />" ;
					if(!$bannette->limite_type)$action_diff_aff .= $bannette->vider();
					$action_diff_aff .= $msg['dsi_dif_remplissage'].": ".$bannette->nom_bannette ;
					$action_diff_aff .= $bannette->remplir();
					$bannette->purger();
					$action_diff_aff .= "<strong>".$msg['dsi_dif_diffusion'].": ".$bannette->nom_bannette."</strong><br />" ;
					$action_diff_aff .= $bannette->diffuser();
					break ;
				case "exporter" :
					$action_diff_aff .= "<script>openPopUp('./print_dsi.php?id_bannette=$bannette->id_bannette', 'Impression de DSI : $bannette->id_bannette ', 500, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes')</script>" ;
					break ;
				case "gen_document" :
					$action_diff_aff .= "<script>openPopUp('./print_doc_dsi.php?id_bannette=$bannette->id_bannette', 'Document DSI : $bannette->id_bannette ', 800, 600, -2, -2, 'scrollbars=yes, toolbar=yes, dependent=yes, resizable=yes')</script>" ;
					break ;
			}
		}
		
		switch($suite) {
			case "search":
			case "vider" :
			case "remplir" :
			case "diffuser" :
			case "full_auto" :
			case "exporter" :
			case "gen_document" :
				if ($action_diff_aff) print "<h1>".$msg['dsi_dif_action_effectuee']." : </h1>".$action_diff_aff ;
				static::proceed_diffusion_list();
				break ;
			case "visualiser" :
				if ($action_diff_aff) print $action_diff_aff;
				break ;
			default:
				echo window_title($database_window_title.$msg['dsi_dif_auto']);
				static::proceed_diffusion_list();
				break;
		}
	}
	
	public static function proceed_diffusion_list() {
		global $sub;
		global $form_cb;
		global $id_classement;
		
		switch($sub) {
			case 'auto':
				$list_bannettes_diffusion_ui = new list_bannettes_diffusion_ui(array('sub' => $sub, 'auto' => 1));
				break;
			case 'lancer':
				$list_bannettes_diffusion_ui = new list_bannettes_diffusion_ui(array('sub' => $sub, 'auto' => 1));
				break;
			case 'manu':
				$list_bannettes_diffusion_ui = new list_bannettes_diffusion_ui(array('sub' => $sub, 'auto' => 0));
				break;
		}
		print $list_bannettes_diffusion_ui->get_display_list();
	}
	
	public static function proceed_module_equations($suite) {
		switch($suite) {
			case 'acces':
				$equation = new equation($id_equation) ;
				print $equation->show_form();
				break;
			case 'add':
				$equation = new equation(0) ;
				print $equation->show_form();
				break;
			case 'transform':
				if ($id_equation) {
					$equation = new equation($id_equation) ;
					$equation->requete = stripslashes($requete);
				} else {
					$equation = new equation(0) ;
					$equation->num_classement = 1;
					$equation->nom_equation = "";
					$equation->comment_equation = "";
					$equation->requete =	stripslashes($requete);
					$equation->proprio_equation = 0;
				}
				print $equation->show_form();
				break;
			case 'delete':
				$equation = new equation($id_equation) ;
				$equation->delete();
				print get_equation ($msg['dsi_equ_search'], $msg['dsi_equ_search_nom'], './dsi.php?categ=equations', stripslashes($form_cb));
				print pmb_bidi(dsi_list_equations($form_cb)) ;
				break;
			case 'update':
				if(!isset($proprio_equation)) $proprio_equation = 0;
				$equation = new equation($id_equation);
				$equation->set_properties_from_form();
				$equation->save();
				print get_equation ($msg['dsi_equ_search'], $msg['dsi_equ_search_nom'], './dsi.php?categ=equations', stripslashes($nom_equation));
				print pmb_bidi(dsi_list_equations($nom_equation)) ;
				break;
			case 'search':
				print get_equation ($msg['dsi_equ_search'], $msg['dsi_equ_search_nom'], './dsi.php?categ=equations', stripslashes($form_cb));
				print pmb_bidi(dsi_list_equations($form_cb)) ;
				break;
			default:
				echo window_title($database_window_title.$msg['dsi_menu_title']);
				print get_equation ($msg['dsi_equ_search'], $msg['dsi_equ_search_nom'], './dsi.php?categ=equations', stripslashes($form_cb));
				print pmb_bidi(dsi_list_equations($form_cb)) ;
				break;
		}
	}
}// end class
