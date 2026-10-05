<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_actions.class.php,v 1.55.4.2 2025/05/06 15:25:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
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
	public $demande;
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
			$req = "SELECT titre_demande, allowed_actions FROM demandes JOIN demandes_type ON demandes.type_demande=demandes_type.id_type WHERE id_demande='".$iddemande."'";
			$res = pmb_mysql_query($req);
			$this->libelle_demande = pmb_mysql_result($res,0,0);
			$allowed_actions = pmb_mysql_result($res,0,1);
			if($allowed_actions != '') {
				$this->list_type = unserialize($allowed_actions);
			}
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
	
	public function get_path() {
		global $charset;
		$path = "<a href=./demandes.php?categ=gestion&act=see_dmde&iddemande=$this->num_demande>".htmlentities($this->libelle_demande,ENT_QUOTES,$charset)."</a>";
		
		if($this->id_action) {
			$path .= " > <a href=./demandes.php?categ=action&act=see&idaction=$this->id_action>".htmlentities($this->sujet_action,ENT_QUOTES,$charset)."</a>";
		}
		return $path;
	}
	
	public function get_modif_content_form() {
	    global $msg;
	    global $pmb_gestion_devise;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    
	    if($this->id_action){
	        $type_action_html_node = $this->workflow->getTypeCommentById($this->type_action);
	        $type_action_html_node .= "<input type='hidden' name='idtype' id='idtype' value='$this->type_action' />";
	    } else {
	        $type_action_html_node = $this->getTypeSelector();
	    }
	    $interface_content_form->add_element('idtype', 'demandes_action_type')
	    ->set_class('colonne3')
	    ->add_html_node($type_action_html_node);
	    $interface_content_form->add_element('idstatut', 'demandes_action_statut')
	    ->set_class('colonne3')
	    ->add_html_node($this->getStatutSelector($this->statut_action));
	    $interface_content_form->add_element('sujet', 'demandes_action_sujet')
	    ->add_input_node('text', $this->sujet_action);
	    $interface_content_form->add_element('detail', 'demandes_action_detail')
	    ->add_textarea_node($this->detail_action, 50, 4)
	    ->set_attributes(array('wrap' => 'virtual'));
	    $interface_content_form->add_element('ck_prive', 'demandes_action_privacy', 'flat')
	    ->add_input_node('boolean', $this->prive_action);
	    
	    if(!$this->id_action){
	        $this->date_action = date("Y-m-d",time());
	        $this->deadline_action = date("Y-m-d",time());
	    }
	    $interface_content_form->add_element('date_debut', 'demandes_action_date')
	    ->set_class('colonne3')
	    ->add_input_node('date', $this->date_action);
	    $interface_content_form->add_element('date_fin', 'demandes_action_date_butoir')
	    ->set_class('colonne3')
	    ->add_input_node('date', $this->deadline_action);
	    
	    $interface_content_form->add_element('time_elapsed')
	    ->set_label($msg['demandes_action_time_elapsed']." (".$msg['demandes_action_time_unit'].")")
	    ->set_class('colonne3')
	    ->add_input_node('text', $this->time_elapsed)
	    ->set_class('saisie-20em');
	    $interface_content_form->add_element('cout')
	    ->set_label(sprintf($msg['demandes_action_cout'],$pmb_gestion_devise))
	    ->set_class('colonne3')
	    ->add_input_node('integer', $this->cout);
	    $interface_content_form->add_element('progression', 'demandes_action_progression')
	    ->set_class('colonne3')
	    ->add_input_node('integer', $this->progression_action);
	    
	    $interface_content_form->add_zone('action', '', ['idtype', 'idstatut']);
	    $interface_content_form->add_zone('body', '', ['sujet', 'detail', 'ck_prive']);
	    $interface_content_form->add_zone('dates', '', ['date_debut', 'date_fin']);
	    $interface_content_form->add_zone('additionals_info', '', ['time_elapsed', 'cout', 'progression']);
	    return $interface_content_form->get_display();
	}
	
	/*
	 * Affichage du formulaire de création/modification
	 */
	public function show_modif_form(){
		global $js_modif_action,$msg;
		
		print $js_modif_action;
		print "<h2>".$this->get_path()."</h2>";
		
		$interface_form = new interface_demandes_form('modif_action');
		$interface_form->set_num_demande($this->num_demande);
		if($this->id_action){
			$interface_form->set_label(sprintf($msg['demandes_action_modif'],' : '.$this->sujet_action));
		} else {
			$interface_form->set_label($msg['demandes_action_creation']);
		}
		$interface_form->set_object_id($this->id_action)
		->set_confirm_delete_msg($msg['demandes_confirm_suppr'])
		->set_content_form($this->get_modif_content_form())
		->set_table_name('demandes_actions');
		print $interface_form->get_display();
		print "<div class='row' id='docnum'></div>";
	}
	
	/*
	 * Formulaire de consultation d'une action
	 */
	public function show_consultation_form(){
		global $form_consult_action, $form_see_docnum, $msg, $charset, $pmb_gestion_devise, $pmb_type_audit;
		
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
		$res = pmb_mysql_query($req);
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
	 * Contenu du formulaire d'ajout/modification d'un document numérique
	 */
	public function get_docnum_content_form(){
	    global $explnumdoc_id, $explnum_doc;
	    
	    $explnumdoc_id = intval($explnumdoc_id);
	    $nom = '';
	    $doc_url = '';
	    $prive = 0;
	    $rapport = 0;
	    if($explnumdoc_id){
	        $explnum_doc = new explnum_doc($explnumdoc_id);
	        $nom = $explnum_doc->explnum_doc_nomfichier;
	        $doc_url = $explnum_doc->explnum_doc_url;
	        
	        $query = "select prive, rapport from explnum_doc_actions where num_explnum_doc='".$explnumdoc_id."'";
	        $result = pmb_mysql_query($query);
	        $row = pmb_mysql_fetch_object($result);
	        $prive = $row->prive;
	        $rapport = $row->rapport;
	    }
	    
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('idaction')
	    ->add_input_node('hidden', $this->id_action);
	    $interface_content_form->add_element('f_nom', 'explnum_nom')
	    ->add_input_node('text', $nom);
	    $interface_content_form->add_element('f_fichier', 'explnum_fichier')
	    ->add_input_node('file');
	    $interface_content_form->add_element('f_url', 'demandes_url_docnum')
	    ->add_input_node('text', $doc_url);
	    $interface_content_form->add_element('ck_prive', 'demandes_note_privacy', 'flat')
	    ->add_input_node('boolean', $prive);
	    $interface_content_form->add_element('ck_rapport', 'demandes_docnum_rapport', 'flat')
	    ->add_input_node('boolean', $rapport);
	    return $interface_content_form->get_display();
	}
	
	/*
	 * Formulaire d'ajout/modification d'un document numérique
	 */
	public function show_docnum_form(){
		global $msg, $explnumdoc_id;
		
		print "<h2>".$this->get_path()."</h2>";
		
		$interface_form = new interface_demandes_form('explnum');
		$interface_form->set_num_action($this->id_action);
		$interface_form->set_enctype('multipart/form-data');
		$explnumdoc_id = intval($explnumdoc_id);
		if($explnumdoc_id){
			$interface_form->set_label($msg['explnum_data_doc']);
		} else {
			$interface_form->set_label($msg['explnum_ajouter_doc']);
		}
		$interface_form->set_object_id($explnumdoc_id)
		->set_confirm_delete_msg($msg['demandes_confirm_suppr'])
		->set_content_form($this->get_docnum_content_form())
		->set_table_name('explnum_doc');
		print $interface_form->get_display();
	}
	
	/*
	 * Retourne un sélecteur avec les types d'action
	 */
	public function getTypeSelector($idtype=0){
		global $charset, $msg, $default;
		
		$selector = "<select name='idtype'>";
		$select="";
		//Si idtype vide, on prend le type par defaut
		if($idtype == 0) {
			foreach($this->list_type as $type) {
				if($type['default'] == 1) {
					$idtype = $type['id'];
					break;
				}
			}
		}
		if($default) $selector .= "<option value='0'>".htmlentities($msg['demandes_action_all_types'],ENT_QUOTES,$charset)."</option>";
		foreach($this->list_type as $type) {
			if($idtype == $type['id']) {
				$select = "selected";
			}
			$selector .= "<option value='".$type['id']."' $select>".htmlentities($type['comment'],ENT_QUOTES,$charset)."</option>";
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
		
		$selector = "<select ".($ajax ? "name='save_statut_".$this->id_action."' id='save_statut_".$this->id_action."'" : "id='idstatut' name='idstatut'").">";
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
		global $idaction,$sujet, $idtype,$PMBuserid ,$idstatut;
		global $date_debut, $date_fin, $detail;
		global $time_elapsed, $progression,$cout,$iddemande, $ck_prive;
		
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
		$this->actions_type_user = '0';
		$this->actions_num_user = $PMBuserid;
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
		$explnum_doc->delete();
		$query = "DELETE FROM explnum_doc_actions WHERE num_explnum_doc='".$explnum_doc->explnum_doc_id."'";
		pmb_mysql_query($query);
	}
	
	public static function save_docnum($action,$explnum_doc){
		$explnum_doc->save();
		
		$query = "REPLACE INTO explnum_doc_actions SET
		num_explnum_doc='".$explnum_doc->explnum_doc_id."',
		num_action='".$action->id_action."',
		prive='".$explnum_doc->prive."',
		rapport='".$explnum_doc->rapport."'";
		
		pmb_mysql_query($query);
	}
	
	/*
	 * Insertion/Modification d'une action
	*/
	public function save(){
		global $pmb_type_audit;
		
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
			
			// audit
			if($pmb_type_audit) audit::insert_modif(AUDIT_ACTION,$this->id_action);
				
			//Création d'une note automatiquement
			if($this->detail_action && $this->detail_action!==""){
				$note=new demandes_note();
				$note->num_action=$this->id_action;
				$note->date_note=date("Y-m-d h:i:s",time());
				$note->rapport=0;
				$note->contenu=$this->detail_action;
				$note->notes_num_user=$this->actions_num_user;
				$note->notes_type_user=$this->actions_type_user;
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
	public static function show_list_actions($actions,$id_demande,$last_modified=0,$allow_expand=true,$from_ajax=false){
	    /*list_demandes_actions_demande_ui::set_allow_expand($allow_expand);
	    $list_demandes_actions_demande_ui = list_demandes_actions_demande_ui::get_instance(array('id_demande' => $id_demande));
		return $list_demandes_actions_demande_ui->get_display_list();
		*/
		
		global $msg, $charset;
		global $content_liste_action, $form_liste_action, $js_liste_action;
		global $pmb_gestion_devise, $ck_vue;
		
		if($from_ajax) {
			$list_actions = $content_liste_action;
		} else {
			$list_actions = $js_liste_action.$form_liste_action;
		}
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
					<td $onclick>".count($action->notes)."</td>
					
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
					window.addEventListener('load',expand_note('note'+document.getElementById('last_modified').value,document.getElementById('last_modified').value, true));
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
	public static function delete(demandes_actions $action) {
		if (!empty($action->id_action)) {
			$action->fetch_data($action->id_action, false);
			if (!empty($action->notes)) {
				foreach($action->notes as $note) {
					demandes_note::delete($note);
				}
			}
			
			$req = "delete from demandes_actions where id_action='$action->id_action'"; 
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
		
		$nb_fil = 0;
		if (is_array($chk)) {
		    $nb_fil = count($chk);
		}
		for ($i = 0; $i < $nb_fil; $i++) {
			$req = "update demandes_actions set statut_action=3 where id_action='".$chk[$i]."'";
			pmb_mysql_query($req);
		}
	}
	
	/*
	 * Annule tous les RDV
	 */
	public function close_rdv(){
		global $chk;
		
		$nb_rdv = 0;
		if (is_array($chk)) {
		    $nb_rdv = count($chk);
		}
		for ($i = 0; $i < $nb_rdv; $i++) {		
			$req = "update demandes_actions set statut_action=3 where id_action='".$chk[$i]."'";
			pmb_mysql_query($req);
		}
	}
	
	/*
	 * Valide tous les RDV
	 */
	public function valider_rdv(){
		global $chk;
		
		$nb_rdv = 0;
		if (is_array($chk)) {
		    $nb_rdv = count($chk);
		}
		for ($i = 0; $i < $nb_rdv; $i++) {
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
	
	public function get_id() {
		return $this->id_action;
	}
	
	/*
	 * fonction qui renvoie un booléen indiquant si une action a été lue ou pas
	*/
	public static function read($action,$side="_gestion"){
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
	public static function change_read($action,$side="_gestion"){
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
	 * true => action est déjà lue doc pas d'alerte
	 * false => alerte
	*/
	public static function action_read($id_action,$booleen=true,$side="_gestion"){
		$value = "";
		if($booleen){
			$value = 0;
		} else {
			$value = 1;
		}
		$query = "UPDATE demandes_actions SET actions_read".$side."=".$value." WHERE id_action=".$id_action;
		if(pmb_mysql_query($query)){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Met à jour les alertes sur l'action et la demande dont dépend la note
	*/
	public static function action_majParentEnfant($id_action,$id_demande,$side="_gestion"){
		$ok = false;
		if($id_action){
				
			$select = "SELECT actions_read".$side." FROM demandes_actions WHERE id_action=".$id_action;
			$result  = pmb_mysql_query($select);
			$read = pmb_mysql_result($result,0,0);
				
			if($read == 1){
				if(demandes::demande_read($id_demande,false)){
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
?>