<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: configuration_acquisition_controller.class.php,v 1.2 2022/07/08 12:25:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class configuration_acquisition_controller extends configuration_controller {
	
	protected static $id_entity;
	
	public static function redirect_display_list() {
		global $sub;
		
		switch ($sub) {
			case 'budget':
				if(headers_sent()) {
					print "
				<script type='text/javascript'>
					window.location.href='".static::get_url_base()."&action=list&id_bibli=".static::$id_entity."';
				</script>";
				} else {
					header('Location: '.static::get_url_base().'&action=list&id_bibli='.static::$id_entity);
				}
				break;
			default:
				parent::redirect_display_list();
				break;
		}
	}
	
	public static function redirect_display_form($id) {
		if(headers_sent()) {
			print "
				<script type='text/javascript'>
					window.location.href='".static::get_url_base()."&action=modif&id_bibli=".static::$id_entity."&id_bud=".$id."';
				</script>";
		} else {
			header('Location: '.static::get_url_base().'&action=modif&id_bibli='.static::$id_entity.'&id_bud='.$id);
		}
	}
	
	//Vérification qu'un exercice actif existe pour création budget
	public static function verif_exercice() {
		global $charset;
		global $msg;
		
		$q = entites::getCurrentExercices(static::$id_entity);
		$r = pmb_mysql_query($q);
		
		if (pmb_mysql_num_rows($r)) return;
		
		//Pas d'exercice actif pour la bibliothèque
		$error_msg = htmlentities($msg["acquisition_err_exer"], ENT_QUOTES, $charset)."<div class='row'></div>";
		error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
		die;
	}
	
	public static function proceed($id=0) {
		global $msg, $charset;
		global $sub, $action;
		global $libelle, $exer, $seuil, $sel_typ, $mnt_bud;
		
		$id = intval($id);
		switch ($sub) {
			case 'budget':
				switch ($action) {
					case 'list':
						//Rappel du nom de l'etablissement
						$biblio = new entites(static::$id_entity);
						print "<div class='row'><label class='etiquette'>".htmlentities($biblio->raison_sociale,ENT_QUOTES,$charset)."</label></div>";
						print list_configuration_acquisition_budget_ui::get_instance(array('num_entite' => static::$id_entity))->get_display_list();
						break;
					case 'add':
						static::verif_exercice();
						$model_instance = static::get_model_instance($id);
						print $model_instance->get_form(static::$id_entity);
						break;
					case 'modif':
						$model_class_name = static::$model_class_name;
						if ($model_class_name::exists($id)) {
							$model_instance = static::get_model_instance($id);
							print $model_instance->get_form(static::$id_entity);
						} else {
							static::redirect_display_list();
						}
						break;
					case 'activation':
						$model_instance = static::get_model_instance($id);
						$model_instance->statut = 1;
						$model_instance->save();
						if ($id) {
							static::redirect_display_list();
						} else {
							static::redirect_display_form($model_instance->id_budget);
						}
						break;
					case 'cloture':
						$model_instance = static::get_model_instance($id);
						$model_instance->statut = 2;
						$model_instance->save();
						if ($id) {
							static::redirect_display_list();
						} else {
							static::redirect_display_form($model_instance->id_budget);
						}
						break;
					case 'save':
					case 'update':
						$model_class_name = static::$model_class_name;
						// vérification validité des données fournies.
						//Pas deux libelles de budgets identiques pour la même entité et le même exercice
						$nbr = $model_class_name::existsLibelle(static::$id_entity, stripslashes($libelle), $exer, $id);
						if ( $nbr > 0 ) {
							error_form_message($libelle.$msg["acquisition_budg_already_used"]);
							break;
						}
						//Seuil d'alerte compris entre 0 et 100
						if (!is_numeric($seuil) || $seuil < 0 || $seuil > 100 ) {
							error_form_message($libelle.$msg["acquisition_budg_seu_error"]);
							break;
						}
						//Montant du budget compris entre 0.00 et 999999.99 si global
						if ( (!$id && $sel_typ==1) || $id ) {
							if ( isset($mnt_bud) && $mnt_bud && (!is_numeric($mnt_bud) || $mnt_bud < 0.00 || $mnt_bud > 9999999999.99 )) {
								error_form_message($libelle." ".$msg["acquisition_bud_mnt_error"]);
								break;
							}
						}
						$model_instance = static::get_model_instance($id);
						$model_instance->set_properties_from_form();
						$model_instance->save();
						
						if ($id) {
							static::redirect_display_list();
						} else {
							static::redirect_display_form($model_instance->id_budget);
						}
						break;
					case 'del':
					case 'delete':
						if($id) {
							$model_class_name = static::$model_class_name;
							$total1 = $model_class_name::hasLignes($id);
							$total2 = $model_class_name::countRubriques($id);
							
							if ( ($total1==0) &&  ($total2==0) ) {
								$model_class_name::delete($id);
								static::redirect_display_list();
							} else {
								$msg_suppr_err = $msg['acquisition_budg_used'] ;
								if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_budg_used_lg'] ;
								if ($total2) $msg_suppr_err .= "<br />- ".$msg['acquisition_budg_used_rubr'] ;
								error_message($msg[321], $msg_suppr_err, 1, static::get_url_base().'&action=list&id_bibli='.static::$id_entity);
							}
							
						} else {
							static::redirect_display_list();
						}
						break;
					case 'dup' :
					case 'duplicate' :
						$model_class_name = static::$model_class_name;
						if ($model_class_name::exists($id)) {
							$id_new_bud = $model_class_name::duplicate($id);
							static::redirect_display_form($id_new_bud);
						} else {
							static::redirect_display_list();
						}
						break;
					default:
						show_list_biblio();
						break;
				}
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
	
	public static function set_id_entity($id_entity) {
		static::$id_entity = intval($id_entity);
	}
}