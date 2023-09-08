<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user.class.php,v 1.20 2019-06-10 08:57:11 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/marc_table.class.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/actes.class.php");
require_once($class_path."/suggestions_map.class.php");
require_once($class_path."/lignes_actes_statuts.class.php");
require_once($base_path."/admin/connecteurs/in/agnostic/agnostic.class.php");

require_once($class_path."/onto/common/onto_common_uri.class.php");
require_once($class_path."/onto/onto_store_arc2.class.php");
require_once($class_path."/onto/onto_handler.class.php");
require_once($class_path."/onto/onto_root_ui.class.php");
require_once($class_path."/onto/common/onto_common_ui.class.php");
require_once($class_path."/onto/common/onto_common_controler.class.php");
require_once($class_path."/onto/skos/onto_skos_concept_ui.class.php");
require_once($class_path."/onto/skos/onto_skos_controler.class.php");
require_once($class_path."/onto/onto_param.class.php");
require_once($class_path.'/scan_request/scan_request_admin_status.class.php');
require_once($class_path.'/notice_relations_collection.class.php');
require_once($class_path.'/printer/raspberry.class.php');

class user {
	
	protected $userid;
	
	protected $username;
	protected $pwd;
	protected $pwd_encrypted;
	protected $nom;
	protected $prenom;
	protected $rights;
	protected $user_lang;
	
	protected $nb_per_page_search;
	protected $nb_per_page_select;
	protected $nb_per_page_gestion;
	
	protected $explr_invisible;
	protected $explr_visible_mod;
	protected $explr_visible_unmod;
	
	protected $user_email;
	
	protected $user_alert_resamail;
	protected $user_alert_demandesmail;
	protected $user_alert_subscribemail;
	protected $user_alert_serialcircmail;
	protected $user_alert_suggmail;
	
	protected $grp_num;
	
	protected $duplicate_from_userid;
	
	public function __construct($userid=0) {
		$this->userid = $userid+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		global $lang;
		
		$query = "SELECT username, nom, prenom, rights, userid, user_lang,
			nb_per_page_search, nb_per_page_select, nb_per_page_gestion,
			param_popup_ticket, param_sounds, user_email,
			user_alert_resamail, user_alert_demandesmail, user_alert_subscribemail, user_alert_serialcircmail, user_alert_suggmail, 
			explr_invisible, explr_visible_mod, explr_visible_unmod, grp_num 
			FROM users WHERE userid='".$this->userid."' LIMIT 1 ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$this->username = $row->username;
			$this->nom = $row->nom;
			$this->prenom = $row->prenom;
			$this->rights = $row->rights;
			$this->user_lang = $row->user_lang;
			$this->nb_per_page_search = $row->nb_per_page_search;
			$this->nb_per_page_select = $row->nb_per_page_select;
			$this->nb_per_page_gestion = $row->nb_per_page_gestion;
			$this->user_email = $row->user_email;
			$this->user_alert_resamail = $row->user_alert_resamail;
			$this->user_alert_demandesmail = $row->user_alert_demandesmail;
			$this->user_alert_subscribemail = $row->user_alert_subscribemail;
			$this->user_alert_serialcircmail = $row->user_alert_serialcircmail;
			$this->user_alert_suggmail = $row->user_alert_suggmail;
			$this->explr_invisible = $row->explr_invisible;
			$this->explr_visible_mod = $row->explr_visible_mod;
			$this->explr_visible_unmod = $row->explr_visible_unmod;
			$this->grp_num = $row->grp_num;
		} else {
			$this->username = '';
			$this->pwd = '';
			$this->pwd_encrypted = FALSE;
			$this->nom = '';
			$this->prenom = '';
			$this->rights = 3;
			$this->user_lang = $lang;
			$this->nb_per_page_search = 10;
			$this->nb_per_page_select = 10;
			$this->nb_per_page_gestion = 10;
			$this->user_alert_resamail = 0;
			$this->user_alert_demandesmail = 0;
			$this->user_alert_subscribemail = 0;
			$this->user_alert_serialcircmail = 0;
			$this->user_alert_suggmail = 0;
			$this->explr_invisible = 0;
			$this->explr_visible_mod = 0;
			$this->explr_visible_unmod = 0;
			$this->grp_num = FALSE;
		}
	}
	
	public function set_userid($userid=0) {
		$this->userid = $userid+0;
	}
	
	public function set_duplicate_from_userid($duplicate_from_userid=0) {
		$this->duplicate_from_userid = $duplicate_from_userid+0;
	}
	
	public function get_user_form($form_param_default="") {
		global $base_path;
		global $msg;
		global $admin_user_form;
		global $charset;
		global $password_field;
		global $include_path ;
		global $demandes_active;
		global $opac_websubscribe_show,$acquisition_active,$opac_serialcirc_active;
	
		$user_encours=$_COOKIE["PhpMyBibli-LOGIN"];
		if(!$this->userid) $admin_user_form =str_replace('!!button_duplicate!!', "", $admin_user_form);
		else $admin_user_form =str_replace('!!button_duplicate!!', " <input class='bouton' type='button' value=' ".$msg['duplicate']." ' onclick=\"window.location='".$base_path."/admin.php?categ=users&sub=users&action=duplicate&id=!!id!!'\" /> ", $admin_user_form);
	
		if(($this->userid == 1) || ($this->username == $user_encours) || ($this->userid==0)) // $id est admin ou $login est l'utilisateur en cours
			$admin_user_form =str_replace('!!bouton_suppression!!', "", $admin_user_form);
		else
			$admin_user_form =str_replace('!!bouton_suppression!!', " <input class='bouton' type='button' value=' $msg[63] ' onClick=\"javascript:confirmation_delete(!!id!!,'".$this->username."')\" /> ", $admin_user_form);
	
		if(!$this->userid) $title = $msg[85]; // ajout
		else $title = $msg[90]; 	// modification
	
		$admin_user_form = str_replace('!!id!!', $this->userid, $admin_user_form);
		$admin_user_form = str_replace('!!title!!', htmlentities($title,ENT_QUOTES,$charset), $admin_user_form);
		$admin_user_form = str_replace('!!login!!', htmlentities($this->username,ENT_QUOTES,$charset), $admin_user_form);
		$admin_user_form = str_replace('!!nom!!', htmlentities($this->nom,ENT_QUOTES,$charset), $admin_user_form);
		$admin_user_form = str_replace('!!prenom!!', htmlentities($this->prenom,ENT_QUOTES,$charset), $admin_user_form);
		$admin_user_form = str_replace('!!nb_per_page_search!!', $this->nb_per_page_search, $admin_user_form);
		$admin_user_form = str_replace('!!nb_per_page_select!!', $this->nb_per_page_select, $admin_user_form);
		$admin_user_form = str_replace('!!nb_per_page_gestion!!', $this->nb_per_page_gestion, $admin_user_form);
	
		if(!$this->userid) $admin_user_form = str_replace('!!password_field!!', $password_field, $admin_user_form);
		else $admin_user_form = str_replace('!!password_field!!', '', $admin_user_form);
	
		$this->rights & ADMINISTRATION_AUTH ? $admin_flg_form = "checked " : $admin_flg_form = "";
		$this->rights & CIRCULATION_AUTH ? $circ_flg_form = "checked " : $circ_flg_form = "";
		$this->rights & CATALOGAGE_AUTH ? $catal_flg_form = "checked " : $catal_flg_form = "";
		$this->rights & AUTORITES_AUTH ? $auth_flg_form = "checked " : $auth_flg_form = "";
		$this->rights & EDIT_AUTH ? $edit_flg_form = "checked " : $edit_flg_form = "";
		$this->rights & EDIT_FORCING_AUTH ? $edit_forcing_flg_form = "checked " : $edit_forcing_flg_form = "";
		$this->rights & SAUV_AUTH ? $sauv_flg_form = "checked " : $sauv_flg_form = "";
		$this->rights & DSI_AUTH ? $dsi_flg_form = "checked " : $dsi_flg_form = "";
		$this->rights & PREF_AUTH ? $pref_flg_form = "checked " : $pref_flg_form = "";
		$this->rights & ACQUISITION_ACCOUNT_INVOICE_AUTH ? $acquisition_account_invoice_flg = "checked " : $acquisition_account_invoice_flg = "";
		$this->rights & ACQUISITION_AUTH ? $acquisition_flg_form = "checked " : $acquisition_flg_form = "";
		$this->rights & RESTRICTCIRC_AUTH ? $restrictcirc_flg_form = "checked " : $restrictcirc_flg_form = "";
		$this->rights & THESAURUS_AUTH ? $thesaurus_flg_form = "checked " : $thesaurus_flg_form = "";
		$this->rights & TRANSFERTS_AUTH ? $transferts_flg_form = "checked " : $transferts_flg_form = "";
		$this->rights & EXTENSIONS_AUTH ? $extensions_flg_form = "checked " : $extensions_flg_form = "";
		$this->rights & DEMANDES_AUTH ? $demandes_flg_form = "checked " : $demandes_flg_form = "";
		$this->rights & CMS_AUTH ? $cms_flg_form = "checked " : $cms_flg_form = "";
		$this->rights & CMS_BUILD_AUTH ? $cms_build_flg_form = "checked " : $cms_build_flg_form = "";
		$this->rights & FICHES_AUTH ? $fiches_flg_form = "checked " : $fiches_flg_form = "";
		$this->rights & CATAL_MODIF_CB_EXPL_AUTH ? $modif_cb_expl_flg_form = "checked " : $modif_cb_expl_flg_form = "";
		$this->rights & SEMANTIC_AUTH ? $semantic_flg_form = "checked " : $semantic_flg_form = "";
		$this->rights & CONCEPTS_AUTH ? $concepts_flg_form = "checked " : $concepts_flg_form = "";
		$this->rights & FRBR_AUTH ? $frbr_flg_form = "checked " : $frbr_flg_form = "";
		$this->rights & MODELLING_AUTH ? $modelling_flg_form = "checked " : $modelling_flg_form = "";
	
	
		$admin_user_form = str_replace('!!admin_flg!!', $admin_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!catal_flg!!', $catal_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!circ_flg!!', $circ_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!auth_flg!!', $auth_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!edit_flg!!', $edit_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!edit_forcing_flg!!', $edit_forcing_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!sauv_flg!!', $sauv_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!dsi_flg!!', $dsi_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!pref_flg!!', $pref_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!acquisition_account_invoice_flg!!', $acquisition_account_invoice_flg, $admin_user_form);
		$admin_user_form = str_replace('!!acquisition_flg!!', $acquisition_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!restrictcirc_flg!!', $restrictcirc_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!thesaurus_flg!!', $thesaurus_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!transferts_flg!!', $transferts_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!extensions_flg!!', $extensions_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!demandes_flg!!', $demandes_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!cms_flg!!', $cms_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!cms_build_flg!!', $cms_build_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!fiches_flg!!', $fiches_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!modif_cb_expl_flg!!', $modif_cb_expl_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!semantic_flg!!', $semantic_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!concepts_flg!!', $concepts_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!frbr_flg!!', $frbr_flg_form, $admin_user_form);
		$admin_user_form = str_replace('!!modelling_flg!!', $modelling_flg_form, $admin_user_form);
	
		if ($this->user_alert_resamail==1) $alert_resa_mail=" checked";
		else $alert_resa_mail="";
		$admin_user_form = str_replace('!!alter_resa_mail!!', $alert_resa_mail, $admin_user_form);
		if ($demandes_active) {
			if ($this->user_alert_demandesmail==1) $alert_demandes_mail=" checked";
			else $alert_demandes_mail="";
			$admin_user_form = str_replace('!!alert_demandes_mail!!', $alert_demandes_mail, $admin_user_form);
		}
		if ($opac_websubscribe_show) {
			if ($this->user_alert_subscribemail==1) $alert_subscribe_mail=" checked";
			else $alert_subscribe_mail="";
			$admin_user_form = str_replace('!!alert_subscribe_mail!!', $alert_subscribe_mail, $admin_user_form);
		}
		if ($opac_serialcirc_active) {
			if ($this->user_alert_serialcircmail==1) $alert_serialcirc_mail=" checked";
			else $alert_serialcirc_mail="";
			$admin_user_form = str_replace('!!alert_serialcirc_mail!!', $alert_serialcirc_mail, $admin_user_form);
		}
		if ($acquisition_active) {
			if ($this->user_alert_suggmail==1) $alert_sugg_mail=" checked";
			else $alert_sugg_mail="";
			$admin_user_form = str_replace('!!alert_sugg_mail!!', $alert_sugg_mail, $admin_user_form);
		}
		$admin_user_form = str_replace('!!user_email!!', $this->user_email, $admin_user_form);
	
		if(!$this->userid) $form_type = '1';
		else $form_type = '0';

		// récupération des codes langues
		$la = new XMLlist("$include_path/messages/languages.xml", 0);
		$la->analyser();
		$languages = $la->table;
	
		// constitution du sélecteur
		$selector = "<select name='user_lang'>	";
		foreach ($languages as $codelang => $libelle) {
			// arabe seulement si on est en utf-8
			if (($charset != 'utf-8' and $codelang != 'ar') or ($charset == 'utf-8')) {
				if($this->user_lang == $codelang) $selector .= "<option value='".htmlentities($codelang,ENT_QUOTES, $charset)."' SELECTED>".htmlentities($libelle,ENT_QUOTES, $charset)."</option>";
				else $selector .= "<option value='".htmlentities($codelang,ENT_QUOTES, $charset)."'>".htmlentities($libelle,ENT_QUOTES, $charset)."</option>";
			}
		}
		$selector .= '</select>';
	
		$admin_user_form = str_replace('!!select_lang!!', $selector, $admin_user_form);
		$admin_user_form = str_replace('!!form_type!!', $form_type, $admin_user_form);
		$admin_user_form = str_replace('!!form_param_default!!', $form_param_default, $admin_user_form);
	
		//groupes
		if ($this->grp_num !== FALSE) {
			$q = "select * from users_groups order by grp_name ";
			$sel_group = gen_liste($q, 'grp_id', 'grp_name', 'sel_group', '', $this->grp_num, '0', $msg[128], '0',$msg[128]);
			$sel_group = "<label class='etiquette'>".htmlentities($msg['admin_usr_grp_aff'], ENT_QUOTES, $charset).'</label><br />'.$sel_group;
			$admin_user_form = str_replace('<!-- sel_group -->', $sel_group, $admin_user_form);
		}
		return confirmation_delete("./admin.php?categ=users&sub=users&action=del&id=").
		$admin_user_form;
	}
	
	public static function get_field_selector($field, $selector) {
		global $msg;
		//TODO : Tester les deux points finaux du $msg
		return 
		"<div class='row userParam-row'>
			<div class='colonne60'>".$msg[$field]."&nbsp;:&nbsp;</div>
			<div class='colonne_suite'>".$selector."</div>
		</div>\n";
	}
	
	public function set_properties_from_form() {
		global $form_login, $form_pwd, $form_nom, $form_prenom;
		global $droits, $user_lang;
		global $form_nb_per_page_search, $form_nb_per_page_select, $form_nb_per_page_gestion;
		global $form_expl_visibilite;
		global $sel_group;
		global $form_user_email;
		global $form_user_alert_resamail, $form_user_alert_demandesmail, $form_user_alert_subscribemail, $form_user_alert_suggmail, $form_user_alert_serialcircmail;
		
		$form_login = stripslashes($form_login);
		if($this->username != $form_login && !empty($form_login)) {
			$this->username = $form_login;
		}
		if(!$this->userid && !empty($form_pwd)) {
			$this->pwd = stripslashes($form_pwd);
		}
		$this->nom = stripslashes($form_nom);
		$this->prenom = stripslashes($form_prenom);
		$this->rights = stripslashes($droits);
		$this->user_lang = $user_lang;
		$this->nb_per_page_search = $form_nb_per_page_search+0;
		$this->nb_per_page_select = $form_nb_per_page_select+0;
		$this->nb_per_page_gestion = $form_nb_per_page_gestion+0;
		
		$this->explr_invisible = $form_expl_visibilite[0];
		$this->explr_visible_mod = $form_expl_visibilite[1];
		$this->explr_visible_unmod = $form_expl_visibilite[2];
		if (isset($sel_group)) {
			$this->grp_num = $sel_group;
		}
		
		$this->user_email = stripslashes($form_user_email);
		
		$this->user_alert_resamail = intval($form_user_alert_resamail);
		$this->user_alert_demandesmail = intval($form_user_alert_demandesmail);
		$this->user_alert_subscribemail = intval($form_user_alert_subscribemail);
		$this->user_alert_suggmail = intval($form_user_alert_suggmail);
		$this->user_alert_serialcircmail = intval($form_user_alert_serialcircmail);
		
	}
	
	public function save() {
		global $form_style;
		global $form_deflt_docs_location;
		
		
		$dummy=array();
		$dummy[0] = "username='".addslashes($this->username)."'";
		$dummy[1] = "nom='".addslashes($this->nom)."'";
		$dummy[2] = "prenom='".addslashes($this->prenom)."'";
		$dummy[3] = "rights='".$this->rights."'";
		$dummy[4] = "user_lang='".addslashes($this->user_lang)."'";
		$dummy[5] = "nb_per_page_search='".$this->nb_per_page_search."'";
		$dummy[6] = "nb_per_page_select='".$this->nb_per_page_select."'";
		$dummy[7] = "nb_per_page_gestion='".$this->nb_per_page_gestion."'";
		$dummy[8] = "explr_invisible='".$this->explr_invisible."'";
		$dummy[9] = "explr_visible_mod='".$this->explr_visible_mod."'";
		$dummy[10]= "explr_visible_unmod='".$this->explr_visible_unmod."'";
		if (isset($this->grp_num)) {
			$dummy[11]= "grp_num='".$this->grp_num."'";
		}
		/* insérer ici la maj des param et deflt */
		if($this->userid || $this->duplicate_from_userid) {
			$i = 0;
			if($this->duplicate_from_userid) {
				$requete_param = "SELECT * FROM users WHERE userid='".$this->duplicate_from_userid."' LIMIT 1 ";
			} else {
				$requete_param = "SELECT * FROM users WHERE userid='".$this->userid."' LIMIT 1 ";
			}
			$res_param = pmb_mysql_query($requete_param);
			while ($i < pmb_mysql_num_fields($res_param)) {
				$field = pmb_mysql_field_name($res_param, $i) ;
				$field_deb = substr($field,0,6);
				switch ($field_deb) {
					case "deflt_" :
						if ($field == "deflt_styles") {
							$dummy[$i+12]=$field."='".$form_style."'";
						} elseif ($field == "deflt_docs_section") {
							$formlocid="f_ex_section".$form_deflt_docs_location ;
							global ${$formlocid};
							$dummy[$i+12]=$field."='".${$formlocid}."'";
						} else {
							$var_form = "form_".$field;
							global ${$var_form};
							$dummy[$i+12]=$field."='".(isset(${$var_form}) ? ${$var_form} : '')."'";
						}
						break;
					case "deflt2" :
						$var_form = "form_".$field;
						global ${$var_form};
						$dummy[$i+12]=$field."='".${$var_form}."'";
						break ;
					case "param_" :
						$var_form = "form_".$field;
						global ${$var_form};
						$dummy[$i+12]=$field."='".(isset(${$var_form}) ? ${$var_form} : '')."'";
						break ;
					case "value_" :
						$var_form = "form_".$field;
						global ${$var_form};
						$dummy[$i+12]=$field."='".(isset(${$var_form}) ? ${$var_form} : '')."'";
						break ;
					case "deflt3" :
						$var_form = "form_".$field;
						global ${$var_form};
						$dummy[$i+12]=$field."='".(isset(${$var_form}) ? ${$var_form} : '')."'";
						break ;
					case "xmlta_" :
						$var_form = "form_".$field;
						global ${$var_form};
						$dummy[$i+12]=$field."='".(isset(${$var_form}) ? ${$var_form} : '')."'";
						break ;
					case "speci_" :
						$speci_func = substr($field, 6);
						eval('$dummy[$i+12] = set_'.$speci_func.'();');
						break;
					default :
						break ;
				}
			
				$i++;
			}
		}
		
		$dummy[] = "user_email='".addslashes($this->user_email)."'";
		$dummy[] = "user_alert_resamail='".$this->user_alert_resamail."'";
		$dummy[] = "user_alert_demandesmail='".$this->user_alert_demandesmail."'";
		$dummy[] = "user_alert_subscribemail='".$this->user_alert_subscribemail."'";
		$dummy[] = "user_alert_suggmail='".$this->user_alert_suggmail."'";
		$dummy[] = "user_alert_serialcircmail='".$this->user_alert_serialcircmail."'";
		
		if(!$this->userid && $this->pwd) {
			if(!$this->pwd_encrypted) {
				$dummy[] = "pwd=password('".addslashes($this->pwd)."')";
			} else {
				$dummy[] = "pwd='".addslashes($this->pwd)."'";
			}
		}
		if(!empty($dummy)) {
			$set = join($dummy, ", ");
		}
		if(!empty($set)) {
			if($this->userid) {
				$set = "SET last_updated_dt=curdate(),".$set;
				$requete = "UPDATE users $set WHERE userid=".$this->userid." ";
			} else {
				$set = "SET create_dt=curdate(), last_updated_dt=curdate(),".$set;
				$requete = "INSERT INTO users ".$set;
			}
			$res = pmb_mysql_query($requete);
		}
	}
	
	public static function get_field_radio($field, $selected) {
		global $msg;
		return
		"<div class='row userParam-row'>
			<div class='colonne60'>".$msg[$field]."</div>\n
			<div class='colonne_suite'>
				".$msg[39]." <input type='radio' name='form_$field' value='0' ".(!$selected ? "checked='checked'" : "")." />
				".$msg[40]." <input type='radio' name='form_$field' value='1' ".($selected ? "checked='checked'" : "")." />
			</div>
		</div>\n";
	}
	
	public static function get_field_checkbox($field, $checked) {
		global $msg;
		return 
		"<div class='row userParam-row'>
			<div class='colonne60'>".$msg[$field]."</div>\n
			<div class='colonne_suite'>
				<input type='checkbox' class='checkbox' ".($checked==1 ? "checked='checked'" : "")." value='1' name='form_$field' />
			</div>
		</div>\n";
	}
	
	public static function get_form($id=0, $caller='') {
		global $msg, $charset;
		global $base_path, $class_path, $include_path;
		global $deflt_concept_scheme;
		global $pmb_droits_explr_localises;
		global $deflt_docs_location;
		global $cms_active;
		global $pmb_scan_request_activate;
		global $pmb_printer_name;
		
		//A verifier : si ce sont bien des globales
		global $explr_invisible;
		global $explr_visible_unmod;
		global $explr_visible_mod;
		global $user_lang;
		
		$requete = "SELECT username, nom, prenom, rights, userid, user_lang, ";
		$requete .="nb_per_page_search, nb_per_page_select, nb_per_page_gestion, ";
		$requete .="param_popup_ticket, param_sounds, ";
		$requete .="user_email, user_alert_resamail, user_alert_demandesmail, user_alert_subscribemail, user_alert_serialcircmail, user_alert_suggmail, explr_invisible, explr_visible_mod, explr_visible_unmod, grp_num FROM users WHERE userid='$id' LIMIT 1 ";
		$res = pmb_mysql_query($requete);
		$nbr = pmb_mysql_num_rows($res);
		if ($nbr) {
			$usr=pmb_mysql_fetch_object($res);
		} else die ('Unknown user');
		
		$requete_param = "SELECT * FROM users WHERE userid='$id' LIMIT 1 ";
		$res_param = pmb_mysql_query($requete_param);
		$field_values = pmb_mysql_fetch_row( $res_param );
		
		$param_user="<div class='row'><b>".$msg["1500"]."</b></div>\n";
		$deflt_user="<div class='row'><b>".$msg["1501"]."</b></div>\n";
		$speci_user="";
		$deflt3user="";
		$value_user="";
		
		$i = 0;
		while ($i < pmb_mysql_num_fields($res_param)) {
			$field = pmb_mysql_field_name($res_param, $i) ;
			$field_deb = substr($field,0,6);
			switch ($field_deb) {
				case "deflt_" :
					if ($field=="deflt_styles") {
						$deflt_user_style=static::get_field_selector($field, make_user_style_combo($field_values[$i]));
					} elseif ($field=="deflt_docs_location") {
						//visibilité des exemplaires
						if ($pmb_droits_explr_localises && $usr->explr_visible_mod) $where_clause_explr = "idlocation in (".$usr->explr_visible_mod.") and";
						else $where_clause_explr = "";
						$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location, docsloc_section where $where_clause_explr num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "account_calcule_section(this);", $field_values[$i], "", "","","",0);
						$deflt_user .= static::get_field_selector($field, $selector);
						//localisation de l'utilisateur pour le calcul de la section
						$location_user_section = $field_values[$i];
					} elseif ($field=="deflt_collstate_location") {
						$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "","0",$msg["all_location"],0);
						$deflt_user .= static::get_field_selector($field, $selector);
					} elseif ($field=="deflt_resas_location") {
						$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "","0",$msg["all_location"],0);
						$deflt_user .= static::get_field_selector($field, $selector);
					} elseif ($field=="deflt_docs_section") {
						// calcul des sections
						$selector="";
						if (!$location_user_section) $location_user_section = $deflt_docs_location;
						if ($pmb_droits_explr_localises && $usr->explr_visible_mod) $where_clause_explr = "where idlocation in (".$usr->explr_visible_mod.")";
						else $where_clause_explr = "";
						$rqtloc = "SELECT idlocation FROM docs_location $where_clause_explr order by location_libelle";
						$resloc = pmb_mysql_query($rqtloc);
						while ($loc=pmb_mysql_fetch_object($resloc)) {
							$requete = "SELECT idsection, section_libelle FROM docs_section, docsloc_section where idsection=num_section and num_location='$loc->idlocation' order by section_libelle";
							$result = pmb_mysql_query($requete);
							$nbr_lignes = pmb_mysql_num_rows($result);
							if ($nbr_lignes) {
								if ($loc->idlocation==$location_user_section ) $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:block\">\r\n";
								else $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:none\">\r\n";
								$selector .= "<select name='f_ex_section".$loc->idlocation."' id='f_ex_section".$loc->idlocation."'>";
								while($line = pmb_mysql_fetch_row($result)) {
									$selector .= "<option value='$line[0]' ";
									$selector .= (($line[0] == $field_values[$i]) ? "selected='selected' >" : '>');
									$selector .= htmlentities($line[1],ENT_QUOTES, $charset).'</option>';
								}
								$selector .= '</select></div>';
							}
						}
						$deflt_user .= static::get_field_selector($field, $selector);
					} elseif ($field=="deflt_upload_repertoire") {
						$selector = "";
						$requpload = "select repertoire_id, repertoire_nom from upload_repertoire";
						$resupload = pmb_mysql_query($requpload);
						$selector .=  "<div id='upload_section'>";
						$selector .= "<select name='form_deflt_upload_repertoire'>";
						$selector .= "<option value='0'>".$msg['upload_repertoire_sql']."</option>";
						while(($repupload = pmb_mysql_fetch_object($resupload))){
							$selector .= "<option value='".$repupload->repertoire_id."' ";
							if ($field_values[$i] == $repupload->repertoire_id ) {
								$selector .= "selected='selected' ";
							}
							$selector .= ">";
							$selector .= htmlentities($repupload->repertoire_nom,ENT_QUOTES,$charset)."</option>";
						}
						$selector .=  "</select></div>";
						$deflt_user .= static::get_field_selector($field, $selector);
					} elseif($field=="deflt_import_thesaurus"){
						$requete="select * from thesaurus order by 2";
						$resultat_liste=pmb_mysql_query($requete);
						$nb_liste=pmb_mysql_num_rows($resultat_liste);
						if($nb_liste) {
							$selector = "
								<select class='saisie-30em' name=\"form_".$field."\">";
							$j=0;
							while ($j<$nb_liste) {
								$liste_values = pmb_mysql_fetch_row( $resultat_liste );
								$selector.="<option value=\"".$liste_values[0]."\" " ;
								if ($field_values[$i]==$liste_values[0]) {
									$selector.="selected='selected' " ;
								}
								$selector.=">".$liste_values[1]."</option>\n" ;
								$j++;
							}
							$selector.="</select>" ;
							$deflt_user .= static::get_field_selector($field, $selector);
						}
					} elseif ($field=="deflt_short_loan_activate") {
						$deflt_user.=static::get_field_checkbox($field, $field_values[$i]);
					} elseif ($field=="deflt_camera_empr") {
						$deflt_user.=static::get_field_checkbox($field, $field_values[$i]);
					} elseif ($field=="deflt_cashdesk"){
						$requete="select * from cashdesk order by cashdesk_name";
						$resultat_liste=pmb_mysql_query($requete);
						$nb_liste=pmb_mysql_num_rows($resultat_liste);
						if ($nb_liste) {
							$selector= "<select class='saisie-30em' name=\"form_".$field."\">";
							$j=0;
							while ($j<$nb_liste) {
								$liste_values = pmb_mysql_fetch_object( $resultat_liste );
								$selector.= "<option value=\"".$liste_values->cashdesk_id."\" " ;
								if ($field_values[$i]==$liste_values->cashdesk_id) {
									$selector.="selected" ;
								}
								$selector.=">".htmlentities($liste_values->cashdesk_name,ENT_QUOTES,$charset)."</option>\n" ;
								$j++;
							}
							$selector.="</select>";
							$deflt_user .= static::get_field_selector($field, $selector);
						}
					} elseif(($field=="deflt_concept_scheme")){
						$deflt_user.="<div class='row userParam-row'><div class='colonne60'>".$msg[$field]."</div>\n";
						$deflt_user.="<div class='colonne_suite'>";
		
		
						$onto_store_config = array(
								/* db */
								'db_name' => DATA_BASE,
								'db_user' => USER_NAME,
								'db_pwd' => USER_PASS,
								'db_host' => SQL_SERVER,
								/* store */
								'store_name' => 'ontology',
								/* stop after 100 errors */
								'max_errors' => 100,
								'store_strip_mb_comp_str' => 0
						);
						$data_store_config = array(
								/* db */
								'db_name' => DATA_BASE,
								'db_user' => USER_NAME,
								'db_pwd' => USER_PASS,
								'db_host' => SQL_SERVER,
								/* store */
								'store_name' => 'rdfstore',
								/* stop after 100 errors */
								'max_errors' => 100,
								'store_strip_mb_comp_str' => 0
						);
		
						$tab_namespaces=array(
								"skos"	=> "http://www.w3.org/2004/02/skos/core#",
								"dc"	=> "http://purl.org/dc/elements/1.1",
								"dct"	=> "http://purl.org/dc/terms/",
								"owl"	=> "http://www.w3.org/2002/07/owl#",
								"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
								"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
								"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
								"pmb"	=> "http://www.pmbservices.fr/ontology#"
						);
		
		
						$onto_handler = new onto_handler($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel','http://www.w3.org/2004/02/skos/core#ConceptScheme');
		
						$params=new onto_param();
						$params->concept_scheme=[$deflt_concept_scheme];
						$onto_controler=new onto_skos_controler($onto_handler, $params);
		
						$deflt_user.=onto_skos_concept_ui::get_scheme_list_selector($onto_controler, $params,true,'','form_deflt_concept_scheme');
						$deflt_user.="</div></div>\n" ;
		
					} elseif ($field=="deflt_notice_replace_keep_categories") {
						$deflt_user.=static::get_field_radio($field, $field_values[$i]);
					} elseif ($field=="deflt_notice_is_new") {
						$deflt_user.=static::get_field_radio($field, $field_values[$i]);
					} elseif ($field=="deflt_bulletinage_location") {
						$selector = gen_liste ("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "","0",$msg["all_location"],0);
						$deflt_user.=static::get_field_selector($field, $selector);
					} elseif ($field=="deflt_agnostic_warehouse") {
						$conn=new agnostic($base_path.'/admin/connecteurs/in/agnostic');
						$conn->get_sources();
						$selector = "<select name=\"form_".$field."\">
						<option value='0' ".(!$field_values[$i] ? "selected='selected'" : "").">".$msg['caddie_save_to_warehouse_none']."</option>";
						if(is_array($conn->sources)) {
							foreach($conn->sources as $key_source=>$source) {
								$selector .= "<option value='".$key_source."' ".($field_values[$i] == $key_source ? "selected='selected'" : "").">".htmlentities($source['NAME'],ENT_QUOTES,$charset)."</option>";
							}
						}
						$selector .= "</select>";
						$deflt_user.=static::get_field_selector($field, $selector);
					} elseif ($field=="deflt_cms_article_statut") {
						if($cms_active && (SESSrights & CMS_AUTH)){
							$publications_states = new cms_editorial_publications_states();
							$selector = "
							<select name=\"form_".$field."\">
								".$publications_states->get_selector_options($field_values[$i])."
							</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					}  elseif ($field=="deflt_cms_article_type") {
						if($cms_active && (SESSrights & CMS_AUTH)){
							$types = new cms_editorial_types('article');
							$types->get_types();
							$selector = "
							<select name=\"form_".$field."\">
								".$types->get_selector_options($field_values[$i])."
							</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					}  elseif ($field=="deflt_cms_section_type") {
						if($cms_active && (SESSrights & CMS_AUTH)){
							$types = new cms_editorial_types('section');
							$types->get_types();
							$selector = "
							<select name=\"form_".$field."\">
								".$types->get_selector_options($field_values[$i])."
							</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					}elseif ($field=="deflt_scan_request_status") {
						if($pmb_scan_request_activate){
							$request_status_instance = new scan_request_admin_status();
							$selector = "
							<select name=\"form_".$field."\">
								".$request_status_instance->get_selector_options($field_values[$i])."
							</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					} elseif ($field=="deflt_catalog_expanded_caddies") {
						$deflt_user.=static::get_field_radio($field, $field_values[$i]);
					} elseif ($field=="deflt_notice_replace_links") {
						$selector = "<input type='radio' name='form_".$field."' value='0' ".($field_values[$i]==0?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_all']."
							<br /><input type='radio' name='form_".$field."' value='1' ".($field_values[$i]==1?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replacing']."
							<br /><input type='radio' name='form_".$field."' value='2' ".($field_values[$i]==2?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replaced'];
						$deflt_user.=static::get_field_selector($field, $selector);
					}elseif ($field=="deflt_printer") {
						if (substr($pmb_printer_name,0,9) == 'raspberry') {
							$selector = "
								<select name=\"form_".$field."\">
									".raspberry::get_selector_options($field_values[$i])."
								</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					} elseif ($field=="deflt_opac_visible_bulletinage") {
						$deflt_user.=static::get_field_checkbox($field, $field_values[$i]);
					} else {
						$deflt_table = substr($field,6);
						switch ($deflt_table) {
							case 'integration_notice_statut' :
								$deflt_table = 'notice_statut';
								break;
							case 'serials_docs_type' :
								$deflt_table = 'docs_type';
								break;
						}
						switch($field) {
							case "deflt_entites":
								$requete="select id_entite, raison_sociale from ".$deflt_table." where type_entite='1' order by 2 ";
								break;
							case "deflt_exercices":
								$requete="select id_exercice, libelle from ".$deflt_table." order by 2 ";
								break;
							case "deflt_rubriques":
								$requete="select id_rubrique, concat(budgets.libelle,':', rubriques.libelle) from ".$deflt_table." join budgets on num_budget=id_budget order by 2 ";
								break;
							case "deflt_notice_statut_analysis":
								$requete="(select 0,'".addslashes($msg[$field."_parent"])."') union ";
								$requete.="(select id_notice_statut, gestion_libelle from notice_statut order by 2)";
								break;
							default :
								$requete="select * from ".$deflt_table." order by 2";
								break;
						}
		
						$resultat_liste=pmb_mysql_query($requete);
						$nb_liste=pmb_mysql_num_rows($resultat_liste);
						if ($nb_liste) {
							$selector="
							<select class='saisie-30em' name=\"form_".$field."\">";
							$j=0;
							while ($j<$nb_liste) {
								$liste_values = pmb_mysql_fetch_row( $resultat_liste );
								$selector.="<option value=\"".$liste_values[0]."\" " ;
								if ($field_values[$i]==$liste_values[0]) {
									$selector.="selected='selected' " ;
								}
								$selector.=">".$liste_values[1]."</option>\n" ;
								$j++;
							}
							$selector.="</select>";
							$deflt_user.=static::get_field_selector($field, $selector);
						}
					}
					break;
		
				case "param_" :
					if ($field=="param_allloc") {
						$param_user_allloc="<div class='row userParam-row'><div class='colonne60'>".$msg[$field]."</div>\n
					<div class='colonne_suite'>
					<input type='checkbox' class='checkbox'";
						if ($field_values[$i]==1) $param_user_allloc.=" checked";
						$param_user_allloc.=" value='1' name='form_$field'></div></div>\n" ;
					} else {
						$param_user.="<div class='row'>";
						//if (strpos($msg[$field],'<br />')) $param_user .= "<br />";
						$param_user.="<input type='checkbox' class='checkbox'";
						if ($field_values[$i]==1) $param_user.=" checked";
						$param_user.=" value='1' name='form_$field'>\n
						$msg[$field]
						</div>\n";
					}
					break ;
		
				case "value_" :
					switch ($field) {
						case "value_deflt_fonction" :
							$flist=new marc_list('function');
							$f=$flist->table[$field_values[$i]];
							$value_user.="<div class='row userParam-row'><div class='colonne60'>
							$msg[$field]&nbsp;:&nbsp;</div>\n
							<div class='colonne_suite'>
							<input type='text' class='saisie-30emr' id='form_value_deflt_fonction_libelle' name='form_value_deflt_fonction_libelle' value='".htmlentities($f,ENT_QUOTES, $charset)."' />
					<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=".$caller."&p1=form_value_deflt_fonction&p2=form_value_deflt_fonction_libelle', 'selector')\" />
							<input type='button' class='bouton_small' value='X' onclick=\"this.form.elements['form_value_deflt_fonction'].value='';this.form.elements['form_value_deflt_fonction_libelle'].value='';return false;\" />
							<input type='hidden' name='form_value_deflt_fonction' id='form_value_deflt_fonction' value=\"$field_values[$i]\" />
							</div></div><br />";
							break;
						case "value_deflt_lang" :
							$llist=new marc_list('lang');
							$l=$llist->table[$field_values[$i]];
							$value_user.="<div class='row userParam-row'><div class='colonne60'>
							$msg[$field]&nbsp;:&nbsp;</div>\n
							<div class='colonne_suite'>
							<input type='text' class='saisie-30emr' id='form_value_deflt_lang_libelle' name='form_value_deflt_lang_libelle' value='".htmlentities($l,ENT_QUOTES, $charset)."' />
					<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=lang&caller=".$caller."&p1=form_value_deflt_lang&p2=form_value_deflt_lang_libelle', 'selector')\" />
							<input type='button' class='bouton_small' value='X' onclick=\"this.form.elements['form_value_deflt_lang'].value='';this.form.elements['form_value_deflt_lang_libelle'].value='';return false;\" />
							<input type='hidden' name='form_value_deflt_lang' id='form_value_deflt_lang' value=\"$field_values[$i]\" />
							</div></div><br />";
							break;
						case "value_deflt_relation" :
						case "value_deflt_relation_serial" :
						case "value_deflt_relation_bulletin" :
						case "value_deflt_relation_analysis" :
							$selector = notice_relations::get_selector('form_'.$field, $field_values[$i]);
							$value_user.=static::get_field_selector($field, $selector);
							break;
						case "value_deflt_module" :
							$arrayModules = array(
							'dashboard'=>$msg['dashboard'],
							'circu'=>$msg['5'],
							'catal'=>$msg['93'],
							'autor'=>$msg['132'],
							'edit'=>$msg['1100'],
							'dsi'=>$msg['dsi_droit'],
							'acquis'=>$msg['acquisition_droit'],
							'admin'=>$msg['7'],
							'cms'=>$msg['cms_onglet_title'],
							'account'=>$msg['933'],
							'fiches'=>$msg['onglet_fichier']
							);
							$selector = "<select name='form_".$field."'>";
							foreach ($arrayModules as $k=>$v) {
								$selector.="<option value='".$k."'";
								if($k == $field_values[$i]){
									$selector.=" selected";
								}
								$selector.=">".$v."</option>";
							}
							$selector.="</select>";
							$value_user.=static::get_field_selector($field, $selector);
							break;
						default :
							$value_user.="<div class='row userParam-row'><div class='colonne60'>
							$msg[$field]&nbsp;:&nbsp;</div>\n
							<div class='colonne_suite'>
							<input type='text' class='saisie-20em' name='form_$field' value='".htmlentities($field_values[$i],ENT_QUOTES, $charset)."' />
					</div></div><br />";
							break;
					}
					break ;
		
				case "deflt2" :
					if ($field=="deflt2docs_location") {
						// localisation des lecteurs
						$deflt_table = substr($field,6);
						$requete="select * from ".$deflt_table." order by 2";
						$resultat_liste=pmb_mysql_query($requete);
						$nb_liste=pmb_mysql_num_rows($resultat_liste);
						if ($nb_liste==0) {
							$deflt_user.="" ;
						} else {
							$deflt_user.="
						<div class='row userParam-row'><div class='colonne60'>".
								$msg[$field]."&nbsp;:&nbsp;</div>\n";
							$deflt_user.= "
						<div class='colonne_suite'>
						<select class='saisie-30em' name=\"form_".$field."\">";
		
							$j=0;
							while ($j<$nb_liste) {
								$liste_values = pmb_mysql_fetch_row( $resultat_liste );
								$deflt_user.="<option value=\"".$liste_values[0]."\" " ;
								if ($field_values[$i]==$liste_values[0]) {
									$deflt_user.="selected='selected' " ;
								}
								$deflt_user.=">".$liste_values[1]."</option>\n" ;
								$j++;
							}
							$deflt_user.="</select></div></div>!!param_allloc!!<br />\n" ;
						}
					} else {
						$deflt_table = substr($field,6);
						$requete="select * from ".$deflt_table." order by 2 ";
						$resultat_liste=pmb_mysql_query($requete);
						$nb_liste=pmb_mysql_num_rows($resultat_liste);
						if ($nb_liste==0) {
							$deflt_user.="" ;
						} else {
							$deflt_user.="
							<div class='row userParam-row'><div class='colonne60'>".
									$msg[$field]."&nbsp;:&nbsp;</div>\n";
							$deflt_user.= "
							<div class='colonne_suite'>
								<select class='saisie-30em' name=\"form_".$field."\">";
							$j=0;
							while ($j<$nb_liste) {
								$liste_values = pmb_mysql_fetch_row( $resultat_liste );
								$deflt_user.="<option value=\"".$liste_values[0]."\" " ;
								if ($field_values[$i]==$liste_values[0]) {
									$deflt_user.="selected='selected' " ;
								}
								$deflt_user.=">".$liste_values[1]."</option>\n" ;
								$j++;
							}
							$deflt_user.="</select></div></div>\n" ;
						}
					}
					break;
		
				case "xmlta_" :
					switch($field) {
						case "xmlta_indexation_lang" :
							$langues = new XMLlist("$include_path/messages/languages.xml");
							$langues->analyser();
							$clang = $langues->table;
		
							$combo = "<select name='form_".$field."' id='form_".$field."' class='saisie-20em' >";
							if(!$field_values[$i]) $combo .= "<option value='' selected>--</option>";
							else $combo .= "<option value='' >--</option>";
							foreach ($clang as $cle => $value) {
								// arabe seulement si on est en utf-8
								if (($charset != 'utf-8' and $user_lang != 'ar') or ($charset == 'utf-8')) {
									if(strcmp($cle, $field_values[$i]) != 0) $combo .= "<option value='$cle'>$value ($cle)</option>";
									else $combo .= "<option value='$cle' selected>$value ($cle)</option>";
								}
							}
							$combo .= "</select>";
							$deflt_user.=static::get_field_selector($field, $combo);
							break;
						case "xmlta_doctype_serial" :
								$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "");
								$deflt_user.=static::get_field_selector($field, $select_doc->display);
								break;
						case "xmlta_doctype_bulletin" :
						case "xmlta_doctype_analysis" :
								$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "","0",$msg[$field."_parent"]);
								$deflt_user.=static::get_field_selector($field, $select_doc->display);
								break;
						case "xmlta_doctype_scan_request_folder_record" :
							if($pmb_scan_request_activate){
								$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "");
								$deflt_user.=static::get_field_selector($field, $select_doc->display);
							}
							break;
						default :
							$deflt_table = substr($field,6);
							$select_doc = new marc_select("$deflt_table", "form_".$field, $field_values[$i], "");
							$deflt_user.=static::get_field_selector($field, $select_doc->display);
							break;
					}
				case "deflt3" :
					$q='';
					$t=array();
					switch($field) {
						case "deflt3bibli":
							$q="select 0,'".addslashes($msg['deflt3none'])."' union ";
							$q.="select id_entite, raison_sociale from entites where type_entite='1' order by 2 ";
							break;
						case "deflt3exercice":
							$q="select 0,'".addslashes($msg['deflt3none'])."' union ";
							$q.="select id_exercice, libelle from exercices order by 2 ";
							break;
						case "deflt3rubrique":
							$q="select 0,'".addslashes($msg['deflt3none'])."' union ";
							$q.="select id_rubrique, concat(budgets.libelle,':', rubriques.libelle) from rubriques join budgets on num_budget=id_budget order by 2 ";
							break;
						case "deflt3type_produit":
							$q="select 0,'".addslashes($msg['deflt3none'])."' union ";
							$q.="select id_produit, libelle from types_produits order by 2 ";
							break;
						case "deflt3dev_statut":
							$t=actes::getStatelist(TYP_ACT_DEV);
							break;
						case "deflt3cde_statut":
							$t=actes::getStatelist(TYP_ACT_CDE);
							break;
						case "deflt3liv_statut":
							$t=actes::getStatelist(TYP_ACT_LIV);
							break;
						case "deflt3fac_statut":
							$t=actes::getStatelist(TYP_ACT_FAC);
							break;
						case "deflt3sug_statut":
							$m=new suggestions_map();
							$t=$m->getStateList();
							break;
						case 'deflt3lgstatcde':
						case 'deflt3lgstatdev':
							$q=lgstat::getList('QUERY');
							break;
						case 'deflt3receptsugstat':
							$m=new suggestions_map();
							$t=$m->getStateList('ORDERED',TRUE);
							break;
					}
					if($q) {
						$r=pmb_mysql_query($q);
						$nb=pmb_mysql_num_rows($r);
						while($row=pmb_mysql_fetch_row($r)) {
							$t[$row[0]]=$row[1];
						}
					}
					if (count($t)) {
						$deflt3user.="<div class='row userParam-row'><div class='colonne60'>".$msg[$field]."&nbsp;:&nbsp;</div>\n";
						$deflt3user.= "<div class='colonne_suite'><select class='saisie-30em' name=\"form_".$field."\">";
						foreach($t as $k=>$v) {
							$deflt3user.="<option value=\"".$k."\" " ;
							if ($field_values[$i]==$k) {
								$deflt3user.="selected='selected' " ;
							}
							$deflt3user.=">".htmlentities($v, ENT_QUOTES, $charset)."</option>\n" ;
						}
						$deflt3user.="</select></div></div><br />\n";
					}
					break;
		
				case "speci_" :
					$speci_func = substr($field, 6);
					eval('$speci_user.= get_'.$speci_func.'($id, $field_values, $i, \''.$caller.'\');');
					break;
		
				case "explr_" :
					${$field}=$field_values[$i];
					break;
				default :
					break ;
			}
			$i++;
		}
		if($caller == 'userform') {
			//visibilité des exemplaires
			if ($pmb_droits_explr_localises) {
				$explr_tab_invis=explode(",",$explr_invisible);
				$explr_tab_unmod=explode(",",$explr_visible_unmod);
				$explr_tab_modif=explode(",",$explr_visible_mod);
			
				$visibilite_expl_user="
				<div class='row'><hr /></div>
				<div class='row'>
					<div class='colonne3'>".$msg["expl_visibilite"]."&nbsp;:&nbsp;</div>
					<div class='colonne_suite'>&nbsp;</div>
				</div>\n";
				$requete_droits_expl="select idlocation, location_libelle from docs_location order by location_libelle";
				$resultat_droits_expl=pmb_mysql_query($requete_droits_expl);
				$temp="";
				while ($j=pmb_mysql_fetch_array($resultat_droits_expl)) {
					$temp.=$j["idlocation"].",";
					$visibilite_expl_user.= "
					<div class='row'>
						<div class='colonne3 align_right'>".$j["location_libelle"]." : </div>
						<div class='colonne_suite'>&nbsp;<select name=\"form_expl_visibilite_".$j["idlocation"]."\">
					";
					$as_invis = array_search($j["idlocation"],$explr_tab_invis);
					$as_unmod = array_search($j["idlocation"],$explr_tab_unmod);
					$as_mod = array_search($j["idlocation"],$explr_tab_modif);
					$visibilite_expl_user .="\n<option value='explr_invisible' ".($as_invis!== FALSE && $as_invis!== NULL?"selected='selected' ":"").">".$msg["explr_invisible"]."</option>";
					if (($as_mod!== FALSE && $as_mod !== NULL)||($as_unmod!== FALSE && $as_unmod !== NULL)||($as_invis!== FALSE && $as_invis !== NULL)) {
						$visibilite_expl_user .="\n<option value='explr_visible_unmod' ".($as_unmod!== FALSE && $as_unmod!== NULL?"selected='selected' ":"").">".$msg["explr_visible_unmod"]."</option>";
					} else {
						$visibilite_expl_user .="\n<option value='explr_visible_unmod' selected='selected' >".$msg["explr_visible_unmod"]."</option>";
					}
					$visibilite_expl_user .="\n<option value='explr_visible_mod' ".($as_mod!== FALSE && $as_mod!== NULL?"selected='selected' ":"").">".$msg["explr_visible_mod"]."</option>";
					$visibilite_expl_user.="</select></div></div>\n" ;
				}
				pmb_mysql_free_result($resultat_droits_expl);
			
				if ((!$explr_invisible)&&(!$explr_visible_unmod)&&(!$explr_visible_mod)) {
					$rqt="UPDATE users SET explr_invisible=0,explr_visible_mod=0,explr_visible_unmod='".substr($temp,0,strlen($temp)-1)."' WHERE userid=$id";
					@pmb_mysql_query($rqt);
				}
			
				$deflt_user .=$visibilite_expl_user;
			} //fin visibilité des exemplaires
		}
		
		$param_default="
		<div class='row'><hr /></div>
		".$param_user."
		<div class='row'><hr /></div>
		".str_replace("!!param_allloc!!",$param_user_allloc,$deflt_user)."
		<br />
		<div class='row'><hr /></div>
		".$value_user.
		($caller == 'userform' ? "<div class='row'><hr /></div>
		$deflt_user_style
		<br />" : "");
		if ($speci_user || $deflt3user) {
			$param_default.= "<div class='row'><hr /></div>";
			$param_default.=$deflt3user;
			$param_default.=$speci_user;
			$param_default.= "<div class='row'></div>";
		}
		return $param_default;
	}
	
	public static function get_param($id, $field) {
		$id += 0;
		$param = '';
		if($id) {
			$query = "SELECT ".$field." FROM users WHERE userid='".$id."' ";
			$result = pmb_mysql_query($query);
			$param = pmb_mysql_result($result, 0, 0);
		}
		return $param;
	}
	
	public static function get_name($id) {
		$id += 0;
		$name = '';
		if($id) {
			$query = "SELECT nom, prenom FROM users WHERE userid='".$id."' ";
			$result = pmb_mysql_query($query);
			$row_user=pmb_mysql_fetch_object($result);
			$name = $row_user->nom;
			if($row_user->prenom) {
				$name = $row_user->prenom.' '.$name;
			}
		}
		return $name;
	}
	
} // fin de déclaration de la classe user