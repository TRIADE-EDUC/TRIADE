<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collstate.class.php,v 1.36 2022/02/23 11:18:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes de gestion des états de collection de périodique
global $class_path, $include_path;
require_once($class_path."/parametres_perso.class.php");
require_once($include_path."/templates/collstate.tpl.php");
require_once($class_path."/serial_display.class.php");

class collstate {

	// classe de la notice chapeau des périodiques
	public $id = 0;       // id de l'état de collection
	public $serial_id = 0;         // id du périodique lié
	public $bulletin_id = 0;		// id du bulletin lié
	public $location_id = 0;
	public $location_libelle = '';
	public $surloc_id = 0;
	public $state_collections = '';
	public $emplacement	= 0;
	public $emplacement_libelle = '';
	public $type = 0;
	public $type_libelle = '';
	public $origine = '';
	public $note = '';
	public $cote = '';
	public $archive = '';
	public $lacune = '';
	public $statut = 0;
	public $statut_gestion_libelle = '';
	public $statut_opac_libelle = '';
	public $statut_visible_opac = '';
	public $statut_visible_opac_abon = '';
	public $statut_visible_gestion = '';
	public $statut_class_html = '';
	public $surloc_libelle = '';
	public $bulletins = array();
	public $explr_acces_autorise = '';

	// constructeur
	public function __construct($id=0,$serial_id=0,$bulletin_id=0) {
		$id = intval($id);
		$serial_id = intval($serial_id);
		$bulletin_id = intval($bulletin_id);
		// si id, allez chercher les infos dans la base
		if($id) {
			$this->id = $id;
			$this->fetch_data();
		}
		if($serial_id) {
			$this->serial_id = $serial_id;
		}
		if($bulletin_id) {
			$this->bulletin_id = $bulletin_id;
		}
	}
	
	// récupération des infos en base
	public function fetch_data() {
		global $explr_invisible, $explr_visible_unmod, $pmb_droits_explr_localises ;
		global $pmb_sur_location_activate;
		global $pmb_collstate_advanced;
	
		$myQuery = pmb_mysql_query("SELECT * FROM collections_state WHERE collstate_id='".$this->id."' LIMIT 1");
		$mycoll= pmb_mysql_fetch_object($myQuery);
	
		$this->serial_id=$mycoll->id_serial;
		$this->location_id=$mycoll->location_id;
		$this->state_collections=$mycoll->state_collections;
		$this->emplacement=$mycoll->collstate_emplacement;
		$this->type=$mycoll->collstate_type;
		$this->origine=$mycoll->collstate_origine;
		$this->note=$mycoll->collstate_note;
		$this->cote=$mycoll->collstate_cote;
		$this->archive=$mycoll->collstate_archive;
		$this->lacune=$mycoll->collstate_lacune;
		$this->statut=$mycoll->collstate_statut;
	
		$myQuery = pmb_mysql_query("SELECT * FROM arch_emplacement WHERE archempla_id='".$this->emplacement."' LIMIT 1");
		$myempl= pmb_mysql_fetch_object($myQuery);
		$this->emplacement_libelle=$myempl->archempla_libelle;
	
		$myQuery = pmb_mysql_query("SELECT * FROM arch_type WHERE archtype_id='".$this->type."' LIMIT 1");
		if(pmb_mysql_num_rows($myQuery)) {
			$mytype= pmb_mysql_fetch_object($myQuery);
			$this->type_libelle=$mytype->archtype_libelle;
		}
	
		// Lecture des statuts
		$myQuery = pmb_mysql_query("SELECT * FROM arch_statut WHERE archstatut_id='".$this->statut."' LIMIT 1");
		if(pmb_mysql_num_rows($myQuery)) {
			$mystatut= pmb_mysql_fetch_object($myQuery);
			$this->statut_gestion_libelle=$mystatut->archstatut_gestion_libelle;
			$this->statut_opac_libelle=$mystatut->archstatut_opac_libelle;
			$this->statut_visible_opac=$mystatut->archstatut_visible_opac;
			$this->statut_visible_opac_abon=$mystatut->archstatut_visible_opac_abon;
			$this->statut_visible_gestion=$mystatut->archstatut_visible_gestion;
			$this->statut_class_html=$mystatut->archstatut_class_html;
		}
		
		$myQuery = pmb_mysql_query("select location_libelle, surloc_num from docs_location where idlocation='".$this->location_id."' LIMIT 1");
		if(pmb_mysql_num_rows($myQuery)) {
			$mylocation= pmb_mysql_fetch_object($myQuery);
			$this->location_libelle=$mylocation->location_libelle;
	
			if ($pmb_droits_explr_localises) {
				$tab_invis=explode(",",$explr_invisible);
				$tab_unmod=explode(",",$explr_visible_unmod);
		
				$as_invis = array_search($this->location_id,$tab_invis);
				$as_unmod = array_search($this->location_id,$tab_unmod);
				if ($as_invis!== FALSE && $as_invis!== NULL) $this->explr_acces_autorise="INVIS" ;
				elseif ($as_unmod!== FALSE && $as_unmod!== NULL) $this->explr_acces_autorise="UNMOD" ;
				else $this->explr_acces_autorise="MODIF" ;
			} else $this->explr_acces_autorise="MODIF" ;
			
			if ($pmb_sur_location_activate && $mylocation->surloc_num) {
				$this->surloc_id = $mylocation->surloc_num;
				$myQuery = pmb_mysql_query("select surloc_libelle from sur_location where surloc_id='".$this->surloc_id."' LIMIT 1");	
				if (pmb_mysql_num_rows($myQuery)) {
					$mysurloc = pmb_mysql_fetch_object($myQuery);
					$this->surloc_libelle=$mysurloc->surloc_libelle;
				}
			}
		}
		
		if ($pmb_collstate_advanced) {
			$myQuery = pmb_mysql_query("SELECT collstate_bulletins_num_bulletin FROM collstate_bulletins join bulletins on collstate_bulletins_num_bulletin = bulletin_id WHERE collstate_bulletins_num_collstate='".$this->id."' order by date_date");
			while($mybulletins = pmb_mysql_fetch_object($myQuery)) {
				$this->bulletins[] = $mybulletins->collstate_bulletins_num_bulletin;
			}
		}
	}
	
	//Récupération de l'affichage dans l'isbd
	public function get_isbd() {
		global $msg, $pmb_etat_collections_localise,$pmb_droits_explr_localises,$explr_visible_mod;
	
		if ($pmb_etat_collections_localise && $pmb_droits_explr_localises && $explr_visible_mod) {
			$restrict_location=" and location_id in (".$explr_visible_mod.") and idlocation=location_id";
			$table_location=",docs_location";
			$select_location=",location_libelle";
		}
		$rqt="select state_collections $select_location from collections_state $table_location where id_serial=".$this->serial_id.$restrict_location;
		$execute_query=pmb_mysql_query($rqt);
		if (pmb_mysql_num_rows($execute_query)) {
			$bool=false;
			$affichage="<br /><strong>".$msg["4001"]."</strong><br />";
			while (($r=pmb_mysql_fetch_object($execute_query))) {
				if ($r->state_collections) {
					if ($r->location_libelle) $affichage .= "<strong>".$r->location_libelle."</strong> : ";
					$affichage .= str_replace("\n","<br />",$r->state_collections)."<br />\n";
					$bool=true;
				}
			}
			if ($bool==true) return($affichage);
		}
		return "";
	}
	
	// fonction de mise à jour ou de création d'état de collection
	public function update($value) {
		global $msg;
		$fields="";
		$id_serial=0;
		foreach($value as $key => $val) {
			if($fields) $fields.=",";
			$fields.=" $key='".addslashes($val)."' ";
			if($key == "id_serial"){
				$id_serial=addslashes($val);
			}
		}
		if($this->id) {
			// modif
			$no_erreur=pmb_mysql_query("UPDATE collections_state SET $fields WHERE collstate_id=".$this->id);
			if(!$no_erreur) {
				error_message($msg["collstate_add_collstate"], $msg["collstate_add_error"],1);
				exit;
			}
		} else {
			// create
			$no_erreur=pmb_mysql_query("INSERT INTO collections_state SET $fields ");
			$this->id = pmb_mysql_insert_id();
			if(!$no_erreur) {
				error_message($msg["collstate_edit_collstate"], $msg["collstate_add_error"],1);
				exit;
			}
		}
		
		if($id_serial){
			notice::majNoticesMotsGlobalIndex($id_serial,'collstate');
		}
		return $this->id;
	}
	
	public function update_from_form() {
		global $state_collections,$origine,$archive,$cote,$note,$lacune,$serial_id,$archstatut_id,$archtype_id,$location_id,$archempla_id;
		global $collstate_advanced_expl_list_bulletins;
		global $deflt_docs_location;
		global $collstate_advanced_caddie_bull_id, $collstate_advanced_caddie_expl_id;
		
		$serial_id = intval($serial_id);
		$value = new stdClass();
		
		if ($collstate_advanced_expl_list_bulletins) {
			$this->bulletins = explode(',', $collstate_advanced_expl_list_bulletins);
		}
		if (!$this->bulletin_id && count($this->bulletins)) {
			$this->bulletin_id = $this->bulletins[0];
		}
		if (!$serial_id && $this->bulletin_id) {
			$query = 'select bulletin_notice from bulletins where bulletin_id = '.$this->bulletin_id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$serial_id = pmb_mysql_result($result, 0, 0);
			}
		}
		
		$value->id_serial=stripslashes($serial_id);
		if(!$location_id) $location_id=$deflt_docs_location;
		$value->location_id=intval($location_id);
		$value->state_collections=stripslashes($state_collections);
		$value->collstate_emplacement=stripslashes($archempla_id);
		$value->collstate_type=stripslashes($archtype_id);
		$value->collstate_origine=stripslashes($origine);
		$value->collstate_note=stripslashes($note);
		$value->collstate_cote=stripslashes($cote);
		$value->collstate_archive=stripslashes($archive);
		$value->collstate_lacune=stripslashes($lacune);
		if(!$archstatut_id)$archstatut_id=1;
		$value->collstate_statut=stripslashes($archstatut_id);
		$this->update($value);
	
		//Traitement des champs perso
		$p_perso=new parametres_perso("collstate");
		$p_perso->check_submited_fields();
		$p_perso->rec_fields_perso($this->id);
		
		//Rattachement d'un état des collections à un ou plusieurs bulletins
		if ($collstate_advanced_caddie_bull_id) {
			$caddie_bull = new caddie($collstate_advanced_caddie_bull_id);
		}
		if ($collstate_advanced_caddie_expl_id) {
			$caddie_expl = new caddie($collstate_advanced_caddie_expl_id);
		}
		foreach ($this->bulletins as $i=>$bulletin) {
			$query = "insert into collstate_bulletins set
					collstate_bulletins_num_collstate = '".$this->id."',
					collstate_bulletins_num_bulletin = '".$bulletin."',
					collstate_bulletins_order = '".$i."'";
			pmb_mysql_query($query);
			// On pointe le bulletin dans le panier
			if ($collstate_advanced_caddie_bull_id) {
				$caddie_bull->pointe_item($bulletin, 'BULL');
			}
			// On pointe l'exemplaire dans le panier
			if ($collstate_advanced_caddie_expl_id) {
				$caddie_expl->pointe_item($bulletin, 'BULL');
			}
		}
		
		if($value->id_serial){
			notice::majNoticesMotsGlobalIndex($value->id_serial,'collstate');
		}
	}
	// fonction générant le form de saisie de notice chapeau
	public function do_form() {
		global $msg;
		global $collstate_form,$statut_field,$emplacement_field, $location_field, $support_field;
		global $deflt_docs_location;
		global 	$deflt_arch_statut,$deflt_arch_emplacement,$deflt_arch_type;
		global $charset;
		global $pmb_etat_collections_localise;
		global $pmb_collstate_advanced, $collstate_expl_list_form;
		
		// titre formulaire
		if($this->id) {
			$libelle=$libelle=$msg["collstate_edit_collstate"];
			$link_delete="<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\" />";
	
		} else {
			$libelle=$msg["collstate_add_collstate"];
			$link_delete="";
		}
		$collstate_form = str_replace('!!id!!', htmlentities($this->id,ENT_QUOTES,$charset), $collstate_form);
		$collstate_form = str_replace('!!location_id!!', htmlentities($this->location_id,ENT_QUOTES,$charset), $collstate_form);
		$collstate_form = str_replace('!!serial_id!!', htmlentities($this->serial_id,ENT_QUOTES,$charset), $collstate_form);
		$action="./catalog.php?categ=serials&sub=collstate_update&serial_id=".rawurlencode($this->serial_id)."&id=".rawurlencode($this->id);
		$collstate_form = str_replace('!!action!!', $action, $collstate_form);
		$collstate_form = str_replace('!!delete!!', $link_delete, $collstate_form);
		$collstate_form = str_replace('!!libelle!!',$libelle , $collstate_form);
	
		$collstate_form = str_replace('!!origine!!',htmlentities($this->origine,ENT_QUOTES,$charset) , $collstate_form);
		$collstate_form = str_replace('!!archive!!',htmlentities($this->archive,ENT_QUOTES,$charset) , $collstate_form);
		$collstate_form = str_replace('!!cote!!',htmlentities($this->cote,ENT_QUOTES,$charset) , $collstate_form);
		$collstate_form = str_replace('!!note!!',htmlentities($this->note,ENT_QUOTES,$charset) , $collstate_form);
		$collstate_form = str_replace('!!lacune!!',htmlentities($this->lacune,ENT_QUOTES,$charset) , $collstate_form);
		$collstate_form = str_replace('!!state_collections!!',htmlentities($this->state_collections,ENT_QUOTES,$charset) , $collstate_form);
	
		// champs des localisations
		if($pmb_etat_collections_localise) {
			if(!$this->location_id) $this->location_id=$deflt_docs_location;
			if(!$this->id && !$this->serial_id && $pmb_collstate_advanced) {
				$onchange = "update_expl_list();";
			} else {
				$onchange = "";
			}
			$select = gen_liste("select distinct idlocation, location_libelle from docs_location order by 2 ", "idlocation", "location_libelle", 'location_id', $onchange, $this->location_id, "", "","","",0);
			$field="";
			if($select) $field = str_replace('!!location!!',$select, $location_field);
			$collstate_form = str_replace('!!location_field!!',$field, $collstate_form);
		}else{
			$field="<input type='hidden' name='location_id' id='location_id' value=''/> ";
			$collstate_form = str_replace('!!location_field!!',$field, $collstate_form);
		}
	
	
		// champs des emplacements
		if(!$this->emplacement) $this->emplacement=$deflt_arch_emplacement;
		$select = gen_liste("select archempla_id, archempla_libelle from arch_emplacement order by 2", "archempla_id", "archempla_libelle", "archempla_id", "",$this->emplacement, "",  "", "","","",0) ;
		$field="";
		if($select) $field = str_replace('!!emplacement!!',$select, $emplacement_field);
		$collstate_form = str_replace('!!emplacement_field!!',$field, $collstate_form);
	
		// gestion avancée : liste des exemplaires
		if(!$this->id && !$this->serial_id && $pmb_collstate_advanced) {
			$collstate_form = str_replace('!!expl_list!!', $collstate_expl_list_form , $collstate_form);
		} else {
			$collstate_form = str_replace('!!expl_list!!', '' , $collstate_form);
		}
		
		// champs des supports
		if(!$this->type) $this->type=$deflt_arch_type;
		$select = gen_liste("select archtype_id, archtype_libelle from arch_type order by 2", "archtype_id", "archtype_libelle", "archtype_id", "", $this->type, "", "","","",0) ;
		$field="";
		if($select) $field = str_replace('!!support!!',$select, $support_field);
		$collstate_form = str_replace('!!support_field!!',$field, $collstate_form);
	
		// champs des statuts
		if(!$this->statut) $this->statut=$deflt_arch_statut;
		$select = gen_liste("select archstatut_id, archstatut_gestion_libelle from arch_statut order by 2", "archstatut_id", "archstatut_gestion_libelle", "archstatut_id", "", $this->statut, "", "","","",0) ;
		$field="";
		if($select) $field = str_replace('!!statut!!',$select, $statut_field);
		$collstate_form = str_replace('!!statut_field!!',$field, $collstate_form);
	
		// Champs perso
		$p_perso=new parametres_perso("collstate");
		$parametres_perso="";
		if (!$p_perso->no_special_fields) {
			$perso_=$p_perso->show_editable_fields($this->id);
			$perso="";
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				$perso.="
					<div class='row'>
						<label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]." </label>".$p["COMMENT_DISPLAY"]."
					</div>
					<div class='row'>
						".$p["AFF"]."
					</div>";
			}
			$perso.=$perso_["CHECK_SCRIPTS"];
			$parametres_perso.=$perso;
		}
		$collstate_form = str_replace('!!parametres_perso!!',$parametres_perso , $collstate_form);
	
		$link_annul = "onClick=\"unload_off();history.go(-1);\"";
		$collstate_form = str_replace('!!annul!!', $link_annul, $collstate_form);
	
		//vérification de la présence de champs perso
		//si non, on confirme la soumission du formulaire
		if($p_perso->no_special_fields)
			$return_form = "return true";
		//sinon, on vérifie leurs valeurs
		else $return_form = "return check_form()";
		$collstate_form = str_replace('!!return_form!!',$return_form, $collstate_form);
	
		// gestion avancée
		if(!$this->id && !$this->serial_id && $pmb_collstate_advanced) {
			$collstate_form = $this->do_advanced_form().$collstate_form;
		}
		
		return $collstate_form;
	}
	
	// formulaire de gestion avancée
	protected function do_advanced_form() {
		global $collstate_advanced_form;
		global $PMBuserid;
		global $msg;
		
		$form = $collstate_advanced_form;
		
		// Paniers de bulletins 
		$query = 'select idcaddie, name from caddie where type = "BULL" and (autorisations="'.$PMBuserid.'" or autorisations like "'.$PMBuserid.' %" or autorisations like "% '.$PMBuserid.' %" or autorisations like "% '.$PMBuserid.'") order by name';
		$caddie_bull = gen_liste($query, 'idcaddie', 'name', 'collstate_caddie_bull', 'update_expl_list()', 0, 0, $msg['collstate_advanced_select_caddie'], 0, $msg['collstate_advanced_select_caddie']);
		$form = str_replace('!!caddie_bull!!', $caddie_bull, $form);
		
		// Paniers d'exemplaires
		$query = 'select idcaddie, name from caddie where type = "EXPL" and (autorisations="'.$PMBuserid.'" or autorisations like "'.$PMBuserid.' %" or autorisations like "% '.$PMBuserid.' %" or autorisations like "% '.$PMBuserid.'") order by name';
		$caddie_expl = gen_liste($query, 'idcaddie', 'name', 'collstate_caddie_expl', 'update_expl_list()', 0, 0, $msg['collstate_advanced_select_caddie'], 0, $msg['collstate_advanced_select_caddie']);
		$form = str_replace('!!caddie_expl!!', $caddie_expl, $form);
		
		return $form;
	}
	
	// suppression d'une collection ou de toute les collections d'un périodique
	public function delete() {
		if($this->id) {
			//On nettoye l'index
			if(!$this->serial_id){
				$req="SELECT id_serial FROM collections_state WHERE collstate_id='".$this->id."'";
				$res=pmb_mysql_query($req);
				if($res && pmb_mysql_num_rows($res)){
					$this->serial_id=pmb_mysql_result($res,0,0);
				}
			}
			//elimination des champs persos
			$p_perso=new parametres_perso("collstate");
			$p_perso->delete_values($this->id);
			pmb_mysql_query("DELETE from collections_state WHERE collstate_id='".$this->id."' ");
			
			// Nettoyage de la table collstate_bulletins
			pmb_mysql_query('delete from collstate_bulletins where collstate_bulletins_num_collstate = '.$this->id);
		} else if($this->serial_id) {
			$myQuery = pmb_mysql_query("SELECT collstate_id FROM collections_state WHERE id_serial='".$this->serial_id."' ");
			if((pmb_mysql_num_rows($myQuery))) {
				while(($coll = pmb_mysql_fetch_object($myQuery))) {
					$my_collstate=new collstate($coll->collstate_id);
					$my_collstate->delete();
				}
			}
		}
		//On nettoye l'index
		notice::majNoticesMotsGlobalIndex($this->serial_id,'collstate');
	}
	
	public function get_bulletins_list() {
		global $tpl_collstate_bulletins_list_page, $tpl_collstate_bulletins_list_line;
		global $nb_per_page_a_search, $page, $msg;		
		global $aff_bulletins_restrict_numero, $aff_bulletins_restrict_date, $aff_bulletins_restrict_periode;
		
		if (!$page) $page=1;
		$debut = ($page-1)*$nb_per_page_a_search;
		
		$html = $tpl_collstate_bulletins_list_page;
		$html = str_replace('!!localisation!!', $this->location_libelle, $html);
		$html = str_replace('!!cote!!', $this->cote, $html);
		$html = str_replace('!!type_libelle!!', $this->type_libelle, $html);
		$html = str_replace('!!emplacement_libelle!!', $this->emplacement_libelle, $html);
		$html = str_replace('!!statut_libelle!!', "<span class='".$this->statut_class_html."'  style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span>".$this->statut_gestion_libelle, $html);
		$html = str_replace('!!origine!!', $this->origine, $html);
		$html = str_replace('!!state_collections!!',str_replace("\n","<br />",$this->state_collections), $html);
		$html = str_replace('!!archive!!',$this->archive, $html);
		$html = str_replace('!!lacune!!', str_replace("\n","<br />",$this->lacune), $html);
		
// 		$bulletins_list = '';
// 		foreach ($this->bulletins as $bulletin) {
// 			$line = $tpl_collstate_bulletins_list_line;
// 			$bulletinage = new bulletinage_display($bulletin);
// 			$line = str_replace('!!bulletin_id!!', $bulletin, $line);
// 			$line = str_replace('!!bulletin_display!!', $bulletinage->display, $line);
// 			$bulletins_list.= $line;
// 		}
// 		$html = str_replace('!!bulletins_list!!', $bulletins_list, $html);

		$base_url = "./catalog.php?categ=serials&sub=collstate_bulletins_list&id=".$this->id."&serial_id=".$this->serial_id."&bulletin_id=".$this->bulletin_id;

		$bulletins = '';
		$pages_display = '';
		if (count($this->bulletins)) {
			$clause = " and bulletin_id in (".implode(',', $this->bulletins).") ";
			// barre de restriction des bulletins affichés
			if ($aff_bulletins_restrict_numero) {
				$clause.= " and bulletin_numero like '%".str_replace("*","%",$aff_bulletins_restrict_numero)."%' ";
				$base_url .= "&aff_bulletins_restrict_numero=".urlencode($aff_bulletins_restrict_numero) ;
			}
			if ($aff_bulletins_restrict_date) {
				$aff_bulletins_restrict_date_traite = str_replace("*","%",$aff_bulletins_restrict_date) ;
				$tab_bulletins_restrict_date = explode ($msg['format_date_input_separator'],$aff_bulletins_restrict_date_traite) ;
				if(count($tab_bulletins_restrict_date)==3)$aff_bulletins_restrict_date_traite = $tab_bulletins_restrict_date[2]."-".$tab_bulletins_restrict_date[1]."-".$tab_bulletins_restrict_date[0];
				if(count($tab_bulletins_restrict_date)==2)$aff_bulletins_restrict_date_traite = $tab_bulletins_restrict_date[1]."-".$tab_bulletins_restrict_date[0];
				if(count($tab_bulletins_restrict_date)==1)$aff_bulletins_restrict_date_traite = $tab_bulletins_restrict_date[0];
				$clause .= " and date_date like '%".$aff_bulletins_restrict_date_traite."%'" ;
				$base_url .= "&aff_bulletins_restrict_date=".urlencode($aff_bulletins_restrict_date) ;
			}
			if ($aff_bulletins_restrict_periode) {
				$aff_bulletins_restrict_periode_traite = str_replace("*","%",$aff_bulletins_restrict_periode) ;
				$clause .= " and mention_date like '%".$aff_bulletins_restrict_periode_traite."%'" ;
				$base_url .= "&aff_bulletins_restrict_periode=".urlencode($aff_bulletins_restrict_periode) ;
			}
			$base_url .= $url_suffix;
			
			//On compte les expl de la localisation
			$rqt="SELECT COUNT(1) FROM bulletins ".($location?", exemplaires":"")." WHERE 1 ".($location?"and (expl_bulletin=bulletin_id and expl_location='$location' or expl_location is null) ":"").($serial_id ? "and bulletin_notice='".$serial_id."' " : "");
			$myQuery = pmb_mysql_query($rqt);
			$nb_expl_loc = pmb_mysql_result($myQuery,0,0);
			
			//On compte les bulletins de la localisation
			$rqt="SELECT count(distinct bulletin_id) FROM bulletins ".($location?",exemplaires ":"")." WHERE 1 ".($location?"and (expl_bulletin=bulletin_id and expl_location='$location') ":"").($serial_id ? "and bulletin_notice='".$serial_id."' " : "");
			$myQuery = pmb_mysql_query($rqt);
			if ($execute_query&&pmb_mysql_num_rows($myQuery)) {
				$nb_bull_loc = pmb_mysql_result($myQuery,0,0);
			}
			//On compte les bulletins à afficher
			$rqt="SELECT count(distinct bulletin_id) FROM bulletins ".($location?", exemplaires":"")." WHERE 1 ".($location?"and (expl_bulletin=bulletin_id and expl_location='$location' or expl_location is null) ":"").($serial_id ? "and bulletin_notice='".$serial_id."' " : "").$clause;
			$myQuery = pmb_mysql_query($rqt);
			$nbr_lignes = pmb_mysql_result($myQuery,0,0);
			
			require_once("./catalog/serials/views/view_bulletins.inc.php");
		}

		$html = str_replace('!!collstate_id!!', $this->id, $html);
		$html = str_replace('!!serial_id!!', $this->serial_id, $html);
		$html = str_replace('!!bulletin_id!!', $this->bulletin_id, $html);
		$html = str_replace('!!bulletins_list!!', $bulletins, $html);
		$html = str_replace('!!paginator!!', $pages_display, $html);
		
		return $html;
	}

} // fin définition classe
