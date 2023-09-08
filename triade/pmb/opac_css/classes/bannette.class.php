<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette.class.php,v 1.83 2019-06-11 08:53:16 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ("$class_path/search.class.php") ; 
require_once ("$class_path/equation.class.php") ; 
require_once ($include_path."/mail.inc.php") ;
require_once ($include_path."/export_notices.inc.php");
require_once($class_path."/notice_tpl_gen.class.php");
global $gestion_acces_active, $gestion_acces_empr_notice;
if($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
	require_once ("$class_path/acces.class.php") ; 
}
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/bannette_facettes.class.php");
require_once($class_path."/bannette_descriptors.class.php");
require_once($class_path."/bannette_equations.class.php");
require_once($class_path."/bannette_tpl.class.php");

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
	public $notice_tpl="";
	public $date_last_remplissage="";
	public $date_last_envoi="";
	public $aff_date_last_remplissage="";
	public $aff_date_last_envoi="";
	public $date_last_envoi_sql="";
	public $proprio_bannette=0;
	public $bannette_auto=0;
	public $periodicite=0;
	public $diffusion_email=0;
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
	public $export_contenu = "";
	public $typeexport = "";
	public $prefixe_fichier = "prefix_";
	public $param_export = array();
	public $group_pperso=0;
	public $display_notice_in_every_group=1;
	public $archive_number=0;
	public $group_type = 0;
	public $statut_not_account=0;
	public $field_type='';
	public $field_id=0;
	public $group_pperso_order=array();
	public $document_generate=0;
	public $document_notice_tpl=0;
	public $document_insert_docnum=0;
	public $document_group=0;
	public $document_add_summary=0;
	public $aff_document="";
	public $bannette_opac_accueil=0;
	public $document_diffuse=""; //contenu html du document généré
	protected $bannette_descriptors;
	public $id_bannette_origine = 0; //Utilisé en duplication de bannette
	public $bannette_aff_notice_number = 1; //Afficher le nombre de notices envoyées dans le mail
	protected $bannette_equations;
	protected static $lang_messages;
	
	protected $use_limit=1;
	protected $list;
	protected $list_group;
	protected $notice_group;
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id_bannette = $id+0;
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos
	// ---------------------------------------------------------------
	public function getData() {
		global $msg;
		global $opac_bannette_priv_periodicite;
		
		$this->periodicite = $opac_bannette_priv_periodicite;
		$this->p_perso=new parametres_perso("notices");
		if (!$this->id_bannette) {
			// pas d'identifiant. on retourne un tableau vide
			$this->date_last_envoi=today();
			$this->aff_date_last_envoi=formatdate($this->date_last_envoi);
			$this->date_last_envoi_sql=today();
		} else {
			$requete = "SELECT id_bannette, num_classement, nom_bannette,comment_gestion,comment_public,statut_not_account, ";
			$requete .= "date_last_remplissage, date_format(date_last_remplissage, '".$msg["format_date_heure"]."') as aff_date_last_remplissage, ";
			$requete .= "date_last_envoi,date_last_envoi as date_last_envoi_sql, date_format(date_last_envoi, '".$msg["format_date_heure"]."') as aff_date_last_envoi, ";
			$requete .= "proprio_bannette,bannette_auto,periodicite,diffusion_email, nb_notices_diff, update_type, entete_mail, bannette_tpl_num, piedpage_mail, notice_tpl, num_panier, ";
			$requete .= "limite_type, limite_nombre, typeexport, prefixe_fichier, param_export, group_type, group_pperso, display_notice_in_every_group, archive_number, ";
			$requete .= "document_generate, document_notice_tpl, document_insert_docnum, document_group, document_add_summary, bannette_opac_accueil, bannette_aff_notice_number ";
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
				$this->notice_tpl			= $temp->notice_tpl ;
				$this->date_last_remplissage= $temp->date_last_remplissage ;
				$this->date_last_envoi		= $temp->date_last_envoi ;	
				$this->aff_date_last_remplissage	= $temp->aff_date_last_remplissage ;
				$this->aff_date_last_envoi	= $temp->aff_date_last_envoi ;	
				$this->date_last_envoi_sql	= $temp->date_last_envoi_sql;
				$this->proprio_bannette		= $temp->proprio_bannette ;	
				$this->bannette_auto		= $temp->bannette_auto ;
				$this->periodicite			= $temp->periodicite ;
				$this->diffusion_email		= $temp->diffusion_email ;	
				$this->nb_notices_diff 		= $temp->nb_notices_diff;
				$this->update_type			= $temp->update_type ;
				$this->num_panier			= $temp->num_panier ;
				$this->limite_type 			= $temp->limite_type ;
				$this->limite_nombre 		= $temp->limite_nombre ;
				$this->typeexport 			= $temp->typeexport ;
				$this->prefixe_fichier 		= $temp->prefixe_fichier ;
				$this->group_pperso 		= $temp->group_pperso ;
				$this->group_type 			= $temp->group_type;
				$this->display_notice_in_every_group=$temp->display_notice_in_every_group;
				$this->statut_not_account 	= $temp->statut_not_account ;
				$this->archive_number 		= $temp->archive_number ;
				$this->document_generate 	= $temp->document_generate ;
				$this->document_notice_tpl	= $temp->document_notice_tpl;
				$this->document_insert_docnum= $temp->document_insert_docnum ;
				$this->document_group 		= $temp->document_group ;
				$this->document_add_summary = $temp->document_add_summary ;
				$this->bannette_opac_accueil= $temp->bannette_opac_accueil ;
				$this->bannette_aff_notice_number= $temp->bannette_aff_notice_number;
				$this->param_export			= unserialize($temp->param_export) ;
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
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form($type="pro") {
	
		global $msg, $charset;
		global $dsi_bannette_form;
		global $dsi_bannette_form_abo, $dsi_bannette_form_selvars;
		global $nom_prenom_abo;
		global $dsi_bannette_notices_template, $PMBuserid;
		global $form_cb, $id_classement;
		global $page, $nbr_lignes, $nb_per_page;
	
		if ($type=="abo") $dsi_bannette_form = $dsi_bannette_form_abo ;
	
		if($this->id_bannette) {
			$link_pagination = "";
			if($page > 1) {
				$link_pagination .= "&page=".$page."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page;
			}
			$action = "./dsi.php?categ=bannettes&sub=$type&id_bannette=$this->id_bannette&suite=update&id_classement=$id_classement&form_cb=$form_cb$link_pagination";
			$link_duplicate =  "<input type='button' class='bouton' value='".$msg['bannette_duplicate_bouton']."' onclick='document.location=\"./dsi.php?categ=bannettes&sub=$type&id_bannette=$this->id_bannette&suite=duplicate&id_classement=$id_classement&form_cb=$form_cb$link_pagination\"' />";
			$link_annul = "<input type='button' class='bouton' value='$msg[76]' onClick=\"document.location='./dsi.php?categ=bannettes&sub=$type&id_bannette=&suite=search&id_classement=$id_classement&form_cb=$form_cb$link_pagination';\" />";
			$button_delete = "<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\">";
			$libelle = $msg['dsi_ban_form_modif'];
		} else {
			$action = "./dsi.php?categ=bannettes&sub=$type&id_bannette=0&suite=update";
			$link_duplicate = "";
			$link_annul = "<input type='button' class='bouton' value='$msg[76]' onClick=\"history.go(-1);\" />";
			$libelle = $msg['dsi_ban_form_creat'];
			$button_delete ="";
			if ($this->id_bannette_origine) { //On vient d'une duplication
				$origine_bannette = new bannette($this->id_bannette_origine) ;
				$this->notice_tpl=$origine_bannette->notice_tpl;
			} else {
				$this->notice_tpl=$dsi_bannette_notices_template;
			}
		}
	
		$bannette_tpl_list=bannette_tpl::gen_tpl_select("bannette_tpl_num",$this->bannette_tpl_num);
	
		$dsi_bannette_form = str_replace('!!libelle!!', $libelle, $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!type!!', $type, $dsi_bannette_form);
	
		$dsi_bannette_form = str_replace('!!id_bannette!!', $this->id_bannette, $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!action!!', $action, $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!link_duplicate!!', $link_duplicate, $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!link_annul!!', $link_annul, $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!nom_bannette!!', htmlentities($this->nom_bannette,ENT_QUOTES, $charset), $dsi_bannette_form);
	
		if ($type=="pro") $dsi_bannette_form = str_replace('!!num_classement!!', show_classement_utilise ('BAN', $this->num_classement, 0), $dsi_bannette_form);
		else $dsi_bannette_form = str_replace('!!num_classement!!', "<input type=hidden name=num_classement value=0 />", $dsi_bannette_form);
	
		global $id_empr ;
		$dsi_bannette_form = str_replace('!!id_empr!!', $id_empr, $dsi_bannette_form);
	
		$dsi_bannette_form = str_replace('!!comment_gestion!!', htmlentities($this->comment_gestion,ENT_QUOTES, $charset), $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!comment_public!!', htmlentities($this->comment_public,ENT_QUOTES, $charset), $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!bannette_tpl_list!!', $bannette_tpl_list, $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!entete_mail!!', htmlentities($this->entete_mail,ENT_QUOTES, $charset), $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!piedpage_mail!!', htmlentities($this->piedpage_mail,ENT_QUOTES, $charset), $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!date_last_remplissage!!', htmlentities($this->aff_date_last_remplissage,ENT_QUOTES, $charset), $dsi_bannette_form);
	
		$date_clic   = "onClick=\"openPopUp('./select.php?what=calendrier&caller=saisie_bannette&date_caller=".substr(preg_replace('/-/', '', $this->date_last_envoi),0,8)."&param1=form_date_last_envoi&param2=form_aff_date_last_envoi&auto_submit=NO&date_anterieure=YES', 'calendar')\"  ";
		$date_last_envoi = "
					<input type='hidden' name='form_date_last_envoi' value='".str_replace(' ', '', str_replace('-', '', str_replace(':', '', $this->date_last_envoi)))."' />
					<input class='bouton' type='button' name='form_aff_date_last_envoi' value='".$this->aff_date_last_envoi."' ".$date_clic." />";
			
		$dsi_bannette_form = str_replace('!!date_last_envoi!!', $date_last_envoi, $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!archive_number!!', $this->archive_number, $dsi_bannette_form);
		if ($type=="pro") $dsi_bannette_form = str_replace('!!proprio_bannette!!', htmlentities($msg['dsi_ban_no_proprio'],ENT_QUOTES, $charset), $dsi_bannette_form);
		else $dsi_bannette_form = str_replace('!!proprio_bannette!!', htmlentities($nom_prenom_abo,ENT_QUOTES, $charset), $dsi_bannette_form);
	
		if ($this->bannette_auto) $dsi_bannette_form = str_replace('!!bannette_auto!!', "checked", $dsi_bannette_form);
		else $dsi_bannette_form = str_replace('!!bannette_auto!!', "", $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!periodicite!!', htmlentities($this->periodicite,ENT_QUOTES, $charset), $dsi_bannette_form);
		if ($this->diffusion_email) $dsi_bannette_form = str_replace('!!diffusion_email!!', "checked='checked'", $dsi_bannette_form);
		else $dsi_bannette_form = str_replace('!!diffusion_email!!', "", $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!nb_notices_diff!!', htmlentities($this->nb_notices_diff,ENT_QUOTES, $charset), $dsi_bannette_form);
	
		$dsi_bannette_form = str_replace('!!notice_tpl!!', notice_tpl_gen::gen_tpl_select("notice_tpl",$this->notice_tpl), $dsi_bannette_form);
	
		if ($this->statut_not_account) $dsi_bannette_form = str_replace('!!statut_not_account!!', "checked", $dsi_bannette_form);
		else $dsi_bannette_form = str_replace('!!statut_not_account!!', "", $dsi_bannette_form);
		// group_type, group_pperso, group_facettes
		if($this->group_type){
			$dsi_bannette_form = str_replace('!!checked_group_facette!!', " checked='checked' ", $dsi_bannette_form);
			$dsi_bannette_form = str_replace('!!checked_group_pperso!!', "", $dsi_bannette_form);
		}else{
			$dsi_bannette_form = str_replace('!!checked_group_facette!!', "", $dsi_bannette_form);
			$dsi_bannette_form = str_replace('!!checked_group_pperso!!', " checked='checked' ", $dsi_bannette_form);
		}
		$dsi_bannette_form = str_replace('!!bannette_aff_notice_number!!', ($this->bannette_aff_notice_number ? " checked='checked' " : ""), $dsi_bannette_form);
	
		$liste_p_perso = $this->p_perso->gen_liste_field("group_pperso",$this->group_pperso,$msg["dsi_ban_form_regroupe_pperso_no"]);
		$dsi_bannette_form = str_replace('!!pperso_group!!', $liste_p_perso, $dsi_bannette_form);
			
		if ($this->id_bannette_origine) { //On vient d'une duplication
			$origine_bannette = new bannette($this->id_bannette_origine) ;
			$dsi_bannette_form = str_replace('!!facette_group!!', $origine_bannette->gen_facette_selection(), $dsi_bannette_form);
		} else {
			$dsi_bannette_form = str_replace('!!facette_group!!', $this->gen_facette_selection(), $dsi_bannette_form);
		}
		$dsi_bannette_form = str_replace("!!display_notice_in_every_group!!", ($this->display_notice_in_every_group ? "checked='checked'" : ""), $dsi_bannette_form);
	
		if ($type=="pro") {
			$requete = 'SELECT id_categ_empr, libelle, IF(empr_categ_num_categ IS NULL, 0,1) as inscription FROM empr_categ
						left join bannette_empr_categs on (id_categ_empr=empr_categ_num_categ and empr_categ_num_bannette=' . $this->id_bannette . ' )
						ORDER BY libelle';
			$categ_lect_aff = gen_liste_multiple ($requete, "id_categ_empr", "libelle", "inscription", "categorie_lecteurs[]", '', 1, 0, $msg['dsi_ban_aucune_categ'], -1,$msg['dsi_all_empr_categ'], 5) ;
			$dsi_bannette_form = str_replace('!!categorie_lecteurs!!', $categ_lect_aff, $dsi_bannette_form);
				
			$requete = 'SELECT id_groupe, libelle_groupe, IF(empr_groupe_num_groupe IS NULL, 0,1) as inscription FROM groupe
						left join bannette_empr_groupes on (id_groupe=empr_groupe_num_groupe and empr_groupe_num_bannette=' . $this->id_bannette . ' )
						ORDER BY libelle_groupe';
			$groupe_lect_aff = gen_liste_multiple ($requete, "id_groupe", "libelle_groupe", "inscription", "groupe_lecteurs[]", '', 1, 0, $msg['empr_form_aucungroupe'], 0,'', 5) ;
			$dsi_bannette_form = str_replace('!!groupe_lecteurs!!', $groupe_lect_aff, $dsi_bannette_form);
		} else {
			$dsi_bannette_form = str_replace('!!categorie_lecteurs!!', "<input type=hidden name=categorie_lecteurs[] value=0 />", $dsi_bannette_form);
			$dsi_bannette_form = str_replace('!!groupe_lecteurs!!', "<input type=hidden name=groupe_lecteurs[] value=0 />", $dsi_bannette_form);
		}
	
		$dsi_bannette_form = str_replace('!!desc_fields!!', $this->bannette_descriptors->get_form(), $dsi_bannette_form);
	
		$requete = "SELECT idcaddie, name FROM caddie where type='NOTI' ";
		if ($PMBuserid!=1) $requete.=" and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
		$requete.=" ORDER BY name ";
		$panier_bann_aff = gen_liste ($requete, "idcaddie", "name", "num_panier", "", $this->num_panier, 0, $msg['dsi_panier_aucun'], 0,$msg['dsi_panier_aucun'], 0) ;
		$dsi_bannette_form = str_replace('!!num_panier!!', $panier_bann_aff, $dsi_bannette_form);
	
		$limite_type = "<select name='limite_type' id='limite_type'>
						<option value='' ".(!$this->limite_type ? "selected='selected'" : "").">".$msg['dsi_ban_non_cumul']."</option>
						<option value='D' ".($this->limite_type == 'D' ? "selected='selected'" : "").">".$msg['dsi_ban_cumul_jours']."</option>
						<option value='I' ".($this->limite_type == 'I' ? "selected='selected'" : "").">".$msg['dsi_ban_cumul_notice']."</option>
						</select>";
		$dsi_bannette_form = str_replace('!!limite_type!!', $limite_type, $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!limite_nombre!!', $this->limite_nombre, $dsi_bannette_form);
	
		// update_type: se baser sur la date de création ou la date de mise à jour des notices ?
		$update_type = "<select name='update_type' id='update_type'>
						<option value='C' ".((!$this->update_type || $this->update_type == 'C') ? "selected='selected'" : "").">".$msg['dsi_ban_update_type_c']."</option>
						<option value='U' ".($this->update_type == 'U' ? "selected='selected'" : "").">".$msg['dsi_ban_update_type_u']."</option>
						</select>";
		$dsi_bannette_form = str_replace('!!update_type!!', $update_type, $dsi_bannette_form);
	
		$exp = start_export::get_exports();
		$liste_exports = "<select name='typeexport' onchange=\"if(this.selectedIndex==0) document.getElementById('liste_parametre').style.display='none'; else document.getElementById('liste_parametre').style.display=''; \">" ;
		if (!$this->typeexport) $liste_exports .= "<option value='' selected>".$msg['dsi_ban_noexport']."</option>";
		else $liste_exports .= "<option value=''>".$msg['dsi_ban_noexport']."</option>";
		for ($i=0;$i<count($exp);$i++) {
			if ($this->typeexport==$exp[$i]["PATH"]) $liste_exports .= "<option value='".$exp[$i]["PATH"]."' selected>".$exp[$i]["NAME"]."</option>";
			else $liste_exports .= "<option value='".$exp[$i]["PATH"]."' >".$exp[$i]["NAME"]."</option>";
		}
		$liste_exports .= "</select>" ;
		$dsi_bannette_form = str_replace('!!typeexport!!', $liste_exports,  $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!prefixe_fichier!!', $this->prefixe_fichier,  $dsi_bannette_form);
	
		$dsi_bannette_form = str_replace('!!bannette_opac_accueil_check!!', ($this->bannette_opac_accueil ? "checked='checked'" : ""), $dsi_bannette_form);
	
		$dsi_bannette_form = str_replace('!!document_generate!!', ($this->document_generate ? "checked='checked'" : ""), $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!document_notice_tpl!!', notice_tpl_gen::gen_tpl_select("document_notice_tpl",$this->document_notice_tpl), $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!document_insert_docnum!!', ($this->document_insert_docnum ? "checked='checked'" : ""), $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!document_group!!', ($this->document_group ? "checked='checked'" : ""), $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!document_add_summary!!', ($this->document_add_summary ? "checked='checked'" : ""), $dsi_bannette_form);
	
		$dsi_bannette_form = str_replace('!!delete!!', $button_delete,  $dsi_bannette_form);
	
		// afin de revenir où on était : $form_cb, le critère de recherche
		$dsi_bannette_form = str_replace('!!form_cb!!', $form_cb,  $dsi_bannette_form);
		if($this->param_export) {
			$param=new export_param(EXP_DSI_CONTEXT, $this->param_export);
		} else {
			$param=new export_param(EXP_DEFAULT_GESTION);
		}
		$dsi_bannette_form = str_replace('!!display_liste_param!!', (!$this->typeexport ? 'display:none' : ''),  $dsi_bannette_form);
		$dsi_bannette_form = str_replace('!!form_param!!', $param->check_default_param(),  $dsi_bannette_form);
	
		//ajout champs emprunteur
		$dsi_bannette_form = str_replace('!!info_empr!!', $dsi_bannette_form_selvars,  $dsi_bannette_form);
	
		print $dsi_bannette_form;
	}
	
	public function set_properties_from_form() {
		global $num_classement, $nom_bannette, $comment_gestion, $comment_public;
		global $entete_mail, $piedpage_mail, $notice_tpl;
		global $id_empr;
		global $bannette_auto, $periodicite, $diffusion_email, $statut_not_account, $dsi_private_bannette_nb_notices;
		global $categorie_lecteurs, $groupe_lecteurs;
		global $update_type, $date_last_envoi, $num_panier;
		global $limite_type, $limite_nombre, $typeexport, $prefixe_fichier;
		global $group_pperso, $display_notice_in_every_group, $archive_number, $group_type;
		global $document_generate, $document_notice_tpl, $document_insert_docnum, $document_group, $document_add_summary;
		global $bannette_opac_accueil,$bannette_tpl_num, $dsi_private_bannette_tpl;
		global $genere_lien, $mere, $fille, $notice_mere, $notice_fille, $art_link, $bull_link, $perio_link, $bulletinage, $notice_art, $notice_perio;
		global $empr_nom, $empr_prenom;
		global $dsi_private_bannette_notices_template;
		global $bannette_diffuse_checked;
		global $opac_private_bannette_date_used_to_calc;
		
		$this->num_classement 		= $num_classement+0;
		if($this->id_bannette) {
			$this->nom_bannette 		= stripslashes($nom_bannette);
			$this->comment_gestion 		= stripslashes($nom_bannette);
		} else {
			$this->nom_bannette 		= $empr_nom." ".$empr_prenom.' > '.stripslashes($nom_bannette);
			$this->comment_gestion 		= $empr_nom." ".$empr_prenom.' > '.stripslashes($nom_bannette);
			if(trim($dsi_private_bannette_notices_template)){
				$this->notice_tpl 		= trim($dsi_private_bannette_notices_template)+0;
			}
		}
		$this->comment_public		= stripslashes($nom_bannette);
		$this->get_entete_mail();
		$this->proprio_bannette		= $id_empr+0;
		$this->bannette_auto		= 1;
		$this->periodicite			= (!$periodicite || $periodicite>200 ? 15 : $periodicite*1);
		$this->diffusion_email		= 1;
// 		$this->statut_not_account 	= $statut_not_account+0;
		$this->nb_notices_diff		= $dsi_private_bannette_nb_notices+0;
		$this->categorie_lecteurs	= array();
		$this->groupe_lecteurs		= array();
		
		switch ($opac_private_bannette_date_used_to_calc) {
		    case 0 :
		        $this->update_type	= "C";
		        break;
		    case 1 :
		        $this->update_type	= "U";
		        break;
		    default :
		        $this->update_type	= $update_type;
		        break;
		}
		if(isset($bannette_diffuse_checked) && $bannette_diffuse_checked) {
			$this->date_last_envoi = date('Y-m-d H:i:s',strtotime("-".$this->periodicite." day",mktime(0,0,0,date('m'),date('d'),date('Y'))));
			$this->date_last_remplissage = $this->date_last_envoi;
		} else {
			if($date_last_envoi) {
				$this->date_last_envoi		= $date_last_envoi;
				$this->date_last_remplissage = $date_last_envoi;
			} elseif(!$this->id_bannette) {
				$this->date_last_envoi = date('Y-m-d H:i:s',strtotime("-".$this->periodicite." day",mktime(0,0,0,date('m'),date('d'),date('Y'))));
				$this->date_last_remplissage = $this->date_last_envoi;
			}
		}
		$this->num_panier			= $num_panier+0;
// 		$this->limite_type 			= stripslashes($limite_type);
// 		$this->limite_nombre		= $limite_nombre+0;
		$this->typeexport 			= stripslashes($typeexport);
		$this->prefixe_fichier 		= stripslashes($nom_bannette);
		$this->group_pperso 		= stripslashes($group_pperso);
		$this->display_notice_in_every_group = $display_notice_in_every_group+0;
		$this->archive_number		= $archive_number+0;
		$this->group_type 			= $group_type+0;
		$this->document_generate 	= $document_generate+0;
		$this->document_notice_tpl 	= $document_notice_tpl+0;
		$this->document_insert_docnum = $document_insert_docnum+0;
		$this->document_group 		= $document_group+0;
		$this->document_add_summary = $document_add_summary+0;
		$this->bannette_opac_accueil= $bannette_opac_accueil+0;
		if($bannette_tpl_num) {
			$this->bannette_tpl_num 	= $bannette_tpl_num+0;
		} else {
			$this->bannette_tpl_num 	= $dsi_private_bannette_tpl;
		}
		if(!$this->id_bannette) {
			$this->param_export=array("genere_lien" => $genere_lien+0,
					"mere" => $mere+0,
					"fille" => $fille+0,
					"notice_mere" => $notice_mere+0,
					"notice_fille" => $notice_fille+0,
					"art_link" => $art_link+0,
					"bull_link" => $bull_link+0,
					"perio_link" => $perio_link+0,
					"bulletinage" => $bulletinage+0,
					"notice_art" => $notice_art+0,
					"notice_perio" => $notice_perio+0);
		}
	}
	
	// ---------------------------------------------------------------
	//		save
	// ---------------------------------------------------------------
	public function save() {
		global $msg;
	
		if ($this->id_bannette) {
			// update
			$req = "UPDATE bannettes set ";
			$clause = " WHERE id_bannette='".$this->id_bannette."'";
		} else {
			$req = "insert into bannettes set ";
			$clause = "";
		}
		$req.="num_classement='".$this->num_classement."',";
		$req.="nom_bannette='".addslashes($this->nom_bannette)."',";
		$req.="comment_gestion='".addslashes($this->comment_gestion)."',";
		$req.="comment_public='".addslashes($this->comment_public)."',";
		$req.="bannette_tpl_num='".$this->bannette_tpl_num."',";
		$req.="entete_mail='".addslashes($this->entete_mail)."',";
		$req.="piedpage_mail='".addslashes($this->piedpage_mail)."',";
		$req.="notice_tpl='".$this->notice_tpl."',";
		$req.="proprio_bannette='".$this->proprio_bannette."',";
		$req.="bannette_auto='".$this->bannette_auto."',";
		$req.="periodicite='".$this->periodicite."',";
		$req.="diffusion_email='".$this->diffusion_email."',";
		$req.="statut_not_account='".$this->statut_not_account."',";
		$req.="nb_notices_diff='".$this->nb_notices_diff."',";
		$req.="update_type='".$this->update_type."',";
		$req.="num_panier='".$this->num_panier."',";
		$req.="limite_type='".$this->limite_type."',";
		$req.="limite_nombre='".$this->limite_nombre."',";
		$req.="typeexport='".$this->typeexport."',";
		$req.="prefixe_fichier='".$this->prefixe_fichier."',";
		$req.="group_type='".$this->group_type."',";
		$req.="group_pperso='".$this->group_pperso."',";
		$req.="display_notice_in_every_group='".$this->display_notice_in_every_group."',";
		$req.="archive_number='".$this->archive_number."',";
		$req.="param_export='".addslashes(serialize($this->param_export))."',";
		$req.="document_generate='".$this->document_generate."',";
		$req.="document_notice_tpl='".$this->document_notice_tpl."',";
		$req.="document_insert_docnum='".$this->document_insert_docnum."',";
		$req.="document_group='".$this->document_group."',";
		$req.="document_add_summary='".$this->document_add_summary."',";
		$req.="bannette_opac_accueil='".$this->bannette_opac_accueil."',";
		$req.="bannette_aff_notice_number='".$this->bannette_aff_notice_number."',";
		if (!$this->date_last_envoi) $req.="date_last_envoi=sysdate(), ";
		else $req.="date_last_envoi='".$this->date_last_envoi."', ";
		if (!$this->date_last_remplissage) $req.="date_last_remplissage=sysdate() ";
		else $req.="date_last_remplissage='".$this->date_last_remplissage."' ";
		$req.=$clause ;
		$res = pmb_mysql_query($req);
		if (!$this->id_bannette) $this->id_bannette = pmb_mysql_insert_id() ;
	
		$del = "delete from bannette_empr_groupes where empr_groupe_num_bannette = '".$this->id_bannette."'";
		pmb_mysql_query($del);
		if(count($this->groupe_lecteurs))
			for($i=0 ; $i<count($this->groupe_lecteurs) ; $i++){
				$id_groupe=$this->groupe_lecteurs[$i]*1;
				$rqt = "insert into bannette_empr_groupes set empr_groupe_num_bannette = '".$this->id_bannette."', empr_groupe_num_groupe = '".$id_groupe."' ";
				pmb_mysql_query($rqt);
		}
	
		$del = "delete from bannette_empr_categs where empr_categ_num_bannette = '".$this->id_bannette."'";
		pmb_mysql_query($del);
		if(count($this->categorie_lecteurs)){
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
				$id_categ=$this->categorie_lecteurs[$i]*1;
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
		global $msg;
	
		$requete = "delete from bannette_abon WHERE num_bannette='$this->id_bannette'";
		pmb_mysql_query($requete);
		$requete = "delete from bannette_contenu WHERE num_bannette='$this->id_bannette'";
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
		$requete = "select num_notice from bannette_contenu, notices WHERE num_bannette='".$this->id_bannette."' and notice_id=num_notice order by date_ajout DESC, update_date DESC ";
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
		global $msg;
		global $gestion_acces_active,$gestion_acces_empr_notice;
	
		//purge pour les bannettes privees des notices ne devant pas etre diffusees
		if ($this->proprio_bannette && $gestion_acces_active==1 && $gestion_acces_empr_notice==1){
			$ac = new acces();
			$dom_2 = $ac->setDomain(2);
			$acces_j = $dom_2->getJoin($this->proprio_bannette,'4=0','num_notice');
				
			$q="delete from bannette_contenu using bannette_contenu $acces_j WHERE num_bannette='$this->id_bannette' ";
			pmb_mysql_query($q);
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
				$requete = "delete from caddie_content WHERE caddie_id='$this->num_panier' and object_id not in (".$notice_suppr.") ";
				$res = pmb_mysql_query($requete);
			}
			$requete = "delete from bannette_contenu WHERE num_bannette='$this->id_bannette' and num_notice not in (".$notice_suppr.") ";
			$res = pmb_mysql_query($requete);
		}
		$this->compte_elements() ;
	}
	
	// ---------------------------------------------------------------
	//		vider() : vider le contenu de la bannette 
	// ---------------------------------------------------------------
	public function vider() {
		$requete = "delete from bannette_contenu WHERE num_bannette='".$this->id_bannette."'";
		pmb_mysql_query($requete);
		$requete = "delete from caddie_content WHERE caddie_id='".$this->num_panier."'";
		pmb_mysql_query($requete);
	
		$this->compte_elements() ;
	}

	// ---------------------------------------------------------------
	//		remplir() : remplir la bannette à partir des équations 
	// ---------------------------------------------------------------
	public function remplir() {
		global $msg;
		global $gestion_acces_active,$gestion_acces_empr_notice;
		
		// récupérer les équations associées à la bannette
		$equations = $this->bannette_equations->get_equations() ;
		$res_affichage = "<ul>" ;
		if ($this->update_type=="C") $colonne_update_create="create_date";
			else $colonne_update_create="update_date";
		for ($i=0 ; $i < sizeof($equations) ; $i++) {
			// pour chaque équation ajouter les notices trouvées au contenu de la bannette
			$equ = new equation ($equations[$i]) ;
			$search = new search() ;
			$search->unserialize_search($equ->requete) ;
			$table = $search->make_search() ;
			if($search->is_created_temporary_table($table)) {
				$temp_requete = "insert into bannette_contenu (num_bannette, num_notice) (select ".$this->id_bannette." , notices.notice_id from $table , notices, notice_statut where notices.$colonne_update_create>='".$this->date_last_envoi."' and $table.notice_id=notices.notice_id and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0) or (notice_visible_opac_abon=1 and notice_visible_opac=1)) limit 300) " ;
				@pmb_mysql_query($temp_requete);
				$temp_requete = "drop table $table " ;
				@pmb_mysql_query($temp_requete);
			}
			$res_affichage .= "<li>".$equ->human_query."</li>" ;
		}
		$res_affichage .= "</ul>" ;
		$this->compte_elements() ;
		$temp_requete = "update bannettes set date_last_remplissage=sysdate() where id_bannette='".$this->id_bannette."' " ;
		$res = @pmb_mysql_query($temp_requete);
	
		//purge pour les bannettes privees des notices ne devant pas etre diffusees 
		if ($this->proprio_bannette && $gestion_acces_active==1 && $gestion_acces_empr_notice==1){
			$ac = new acces();
			$dom_2 = $ac->setDomain(2);
			$acces_j = $dom_2->getJoin($this->proprio_bannette,'4=0','num_notice');
			
			$q="delete from bannette_contenu using bannette_contenu $acces_j WHERE num_bannette='$this->id_bannette' ";
			pmb_mysql_query($q);
		}
	
		return $res_affichage ;
	}

	protected function get_css($directory) {
		global $base_path;
		global $opac_url_base;
	
		$css = '';
		$css_path= $base_path."/styles/".$directory."/dsi";
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
		if (!$dsi_bannette_notices_order) $dsi_bannette_notices_order="index_serie, tnvol, index_sew";
		$limitation = '';
		if ($this->nb_notices_diff && $this->use_limit) $limitation = " LIMIT $this->nb_notices_diff " ;
		$query = "select num_notice from bannette_contenu, notices where num_bannette='".$this->id_bannette."' and notice_id=num_notice order by $dsi_bannette_notices_order $limitation ";
		return $query;
	}
	
	protected function get_data_structure($notice_template_id=0, $grouped=false, $add_summary=true) {
		$data = array();
	
		//Nb total sans limitation
		$data['records']['length_total'] = $this->nb_notices;
	
		$result = pmb_mysql_query($this->get_query_records());
		$data['records']['length'] = pmb_mysql_num_rows($result);
		if(pmb_mysql_num_rows($result)) {
			while (($row = pmb_mysql_fetch_object($result))) {
				$bannette_facette = $this->get_instance_bannette_facette();
				if($notice_template_id){
					$bannette_facette->noti_tpl_document = notice_tpl_gen::get_instance($notice_template_id);
				}
				$data['records'][$row->num_notice]['render'] = $bannette_facette->build_notice($row->num_notice, $this->id_bannette);
			}
		}
		if($add_summary) {
			$data['sommaires'] = $this->get_data_summary_structure($notice_template_id, $grouped);
		} else {
			$data['sommaires'] = array();
		}
		return $data;
	}
	
	public function get_display_bannette_tpl($notice_template_id=0, $grouped=false, $add_summary=true) {
		$data=$this->get_data_structure($notice_template_id, $grouped, $add_summary);	//$this->data_document
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
		global $suite;
	
		$this->set_records_globals();
		$this->build_lists(0);
		$document = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" />".$this->get_css_style()."</head><body>";
		if($this->get_private_bannette_tpl()){
			$document .= $this->get_display_bannette_tpl($this->document_notice_tpl, $this->document_group,$this->document_add_summary);
		} else {
			$document .= $this->get_display_header();
			if (count($this->list) && $this->group_type==1) {
				$facette = $this->get_instance_bannette_facette();
				$document .= $facette->build_document($this->list,$this->document_notice_tpl,$this->document_add_summary,1);
			} else {
				$this->add_list_group_in_list();
				if($this->document_group){
					$document .= $this->get_display_summary($this->document_notice_tpl);
				} else {
					if($this->list) {
						$document .= $this->get_display_template($this->document_notice_tpl, $this->document_group);
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
				$display .= $this->get_display_summary($this->notice_tpl);
			} else {
				if ($this->list) {
					// DSI classique par mail...
					$display .= $this->get_display_template($this->notice_tpl);
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
		$this->build_lists(1);
		$display = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" />".$this->get_css_style()."</head><body>";
		if($this->bannette_aff_notice_number){
			$display .= "<span class=\"dsi_hide_for_emails\"><hr />!!dsi_diff_n_notices!!</span>";
		}
		if($this->get_private_bannette_tpl()){
			if ($this->diffusion_email) {
				$display .= $this->get_display_bannette_tpl($this->notice_tpl, $this->group_pperso);
			}
		} else {
			$display .= $this->get_display_header();
			if ($this->diffusion_email) {
				$display .= $this->get_display_content();
			}
			$display .= $this->get_display_footer();
		}
		$display .= "</body></html>";
		return $display;
	}
	
	protected function get_formatted_text($text, $empr) {
		global $opac_url_base;
		global $opac_connexion_phrase;
		
		$lang_messages = static::get_lang_messages($empr->empr_lang);
		$formatted_text = $text;
		$formatted_text = str_replace ("!!nb_notice!!", $this->nb_notices, $formatted_text);
		$formatted_text = str_replace("!!print_n_notices!!", sprintf($lang_messages["print_n_notices"],$this->nb_notices), $formatted_text);
		if (($this->nb_notices_diff >= $this->nb_notices) || (!$this->nb_notices_diff)) {
			$nb_envoyees = $this->nb_notices ;
		} else {
			$nb_envoyees = $this->nb_notices_diff;
		}
		$msg_dsi_diff_n_notices = sprintf($lang_messages["dsi_diff_n_notices"],$nb_envoyees,$this->nb_notices);
		$formatted_text = str_replace("!!dsi_diff_n_notices!!", ($msg_dsi_diff_n_notices ? $msg_dsi_diff_n_notices.'<hr />' : ''), $formatted_text);
			
		$dates = time();
		$code=md5($opac_connexion_phrase.$empr->empr_login.$dates);
		$formatted_text = str_replace('!!empr_connect!!',"<a href='".$opac_url_base."empr.php".static::get_url_connexion_auto('?')."'>".$lang_messages["selvars_empr_auth_opac"]."</a>",$formatted_text);
		$formatted_text = str_replace('!!code!!',$code,$formatted_text);
		$formatted_text = str_replace('!!login!!',$empr->empr_login,$formatted_text);
	
		$formatted_text = str_replace('!!date_conex!!',$dates,$formatted_text);
	
		$formatted_text = str_replace('!!empr_name!!',$empr->empr_nom,$formatted_text);
		$formatted_text = str_replace('!!empr_first_name!!',$empr->empr_prenom,$formatted_text);
		$formatted_text = str_replace('!!empr_sexe!!',$empr->empr_sexe,$formatted_text);
		$formatted_text = str_replace('!!empr_cb!!',$empr->empr_cb,$formatted_text);
		$formatted_text = str_replace('!!empr_login!!',$empr->empr_login,$formatted_text);
		$formatted_text = str_replace('!!empr_mail!!',$empr->empr_mail,$formatted_text);
		if (strpos($formatted_text,"!!empr_name_and_adress!!")) {
			$formatted_text=str_replace("!!empr_name_and_adress!!", nl2br($this->m_lecteur_adresse($empr)),$formatted_text);
		}
		if (strpos($formatted_text,"!!empr_all_information!!")) {
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
		} else {
			$formatted_text = str_replace('!!loc_name!!','',$formatted_text);
			$formatted_text = str_replace('!!loc_adr1!!','',$formatted_text);
			$formatted_text = str_replace('!!loc_adr2!!','',$formatted_text);
			$formatted_text = str_replace('!!loc_cp!!','',$formatted_text);
			$formatted_text = str_replace('!!loc_town!!','',$formatted_text);
			$formatted_text = str_replace('!!loc_phone!!','',$formatted_text);
			$formatted_text = str_replace('!!loc_email!!','',$formatted_text);
			$formatted_text = str_replace('!!loc_website!!','',$formatted_text);
		}
		return $formatted_text;
	}
	
	protected function get_formatted_mail($empr) {
		$text = $this->get_display_mail();
		$formatted_mail = $this->get_formatted_text($text, $empr);
		return $formatted_mail;
	}
	
	public function get_display_export() {
		global $charset;
	
		$this->set_records_globals();
		$this->build_lists(0);
		$display = "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" />".$this->get_css_style()."</head><body>";
		if($this->get_private_bannette_tpl()){
			$display .= $this->get_display_bannette_tpl($this->notice_tpl, $this->group_pperso);
		} else {
			$display .= $this->get_display_header().$this->get_display_content().$this->get_display_footer();
		}
		$display .= "</body></html>";
		return $display;
	}
	
	protected function get_record_display($idnotice) {
		global $opac_notice_affichage_class;
		global $liens_opac;
		global $opac_bannette_notices_format;
		global $opac_bannette_notices_depliables;
		global $opac_notices_format_django_directory;
		global $record_css_already_included; // Pour pas inclure la css 10 fois
		global $include_path;
	
		$display = '';
		$notice = new $opac_notice_affichage_class($idnotice, $liens_opac, 1) ;
		// si notice visible
		if ($notice->visu_notice) {
			$notice->do_header();
			switch ($opac_bannette_notices_format) {
				case AFF_BAN_NOTICES_REDUIT :
					$display .= "<div class='etagere-titre-reduit'>".$notice->notice_header_with_link."</div>" ;
					break;
				case AFF_BAN_NOTICES_ISBD :
					$notice->do_isbd();
					$notice->genere_simple($opac_bannette_notices_depliables, 'ISBD') ;
					$display .= $notice->result ;
					break;
				case AFF_BAN_NOTICES_PUBLIC :
					$notice->do_public();
					$notice->genere_simple($opac_bannette_notices_depliables, 'PUBLIC') ;
					$display .= $notice->result ;
					break;
				case AFF_BAN_NOTICES_BOTH :
					$notice->do_isbd();
					$notice->do_public();
					$notice->genere_double($opac_bannette_notices_depliables, 'PUBLIC') ;
					$display .= $notice->result ;
					break ;
				case AFF_BAN_NOTICES_TEMPLATE_DJANGO :
					if (!$opac_notices_format_django_directory) $opac_notices_format_django_directory = "common";
					if (!$record_css_already_included) {
						if (file_exists($include_path."/templates/record/".$opac_notices_format_django_directory."/styles/style.css")) {
							$display .= "<link type='text/css' href='./includes/templates/record/".$opac_notices_format_django_directory."/styles/style.css' rel='stylesheet'></link>";
						}
						$record_css_already_included = true;
					}
					$display .= record_display::get_display_in_result($idnotice);
					break;
				default:
					$notice->do_isbd();
					$notice->do_public();
					$notice->genere_double($opac_bannette_notices_depliables, 'autre') ;
					$display .= $notice->result ;
					break ;
			}
		}
		return $display;
	}
	
	public function get_display($aff_notices_nb=0, $link_to_bannette="",$home=false ) {
		global $msg,$charset;
		global $date_diff;
		global $affiche_bannette_tpl;
		global $id_empr;
		global $opac_websubscribe_show;
	
		$diffusion = "";
		$diffusion .= "\n<div class='bannette-titre'><h1>";
		$diffusion .= "<a href='cart_info.php?lvl=dsi&id=".$this->id_bannette."' target='cart_info' title=\"".$msg['notice_title_basket']."\"><img src='".get_url_icon("basket_small_20x20.png")."' style='border:0px' alt=\"".$msg['notice_title_basket']."\"></a>";
		if ($link_to_bannette) {
			$diffusion .= "<a href=\"".str_replace("!!id_bannette!!",$this->id_bannette, $link_to_bannette)."\">";
		}
		if($date_diff){
			$diffusion .= htmlentities($this->comment_public." - ".formatdate($date_diff),ENT_QUOTES, $charset);
		}else{
			$diffusion .= htmlentities($this->comment_public." - ".$this->aff_date_last_envoi,ENT_QUOTES, $charset);
		}
		if ($link_to_bannette) $diffusion .="</a>";
	
		//TODO
		if($home && $_SESSION['user_code']){
			$diffusion .= "
			<form name='bannette_subscription' method='post' action='./empr.php?tab=dsi&lvl=bannette_gerer'>
				<input type='hidden' name='enregistrer' value='PUB'/>
				<input type='hidden' name='lvl' value='bannette_gerer'/>
				<input type='hidden' name='bannette_abon[".$this->id_bannette."]' value='1' />";
			$query = "select num_bannette from bannette_abon where num_empr = ".$id_empr;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$diffusion .= "
						<input type='hidden' name='bannette_abon[".$row->num_bannette."]' value='1' />";
				}
			}
			$diffusion .= "
				<input type='submit' class='bouton' value=\"".$msg['bannette_abonn']."\"/>
			</form>";
		}else if ($home && $opac_websubscribe_show ==2){
			$diffusion .= "
			<form name='bannette_subscription' method='post' action='./do_resa.php'>
				<input type='hidden' name='enregistrer' value='PUB'/>
				<input type='hidden' name='lvl' value='bannette_gerer'/>
				<input type='hidden' name='new_connexion' value='1' />
				<input type='hidden' name='tab' value='dsi'/>
				<input type='hidden' name='bannette_abon[".$this->id_bannette."]' value='1' />";
			$diffusion .= "
				<input type='submit' class='bouton' value=\"".$msg['bannette_abonn']."\"/>
			</form>";
		}
		$diffusion .= "</h1></div>";
	
		if($this->proprio_bannette) {
			$recherche = get_bannette_human_query($this->id_bannette);
			if ($recherche) {
				$diffusion .= "<a id=\"bannette_human_query_link".$this->id_bannette."\" onclick=\"javascript:document.getElementById('bannette_human_query".$this->id_bannette."').style.display='';document.getElementById('bannette_human_query_link".$this->id_bannette."').style.display='none';\">".$msg['bannette_human_query_show']."</a>";
				$diffusion .= "<div id='bannette_human_query".$this->id_bannette."' class='bannette_human_query' style='display:none'>";
				$diffusion .= $recherche;
				$diffusion .= "</div>";
			}
		} else {
			$diffusion .= "<hr />";
		}
	
		$notices = array();
		notices_bannette($this->id_bannette, $notices,$date_diff) ;
	
		if ($aff_notices_nb>0) $limite_notices = min($aff_notices_nb, count($notices)) ;
		elseif ($aff_notices_nb<0) $limite_notices = min($aff_notices_nb, count($notices)) ;
		else  $limite_notices = count($notices) ;
		reset ($notices) ;
		$limit=0;
		if ($limite_notices) $diffusion .= "<div id='etagere-notice-list_".$this->id_bannette."'>";
		foreach ($notices as $idnotice => $niveau_biblio) {
		    if ($limit<$limite_notices) {
		        $limit++;
		        $diffusion .= $this->get_record_display($idnotice);
		    }
		}
		if ($limite_notices&&($limite_notices<count($notices))){
			if ($link_to_bannette) $diffusion .= "<a href=\"".str_replace("!!id_bannette!!",$this->id_bannette,$link_to_bannette)."\"><span class='banette-suite'>".$msg['banette_suite']."</span></a>";
		}
		if ($limite_notices) $diffusion.= "</div>";
	
		$req="select distinct date_diff_arc from dsi_archive where num_banette_arc='".$this->id_bannette."' order by date_diff_arc desc";
		$res_arc=pmb_mysql_query($req);
		$first=0;
		$diff_list="";
		while (($r = pmb_mysql_fetch_object($res_arc))){
			if(!$first)$libelle=$msg['dsi_archive_last'];
			else $libelle=sprintf($msg['dsi_archive_other'], formatdate($r->date_diff_arc));
				
			if($pair_impair == 'even')$pair_impair='odd'; else $pair_impair='even';
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='$link_to_bannette&date_diff=".$r->date_diff_arc."';\" ";
			$diff= "<tr style='cursor: pointer' class='$pair_impair' $tr_javascript><td>".$libelle ."</td></tr>";
			$first=1;
				
			$diff_list.=$diff;
		}
		$display = $affiche_bannette_tpl;
		$display = str_replace ("!!historique!!", $diff_list, $display);
		$display = str_replace ("!!diffusion!!", $diffusion, $display) ;
		$display = str_replace("!!id_bannette!!", $this->id_bannette, $display);
		return $display;
	}
	
	// ---------------------------------------------------------------
	//		diffuser() : diffuser le contenu de la bannette  
	// ---------------------------------------------------------------
	public function diffuser() {
		global $msg, $charset, $base_path, $include_path, $opac_connexion_phrase, $opac_url_base;
		global $id_empr;

		$res_envoi = false;
		if($this->nb_notices > 0){
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
			
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=".$charset."\n";
			
			$requete = "select id_empr, empr_cb, empr_mail, empr_nom, empr_prenom, empr_login, empr_password, if(empr_sexe=2,'".$msg["civilite_madame"]."', ";
			$requete .= "if(empr_sexe=1,'".$msg["civilite_monsieur"]."','".$msg["civilite_unknown"]."')) as empr_sexe, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_lang, ";
			$requete .= "empr_pays, empr_tel1, empr_tel2, date_format(empr_date_adhesion, '".$msg["format_date"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_empr_date_expiration,";
			$requete .= "idstatut, statut_libelle, id_categ_empr, empr_categ.libelle as libelle_categ, idcode as id_codestat, empr_codestat.libelle as libelle_codestat, allow_dsi, allow_dsi_priv, proprio_bannette, bannette_mail, empr_location from empr, empr_statut, empr_categ, empr_codestat, bannette_abon, bannettes ";
			$requete .= "where num_bannette='".$this->id_bannette."' and num_empr=id_empr and empr_statut=idstatut and empr_categ=id_categ_empr and empr_codestat=idcode and num_bannette=id_bannette ";
			$requete .= "order by empr_nom, empr_prenom ";
			$res = pmb_mysql_query($requete);
			while ($empr=pmb_mysql_fetch_object($res)) {
				$code_langue = $empr->empr_lang;
				$langues = new XMLlist($include_path."/messages/languages.xml");
				$langues->analyser();
				$libelle_langue = $langues->table[$code_langue];
				$emaildest = $empr->empr_mail;
				if ($emaildest) {
					$docs_location = new docs_location($empr->empr_location);
					$res_envoi=mailpmb($empr->empr_prenom." ".$empr->empr_nom, $emaildest,$this->get_formatted_text($this->comment_public, $empr), $this->get_formatted_mail($empr), $docs_location->libelle, $docs_location->email, $headers, "", "", 0, $pieces_jointes);
				}
			}
			/* A commenter pour tests */ 
			$temp_requete = "update bannettes set date_last_envoi=sysdate() where id_bannette='".$this->id_bannette."' " ;
			$res = pmb_mysql_query($temp_requete);
		} 
		return $res_envoi ;
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
		$result = @pmb_mysql_query($requete);
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
	
		if(!sizeof($liste) || !is_array($liste)) {
			return $msg['dsi_ban_empty'];
		} else {
			// boucle de parcours des notices trouvées
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			$return_affichage .= $begin_result_liste;
			//Affichage du lien impression et panier
			
			foreach ($liste as $cle => $object) {
	
				// affichage de la liste des notices sous la forme 'expandable'
				$requete = "SELECT * FROM notices WHERE notice_id='".$object['num_notice']."' ";
				$fetch = pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($fetch)) {
					$notice = pmb_mysql_fetch_object($fetch);
					if($notice->niveau_biblio != 's' && $notice->niveau_biblio != 'a') {
						// notice de monographie
						$link = './catalog.php?categ=isbd&id=!!id!!';
						$link_expl = './catalog.php?categ=edit_expl&id=!!notice_id!!&cb=!!expl_cb!!&expl_id=!!expl_id!!';
						$link_explnum = './catalog.php?categ=edit_explnum&id=!!notice_id!!&explnum_id=!!explnum_id!!';
						if (!$no_del)
							$lien_suppr_cart = "<a href='$url_base&suite=suppr_notice&num_notice=$notice->notice_id&id_bannette=$this->id_bannette'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a> ";
							else
								$lien_suppr_cart = "" ;
								$display = new mono_display($notice, 6, $link, 1, $link_expl, $lien_suppr_cart, $link_explnum );
								$return_affichage .= $display->result;
					} else {
						// on a affaire à un périodique
						// préparation des liens pour lui
						$link_serial = './catalog.php?categ=serials&sub=view&serial_id=!!id!!';
						$link_analysis = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!bul_id!!&art_to_show=!!id!!';
						$link_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!';
						if (!$no_del)
							$lien_suppr_cart = "<a href='$url_base&suite=suppr_notice&num_notice=$notice->notice_id&id_bannette=$this->id_bannette'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a> ";
							else
								$lien_suppr_cart = "" ;
								$link_explnum = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
								$serial = new serial_display($notice, 6, $link_serial, $link_analysis, $link_bulletin, $lien_suppr_cart, $link_explnum, 0);
								$return_affichage .= $serial->result;
					}
				}
			} // fin de liste
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
		global $opac_notice_affichage_class;
		global $opac_url_base;
		global $opac_resa ;
		global $liens_opac;
		
		$url_base_opac = $opac_url_base."index.php?database=".DATA_BASE."&lvl=notice_display&id=";
		
		//classe notice _affichage
		if (!$opac_notice_affichage_class) $opac_notice_affichage_class="notice_affichage";
		
		$opac_resa = 0 ;
		$depliable = 0 ;
		$notice = new $opac_notice_affichage_class($n->notice_id, $liens_opac) ;
		$notice->do_header();
		$notice->do_isbd();
		$notice->genere_simple($depliable, 'ISBD') ;
		$isbd = "<a href='".$url_base_opac.$n->notice_id."'><b>".$notice->notice_header."</b></a><br /><br />\r\n";
		$isbd .= $notice->notice_isbd;
		return $isbd;
	}
	
	protected function get_export_contenu() {
		if(!isset($this->export_contenu) || !$this->export_contenu) {
			$this->export_contenu=cree_export_notices($this->liste_id_notice, start_export::get_id_by_path($this->typeexport)) ;
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
	
	protected function get_display_template($template_id=0) {
		$already_printed=array();
	
		$display_template = "";
		foreach($this->list as $num_notice) {
			$tpl_notice=$this->get_tpl_notice($template_id, $num_notice);
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
	
	protected function get_tpl_notice($template_id=0, $num_notice) {
		if($template_id) {
			$notice_tpl_gen = notice_tpl_gen::get_instance($template_id);
			$tpl_notice=$notice_tpl_gen->build_notice($num_notice, $this->get_location(), false, $this->id_bannette);
		} else {
			$tpl_notice="";
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
	
	protected function get_tri_tpl($template_id=0) {
		if(!isset($this->tri_tpl[$template_id])) {
			$this->tri_tpl[$template_id] = array();
			$already_printed=array();
			// Si un champ perso est donné comme critère de regroupement
			if($this->group_pperso && $this->group_type!=1) {
				foreach($this->list_group as $group) {
					foreach($group as $notice) {
						$num_notice = $notice->num_notice;
						$tpl_notice = $this->get_tpl_notice($template_id, $num_notice);
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
				$tpl_notice = $this->get_tpl_notice($template_id, $num_notice);
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
	
	protected function get_data_summary_structure($template_id=0, $grouped=false) {
		$data = array();
		$already_printed=array();
	
		if (count($this->list) && $this->group_type==1) {
			$facette = $this->get_instance_bannette_facette();
			$data = $facette->build_document_data($this->list,$template_id);
		} else {
			if($grouped) {
				$tri_tpl = $this->get_tri_tpl($template_id);
				$this->pmb_ksort($tri_tpl);
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
					$tpl_notice = $this->get_tpl_notice($template_id, $num_notice);
					if(!in_array($num_notice, $already_printed)){
						$already_printed[]=$num_notice;
						$data[0]['records'][]['render']=$tpl_notice;
					}
				}
			}
		}
		return $data;
	}
	
	protected function get_display_summary($template_id=0) {
		global $group_separator;
		global $notice_separator;
	
		$display="";
	
		$index=0;
		$summary="";
		$tri_tpl = $this->get_tri_tpl($template_id);
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
		if($this->document_add_summary){
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
					$this->p_perso->get_values($temp->num_notice);
					$values = $this->p_perso->values;
					$trouve = false;
					foreach ( $values as $field_id => $vals ) {
						if ($this->group_pperso==$field_id) {
	
							foreach($vals as $cpVal){
								$this->notice_group[$temp->num_notice][] = $this->p_perso->get_formatted_output(array($cpVal),$field_id);
								if (!$cpVal) {
									$cpVal = "_no_value_";
								}
								$this->list_group[$cpVal][] = $temp;
								$trouve = true;
							}
	
							$this->field_type = $this->p_perso->t_fields[$field_id]["TYPE"];
							$this->field_id = $field_id;
						}
					}
					if (!$trouve) {
						$this->list_group["_no_value_"][] = $temp;
						if ($field_id) {
							$this->notice_group[$temp->num_notice][] = $this->p_perso->get_formatted_output(array(),$field_id);
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
	
	// ---------------------------------------------------------------
	//		construit_liens_HTML()
	// ---------------------------------------------------------------
	public function construit_liens_HTML() {
		global $opac_url_base, $opac_connexion_phrase ;
		global $msg;
		global $charset ;
		global $dsi_connexion_auto;
		
		$url_base_opac = $opac_url_base."empr.php?lvl=bannette";
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
		
		$req = "select empr_login from empr where id_empr=$this->proprio_bannette";
		$res = pmb_mysql_query($req);
		$empr = pmb_mysql_fetch_object($res);
		$date_today = formatdate(today()) ;
		$date = time();
		$login = $empr->empr_login;
		$code=md5($opac_connexion_phrase.$login.$date);	
		$public  = "<a href='$url_base_opac&id_bannette=".$this->id_bannette.($dsi_connexion_auto ? "&code=".$code."&emprlogin=".$login."&date_conex=".$date : "")."'>";
		$public .= htmlentities($this->comment_public,ENT_QUOTES, $charset) ;
		$public .= "</a>";
	
		$entete = str_replace ("!!public!!",$public,$this->entete_mail) ;
		$entete = str_replace ("!!equation!!",$this->bannette_equations->get_text(),$entete) ;
		$entete = str_replace ("!!date!!",$date_today,$entete) ;
		
		return $entete ;
	}
	
	// ---------------------------------------------------------------
	//		compte_elements() : méthode pour pouvoir recompter en dehors !
	// ---------------------------------------------------------------
	public function compte_elements() {
		$req_nb = "SELECT num_notice from bannette_contenu WHERE num_bannette='".$this->id_bannette."' " ;
		$res_nb = pmb_mysql_query($req_nb);
		$this->nb_notices = pmb_mysql_num_rows($res_nb);
		//initialisation du tableau à chaque fois que cette fonction est appelée pour éviter un mauvais cumul
		$this->liste_id_notice = array();
		while ($res = pmb_mysql_fetch_object($res_nb)) {
			$this->liste_id_notice[]=$res->num_notice ;
		}
		
		$req_nb = "SELECT count(1) as nb_abonnes from bannette_abon WHERE num_bannette='".$this->id_bannette."' " ;
		$res_nb = pmb_mysql_query($req_nb);
		$res = pmb_mysql_fetch_object($res_nb);
		$this->nb_abonnes = $res->nb_abonnes ;
		$requete = "SELECT if(date_last_remplissage>date_last_envoi,1,0) as alert_diff ";
		$requete .= "FROM bannettes WHERE id_bannette='".$this->id_bannette."' " ;
		$result = pmb_mysql_query($requete);
		$temp = pmb_mysql_fetch_object($result);
		$this->alert_diff = $temp->alert_diff ; 
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
			$tel = $tel.$msg['fpdf_tel']." ".$empr->empr_tel1." " ;
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
			$tel = $tel.$msg['fpdf_tel']." ".$empr->empr_tel1." " ;
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
	
		return $dsi_private_bannette_tpl+0;
	}
	
	public function get_instance_bannette_facette() {
		global $use_dsi_diff_mode;
		if($this->statut_not_account) {
			$use_dsi_diff_mode=2;//On ne tient pas compte des statuts de notice pour la diffusion
		} else {
			$use_dsi_diff_mode=1;
		}
		return new bannette_facettes($this->id_bannette);
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
		$id_empr += 0;
		$query = "select count(*) as subscribed from bannette_abon where num_bannette = '".$this->id_bannette."' and num_empr='".$id_empr."' ";
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 'subscribed');
	}
	
	// ---------------------------------------------------------------
	//		construit_contenu_HTML() : Préparation du contenu du mail ou du bulletin
	// ---------------------------------------------------------------
	public function get_datas_content ($use_limit=1) {
		global $msg;
		global $opac_url_base, $use_opac_url_base ;
		global $opac_notice_affichage_class;
		global $liens_opac ;
		global $dsi_bannette_notices_order ;
	
		if ($this->nb_notices_diff && $use_limit) $limitation = " LIMIT $this->nb_notices_diff " ;
		$requete = "select num_notice from bannette_contenu, notices where num_bannette='".$this->id_bannette."' and notice_id=num_notice order by index_serie, tnvol, index_sew $limitation ";
		$resultat = pmb_mysql_query($requete);
	
		if($this->notice_tpl){
			$noti_tpl = notice_tpl_gen::get_instance($this->notice_tpl);
		}
		$liste=array();
		$liste_group=array();
		$notice_group=array();
		if(pmb_mysql_num_rows($resultat)) {
			while (($temp = pmb_mysql_fetch_object($resultat))) {
				// Si un champ perso est donné comme critère de regroupement
				if($this->group_pperso && $this->group_type!=1) {
					$this->p_perso->get_values($temp->num_notice);
					$values = $this->p_perso->values;
					$trouve = false;
					foreach ( $values as $field_id => $vals ) {
						if ($this->group_pperso==$field_id) {

							foreach($vals as $cpVal){
								$notice_group[$temp->num_notice][] = $this->p_perso->get_formatted_output(array($cpVal),$field_id);
								if (!$cpVal) {
									$cpVal = "_no_value_";
								}
								$liste_group[$cpVal][] = $temp;
								$trouve = true;
							}

							$this->field_type = $this->p_perso->t_fields[$field_id]["TYPE"];
							$this->field_id = $field_id;
						}
					}
					if (!$trouve) {
						$liste_group["_no_value_"][] = $temp;
						$notice_group[$temp->num_notice][] = $this->p_perso->get_formatted_output(array(),$field_id);
					}
				}
				else $liste[] = $temp ;
			}
		}
	
		// groupement par facettes
		if (count($liste) && $this->group_type==1) {
			$notice_ids=array();
			foreach($liste as $r) $notice_ids[]=$r->num_notice;
			$facette = new bannette_facettes($this->id_bannette);
			$this->data_document['sommaires']=$facette->build_document_data($notice_ids,$this->document_notice_tpl);
			return;
		}
		if(count($liste_group)) {
			foreach($liste_group as $list_notice) {
				$req_list=array();
				foreach($list_notice as $r) {
					$req_list[]=$r->num_notice;
				}
				$requete = "select notice_id as num_notice from  notices where  notice_id in(".implode(",",$req_list).") order by $dsi_bannette_notices_order ";
				$res_tri = pmb_mysql_query($requete) ;
				while (($r = pmb_mysql_fetch_object($res_tri))) {
					$liste[] = $r;
				}
			}
		}
		$tri_tpl=array();
		if ($liste) {
			$already_printed=array();
	
			foreach($liste as $r) {
					$tpl="";
					if($this->notice_tpl) {
						$tpl=$noti_tpl->build_notice($r->num_notice,$this->get_location());
					}
					if(!$tpl) {
						if (!$opac_notice_affichage_class) $opac_notice_affichage_class="notice_affichage";
						$current = new $opac_notice_affichage_class($r->num_notice, $liens_opac);
						$current->do_isbd();
						$tpl.=$current->notice_isbd;
					}
					if($this->group_pperso) {
						if($notice_group[$r->num_notice]) {
							foreach($notice_group[$r->num_notice] as $id=>$cpDisplay){
					
								if($this->display_notice_in_every_group){
									$already_printed=array();
								}
					
								if(!$tri_tpl[$notice_group[$r->num_notice][$id]] || !in_array($tpl, $tri_tpl[$notice_group[$r->num_notice][$id]])){
									if(!in_array($r->num_notice, $already_printed)){
										$tri_tpl[$notice_group[$r->num_notice][$id]][]= $tpl;
										$already_printed[]=$r->num_notice;
									}
								}
							}
						}
					}else{
						$this->data_document['sommaires'][0]['records'][]['render']=$tpl;
					}
			}
		}
		
		// il faut trier les regroupements par ordre alphabétique
		if($this->group_pperso) {
			//ksort($tri_tpl);
			$this->pmb_ksort($tri_tpl);
			$index=0;
			foreach ($tri_tpl as $titre => $liste) {
				$index++;
				$this->data_document['sommaires'][$index]['level']=1;
				$this->data_document['sommaires'][$index]['title']=$titre;
				$nb=0;
				foreach ($liste as $val) {
					$this->data_document['sommaires'][$index]['records'][$nb]['render']=$val;
					$nb++;
				}
			}
		}
	}
	
	public function get_entete_mail() {
		global $msg, $id_empr;
		
		if ($this->entete_mail) {
			return $this->entete_mail;
		}
		// paramétrage OPAC: choix du nom de la bibliothèque comme expéditeur
		$requete = "select location_libelle, email, adr1, cp, town from empr, docs_location where empr_location=idlocation and id_empr='$id_empr' ";
		$res = pmb_mysql_query($requete);
		$loc = pmb_mysql_fetch_object($res) ;
		
		$this->entete_mail = "<SPAN style=\'FONT-SIZE: 11pt; FONT-FAMILY: Arial\'>".addslashes($msg['dsi_priv_mail_1'])."!!public!!</SPAN><br /><br /><SPAN style=\'FONT-SIZE: 10pt; FONT-FAMILY: Arial\'>".addslashes($msg['dsi_priv_mail_2'])." ".$msg['empr_ban_priv_opening_quotation_mark'].addslashes($msg['empr_my_account']).$msg['empr_ban_priv_closing_quotation_mark']." > ".$msg['empr_ban_priv_opening_quotation_mark'].addslashes($msg['dsi_bannette_acceder']).$msg['empr_ban_priv_closing_quotation_mark']."&nbsp;:&nbsp; !!public!! - !!date!! </SPAN><br /><br />" ;
		$this->entete_mail .= addslashes($loc->location_libelle."<br />".$loc->adr1."<br />".$loc->cp." ".$loc->town)."<br />";
		$this->entete_mail .= addslashes($msg['dsi_priv_mail_3'])."&nbsp;: <A href=\'mailto:".$loc->email."\'>".$loc->email."</A><br />";
		$this->entete_mail .= "<hr style=''border:none; border-bottom:solid #000000 3px;''/>!!equation!!" ;
		
		return $this->entete_mail;
	}
	
	protected function get_export_selector() {
		global $msg;
		
		$exp = start_export::get_exports();
		$selector = "<select name='typeexport'>";
		$selector .= "<option value='' ".(!$this->typeexport ? "selected='selected'" : "").">".$msg['dsi_ban_noexport']."</option>";
		for ($i=0;$i<count($exp);$i++) {
			$selector .= "<option value='".$exp[$i]["PATH"]."' ".($this->typeexport == $exp[$i]["PATH"] ? "selected='selected'" : "").">".$exp[$i]["NAME"]."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	public function get_short_form($equation) {
		global $msg, $charset;
		global $opac_allow_bannette_export;
		global $opac_private_bannette_date_used_to_calc;
		
		if ($opac_allow_bannette_export) {
			
		} else $liste_exports = "";
		
		
		$form = "
			<form name='creer_dsi' method='post'>
				<input type='hidden' name='equation' value=\"".htmlentities($equation,ENT_QUOTES, $charset)."\" />
				<input type='hidden' name='enregistrer' value='1' />
				<input type='hidden' name='lvl' value='".($this->id_bannette ? 'bannette_edit' : 'bannette_creer')."' />
				<table>
					<tr>
						<td class='align_right'>".$msg['dsi_priv_form_nom']."</td>
						<td><input type='text' name='nom_bannette' value='".$this->nom_bannette."' /></td>
					</tr>
					<tr>
						<td class='align_right'>".$msg['dsi_priv_form_periodicite']."</td>
						<td><input type='text' name='periodicite' value='".$this->periodicite."' /></td>
					</tr>";
		if ($opac_private_bannette_date_used_to_calc == 2) {
    		$form .= "
					<tr>
						<td class='align_right'>".$msg['dsi_priv_form_date_used_to_calc']."</td>
						<td>
						    <select name='update_type'>
                                <option value='C' ".((!$this->update_type || $this->update_type == 'C') ? "selected='selected'" : "").">".$msg['dsi_ban_update_type_c']."</option>
                                <option value='U' ".($this->update_type == 'U' ? "selected='selected'" : "").">".$msg['dsi_ban_update_type_u']."</option>
						    </select>
						</td>
					</tr>
    	    ";
        }
		if ($opac_allow_bannette_export) {
			$form .= "
					<tr>
						<td class='align_right'>".$msg['dsi_ban_typeexport']."</td>
						<td>".$this->get_export_selector()."</td>
					</tr>";
		}
		if ($this->id_bannette) {
			$form .= "
					<tr>
						<td class='align_right'>".$msg['dsi_priv_diffusion']."</td>
						<td><input type='checkbox' name='bannette_diffuse_checked' value='1' checked='checked' /></td>
					</tr>";
		}
		$form .= "</table>
				<input type='submit' class='bouton' value=\"".$msg['dsi_bannette_creer_sauver']."\"/>
			</form>";
		return $form;
	}
	
	public function set_bannette_equations() {
		$this->bannette_equations = new bannette_equations($this->id_bannette);
	}
	
	public static function has_rights($id_bannette) {
		global $id_empr;
		
		$id_bannette += 0;
		
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
	
	protected static function get_lang_messages($lang) {
		global $include_path;
	
		if (!isset(static::$lang_messages[$lang])) {
			$messages = new XMLlist($include_path."/messages/".$lang.".xml", 0);
			$messages->analyser();
			static::$lang_messages[$lang] = $messages->table;
		}
		return static::$lang_messages[$lang];
	}
} # fin de définition de la classe bannette
