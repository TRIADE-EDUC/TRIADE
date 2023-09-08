<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_actions.class.php,v 1.47 2018-12-03 10:15:23 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/demandes_notes.class.php");
require_once($class_path."/demandes.class.php");
require_once($class_path."/explnum_doc.class.php");
require_once($class_path."/workflow.class.php");
require_once($class_path."/audit.class.php");

class demandes_actions{
	
	public $id_action = 0;
	public $type_action = 0;
	public $statut_action = 0;
	public $sujet_action = '';
	public $detail_action = '';
	public $time_elapsed = 0;
	public $date_action = '0000-00-00';
	public $deadline_action = '0000-00-00';
	public $progression_action = 0;
	public $prive_action = 0;
	public $cout = 0;
	public $num_demande = 0;
	public $libelle_demande = '';
	public $actions_num_user = 0;
	public $actions_type_user = 0;
	public $createur_action ="";
	public $list_type = array();
	public $list_statut = array();
	public $workflow = array();
	public $notes = array();
	public $actions_read_gestion = 0; // flag gestion sur la lecture de l'action par l'utilisateur
	public $actions_read_opac = 0; // flag opac sur la lecture de l'action par l'utilisateur
	public $last_modified=0;
	/*
	 * Constructeur
	 */
	public function __construct($id=0,$lazzy_load=true){
		$id += 0;
		$this->fetch_data($id,$lazzy_load);
	}
	
	public function fetch_data($id=0,$lazzy_load=true){
		global $base_path, $dbh, $iddemande;
		
		if($this->id_action && !$id){
			$id=$this->id_action;
		}elseif(!$this->id_action && $id){
			$this->id_action=$id;
		}
		
		if($this->id_action){
			$req = "select id_action,type_action,statut_action, sujet_action,
			detail_action,date_action,deadline_action,temps_passe, cout, progression_action, prive_action, num_demande, titre_demande,
			actions_num_user,actions_type_user,actions_read_gestion,actions_read_opac  
			from demandes_actions
			join demandes on num_demande=id_demande
			where id_action='".$this->id_action."'";
			$res=pmb_mysql_query($req,$dbh);
			if(pmb_mysql_num_rows($res)){
				$obj = pmb_mysql_fetch_object($res);
				$this->type_action = $obj->type_action;
				$this->date_action = $obj->date_action;
				$this->deadline_action = $obj->deadline_action;
				$this->sujet_action = $obj->sujet_action;
				$this->detail_action = $obj->detail_action;
				$this->cout = $obj->cout;
				$this->progression_action = $obj->progression_action;
				$this->time_elapsed = $obj->temps_passe;
				$this->num_demande = $obj->num_demande;
				$this->statut_action = $obj->statut_action;
				$this->libelle_demande = $obj->titre_demande;
				$this->prive_action = $obj->prive_action;
				$this->actions_num_user = $obj->actions_num_user;
				$this->actions_type_user =  $obj->actions_type_user;
				$this->actions_read_gestion =  $obj->actions_read_gestion;
				$this->actions_read_opac =  $obj->actions_read_opac;
			} else{
				$this->id_action = 0;
				$this->type_action = 0;
				$this->date_action = '0000-00-00';
				$this->deadline_action = '0000-00-00';
				$this->sujet_action = '';
				$this->detail_action = '';
				$this->cout = 0;
				$this->progression_action = 0;
				$this->time_elapsed = 0;
				$this->num_demande = 0;
				$this->statut_action =	0;
				$this->libelle_demande = '';
				$this->prive_action = 0;
				$this->actions_num_user = 0;
				$this->actions_type_user =  0;
				$this->actions_read_gestion =  0;
				$this->actions_read_opac = 0;
			}
		} else {
			$this->id_action = 0;
			$this->type_action = 0;
			$this->date_action = '0000-00-00';
			$this->deadline_action = '0000-00-00';
			$this->sujet_action = '';
			$this->detail_action = '';
			$this->cout = 0;
			$this->progression_action = 0;
			$this->time_elapsed = 0;
			$this->num_demande = 0;
			$this->statut_action =	0;
			$this->libelle_demande = '';
			$this->prive_action = 0;
			$this->actions_num_user = 0;
			$this->actions_type_user =  0;
			$this->actions_read_gestion =  0;
			$this->actions_read_opac = 0;
		}
		
		if(!sizeof($this->workflow)){
			$this->workflow = new workflow('ACTIONS','INITIAL');
			$this->list_type = $this->workflow->getTypeList();
			$this->list_statut = $this->workflow->getStateList();
		}
		
		if($iddemande) {
			$this->num_demande = $iddemande;
			$req = "select titre_demande from demandes where id_demande='".$iddemande."'";
			$res = pmb_mysql_query($req,$dbh);
			$this->libelle_demande = pmb_mysql_result($res,0,0);
		}
		
		//On remonte les notes
		if($this->id_action){
			$this->notes=array();
			//On charge la liste d'id des notes
			$query='SELECT id_note,date_note FROM demandes_notes WHERE num_action='.$this->id_action.' ORDER BY id_note ASC';
			$result=pmb_mysql_query($query);
			
			while($note=pmb_mysql_fetch_array($result,PMB_MYSQL_ASSOC)){
				if($lazzy_load){
					$this->notes[$note['id_note']]=new stdClass();
					$this->notes[$note['id_note']]->id_note=$note['id_note'];
					$this->notes[$note['id_note']]->date_note=$note['date_note'];
					$this->notes[$note['id_note']]->id_action=$this->id_action;
				}else{
					$this->notes[$note['id_note']]=new demandes_notes($note['id_note'],$this->id_action);
				}
				
				
			}
			$this->last_modified=self::get_last_modified_note($this->notes);
		}
	}
	
	/*
	 * Cherche la note la plus récente grace à l'audit
	 */
	public static function get_last_modified_note($notes){
		$temp=0;
		foreach($notes as $id_note=>$note){
			//On cherche la derniere note modifiée
			if(!$temp){
				$temp=$note;
			}
			
			$dateLast_modified= new DateTime($temp->date_note);
			$dateNote= new DateTime($note->date_note);
			
			if($dateLast_modified->format('U') < $dateNote->format('U')){
				$temp = $note;
			}
		}
		
		if($temp){
			return $temp;
		}
	}
	
	/*
	 * Affichage du formulaire de création/modification
	 */
	public function show_modif_form(){
		
		global $form_modif_action,$msg, $charset;
		
		
		if($this->id_action){
			$form_modif_action = str_replace('!!form_title!!',htmlentities(sprintf($msg['demandes_action_modif'],' : '.$this->sujet_action),ENT_QUOTES,$charset),$form_modif_action);
			$form_modif_action = str_replace('!!sujet!!',htmlentities($this->sujet_action,ENT_QUOTES,$charset),$form_modif_action);
			$form_modif_action = str_replace('!!detail!!',htmlentities($this->detail_action,ENT_QUOTES,$charset),$form_modif_action);
			$form_modif_action = str_replace('!!cout!!',htmlentities($this->cout,ENT_QUOTES,$charset),$form_modif_action);
			$form_modif_action = str_replace('!!time_elapsed!!',htmlentities($this->time_elapsed,ENT_QUOTES,$charset),$form_modif_action);
			$form_modif_action = str_replace('!!progression!!',htmlentities($this->progression_action,ENT_QUOTES,$charset),$form_modif_action);
			$form_modif_action = str_replace('!!select_type!!',$this->workflow->getTypeCommentById($this->type_action),$form_modif_action);
			$type_hide = "<input type='hidden' name='idtype' id='idtype' value='$this->type_action' />";
			$form_modif_action = str_replace('!!type_action!!',$type_hide,$form_modif_action);
			$form_modif_action = str_replace('!!select_statut!!',$this->getStatutSelector($this->statut_action),$form_modif_action);
			
			$form_modif_action = str_replace('!!date_fin_btn!!',formatdate($this->deadline_action),$form_modif_action);
			$form_modif_action = str_replace('!!date_debut_btn!!',formatdate($this->date_action),$form_modif_action);
			$form_modif_action = str_replace('!!date_debut!!',htmlentities($this->date_action,ENT_QUOTES,$charset),$form_modif_action);
			$form_modif_action = str_replace('!!date_fin!!',htmlentities($this->deadline_action,ENT_QUOTES,$charset),$form_modif_action);
			
			$btn_suppr = "<input type='submit' class='bouton' value='$msg[63]' onclick='this.form.act.value=\"suppr_action\"; return confirm_delete();' />";	
			$form_modif_action = str_replace('!!btn_suppr!!',$btn_suppr,$form_modif_action);
			$form_modif_action = str_replace('!!idaction!!',$this->id_action,$form_modif_action);
			$form_modif_action = str_replace('!!iddemande!!',$this->num_demande,$form_modif_action);
			if($this->prive_action)
				$form_modif_action = str_replace('!!ck_prive!!','checked',$form_modif_action);
			else $form_modif_action = str_replace('!!ck_prive!!','',$form_modif_action);
			
			
			$act_cancel = "document.location='./demandes.php?categ=action&act=see&idaction=$this->id_action'";
			$act_form = "./demandes.php?categ=action&act=see&idaction=$this->id_action";
			
			$form_modif_action = str_replace('!!form_action!!',$act_form,$form_modif_action);				
			$form_modif_action = str_replace('!!cancel_action!!',$act_cancel,$form_modif_action);	
			$path = "<a href=./demandes.php?categ=gestion&act=see_dmde&iddemande=$this->num_demande>".htmlentities($this->libelle_demande,ENT_QUOTES,$charset)."</a>";
			$path .= " > <a href=./demandes.php?categ=action&act=see&idaction=$this->id_action>".htmlentities($this->sujet_action,ENT_QUOTES,$charset)."</a>";
			$form_modif_action = str_replace('!!path!!',$path,$form_modif_action);
			
			print $form_modif_action;
			
		} else {
			$form_modif_action = str_replace('!!form_title!!',htmlentities($msg['demandes_action_creation'],ENT_QUOTES,$charset),$form_modif_action);
			$form_modif_action = str_replace('!!cout!!','',$form_modif_action);
			$form_modif_action = str_replace('!!progression!!','',$form_modif_action);
			$form_modif_action = str_replace('!!sujet!!','',$form_modif_action);
			$form_modif_action = str_replace('!!detail!!','',$form_modif_action);
			$form_modif_action = str_replace('!!time_elapsed!!','',$form_modif_action);
			$date = formatdate(today());
			$date_debut=date("Y-m-d",time());
			$form_modif_action = str_replace('!!date_fin_btn!!',$date,$form_modif_action);
			$form_modif_action = str_replace('!!date_debut_btn!!',$date,$form_modif_action);
			$form_modif_action = str_replace('!!date_debut!!',$date_debut,$form_modif_action);
			$form_modif_action = str_replace('!!date_fin!!',$date_debut,$form_modif_action);
			$form_modif_action = str_replace('!!select_type!!',$this->getTypeSelector(),$form_modif_action);
			$form_modif_action = str_replace('!!type_action!!','',$form_modif_action);
			$form_modif_action = str_replace('!!select_statut!!',$this->getStatutSelector(),$form_modif_action);
			$form_modif_action = str_replace('!!btn_suppr!!','',$form_modif_action);
			$form_modif_action = str_replace('!!idaction!!','',$form_modif_action);
			$form_modif_action = str_replace('!!iddemande!!',$this->num_demande,$form_modif_action);
			
			$act_cancel = "document.location='./demandes.php?categ=gestion&act=see_dmde&iddemande=$this->num_demande'";
						
			$form_modif_action = str_replace('!!form_action!!',"",$form_modif_action);
			$form_modif_action = str_replace('!!cancel_action!!',$act_cancel,$form_modif_action);	
			$path = "<a href=./demandes.php?categ=gestion&act=see_dmde&iddemande=$this->num_demande>".htmlentities($this->libelle_demande,ENT_QUOTES,$charset)."</a>";
			$form_modif_action = str_replace('!!path!!',$path,$form_modif_action);
			
			print $form_modif_action;
		}
	}
	
	/*
	 * Formulaire de consultation d'une action
	 */
	public function show_consultation_form(){
		
		global $form_consult_action, $form_see_docnum, $msg, $charset, $pmb_gestion_devise, $dbh, $pmb_type_audit;
		
		$form_consult_action = str_replace('!!form_title!!',htmlentities($this->sujet_action,ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!idstatut!!',htmlentities($this->statut_action,ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!type_action!!',htmlentities($this->workflow->getTypeCommentById($this->type_action),ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!statut_action!!',htmlentities($this->workflow->getStateCommentById($this->statut_action),ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!detail_action!!',htmlentities($this->detail_action,ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!date_action!!',htmlentities(formatdate($this->date_action),ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!date_butoir_action!!',htmlentities(formatdate($this->deadline_action),ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!time_action!!',htmlentities($this->time_elapsed.$msg['demandes_action_time_unit'],ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!cout_action!!',htmlentities($this->cout,ENT_QUOTES,$charset).$pmb_gestion_devise,$form_consult_action);
		$form_consult_action = str_replace('!!progression_action!!',htmlentities($this->progression_action,ENT_QUOTES,$charset).'%',$form_consult_action);
		$form_consult_action = str_replace('!!idaction!!',htmlentities($this->id_action,ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!iddemande!!',htmlentities($this->num_demande,ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!createur!!',htmlentities($this->getCreateur($this->actions_num_user,$this->actions_type_user),ENT_QUOTES,$charset),$form_consult_action);
		$form_consult_action = str_replace('!!prive_action!!',htmlentities(($this->prive_action ? $msg[40] : $msg[39] ),ENT_QUOTES,$charset),$form_consult_action);
		
		$path = "<a href=./demandes.php?categ=gestion&act=see_dmde&iddemande=$this->num_demande>".htmlentities($this->libelle_demande,ENT_QUOTES,$charset)."</a>";
		$form_consult_action = str_replace('!!path!!',$path,$form_consult_action);
		
		$act_cancel = "document.location='./demandes.php?categ=gestion&act=see_dmde&iddemande=$this->num_demande'";
		$form_consult_action = str_replace('!!cancel_action!!',$act_cancel,$form_consult_action);		

		$states_btn = $this->getDisplayStateBtn($this->workflow->getStateList($this->statut_action));
		$form_consult_action = str_replace('!!btn_etat!!',$states_btn,$form_consult_action);
		
		// bouton audit
		if($pmb_type_audit){
			$btn_audit = audit::get_dialog_button($this->id_action, 15);
		} else {
			$btn_audit = "";
		}
		$form_consult_action = str_replace('!!btn_audit!!',$btn_audit,$form_consult_action);
		
		print $form_consult_action;
		
		//Notes
		print demandes_notes::show_dialog($this->notes,$this->id_action,$this->num_demande);
		
		//Documents Numériques
		$req = "select * from explnum_doc join explnum_doc_actions on num_explnum_doc=id_explnum_doc 
		where num_action='".$this->id_action."'";
		$res = pmb_mysql_query($req,$dbh);
		if(pmb_mysql_num_rows($res)){
			$tab_docnum = array();
			while(($docnums = pmb_mysql_fetch_array($res))){
				$tab_docnum[] = $docnums;
			}
			$explnum_doc = new explnum_doc();
			$liste_docnum = $explnum_doc->show_docnum_table($tab_docnum,'./demandes.php?categ=action&act=modif_docnum&idaction='.$this->id_action);
			$form_see_docnum = str_replace('!!list_docnum!!',$liste_docnum,$form_see_docnum);
		} else {
			$form_see_docnum = str_replace('!!list_docnum!!',htmlentities($msg['demandes_action_no_docnum'],ENT_QUOTES,$charset),$form_see_docnum);
		}
		$form_see_docnum = str_replace('!!idaction!!',$this->id_action,$form_see_docnum);
		print $form_see_docnum;
		
		// Annulation de l'alerte sur l'action en cours après lecture des nouvelles notes si c'est la personne à laquelle est affectée l'action qui la lit
		$this->actions_read_gestion = demandes_actions::action_read($this->id_action,true,"_gestion");
		// Mise à jour de la demande dont est issue l'action
		demandes_actions::action_majParentEnfant($this->id_action,$this->num_demande,"_gestion");
	}
	
	/*
	 * Formulaire d'ajout/modification d'un document numérique
	 */
	public function show_docnum_form(){
		
		global $form_add_docnum, $msg,$dbh, $charset,$explnumdoc_id,$explnum_doc;
		
		if($explnumdoc_id){
			$rqt = "select prive, rapport from explnum_doc_actions where num_explnum_doc='".$explnumdoc_id."'";
			$res = pmb_mysql_query($rqt, $dbh);
			$expl = pmb_mysql_fetch_object($res);
			$prive = $expl->prive;
			$rapport = $expl->rapport;
			
			$explnum_doc = new explnum_doc($explnumdoc_id);
			$form_add_docnum = str_replace('!!idaction!!',$this->id_action, $form_add_docnum);
			$form_add_docnum = str_replace('!!url_doc!!',htmlentities($explnum_doc->explnum_doc_url,ENT_QUOTES,$charset), $form_add_docnum);
			$form_add_docnum = str_replace('!!nom!!',htmlentities($explnum_doc->explnum_doc_nomfichier,ENT_QUOTES,$charset), $form_add_docnum);
			$act_cancel = "document.location='./demandes.php?categ=action&act=see&idaction=$this->id_action'";
			$form_add_docnum = str_replace('!!cancel_action!!',$act_cancel, $form_add_docnum);
			$form_add_docnum = str_replace('!!form_title!!',htmlentities($msg['explnum_data_doc'],ENT_QUOTES,$charset),$form_add_docnum);
			$form_add_docnum = str_replace('!!iddocnum!!',$explnumdoc_id,$form_add_docnum);		
			$form_add_docnum = str_replace('!!ck_prive!!',($prive ? 'checked' :''),$form_add_docnum);
			$form_add_docnum = str_replace('!!ck_rapport!!',($rapport ? 'checked' :''),$form_add_docnum);		
			$btn_suppr= "<input type='submit' class='bouton' value='$msg[63]' onClick='this.form.act.value=\"suppr_docnum\" ; ' />";
		} else {
			$form_add_docnum = str_replace('!!idaction!!',$this->id_action, $form_add_docnum);
			$form_add_docnum = str_replace('!!url_doc!!',"", $form_add_docnum);
			$form_add_docnum = str_replace('!!nom!!','', $form_add_docnum);
			$act_cancel = "document.location='./demandes.php?categ=action&act=see&idaction=$this->id_action'";
			$form_add_docnum = str_replace('!!cancel_action!!',$act_cancel, $form_add_docnum);
			$form_add_docnum = str_replace('!!iddocnum!!','',$form_add_docnum);			
			$form_add_docnum = str_replace('!!form_title!!',htmlentities($msg['explnum_ajouter_doc'],ENT_QUOTES,$charset),$form_add_docnum);
			$form_add_docnum = str_replace('!!ck_prive!!','',$form_add_docnum);
			$form_add_docnum = str_replace('!!ck_rapport!!','',$form_add_docnum);
			$btn_suppr="";			
		}
		$form_add_docnum = str_replace('!!suppr_btn!!',$btn_suppr,$form_add_docnum);
		$act_cancel = "document.location='./demandes.php?categ=action&act=see&idaction=$this->id_action'";
		$form_add_docnum = str_replace('!!cancel_action!!',$act_cancel, $form_add_docnum);
		
		$path = "<a href=./demandes.php?categ=gestion&act=see_dmde&iddemande=$this->num_demande>".htmlentities($this->libelle_demande,ENT_QUOTES,$charset)."</a>";
		$path .= " > <a href=./demandes.php?categ=action&act=see&idaction=$this->id_action>".htmlentities($this->sujet_action,ENT_QUOTES,$charset)."</a>";
		$form_add_docnum = str_replace('!!path!!',$path,$form_add_docnum);
		
		print $form_add_docnum;
	}
	
	/*
	 * Retourne un sélecteur avec les types d'action
	 */
	public function getTypeSelector($idtype=0){
		
		global $charset, $msg;
		
		$selector = "<select name='idtype'>";
		$select="";
		if($default) $selector .= "<option value='0'>".htmlentities($msg['demandes_action_all_types'],ENT_QUOTES,$charset)."</option>";
		for($i=1;$i<=count($this->list_type);$i++){
			if($idtype == $i) $select = "selected";
			$selector .= "<option value='".$this->list_type[$i]['id']."' $select>".htmlentities($this->list_type[$i]['comment'],ENT_QUOTES,$charset)."</option>";
			$select = "";
		}
		$selector .= "</select>";
		
		return $selector;
	}
	
	/*
	 * Affiche la liste des boutons correspondants au statut en cours
	*/
	public function getDisplayStateBtn($list_statut=array(),$multi=0){
		global $charset,$msg;
		
		if($multi){
			$message = $msg['demandes_action_change_checked_states'];
		} else {
			$message = $msg['demandes_action_change_state'];
		}
		$display = "<label class='etiquette'>".$message." : </label>";
		
		for($i=0;$i<count($list_statut);$i++){
			$display .= "&nbsp;<input class='bouton' type='submit' name='btn_".$list_statut[$i]['id']."' value='".htmlentities($list_statut[$i]['comment'],ENT_QUOTES,$charset)."' onclick='this.form.idstatut.value=\"".$list_statut[$i]['id']."\"; this.form.act.value=\"change_statut\";'/>";
		}
	
		return $display;
	}
	
	/*
	 * Retourne un sélecteur avec les statuts d'action
	 */
	public function getStatutSelector($idstatut=0,$ajax=false){
		
		global $charset;
		
		$selector = "<select ".($ajax ? "name='save_statut_".$this->id_action."' id='save_statut_".$this->id_action."'" : "name='idstatut'").">";
		$select="";
		for($i=1;$i<=count($this->list_statut);$i++){
			if($idstatut == $this->list_statut[$i]['id']) $select = "selected";
			$selector .= "<option value='".$this->list_statut[$i]['id']."' $select>".htmlentities($this->list_statut[$i]['comment'],ENT_QUOTES,$charset)."</option>";
			$select = "";
		}
		$selector .= "</select>";
		
		return $selector;
	}
	
	public static function get_values_from_form(&$action){
		global $idaction,$sujet, $idtype,$PMBuserid ,$idstatut;
		global $date_debut, $date_fin, $detail;
		global $time_elapsed, $progression,$cout,$iddemande, $ck_prive, $pmb_type_audit;
		
		$action->id_action=$idaction;
		$action->num_demande=$iddemande;
		$action->sujet_action=$sujet;
		$action->type_action=$idtype;
		$action->statut_action=$idstatut;
		$action->date_action=$date_debut;
		$action->deadline_action=$date_fin;
		$action->detail_action=$detail;
		$action->time_elapsed=$time_elapsed;
		$action->progression_action=$progression;
		$action->cout=$cout;
		$action->prive_action=$ck_prive;
		$action->actions_type_user='0';
		$action->actions_num_user=$PMBuserid;
	}

	public static function get_docnum_values_from_form(&$explnum_doc) {
		global $f_url,$f_nom,$ck_prive,$ck_rapport;
		
		if($f_url){
			$explnum_doc->explnum_doc_url = stripslashes($f_url);
			$explnum_doc->explnum_doc_mime = 'URL';
			$explnum_doc->explnum_doc_nomfichier = stripslashes($f_nom ? $f_nom : $f_url);
		} else {
			if(!$_FILES['f_fichier']['error']){
				$explnum_doc->load_file($_FILES['f_fichier']);
				$explnum_doc->analyse_file();
			}
			if($f_nom){
				$explnum_doc->setName($f_nom);
			}
			
		}
		
		if($ck_prive){
			$explnum_doc->prive=1;
		}else{
			$explnum_doc->prive=0;
		}
		
		if($ck_rapport){
			$explnum_doc->rapport=1;
		}else{
			$explnum_doc->rapport=0;
		}
	}
	
	public static function delete_docnum($explnum_doc){
		global $dbh;
		
		$explnum_doc->delete();
		$query = "DELETE FROM explnum_doc_actions WHERE num_explnum_doc='".$explnum_doc->explnum_doc_id."'";
		pmb_mysql_query($query,$dbh);
	}
	
	public static function save_docnum($action,$explnum_doc){
		global $dbh;
		
		$explnum_doc->save();
		
		$query = "REPLACE INTO explnum_doc_actions SET
		num_explnum_doc='".$explnum_doc->explnum_doc_id."',
		num_action='".$action->id_action."',
		prive='".$explnum_doc->prive."',
		rapport='".$explnum_doc->rapport."'";
		
		pmb_mysql_query($query,$dbh);
	}
	
	
	/*
	 * Insertion/Modification d'une action
	*/
	public static function save(&$action){
		global $dbh, $pmb_type_audit;
		
		if($action->id_action){
			//MODIFICATION
			$query = "UPDATE demandes_actions SET
			sujet_action='".$action->sujet_action."',
			type_action='".$action->type_action."',
			statut_action='".$action->statut_action."',
			detail_action='".$action->detail_action."',
			date_action='".$action->date_action."',
			deadline_action='".$action->deadline_action."',
			temps_passe='".$action->time_elapsed."',
			cout='".$action->cout."',
			progression_action='".$action->progression_action."',
			prive_action='".$action->prive_action."',
			num_demande='".$action->num_demande."',
			actions_read_gestion='1',
			actions_read_opac='1' 
			WHERE id_action='".$action->id_action."'";
			
			pmb_mysql_query($query,$dbh);
			//audit
			if($pmb_type_audit) audit::insert_modif(AUDIT_ACTION,$action->id_action);
				
		} else {
			//CREATION
			$query = "INSERT INTO demandes_actions SET
			sujet_action='".$action->sujet_action."',
			type_action='".$action->type_action."',
			statut_action='".$action->statut_action."',
			detail_action='".$action->detail_action."',
			date_action='".$action->date_action."',
			deadline_action='".$action->deadline_action."',
			temps_passe='".$action->time_elapsed."',
			cout='".$action->cout."',
			progression_action='".$action->progression_action."',
			prive_action='".$action->prive_action."',
			num_demande='".$action->num_demande."',
			actions_num_user='".$action->actions_num_user."',
			actions_type_user='".$action->actions_type_user."',
			actions_read_gestion='1',
			actions_read_opac='1'
			";
			pmb_mysql_query($query,$dbh);
			$action->id_action = pmb_mysql_insert_id($dbh);
			
			// audit
			if($pmb_type_audit) audit::insert_modif(AUDIT_ACTION,$action->id_action);
				
			//Création d'une note automatiquement
			if($action->detail_action && $action->detail_action!==""){
	
				$note=new demandes_notes();
				$note->num_action=$action->id_action;
				$note->date_note=date("Y-m-d h:i:s",time());
				$note->rapport=0;
				$note->contenu=$action->detail_action;
				$note->notes_num_user=$action->actions_num_user;
				$note->notes_type_user=$action->actions_type_user;
				demandes_notes::save($note);
				
			}
		}
	}

	/*
	 * Changement de statut d'une action
	*/
	public static function change_statut($statut,$action){
		global $dbh, $pmb_type_audit , $PMBuserid;
	
		$query = "update demandes_actions set statut_action=$statut where id_action='".$action->id_action."'";
		pmb_mysql_query($query,$dbh);
		
		if($pmb_type_audit) audit::insert_modif(AUDIT_ACTION,$action->id_action);
	}
	
	
	
	/*
	 * Affichage de la liste des actions
	 */
	public static function show_list_actions($actions,$id_demande,$last_modified=0,$allow_expand=true,$from_ajax=false){
		global $dbh, $msg, $charset;
		global $content_liste_action, $form_liste_action, $js_liste_action;
		global $pmb_gestion_devise, $pmb_type_audit, $ck_vue;
		
		if($from_ajax) {
			$list_actions = $content_liste_action;
		} else {
			$list_actions = $js_liste_action.$form_liste_action;
		}
		$liste ="";
		if(sizeof($actions)){
			$parity=1;						
			foreach($actions as $id_action=>$action){
				
				if ($parity % 2) {
					$pair_impair = "even";
				} else {
					$pair_impair = "odd";
				}
				$parity += 1;
				$tr_javascript = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
				$onclick = "onclick=\"document.location='./demandes.php?categ=action&act=see&idaction=".$action->id_action."#fin'\"";
				
				//On ouvre la derniere conversation
				if($last_modified==$action->id_action){
					$list_actions = str_replace('!!last_modified!!',$last_modified,$list_actions);
				}
				
				// affichage en gras si nouveauté du côté des notes ou des actions + icone
				$style =""; 
				if($action->actions_read_gestion == 1){				
					$style=" style='cursor: pointer; font-weight:bold'";									
				} else {
					$style=" style='cursor: pointer'";					
				}
				
				$liste .= "<tr id='action".$action->id_action."' class='".$pair_impair."' ".$tr_javascript.$style."  >";
				
				if($allow_expand){
					$list_actions = str_replace('!!expand_header!!',"<th></th>",$list_actions);
					$liste .= "
						<td><img hspace=\"3\" border=\"0\" onclick=\"expand_note('note".$action->id_action."','$action->id_action', true, 0); return false;\" title=\"\" id=\"note".$action->id_action."Img\" class=\"img_plus\" src=\"".get_url_icon('plus.gif')."\"></td>";
						
				}else{
					$list_actions = str_replace('!!expand_header!!',"",$list_actions);
				}
				
				$liste.="<td>";
				if($action->actions_read_gestion == 1){
					// remplacer $action le jour où on décide d'activer la modif d'état manuellement par //onclick=\"change_read_action('read".$action->id_action."','$action->id_action','$action->num_demande', true); return false;\"
					$liste .= "<img hspace=\"3\" border=\"0\" title=\"\" ".$onclick." id=\"read".$action->id_action."Img1\" class=\"img_plus\" src='".get_url_icon('notification_empty.png')."' style='display:none'>
								<img hspace=\"3\" border=\"0\"  title=\"" . $msg['demandes_new']. "\" ".$onclick." id=\"read".$action->id_action."Img2\" class=\"img_plus\" src='".get_url_icon('notification_new.png')."'>";
				} else {
					// remplacer $action le jour où on décide d'activer la modif d'état manuellement par onclick=\"change_read_action('read".$action->id_action."','$action->id_action','$action->num_demande', true); return false;\"
					$liste .= "<img hspace=\"3\" border=\"0\" title=\"\" ".$onclick." id=\"read".$action->id_action."Img1\" class=\"img_plus\" src='".get_url_icon('notification_empty.png')."' >
								<img hspace=\"3\" border=\"0\" title=\"" . $msg['demandes_new']. "\" ".$onclick." id=\"read".$action->id_action."Img2\" class=\"img_plus\" src='".get_url_icon('notification_new.png')."' style='display:none'>";
				}
				$liste .= 	"</td>
					<td $onclick>".htmlentities($action->workflow->getTypeCommentById($action->type_action),ENT_QUOTES,$charset)."</td>
					<td $onclick>".htmlentities($action->sujet_action,ENT_QUOTES,$charset)."</td>
					<td $onclick>".htmlentities($action->detail_action,ENT_QUOTES,$charset)."</td>	
					<td ><span id='statut_".$action->id_action."' dynamics='demandes,statut' dynamics_params='selector'>".htmlentities($action->workflow->getStateCommentById($action->statut_action),ENT_QUOTES,$charset)."</span></td>
					<td $onclick>".htmlentities(formatdate($action->date_action),ENT_QUOTES,$charset)."</td>
					<td $onclick>".htmlentities(formatdate($action->deadline_action),ENT_QUOTES,$charset)."</td>
					<td $onclick>".htmlentities($action->getCreateur($action->actions_num_user,$action->actions_type_user),ENT_QUOTES,$charset)."</td>
					
					<td ><span dynamics='demandes,temps' dynamics_params='text' id='temps_".$action->id_action."'>".htmlentities($action->time_elapsed.$msg['demandes_action_time_unit'],ENT_QUOTES,$charset)."</span></td>
					<td id='up_temps_".$action->id_action."' style=\"display:none\"></td>
					
					<td><span dynamics='demandes,cout' dynamics_params='text' id='cout_".$action->id_action."'>".htmlentities($action->cout,ENT_QUOTES,$charset).$pmb_gestion_devise."</span></td>
					<td id='up_cout_".$action->id_action."' style=\"display:none\"></td>
					
					<td><span dynamics='demandes,progression' dynamics_params='text' id='progression_".$action->id_action."' >
						<img src='".get_url_icon('jauge.png')."' style='height:16px;' width=\"".$action->progression_action."%\" title='".$action->progression_action."%' />
						</span>
					</td>
					<td $onclick>".sizeof($action->notes)."</td>
					
					<td><input type='checkbox' id='chk_action_".$id_demande."[".$action->id_action."]' name='chk_action_".$id_demande."[]' value='".$action->id_action."'/></td>
				"; 
				$liste .= "</tr>";
				
				if($allow_expand){
					//Le détail de l'action, contient les notes
					$liste .="<tr id=\"note".$action->id_action."Child\" style=\"display:none\">
					<td></td>
					<td colspan=\"13\" id=\"note".$action->id_action."ChildTd\">";
						
					$liste .="</td>
					</tr>";
				}
			}
			$btn_suppr = "<input type='submit' class='bouton' value='$msg[63]' onclick='!!change_action_form!! this.form.act.value=\"suppr_action\"; return verifChkAction(this.form.name,".$id_demande.");'/>";	
		} else {
			$list_actions = str_replace('!!expand_header!!',"",$list_actions);
			$liste .= "<tr><td colspan=\"13\">".$msg['demandes_action_liste_vide']."</td></tr>";
			$btn_suppr = "";
		}
		
		if(!$last_modified){
			$list_actions = str_replace('!!last_modified!!','',$list_actions);
		}
		
		$list_actions = str_replace('!!iddemande!!',$id_demande,$list_actions);
		$list_actions = str_replace('!!btn_suppr!!',$btn_suppr,$list_actions);
		$list_actions = str_replace('!!liste_action!!',$liste,$list_actions);
		
		if($from_ajax) {
			$list_actions = str_replace('!!change_action_form!!','this.form.action="./demandes.php?categ=action";',$list_actions);
		} else {
			$list_actions = str_replace('!!change_action_form!!','',$list_actions);
		}
		
		if($allow_expand){
			$script="
				if(document.getElementById('last_modified').value!=0){
					window.onload(expand_note('note'+document.getElementById('last_modified').value,document.getElementById('last_modified').value, true));
				}
			";
		} else {
			$script="";
		}
		$list_actions = str_replace('!!script_expand!!',$script,$list_actions);
		
		return $list_actions;
	}
	
	
	/*
	 * Suppression d'une action 
	 */
	public static function delete(demandes_actions $action){
		
		global $dbh,$chk;
		
		if($action->id_action){
			$action->fetch_data($action->id_action,false);
			if(sizeof($action->notes)){
				foreach($action->notes as $note){
					demandes_notes::delete($note);
				}
			}
			
			$req = "delete from demandes_actions where id_action='".$action->id_action."'"; 
			pmb_mysql_query($req,$dbh);

			$q = "delete ed,eda from explnum_doc ed join explnum_doc_actions eda on ed.id_explnum_doc=eda.num_explnum_doc where eda.num_action=$action->id_action";
			pmb_mysql_query($q, $dbh);
			audit::delete_audit(AUDIT_ACTION,$action->id_action);
 		}		
	}
	
	/*
	 * Liste des actions Questions/Réponses ouverte ou en attente
	 */
	public function show_com_form(){
		global $form_communication, $dbh, $charset, $msg;
		
		$list = "";
		$req_dmde = "select id_demande, titre_demande from demandes
			join demandes_actions on num_demande=id_demande
			join demandes_users du on du.num_demande=id_demande
			and type_action=1 
			and (statut_action=1 or statut_action=2)
			and num_user='".SESSuserid."' group by id_demande";
		$res_dmde=pmb_mysql_query($req_dmde,$dbh);
		if(pmb_mysql_num_rows($res_dmde)){
			while(($dmde=pmb_mysql_fetch_object($res_dmde))){
				$dmde_action = "onclick=\"document.location='./demandes.php?categ=gestion&act=see_dmde&iddemande=".$dmde->id_demande."'\"";
				$list .= "<tr id='demande_$dmde->id_demande' $dmde_action style='cursor: pointer'>";
				$list .= "<td colspan=8>".htmlentities($dmde->titre_demande,ENT_QUOTES,$charset)."</td>";
				$list .= "</tr>";	
				$req_act="select id_action, sujet_action,detail_action,date_action, deadline_action, temps_passe, cout, progression_action, actions_read_gestion 
				from demandes_actions 
				where num_demande='".$dmde->id_demande."'
				and type_action=1 
				and (statut_action=1 or statut_action=2)"; 
				$res_act=pmb_mysql_query($req_act,$dbh);
				if(pmb_mysql_num_rows($res_act)){						
					$parity=1;						
					while(($com = pmb_mysql_fetch_object($res_act))){
						if ($parity % 2) {
							$pair_impair = "even";
						} else {
							$pair_impair = "odd";
						}
						$parity += 1;
						$tr_javascript = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
						$action = "onclick=\"document.location='./demandes.php?categ=action&act=see&idaction=".$com->id_action."'\"";
						$list .= 
						"<tr class='$pair_impair' id='act_$com->id_action' $tr_javascript style='cursor: pointer'>
					 		<td>&nbsp;</td>
					 		<td $action>".htmlentities($com->sujet_action,ENT_QUOTES,$charset)."</td>
							<td $action>".htmlentities($com->detail_action,ENT_QUOTES,$charset)."</td>				
							<td $action>".htmlentities(formatdate($com->date_action),ENT_QUOTES,$charset)."</td>
							<td $action>".htmlentities($com->temps_passe.$msg['demandes_action_time_unit'],ENT_QUOTES,$charset)."</td>
							<td $action><img src='".get_url_icon('jauge.png')."' style='height:16px;' width=\"".$com->progression_action."%\" title='".$com->progression_action."%' /></td>
							<td><input type='checkbox' id='chk[".$com->id_action."]' name='chk[]' value='".$com->id_action."'></td>
						</tr>";
					}
				} else $list = "<tr><td>".htmlentities($msg['demandes_no_com'],ENT_QUOTES,$charset)."</td></tr>";
			}
		} else $list = "<tr><td>".htmlentities($msg['demandes_no_com'],ENT_QUOTES,$charset)."</td></tr>";
		
		$btn_action = "<input type='submit' class='bouton' name='close_fil' id='close_fil' value='".$msg['demandes_action_close_fil']."' onclick='this.form.act.value=\"close_fil\"'>";
		$form_communication=str_replace('!!btn_action!!',$btn_action,$form_communication);
		$form_communication=str_replace('!!form_title!!',$msg['demandes_action_com'],$form_communication);
		$form_communication=str_replace('!!liste_comm!!',$list,$form_communication);
		
		$form_communication=str_replace('!!action!!','demandes.php?categ=action&sub=com',$form_communication);
		
		print $form_communication;
	}
	
	/*
	 * Liste des RDV planifiés
	 */
	public function show_planning_form(){
		global $form_communication, $dbh, $charset, $msg;
		
		
		$req_dmde = "select id_demande, titre_demande from demandes
			join demandes_actions on num_demande=id_demande
			join demandes_users du on du.num_demande=id_demande
			and type_action=4 
			and statut_action=1 
			and num_user='".SESSuserid."' group by id_demande";
		$res_dmde=pmb_mysql_query($req_dmde,$dbh);
		if(pmb_mysql_num_rows($res_dmde)){
			while(($dmde=pmb_mysql_fetch_object($res_dmde))){
				$dmde_action = "onclick=\"document.location='./demandes.php?categ=gestion&act=see_dmde&iddemande=".$dmde->id_demande."'\"";
				$list .= "<tr id='demande_$dmde->id_demande' $dmde_action style='cursor: pointer'>";
				$list .= "<td colspan=8>".htmlentities($dmde->titre_demande,ENT_QUOTES,$charset)."</td>";
				$list .= "</tr>";	
				$req_act="select id_action, sujet_action,detail_action,date_action, deadline_action, temps_passe, cout, progression_action, actions_read_gestion  
				from demandes_actions 
				where num_demande='".$dmde->id_demande."'
				and type_action=4 
				and statut_action=1"; 
				$res_act=pmb_mysql_query($req_act,$dbh);
				if(pmb_mysql_num_rows($res_act)){						
					$parity=1;						
					while(($com = pmb_mysql_fetch_object($res_act))){
						if ($parity % 2) {
							$pair_impair = "even";
						} else {
							$pair_impair = "odd";
						}
						$parity += 1;
						$tr_javascript = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
						$action = "onclick=\"document.location='./demandes.php?categ=action&act=see&idaction=".$com->id_action."'\"";
						$list .= 
						"<tr class='$pair_impair' id='act_$com->id_action' $tr_javascript style='cursor: pointer'>
					 		<td>&nbsp;</td>
					 		<td $action>".htmlentities($com->sujet_action,ENT_QUOTES,$charset)."</td>
							<td $action>".htmlentities($com->detail_action,ENT_QUOTES,$charset)."</td>				
							<td $action>".htmlentities(formatdate($com->date_action),ENT_QUOTES,$charset)."</td>
							<td $action>".htmlentities($com->temps_passe.$msg['demandes_action_time_unit'],ENT_QUOTES,$charset)."</td>
							<td $action><img src='".get_url_icon('jauge.png')."' style='height:16px;' width=\"".$com->progression_action."%\" title='".$com->progression_action."%' /></td>
							<td><input type='checkbox' id='chk[".$com->id_action."]' name='chk[]' value='".$com->id_action."'></td>
						</tr>";
					}
				} else $list = "<tr><td>".htmlentities($msg['demandes_no_rdv_plan'],ENT_QUOTES,$charset)."</td></tr>";
			}
		} else $list = "<tr><td>".htmlentities($msg['demandes_no_rdv_plan'],ENT_QUOTES,$charset)."</td></tr>";
		
		$btn_action = "<input type='submit' class='bouton' name='close_rdv' id='close_rdv' value='".$msg['demandes_action_close_rdv']."'  onclick='this.form.act.value=\"close_rdv\"'>";
		$form_communication=str_replace('!!btn_action!!',$btn_action,$form_communication);
		$form_communication=str_replace('!!form_title!!',$msg['demandes_menu_rdv_planning'],$form_communication);
		$form_communication=str_replace('!!liste_comm!!',$list,$form_communication);
		$form_communication=str_replace('!!action!!','demandes.php?categ=action&sub=rdv_plan',$form_communication);
		print $form_communication;
	}
	
	/*
	 * Formulaire qui gère l'affichage des actions
	 */
	public function show_rdv_val_form(){
		global $form_communication, $dbh, $charset, $msg;
		
		$req_dmde = "select id_demande, titre_demande from demandes
			join demandes_actions on num_demande=id_demande
			join demandes_users du on du.num_demande=id_demande
			and type_action=4 
			and statut_action=2 
			and num_user='".SESSuserid."' group by id_demande";
		$res_dmde=pmb_mysql_query($req_dmde,$dbh);
		if(pmb_mysql_num_rows($res_dmde)){
			while(($dmde=pmb_mysql_fetch_object($res_dmde))){
				$dmde_action = "onclick=\"document.location='./demandes.php?categ=gestion&act=see_dmde&iddemande=".$dmde->id_demande."'\"";
				$list .= "<tr id='demande_$dmde->id_demande' $dmde_action style='cursor: pointer'>";
				$list .= "<td colspan=8>".htmlentities($dmde->titre_demande,ENT_QUOTES,$charset)."</td>";
				$list .= "</tr>";	
				$req_act="select id_action, sujet_action,detail_action,date_action, deadline_action, temps_passe, cout, progression_action, actions_read_gestion 
				from demandes_actions 
				where num_demande='".$dmde->id_demande."'
				and type_action=4 
				and statut_action=2"; 
				$res_act=pmb_mysql_query($req_act,$dbh);
				if(pmb_mysql_num_rows($res_act)){						
					$parity=1;						
					while(($com = pmb_mysql_fetch_object($res_act))){
						if ($parity % 2) {
							$pair_impair = "even";
						} else {
							$pair_impair = "odd";
						}
						$parity += 1;
						$tr_javascript = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
						$action = "onclick=\"document.location='./demandes.php?categ=action&act=see&idaction=".$com->id_action."'\"";
						$list .= 
						"<tr class='$pair_impair' id='act_$com->id_action' $tr_javascript style='cursor: pointer'>
					 		<td>&nbsp;</td>
					 		<td $action>".htmlentities($com->sujet_action,ENT_QUOTES,$charset)."</td>
							<td $action>".htmlentities($com->detail_action,ENT_QUOTES,$charset)."</td>				
							<td $action>".htmlentities(formatdate($com->date_action),ENT_QUOTES,$charset)."</td>
							<td $action>".htmlentities($com->temps_passe.$msg['demandes_action_time_unit'],ENT_QUOTES,$charset)."</td>
							<td $action><img src='".get_url_icon('jauge.png')."' style='height:16px;' width=\"".$com->progression_action."%\" title='".$com->progression_action."%' /></td>
							<td><input type='checkbox' id='chk[".$com->id_action."]' name='chk[]' value='".$com->id_action."'></td>
						</tr>";
					}
				} else $list = "<tr><td>".htmlentities($msg['demandes_no_rdv_val'],ENT_QUOTES,$charset)."</td></tr>";
			}
		} else $list = "<tr><td>".htmlentities($msg['demandes_no_rdv_val'],ENT_QUOTES,$charset)."</td></tr>";
		
		$btn_action = "<input type='submit' class='bouton' name='val_rdv' id='val_rdv' value='".$msg['demandes_action_valid_rdv']."' onclick='this.form.act.value=\"val_rdv\"'>";
		$form_communication=str_replace('!!btn_action!!',$btn_action,$form_communication);
		$form_communication=str_replace('!!form_title!!',$msg['demandes_menu_rdv_a_valide'],$form_communication);
		$form_communication=str_replace('!!liste_comm!!',$list,$form_communication);
		$form_communication=str_replace('!!action!!','demandes.php?categ=action&sub=rdv_val',$form_communication);
		print $form_communication;
	}
	
	/*
	 * Ferme toutes les discussions en cours
	 */
	public function close_fil(){
		global $chk, $dbh;
		
		for($i=0;$i<count($chk);$i++){		
			$req = "update demandes_actions set statut_action=3 where id_action='".$chk[$i]."'";
			pmb_mysql_query($req,$dbh);
		}
	}
	
	/*
	 * Annule tous les RDV
	 */
	public function close_rdv(){
		global $chk, $dbh;
		
		for($i=0;$i<count($chk);$i++){		
			$req = "update demandes_actions set statut_action=3 where id_action='".$chk[$i]."'";
			pmb_mysql_query($req,$dbh);
		}
	}
	
	/*
	 * Valide tous les RDV
	 */
	public function valider_rdv(){
		global $chk, $dbh;
		
		for($i=0;$i<count($chk);$i++){		
			$req = "update demandes_actions set statut_action=1 where id_action='".$chk[$i]."'";
			pmb_mysql_query($req,$dbh);
		}
	}
	
	/*
	 * Retourne le nom de celui qui a créé l'action
	 */
	public function getCreateur($id_createur,$type_createur=0){
		global $dbh;
		
		if(!$type_createur)
			$rqt = "select concat(prenom,' ',nom) as nom, username from users where userid='".$id_createur."'";
		else 
			$rqt = "select concat(empr_prenom,' ',empr_nom) as nom from empr where id_empr='".$id_createur."'";
		
		$res = pmb_mysql_query($rqt,$dbh);
		if(pmb_mysql_num_rows($res)){		
			$createur = pmb_mysql_fetch_object($res);			
			return (trim($createur->nom)  ? $createur->nom : $createur->username );
		}		
		return "";
	}
	
	/*
	 * fonction qui renvoie un booléen indiquant si une action a été lue ou pas
	*/
	public static function read($action,$side="_gestion"){
		global $dbh;
		$read  = false;
		$query = "SELECT actions_read".$side." FROM demandes_actions WHERE id_action=".$action->id_action;
		$result = pmb_mysql_query($query,$dbh);
		if($result){
			$tmp = pmb_mysql_result($result,0,0);
			if($tmp == 0){
				$read = true;
			}
		}
		return $read;
	}
	
	/*
	 * Change l'alerte de l'action : si elle est lue, elle passe en non lue et inversement
	*/
	public static function change_read($action,$side="_gestion"){
		global $dbh;
		
		$read = demandes_actions::read($action,$side);
		$value = "";
		if($read){
			$value = 1;
		} else {
			$value = 0;
		}
		$query = "UPDATE demandes_actions SET actions_read".$side."=".$value." WHERE id_action=".$action->id_action;
		if(pmb_mysql_query($query,$dbh)){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * changement forcé de la mention "lue" ou "pas lue" de l'action
	 * true => action est déjà lue doc pas d'alerte
	 * false => alerte
	*/
	public static function action_read($id_action,$booleen=true,$side="_gestion"){
		global $dbh;
		
		$value = "";
		if($booleen){
			$value = 0;
		} else {
			$value = 1;
		}
		$query = "UPDATE demandes_actions SET actions_read".$side."=".$value." WHERE id_action=".$id_action;
		if(pmb_mysql_query($query,$dbh)){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Met à jour les alertes sur l'action et la demande dont dépend la note
	*/
	public static function action_majParentEnfant($id_action,$id_demande,$side="_gestion"){
		global $dbh;
	
		$ok = false;
		if($id_action){
				
			$select = "SELECT actions_read".$side." FROM demandes_actions WHERE id_action=".$id_action;
			$result  = pmb_mysql_query($select,$dbh);
			$read = pmb_mysql_result($result,0,0);
				
			if($read == 1){
				if(demandes::demande_read($id_demande,false)){
					$ok = true;
				}
			} else {
				// maj notes : si l'action est lue, on met à 0 toutes les notes
				$query = "UPDATE demandes_notes SET notes_read".$side." = 0 WHERE num_action=".$id_action;			
				if(pmb_mysql_query($query,$dbh)){
					// maj demande : controle s'il existe des actions non lues pour la demande en cours
					$query = "SELECT actions_read".$side." FROM demandes_actions WHERE num_demande=".$id_demande." AND id_action != ".$id_action." AND actions_read".$side."=1";
					$result = pmb_mysql_query($query,$dbh);
					if(pmb_mysql_num_rows($result)){
						$ok = demandes::demande_read($id_demande,false,$side);
					} else {
						$ok = demandes::demande_read($id_demande,true,$side);
					}
				}
			}
		}
		return $ok;
	}
}
?>