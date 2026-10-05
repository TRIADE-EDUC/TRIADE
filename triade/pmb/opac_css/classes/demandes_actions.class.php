<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_actions.class.php,v 1.24.4.3 2025/05/16 14:07:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/demandes_notes.class.php");
require_once($class_path."/demandes.class.php");
require_once($class_path."/explnum_doc.class.php");
require_once($class_path."/workflow.class.php");
require_once($class_path."/audit.class.php");
require_once($include_path."/templates/demandes_actions.tpl.php");

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
	public $demande;
	public $libelle_demande = '';
	public $actions_num_user = 0;
	public $actions_type_user = 0;
	public $createur_action ="";
	public $list_type = array();
	public $list_statut = array();
	public $workflow = array();
	public $notes = array();
	public $actions_read_gestion = 0; // alerte sur la lecture de l'action en gestion par l'utilisateur
	public $actions_read_opac = 0; // alerte sur la lecture de l'action en opac par le lecteur
	public $last_modified=0;
	/*
	 * Constructeur
	 */
	public function __construct($id=0,$lazzy_load=true){
		$id = intval($id);
		$this->fetch_data($id,$lazzy_load);
	}

	public function fetch_data($id=0,$lazzy_load=true){
		global $iddemande;

		if($this->id_action && !$id){
			$id=$this->id_action;
		}elseif(!$this->id_action && $id){
			$this->id_action=$id;
		}
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
		if($this->id_action){
			$req = "select id_action,type_action,statut_action, sujet_action,
			detail_action,date_action,deadline_action,temps_passe, cout, progression_action, prive_action, num_demande, titre_demande,
			actions_num_user,actions_type_user,actions_read_gestion,actions_read_opac
			from demandes_actions
			join demandes on num_demande=id_demande
			where id_action='".$this->id_action."'";
			$res=pmb_mysql_query($req);
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
			}
		}

		if(empty($this->workflow)){
			$this->workflow = new workflow('ACTIONS','INITIAL');
			$this->list_type = $this->workflow->getTypeList();
			$this->list_statut = $this->workflow->getStateList();
		}
		$iddemande = intval($iddemande);
		if($iddemande) {
			$this->num_demande = $iddemande;
			$req = "select titre_demande from demandes where id_demande='".$iddemande."'";
			$res = pmb_mysql_query($req);
			$this->libelle_demande = pmb_mysql_result($res,0,0);
		}

		//On remonte les notes
		if($this->id_action){
			$this->notes=array();
			//On charge la liste d'id des notes
			$query='SELECT id_note,date_note FROM demandes_notes WHERE num_action='.$this->id_action.' AND prive!=1 ORDER BY id_note ASC';
			$result=pmb_mysql_query($query);

			while($note=pmb_mysql_fetch_array($result,PMB_MYSQL_ASSOC)){
				if($lazzy_load){
					$this->notes[$note['id_note']]=new stdClass();
					$this->notes[$note['id_note']]->id_note=$note['id_note'];
					$this->notes[$note['id_note']]->date_note=$note['date_note'];
					$this->notes[$note['id_note']]->id_action=$this->id_action;
				}else{
					$this->notes[$note['id_note']]=new demandes_note($note['id_note'],$this->id_action);
				}


			}
			$this->last_modified=$this->get_last_modified_note();
		}
	}

	/*
	 * Cherche la note la plus récente grace à l'audit
	 */
	public function get_last_modified_note(){
		$temp=0;
		foreach($this->notes as $note){
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
		global $form_modif_action,$msg, $charset,$type_action;

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

		} else {
			$form_modif_action = str_replace('!!idaction!!','',$form_modif_action);
			$form_modif_action = str_replace('!!iddemande!!',$this->num_demande,$form_modif_action);

			$form_modif_action = str_replace('!!sujet!!','',$form_modif_action);
			$form_modif_action = str_replace('!!detail!!','',$form_modif_action);
			$date = formatdate(today());
			$date_debut=date("Y-m-d",time());
			$form_modif_action = str_replace('!!date_fin_btn!!',$date,$form_modif_action);
			$form_modif_action = str_replace('!!date_debut_btn!!',$date,$form_modif_action);
			$form_modif_action = str_replace('!!date_debut!!',$date_debut,$form_modif_action);
			$form_modif_action = str_replace('!!date_fin!!',$date_debut,$form_modif_action);

			$type=$this->getType($type_action);
			$form_modif_action = str_replace('!!libelle_type!!',$type['comment'],$form_modif_action);
			$form_modif_action = str_replace('!!idtype!!',$type['id'],$form_modif_action);

			$statut=$this->getStatut();
			$form_modif_action = str_replace('!!libelle_statut!!',$statut['comment'],$form_modif_action);
			$form_modif_action = str_replace('!!idstatut!!',$statut['id'],$form_modif_action);

			$form_modif_action = str_replace('!!time_elapsed!!','',$form_modif_action);
			$form_modif_action = str_replace('!!progression!!','',$form_modif_action);
			$form_modif_action = str_replace('!!ck_prive!!','',$form_modif_action);
			$form_modif_action = str_replace('!!cout!!','',$form_modif_action);

		}

		$form_modif_action = str_replace('!!form_action!!',"./empr.php?tab=request&lvl=list_dmde&sub=save_action",$form_modif_action);
		$form_modif_action = str_replace('!!cancel_action!!',"document.location='./empr.php?tab=request&lvl=list_dmde&sub=open_demande&iddemande=$this->num_demande#fin'",$form_modif_action);

		print $form_modif_action;
	}

	public function getStatut($idstatut=0){
		foreach($this->list_statut as $key=>$value){
			if($idstatut){
				if($value['id']==$idstatut){
					return $value;
				}
			}else{
				if($value['default']==true){
					return $value;
				}
			}
		}
	}

	public function getType($idtype=0){
		foreach($this->list_type as $key=>$value){
			if($idtype){
				if($value['id']==$idtype){
					return $value;
				}
			}else{
				if($value['default']==true){
					return $value;
				}
			}
		}
	}

	/*
	 * Formulaire de consultation d'une action
	 */
	public function show_consultation_form(){
		global $idetat,$form_consult_action, $msg, $charset, $pmb_gestion_devise;

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

		$form_consult_action = str_replace('!!params_retour!!','&iddemande='.$this->num_demande,$form_consult_action);

		print $form_consult_action;

		//Notes
		print demandes_notes::show_dialog($this->notes,$this->id_action,$this->num_demande,'demandes-show_consultation_form');

		// Annulation de l'alerte sur l'action en cours après lecture des nouvelles notes si c'est la personne à laquelle est affectée l'action qui la lit
		$this->actions_read_opac = demandes_actions::action_read($this->id_action,true,"_opac");
		// Mise à jour de la demande dont est issue l'action
		demandes_actions::action_majParentEnfant($this->id_action,$this->num_demande,"_opac");
	}

	/*
	 * Retourne un sélecteur avec les types d'action
	 */
	public function getTypeSelector($idtype=0){
		global $charset, $msg, $default;

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

	public function set_properties_from_form() {
		global $idaction,$sujet, $idtype,$id_empr ,$idstatut, $opac_demandes_no_action;
		global $date_debut, $date_fin, $detail;
		global $time_elapsed, $progression,$cout,$iddemande, $ck_prive;

		if($opac_demandes_no_action) return;

		$this->id_action = intval($idaction);
		$this->num_demande = intval($iddemande);
		$this->sujet_action = stripslashes($sujet);
		$this->type_action = intval($idtype);
		$this->statut_action = intval($idstatut);
		$this->date_action = $date_debut;
		$this->deadline_action = $date_fin;
		$this->detail_action = stripslashes($detail);
		$this->time_elapsed = $time_elapsed;
		$this->progression_action = intval($progression);
		$this->cout = $cout;
		$this->prive_action = $ck_prive;
		$this->actions_type_user = '1';
		$this->actions_num_user = $id_empr;
	}

	/*
	 * Insertion/Modification d'une action
	*/
	public function save(){
		global $pmb_type_audit, $opac_demandes_no_action, $id_empr;
		if($opac_demandes_no_action) return;

		if($this->id_action){
			//MODIFICATION
			$query = "UPDATE demandes_actions SET
			sujet_action='".addslashes($this->sujet_action)."',
			type_action='".$this->type_action."',
			statut_action='".$this->statut_action."',
			detail_action='".addslashes($this->detail_action)."',
			date_action='".$this->date_action."',
			deadline_action='".$this->deadline_action."',
			temps_passe='".$this->time_elapsed."',
			cout='".$this->cout."',
			progression_action='".$this->progression_action."',
			prive_action='".$this->prive_action."',
			num_demande='".$this->num_demande."',
			actions_read_gestion='1',
			actions_read_opac='1'
			WHERE id_action='".$this->id_action."'";

			pmb_mysql_query($query);
			//audit
			if($pmb_type_audit) audit::insert_modif(AUDIT_ACTION,$this->id_action);

		} else {
			//CREATION
			$query = "INSERT INTO demandes_actions SET
			sujet_action='".addslashes($this->sujet_action)."',
			type_action='".$this->type_action."',
			statut_action='".$this->statut_action."',
			detail_action='".addslashes($this->detail_action)."',
			date_action='".$this->date_action."',
			deadline_action='".$this->deadline_action."',
			temps_passe='".$this->time_elapsed."',
			cout='".$this->cout."',
			progression_action='".$this->progression_action."',
			prive_action='".$this->prive_action."',
			num_demande='".$this->num_demande."',
			actions_num_user='".$this->actions_num_user."',
			actions_type_user='".$this->actions_type_user."',
			actions_read_gestion='1',
			actions_read_opac='1'
			";
			pmb_mysql_query($query);
			$this->id_action = pmb_mysql_insert_id();
			$this->actions_num_user = $id_empr;
			$this->actions_type_user = 1;
			// audit
			if($pmb_type_audit) audit::insert_modif(AUDIT_ACTION,$this->id_action);

			//Création d'une note automatiquement
			if($this->detail_action && $this->detail_action!==""){
				$note=new demandes_note();
				$note->num_action=$this->id_action;
				$note->date_note=date("Y-m-d h:i:s",time());
				$note->rapport=0;
				$note->contenu=$this->detail_action;
				$note->notes_type_user=$this->actions_type_user;
				$note->notes_num_user=$this->actions_num_user;
				$note->save();
			}
		}
	}

	/*
	 * Changement de statut d'une action
	*/
	public function change_statut($statut){
		global $pmb_type_audit;

		$query = "update demandes_actions set statut_action=$statut where id_action='".$this->id_action."'";
		pmb_mysql_query($query);

		if($pmb_type_audit) audit::insert_modif(AUDIT_ACTION,$this->id_action);
	}



	/*
	 * Affichage de la liste des actions
	 */
	public static function show_list_actions($actions,$id_demande,$last_modified=0,$allow_expand=true){
		global $form_liste_action,$msg, $pmb_gestion_devise, $charset, $pmb_type_audit, $ck_vue,$form_see_docnum;

		$liste ="";
		if (!empty($actions)) {
			$parity=1;
			foreach($actions as $id_action=>$action){

				if ($parity % 2) {
					$pair_impair = "even";
				} else {
					$pair_impair = "odd";
				}
				$parity += 1;
				$tr_javascript = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";

				//On ouvre la derniere conversation
				if($last_modified==$action->id_action){
					$form_liste_action = str_replace('!!last_modified!!',$last_modified,$form_liste_action);
				}

				// affichage en gras si nouveauté pour l'opac du côté des notes ou des actions + icone
				$style ="";
				if($action->actions_read_opac == 1){
					$style=" style='cursor: pointer; font-weight:bold;width:100%;height:30px;margin:5px;border-radius: 6px;'";
				} else {
					$style=" style='cursor: pointer;width:100%;margin:5px;border-radius: 6px;'";
				}

				if($allow_expand){
					$onclick="onclick=\"expand_note('note".$action->id_action."','$action->id_action', true); return false;\"";
				}else{
					$onclick="onclick=\"document.location='./empr.php?tab=request&lvl=list_dmde&sub=open_demande&iddemande=$id_demande&last_modified=$action->id_action'\"";
				}

				$liste.="<div $onclick class='form-list-actions ".$pair_impair."' ".$tr_javascript.$style." >";
				$liste.="<div style=\"float:left;padding-top: 6px;\">";
				if($action->actions_read_opac == 1){
					$liste .= "<img ".$onclick." title=\"\" id=\"read".$action->id_action."Img1\" class=\"img_plus\" src=\"".get_url_icon('notification_empty.png')."\" style='display:none;margin:0 5px'>
								<img style=\"margin:0 5px\" ".$onclick." title=\"\" id=\"read".$action->id_action."Img2\" class=\"img_plus\" src=\"".get_url_icon('notification_new.png')."\">";
				} else {
					$liste .= "<img style=\";margin:0 5px\" ".$onclick." title=\"\" id=\"read".$action->id_action."Img1\" class=\"img_plus\" src=\"".get_url_icon('notification_empty.png')."\" >
								<img ".$onclick." title=\"\" id=\"read".$action->id_action."Img2\" class=\"img_plus\" src=\"".get_url_icon('notification_new.png')."\" style='display:none;margin:0 5px'>";
				}

				foreach($action->workflow->getTypeList() as $id=>$value){
					if($value['id']==$action->type_action && $value['image']){
						$liste.="<img title=\"".$value['comment']."\" src=\"".get_url_icon($value['image'])."\" style=\"height:16px;margin:0 5px\" />";
					}
				}
				$liste.="</div>";
				$date_action = "";
				if($action->date_action != "") {
					$date_action = DateTime::createFromFormat('Y-m-d', $action->date_action);
					$date_action = $date_action->format('d/m/Y');
				}
				$liste.="<p style=\"padding-top: 8px;\"> " . $date_action . " #".$action->getCreateur($action->actions_num_user,$action->actions_type_user)." : <i>".htmlentities($action->sujet_action,ENT_QUOTES,$charset)."</i> ".htmlentities($action->detail_action,ENT_QUOTES,$charset)."</p>";

				$liste.="</div>";

				if($allow_expand){
					//Le détail de l'action, contient les notes
					$liste .="<div id=\"note".$action->id_action."Child\" style=\"display:none\">

					<div id=\"note".$action->id_action."ChildTd\" class='dijit'>";

					$liste .="</div>";

					$liste .= self::show_action_docnum($action);

					$liste .="</div>";
				}
			}
		} else {
			$liste .= "<div>".$msg['demandes_action_liste_vide']."</div>";
		}

		if(!$last_modified){
			$form_liste_action = str_replace('!!last_modified!!','',$form_liste_action);
		}

		$form_liste_action = str_replace('!!iddemande!!',$id_demande,$form_liste_action);
		$form_liste_action = str_replace('!!liste_action!!',$liste,$form_liste_action);

		if($allow_expand){
			$script="
				if(document.getElementById('last_modified').value!=0){
					window.addEventListener('load',expand_note('note'+document.getElementById('last_modified').value,document.getElementById('last_modified').value, true));
				}
			";
		} else {
			$script="";
		}
		$form_liste_action = str_replace('!!script_expand!!',$script,$form_liste_action);

		return $form_liste_action;
	}

	public static function show_action_docnum($action){
	    global $msg,$form_see_docnum, $charset;

		//Documents Numériques
		$query = "SELECT * FROM explnum_doc
		JOIN explnum_doc_actions ON num_explnum_doc=id_explnum_doc
		WHERE num_action='".$action->id_action."'";

		$result = pmb_mysql_query($query);

		if(pmb_mysql_num_rows($result)){
			$tab_docnum = array();
			while(($docnums = pmb_mysql_fetch_array($result))){
				$tab_docnum[] = $docnums;
			}
			$explnum_doc = new explnum_doc();
			$liste_docnum = $explnum_doc->show_docnum_table($tab_docnum);
			$form_see_docnum = str_replace('!!list_docnum!!',$liste_docnum,$form_see_docnum);
		} else {
			$form_see_docnum = str_replace('!!list_docnum!!',htmlentities($msg['demandes_action_no_docnum'],ENT_QUOTES,$charset),$form_see_docnum);
		}
		$form_see_docnum = str_replace('!!idaction!!',$action->id_action,$form_see_docnum);

		return $form_see_docnum;
	}

	/*
	 * Suppression d'une action
	 */
	public static function delete(demandes_actions $action){
		global $chk;

		if($action->id_action){
			$action->fetch_data($action->id_action,false);
			if(is_countable($action->notes) && sizeof($action->notes)){
				foreach($action->notes as $note){
					demandes_note::delete($note);
				}
			}

			$req = "delete from demandes_actions where id_action='".$action->id_action."'";
			pmb_mysql_query($req);

			$q = "delete ed,eda from explnum_doc ed join explnum_doc_actions eda on ed.id_explnum_doc=eda.num_explnum_doc where eda.num_action=$action->id_action";
			pmb_mysql_query($q);
			audit::delete_audit(AUDIT_ACTION, $action->id_action);
 		}
	}

	/*
	 * Ferme toutes les discussions en cours
	 */
	public function close_fil(){
		global $chk;

		for($i=0;$i<count($chk);$i++){
			$req = "update demandes_actions set statut_action=3 where id_action='".$chk[$i]."'";
			pmb_mysql_query($req);
		}
	}

	/*
	 * Annule tous les RDV
	 */
	public function close_rdv(){
		global $chk;

		for($i=0;$i<count($chk);$i++){
			$req = "update demandes_actions set statut_action=3 where id_action='".$chk[$i]."'";
			pmb_mysql_query($req);
		}
	}

	/*
	 * Valide tous les RDV
	 */
	public function valider_rdv(){
		global $chk;

		for($i=0;$i<count($chk);$i++){
			$req = "update demandes_actions set statut_action=1 where id_action='".$chk[$i]."'";
			pmb_mysql_query($req);
		}
	}

	public function get_demande() {
		if(!isset($this->demande)) {
			$this->demande = new demandes($this->num_demande);
		}
		return $this->demande;
	}

	/*
	 * Retourne le nom de celui qui a créé l'action
	 */
	public function getCreateur($id_createur,$type_createur=0){
		if(!$type_createur)
			$rqt = "select concat(prenom,' ',nom) as nom, username from users where userid='".$id_createur."'";
		else
			$rqt = "select concat(empr_prenom,' ',empr_nom) as nom from empr where id_empr='".$id_createur."'";

		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$createur = pmb_mysql_fetch_object($res);
			return (trim($createur->nom)  ? $createur->nom : $createur->username );
		}
		return "";
	}

	/*
	 * fonction qui renvoie un booléen indiquant si une action a été lue ou pas
	*/
	public static function read($action,$side="_opac"){
		$read  = false;
		$query = "SELECT actions_read".$side." FROM demandes_actions WHERE id_action=".$action->id_action;
		$result = pmb_mysql_query($query);
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
	public static function change_read($action,$side="_opac"){
		$read = demandes_actions::read($action,$side);
		$value = "";
		if($read){
			$value = 1;
		} else {
			$value = 0;
		}
		$query = "UPDATE demandes_actions SET actions_read".$side."=".$value." WHERE id_action=".$action->id_action;
		if(pmb_mysql_query($query)){
			return true;
		} else {
			return false;
		}
	}

	/*
	 * changement forcé de la mention "lue" ou "pas lue" de l'action
	*/
	public static function action_read($id_action,$booleen=true, $side="_opac"){
		$value = "";
		if($booleen){
			$value = 0;
		} else {
			$value = 1;
		}
		$query = "UPDATE demandes_actions SET actions_read".$side."=".$value." WHERE id_action=".$id_action;
		pmb_mysql_query($query);
	}

	/*
	 * Met à jour les alertes sur l'action et la demande dont dépend la note
	*/
	public static function action_majParentEnfant($id_action,$id_demande,$side="_opac"){
		$ok = false;
		if($id_action){

			$select = "SELECT actions_read".$side." FROM demandes_actions WHERE id_action=".$id_action;
			$result  = pmb_mysql_query($select);
			$read = pmb_mysql_result($result,0,0);

			if($read == 1){
				if(demandes::demande_read($id_demande,false,$side)){
					$ok = true;
				}
			} else {
				// maj notes : si l'action est lue, on met à 0 toutes les notes
				$query = "UPDATE demandes_notes SET notes_read".$side." = 0 WHERE num_action=".$id_action;
				if(pmb_mysql_query($query)){
					// maj demande : controle s'il existe des actions non lues pour la demande en cours
					$query = "SELECT actions_read".$side." FROM demandes_actions WHERE num_demande=".$id_demande." AND id_action != ".$id_action." AND actions_read".$side."=1";
					$result = pmb_mysql_query($query);
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
