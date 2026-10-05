<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user.class.php,v 1.67.2.3.2.2 2025/05/20 14:00:08 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Animations\Models\AnimationTypesModel;
use Pmb\Animations\Models\MailingTypeModel;
use Pmb\Common\Helper\MySQL;
use Pmb\Users\Models\RoleModel;
use Pmb\Users\Models\RoleMemberModel;

global $base_path, $class_path;
require_once ($class_path."/marc_table.class.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/actes.class.php");
require_once($class_path."/suggestions_map.class.php");
require_once($class_path."/lignes_actes_statuts.class.php");
require_once($base_path."/admin/connecteurs/in/agnostic/agnostic.class.php");
require_once($class_path.'/scan_request/scan_request_admin_status.class.php');
require_once($class_path.'/notice_relations_collection.class.php');
require_once($class_path.'/printer/raspberry.class.php');

class user {

	protected $userid = 0;

	protected $username = '';
	protected $pwd = '';
	protected $pwd_encrypted = FALSE;
	protected $nom = '';
	protected $prenom = '';
	protected $rights = 3;
	protected $user_lang = 'fr_FR';

	protected $nb_per_page_search = 20;
	protected $nb_per_page_select = 10;
	protected $nb_per_page_gestion = 20;

	protected $explr_invisible = 0;
	protected $explr_visible_mod = 0;
	protected $explr_visible_unmod = 0;

	protected $user_email = '';
	protected $user_email_recipient = '';

	protected $user_alert_resamail = 0;
	protected $user_alert_contribmail = 0;
	protected $user_alert_demandesmail = 0;
	protected $user_alert_subscribemail = 0;
	protected $user_alert_serialcircmail = 0;
	protected $user_alert_suggmail = 0;
	protected $user_alert_animation_mail = 0;
	protected $grp_num = FALSE;
	protected $mfa_secret_code = "";
	protected $duplicate_from_userid = 0;
	protected $roles = array();

	public function __construct($userid=0) {
		$this->userid = intval($userid);
		$this->fetch_data();
	}

	protected function fetch_data() {
		global $lang;

		$query = "SELECT username, nom, prenom, rights, userid, user_lang,
			nb_per_page_search, nb_per_page_select, nb_per_page_gestion,
			param_popup_ticket, param_sounds, user_email,
			user_alert_resamail, user_alert_contribmail, user_alert_demandesmail, user_alert_subscribemail, user_alert_serialcircmail, user_alert_suggmail,
			explr_invisible, explr_visible_mod, explr_visible_unmod, grp_num, mfa_secret_code, user_alert_animation_mail, user_email_recipient
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
			$this->user_email_recipient = $row->user_email_recipient;
			$this->user_alert_resamail = $row->user_alert_resamail;
			$this->user_alert_contribmail = $row->user_alert_contribmail;
			$this->user_alert_demandesmail = $row->user_alert_demandesmail;
			$this->user_alert_subscribemail = $row->user_alert_subscribemail;
			$this->user_alert_serialcircmail = $row->user_alert_serialcircmail;
			$this->user_alert_animation_mail = $row->user_alert_animation_mail;
			$this->user_alert_suggmail = $row->user_alert_suggmail;
			$this->explr_invisible = $row->explr_invisible;
			$this->explr_visible_mod = $row->explr_visible_mod;
			$this->explr_visible_unmod = $row->explr_visible_unmod;
			$this->grp_num = $row->grp_num;
			$this->mfa_secret_code = $row->mfa_secret_code;

			$roleModel = new RoleModel();
			$this->roles = $roleModel->getList();
		} else {
			$this->user_lang = $lang;
		}
	}

	public function set_userid($userid=0) {
		$this->userid = intval($userid);
	}

	public function set_username($username = '') {
	    $this->username = $username;
	}

	public function set_nom($nom = '') {
	    $this->nom = $nom;
	}

	public function set_prenom($prenom = '') {
	    $this->prenom = $prenom;
	}

	public function set_user_email($user_email = '') {
	    $this->user_email = $user_email;
	}

	public function set_rights($rights = 0) {
	    $this->rights = intval($rights);
	}

	public function set_user_email_recipient($user_email_recipient = '') {
	    $this->user_email_recipient = $user_email_recipient;
	}

	public function get_id() {
		return $this->userid;
	}

	public function get_username() {
	    return $this->username;
	}

	public function get_nom() {
		return $this->nom;
	}

	public function get_prenom() {
		return $this->prenom;
	}

	public function get_user_email() {
		return $this->user_email;
	}

	public function get_mfa_secret_code() {
		return $this->mfa_secret_code;
	}

	public function get_user_email_recipient() {
	    return $this->user_email_recipient;
	}

	public function get_rights() {
	    return $this->rights;
	}

	public function get_roles() {
	    return $this->roles;
	}

	public function set_duplicate_from_userid($duplicate_from_userid=0) {
		$this->duplicate_from_userid = intval($duplicate_from_userid);
	}

	public function get_constants_roles_rights() {
	    $constants_rights = array();
	    $constants_rights['modules'] = array(
	        'circ' => CIRCULATION_AUTH,
	        'catalog' => CATALOGAGE_AUTH,
	        'autorites' => AUTORITES_AUTH,
	        'admin' => ADMINISTRATION_AUTH,
	        'edit' => EDIT_AUTH,
	        'dsi' => DSI_AUTH,
	        'acquisition' => ACQUISITION_AUTH,
	        'extensions' => EXTENSIONS_AUTH,
	        'demandes' => DEMANDES_AUTH,
	        'fichier' => FICHES_AUTH,
	        'cms' => CMS_AUTH,
	        'semantic' => SEMANTIC_AUTH,
	        'frbr' => FRBR_AUTH,
	        'modelling' => MODELLING_AUTH,
	        'animations' => ANIMATION_AUTH,
// 	        'import_export' => IMPORT_EXPORT_AUTH
	    );

	    $constants_rights['actions'] = array(
	        'sauv' => SAUV_AUTH,
	        'pref' => PREF_AUTH,
	        'restrictcirc' => RESTRICTCIRC_AUTH,
	        'restrictcatal' => RESTRICTCATAL_AUTH,
	        'thesaurus' => THESAURUS_AUTH,
	        'transferts' => TRANSFERTS_AUTH,
	        'edit_forcing' => EDIT_FORCING_AUTH,
	        'catal_modif_cb_expl' => CATAL_MODIF_CB_EXPL_AUTH,
	        'acquisition_account_invoice' => ACQUISITION_ACCOUNT_INVOICE_AUTH,
	        'cms_build' => CMS_BUILD_AUTH,
	        'concepts' => CONCEPTS_AUTH
	    );
	    return $constants_rights;
	}
	/**
	 * @param array $num_roles
	 * @return number|string
	 */
	public function adjustment_rights_from_roles($num_roles) {
	    $constants_roles_rights = $this->get_constants_roles_rights();
	    $rights = 0;
	    foreach ($num_roles as $num_role) {
	        $num_role = intval($num_role);
	        $roleModel = RoleModel::getInstance($num_role);
	        if (!empty($roleModel->modules)) {
	            foreach ($roleModel->modules as $module_name=>$module) {
	                if (!empty($module['visible'])) {
	                    if (isset($constants_roles_rights['modules'][$module_name])) {
    	                    $right_value = $constants_roles_rights['modules'][$module_name];
    	                    if(!($rights & $right_value)) {
    	                        $rights += $right_value;
    	                    }
	                    }
	                }
	            }

	        }
	    }
	    //Parcours des actions (en attendant l'implementation des actions dans les roles)
	    foreach ($constants_roles_rights['actions'] as $constant) {
	        if ($this->rights & $constant) {
	            $rights += $constant;
	        }
	    }

	    //On valorise la propriété en toute fin
	    $this->rights = $rights;
	    return $this->rights;
	}

	protected function get_roles_content_form() {
	    global $msg, $charset;

	    $display = "<div class='row'>";
	    if (!empty($this->roles)) {
	        foreach($this->roles as $index=>$role) {
    	        if($index % 7 == 0) {
    	            if($index) {
    	                $display .= "</div>";
    	            }
    	            $display .= "<div class='colonne4'>";
    	        }
    	        $display .= "<input
                        type='checkbox'
                        class='checkbox'
                        ".(!empty($role->members) && !empty($role->members['user']) && in_array($this->userid, $role->members['user']) ? "checked" : "")."
                        title='".htmlentities($role->comment, ENT_QUOTES, $charset)."'
                        value='".$role->id."'
                        id='form_role_".$role->id."'
                        name='form_roles[]'
                        onchange=\"if(confirm(pmbDojo.messages.getMessage('roles', 'user_update_form_rights_confirm'))) {update_form_rights();}\"/>
                    <label for='form_role_".$role->id."'>".htmlentities($role->name, ENT_QUOTES, $charset)."</label>
                    <br />\n";
    	    }
	    } else {
	        $display .= htmlentities($msg['roles_empty'], ENT_QUOTES, $charset);
	    }
	    $display .= "</div>";
	    return $display;
	}

	protected function get_right_properties($name, $label, $right, $parameter=1, $parameter_label='') {
	    return [
	        'name' => $name,
	        'label' => $label,
	        'right' => $right,
	        'parameter' => $parameter,
	        'parameter_label' => $parameter_label
	    ];
	}

	protected function get_rights_properties() {
	    global $msg;
	    global $acquisition_active, $dsi_active, $demandes_active, $cms_active, $frbr_active, $modelling_active;
	    global $fiches_active, $pmb_extension_tab, $pmb_transferts_actif, $thesaurus_concepts_active, $semantic_active, $animations_active;

	    return [
	        $this->get_right_properties('circ', $msg['5'], CIRCULATION_AUTH),
	        $this->get_right_properties('catal_modif_cb_expl', $msg['catal_modif_cb_expl_droit'], CATAL_MODIF_CB_EXPL_AUTH),
	        $this->get_right_properties('restrictcirc', $msg['restrictcirc_auth'], RESTRICTCIRC_AUTH),
	        $this->get_right_properties('admin', $msg['7'], ADMINISTRATION_AUTH),
	        $this->get_right_properties('fiches', $msg['onglet_fichier'], FICHES_AUTH, $fiches_active, $msg['param_fiches']),
	        $this->get_right_properties('concepts', $msg['ontology_skos_menu'], CONCEPTS_AUTH, $thesaurus_concepts_active, $msg['param_concepts']),
	        $this->get_right_properties('catal', $msg['93'], CATALOGAGE_AUTH),

	        $this->get_right_properties('edition', $msg['1100'], EDIT_AUTH),
	        $this->get_right_properties('edition_forcing', $msg['edit_droit_forcing'], EDIT_FORCING_AUTH),
	        $this->get_right_properties('sauv', $msg['28'], SAUV_AUTH),
	        $this->get_right_properties('cms', $msg['cms_onglet_title'], CMS_AUTH, $cms_active, $msg['param_cms']),
	        $this->get_right_properties('cms_build', $msg['cms_build_tab'], CMS_BUILD_AUTH, $cms_active, $msg['param_cms']),
	        $this->get_right_properties('auth', $msg['132'], AUTORITES_AUTH),
	        $this->get_right_properties('dsi', $msg['dsi_droit'], DSI_AUTH, $dsi_active, $msg['dsi_menu']),

	        $this->get_right_properties('pref', $msg['933'], PREF_AUTH),
	        $this->get_right_properties('acquisition_account_invoice_flg', $msg['acquisition_account_invoice_flg'], ACQUISITION_ACCOUNT_INVOICE_AUTH),
	        $this->get_right_properties('semantic', $msg['semantic_flg'], SEMANTIC_AUTH, $semantic_active, $msg['param_semantic']),
	        $this->get_right_properties('animations', $msg['animations'], ANIMATION_AUTH, $animations_active, $msg['param_animations']),
	        $this->get_right_properties('thesaurus', $msg['thesaurus_auth'], THESAURUS_AUTH),
	        $this->get_right_properties('acquisition', $msg['acquisition_droit'], ACQUISITION_AUTH, $acquisition_active, $msg['acquisition_menu']),
	        $this->get_right_properties('transferts', $msg['transferts_droit'], TRANSFERTS_AUTH, $pmb_transferts_actif, $msg['param_transferts']),

	        $this->get_right_properties('extensions', $msg['extensions_droit'], EXTENSIONS_AUTH, $pmb_extension_tab, $msg['extensions_menu']),
	        $this->get_right_properties('demandes', $msg['demandes_droit'], DEMANDES_AUTH, $demandes_active, $msg['demandes_menu']),
	        $this->get_right_properties('frbr', $msg['frbr'], FRBR_AUTH, $frbr_active, $msg['frbr_pages_menu']),
	        $this->get_right_properties('modelling', $msg['modelling'], MODELLING_AUTH, $modelling_active, $msg['param_modelling']),
//	        $this->get_right_properties('import_export', $msg['imports_exports'], IMPORT_EXPORT_AUTH),
	    ];
	}

	protected function _compare_rights_properties($a, $b) {
	    $a = convert_diacrit($a['label']);
	    $b = convert_diacrit($b['label']);
	    return strcmp(strtolower($a), strtolower($b));
	}

	protected function get_sorted_rights_properties() {
	    $rights_properties = $this->get_rights_properties();
	    usort($rights_properties, array($this, "_compare_rights_properties"));
	    return $rights_properties;
	}

	public function get_rights_content_form($form_param_default="") {
	    global $msg, $charset;

	    $display = "<div id='form_rights' class='row'>";
	    $rights_properties = $this->get_rights_properties();
	    foreach($rights_properties as $index=>$right_properties) {
	        if($index % 7 == 0) {
	            if($index) {
	                $display .= "</div>";
	            }
	            $display .= "<div class='colonne4'>";
	        }
	        $name = $right_properties['name'];
	        $parameter = $right_properties['parameter'];
	        if(!$parameter) {
	            $title = str_replace("!!parametre!!", $right_properties['parameter_label'].">active" , $msg['admin_disabled_module_checkbox']);
	        } else {
	            $title = '';
	        }
	        $display .= "<input
                    type='checkbox'
                    class='checkbox'
                    ".($this->rights & $right_properties['right'] ? "checked" : "")."
                    ".(!$parameter ? "disabled" : "")."
                    title='".htmlentities($title, ENT_QUOTES, $charset)."'
                    value='1'
                    id='form_".$name."'
                    name='form_".$name."' />
                <label for='form_".$name."'>".htmlentities($right_properties['label'], ENT_QUOTES, $charset)."</label>
                <br />\n";
	    }
	    $display .= "</div>";
	    return $display;
	}

	public function get_user_form($form_param_default="") {
		global $base_path;
		global $msg;
		global $admin_user_form;
		global $charset;
		global $password_field;
		global $include_path ;
		global $opac_websubscribe_show,$opac_serialcirc_active;
		global $acquisition_active, $demandes_active;
		global $animations_active;
		global $pmb_contribution_area_activate;

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

		// Affichage des rôles
		$admin_user_form = str_replace('!!roles_content_form!!', $this->get_roles_content_form(), $admin_user_form);

        // Affichage des droits
		$admin_user_form = str_replace('!!rights_content_form!!', $this->get_rights_content_form(), $admin_user_form);


		if ($this->user_alert_resamail==1) $alert_resa_mail=" checked";
		else $alert_resa_mail="";
		$admin_user_form = str_replace('!!alter_resa_mail!!', $alert_resa_mail, $admin_user_form);
		if ($pmb_contribution_area_activate) {
    		if ($this->user_alert_contribmail==1) $alert_contrib_mail=" checked";
    		else $alert_contrib_mail="";
    		$admin_user_form = str_replace('!!alert_contrib_mail!!', $alert_contrib_mail, $admin_user_form);
		}
		if ($animations_active) {
		    if ($this->user_alert_animation_mail==1) $alert_animations_mail=" checked";
		    else $alert_animations_mail="";
		    $admin_user_form = str_replace('!!alert_user_alert_animation_mail!!', $alert_animations_mail, $admin_user_form);
		}
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
		$admin_user_form = str_replace('!!user_email_recipient!!', $this->user_email_recipient, $admin_user_form);

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
			<div class='colonne60'><label for='form_".$field."' style='all:unset'>".$msg[$field]."&nbsp;:&nbsp;</label></div>
			<div class='colonne_suite'>".$selector."</div>
		</div>\n";
	}

	public function set_properties_from_form() {
		global $form_login, $form_pwd, $form_nom, $form_prenom;
		global $droits, $user_lang;
		global $form_nb_per_page_search, $form_nb_per_page_select, $form_nb_per_page_gestion;
		global $form_expl_visibilite;
		global $sel_group;
		global $form_user_email, $form_user_email_recipient;
		global $form_user_alert_resamail, $form_user_alert_contribmail, $form_user_alert_demandesmail, $form_user_alert_subscribemail, $form_user_alert_suggmail, $form_user_alert_serialcircmail;
		global $form_alert_user_alert_animation_mail;
		global $form_roles;

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
		$this->nb_per_page_search = intval($form_nb_per_page_search);
		$this->nb_per_page_select = intval($form_nb_per_page_select);
		$this->nb_per_page_gestion = intval($form_nb_per_page_gestion);

		$this->explr_invisible = $form_expl_visibilite[0];
		$this->explr_visible_mod = $form_expl_visibilite[1];
		$this->explr_visible_unmod = $form_expl_visibilite[2];
		if (isset($sel_group)) {
			$this->grp_num = $sel_group;
		}

		$this->user_email = stripslashes($form_user_email);
		$this->user_email_recipient = stripslashes($form_user_email_recipient);

		$this->user_alert_resamail = intval($form_user_alert_resamail);
		$this->user_alert_contribmail = intval($form_user_alert_contribmail);
		$this->user_alert_demandesmail = intval($form_user_alert_demandesmail);
		$this->user_alert_subscribemail = intval($form_user_alert_subscribemail);
		$this->user_alert_suggmail = intval($form_user_alert_suggmail);
		$this->user_alert_serialcircmail = intval($form_user_alert_serialcircmail);
		$this->user_alert_animation_mail = intval($form_alert_user_alert_animation_mail);

		$this->roles = array();
		if (!empty($form_roles)) {
		    foreach ($form_roles as $num_role) {
		        $this->roles[] = new RoleModel($num_role);
		    }
		}
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
		$dummy[] = "user_email_recipient='".addslashes($this->user_email_recipient)."'";
		$dummy[] = "user_alert_resamail='".$this->user_alert_resamail."'";
		$dummy[] = "user_alert_contribmail='".$this->user_alert_contribmail."'";
		$dummy[] = "user_alert_demandesmail='".$this->user_alert_demandesmail."'";
		$dummy[] = "user_alert_subscribemail='".$this->user_alert_subscribemail."'";
		$dummy[] = "user_alert_suggmail='".$this->user_alert_suggmail."'";
		$dummy[] = "user_alert_serialcircmail='".$this->user_alert_serialcircmail."'";
		$dummy[] = "user_alert_animation_mail='".$this->user_alert_animation_mail."'";

		if(!$this->userid && $this->pwd) {
			if(!$this->pwd_encrypted) {
				$dummy[] = "pwd='".MySQL::password(addslashes($this->pwd))."'";
			} else {
				$dummy[] = "pwd='".addslashes($this->pwd)."'";
			}
		}
		if (!empty($dummy)) {
			$set = implode(", ", $dummy);
		}
		if(!empty($set)) {
			if($this->userid) {
				$set = "SET last_updated_dt=curdate(),".$set;
				$requete = "UPDATE users $set WHERE userid=".$this->userid." ";
			} else {
				$set = "SET create_dt=curdate(), last_updated_dt=curdate(),".$set;
				$requete = "INSERT INTO users ".$set;
			}
			pmb_mysql_query($requete);

			//Mise à jour des rôles associés
			RoleMemberModel::saveRolesFromNumMember($this->userid, $this->roles);
		}
	}

	public static function delete($id, $user_encours) {
		if($id && $id !=1) {
			$requete = "select username from users where userid=$id ";
			$res=pmb_mysql_fetch_row( pmb_mysql_query($requete));
			$username_del=$res[0];
			$requete = "DELETE FROM users WHERE userid=$id and username<>'".addslashes($user_encours)."'";
			$res = pmb_mysql_query($requete);
			if ($res) {
				$requete = "DELETE FROM sessions WHERE login='".$username_del."'";
				$res = pmb_mysql_query($requete);
				$requete = "DELETE FROM es_methods_users WHERE num_user=$id ";
				$res = pmb_mysql_query($requete);
			}
			$requete = "OPTIMIZE TABLE users ";
			$res = pmb_mysql_query($requete);
			return true;
		}
		return false;
	}

	public static function get_fields_query($id, $all_fields=false, $field='') {
	    if($all_fields) {
	        $query = "SELECT * ";
	    } elseif($field) {
	        $query = "SELECT ".$field." ";
	    } else {
	        $query = "SELECT username, nom, prenom, rights, userid, user_lang, ";
	        $query .="nb_per_page_search, nb_per_page_select, nb_per_page_gestion, ";
	        $query .="param_popup_ticket, param_sounds, ";
	        $query .="user_email, user_alert_resamail, user_alert_contribmail, user_alert_demandesmail, user_alert_subscribemail, user_alert_serialcircmail, user_alert_suggmail, explr_invisible, explr_visible_mod, explr_visible_unmod, grp_num, user_alert_animation_mail, user_email_recipient ";
	    }
	    $query .="FROM users WHERE userid='$id' LIMIT 1 ";
	    return $query;
	}

	public static function get_field_radio($field, $selected) {
		global $msg;
		return
		"<div class='row userParam-row'>
			<div class='colonne60'>".$msg[$field]."</div>\n
			<div class='colonne_suite'>
				<label for='form_".$field."_0' style='all:unset'>".$msg[39]."</label> <input type='radio' id='form_".$field."_0' name='form_$field' value='0' ".(!$selected ? "checked='checked'" : "")." />
				<label for='form_".$field."_1' style='all:unset'>".$msg[40]."</label> <input type='radio' id='form_".$field."_1' name='form_$field' value='1' ".($selected ? "checked='checked'" : "")." />
			</div>
		</div>\n";
	}

	public static function get_field_checkbox($field, $checked) {
		global $msg;
		return
		"<div class='row userParam-row'>
			<div class='colonne60'><label for='form_$field' style='all:unset'>".$msg[$field]."</label></div>\n
			<div class='colonne_suite'>
				<input type='checkbox' class='checkbox' ".($checked==1 ? "checked='checked'" : "")." value='1' id='form_$field' name='form_$field' />
			</div>
		</div>\n";
	}

	public static function get_form($id=0, $caller='', $field='') {
		global $msg, $charset;
		global $base_path, $class_path, $include_path;
		global $deflt_concept_scheme, $thesaurus_concepts_active;
		global $pmb_droits_explr_localises, $pmb_docnum_in_database_allow;
		global $deflt_docs_location;
		global $cms_active;
		global $pmb_scan_request_activate;
		global $pmb_short_loan_management;
		global $pmb_printer_name;
		global $acquisition_active, $begin_result_liste;
		global $pmb_gestion_abonnement, $pmb_gestion_financiere;

		//A verifier : si ce sont bien des globales
		global $explr_invisible;
		global $explr_visible_unmod;
		global $explr_visible_mod;
		global $user_lang;

		$requete = static::get_fields_query($id);
		$res = pmb_mysql_query($requete);
		$nbr = pmb_mysql_num_rows($res);
		if ($nbr) {
			$usr=pmb_mysql_fetch_object($res);
		} else die ('Unknown user');

		if($field) {
		    $requete_param = static::get_fields_query($id, false, $field);
		} else {
		    $requete_param = static::get_fields_query($id, true);
		}

		$res_param = pmb_mysql_query($requete_param);
		$field_values = pmb_mysql_fetch_row( $res_param );

		$param_user = '';
		$deflt_user = '';
		if (empty($field)) {
		    $param_user = "<div class='row'><b>".$msg["1500"]."</b></div>\n";
		    $deflt_user = "<div class='row'><b>".$msg["1501"]."</b></div>\n";
		}
		$speci_user="";
		$deflt3user="";
		$value_user="";
		$param_user_allloc="";

		$deflt_user_array = array();
		$i = 0;
		while ($i < pmb_mysql_num_fields($res_param)) {
			$field = pmb_mysql_field_name($res_param, $i) ;
			$field_deb = substr($field,0,6);
			switch ($field_deb) {
				case "deflt_" :
				    switch ($field) {
				        case 'deflt_styles':
    						$deflt_user_style = static::get_field_selector($field, make_user_style_combo($field_values[$i]));
				            break;

				        case 'deflt_docs_location':
				            // Visibilité des exemplaires
				            $where_clause_explr = '';
				            if ($pmb_droits_explr_localises && $usr->explr_visible_mod) {
				                $where_clause_explr = "idlocation in ($usr->explr_visible_mod) and";
				            }
				            $selector = gen_liste("select distinct idlocation, location_libelle from docs_location, docsloc_section where $where_clause_explr num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "account_calcule_section(this);", $field_values[$i], "", "","","", 0);
				            $deflt_user_array['expl'][] = static::get_field_selector($field, $selector);

				            // Localisation de l'utilisateur pour le calcul de la section
				            $location_user_section = $field_values[$i];
				            break;

				        case 'deflt_collstate_location':
				            $selector = gen_liste("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "", "0", $msg["all_location"], 0);
				            $deflt_user_array['collstate'][] = static::get_field_selector($field, $selector);
				            break;

				        case 'deflt_resas_location':
    						$selector = gen_liste("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "", "0", $msg["all_location"], 0);
    						$deflt_user_array['other'][] = static::get_field_selector($field, $selector);
				            break;

				        case 'deflt_animation_calendar':
    						$selector = gen_liste("select distinct id_calendar, name from anim_calendar", "id_calendar", "name", 'form_'.$field, "", $field_values[$i], "", "", "0", "", 0);
    						$deflt_user_array['animation'][] = static::get_field_selector($field, $selector);
				            break;

				        case 'deflt_animation_communication_type':
    						$selector = gen_liste("select id_mailing_type, name from anim_mailing_types where periodicity = " . MailingTypeModel::MAILING_BEFORE . " or periodicity = " . MailingTypeModel::MAILING_AFTER . "", "id_mailing_type", "name", 'form_'.$field, "", $field_values[$i], "", "", "0", "", 0);
    						$deflt_user_array['animation'][] = static::get_field_selector($field, $selector);
				            break;

				        case 'deflt_docs_section':
    						// Calcul des sections
    						$selector = '';
    						if (empty($location_user_section)) {
    						    $location_user_section = $deflt_docs_location;
    						}

    						$where_clause_explr = '';
    						if ($pmb_droits_explr_localises && $usr->explr_visible_mod) {
    						    $where_clause_explr = "where idlocation in ($usr->explr_visible_mod)";
    						}

    						$rqtloc = "SELECT idlocation FROM docs_location $where_clause_explr order by location_libelle";
    						$resloc = pmb_mysql_query($rqtloc);
    						while ($loc = pmb_mysql_fetch_object($resloc)) {
    							$requete = "SELECT idsection, section_libelle FROM docs_section, docsloc_section where idsection=num_section and num_location='$loc->idlocation' order by section_libelle";
    							$result = pmb_mysql_query($requete);
    							$nbr_lignes = pmb_mysql_num_rows($result);
    							if ($nbr_lignes) {
							        $display = 'none';
    							    if ($loc->idlocation == $location_user_section) {
    							        $display = 'block';
    							    }
							        $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:$display\">\r\n";
    								$selector .= "<select name='f_ex_section".$loc->idlocation."' id='f_ex_section".$loc->idlocation."'>";
    								while ($line = pmb_mysql_fetch_row($result)) {
    									$selector .= "<option value='$line[0]' ";
    									$selector .= (($line[0] == $field_values[$i]) ? "selected='selected' >" : '>');
    									$selector .= htmlentities($line[1], ENT_QUOTES, $charset).'</option>';
    								}
    								$selector .= '</select></div>';
    							}
    						}
    						$deflt_user_array['expl'][] = static::get_field_selector($field, $selector);
				            break;

				        case 'deflt_upload_repertoire':
				            $selector = "";
				            $requpload = "select repertoire_id, repertoire_nom from upload_repertoire";
				            $resupload = pmb_mysql_query($requpload);
				            $selector .=  "<div id='upload_section'>";
				            $selector .= "<select id='form_deflt_upload_repertoire' name='form_deflt_upload_repertoire'>";
				            if ($pmb_docnum_in_database_allow) {
				                $selector .= "<option value='0'>".$msg['upload_repertoire_sql']."</option>";
				            }
				            while ($repupload = pmb_mysql_fetch_object($resupload)) {
				                $selector .= "<option value='".$repupload->repertoire_id."' ";
				                if ($field_values[$i] == $repupload->repertoire_id) {
				                    $selector .= "selected='selected' ";
				                }
				                $selector .= ">";
				                $selector .= htmlentities($repupload->repertoire_nom, ENT_QUOTES, $charset) . "</option>";
				            }
				            $selector .=  "</select></div>";
				            $deflt_user_array['explnum'][] = static::get_field_selector($field, $selector);
				            break;

				        case 'deflt_import_thesaurus':
    						$requete = "select * from thesaurus order by 2";
    						$resultat_liste = pmb_mysql_query($requete);
    						$nb_liste = pmb_mysql_num_rows($resultat_liste);
    						if ($nb_liste) {
    							$selector = "<select class='saisie-30em' id=\"form_".$field."\" name=\"form_".$field."\">";
    							$j = 0;
    							while ($j < $nb_liste) {
    								$liste_values = pmb_mysql_fetch_row( $resultat_liste );
    								$selector .= "<option value=\"".$liste_values[0]."\" " ;
    								if ($field_values[$i] == $liste_values[0]) {
    									$selector .= "selected='selected' " ;
    								}
    								$selector .= ">" . $liste_values[1] . "</option>\n" ;
    								$j++;
    							}
    							$selector .= "</select>";
    							$deflt_user_array['autorities'][] = static::get_field_selector($field, $selector);
    						}
				            break;

				        case 'deflt_camera_empr':
				            $deflt_user_array['empr'][] = static::get_field_checkbox($field, $field_values[$i]);
				            break;

				        case 'deflt_cashdesk':
    						$requete = "select * from cashdesk order by cashdesk_name";
    						$resultat_liste = pmb_mysql_query($requete);
    						$nb_liste = pmb_mysql_num_rows($resultat_liste);
    						if ($nb_liste) {
    							$selector = "<select class='saisie-30em' id=\"form_".$field."\" name=\"form_".$field."\">";
    							if(empty($field_values[$i])) {
    								$selector .= "<option value='0'>--</option>";
    							}
    							$j = 0;
    							while ($j < $nb_liste) {
    								$liste_values = pmb_mysql_fetch_object($resultat_liste);
    								$selector .= "<option value=\"".$liste_values->cashdesk_id."\" ";
    								if ($field_values[$i] == $liste_values->cashdesk_id) {
    									$selector .= "selected";
    								}
    								$selector .= ">" . htmlentities($liste_values->cashdesk_name, ENT_QUOTES, $charset) . "</option>\n";
    								$j++;
    							}
    							$selector .= "</select>";
    							$deflt_user_array['other'][] = static::get_field_selector($field, $selector);
    						}
				            break;

				        case 'deflt_notice_replace_keep_categories':
				            $deflt_user_array['records'][] = static::get_field_radio($field, $field_values[$i]);
				            break;

				        case 'deflt_notice_catalog_categories_auto':
				            $deflt_user_array['records'][] = static::get_field_radio($field, $field_values[$i]);
				            break;

				        case 'deflt_notice_is_new':
				            $deflt_user_array['records'][] = static::get_field_radio($field, $field_values[$i]);
				            break;

				        case 'deflt_short_loan_activate':
				            $deflt_user_array['expl'][] = static::get_field_radio($field, $field_values[$i]);
				            break;

				        case 'deflt_bulletinage_location':
				            $selector = gen_liste("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'form_'.$field, "", $field_values[$i], "", "", "0", $msg["all_location"], 0);
				            $deflt_user_array['records'][] = static::get_field_selector($field, $selector);
				            break;

				        case 'deflt_agnostic_warehouse':
				            $conn = new agnostic($base_path.'/admin/connecteurs/in/agnostic');
				            $conn->get_sources();
				            $selector = "<select id=\"form_".$field."\" name=\"form_".$field."\">
						<option value='0' ".(!$field_values[$i] ? "selected='selected'" : "").">".$msg['caddie_save_to_warehouse_none']."</option>";
				            if (is_array($conn->sources)) {
				                foreach ($conn->sources as $key_source => $source) {
				                    $selector .= "<option value='$key_source' ".($field_values[$i] == $key_source ? "selected='selected'" : "").">".htmlentities($source['NAME'],ENT_QUOTES,$charset)."</option>";
				                }
				            }
				            $selector .= "</select>";
				            $deflt_user_array['other'][] = static::get_field_selector($field, $selector);
				            break;

				        case 'deflt_cms_article_statut':
				            if ($cms_active && (SESSrights & CMS_AUTH)) {
				                $publications_states = new cms_editorial_publications_states();
				                $selector = "
							<select id=\"form_".$field."\" name=\"form_".$field."\">
								".$publications_states->get_selector_options($field_values[$i])."
							</select>";
				                $deflt_user_array['cms'][] = static::get_field_selector($field, $selector);
				            }
				            break;

				        case 'deflt_cms_article_type':
				            if ($cms_active && (SESSrights & CMS_AUTH)) {
				                $types = new cms_editorial_types('article');
				                $types->get_types();
				                $selector = "
							<select id=\"form_".$field."\" name=\"form_".$field."\">
								".$types->get_selector_options($field_values[$i])."
							</select>";
				                $deflt_user_array['cms'][] = static::get_field_selector($field, $selector);
				            }
				            break;

				        case 'deflt_cms_section_type':
				            if ($cms_active && (SESSrights & CMS_AUTH)) {
				                $types = new cms_editorial_types('section');
				                $types->get_types();
				                $selector = "
							<select id=\"form_".$field."\" name=\"form_".$field."\">
								".$types->get_selector_options($field_values[$i])."
							</select>";
				                $deflt_user_array['cms'][] = static::get_field_selector($field, $selector);
				            }
				            break;

				        case 'deflt_scan_request_status':
				            if ($pmb_scan_request_activate) {
				                $request_status_instance = new scan_request_admin_status();
				                $selector = "
							<select id=\"form_".$field."\" name=\"form_".$field."\">
								".$request_status_instance->get_selector_options($field_values[$i])."
							</select>";
				                $deflt_user_array['other'][] = static::get_field_selector($field, $selector);
				            }
				            break;

				        case 'deflt_catalog_expanded_caddies':
				            $deflt_user_array['other'][] = static::get_field_radio($field, $field_values[$i]);
				            break;

				        case 'deflt_notice_replace_links':
				            $selector = "<input type='radio' id='form_".$field."_0' name='form_".$field."' value='0' ".($field_values[$i]==0?"checked='checked'":"")." /> <label for='form_".$field."_0' style='all:unset'>".$msg['notice_replace_links_option_keep_all']."</label>
							<br /><input type='radio' id='form_".$field."_1' name='form_".$field."' value='1' ".($field_values[$i]==1?"checked='checked'":"")." /> <label for='form_".$field."_1' style='all:unset'>".$msg['notice_replace_links_option_keep_replacing']."</label>
							<br /><input type='radio' id='form_".$field."_2' name='form_".$field."' value='2' ".($field_values[$i]==2?"checked='checked'":"")." /> <label for='form_".$field."_2' style='all:unset'>".$msg['notice_replace_links_option_keep_replaced']."</label>";
				            $deflt_user_array['records'][] = static::get_field_selector($field, $selector);
				            break;

				        case 'deflt_printer':
				            if (substr($pmb_printer_name, 0, 9) == 'raspberry') {
				                $selector = "
								<select id=\"form_".$field."\" name=\"form_".$field."\">
									".raspberry::get_selector_options($field_values[$i])."
								</select>";
				                $deflt_user_array['other'][] = static::get_field_selector($field, $selector);
				            }
				            break;

				        case 'deflt_opac_visible_bulletinage':
				            $deflt_user_array['opac'][] = static::get_field_checkbox($field, $field_values[$i]);
				            break;

				        case 'deflt_type_abts':
				        	if (($pmb_gestion_abonnement==2)&&($pmb_gestion_financiere)) {
				        		$selector = gen_liste("select distinct id_type_abt, type_abt_libelle from type_abts order by 2 ", "id_type_abt", "type_abt_libelle", 'form_'.$field, "", $field_values[$i], "", "", "0", "", 0);
				        		$deflt_user_array['empr'][] = static::get_field_selector($field, $selector);
				        	}
				        	break;

				        case 'deflt_docwatch_watch_filter_deleted':
				        	$deflt_user_array['other'][] = static::get_field_radio($field, $field_values[$i]);
				        	break;

				        case 'deflt_associated_campaign':
				        	$deflt_user_array['other'][] = static::get_field_radio($field, $field_values[$i]);
				        	break;

				        case 'deflt_bypass_isbn_page':
				            $deflt_user_array['records'][] = static::get_field_checkbox($field, $field_values[$i]);
				            break;

				        case 'deflt_animation_waiting_list':
				            $deflt_user_array['animation'][] = static::get_field_radio($field, $field_values[$i]);
				            break;

				        case 'deflt_animation_automatic_registration':
				            $deflt_user_array['animation'][] = static::get_field_radio($field, $field_values[$i]);
				            break;

				        case 'deflt_animation_unique_registration':
				            $deflt_user_array['animation'][] = static::get_field_radio($field, $field_values[$i]);
				            break;
				        case 'deflt_dsi_diffusion_default_status':
							global $dsi_active;
							if($dsi_active == 2) {
								$query = "SELECT id_diffusion_status, name FROM dsi_diffusion_status";
								$result = pmb_mysql_query($query);
								$selector = "<select id=\"form_".$field."\" name=\"form_".$field."\">";
								while($row = pmb_mysql_fetch_assoc($result)) {
									$selected = "";
									if($field_values[$i] == $row['id_diffusion_status']) {
										$selected = "selected";
									}
									$selector .= "<option value=\"".$row['id_diffusion_status']."\" $selected>".$row['name']."</option>";
								}
				                $deflt_user_array['dsi'][] = static::get_field_selector($field, $selector);
				            }
							break;
				        case 'deflt_dsi_product_default_status':
							global $dsi_active;
							if($dsi_active == 2) {
								$query = "SELECT id_product_status, name FROM dsi_product_status";
								$result = pmb_mysql_query($query);
								$selector = "<select id=\"form_".$field."\" name=\"form_".$field."\">";
								while($row = pmb_mysql_fetch_assoc($result)) {
									$selected = "";
									if($field_values[$i] == $row['id_product_status']) {
										$selected = "selected";
									}
									$selector .= "<option value=\"".$row['id_product_status']."\" $selected>".$row['name']."</option>";
								}
				                $deflt_user_array['dsi'][] = static::get_field_selector($field, $selector);
				            }
							break;
				        default:
				            if ($field == "deflt_short_loan_activate" && $pmb_short_loan_management) {
				                $deflt_user_array['circ'][] = static::get_field_checkbox($field, $field_values[$i]);
				                break;
				            }

				            if ($field == "deflt_concept_scheme" && $thesaurus_concepts_active) {
				                $deflt_field = "<div class='row userParam-row'><div class='colonne60'>".$msg[$field]."</div>\n";
				                $deflt_field .= "<div class='colonne_suite'>";

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
				                $onto_handler = new onto_handler($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config, ONTOLOGY_NAMESPACE, 'http://www.w3.org/2004/02/skos/core#prefLabel', 'http://www.w3.org/2004/02/skos/core#ConceptScheme');
				                $params = new onto_param();
				                $params->concept_scheme = [$deflt_concept_scheme];
				                $onto_controler = new onto_skos_controler($onto_handler, $params);

				                $deflt_field .= onto_skos_concept_ui::get_scheme_list_selector($onto_controler, $params, true, '', 'form_deflt_concept_scheme');
				                $deflt_field .= "</div></div>\n";
				                $deflt_user_array['autorities'][] = $deflt_field;
				                break;
				            }

				            $deflt_table = substr($field, 6);
				            switch ($deflt_table) {
				                case 'integration_notice_statut':
				                    $deflt_table = 'notice_statut';
				                    break;
				                case 'serials_docs_type':
				                    $deflt_table = 'docs_type';
				                    break;
				            }

				            switch ($field) {
				                case "deflt_entites":
				                    $requete = "select id_entite, raison_sociale from ".$deflt_table." where type_entite='1' order by 2 ";
				                    break;
				                case "deflt_exercices":
				                    $requete = "select id_exercice, libelle from ".$deflt_table." order by 2 ";
				                    break;
				                case "deflt_rubriques":
				                    $requete = "select id_rubrique, concat(budgets.libelle,':', rubriques.libelle) from ".$deflt_table." join budgets on num_budget=id_budget order by 2 ";
				                    break;
				                case "deflt_notice_statut_analysis":
				                    $requete = "(select 0,'".addslashes($msg[$field."_parent"])."') union (select id_notice_statut, gestion_libelle from notice_statut order by 2)";
				                    break;
				                case "deflt_scan_request_explnum_status":
				                    $requete = "select * from explnum_statut order by 2";
				                    break;
				                case "deflt_explnum_location":
				                	$requete = "select * from docs_location order by 2";
				                	break;
				                case "deflt_explnum_lenders":
				                case "deflt_import_lenders":
				                	$requete = "select * from lenders order by 2";
				                	break;
				                default :
				                    $requete = "SELECT 1 WHERE 0>1";//résultat vide mais qui ne provoque pas d'erreur
				                    if (pmb_mysql_num_rows(pmb_mysql_query("SHOW TABLES LIKE '$deflt_table'"))) {
				                        $requete = "select * from ".$deflt_table." order by 2";
				                    }
				                    break;
				            }

				            $resultat_liste = pmb_mysql_query($requete);
				            $nb_liste = pmb_mysql_num_rows($resultat_liste);
				            if ($nb_liste) {
				                $selector = "
							<select class='saisie-30em' id=\"form_".$field."\" name=\"form_".$field."\">";
				                $j = 0;
				                while ($j < $nb_liste) {
				                    $liste_values = pmb_mysql_fetch_row($resultat_liste);
				                    $selector .= "<option value=\"".$liste_values[0]."\" " ;
				                    if ($field_values[$i] == $liste_values[0]) {
				                        $selector .= "selected='selected' " ;
				                    }
				                    $selector .= ">" . $liste_values[1] . "</option>\n" ;
				                    $j++;
				                }
				                $selector .= "</select>";
				                $type = self::get_type_from_field($field);
				                $deflt_user_array[$type][] = static::get_field_selector($field, $selector);
				            }
				            break;
				    }
				    break;

				case "param_" :
				    switch ($field) {
				        case 'param_allloc':
						    $checked = '';
    						if ($field_values[$i] == 1) {
    						    $checked = 'checked';
    						}

    						$param_user_allloc = "
    						    <div class='row userParam-row'>
    						        <div class='colonne60'><label for='form_$field' style='all: unset'>".$msg[$field]."</label></div>\n
						            <div class='colonne_suite'>
    						            <input type='checkbox' class='checkbox' $checked value='1' id='form_$field' name='form_$field'>
						            </div>
					            </div>\n";
    						break;
				        default:
				            $checked = '';
				            if ($field_values[$i] == 1) {
				                $checked = ' checked';
				            }

    						$param_user .= "
    						    <div class='row'>
    						        <input type='checkbox' class='checkbox' $checked value='1' id='form_$field' name='form_$field'>\n
    						        <label for='form_$field' style='all: unset'>$msg[$field]</label>
						        </div>\n";
				            break;
				    }
					break;

				case "value_":
					switch ($field) {
						case "value_deflt_fonction":
							$flist = new marc_list('function');
							$f = (isset($flist->table[$field_values[$i]]) ? $flist->table[$field_values[$i]] : '');
							$selector = "
    							<div class='row userParam-row'>
    							    <div class='colonne60'>
    							        <label for='form_value_deflt_fonction_libelle' style='all: unset'>$msg[$field]&nbsp;:&nbsp;</label>
							        </div>\n
							        <div class='colonne_suite'>
            							<input type='text' class='saisie-30emr' id='form_value_deflt_fonction_libelle' name='form_value_deflt_fonction_libelle' completion='fonction' value='".htmlentities($f,ENT_QUOTES, $charset)."' />
            						    <input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=".$caller."&p1=form_value_deflt_fonction&p2=form_value_deflt_fonction_libelle', 'selector')\" />
            							<input type='button' class='bouton_small' value='X' onclick=\"this.form.elements['form_value_deflt_fonction'].value='';this.form.elements['form_value_deflt_fonction_libelle'].value='';return false;\" />
            							<input type='hidden' name='form_value_deflt_fonction' id='form_value_deflt_fonction' value=\"$field_values[$i]\" />
        							</div>
    							</div>
    							<br />";
							break;
						case "value_deflt_lang":
							$llist = new marc_list('lang');
							if(!empty($field_values[$i])) {
							    $l = $llist->table[$field_values[$i]];
							} else {
							    $l = '';
							}
							$selector = "
							    <div class='row userParam-row'>
							        <div class='colonne60'>
							            <label for='form_value_deflt_lang_libelle' style='all: unset'>$msg[$field]&nbsp;:&nbsp;</label>
						            </div>\n
						            <div class='colonne_suite'>
            							<input type='text' class='saisie-30emr' id='form_value_deflt_lang_libelle' name='form_value_deflt_lang_libelle' completion='lang' value='".htmlentities($l,ENT_QUOTES, $charset)."' />
            						    <input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=lang&caller=".$caller."&p1=form_value_deflt_lang&p2=form_value_deflt_lang_libelle', 'selector')\" />
            							<input type='button' class='bouton_small' value='X' onclick=\"this.form.elements['form_value_deflt_lang'].value='';this.form.elements['form_value_deflt_lang_libelle'].value='';return false;\" />
            							<input type='hidden' name='form_value_deflt_lang' id='form_value_deflt_lang' value=\"$field_values[$i]\" />
        							</div>
    							</div>
    							<br />";
							break;
						case "value_deflt_relation":
						case "value_deflt_relation_serial":
						case "value_deflt_relation_bulletin":
						case "value_deflt_relation_analysis":
							$temp_selector = notice_relations::get_selector("form_$field", $field_values[$i]);
							$selector = static::get_field_selector($field, $temp_selector);
							break;
						case "value_deflt_module":
							$arrayModules = array(
    							'dashboard' => $msg['dashboard'],
    							'circu' => $msg['5'],
    							'catal' => $msg['93'],
    							'autor' => $msg['132'],
    							'edit' => $msg['1100'],
    							'dsi' => $msg['dsi_droit'],
    							'acquis' => $msg['acquisition_droit'],
    							'admin' => $msg['7'],
    							'cms' => $msg['cms_onglet_title'],
    							'account' => $msg['933'],
    							'fiches' => $msg['onglet_fichier']
							);
							$temp_selector = "<select id='form_$field' name='form_$field'>";
							foreach ($arrayModules as $k => $v) {
							    $temp_selector .= "<option value='$k'";
								if ($k == $field_values[$i]) {
								    $temp_selector .= " selected";
								}
								$temp_selector .= ">$v</option>";
							}
							$temp_selector .= "</select>";
							$selector = static::get_field_selector($field, $temp_selector);
							break;
						default:
						    $selector = "
						        <div class='row userParam-row'>
						            <div class='colonne60'>
						                <label for='form_$field' style='all: unset'>$msg[$field]&nbsp;:&nbsp;</label>
					                </div>\n
        							<div class='colonne_suite'>
        							    <input type='text' class='saisie-20em' id='form_$field' name='form_$field' value='".htmlentities($field_values[$i],ENT_QUOTES, $charset)."' />
							        </div>
						        </div>
						        <br />";
							break;
					}
					$type = self::get_type_from_field($field);
					$deflt_user_array[$type][] = $selector;
					break;

				case "deflt2":
				    switch ($field) {
				        default:
    						$deflt_table = substr($field, 6);
    						$requete = "select * from $deflt_table order by 2";
    						$resultat_liste = pmb_mysql_query($requete);
    						$nb_liste = pmb_mysql_num_rows($resultat_liste);
    						if (!empty($nb_liste)) {
    						    $selector = "<div class='row userParam-row'><div class='colonne60'><label for='form_".$field."' style='all:unset'>".$msg[$field]."&nbsp;:&nbsp;</label></div>\n";
    						    $selector .= "<div class='colonne_suite'><select class='saisie-30em' id=\"form_".$field."\" name=\"form_".$field."\">";

    							$j = 0;
    							while ($j < $nb_liste) {
    								$liste_values = pmb_mysql_fetch_row($resultat_liste);
    								$selector .= "<option value='" . $liste_values[0] . "'" ;
    								if ($field_values[$i] == $liste_values[0]) {
    								    $selector .= " selected='selected'";
    								}
    								$selector .= ">" . $liste_values[1] . "</option>\n" ;
    								$j++;
    							}
    							$selector .= "</select></div></div>\n" ;
    							$type = self::get_type_from_field($field);
    							$deflt_user_array[$type][] = $selector;
    							if ($field == 'deflt2docs_location') {
    							    $deflt_user_array[$type][] = '!!param_allloc!!';
    							}
    						}
				            break;
					}
					break;

				case "xmlta_":
					switch ($field) {
						case "xmlta_indexation_lang":
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
							$deflt_user_array['records'][] = static::get_field_selector($field, $combo);
							break;
						case "xmlta_doctype_serial":
							$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "");
							$deflt_user_array['records'][] = static::get_field_selector($field, $select_doc->display);
							break;
						case "xmlta_doctype_bulletin":
						case "xmlta_doctype_analysis":
							$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "","0",$msg[$field."_parent"]);
							$deflt_user_array['records'][] = static::get_field_selector($field, $select_doc->display);
							break;
						case "xmlta_doctype_scan_request_folder_record":
							if ($pmb_scan_request_activate) {
								$select_doc = new marc_select("doctype", "form_".$field, $field_values[$i], "");
								$deflt_user_array['records'][] = static::get_field_selector($field, $select_doc->display);
							}
							break;
						default :
							$deflt_table = substr($field,6);
							$select_doc = new marc_select("$deflt_table", "form_".$field, $field_values[$i], "");
							$type = self::get_type_from_field($field);
							$deflt_user_array[$type][] = static::get_field_selector($field, $select_doc->display);
							break;
					}
				case "deflt3":
					$q = '';
					$t = array();
					if ($acquisition_active) {
    					switch ($field) {
    						case "deflt3bibli":
    							$q = "select 0,'".addslashes($msg['deflt3none'])."' union ";
    							$q .= "select id_entite, raison_sociale from entites where type_entite='1' order by 2 ";
    							break;
    						case "deflt3exercice":
    							$q = "select 0,'".addslashes($msg['deflt3none'])."' union ";
    							$q .= "select id_exercice, libelle from exercices order by 2 ";
    							break;
    						case "deflt3rubrique":
    							$q = "select 0,'".addslashes($msg['deflt3none'])."' union ";
    							$q .= "select id_rubrique, concat(budgets.libelle,':', rubriques.libelle) from rubriques join budgets on num_budget=id_budget order by 2 ";
    							break;
    						case "deflt3type_produit":
    							$q = "select 0,'".addslashes($msg['deflt3none'])."' union ";
    							$q .= "select id_produit, libelle from types_produits order by 2 ";
    							break;
    						case "deflt3dev_statut":
    							$t = actes::getStatelist(TYP_ACT_DEV);
    							break;
    						case "deflt3cde_statut":
    							$t = actes::getStatelist(TYP_ACT_CDE);
    							break;
    						case "deflt3liv_statut":
    							$t = actes::getStatelist(TYP_ACT_LIV);
    							break;
    						case "deflt3fac_statut":
    							$t = actes::getStatelist(TYP_ACT_FAC);
    							break;
    						case "deflt3sug_statut":
    							$m = new suggestions_map();
    							$t = $m->getStateList();
    							break;
    						case 'deflt3lgstatcde':
    						case 'deflt3lgstatdev':
    							$q = lgstat::getList('QUERY');
    							break;
    						case 'deflt3receptsugstat':
    							$m = new suggestions_map();
    							$t = $m->getStateList('ORDERED',TRUE);
    							break;
    					}

    					if ($q) {
    						$r = pmb_mysql_query($q);
    						if (pmb_mysql_num_rows($r)) {
        						while ($row = pmb_mysql_fetch_row($r)) {
        							$t[$row[0]] = $row[1];
        						}
    						}
    					}

    					if (count($t)) {
    						$deflt3user = "<div class='row userParam-row'><div class='colonne60'><label for='form_$field' style='all:unset'>".$msg[$field]."&nbsp;:&nbsp;</label></div>\n";
    						$deflt3user .= "<div class='colonne_suite'><select class='saisie-30em' id='form_$field' name='form_$field'>";
    						foreach ($t as $k => $v) {
    							$deflt3user .= "<option value='$k'";
    							if ($field_values[$i] == $k) {
    								$deflt3user .= " selected='selected'";
    							}
    							$deflt3user .= ">" . htmlentities($v, ENT_QUOTES, $charset) . "</option>\n";
    						}
    						$deflt3user .= "</select></div></div><br />\n";
    						$type = self::get_type_from_field($field);
    						$deflt_user_array['acquisition'][] = $deflt3user;
    					}
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
		if ($caller == 'userform') {
			// Visibilité des exemplaires
			if ($pmb_droits_explr_localises) {
				$explr_tab_invis = explode(",", $explr_invisible);
				$explr_tab_unmod = explode(",", $explr_visible_unmod);
				$explr_tab_modif = explode(",", $explr_visible_mod);

				$visibilite_expl_user = "
				<div class='row userParam-row'>
					<div class='colonne60'>".$msg["expl_visibilite"]."</div>
					<div class='colonne_suite'>";
				$requete_droits_expl = "select idlocation, location_libelle from docs_location order by location_libelle";
				$resultat_droits_expl = pmb_mysql_query($requete_droits_expl);
				$temp = "";
				while ($j = pmb_mysql_fetch_array($resultat_droits_expl)) {
					$temp .= $j["idlocation"].",";
					$visibilite_expl_user .= "<div class='row userParam-row'>" . $j["location_libelle"] . " :
						    <select name=\"form_expl_visibilite_".$j["idlocation"]."\">
					";
					$as_invis = array_search($j["idlocation"], $explr_tab_invis);
					$as_unmod = array_search($j["idlocation"], $explr_tab_unmod);
					$as_mod = array_search($j["idlocation"], $explr_tab_modif);
					$visibilite_expl_user .= "\n<option value='explr_invisible'" . ($as_invis !== FALSE && $as_invis !== NULL ? " selected='selected'" : "") . ">" . $msg["explr_invisible"] . "</option>";
					if (($as_mod !== FALSE && $as_mod !== NULL) || ($as_unmod !== FALSE && $as_unmod !== NULL) || ($as_invis !== FALSE && $as_invis !== NULL)) {
						$visibilite_expl_user .= "\n<option value='explr_visible_unmod'".($as_unmod !== FALSE && $as_unmod !== NULL ? " selected='selected'" : "") . ">" . $msg["explr_visible_unmod"] . "</option>";
					} else {
						$visibilite_expl_user .= "\n<option value='explr_visible_unmod' selected='selected' >" . $msg["explr_visible_unmod"] . "</option>";
					}
					$visibilite_expl_user .= "\n<option value='explr_visible_mod'".($as_mod !== FALSE && $as_mod !== NULL ? " selected='selected'" : "") . ">" . $msg["explr_visible_mod"] . "</option>";
					$visibilite_expl_user .= "</select></div>\n";
				}
				$visibilite_expl_user .= "</div>";
				pmb_mysql_free_result($resultat_droits_expl);

				if (empty($explr_invisible) && empty($explr_visible_unmod) && empty($explr_visible_mod)) {
					$rqt = "UPDATE users SET explr_invisible=0,explr_visible_mod=0,explr_visible_unmod='".substr($temp, 0, strlen($temp) - 1)."' WHERE userid=$id";
					@pmb_mysql_query($rqt);
				}

				$deflt_user_array['expl'][] = $visibilite_expl_user;
			}
		}

		$param_default = '';
		if ($param_user) {
		    $param_default .= "
		        <div class='row'>
		            <hr />
		        </div>$param_user";
		}

		if (!empty($deflt_user_array)) {
    		// Affichage du HR + msg entre paramètres et valeurs
		    $param_default .= "
		        <div class='row'>
		            <hr />
		        </div>
		        <div class='row parammenu'>
		            <b>".$msg["1501"]."</b>
                </div>";

		    // Affichage des boutons expandAll et collapseAll
		    $param_default .= $begin_result_liste;

		    $user_params = array();
		    foreach ($deflt_user_array as $type => $array_values) {
		        $user_params[self::get_header_from_type($type)][$type] = $array_values;
		    }
		    ksort($user_params);

		    $param_default .= "<div id='favoritesContent' class='row'>";
    		// Affichage des valeurs de l'applications
		    foreach ($user_params as $header => $values) {
    		    foreach ($values as $type => $array_user_values) {
        		    $param_default .= "
        		        <div id='el" . $type . "Parent' class='parent' style='width:100%'>
                            ".get_expandBase_button('el' . $type)."
            	            <span class='heada'>". $header ."</span>
            	            <br />
        				</div>
        				<div id='el" . $type . "Child' class='child' style='margin-bottom:6px;display:none;'>
        				    <table role='presentation'>";
        		    foreach ($array_user_values as $user_value) {
        		        if ($user_value == '!!param_allloc!!') {
        		            $user_value = str_replace("!!param_allloc!!", $param_user_allloc, $user_value);
        		        }
        		        $datasearch_value = explode("'colonne60'>", $user_value);
        		        $datasearch_value = explode("</div>", $datasearch_value[1]);
        		        $datasearch_value = $datasearch_value[0];
        		        $datasearch_value = strtolower(encoding_normalize::json_encode(array('search_value' => $datasearch_value),JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP));

        		        $param_default .= "<tr data-search='$datasearch_value'><td>$user_value</td></tr>";
        		    }
        		    $param_default .= "</table></div>";
    		    }
    		}
    		$param_default .= '</div>';
		}

		if ($value_user) {
		    $param_default .= "<div class='row'><hr /></div>".$value_user;
		}

		if ($caller == 'userform') {
		    $param_default .= "<div class='row'><hr /></div>".$deflt_user_style."<br />";
        }

		if ($speci_user) {
			$param_default.= "<div class='row'><hr /></div>";
			$param_default.=$speci_user;
			$param_default.= "<div class='row'></div>";
		}
		$param_default .= "<script type='text/javascript' src='./javascript/ajax.js'></script>";
		$param_default .=  "<script type='text/javascript'>
                require(['dojo/ready', 'apps/pmb/FavoritesRefactor'], function(ready, FavoritesRefactor){
                    ready(function(){
                        new FavoritesRefactor();
                    });
                });
           </script>";
		$param_default .= "<script type='text/javascript'>ajax_parse_dom();</script>";
		return $param_default;
	}

	public static function get_param($id, $field) {
		$id = intval($id);
		$param = '';
		if($id) {
			$query = "SELECT ".$field." FROM users WHERE userid='".$id."' ";
			$result = pmb_mysql_query($query);
			$param = pmb_mysql_result($result, 0, 0);
		}
		return $param;
	}

	public static function get_name($id) {
		$id = intval($id);
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

	public static function get_grp_num($id) {
		$id = intval($id);
		if($id) {
			$query = "SELECT grp_num FROM users WHERE userid='".$id."' ";
			$result = pmb_mysql_query($query);
			return pmb_mysql_result($result, 0, 'grp_num');
		}
		return 0;
	}

	public static function get_header_from_type($type) {
	    global $msg;

	    $header = '';
	    switch ($type) {
	        case 'records':
	            $header = $msg['admin_menu_notices'];
	            break;
	        case 'cms':
	            $header = $msg['cms_onglet_title'];
	            break;
	        case 'autorities':
	            $header = $msg['admin_menu_authorities'];
	            break;
	        case 'expl':
	            $header = $msg['admin_menu_exemplaires'];
	            break;
	        case 'collstate':
	            $header = $msg['admin_menu_collstate'];
	            break;
	        case 'explnum':
	            $header = $msg['admin_menu_upload_docnum'];
	            break;
	        case 'empr':
	            $header = $msg['param_empr'];
	            break;
	        case 'opac':
	            $header = $msg['admin_menu_opac'];
	            break;
	        case 'other':
	            $header = $msg['search_extended_lonely_fields'];
	            break;
	        case 'acquisition':
	            $header = $msg['acquisition_menu_title'];
	            break;
	        case 'animation':
	            $header = $msg['admin_menu_animation'];
	            break;
	        case 'dsi':
	            $header = $msg['admin_menu_dsi'];
	            break;
	        default:
	            $header = 'Automatique';
	            break;
	    }
	    return $header;
	}

	public static function get_type_from_field($field) {
	    switch ($field) {
	        case 'deflt_notice_statut':
	        case 'deflt_notice_statut_analysis':
	        case 'deflt_integration_notice_statut':
	        case 'xmlta_doctype':
	        case 'value_deflt_lang':
	        case 'value_deflt_fonction':
	        case 'value_deflt_relation':
	        case 'value_deflt_relation_serial':
	        case 'value_deflt_relation_bulletin':
	        case 'value_deflt_relation_analysis':
	            $type = 'records';
	            break;
	        case 'deflt_docs_type':
	        case 'deflt_serials_docs_type':
	        case 'deflt_lenders':
	        case 'deflt_docs_statut':
	        case 'deflt_docs_codestat':
	        case 'value_prefix_cote':
	        case 'deflt_import_lenders':
	            $type = 'expl';
	            break;
	        case 'deflt_empr_statut':
	        case 'deflt_empr_categ':
	        case 'deflt_empr_codestat':
	        case 'deflt2docs_location':
	        case 'deflt_type_abts':
	            $type = 'empr';
	            break;
	        case 'deflt_thesaurus':
	        case 'deflt_pclassement':
	            $type = 'autorities';
	            break;
	        case 'deflt_arch_statut':
	        case 'deflt_arch_emplacement':
	        case 'deflt_arch_type':
	            $type = 'collstate';
	            break;
	        case 'deflt_explnum_statut':
	        case 'deflt_explnum_location':
	        case 'deflt_explnum_lenders':
	        case 'deflt_scan_request_explnum_status':
	            $type = 'explnum';
	            break;
	        case 'value_deflt_module':
	        case 'value_email_bcc':
	        case 'value_deflt_antivol':
	        default:
	            $type = 'other';
	            break;
	    }
	    return $type;
	}
} // fin de déclaration de la classe user