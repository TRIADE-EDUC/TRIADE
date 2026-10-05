<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette.class.php,v 1.249.2.5.2.1 2025/03/14 08:07:34 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
global $gestion_acces_active, $gestion_acces_empr_notice;

require_once ("$class_path/search.class.php") ;
require_once ("$class_path/equation.class.php") ;
require_once ("$class_path/mono_display.class.php") ;
require_once ("$class_path/serial_display.class.php") ;
require_once ($class_path."/record_display.class.php") ;
require_once ($include_path."/mail.inc.php") ;
require_once ($include_path."/export_notices.inc.php");
require_once($class_path."/export_param.class.php");
require_once($class_path."/notice_tpl_gen.class.php");
if($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
	require_once ("$class_path/acces.class.php") ;
}
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/categories.class.php");
require_once($class_path."/bannette_facettes.class.php");
require_once($class_path."/bannette_descriptors.class.php");
require_once($class_path."/bannette_equations.class.php");
require_once($class_path."/bannette_diffusion.class.php");
require_once($class_path."/bannette_tpl.class.php");
require_once($class_path."/caddie.class.php");
require_once($class_path."/campaigns/campaign.class.php");
require_once($class_path.'/elements_list/elements_records_list_ui.class.php');
require_once($class_path.'/audit.class.php');

// définition de la classe de gestion des 'bannettes'
class bannette {

	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $id_bannette=0;
	public $num_classement=1;
	public $nom_classement="";
	public $nom_bannette="";
	public $comment_gestion="";
	public $comment_public="";
	public $entete_mail="";
	public $bannette_tpl_num=0;
	public $piedpage_mail="";
	public $notice_display_type=0;
	public $notice_tpl="";
	public $django_directory="";
	public $date_last_remplissage="";
	public $date_last_envoi="";
	public $aff_date_last_remplissage="";
	public $aff_date_last_envoi="";
	public $date_last_envoi_sql="";
	public $proprio_bannette=0;
	public $bannette_auto=0;
	public $periodicite=0;
	public $diffusion_email=1;
	public $nb_notices_diff=0;
	public $categorie_lecteurs=array();
	public $groupe_lecteurs=array();
	public $update_type="C";
	public $nb_notices=0;
	public $nb_abonnes=0;
	public $alert_diff=0;
	public $num_panier=0;
	public $limite_type=""; // D ou  I : Days ou Items
	public $limite_nombre=0; // Nombre limite, = soit durée de vie d'une notice dans la bannette ou bien nombre maxi de notices dans le panier
	public $liste_id_notice = array();
	public $export_contenu;
	public $typeexport = "";
	public $prefixe_fichier = "prefix_";
	public $param_export = array();
	public $group_pperso=0;
	public $display_notice_in_every_group=1;
	public $archive_number=0;
	public $diffusions_history=0;
	public $group_type = 0;
	public $statut_not_account=0;
	public $associated_campaign=0;
	public $num_sender=0;
	public $field_type='';
	public $field_id=0;
	public $group_pperso_order=array();
	public $document_generate=0;
	public $document_notice_display_type=0;
	public $document_notice_tpl=0;
	public $document_django_directory=0;
	public $document_insert_docnum=0;
	public $document_group=0;
	public $document_add_summary=0;
	public $aff_document="";
	public $bannette_opac_accueil=0;
	public $document_diffuse=""; //contenu html du document généré
	protected $bannette_descriptors;
	public $id_bannette_origine = 0; //Utilisé en duplication de bannette
	public $bannette_aff_notice_number = 1; //Afficher le nombre de notices envoyées dans le mail
	public $p_perso = null;
	public $tri_tpl;
	protected $bannette_equations;
	protected $equations_notices = array();
	protected $bannette_diffusion;
	protected static $instances;
	protected static $lang_messages;

	protected $use_limit=1;
	protected $list;
	protected $list_group;
	protected $notice_group;

	protected $output_format;
	protected static $controller;


	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id_bannette = intval($id);
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos
	// ---------------------------------------------------------------
	public function getData() {
		global $msg;
		global $deflt_associated_campaign;

		$this->p_perso=new parametres_perso("notices");
		if (!$this->id_bannette) {
			// pas d'identifiant. on retourne un tableau vide
		 	$this->date_last_envoi=today();
			$this->aff_date_last_envoi=formatdate($this->date_last_envoi);
			$this->date_last_envoi_sql=today();
			$this->associated_campaign = intval($deflt_associated_campaign);
		} else {
			$requete = "SELECT id_bannette, num_classement, nom_bannette,comment_gestion,comment_public,statut_not_account, associated_campaign, bannette_num_sender, ";
			$requete .= "date_last_remplissage, date_format(date_last_remplissage, '".$msg["format_date_heure"]."') as aff_date_last_remplissage, ";
			$requete .= "date_last_envoi,date_last_envoi as date_last_envoi_sql, date_format(date_last_envoi, '".$msg["format_date_heure"]."') as aff_date_last_envoi, ";
			$requete .= "proprio_bannette,bannette_auto,periodicite,diffusion_email, nb_notices_diff, update_type, entete_mail, bannette_tpl_num, piedpage_mail, notice_display_type, notice_tpl, django_directory, num_panier, ";
			$requete .= "limite_type, limite_nombre, typeexport, prefixe_fichier, param_export, group_type, group_pperso, display_notice_in_every_group, archive_number, bannette_diffusions_history, ";
			$requete .= "document_generate, document_notice_display_type, document_notice_tpl, document_django_directory, document_insert_docnum, document_group, document_add_summary, bannette_opac_accueil, bannette_aff_notice_number ";
			$requete .= "FROM bannettes WHERE id_bannette='".$this->id_bannette."' " ;
			$result = pmb_mysql_query($requete) or die ($requete."<br /> in bannette.class.php : ".pmb_mysql_error());
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
			 	$this->id_bannette			= $temp->id_bannette ;
			 	$this->num_classement 		= $temp->num_classement ;
				$this->nom_bannette			= $temp->nom_bannette ;
				$this->comment_gestion		= $temp->comment_gestion ;
				$this->comment_public		= $temp->comment_public ;
				$this->bannette_tpl_num			= $temp->bannette_tpl_num ;
				$this->entete_mail			= $temp->entete_mail ;
				$this->piedpage_mail		= $temp->piedpage_mail ;
				$this->notice_display_type	= $temp->notice_display_type ;
				$this->notice_tpl			= $temp->notice_tpl ;
				$this->django_directory     = $temp->django_directory;
				$this->date_last_remplissage= $temp->date_last_remplissage ;
				$this->date_last_envoi		= $temp->date_last_envoi ;
				$this->aff_date_last_remplissage	= $temp->aff_date_last_remplissage ;
				$this->aff_date_last_envoi	= $temp->aff_date_last_envoi ;
				$this->date_last_envoi_sql	= $temp->date_last_envoi_sql;
				$this->proprio_bannette		= $temp->proprio_bannette;
				$this->bannette_auto		= $temp->bannette_auto;
				$this->periodicite			= $temp->periodicite;
				$this->diffusion_email		= $temp->diffusion_email;
				$this->nb_notices_diff 		= $temp->nb_notices_diff;
				$this->update_type			= $temp->update_type;
				$this->num_panier			= $temp->num_panier;
				$this->limite_type 			= $temp->limite_type;
				$this->limite_nombre 		= $temp->limite_nombre;
				$this->typeexport 			= $temp->typeexport;
				$this->prefixe_fichier 		= $temp->prefixe_fichier;
				$this->group_pperso 		= $temp->group_pperso;
				$this->group_type 			= $temp->group_type;
				$this->display_notice_in_every_group=$temp->display_notice_in_every_group;
				$this->statut_not_account 	= $temp->statut_not_account;
				$this->associated_campaign 	= $temp->associated_campaign;
				$this->num_sender 			= $temp->bannette_num_sender;
				$this->archive_number 		= $temp->archive_number;
				$this->diffusions_history	= $temp->bannette_diffusions_history;
				$this->document_generate 	= $temp->document_generate ;
				$this->document_notice_display_type	= $temp->document_notice_display_type;
				$this->document_notice_tpl	= $temp->document_notice_tpl;
				$this->document_django_directory = $temp->document_django_directory;
				$this->document_insert_docnum= $temp->document_insert_docnum;
				$this->document_group 		= $temp->document_group;
				$this->document_add_summary = $temp->document_add_summary;
				$this->bannette_opac_accueil= $temp->bannette_opac_accueil;
				$this->bannette_aff_notice_number= $temp->bannette_aff_notice_number;
				$this->param_export			= unserialize($temp->param_export);
				$this->compte_elements();
				$requete = "SELECt nom_classement FROM classements WHERE id_classement='".$this->num_classement."'" ;
				$resultclass = pmb_mysql_query($requete) or die ($requete."<br /> in bannette.class.php : ".pmb_mysql_error());
				if ($temp = pmb_mysql_fetch_object($resultclass)) $this->nom_classement = $temp->nom_classement ;
				else $this->nom_classement = "" ;

				$rqt = "select * from bannette_empr_groupes where empr_groupe_num_bannette = '".$this->id_bannette."'";
				$res = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($res)){
					while($row = pmb_mysql_fetch_object($res)){
						$this->groupe_lecteurs[] = $row->empr_groupe_num_groupe;
					}
				}
				$rqt = "select * from bannette_empr_categs where empr_categ_num_bannette = '".$this->id_bannette."'";
				$res = pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($res)){
					while($row = pmb_mysql_fetch_object($res)){
						$this->categorie_lecteurs[] = $row->empr_categ_num_categ;
					}
				}
			}
		}
		$this->bannette_descriptors = new bannette_descriptors($this->id_bannette);
		$this->bannette_equations = new bannette_equations($this->id_bannette);
	}

	public function gen_facette_selection(){
		$facette = new bannette_facettes($this->id_bannette);
		return $facette->gen_facette_selection();
	}

	protected function get_senders_selector() {
		global $msg, $charset;

		$selector = "<select name='num_sender'>
				<option value='0'>".htmlentities($msg['dsi_ban_senders_default'], ENT_QUOTES, $charset)."</option>";
		$query = "SELECT userid, CONCAT(prenom, ' ', nom, ' (', user_email, ')') as name FROM users WHERE user_email <> ''";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$selector .= "<option value='".$row->userid."' ".($this->num_sender == $row->userid ? "selected='selected'" : "").">".htmlentities($row->name, ENT_QUOTES, $charset)."</option>";
			}
		}
		$selector .= "</select>";
		return $selector;
	}

	protected function get_classement_content_form($type="pro") {
		global $dsi_bannette_classement_content_form;

		if ($type=="pro") {
			$content_form = $dsi_bannette_classement_content_form;
			$content_form = str_replace('!!num_classement!!', show_classement_utilise ('BAN', $this->num_classement, 0), $content_form);
		} else {
			$content_form = "<input type=hidden name=num_classement value=0 />";
		}
		return $content_form;
	}

	protected function get_common_content_form($type="pro") {
		global $charset;
		global $dsi_bannette_common_content_form, $dsi_bannette_form_selvars;

		$content_form = $dsi_bannette_common_content_form;
		$content_form = str_replace('!!nom_bannette!!', htmlentities($this->nom_bannette,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!classement!!', $this->get_classement_content_form($type), $content_form);
		$content_form = str_replace('!!comment_gestion!!', htmlentities($this->comment_gestion,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!comment_public!!', htmlentities($this->comment_public,ENT_QUOTES, $charset), $content_form);

		//ajout champs emprunteur
		if ($type=="pro") {
			$comment_public_info_empr = str_replace('!!selector_name!!', 'comment_public_selvars_id',  $dsi_bannette_form_selvars);
			$comment_public_info_empr = str_replace('!!dest_dom_node!!', 'comment_public',  $comment_public_info_empr);
			$content_form = str_replace('!!comment_public_info_empr!!', $comment_public_info_empr,  $content_form);
		} else {
			$content_form = str_replace('!!comment_public_info_empr!!', '',  $content_form);
		}

		$bannette_tpl_list=bannette_tpl::gen_tpl_select("bannette_tpl_num",$this->bannette_tpl_num);
		$content_form = str_replace('!!bannette_tpl_list!!', $bannette_tpl_list, $content_form);
		$content_form = str_replace('!!entete_mail!!', htmlentities($this->entete_mail,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!piedpage_mail!!', htmlentities($this->piedpage_mail,ENT_QUOTES, $charset), $content_form);

		$info_empr = str_replace('!!selector_name!!', 'selvars_id',  $dsi_bannette_form_selvars);
		$info_empr = str_replace('!!dest_dom_node!!', 'entete_mail',  $info_empr);
		$content_form = str_replace('!!info_empr!!', $info_empr,  $content_form);

		return $content_form;
	}

	protected function get_entity_content_form($type="pro") {
		global $msg;
		global $dsi_bannette_entity_records_content_form;
		global $dsi_bannette_entity_records_content_form_abo;
		global $dsi_notice_group_by_default;

		$content_form = $dsi_bannette_entity_records_content_form;
		if ($type=="abo") {
			$content_form = $dsi_bannette_entity_records_content_form_abo;
		}

		$group_by = array();
		$group_by = explode(',', $dsi_notice_group_by_default);

		$content_form = str_replace('!!notice_tpl!!', notice_tpl_gen::gen_tpl_select("notice_tpl",$this->notice_tpl), $content_form);


		$selected_id = $this->group_pperso;
		if (!$this->id_bannette && !empty($group_by) && !empty($group_by[0]) && trim($group_by[0]) == "cp" && !empty($group_by[1]) && intval($group_by[1]) != 0) {
		    $selected_id = $group_by[1];
		}

		$liste_p_perso = $this->p_perso->gen_liste_field("group_pperso", $selected_id, $msg["dsi_ban_form_regroupe_pperso_no"]);
		$content_form = str_replace('!!pperso_group!!', $liste_p_perso, $content_form);

		$facette_group = $this->gen_facette_selection();
		if ($this->id_bannette_origine) {
    		// On vient d'une duplication
			$origine_bannette = new bannette($this->id_bannette_origine);
			$facette_group = $origine_bannette->gen_facette_selection();
		}
		$content_form = str_replace('!!facette_group!!', $facette_group, $content_form);
		$content_form = str_replace("!!display_notice_in_every_group!!", ($this->display_notice_in_every_group ? "checked='checked'" : ""), $content_form);

		if (!$this->id_bannette && !empty($group_by) && !empty($group_by[0])) {

    		$checked_group_facette = "";
    		$checked_group_pperso = " checked='checked' ";
		    if (trim($group_by[0]) == "f") {
    		    $checked_group_facette = " checked='checked' ";
    		    $checked_group_pperso = "";
		    }

		} else {
    		$checked_group_facette = "";
    		$checked_group_pperso = " checked='checked' ";
    		if ($this->group_type) {
    		    $checked_group_facette = " checked='checked' ";
    		    $checked_group_pperso = "";
    		}
		}

		$content_form = str_replace('!!checked_group_facette!!', $checked_group_facette, $content_form);
		$content_form = str_replace('!!checked_group_pperso!!', $checked_group_pperso, $content_form);

		return $content_form;
	}

	protected function get_archive_content_form($type="pro") {
		global $dsi_bannette_archive_content_form;

		if ($type=="pro") {
			$content_form = $dsi_bannette_archive_content_form;
			$content_form = str_replace('!!archive_number!!', $this->archive_number, $content_form);
		} else {
			$content_form = "";
		}
		return $content_form;
	}

	protected function get_diffusions_history_content_form($type="pro") {
		global $dsi_bannette_diffusions_history_content_form;

		if ($type=="pro") {
			$content_form = $dsi_bannette_diffusions_history_content_form;
			$content_form = str_replace('!!diffusions_history!!', ($this->diffusions_history ? "checked='checked'" : ""), $content_form);
		} else {
			$content_form = "";
		}
		return $content_form;
	}

	protected function get_options_content_form($type="pro") {
		global $msg, $charset;
		global $nom_prenom_abo;
		global $dsi_bannette_options_content_form;

		$content_form = $dsi_bannette_options_content_form;
		$content_form = str_replace('!!date_last_remplissage!!', htmlentities($this->aff_date_last_remplissage,ENT_QUOTES, $charset), $content_form);

		$date_clic   = "onClick=\"openPopUp('./select.php?what=calendrier&caller=saisie_bannette&date_caller=".substr(preg_replace('/-/', '', $this->date_last_envoi),0,8)."&param1=form_date_last_envoi&param2=form_aff_date_last_envoi&auto_submit=NO&date_anterieure=YES', 'calendar')\"  ";
		$date_last_envoi = "
					<input type='hidden' name='form_date_last_envoi' value='".str_replace(' ', '', str_replace('-', '', str_replace(':', '', $this->date_last_envoi)))."' />
					<input class='bouton' type='button' name='form_aff_date_last_envoi' value='".$this->aff_date_last_envoi."' ".$date_clic." />";

		$content_form = str_replace('!!date_last_envoi!!', $date_last_envoi, $content_form);

		$content_form = str_replace('!!archive!!', $this->get_archive_content_form($type), $content_form);
		$content_form = str_replace('!!diffusions_history!!', $this->get_diffusions_history_content_form($type), $content_form);
		if ($type=="pro") {
			$content_form = str_replace('!!proprio_bannette!!', htmlentities($msg['dsi_ban_no_proprio'],ENT_QUOTES, $charset), $content_form);
		} else {
			$content_form = str_replace('!!proprio_bannette!!', htmlentities($nom_prenom_abo,ENT_QUOTES, $charset), $content_form);
		}

		$content_form = str_replace('!!bannette_auto!!', ($this->bannette_auto ? "checked='checked'" : ""), $content_form);
		$content_form = str_replace('!!periodicite!!', htmlentities($this->periodicite,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!diffusion_email!!', ($this->diffusion_email ? "checked='checked'" : ""), $content_form);
		$content_form = str_replace('!!nb_notices_diff!!', htmlentities($this->nb_notices_diff,ENT_QUOTES, $charset), $content_form);
		$content_form = str_replace('!!bannette_aff_notice_number!!', ($this->bannette_aff_notice_number ? " checked='checked' " : ""), $content_form);

		// update_type: se baser sur la date de création ou la date de mise à jour des notices ?
		$update_type = "<select name='update_type' id='update_type'>
						<option value='C' ".((!$this->update_type || $this->update_type == 'C') ? "selected='selected'" : "").">".$msg['dsi_ban_update_type_c']."</option>
						<option value='U' ".($this->update_type == 'U' ? "selected='selected'" : "").">".$msg['dsi_ban_update_type_u']."</option>
						</select>";
		$content_form = str_replace('!!update_type!!', $update_type, $content_form);

		return $content_form;
	}

	protected function get_access_rights_content_form($type="pro") {
		global $msg;
		global $dsi_bannette_access_rights_content_form, $dsi_bannette_access_rights_content_form_abo;

		if ($type=="pro") {
			$content_form = $dsi_bannette_access_rights_content_form;
			$requete = 'SELECT id_categ_empr, libelle, IF(empr_categ_num_categ IS NULL, 0,1) as inscription FROM empr_categ
						left join bannette_empr_categs on (id_categ_empr=empr_categ_num_categ and empr_categ_num_bannette=' . $this->id_bannette . ' )
						ORDER BY libelle';
			$categ_lect_aff = gen_liste_multiple ($requete, "id_categ_empr", "libelle", "inscription", "categorie_lecteurs[]", '', 1, 0, $msg['dsi_ban_aucune_categ'], -1,$msg['dsi_all_empr_categ'], 5) ;
			$content_form = str_replace('!!categorie_lecteurs!!', $categ_lect_aff, $content_form);

			$requete = 'SELECT id_groupe, libelle_groupe, IF(empr_groupe_num_groupe IS NULL, 0,1) as inscription FROM groupe
						left join bannette_empr_groupes on (id_groupe=empr_groupe_num_groupe and empr_groupe_num_bannette=' . $this->id_bannette . ' )
						ORDER BY libelle_groupe';
			$groupe_lect_aff = gen_liste_multiple ($requete, "id_groupe", "libelle_groupe", "inscription", "groupe_lecteurs[]", '', 1, 0, $msg['empr_form_aucungroupe'], 0,'', 5) ;
			$content_form = str_replace('!!groupe_lecteurs!!', $groupe_lect_aff, $content_form);
			$content_form = str_replace('!!bannette_opac_accueil_check!!', ($this->bannette_opac_accueil ? "checked='checked'" : ""), $content_form);
		} else {
			$content_form = $dsi_bannette_access_rights_content_form_abo;
			$content_form = str_replace('!!categorie_lecteurs!!', "<input type=hidden name=categorie_lecteurs[] value=0 />", $content_form);
			$content_form = str_replace('!!groupe_lecteurs!!', "<input type=hidden name=groupe_lecteurs[] value=0 />", $content_form);
		}

		return $content_form;
	}

	protected function get_export_content_form($type="pro") {
		global $msg;
		global $dsi_bannette_export_content_form;

		$content_form = $dsi_bannette_export_content_form;
		$exp = start_export::get_exports();
		$liste_exports = "<select id='typeexport' name='typeexport' onchange=\"if(this.selectedIndex==0) document.getElementById('liste_parametre').style.display='none'; else document.getElementById('liste_parametre').style.display=''; \">" ;
		if (!$this->typeexport) $liste_exports .= "<option value='' selected>".$msg['dsi_ban_noexport']."</option>";
		else $liste_exports .= "<option value=''>".$msg['dsi_ban_noexport']."</option>";
		for ($i=0;$i<count($exp);$i++) {
			if ($this->typeexport==$exp[$i]["PATH"]) $liste_exports .= "<option value='".$exp[$i]["PATH"]."' selected>".$exp[$i]["NAME"]."</option>";
			else $liste_exports .= "<option value='".$exp[$i]["PATH"]."' >".$exp[$i]["NAME"]."</option>";
		}
		$liste_exports .= "</select>" ;
		$content_form = str_replace('!!typeexport!!', $liste_exports,  $content_form);
		$content_form = str_replace('!!prefixe_fichier!!', $this->prefixe_fichier,  $content_form);

		if($this->param_export) {
			$param=new export_param(EXP_DSI_CONTEXT, $this->param_export);
		} else {
			$param=new export_param(EXP_DEFAULT_GESTION);
		}
		$content_form = str_replace('!!display_liste_param!!', (!$this->typeexport ? 'display:none' : ''),  $content_form);
		$content_form = str_replace('!!form_param!!', $param->check_default_param(),  $content_form);

		return $content_form;
	}

	protected function get_document_content_form($type="pro") {
		global $dsi_bannette_document_content_form;

		$content_form = $dsi_bannette_document_content_form;
		$content_form = str_replace('!!document_generate!!', ($this->document_generate ? "checked='checked'" : ""), $content_form);
// 		$content_form = str_replace('!!document_notice_display_type_0!!', (!$this->document_notice_display_type ? "checked='checked'" : ""), $content_form);
		$content_form = str_replace('!!document_notice_tpl!!', notice_tpl_gen::gen_tpl_select("document_notice_tpl",$this->document_notice_tpl), $content_form);
// 		$content_form = str_replace('!!document_notice_display_type_1!!', ($this->document_notice_display_type ? "checked='checked'" : ""), $content_form);
// 		$content_form = str_replace('!!document_django_directory!!', record_display::get_directories_options($this->document_django_directory), $content_form);
		$content_form = str_replace('!!document_insert_docnum!!', ($this->document_insert_docnum ? "checked='checked'" : ""), $content_form);
		$content_form = str_replace('!!document_group!!', ($this->document_group ? "checked='checked'" : ""), $content_form);
		$content_form = str_replace('!!document_add_summary!!', ($this->document_add_summary ? "checked='checked'" : ""), $content_form);
		return $content_form;
	}

	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form($type="pro") {
		global $msg, $base_path, $current_module;
		global $dsi_bannette_content_form;
		global $dsi_bannette_content_form_abo;
		global $dsi_bannette_notices_template;
		global $form_cb, $id_classement, $id_empr;
		global $page, $nbr_lignes, $nb_per_page;

		if ($type=="abo") {
			$content_form = $dsi_bannette_content_form_abo ;
		} else {
			$content_form = $dsi_bannette_content_form;
		}
		$content_form = str_replace('!!id_bannette!!', $this->id_bannette, $content_form);

		$interface_form = new interface_dsi_form('saisie_bannette');
		if(!$this->id_bannette){
			$interface_form->set_label($msg['dsi_ban_form_creat']);
		}else{
			$interface_form->set_label($msg['dsi_ban_form_modif']);
		}
		if($this->id_bannette) {
			$link_pagination = "";
			if($page > 1) {
				$link_pagination .= "&page=".$page."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page;
			}
			$interface_form->set_url_base($base_path.'/'.$current_module.'.php?categ=bannettes&sub='.$type.'&id_classement='.$id_classement.'&form_cb='.$form_cb.$link_pagination);
			$link_annul = "<input type='button' class='bouton' value='$msg[76]' onClick=\"document.location='".static::format_url("&categ=bannettes&sub=$type&id_bannette=&suite=search&id_classement=$id_classement&form_cb=$form_cb$link_pagination")."';\" />";
		} else {
			$interface_form->set_url_base($base_path.'/'.$current_module.'.php?categ=bannettes&sub='.$type);
			$link_annul = "<input type='button' class='bouton' value='$msg[76]' onClick=\"history.go(-1);\" />";
			if ($this->id_bannette_origine) { //On vient d'une duplication
				$origine_bannette = new bannette($this->id_bannette_origine) ;
				$this->notice_tpl=$origine_bannette->notice_tpl;
			} else {
				$this->notice_tpl=$dsi_bannette_notices_template;
			}
		}
		$content_form = str_replace('!!type!!', $type, $content_form);
		$content_form = str_replace('!!id_empr!!', $id_empr, $content_form);

		$content_form = str_replace('!!bannette_common_content_form!!', $this->get_common_content_form($type), $content_form);
		$content_form = str_replace('!!bannette_entity_content_form!!', $this->get_entity_content_form($type), $content_form);
		$content_form = str_replace('!!bannette_options_content_form!!', $this->get_options_content_form($type), $content_form);
		$content_form = str_replace('!!bannette_access_rights_content_form!!', $this->get_access_rights_content_form($type), $content_form);
		$content_form = str_replace('!!bannette_export_content_form!!', $this->get_export_content_form($type), $content_form);
		if($type=="pro") {
			$content_form = str_replace('!!bannette_document_content_form!!', $this->get_document_content_form($type), $content_form);
		}

		if ($this->statut_not_account) $content_form = str_replace('!!statut_not_account!!', "checked", $content_form);
		else $content_form = str_replace('!!statut_not_account!!', "", $content_form);
		if ($this->associated_campaign) $content_form = str_replace('!!associated_campaign!!', "checked", $content_form);
		else $content_form = str_replace('!!associated_campaign!!', "", $content_form);

		// choix de l'expéditeur
		$content_form = str_replace('!!senders!!', $this->get_senders_selector(), $content_form);

		$content_form = str_replace('!!desc_fields!!', $this->bannette_descriptors->get_form(), $content_form);

		$content_form = str_replace('!!num_panier!!', caddie::get_cart_list_selector('NOTI', 'num_panier', $this->num_panier), $content_form);

		$limite_type = "<select name='limite_type' id='limite_type'>
						<option value='' ".(!$this->limite_type ? "selected='selected'" : "").">".$msg['dsi_ban_non_cumul']."</option>
						<option value='D' ".($this->limite_type == 'D' ? "selected='selected'" : "").">".$msg['dsi_ban_cumul_jours']."</option>
						<option value='I' ".($this->limite_type == 'I' ? "selected='selected'" : "").">".$msg['dsi_ban_cumul_notice']."</option>
						</select>";
		$content_form = str_replace('!!limite_type!!', $limite_type, $content_form);
		$content_form = str_replace('!!limite_nombre!!', $this->limite_nombre, $content_form);

		$interface_form->set_object_id($this->id_bannette)
		->set_bannette_type($type)
		->set_id_empr($id_empr)
		->set_confirm_delete_msg($msg['confirm_suppr'])
		->set_content_form($content_form)
		->set_table_name('bannettes')
		->set_field_focus('nom_bannette');
		if($type=="pro") {
			$interface_form->set_duplicable(true);
		}
		print $interface_form->get_display();
	}

	public function set_properties_from_form() {
		global $num_classement, $nom_bannette, $comment_gestion, $comment_public;
		global $entete_mail, $piedpage_mail, $notice_display_type, $notice_tpl, $django_directory;
		global $id_empr;
		global $bannette_auto, $periodicite, $diffusion_email, $statut_not_account, $associated_campaign, $num_sender, $nb_notices_diff;
		global $categorie_lecteurs, $groupe_lecteurs;
		global $update_type, $form_date_last_envoi, $num_panier;
		global $limite_type, $limite_nombre, $typeexport, $prefixe_fichier;
		global $group_pperso, $display_notice_in_every_group, $archive_number, $diffusions_history, $group_type;
		global $document_generate, $document_notice_display_type, $document_notice_tpl, $document_django_directory, $document_insert_docnum, $document_group, $document_add_summary;
		global $bannette_opac_accueil,$bannette_tpl_num, $bannette_aff_notice_number;
		global $genere_lien, $mere, $fille, $notice_mere, $notice_fille, $art_link, $bull_link, $perio_link, $bulletinage, $notice_art, $notice_perio;

		$this->num_classement 		= intval($num_classement);
		$this->nom_bannette 		= stripslashes($nom_bannette);
		$this->comment_gestion 		= stripslashes($comment_gestion);
		$this->comment_public		= stripslashes($comment_public);
		$this->entete_mail 			= stripslashes($entete_mail);
		$this->piedpage_mail 		= stripslashes($piedpage_mail);
		$this->notice_display_type	= intval($notice_display_type);
		$this->notice_tpl 			= intval($notice_tpl);
		$this->django_directory     = stripslashes($django_directory);
		$this->proprio_bannette		= intval($id_empr);
		$this->bannette_auto		= intval($bannette_auto);
		$this->periodicite			= intval($periodicite);
		$this->diffusion_email		= intval($diffusion_email);
		$this->statut_not_account 	= intval($statut_not_account);
		$this->associated_campaign 	= intval($associated_campaign);
		$this->num_sender 			= intval($num_sender);
		$this->nb_notices_diff		= intval($nb_notices_diff);
		$this->categorie_lecteurs=   $categorie_lecteurs;
		$this->groupe_lecteurs=		$groupe_lecteurs;
		$this->update_type			= stripslashes($update_type);
		$this->date_last_envoi		= $form_date_last_envoi;
		$this->num_panier			= intval($num_panier);
		$this->limite_type 			= stripslashes($limite_type);
		$this->limite_nombre		= intval($limite_nombre);
		$this->typeexport 			= stripslashes($typeexport);
		$this->prefixe_fichier 		= stripslashes($prefixe_fichier);
		$this->group_pperso 		= stripslashes($group_pperso);
		$this->display_notice_in_every_group = intval($display_notice_in_every_group);
		$this->archive_number		= intval($archive_number);
		$this->diffusions_history	= intval($diffusions_history);
		$this->group_type 			= intval($group_type);
		$this->document_generate 	= intval($document_generate);
		$this->document_notice_display_type	= intval($document_notice_display_type);
		$this->document_notice_tpl 	= intval($document_notice_tpl);
		$this->document_django_directory = stripslashes($document_django_directory);
		$this->document_insert_docnum = intval($document_insert_docnum);
		$this->document_group 		= intval($document_group);
		$this->document_add_summary = intval($document_add_summary);
		$this->bannette_opac_accueil= intval($bannette_opac_accueil);
		$this->bannette_tpl_num 	= intval($bannette_tpl_num);
		$this->bannette_aff_notice_number =	intval($bannette_aff_notice_number);
		$this->param_export=array("genere_lien" => intval($genere_lien),
				"mere" => intval($mere),
				"fille" => intval($fille),
				"notice_mere" => intval($notice_mere),
				"notice_fille" => intval($notice_fille),
				"art_link" => intval($art_link),
				"bull_link" => intval($bull_link),
				"perio_link" => intval($perio_link),
				"bulletinage" => intval($bulletinage),
				"notice_art" => intval($notice_art),
				"notice_perio" => intval($notice_perio)
		);
	}

	// ---------------------------------------------------------------
	//		save
	// ---------------------------------------------------------------
	public function save() {
//		global $pmb_type_audit;

		if ($this->id_bannette) {
			// update
			$req = "UPDATE bannettes set ";
			$clause = " WHERE id_bannette='".$this->id_bannette."'";
		} else {
			$req = "insert into bannettes set date_last_remplissage=sysdate(), ";
			$clause = "";
		}
		$req.="num_classement='".$this->num_classement."',";
		$req.="nom_bannette='".addslashes($this->nom_bannette)."',";
		$req.="comment_gestion='".addslashes($this->comment_gestion)."',";
		$req.="comment_public='".addslashes($this->comment_public)."',";
		$req.="bannette_tpl_num='".$this->bannette_tpl_num."',";
		$req.="entete_mail='".addslashes($this->entete_mail)."',";
		$req.="piedpage_mail='".addslashes($this->piedpage_mail)."',";
		$req.="notice_display_type='".$this->notice_display_type."',";
		$req.="notice_tpl='".$this->notice_tpl."',";
		$req.="django_directory='".addslashes($this->django_directory)."',";
		$req.="proprio_bannette='".$this->proprio_bannette."',";
		$req.="bannette_auto='".$this->bannette_auto."',";
		$req.="periodicite='".$this->periodicite."',";
		$req.="diffusion_email='".$this->diffusion_email."',";
		$req.="statut_not_account='".$this->statut_not_account."',";
		$req.="associated_campaign='".$this->associated_campaign."',";
		$req.="bannette_num_sender='".$this->num_sender."',";
		$req.="nb_notices_diff='".$this->nb_notices_diff."',";
		$req.="update_type='".$this->update_type."',";
		$req.="num_panier='".$this->num_panier."',";
		$req.="limite_type='".$this->limite_type."',";
		$req.="limite_nombre='".$this->limite_nombre."',";
		$req.="typeexport='".$this->typeexport."',";
		$req.="prefixe_fichier='".addslashes($this->prefixe_fichier)."',";
		$req.="group_type='".$this->group_type."',";
		$req.="group_pperso='".$this->group_pperso."',";
		$req.="display_notice_in_every_group='".$this->display_notice_in_every_group."',";
		$req.="archive_number='".$this->archive_number."',";
		$req.="bannette_diffusions_history='".$this->diffusions_history."',";
		$req.="param_export='".addslashes(serialize($this->param_export))."',";
		$req.="document_generate='".$this->document_generate."',";
		$req.="document_notice_display_type='".$this->document_notice_display_type."',";
		$req.="document_notice_tpl='".$this->document_notice_tpl."',";
		$req.="document_django_directory='".addslashes($this->document_django_directory)."',";
		$req.="document_insert_docnum='".$this->document_insert_docnum."',";
		$req.="document_group='".$this->document_group."',";
		$req.="document_add_summary='".$this->document_add_summary."',";
		$req.="bannette_opac_accueil='".$this->bannette_opac_accueil."',";
		$req.="bannette_aff_notice_number='".$this->bannette_aff_notice_number."',";
		if (!$this->date_last_envoi) $req.="date_last_envoi=sysdate() ";
			else $req.="date_last_envoi='".construitdateheuremysql($this->date_last_envoi)."' ";
		$req.=$clause ;
		$res = pmb_mysql_query($req);
		if (!$this->id_bannette) {
			$this->id_bannette = pmb_mysql_insert_id();
//			if($pmb_type_audit) audit::insert_creation(AUDIT_BANNETTE,$this->id_bannette);
		} else {
//			if($pmb_type_audit) audit::insert_modif(AUDIT_BANNETTE,$this->id_bannette);
		}

		$del = "delete from bannette_empr_groupes where empr_groupe_num_bannette = '".$this->id_bannette."'";
		pmb_mysql_query($del);

		if(!empty($this->groupe_lecteurs) && is_array($this->groupe_lecteurs)) {
    		for($i=0 ; $i<count($this->groupe_lecteurs) ; $i++){
    			$id_groupe = intval($this->groupe_lecteurs[$i]);
    			$rqt = "insert into bannette_empr_groupes set empr_groupe_num_bannette = '".$this->id_bannette."', empr_groupe_num_groupe = '".$id_groupe."' ";
    			pmb_mysql_query($rqt);
    		}
		}

		$del = "delete from bannette_empr_categs where empr_categ_num_bannette = '".$this->id_bannette."'";
		pmb_mysql_query($del);
		if(!empty($this->categorie_lecteurs) && is_array($this->categorie_lecteurs)) {
			$this->categorie_lecteurs=$this->categorie_lecteurs;
			if($this->categorie_lecteurs[0]==-1){
				$this->categorie_lecteurs=array();
				$rqt = "SELECT id_categ_empr FROM empr_categ";
				$res=pmb_mysql_query($rqt);
				if(pmb_mysql_num_rows($res)){
					while($row = pmb_mysql_fetch_object($res)){
						$this->categorie_lecteurs[] = $row->id_categ_empr;
					}
				}
			}
			for($i=0 ; $i<count($this->categorie_lecteurs) ; $i++){
				$id_categ = intval($this->categorie_lecteurs[$i]);
				$rqt = "insert into bannette_empr_categs set empr_categ_num_bannette = '".$this->id_bannette."', empr_categ_num_categ = '".$id_categ."' ";
				pmb_mysql_query($rqt);
			}
		}else{
			$this->categorie_lecteurs=array();
		}

		$this->bannette_descriptors->set_properties_from_form();
		$this->bannette_descriptors->save();

		$facette = new bannette_facettes($this->id_bannette);
		$facette->save();
	}

	// ---------------------------------------------------------------
	//		delete() : suppression
	// ---------------------------------------------------------------
	public function delete() {
		$requete = "delete from bannette_abon WHERE num_bannette='$this->id_bannette'";
		pmb_mysql_query($requete);
		$requete = "delete from bannette_contenu WHERE num_bannette='$this->id_bannette'";
		pmb_mysql_query($requete);
		$requete = "DELETE FROM bannettes_diffusions WHERE diffusion_num_bannette = ".$this->id_bannette;
		pmb_mysql_query($requete);
		$requete = "delete from bannettes WHERE id_bannette='$this->id_bannette'";
		pmb_mysql_query($requete);

		bannette_descriptors::delete($this->id_bannette);
		bannette_equations::delete($this->id_bannette);
		$facette = new bannette_facettes($this->id_bannette);
		$facette->delete();

		$del = "delete from bannette_empr_groupes where empr_groupe_num_bannette = '".$this->id_bannette."'";
		pmb_mysql_query($del);
		$del = "delete from bannette_empr_categs where empr_categ_num_bannette = '".$this->id_bannette."'";
		pmb_mysql_query($del);

//		audit::delete_audit(AUDIT_BANNETTE, $this->id_bannette);
	}

	protected function get_records_over_days() {
		$query = "select num_notice from bannette_contenu WHERE num_bannette='$this->id_bannette' and ";
		$query .= " date_add(date_ajout, INTERVAL ".$this->limite_nombre." DAY) >= sysdate() ";
		$result = pmb_mysql_query($query);
		$tab = array();
		while ($row=pmb_mysql_fetch_object($result)) {
			$tab[] = $row->num_notice ;
		}
		return $tab;
	}

	protected function get_records_limit() {
		$tab = array();
		// selection des ## dernières notices, celles qu'il faut absolument garder
		$requete = "select num_notice from bannette_contenu, notices WHERE num_bannette='".$this->id_bannette."' and notice_id=num_notice order by date_ajout DESC, ".$this->get_date_used()." DESC ";
		$requete .= " limit $this->limite_nombre ";
		$res = pmb_mysql_query($requete);
		while ($obj=pmb_mysql_fetch_object($res)) {
			$tab[]=$obj->num_notice ;
		}

		// selection des notices ajoutées depuis moins d'un jour
		$requete = "select num_notice from bannette_contenu WHERE num_bannette='".$this->id_bannette."' and ";
		$requete .= " date_add(date_ajout, INTERVAL 1 DAY)>=sysdate() ";
		$res = pmb_mysql_query($requete);
		while ($obj=pmb_mysql_fetch_object($res)) {
			$tab[]=$obj->num_notice ;
		}
		return $tab;
	}

	// ---------------------------------------------------------------
	//		purger() : après remplissage, vider ce qui dépasse selon le type de cumul de la bannette
	// ---------------------------------------------------------------
	public function purger() {
//		global $msg;
		global $gestion_acces_active,$gestion_acces_empr_notice;
//		global $pmb_type_audit;

		//purge pour les bannettes privees des notices ne devant pas etre diffusees
		if ($this->proprio_bannette && $gestion_acces_active==1 && $gestion_acces_empr_notice==1){
			$ac = new acces();
			$dom_2 = $ac->setDomain(2);
			$acces_j = $dom_2->getJoin($this->proprio_bannette,'4=0','num_notice');

			if($this->diffusions_history) {
				$query = "select num_notice from bannette_contenu $acces_j WHERE num_bannette='$this->id_bannette'";
				$this->get_bannette_diffusion()->add_deleted_records($this->get_elements_from_query($query), 'access_rights');
			}

			$query="delete from bannette_contenu using bannette_contenu $acces_j WHERE num_bannette='$this->id_bannette' ";
			pmb_mysql_query($query);
		}

		//purge des notices ne répondant plus aux équations de recherche
		if(!empty($this->equations_notices)) {
			$notice_suppr=implode(",",$this->equations_notices);
			if ($this->num_panier) {
				$query = "delete from caddie_content WHERE caddie_id='$this->num_panier' and object_id not in (".$notice_suppr.") ";
				pmb_mysql_query($query);
			}
			if($this->diffusions_history) {
				$query = "select num_notice from bannette_contenu WHERE num_bannette='".$this->id_bannette."' and num_notice not in (".$notice_suppr.")";
				$this->get_bannette_diffusion()->add_deleted_records($this->get_elements_from_query($query), 'equations');
			}
			$query = "delete from bannette_contenu WHERE num_bannette='$this->id_bannette' and num_notice not in (".$notice_suppr.") ";
			pmb_mysql_query($query);
		}

		$records = array();
		switch ($this->limite_type) {
			case "D":
				$records = $this->get_records_over_days();
				break;
			case "I":
				$records = $this->get_records_limit();
				break;
		}
		if(count($records)) {
			$notice_suppr=implode(",",$records);
			if ($this->num_panier) {
				$query = "delete from caddie_content WHERE caddie_id='$this->num_panier' and object_id not in (".$notice_suppr.") ";
				pmb_mysql_query($query);
			}
			if($this->diffusions_history) {
				$query = "select num_notice from bannette_contenu WHERE num_bannette='".$this->id_bannette."' and num_notice not in (".$notice_suppr.")";
				$this->get_bannette_diffusion()->add_deleted_records($this->get_elements_from_query($query), 'cumulative_limit');
			}
			$query = "delete from bannette_contenu WHERE num_bannette='$this->id_bannette' and num_notice not in (".$notice_suppr.")";
			pmb_mysql_query($query);
		}
		$this->compte_elements() ;
//		if($pmb_type_audit) {
//			audit::insert_modif(AUDIT_BANNETTE,$this->id_bannette, $msg['dsi_ban_purger']);
//		}
	}

	// ---------------------------------------------------------------
	//		vider() : vider le contenu de la bannette
	// ---------------------------------------------------------------
	public function vider() {
//		global $pmb_type_audit, $msg;

		if($this->diffusions_history) {
			$query = "select num_notice from bannette_contenu WHERE num_bannette='".$this->id_bannette."'";
			$this->get_bannette_diffusion()->add_deleted_records($this->get_elements_from_query($query), 'empty');
		}

		$query = "delete from bannette_contenu WHERE num_bannette='".$this->id_bannette."'";
		pmb_mysql_query($query);
		$query = "delete from caddie_content WHERE caddie_id='".$this->num_panier."'";
		pmb_mysql_query($query);

		$this->compte_elements() ;

//		if($pmb_type_audit) {
//			audit::insert_modif(AUDIT_BANNETTE,$this->id_bannette, $msg['dsi_ban_vider']);
//		}
	}

	public function put_equations_notices($table) {
		$query = "SELECT * FROM $table ";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->equations_notices[] = $row->notice_id;
			}
			$this->equations_notices = array_unique($this->equations_notices);
		}
	}

	public function get_records_filtered_equation($query) {
	    $records_filtered_equation = [];
	    if($query) {
	        $result = pmb_mysql_query($query);
	        if(pmb_mysql_num_rows($result)) {
	            while($row = pmb_mysql_fetch_object($result)) {
	                $records_filtered_equation[] = $row->notice_id;
	            }
	        }
	    }
	    return $records_filtered_equation;
	}

	// ---------------------------------------------------------------
	//		remplir() : remplir la bannette à partir des équations
	// ---------------------------------------------------------------
	public function remplir() {
//		global $msg;
		global $pmb_allow_term_troncat_search, $opac_allow_term_troncat_search, $pmb_search_exclude_fields, $opac_exclude_fields;
		global $dsi_private_bannette_search_equation;
//		global $pmb_type_audit;
		global $search;

		//On doit prendre en compte les paramètres OPAC si bannette privée
		$initial_pmb_allow_term_troncat_search = $pmb_allow_term_troncat_search;
		$initial_pmb_search_exclude_fields = $pmb_search_exclude_fields;
		if ($this->proprio_bannette!=0) {
			$pmb_allow_term_troncat_search = $opac_allow_term_troncat_search;
			$pmb_search_exclude_fields = $opac_exclude_fields;
		}

		// récupérer les équations associées à la bannette
		$equations = $this->bannette_equations->get_equations() ;
		$res_affichage = "<ul>" ;

		$colonne_update_create = $this->get_date_used();

		$nb_equations = count($equations);
		for ($i = 0; $i < $nb_equations; $i++) {
		    $records_filtered_equation = array();
			// pour chaque équation ajouter les notices trouvées au contenu de la bannette
			$equ = new equation ($equations[$i]) ;

			$search_class = new search() ;

			$search = array();
			$this->add_extended_search(0, $equ->requete);

			if (!empty($dsi_private_bannette_search_equation) && $this->proprio_bannette) {
			    $default_equation = new equation($dsi_private_bannette_search_equation);
			    if ($default_equation->requete) {
        			$this->add_extended_search(1, $default_equation->requete);
			    }
			}
			$table = $search_class->make_search();

			if($search_class->is_created_temporary_table($table)) {
				$this->put_equations_notices($table);
				if($this->statut_not_account) {
					$temp_requete = "select ".$this->id_bannette." , notices.notice_id from $table , notices where notices.$colonne_update_create>='".$this->date_last_envoi."' and $table.notice_id=notices.notice_id" ;
				} else {
					$temp_requete = "select ".$this->id_bannette." , notices.notice_id from $table , notices, notice_statut where notices.$colonne_update_create>='".$this->date_last_envoi."' and $table.notice_id=notices.notice_id and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0) or (notice_visible_opac_abon=1 and notice_visible_opac=1))" ;
				}
				if($this->diffusions_history) {
				    $records_filtered_equation = $this->get_records_filtered_equation($temp_requete);
				}
				pmb_mysql_query("insert ignore into bannette_contenu (num_bannette, num_notice) (".$temp_requete.")");

				$temp_requete = "drop table $table " ;
				pmb_mysql_query($temp_requete);
			}
			$res_affichage .= "<li>".$equ->human_query."</li>" ;
			if($this->diffusions_history) {
				// $search_class->make_human_query() => permet d'obtenir la human_query reellement exécutée
				// $equ->human_query => résultat de ce qui est censé s'exécuter
			    $this->get_bannette_diffusion()->add_equation(array('name' => $equ->nom_equation, 'human_query' => $search_class->make_human_query(), 'records' => $records_filtered_equation));
			}
		}

		// remplissage du panier avec le contenu de la bannette
		if ($this->num_panier) {
			$temp_requete = "insert ignore into caddie_content (caddie_id, object_id) (select ".$this->num_panier.", num_notice from bannette_contenu where num_bannette=".$this->id_bannette.")" ;
			pmb_mysql_query($temp_requete);
		}

		//Si nécessaire, on remet le paramètre d'origine
		if ($this->proprio_bannette!=0) {
			$pmb_allow_term_troncat_search = $initial_pmb_allow_term_troncat_search;
			$pmb_search_exclude_fields = $initial_pmb_search_exclude_fields;
		}

		$res_affichage .= "</ul>" ;
		$this->compte_elements() ;
		$temp_requete = "update bannettes set date_last_remplissage=sysdate() where id_bannette='".$this->id_bannette."' " ;
		pmb_mysql_query($temp_requete);
//		if($pmb_type_audit) {
//			audit::insert_modif(AUDIT_BANNETTE,$this->id_bannette, $msg['dsi_ban_remplir']);
//		}
		$this->purger();
		$this->detroy_env();

		return $res_affichage ;
	}

	protected function get_date_used() {
    	if ($this->update_type == 'C') {
			return "create_date";
		}
		return "update_date";
	}

	protected function add_extended_search($ind, $extended_search) {
	    global $search;
	    $search[$ind] = 's_11';

	    global ${'inter_'.$ind.'_s_11'};
	    global ${'op_'.$ind.'_s_11'};
	    global ${'field_'.$ind.'_s_11'};

	    if ($ind) {
	        ${'inter_'.$ind.'_s_11'} = 'and';
	    }
	    ${'op_'.$ind.'_s_11'} = 'EQ';
	    ${'field_'.$ind.'_s_11'} = array($extended_search);
	}

	protected function get_css($directory) {
		global $base_path;
		global $opac_url_base;

		$css = '';
		$css_path= $base_path."/opac_css/styles/".$directory."/dsi";
		if (is_dir($css_path)) {
			if (($dh = opendir($css_path))) {
				while (($css_file = readdir($dh)) !== false) {
					if(filetype($css_path."/".$css_file) =='file') {
						if( substr($css_file, -4) == ".css" ) {
							$css.="<link rel='stylesheet' type='text/css' href='".$opac_url_base."styles/".$directory."/dsi/".$css_file."' title='lefttoright' />\n";
						}
					}
				}
				closedir($dh);
			}
		}
		return $css;
	}

	protected function get_css_style() {
		global $opac_default_style;

		// récupération des fichiers de style commun
		$css = $this->get_css('common');

		// récupération des fichiers de style personnalisé
		$css .= $this->get_css($opac_default_style);
		return $css;
	}

	protected function get_query_records() {
		global $dsi_bannette_notices_order;
		global $dsi_bannette_max_nb_notices_per_mail;

		$dsi_bannette_max_nb_notices_per_mail = intval($dsi_bannette_max_nb_notices_per_mail);

		if (!$dsi_bannette_notices_order) $dsi_bannette_notices_order="index_serie, tnvol, index_sew";
		$limitation = '';
		if ($this->use_limit) {
		    if ($this->nb_notices_diff && $this->nb_notices_diff <= $dsi_bannette_max_nb_notices_per_mail) {
		        $limitation = " LIMIT $this->nb_notices_diff ";
		    } else {
		        $limitation = " LIMIT ".$dsi_bannette_max_nb_notices_per_mail." ";
		    }
		}
		$query = "select num_notice from bannette_contenu, notices where num_bannette='".$this->id_bannette."' and notice_id=num_notice order by $dsi_bannette_notices_order $limitation ";
		return $query;
	}

	protected function get_data_structure() {
		$data = array();

		//Nb total sans limitation
		$data['records']['length_total'] = $this->nb_notices;

		$result = pmb_mysql_query($this->get_query_records());
		$data['records']['length'] = pmb_mysql_num_rows($result);
		if(pmb_mysql_num_rows($result)) {
		    $bannette_facette = $this->get_instance_bannette_facette();
		    if($this->get_notice_tpl()){
		        $bannette_facette->noti_tpl_document = notice_tpl_gen::get_instance($this->get_notice_tpl());
		    }
			while (($row = pmb_mysql_fetch_object($result))) {
				$data['records'][$row->num_notice]['render'] = $bannette_facette->build_notice($row->num_notice, $this->id_bannette);
			}
		}
		if($this->add_summary()) {
			$data['sommaires'] = $this->get_data_summary_structure();
		} else {
			$data['sommaires'] = array();
		}
		return $data;
	}

	public function get_display_bannette_tpl() {
		$data=$this->get_data_structure();	//$this->data_document
		$data['info']['header']=$this->get_display_header();
		$data['info']['footer']=$this->get_display_footer();
		$data['info']['opac_name']=$this->comment_public;
		$data['info']['id']=$this->id_bannette;
		$data['info']['name']=$this->nom_bannette;
		$data['info']['date_diff']=formatdate(today());
		$data['info']['equation']=$this->bannette_equations->get_text();
		$data['info']['nb_abonnes']=$this->nb_abonnes;
		$data['empr']['name']='!!empr_name!!';
		$data['empr']['first_name']='!!empr_first_name!!';
		$data['empr']['civ']='!!empr_sexe!!';
		$data['empr']['cb']='!!empr_cb!!';
		$data['empr']['login']='!!empr_login!!';
		$data['empr']['mail']='!!empr_mail!!';
		$data['empr']['name_and_adress']='!!empr_name_and_adress!!';
		$data['empr']['all_information']='!!empr_all_information!!';
		$data['empr']['connect']='!!empr_connect!!';
		$data['empr']['statut_id']='!!empr_statut_id!!';
		$data['empr']['statut_lib']='!!empr_statut_lib!!';
		$data['empr']['categ_id']='!!empr_categ_id!!';
		$data['empr']['categ_lib']='!!empr_categ_lib!!';
		$data['empr']['codestat_id']='!!empr_codestat_id!!';
		$data['empr']['codestat_lib']='!!empr_codestat_lib!!';
		$data['empr']['langopac_code']='!!empr_langopac_code!!';
		$data['empr']['langopac_lib']='!!empr_langopac_lib!!';
		$data['loc']['name']='!!loc_name!!';
		$data['loc']['adr1']='!!loc_adr1!!';
		$data['loc']['adr2']='!!loc_adr2!!';
		$data['loc']['cp']='!!loc_cp!!';
		$data['loc']['town']='!!loc_town!!';
		$data['loc']['phone']='!!loc_phone!!';
		$data['loc']['email']='!!loc_email!!';
		$data['loc']['website']='!!loc_website!!';

		if($this->bannette_tpl_num){
			$tpl_id = $this->bannette_tpl_num;
		} else{
			$tpl_id = $this->get_private_bannette_tpl();
		}
		return bannette_tpl::render($tpl_id,$data);
	}

	public function get_display_document() {
		global $charset;

		$this->set_records_globals();
		$this->set_output_format('document');
		$this->build_lists(0);
		$document = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" />".$this->get_css_style()."</head><body>";
		if(($this->bannette_tpl_num) || (($this->proprio_bannette)&&($this->get_private_bannette_tpl()))){
			$document .= $this->get_display_bannette_tpl();
		} else {
			$document .= $this->get_display_header();
			if (count($this->list) && $this->group_type==1) {
				$facette = $this->get_instance_bannette_facette();
				$document .= $facette->build_document($this->list,$this->document_notice_tpl,$this->document_add_summary,1);
			} else {
				$this->add_list_group_in_list();
				if($this->is_grouped()){
					$document .= $this->get_display_summary();
				} else {
					if($this->list) {
						$document .= $this->get_display_template();
					}
				}
			}
			$document .= $this->get_display_footer();
		}
		$document.= "</body></html>";
		return $document;
	}

	public function get_empr_mail($id_empr){
		$requete = "select empr_mail, bannette_mail from empr,  bannette_abon, bannettes ";
		$requete .= "where num_bannette='".$this->id_bannette."' and num_empr=$id_empr and num_bannette=id_bannette and num_empr=id_empr";
		$res = pmb_mysql_query($requete);
		$emaildest="";
		if($empr=pmb_mysql_fetch_object($res)) {
			$emaildest = $empr->empr_mail;
			if ($empr->bannette_mail && $emaildest){
				$destinataires = explode(";",$emaildest);
				foreach($destinataires as $mail){
					if($mail == $empr->bannette_mail){
						$emaildest=$empr->bannette_mail;
						break;
					}
				}
			}
		}
		return $emaildest;
	}

	protected function get_display_header() {
		$header = $this->construit_liens_HTML();
		return $header;
	}

	protected function add_list_group_in_list() {
		global $dsi_bannette_notices_order;

		if(count($this->list_group)) {
			foreach($this->list_group as $list_notice) {
				$req_list=array();
				foreach($list_notice as $r) {
					$req_list[]=$r->num_notice;
				}
				$query = "select notice_id as num_notice from  notices where  notice_id in(".implode(",",$req_list).") order by $dsi_bannette_notices_order ";
				$result = pmb_mysql_query($query) ;
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->list[] = $row->num_notice;
				}
			}
		}
	}
	protected function get_display_content() {
		$display = "";
		$this->build_lists(1);
		if (count($this->list) && $this->group_type==1) {
			$facette = $this->get_instance_bannette_facette();
			$display .= $facette->build_document($this->list,$this->notice_tpl,1,0);
		} else {
			$this->add_list_group_in_list();
			// il faut trier les regroupements par ordre alphabétique
			if($this->group_pperso) {
				$display .= $this->get_display_summary();
			} else {
				if ($this->list) {
					// DSI classique par mail...
					$display .= $this->get_display_template();
				}
			}
		}
		return $display;
	}

	protected function get_display_footer() {
		$footer = $this->piedpage_mail;
		$footer = str_replace('!!equation!!', $this->bannette_equations->get_text(), $footer);
		return $footer;
	}

	protected function get_display_mail() {
		global $charset;

		$this->set_records_globals();
		$this->set_output_format('mail');
		$this->build_lists(1);
		$display = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" />".$this->get_css_style()."</head><body>";
		if(($this->bannette_tpl_num) || (($this->proprio_bannette)&&($this->get_private_bannette_tpl()))){
			if($this->bannette_aff_notice_number){
				$display .= "<span class=\"dsi_hide_for_emails\"><hr />!!dsi_diff_n_notices!!</span>";
			}
			if ($this->diffusion_email) {
				$display .= $this->get_display_bannette_tpl();
			}
		} else {
			$display .= $this->get_display_header();
			if($this->bannette_aff_notice_number){
				$display .= "<span class=\"dsi_hide_for_emails\"><hr />!!dsi_diff_n_notices!!</span>";
			}
			if ($this->diffusion_email) {
				$display .= $this->get_display_content();
			}
			$display .= $this->get_display_footer();
		}
		$display .= "</body></html>";
		return $display;
	}

	protected function get_empr_informations($id_empr=0) {
		global $msg;

		$query = "SELECT id_empr, empr_cb, empr_mail, empr_nom, empr_prenom, empr_login, empr_password, if(empr_sexe=2,'".$msg["civilite_madame"]."', ";
		$query .= "if(empr_sexe=1,'".$msg["civilite_monsieur"]."','".$msg["civilite_unknown"]."')) as empr_sexe, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_lang, ";
		$query .= "empr_pays, empr_tel1, empr_tel2, date_format(empr_date_adhesion, '".$msg["format_date"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_empr_date_expiration,";
		$query .= "idstatut, statut_libelle, id_categ_empr, empr_categ.libelle as libelle_categ, idcode as id_codestat, empr_codestat.libelle as libelle_codestat, allow_dsi, allow_dsi_priv, empr_location ";
		$query .= "FROM empr ";
		$query .= "JOIN empr_statut ON empr_statut=idstatut ";
		$query .= "JOIN empr_categ ON empr_categ=id_categ_empr ";
		$query .= "JOIN empr_codestat ON empr_codestat=idcode ";
		$query .= "where id_empr=".$id_empr." ";
		$query .= "order by empr_nom, empr_prenom ";
		$result = pmb_mysql_query($query);
		$empr = pmb_mysql_fetch_object($result);
		return $empr;
	}

	protected function get_formatted_anonymous_text($text) {
		global $lang;

		$formatted_text = $text;

		$formatted_text = str_replace('!!empr_name!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_first_name!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_sexe!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_cb!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_login!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_mail!!', '',$formatted_text);
		$formatted_text=str_replace("!!empr_name_and_adress!!", '',$formatted_text);
		$formatted_text=str_replace("!!empr_all_information!!", '',$formatted_text);
		$formatted_text = str_replace('!!empr_statut_id!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_statut_lib!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_categ_id!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_categ_lib!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_codestat_id!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_codestat_lib!!', '',$formatted_text);
		$formatted_text = str_replace('!!empr_langopac_code!!', '',$formatted_text);
		$langues = marc_list_collection::get_instance('languages');
		$formatted_text = str_replace('!!empr_langopac_lib!!',$langues->table[$lang],$formatted_text);

		$formatted_text = str_replace('!!loc_name!!','',$formatted_text);
		$formatted_text = str_replace('!!loc_adr1!!','',$formatted_text);
		$formatted_text = str_replace('!!loc_adr2!!','',$formatted_text);
		$formatted_text = str_replace('!!loc_cp!!','',$formatted_text);
		$formatted_text = str_replace('!!loc_town!!','',$formatted_text);
		$formatted_text = str_replace('!!loc_phone!!','',$formatted_text);
		$formatted_text = str_replace('!!loc_email!!','',$formatted_text);
		$formatted_text = str_replace('!!loc_website!!','',$formatted_text);

		return $formatted_text;
	}

	protected function get_formatted_empr_text($text, $empr) {
		$formatted_text = $text;

		$formatted_text = str_replace('!!empr_name!!',$empr->empr_nom,$formatted_text);
		$formatted_text = str_replace('!!empr_first_name!!',$empr->empr_prenom,$formatted_text);
		$formatted_text = str_replace('!!empr_sexe!!',$empr->empr_sexe,$formatted_text);
		$formatted_text = str_replace('!!empr_cb!!',$empr->empr_cb,$formatted_text);
		$formatted_text = str_replace('!!empr_login!!',$empr->empr_login,$formatted_text);
		$formatted_text = str_replace('!!empr_mail!!',$empr->empr_mail,$formatted_text);
		if (strpos($formatted_text,"!!empr_name_and_adress!!") !== false) {
			$formatted_text=str_replace("!!empr_name_and_adress!!", nl2br($this->m_lecteur_adresse($empr)),$formatted_text);
		}
		if (strpos($formatted_text,"!!empr_all_information!!") !== false) {
			$formatted_text=str_replace("!!empr_all_information!!", nl2br($this->m_lecteur_info($empr)),$formatted_text);
		}
		$formatted_text = str_replace('!!empr_statut_id!!',$empr->idstatut,$formatted_text);
		$formatted_text = str_replace('!!empr_statut_lib!!',$empr->statut_libelle,$formatted_text);
		$formatted_text = str_replace('!!empr_categ_id!!',$empr->id_categ_empr,$formatted_text);
		$formatted_text = str_replace('!!empr_categ_lib!!',$empr->libelle_categ,$formatted_text);
		$formatted_text = str_replace('!!empr_codestat_id!!',$empr->id_codestat,$formatted_text);
		$formatted_text = str_replace('!!empr_codestat_lib!!',$empr->libelle_codestat,$formatted_text);
		$formatted_text = str_replace('!!empr_langopac_code!!',$empr->empr_lang,$formatted_text);
		$langues = marc_list_collection::get_instance('languages');
		$formatted_text = str_replace('!!empr_langopac_lib!!',$langues->table[$empr->empr_lang],$formatted_text);

		if ($empr->empr_location) {
			$empr_dest_loc = pmb_mysql_query("SELECT * FROM docs_location WHERE idlocation=".$empr->empr_location);
			$empr_loc = pmb_mysql_fetch_object($empr_dest_loc);
			$formatted_text = str_replace('!!loc_name!!',$empr_loc->name,$formatted_text);
			$formatted_text = str_replace('!!loc_adr1!!',$empr_loc->adr1,$formatted_text);
			$formatted_text = str_replace('!!loc_adr2!!',$empr_loc->adr2,$formatted_text);
			$formatted_text = str_replace('!!loc_cp!!',$empr_loc->cp,$formatted_text);
			$formatted_text = str_replace('!!loc_town!!',$empr_loc->town,$formatted_text);
			$formatted_text = str_replace('!!loc_phone!!',$empr_loc->phone,$formatted_text);
			$formatted_text = str_replace('!!loc_email!!',$empr_loc->email,$formatted_text);
			$formatted_text = str_replace('!!loc_website!!',$empr_loc->website,$formatted_text);
		}
		return $formatted_text;
	}

	protected function get_formatted_text($text, $empr=null) {
		global $lang;
		global $opac_url_base;
		global $opac_connexion_phrase;
		global $dsi_bannette_max_nb_notices_per_mail;

		$dsi_bannette_max_nb_notices_per_mail = intval($dsi_bannette_max_nb_notices_per_mail);

		$dates = time();
		$formatted_text = $text;
		$formatted_text = str_replace("!!nb_notice!!", $this->nb_notices, $formatted_text);
		$formatted_text = str_replace("!!public!!",$this->comment_public,$formatted_text);
		$formatted_text = str_replace("!!equation!!",$this->bannette_equations->get_text(),$formatted_text);
		if(!empty($empr) && is_object($empr)) {
			$lang_messages = static::get_lang_messages($empr->empr_lang);
			$code = md5($opac_connexion_phrase.$empr->empr_login.$dates);
			$login = $empr->empr_login;
			$formatted_text = $this->get_formatted_empr_text($formatted_text, $empr);
		} else {
			$lang_messages = static::get_lang_messages($lang);
			$code = '';
			$login = '';
			$formatted_text = $this->get_formatted_anonymous_text($formatted_text);
		}
		$formatted_text = str_replace("!!print_n_notices!!", sprintf($lang_messages["print_n_notices"],$this->nb_notices), $formatted_text);

		if ((!$this->nb_notices_diff || $this->nb_notices_diff > $dsi_bannette_max_nb_notices_per_mail)) {
		    $nb_envoyees = ($this->nb_notices <= $dsi_bannette_max_nb_notices_per_mail ? $this->nb_notices : $dsi_bannette_max_nb_notices_per_mail);
		} elseif (($this->nb_notices_diff >= $this->nb_notices) || (!$this->nb_notices_diff)) {
			$nb_envoyees = $this->nb_notices;
		} else {
			$nb_envoyees = $this->nb_notices_diff;
		}
		$msg_dsi_diff_n_notices = sprintf($lang_messages["dsi_diff_n_notices"],$nb_envoyees,$this->nb_notices);
		$formatted_text = str_replace("!!dsi_diff_n_notices!!", ($msg_dsi_diff_n_notices ? $msg_dsi_diff_n_notices.'<hr />' : ''), $formatted_text);

		$formatted_text = str_replace('!!empr_connect!!',"<a href='".$opac_url_base."empr.php".static::get_url_connexion_auto('?')."'>".$lang_messages["selvars_empr_auth_opac"]."</a>",$formatted_text);
		$formatted_text = str_replace('!!code!!',$code,$formatted_text);
		$formatted_text = str_replace('!!login!!',$login,$formatted_text);
		$formatted_text = str_replace('!!date_conex!!',$dates,$formatted_text);
		$formatted_text = str_replace ("!!date!!",formatdate(today()),$formatted_text) ;
		return $formatted_text;
	}

	protected function get_formatted_mail($empr=null) {
		$text = $this->get_display_mail();
		$formatted_mail = $this->get_formatted_text($text, $empr);
		return $formatted_mail;
	}

	public function get_display_export() {
		global $charset;

		$this->set_records_globals();
		$this->set_output_format('export');
		$this->build_lists(0);
		$display = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" />".$this->get_css_style()."</head><body>";
		if(($this->bannette_tpl_num) || (($this->proprio_bannette)&&($this->get_private_bannette_tpl()))){
			$display .= $this->get_display_bannette_tpl();
		} else {
			$display .= $this->get_display_header().$this->get_display_content().$this->get_display_footer();
		}
		$display .= "</body></html>";
		return $display;
	}

	// ---------------------------------------------------------------
	//		diffuser() : diffuser le contenu de la bannette
	// ---------------------------------------------------------------

	public function diffuser() {
		global $msg, $charset, $base_path, $opac_connexion_phrase, $pmb_mail_delay, $opac_url_base, $lang, $include_path;
		global $dbh;
		global $PMBusernom;
		global $PMBuserprenom;
		global $PMBuseremail;
		global $dsi_send_empr_date_expiration;
//		global $pmb_type_audit;

		if (!$this->nb_notices && $this->diffusion_email) {
		    if($this->diffusions_history) {
		        $this->get_bannette_diffusion()->set_num_bannette($this->id_bannette)
		        ->set_mail_object(strip_tags($this->get_formatted_text($this->comment_public)))
		        ->set_mail_content($this->get_formatted_mail());
		        $this->get_bannette_diffusion()->save();
		    }
		    return $msg['dsi_ban_empty']."<br />"; // On demande à diffuser le contenu et la bannette vide : pas question d'envoyer du vide
		}

		// purge des archives au dela de $this->archive_number ou si a 0
		$this->clean_archive();

		mysql_set_wait_timeout(3600);

		$pieces_jointes = array();
		if ($this->typeexport) {
			$fic_params = $base_path."/admin/convert/imports/".$this->typeexport."/params.xml";
			$temppar = file_get_contents($fic_params);
			$params = _parser_text_no_function_($temppar,"PARAMS");
			if ($params["OUTPUT"][0]["SUFFIX"]) $ext=$params["OUTPUT"][0]["SUFFIX"];
			else $ext="fic";
			$pieces_jointes[0]["nomfichier"] = $this->prefixe_fichier.today().".".$ext ;
			$pieces_jointes[0]["contenu"] = $this->get_export_contenu();
		}
		$nb_dest=0;
		$nb_echec=0;
		$nb_no_email=0;
		$no_email = '';
		$echec_email = '';

		if($this->associated_campaign) {
			$campaign = new campaign();
			$campaign->set_type('dsi');
			$campaign->set_label($this->nom_bannette);
			$campaign->set_descriptors($this->bannette_descriptors->descriptors);
			$campaign->save();
		}
		$filtre = '';
		if (!$dsi_send_empr_date_expiration) {
		    $filtre = ' and empr_date_expiration>=NOW() ';
		}
		$requete_list_empr = "select id_empr, empr_lang from empr, bannette_abon where num_bannette='".$this->id_bannette."' and num_empr=id_empr $filtre order by empr_nom, empr_prenom ";
        $res_list_empr = pmb_mysql_query($requete_list_empr);
		while ($list_empr=pmb_mysql_fetch_object($res_list_empr)) {
			//on utilise la langue de l'emprunteur
			$lang_messages = static::get_lang_messages($list_empr->empr_lang);
			$requete = "select id_empr, empr_lang, empr_cb, empr_mail, empr_nom, empr_prenom, empr_login, empr_password, if(empr_sexe=2,'".$lang_messages["civilite_madame"]."',";
			$requete .= "if(empr_sexe=1,'".$lang_messages["civilite_monsieur"]."','".$lang_messages["civilite_unknown"]."')) as empr_sexe, empr_adr1, empr_adr2, empr_cp, empr_ville,";
			$requete .= "empr_pays, empr_tel1, empr_tel2, date_format(empr_date_adhesion, '".$lang_messages["format_date"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$lang_messages["format_date"]."') as aff_empr_date_expiration,";
			$requete .= "idstatut, statut_libelle, id_categ_empr, empr_categ.libelle as libelle_categ, idcode as id_codestat, empr_codestat.libelle as libelle_codestat, allow_dsi, allow_dsi_priv, proprio_bannette, bannette_mail, empr_location from empr, empr_statut, empr_categ, empr_codestat, bannette_abon, bannettes ";
			$requete .= "where num_bannette='".$this->id_bannette."' and id_empr=".$list_empr->id_empr." and num_empr=id_empr and empr_statut=idstatut and empr_categ=id_categ_empr and empr_codestat=idcode and num_bannette=id_bannette ";
			$res = pmb_mysql_query($requete);
			$empr=pmb_mysql_fetch_object($res);

			$emaildest = $this->get_empr_mail($list_empr->id_empr);
			if ($emaildest) {
				if ((!$empr->allow_dsi && !$empr->proprio_bannette) || (!$empr->allow_dsi_priv && $empr->proprio_bannette)) {
					//si la dsi n'est pas autorisée pour ce lecteur, affichage de l'info mais pas d'envoi
					$nb_echec++;
					$echec_email .= "- ".$empr->empr_nom." ".$empr->empr_prenom." (".$lang_messages["statut_empr"]."' ".$empr->statut_libelle."')<br />" ;
				} else {
					//Expéditeur du mail
					$sender_name = $PMBuserprenom." ".$PMBusernom;
					$sender_mail = $PMBuseremail;
					if ($this->num_sender) {
						$user_email = user::get_param($this->num_sender, 'user_email');
						if($user_email) {
							$sender_name = user::get_name($this->num_sender);
							$sender_mail = $user_email;
						}
					}

					$mail_content = $this->get_formatted_mail($empr);
					$mail_object = strip_tags($this->get_formatted_text($this->comment_public, $empr));

					$mail_dsi = new mail_dsi();
					$mail_dsi->set_mail_to_id($empr->id_empr)
							->set_mail_object($mail_object)
							->set_mail_content($mail_content)
							->set_mail_attachments($pieces_jointes);
					if ($this->num_sender) {
					    $mail_dsi->set_mail_from_id($this->num_sender);
					    $mail_dsi->set_setting_value('sender', 'user');
					}
					if(!empty($this->associated_campaign)) {
						$mail_dsi->set_associated_campaign($this->associated_campaign)
								->set_associated_num_campaign($campaign->get_id());
					}
					$res_envoi = $mail_dsi->send_mail();
					if ($pmb_mail_delay*1) {
					    sleep((int)$pmb_mail_delay*1/1000);
					    if(!pmb_mysql_ping($dbh)) {
					        $dbh = connection_mysql();
					    }
					}
					if ($res_envoi) {
						$nb_dest++;
					} else {
						$nb_echec++;
						$echec_email .= "- ".$empr->empr_nom." ".$empr->empr_prenom."<br />" ;
						if($this->diffusions_history) {
							$this->get_bannette_diffusion()->add_failed_recipient($empr->id_empr);
						}
					}
				}
			} else {
				$nb_no_email++;
				$no_email .= "- ".$empr->empr_nom." ".$empr->empr_prenom."<br />" ;
				if($this->diffusions_history) {
					$this->get_bannette_diffusion()->add_failed_recipient($empr->id_empr);
				}
			}
			if($this->diffusions_history) {
				$this->get_bannette_diffusion()->add_recipient($empr->id_empr);
			}
		}
		// remplissage du panier avec le contenu de la bannette
		if ($this->num_panier) {
			$temp_requete = "delete from caddie_content where caddie_id='".$this->num_panier."'" ;
			pmb_mysql_query($temp_requete);
			$temp_requete = "insert into caddie_content (caddie_id, object_id) (select ".$this->num_panier.", num_notice from bannette_contenu where num_bannette=".$this->id_bannette.")" ;
			pmb_mysql_query($temp_requete);
		}

		/* A commenter pour tests */
		$temp_requete = "update bannettes set date_last_envoi=sysdate() where id_bannette='".$this->id_bannette."' " ;
		$res = pmb_mysql_query($temp_requete);

//		if($pmb_type_audit) {
//			audit::insert_modif(AUDIT_BANNETTE,$this->id_bannette, $msg['dsi_ban_diffuser']);
//		}
		if($this->diffusions_history) {
			$this->get_bannette_diffusion()->set_records($this->get_elements());
			$this->get_bannette_diffusion()->set_num_bannette($this->id_bannette)
					->set_mail_object(strip_tags($this->get_formatted_text($this->comment_public)))
					->set_mail_content($this->get_formatted_mail());
			$this->get_bannette_diffusion()->save();
		}

		$res_envoi = $msg["dsi_dif_res_dif"]."<ul><li>".$msg["dsi_dif_res_dif_mail_ok"].": $nb_dest </li>";
		if ($nb_echec)
			$res_envoi .= "<li>".$msg["dsi_dif_res_dif_mail_echec"].": $nb_echec <blockquote>$echec_email</blockquote></li>" ;
		if ($nb_no_email)
			$res_envoi .= "<li>".$msg["dsi_dif_res_dif_no_mail"].": $nb_no_email <blockquote>$no_email</blockquote></li>" ;
		$res_envoi .= "</ul>" ;
		if ($nb_echec || $nb_no_email)
			$res_envoi .= "<script>openPopUp('./print_dsi.php?id_bannette=$this->id_bannette', 'print_dsi')</script>" ;


		$this->detroy_env();

		return $res_envoi ;
	}

	protected function get_display_icons() {
		global $msg;

		$display = '';
		$display .= "<a href='#' onClick=\"openPopUp('./print_cart.php?bannette_id=".$this->id_bannette."&action=print_prepare','print',600,700,-2,-2,'scrollbars=yes,menubar=0,resizable=yes'); return false;\">";
		$display .= "<img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>";
// 		$display .= "<a href='#' onClick=\"openPopUp('./print.php?bannette_id=".$this->id_bannette."&action_print=print_prepare','print'); return false;\">";
// 		$display .= "<img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
//		$display .= "<a href='#' onClick=\"openPopUp('./download.php?bannette_id=".$this->id_bannette."&action_download=download_prepare','download'); return false;\">";
//		$display .= "<img src='".get_url_icon('upload_docnum.gif')."' style='border:0px' class='center' alt=\"".$msg["docnum_download"]."\" title=\"".$msg["docnum_download"]."\"/></a>";
		return $display;
	}

	// ---------------------------------------------------------------
	// affichage du contenu complet d'une bannette
	// ---------------------------------------------------------------
	public function aff_contenu_bannette ($url_base="", $no_del=false ) {
		global $msg;
		global $begin_result_liste, $end_result_liste;
		global $end_result_list;
		global $url_base_suppr_bannette ;

		$return_affichage = "";
		$url_base_suppr_bannette = $url_base ;

		$cb_display = "
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
	    		<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
			</div>
			";

		$requete = "SELECT num_notice FROM bannette_contenu join notices on notice_id = num_notice where num_bannette='".$this->id_bannette."' order by index_sew";

		$liste=array();
		$liste_group = array();
		$result = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($result)) {
			while ($temp = pmb_mysql_fetch_object($result)) {
				if($this->group_pperso) {
					$this->p_perso->get_values($temp->num_notice);
					$values = $this->p_perso->values;
					foreach ( $values as $field_id => $vals ) {
						if ($this->group_pperso==$field_id) {
							break;
						}
					}
					$liste_group[$vals[0]][] = $temp->num_notice;
				}
				else $liste[] = array('num_notice' => $temp->num_notice) ;
			}
		}
		if(count($liste_group)) {
			foreach($liste_group as $list_notice) {
				foreach($list_notice as $num_notice) {
					$liste[] = array('num_notice' => $num_notice) ;
				}
			}
		}

		if ((empty($liste) && !is_array($liste)) || !is_array($liste)) {
			return $msg['dsi_ban_empty'];
		} else {
			// boucle de parcours des notices trouvées
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			$return_affichage .= $begin_result_liste;
			//Affichage du lien impression et panier
			$return_affichage .= $this->get_display_icons();

			$records = array();
			foreach ($liste as $cle => $object) {
				$records[] = $object['num_notice'];
			}
			$elements_records_list_ui = new elements_records_list_ui($records, count($records), false);
			if (!$no_del) {
				$lien_suppr_cart = "<a href='".$url_base."&suite=suppr_notice&num_notice=!!notice_id!!&id_bannette=".$this->id_bannette."'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a> ";
				elements_records_list_ui::set_link_delete_cart($lien_suppr_cart);
			}
			elements_records_list_ui::set_link_delete_cart($lien_suppr_cart);
			$elements_records_list_ui->set_show_resa(0);
			$elements_records_list_ui->set_show_statut(0);
			$elements_records_list_ui->set_show_resa_planning(0);
			$elements_records_list_ui->set_draggable(0);
			$elements_records_list_ui->set_ajax_mode(0);
			$return_affichage .= $elements_records_list_ui->get_elements_list();

			$return_affichage .= $end_result_liste;
		}
		$return_affichage .= "<br />" ;
		return $return_affichage ;
	}

	// ---------------------------------------------------------------
	//		suppr_notice() : suppression d'une notice d'une bannette
	// ---------------------------------------------------------------
	public function suppr_notice($num_notice) {
		$query = "delete from bannette_contenu WHERE num_bannette='$this->id_bannette' and num_notice='$num_notice'";
		pmb_mysql_query($query);
	}

	public function clean_archive(){
		// purge des archives au dela de $this->archive_number ou si a 0
		if(!$this->archive_number){
			$req="delete from dsi_archive where num_banette_arc='".$this->id_bannette."' ";
			pmb_mysql_query($req);
		}else{
			$date_arc_list_to_delete=array();
			$nb=0;
			$req="select distinct date_diff_arc from dsi_archive where num_banette_arc='".$this->id_bannette."' order by date_diff_arc desc";
			$res_arc=pmb_mysql_query($req);
			while (($r = pmb_mysql_fetch_object($res_arc))){
				if($nb++ >= $this->archive_number){
					$date_arc_list_to_delete[]=$r->date_diff_arc;
				}
			}
			foreach($date_arc_list_to_delete as $date_arc){
				$req="delete from dsi_archive where num_banette_arc='".$this->id_bannette."' and date_diff_arc='".$date_arc."'";
				pmb_mysql_query($req);
			}
		}
	}

	protected function get_record_isbd($n) {
		global $opac_url_base;

		$environement=array();
		$environement["short"] = 6 ;
		$environement["ex"] = 0 ;
		$environement["exnum"] = 1 ;

		$isbd = '';
		if ($n->niveau_biblio == 'm'|| $n->niveau_biblio == 'b') {
			$notice_display = new mono_display($n,$environement["short"],"",$environement["ex"],"","","",0,1,$environement["exnum"],0,"",0,true,false,0,0,1,$this->id_bannette);
		} elseif ($n->niveau_biblio == 's' || $n->niveau_biblio == 'a') {
			$notice_display = new serial_display($n, 6, "", "", "", "", "", 0,1,$environement["exnum"],0, false,0,0,'',false,1,$this->id_bannette);

		}
		$isbd .= "<a href='".$opac_url_base."index.php?database=".DATA_BASE."&lvl=notice_display&id=".$n->notice_id.static::get_url_connexion_auto()."'><b>".$notice_display->header."</b></a><br /><br />\r\n";
		$isbd .= $notice_display->isbd;
		return $isbd;
	}

	protected function get_export_contenu() {
		if(!isset($this->export_contenu) || !$this->export_contenu) {
			$this->export_contenu=cree_export_notices($this->liste_id_notice, start_export::get_id_by_path($this->typeexport), 1,$this->param_export) ;
		}
		return $this->export_contenu;
	}

	protected function get_location() {
		//Recherche de la loc de l'emprunteur
		$docs_loc = 0;
		$query = "select empr_location from empr, bannettes where id_bannette='".$this->id_bannette."' and proprio_bannette=id_empr";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$docs_loc = $row->empr_location;
		}
		return $docs_loc;
	}

	protected function get_display_template() {
		$already_printed=array();

		$display_template = "";
		$template_id = $this->get_notice_tpl();
		foreach($this->list as $num_notice) {
			$tpl_notice=$this->get_tpl_notice($num_notice);
			if(!in_array($num_notice, $already_printed)){
				if (!$template_id) {
					$display_template .= $tpl_notice."<hr />\r\n";
				} else {
					$display_template .= $tpl_notice."\r\n";
				}
				$already_printed[]=$num_notice;
			}
		}
		return $display_template;
	}

	protected function get_tpl_notice($num_notice) {
		global $deflt2docs_location;

		$tpl_notice="";
		switch ($this->get_notice_display_type()) {
		    case 1:
		        //Affichage Django
		        $tpl_notice .= record_display::get_display_in_result($num_notice, $this->get_django_directory());
		        break;
		    default:
		        $template_id = $this->get_notice_tpl();
		        if($template_id) {
		            $notice_tpl_gen = notice_tpl_gen::get_instance($template_id);
		            $tpl_notice .= $notice_tpl_gen->build_notice($num_notice, $deflt2docs_location, false, $this->id_bannette);
		        }
		        break;
		}
		if(!$tpl_notice) {
			$n=pmb_mysql_fetch_object(pmb_mysql_query("select * from notices where notice_id=".$num_notice));
			global $use_opac_url_base; $use_opac_url_base=1;
			global $use_dsi_diff_mode; $use_dsi_diff_mode=1;
			if($this->statut_not_account)  $use_dsi_diff_mode=2;//On ne tient pas compte des statuts de notice pour la diffusion
			$tpl_notice .= $this->get_record_isbd($n);
			$tpl_notice=str_replace('<!-- !!avis_notice!! -->', "", $tpl_notice);
		}
		return $tpl_notice;
	}

	protected function get_tri_tpl() {
	    $template_id = $this->get_notice_tpl();
		if(!isset($this->tri_tpl[$template_id])) {
			$this->tri_tpl[$template_id] = array();
			$already_printed=array();
			// Si un champ perso est donné comme critère de regroupement
			if($this->group_pperso && $this->group_type!=1) {
				foreach($this->list_group as $group) {
					foreach($group as $notice) {
						$num_notice = $notice->num_notice;
						$tpl_notice = $this->get_tpl_notice($num_notice);
						if($this->notice_group[$num_notice]) {
							foreach($this->notice_group[$num_notice] as $id=>$cpDisplay){

								if($this->display_notice_in_every_group){
									$already_printed=array();
								}

								if(!isset($this->tri_tpl[$template_id][$cpDisplay])
										|| !$this->tri_tpl[$template_id][$cpDisplay]
										|| !in_array($tpl_notice, $this->tri_tpl[$template_id][$cpDisplay])){
									if(!in_array($num_notice, $already_printed)){
										$this->tri_tpl[$template_id][$cpDisplay][]= $tpl_notice;
										$already_printed[]=$num_notice;
									}
								}
							}
						}
					}
				}
			} else {
				foreach($this->list as $num_notice) {
					$tpl_notice = $this->get_tpl_notice($num_notice);
					if($this->notice_group[$num_notice]) {
						foreach($this->notice_group[$num_notice] as $id=>$cpDisplay){

							if($this->display_notice_in_every_group){
								$already_printed=array();
							}

							if(!isset($this->tri_tpl[$template_id][$cpDisplay])
									|| !$this->tri_tpl[$template_id][$cpDisplay]
									|| !in_array($tpl_notice, $this->tri_tpl[$template_id][$cpDisplay])){
								if(!in_array($num_notice, $already_printed)){
									$this->tri_tpl[$template_id][$cpDisplay][]= $tpl_notice;
									$already_printed[]=$num_notice;
								}
							}
						}
					}
				}
			}
		}
		return $this->tri_tpl[$template_id];
	}

	protected function get_data_summary_structure() {
	    global $msg;

		$data = array();
		$already_printed=array();

		if (count($this->list) && $this->group_type==1) {
			$facette = $this->get_instance_bannette_facette();
			$data = $facette->build_document_data($this->list,$this->get_notice_tpl());
		} else {
			if($this->is_grouped()) {
				$tri_tpl = $this->get_tri_tpl();
				$this->pmb_ksort($tri_tpl);
				$index = 0;
				foreach ($tri_tpl as $titre => $liste) {
					$index++;
					$data[$index]['level']=1;
					$data[$index]['title']=$titre;
					$nb=0;
					foreach ($liste as $val) {
						$data[$index]['records'][$nb]['render']=$val;
						$nb++;
					}
				}
			} else {
				foreach($this->list as $num_notice) {
					$tpl_notice = $this->get_tpl_notice($num_notice);
					if(!in_array($num_notice, $already_printed)){
					    $already_printed[]=$num_notice;
					    $data[0]['title']=$msg['dsi_record_not_classified'];
						$data[0]['records'][]['render']=$tpl_notice;
					}
				}
			}
		}
		return $data;
	}

	protected function get_display_summary() {
		global $group_separator;
		global $notice_separator;

		$display="";

		$index=0;
		$summary="";
		$template_id = $this->get_notice_tpl();
		$tri_tpl = $this->get_tri_tpl();
		$this->pmb_ksort($tri_tpl);
		foreach ($tri_tpl as $titre => $liste) {
			if($group_separator)$display.=$group_separator;
			else $display.= "<div class='hr_group'><hr /></div>";
			$index++;
			$display.= "<a name='[".$index."]'></a><h1><span class='summary_elt_index'>".$index." - </span>".$titre."</h1>";
			$summary.="<a href='#[".$index."]' class='summary_elt'><span class='summary_elt_index'>".$index." - </span>".$titre."</a><br />";

			$nb=0;
			foreach ($liste as $val) {
				$display.=$val;
				if(++$nb < count($liste)){
					if(!$template_id) {
						if($notice_separator)$display.=$notice_separator;
						else $display.="<div class='hr'><hr /></div>";
					} else {
						$display.="<br />";
					}
				}
			}
			$display.= "\r\n";
		}
		if($this->document_add_summary && $index > 1){ // N'affichons pas le sommaire s'il n'y a qu'une seule entrée
			$display="<a name='summary'></a><div class='summary'><br />".$summary."</div>".$display;
		}
		return $display;
	}

	protected function build_lists($use_limit=1) {
		$this->use_limit = $use_limit;
		$this->list=array();
		$this->list_group=array();
		$this->notice_group=array();
		$result = pmb_mysql_query($this->get_query_records());
		if(pmb_mysql_num_rows($result)) {
			while (($temp = pmb_mysql_fetch_object($result))) {
				// Si un champ perso est donné comme critère de regroupement
				if($this->group_pperso && $this->group_type!=1) {
					$this->p_perso->get_out_values($temp->num_notice);
					$values = $this->p_perso->values;
					$trouve = false;
					foreach ( $values as $field) {
						if ($this->group_pperso==$field['id']) {
							foreach($field['values'] as $cpVal){
								$this->notice_group[$temp->num_notice][] = $this->p_perso->get_formatted_output(array($cpVal['value']),$field['id']);
								if (!$cpVal['value']) {
									$cpVal['value'] = "_no_value_";
								}
								$this->list_group[$cpVal['value']][] = $temp;
								$trouve = true;
							}

							$this->field_type = $this->p_perso->t_fields[$field['id']]["TYPE"];
							$this->field_id = $field['id'];
						}
					}
					if (!$trouve) {
						$this->list_group["_no_value_"][] = $temp;
						if ($field['id']) {
							$this->notice_group[$temp->num_notice][] = $this->p_perso->get_formatted_output(array(),$field['id']);
						} else {
							$this->notice_group[$temp->num_notice][] = "";
						}
					}
				} else {
					$this->list[] = $temp->num_notice;
				}
				// archivage
				if($this->archive_number){
					$query = "select count(*) from dsi_archive where num_banette_arc='".$this->id_bannette."' and num_notice_arc='".$temp->num_notice."' and date_diff_arc=CURDATE()";
					$result_archive = pmb_mysql_query($query);
					if(pmb_mysql_result($result_archive, 0, 0) == 0) {
						$req="insert into dsi_archive set num_banette_arc='".$this->id_bannette."', num_notice_arc='".$temp->num_notice."', date_diff_arc=CURDATE()    ";
						pmb_mysql_query($req);
					}
				}
			}
		}
	}

	public function pmb_ksort(&$table){
		$table_final=array();
		if ($this->field_type == 'list') {
			if (is_array($table)) {
				reset($table);
				$tmp=array();
				$requete = "select ordre, notices_custom_list_lib from notices_custom_lists";
				$requete .= " where notices_custom_champ=".$this->field_id;
				$res = pmb_mysql_query($requete);
				while ($row = pmb_mysql_fetch_object($res)) {
					$this->group_pperso_order[$row->notices_custom_list_lib] = $row->ordre;
				}
				uksort($table, array(&$this,"cmp_pperso"));
			}
		} else {
			if (is_array($table)) {
				reset($table);
				$tmp=array();
				$tmp_key=array();
				$tmp_contens=array();
				foreach ($table as $key => $value ) {
		       		$tmp[]=strtoupper(convert_diacrit($key));
		       		$tmp_key[]=$key;
		       		$tmp_contens[]=$value;
				}
				asort($tmp);
				foreach ($tmp as $key=>$value ) {
		       		$table_final[$tmp_key[$key]]=$tmp_contens[$key];
				}
				$table=$table_final;
			}
		}
	}

	public function cmp_pperso($a,$b) {
		if ($this->group_pperso_order[$a]>$this->group_pperso_order[$b]) return 1;
		if ($this->group_pperso_order[$a]<$this->group_pperso_order[$b]) return -1;
		return 0;
	}

	public function get_formatted_comment_public() {
		global $opac_url_base ;

		$url_base_opac = $opac_url_base."empr.php?lvl=bannette";
		$public  = "<a href='$url_base_opac&id_bannette=".$this->id_bannette.static::get_url_connexion_auto()."'>";
		$public .= $this->comment_public." : &nbsp;!!print_n_notices!!";
		$public .= "</a>";
		return $public;
	}

	// ---------------------------------------------------------------
	//		construit_liens_HTML() : Préparation du contenu du mail ou du bulletin
	// ---------------------------------------------------------------
	public function construit_liens_HTML() {
		$resultat_aff = "<style type='text/css'>
			body {
			font-size: 10pt;
			font-family: verdana, geneva, helvetica, arial;
			color:#000000;
			background:#FFFFFF;
			}
			td {
			font-size: 10pt;
			font-family: verdana, geneva, helvetica, arial;
			color:#000000;
			}
			th {
			font-size: 10pt;
			font-family: verdana, geneva, helvetica, arial;
			font-weight:bold;
			color:#000000;
			background:#DDDDDD;
			text-align:left;
			}
			hr {
			border:none;
			border-bottom:1px solid #000000;
			}
			h3 {
			font-size: 12pt;
			color:#000000;
			}
			</style>";

		$entete = str_replace ("!!public!!",$this->get_formatted_comment_public(),$this->entete_mail) ;
		$entete = str_replace ("!!equation!!",$this->bannette_equations->get_text(),$entete) ;
		$entete = str_replace ("!!date!!",formatdate(today()),$entete) ;
		$entete = str_replace ("!!nb_notice!!",$this->nb_notices,$entete) ;
		return $entete ;
	}

	protected function get_elements_from_query($query) {
		$elements = array();
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while ($res = pmb_mysql_fetch_object($result)) {
				$elements[]=$res->num_notice ;
			}
		}
		return $elements;
	}

	protected function get_elements() {
		$query = "SELECT num_notice from bannette_contenu WHERE num_bannette='".$this->id_bannette."' " ;
		//initialisation du tableau à chaque fois que cette fonction est appelée pour éviter un mauvais cumul
		$elements = $this->get_elements_from_query($query);
		$this->nb_notices = count($elements);
		return $elements;
	}

	// ---------------------------------------------------------------
	//		compte_elements() : méthode pour pouvoir recompter en dehors !
	// ---------------------------------------------------------------
	public function compte_elements() {
		//initialisation du tableau à chaque fois que cette fonction est appelée pour éviter un mauvais cumul
		$this->liste_id_notice = $this->get_elements();

		$req_nb = "SELECT count(1) as nb_abonnes from bannette_abon WHERE num_bannette='".$this->id_bannette."' " ;
		$res_nb = pmb_mysql_query($req_nb);
		$res = pmb_mysql_fetch_object($res_nb);
		$this->nb_abonnes = $res->nb_abonnes ;
		$requete = "SELECT if(date_last_remplissage>date_last_envoi,1,0) as alert_diff ";
		$requete .= "FROM bannettes WHERE id_bannette='".$this->id_bannette."' " ;
		$result = pmb_mysql_query($requete);
		$row = pmb_mysql_fetch_object($result);
		if (!empty($row)) {
			$this->alert_diff = $row->alert_diff;
		}
	}

	public function m_lecteur_adresse($empr) {
		global $msg;

		$res_final=array();

		if ($empr->empr_prenom) $empr->empr_nom=$empr->empr_prenom." ".$empr->empr_nom;
		$res_final[]=$empr->empr_nom;

		if ($empr->empr_adr2 != "") $empr->empr_adr1 = $empr->empr_adr1."\n" ;
		if (($empr->empr_cp != "") || ($empr->empr_ville != "")) $empr->empr_adr2 = $empr->empr_adr2."\n" ;
		$adr = $empr->empr_adr1.$empr->empr_adr2.$empr->empr_cp." ".$empr->empr_ville ;
		if ($empr->empr_pays != "") $adr = $adr."\n".$empr->empr_pays ;
		$res_final[]=$adr;

		if ($empr->empr_tel1 != "") {
			$tel = $msg['fpdf_tel']." ".$empr->empr_tel1." " ;
		}
		if ($empr->empr_tel2 != "") {
			$tel = $tel.$msg['fpdf_tel2']." ".$empr->empr_tel2;
		}
		if ($empr->empr_mail != "") {
			if ($tel) $tel = $tel."\n" ;
			$mail = $msg['fpdf_email']." ".$empr->empr_mail;
		}

		$res_final[]="\n".$tel.$mail;

		return implode("\n",$res_final);
	}

	public function m_lecteur_info($empr) {
		global $msg;

		$res_final=array();

		$requete = "SELECT group_concat(libelle_groupe SEPARATOR ', ') as_all_groupes, 1 as rien from groupe join empr_groupe on groupe_id=id_groupe WHERE lettre_rappel_show_nomgroup=1 and empr_id='".$empr->id_empr."' group by rien ";
		$lib_all_groupes=pmb_sql_value($requete);
		if ($lib_all_groupes) $lib_all_groupes="\n".$lib_all_groupes;

		if ($empr->empr_prenom) $empr->empr_nom=$empr->empr_prenom." ".$empr->empr_nom;
		$res_final[]=$empr->empr_nom;

		if ($empr->empr_adr2 != "") $empr->empr_adr1 = $empr->empr_adr1."\n" ;
		if (($empr->empr_cp != "") || ($empr->empr_ville != "")) $empr->empr_adr2 = $empr->empr_adr2."\n" ;
		$adr = $empr->empr_adr1.$empr->empr_adr2.$empr->empr_cp." ".$empr->empr_ville ;
		if ($empr->empr_pays != "") $adr = $adr."\n".$empr->empr_pays ;
		$res_final[]=$adr;

		if ($empr->empr_tel1 != "") {
			$tel = $msg['fpdf_tel']." ".$empr->empr_tel1." " ;
		}
		if ($empr->empr_tel2 != "") {
			$tel = $tel.$msg['fpdf_tel2']." ".$empr->empr_tel2;
		}
		if ($empr->empr_mail != "") {
			if ($tel) $tel = $tel."\n" ;
			$mail = $msg['fpdf_email']." ".$empr->empr_mail;
		}

		$res_final[]="\n".$tel.$mail.$lib_all_groupes;
		$res_final[]="";
		$res_final[]=$msg['fpdf_carte']." ".$empr->empr_cb;
		$res_final[]=$msg['fpdf_adherent']." ".$empr->aff_empr_date_adhesion." ".$msg['fpdf_adherent_au']." ".$empr->aff_empr_date_expiration ;

		return implode("\n",$res_final);
	}

	public function get_private_bannette_tpl() {
		global $dsi_private_bannette_tpl;

		return intval($dsi_private_bannette_tpl);
	}

	public function get_instance_bannette_facette() {
		global $use_dsi_diff_mode;
		if($this->statut_not_account) {
			$use_dsi_diff_mode=2;//On ne tient pas compte des statuts de notice pour la diffusion
		} else {
			$use_dsi_diff_mode=1;
		}
		$bannette_facettes = new bannette_facettes($this->id_bannette);
		$bannette_facettes->noti_django_directory = $this->django_directory;
		return $bannette_facettes;
	}

	public function set_records_globals() {
		global $use_opac_url_base;
		global $prefix_url_image ;
		global $depliable ;
		global $opac_url_base;

		$use_opac_url_base=true;
		// pour URL image vue de l'extérieur

		$depliable = 0;
		$prefix_url_image = $opac_url_base;
	}

	public function is_subscribed($id_empr=0) {
		$id_empr = intval($id_empr);
		$query = "select count(*) as subscribed from bannette_abon where num_bannette = '".$this->id_bannette."' and num_empr='".$id_empr."' ";
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 'subscribed');
	}

	public function is_grouped() {
	    $is_grouped = false;
	    switch ($this->output_format) {
	        case 'document':
	            if($this->group_type != 1 && !$this->group_pperso) {
	                $is_grouped = false;
	            } else {
	                $is_grouped = $this->document_group;
	            }
	            break;
	        case 'export':
	        case 'mail':
	        default:
	            $is_grouped = $this->group_pperso;
	            break;

	    }
	    return $is_grouped;
	}

	public function add_summary() {
	    $add_summary = true;
	    switch ($this->output_format) {
	        case 'document':
	            $add_summary=$this->document_add_summary;
	            break;
	        case 'export':
	        case 'mail':
	        default:
	            $add_summary = true;
	            break;

	    }
	    return $add_summary;
	}

	public function get_notice_tpl() {
	    $notice_template_id = 0;
	    switch ($this->output_format) {
	        case 'document':
	            $notice_template_id = $this->document_notice_tpl;
	            break;
	        case 'export':
	        case 'mail':
	        default:
	            $notice_template_id = $this->notice_tpl;
	            break;

	    }
	    return $notice_template_id;
	}

	public function get_output_format() {
	    return $this->output_format;
	}

	public function set_output_format($output_format='mail') {
	    $this->output_format = $output_format;
	}

	public function get_notice_display_type() {
	    $notice_display_type = 0;
	    switch ($this->output_format) {
	        case 'document':
	            $notice_display_type = $this->document_notice_display_type;
	            break;
	        case 'export':
	        case 'mail':
	        default:
	            $notice_display_type = $this->notice_display_type;
	            break;
	    }
	    return $notice_display_type;
	}

	public function get_django_directory() {
	    $django_directory = 'common';
	    switch ($this->output_format) {
	        case 'document':
	            if($this->document_django_directory) {
	                $django_directory = $this->document_django_directory;
	            }
	            break;
	        case 'export':
	        case 'mail':
	        default:
	            if($this->django_directory) {
	                $django_directory = $this->django_directory;
	            }
	            break;
	    }
	    return $django_directory;
	}

	public function get_id() {
		return $this->id_bannette;
	}

	public function get_formatted_entete_mail() {
		$empr = $this->get_empr_informations($_SESSION["id_empr_session"]);
		return $this->get_formatted_text($this->entete_mail, $empr);
	}

	public function get_formatted_piedpage_mail() {
		$empr = $this->get_empr_informations($_SESSION["id_empr_session"]);
		return $this->get_formatted_text($this->piedpage_mail, $empr);
	}

	public function get_render_comment_public() {
		$empr = $this->get_empr_informations($_SESSION["id_empr_session"]);
		return $this->get_formatted_text($this->comment_public, $empr);
	}

	public function get_bannette_diffusion() {
		if(!isset($this->bannette_diffusion)) {
			$this->bannette_diffusion = new bannette_diffusion();
		}
		return $this->bannette_diffusion;
	}

	public static function has_rights($id_bannette) {
		global $id_empr;

		$id_bannette = intval($id_bannette);

		$query = "select count(*) from bannettes where id_bannette = ".$id_bannette." and proprio_bannette = ".$id_empr;
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 0);
	}

	public static function get_url_connexion_auto($prefix="&") {
		global $dsi_connexion_auto;

		if($dsi_connexion_auto) {
			return $prefix."code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!";
		}
		return "";
	}

	/**
	 *
	 * @param integer $id
	 * @return bannette
	 */
	public static function get_instance($id) {
		if(!isset(static::$instances[$id])) {
			static::$instances[$id] = new bannette($id);
		}
		return static::$instances[$id];
	}

	protected static function get_lang_messages($lang) {
		global $include_path;

		if (!isset(static::$lang_messages[$lang])) {
			$messages = new XMLlist($include_path."/messages/".$lang.".xml", 0);
			$messages->analyser();
			static::$lang_messages[$lang] = $messages->table;
		}
		return static::$lang_messages[$lang];
	}

	public static function set_controller($controller) {
		static::$controller = $controller;
	}

	protected static function format_url($url='') {
		global $base_path;

		if(isset(static::$controller) && is_object(static::$controller)) {
			$url_base = static::$controller->get_url_base();
			if(strpos($url_base, '?') === false) {
				$url_base .= '?';
			}
			if((substr($url, 0, 1) == '&') && (substr($url_base, -1) == '&')) {
				return $url_base.substr($url, 1);
			} else {
				return $url_base.$url;
			}
		} else {
			if(substr($url, 0, 1) == '&') {
				return $base_path.'/dsi.php?'.substr($url, 1);
			} else {
				return $base_path.'/dsi.php?'.$url;
			}
		}
	}

	/**
	 * Destruction de l'environnement de la bannette.
	 *
	 * @return void
	 */
	protected function detroy_env()
	{
		$cleanGlobals = [
			// Global définis pour la recherche et human query
			'date_parution_start_query',
			'date_parution_end_query',
			'date_parution_exact_query',
			'memo_tempo_table_to_rebuild',
			'multi_crit_indexation_docnum_allfields',
			'multi_crit_indexation_oeuvre_title',
			'statut_query',
			'typdoc_query',
			'ex_query',
			'concept_query',
			'search',
			'explicit_search',
			'got_empty_word',
			'docnum_query',
			'f_notice_id',
			'launch_search',
			// Global définis pour la diffusion
			'depliable',
			'prefix_url_image',
			'empty_word_converted',
			'use_dsi_diff_mode',
			'cms_debug_mode',
			'notice_data'
		];

		foreach ($GLOBALS as $globalName => $_) {
			if (
				// Global définis pour la recherche et human query
				strpos($globalName, 'op_') === 0 ||
				strpos($globalName, 'inter_') === 0 ||
				strpos($globalName, 'field_') === 0 ||
				strpos($globalName, 'fieldvar_') === 0 ||
				// Global définis pour la diffusion
				strpos($globalName, 'empr_') === 0
			) {
				$cleanGlobals[] = $globalName;
			}
		}

		foreach ($cleanGlobals as $globalName) {
			unset($GLOBALS[$globalName]);
		}
	}

	public function get_bannette_descriptors()
	{
		return $this->bannette_descriptors;
	}

	public function get_bannette_equations()
	{
		return $this->bannette_equations;
	}

} # fin de définition de la classe bannette
