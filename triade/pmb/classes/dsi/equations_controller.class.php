<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: equations_controller.class.php,v 1.3 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/equation.class.php");

class equations_controller{
	
	protected $id;
	
	public function __construct($id=0) {
	    $this->id = (int) $id;
	}
	
	public function proceed($suite) {
		global $msg;
		global $requete;
		global $form_cb;
		global $nom_equation;
		global $proprio_equation;
		global $database_window_title;
		
		switch($suite) {
			case 'acces':
				$equation = $this->get_object_instance();
				print $equation->show_form();
				break;
			case 'add':
				$equation = $this->get_object_instance();
				print $equation->show_form();
				break;
			case 'transform':
				$equation = $this->get_object_instance();
				if (!$this->id) {
					$equation->num_classement = 1;
					$equation->nom_equation = "";
					$equation->comment_equation = "";
					$equation->proprio_equation = 0;
				}
				$equation->requete = stripslashes($requete);
				print $equation->show_form();
				break;
			case 'delete':
				$equation = $this->get_object_instance();
				$equation->delete();
				print get_equation ($msg['dsi_equ_search'], $msg['dsi_equ_search_nom'], './dsi.php?categ=equations', stripslashes($form_cb));
				print pmb_bidi(dsi_list_equations($form_cb)) ;
				break;
			case 'update':
				if(!isset($proprio_equation)) $proprio_equation = 0;
				$equation = $this->get_object_instance();
				$equation->set_properties_from_form();
				$equation->save();
				print get_equation ($msg['dsi_equ_search'], $msg['dsi_equ_search_nom'], './dsi.php?categ=equations', stripslashes($nom_equation));
				print pmb_bidi(dsi_list_equations($nom_equation));
				break;
			case 'duplicate':
				$equation = $this->get_object_instance();
				$equation->id_equation = 0;
				print $equation->show_form();
				break;
			case 'search':
				print get_equation ($msg['dsi_equ_search'], $msg['dsi_equ_search_nom'], './dsi.php?categ=equations', stripslashes($form_cb));
				print pmb_bidi(dsi_list_equations($form_cb));
				break;
			default:
				echo window_title($database_window_title.$msg['dsi_menu_title']);
				print get_equation ($msg['dsi_equ_search'], $msg['dsi_equ_search_nom'], './dsi.php?categ=equations', stripslashes($form_cb));
				print pmb_bidi(dsi_list_equations($form_cb));
				break;
		}
	}
	
	public function get_object_instance() {
		return new equation($this->id);
	}	
}// end class
