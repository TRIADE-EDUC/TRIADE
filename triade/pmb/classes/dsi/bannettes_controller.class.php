<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannettes_controller.class.php,v 1.14 2023/09/12 12:27:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/bannette.class.php");
require_once($class_path."/dsi/readers_bannette_controller.class.php");

class bannettes_controller extends lists_controller {
	
	protected static $model_class_name = 'bannette';
	protected static $list_ui_class_name = 'list_bannettes_ui';
	
	public static function proceed($id=0) {
		global $msg;
		global $suite;
		global $pmb_javascript_office_editor, $base_path;
		global $form_actif, $majautocateg, $majautogroupe, $categorie_lecteurs, $groupe_lecteurs;
		global $faire, $id_classement, $bannette_equation;
		
		switch($suite) {
			case 'acces':
				$model_instance = static::get_model_instance($id);
				print $model_instance->show_form();
				
				if ($pmb_javascript_office_editor) {
					print $pmb_javascript_office_editor ;
					print "<script type='text/javascript'>
                        pmb_include('$base_path/javascript/tinyMCE_interface.js');
                    </script>";
				}
				break;
			case 'add':
				$model_instance = static::get_model_instance($id);
				print $model_instance->show_form();
				if ($pmb_javascript_office_editor) {
					print $pmb_javascript_office_editor ;
					print "<script type='text/javascript'>
                        pmb_include('$base_path/javascript/tinyMCE_interface.js');
                    </script>";
				}
				break;
			case 'delete':
				$model_instance = static::get_model_instance($id);
				print $model_instance->delete();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'update':
				$model_instance = static::get_model_instance($id);
				$anc_categorie_lecteurs=    $model_instance->categorie_lecteurs ;
				$anc_groupe_lecteurs=    	$model_instance->groupe_lecteurs ;
				if ($form_actif) {
					$model_instance->set_properties_from_form();
					$model_instance->save();
					if (!$id){
						$id = $model_instance->id_bannette;
					}
					if (($majautocateg || $majautogroupe) && $id) {
						if(!count($categorie_lecteurs)) $categorie_lecteurs=array();
						if(!count($groupe_lecteurs)) $groupe_lecteurs=array();
						$new_categorie_lecteurs = $model_instance->categorie_lecteurs;
						$new_groupe_lecteurs = $groupe_lecteurs;
						if ($majautocateg && count($anc_categorie_lecteurs)){
							$req_lec = "select id_empr from empr where empr_categ in (" . implode(',', $anc_categorie_lecteurs) . ")";
							$res_lec=pmb_mysql_query($req_lec) ;
							while ($lec=pmb_mysql_fetch_object($res_lec)) {
								pmb_mysql_query("delete from bannette_abon where num_empr='$lec->id_empr' and num_bannette='$id'") ;
							}
						}
						if ($majautogroupe && count($anc_groupe_lecteurs)) {
							$req_lec = "select id_empr from empr left join empr_groupe on (empr.id_empr=empr_groupe.empr_id)
							where groupe_id in (".implode(',',$anc_groupe_lecteurs).")";
							$res_lec=pmb_mysql_query($req_lec) ;
							while ($lec=pmb_mysql_fetch_object($res_lec)) {
								pmb_mysql_query("delete from bannette_abon where num_empr='$lec->id_empr' and num_bannette='$id'") ;
							}
						}
						if ($majautocateg && count($new_categorie_lecteurs)) {
							$req_lec = "select distinct id_empr from empr left join empr_groupe on (empr.id_empr=empr_groupe.empr_id)
							where empr_categ in (".implode(',',$new_categorie_lecteurs).")";
							$res_lec=pmb_mysql_query($req_lec) ;
							while ($lec=pmb_mysql_fetch_object($res_lec)) {
							    pmb_mysql_query("delete from bannette_abon where num_empr='".$lec->id_empr."' and num_bannette='$id'") ;
								pmb_mysql_query("insert into bannette_abon (num_bannette, num_empr) values('$id', '$lec->id_empr')") ;
							}
						}
						if ($majautogroupe && count($new_groupe_lecteurs)) {
							$req_lec = "select id_empr from empr left join empr_groupe on (empr.id_empr=empr_groupe.empr_id)
	    					where groupe_id in (" . implode(',', $new_groupe_lecteurs) . ")";
							$res_lec=pmb_mysql_query($req_lec) ;
							while ($lec=pmb_mysql_fetch_object($res_lec)) {
							    pmb_mysql_query("delete from bannette_abon where num_empr='".$lec->id_empr."' and num_bannette='$id'") ;
								pmb_mysql_query("insert into bannette_abon (num_bannette, num_empr) values('$id', '$lec->id_empr')") ;
							}
						}
					}
				}
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'duplicate':
				print "<h1>$msg[catal_duplicate_bannette]</h1>";
				// routine de copie
				$model_instance = static::get_model_instance($id);
				$model_instance->id_bannette=0 ;
				$model_instance->date_last_remplissage="";
				$model_instance->aff_date_last_remplissage="";
				$model_instance->date_last_envoi="";
				$model_instance->aff_date_last_envoi="";
				$model_instance->id_bannette_origine = $id;
				print pmb_bidi($model_instance->show_form());
				if ($pmb_javascript_office_editor) {
					print $pmb_javascript_office_editor ;
					print "<script type='text/javascript'>
                        pmb_include('$base_path/javascript/tinyMCE_interface.js');
                    </script>";
				}
				break;
			case 'search':
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'affect_equation':
				if ($faire=="enregistrer") {
					//Enregistrer les affectations
					// selectionner les equations affichées
					if ($id_classement>0) $equ = "select id_equation from equations where num_classement='$id_classement' and proprio_equation=0";
					if ($id_classement==0) $equ = "select id_equation from equations where proprio_equation=0 ";
					if ($id_classement==-1) $equ = "select id_equation from equations, bannette_equation where proprio_equation=0 and num_bannette='$id' and num_equation=id_equation";
					$res = pmb_mysql_query($equ) or die (pmb_mysql_error()." $equ ") ;
					if (empty($bannette_equation)) $bannette_equation = array();
					while ($equa=pmb_mysql_fetch_object($res)) {
						pmb_mysql_query("delete from bannette_equation where num_equation='$equa->id_equation' and num_bannette='$id' ") ;
						$as = array_search($equa->id_equation,$bannette_equation) ;
						if (($as!==false) && ($as!==null) ) pmb_mysql_query("insert into bannette_equation set num_equation='$equa->id_equation', num_bannette='$id'") ;
					}
				}
				$model_instance = static::get_model_instance($id);
				print bannette_equation ($model_instance->nom_bannette, $id) ;
				break;
			case 'affect_lecteurs':
				//Contournement de la mécanique
				global $id_bannette;
				readers_bannette_controller::set_id_bannette($id_bannette);
				readers_bannette_controller::proceed((!empty($_GET['id']) ? $_GET['id'] : 0));
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		return new static::$list_ui_class_name(array('proprio_bannette' => 0));
	}
}// end class
