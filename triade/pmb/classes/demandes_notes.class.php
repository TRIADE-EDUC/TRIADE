<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_notes.class.php,v 1.35 2018-12-03 10:15:23 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/mail.inc.php");
require_once("$class_path/audit.class.php");

class demandes_notes {
	
	public $id_note = 0;
	public $date_note = '0000-00-00';
	public $contenu = '';
	public $prive = 0;
	public $rapport = 0;
	public $num_note_parent = 0;
	public $num_action = 0;
	public $num_demande = 0;
	public $libelle_action = '';
	public $libelle_demande = '';
	public $notes_num_user = 0;
	public $notes_type_user = 0;
	public $createur_note = '';
	public $notes_read_gestion = 0; // flag gestion sur la lecture de la note par l'utilisateur
	public $notes_read_opac = 0; // flag opac sur la lecture de la note par le lecteur
	public $demande_final_note_num = 0;
	
	public function __construct($id_note=0,$id_action=0){
		$this->id_note = $id_note+0;
		$this->num_action = $id_action+0;
		$this->fetch_data();
	}
	
	public function fetch_data(){
		global $dbh;
		
		$this->date_note = '0000-00-00 00:00:00';
		$this->contenu = '';
		$this->rapport = 0;
		$this->prive = 0;
		$this->num_note_parent = 0;
		$this->num_action = 0;
		$this->notes_num_user = 0;
		$this->notes_type_user = 0;
		$this->notes_read_gestion = 0;
		$this->notes_read_opac = 0;
		$this->demande_final_note_num = 0;
		if($this->id_note){
			$req = "select id_note, prive, rapport,contenu,date_note, sujet_action, id_demande, titre_demande, notes_num_user, notes_type_user, 
					num_action, num_note_parent, notes_read_gestion, notes_read_opac , demande_note_num
			from demandes_notes
			join demandes_actions on num_action=id_action
			join demandes on num_demande=id_demande
			where id_note='".$this->id_note."'";
			$res = pmb_mysql_query($req);
			if(pmb_mysql_num_rows($res)){
				$obj = pmb_mysql_fetch_object($res);
				$this->date_note = $obj->date_note;
				$this->contenu = $obj->contenu;
				$this->rapport = $obj->rapport;
				$this->prive = $obj->prive;
				$this->num_note_parent = $obj->num_note_parent;
				$this->num_action = $obj->num_action;
				$this->libelle_action = $obj->sujet_action;
				$this->libelle_demande = $obj->titre_demande;
				$this->num_demande = $obj->id_demande;
				$this->notes_num_user = $obj->notes_num_user;
				$this->notes_type_user = $obj->notes_type_user;
				$this->notes_read_gestion = $obj->notes_read_gestion;
				$this->notes_read_opac = $obj->notes_read_opac;
				$this->demande_final_note_num = $obj->demande_note_num;
			}
		}
		
		if($this->num_action){
			$req = "select sujet_action, titre_demande, id_demande
			from demandes_actions join demandes on num_demande=id_demande
			where id_action='".$this->num_action."'
			";
			$res = pmb_mysql_query($req,$dbh);
			$obj = pmb_mysql_fetch_object($res);
			$this->libelle_action = $obj->sujet_action;
			$this->libelle_demande = $obj->titre_demande;
			$this->num_demande = $obj->id_demande;
		}
		
		if($this->notes_num_user){
			$this->createur_note = $this->getCreateur($this->notes_num_user,$this->notes_type_user);
		}
	}
	
	/*
	 * Formulaire d'ajout/modification
	 */
	public function show_modif_form($reply=false){
		global $form_modif_note, $msg, $charset, $demandes_include_note;
		
		$act_cancel = "document.location='./demandes.php?categ=action&act=see&idaction=$this->num_action#fin'";
		$form_modif_note = str_replace('!!cancel_action!!',$act_cancel,$form_modif_note);
		$form_modif_note = str_replace('!!iduser!!',$this->notes_num_user,$form_modif_note);
		$form_modif_note = str_replace('!!typeuser!!',$this->notes_type_user,$form_modif_note);
		$form_modif_note = str_replace('!!iddemande!!',$this->num_demande,$form_modif_note);
		
		if($this->id_note && !$reply){			
			$title = (strlen($this->contenu)>30 ? substr($this->contenu,0,30).'...' : $this->contenu);
			$form_modif_note = str_replace('!!form_title!!',$msg['demandes_note_modif'].' : '.$title,$form_modif_note);
			
			$form_modif_note = str_replace('!!contenu!!',htmlentities($this->contenu,ENT_QUOTES,$charset),$form_modif_note);
			if($this->rapport){
				$form_modif_note = str_replace('!!ck_rapport!!','checked',$form_modif_note);
			}else{
				$form_modif_note = str_replace('!!ck_rapport!!','',$form_modif_note);
			}
			if($this->prive){
				$form_modif_note = str_replace('!!ck_prive!!','checked',$form_modif_note);
			}else{
				$form_modif_note = str_replace('!!ck_prive!!','',$form_modif_note);
			}
			if($this->notes_read_gestion){
				$form_modif_note = str_replace('!!ck_vue!!','',$form_modif_note);
			}else{
				$form_modif_note = str_replace('!!ck_vue!!','checked',$form_modif_note);
			}	
			
			if($this->demande_final_note_num == $this->id_note){
				$form_modif_note = str_replace('!!ck_final_note!!','checked',$form_modif_note);
			}else{
				$form_modif_note = str_replace('!!ck_final_note!!','',$form_modif_note);
			}
				
			$form_modif_note = str_replace('!!date_note_btn!!',formatdate($this->date_note),$form_modif_note);
			$form_modif_note = str_replace('!!date_note!!',$this->date_note,$form_modif_note);
			$form_modif_note = str_replace('!!idnote!!',$this->id_note,$form_modif_note);
			$form_modif_note = str_replace('!!idaction!!',$this->num_action,$form_modif_note);
			
			$btn_suppr = "<input type='submit' class='bouton' value='".$msg[63]."' id='suppr_note' name='suppr_note' onclick='this.form.act.value=\"suppr_note\";return confirm_delete();'";
		} elseif($this->id_note && $reply){
			$nots = new demandes_notes($this->id_note);
			$title = (strlen($nots->contenu)>30 ? substr($nots->contenu,0,30).'...' : $nots->contenu);
			$form_modif_note = str_replace('!!form_title!!',$msg['demandes_note_reply'].' : '.$title,$form_modif_note);			
			$form_modif_note = str_replace('!!contenu!!','',$form_modif_note);
			if($demandes_include_note)
				$form_modif_note = str_replace('!!ck_rapport!!','checked',$form_modif_note);
			else $form_modif_note = str_replace('!!ck_rapport!!','',$form_modif_note);	
			$form_modif_note = str_replace('!!ck_prive!!','',$form_modif_note);
			$date = formatdate(today());
			$date_note=date("Ymd",time());
			$form_modif_note = str_replace('!!date_note_btn!!',$date,$form_modif_note);
			$form_modif_note = str_replace('!!date_note!!',$date_note,$form_modif_note);
			$form_modif_note = str_replace('!!idnote!!','',$form_modif_note);
			$form_modif_note = str_replace('!!idaction!!',$this->num_action,$form_modif_note);
			$form_modif_note = str_replace('!!ck_final_note!!','',$form_modif_note);
		} else {
			$form_modif_note = str_replace('!!form_title!!',$msg['demandes_note_creation'],$form_modif_note);
			$form_modif_note = str_replace('!!ck_prive!!','',$form_modif_note);
			if($demandes_include_note)
				$form_modif_note = str_replace('!!ck_rapport!!','checked',$form_modif_note);
			else $form_modif_note = str_replace('!!ck_rapport!!','',$form_modif_note);	
			$form_modif_note = str_replace('!!contenu!!','',$form_modif_note);
			$date = formatdate(today());
			$date_note=date("Ymd",time());
			$form_modif_note = str_replace('!!date_note_btn!!',$date,$form_modif_note);
			$form_modif_note = str_replace('!!date_note!!',$date_note,$form_modif_note);
			$form_modif_note = str_replace('!!idnote!!','',$form_modif_note);
			$form_modif_note = str_replace('!!idaction!!',$this->num_action,$form_modif_note);
			$form_modif_note = str_replace('!!parent_text!!','',$form_modif_note);
			$form_modif_note = str_replace('!!id_note_parent!!','',$form_modif_note);
			$form_modif_note = str_replace('!!style!!','',$form_modif_note);
			$form_modif_note = str_replace('!!ck_final_note!!','',$form_modif_note);
		}
		
		$path = "<a href=./demandes.php?categ=gestion&act=see_dmde&iddemande=$this->num_demande>".htmlentities($this->libelle_demande,ENT_QUOTES,$charset)."</a>";
		$path .= " > <a href=./demandes.php?categ=action&act=see&idaction=$this->num_action>".htmlentities($this->libelle_action,ENT_QUOTES,$charset)."</a>";
		$form_modif_note = str_replace('!!path!!',$path,$form_modif_note);
		$form_modif_note = str_replace('!!btn_suppr!!',$btn_suppr,$form_modif_note);
		
		print $form_modif_note;
	}
	
	public static function get_values_from_form(&$note){
		global $contenu_note, $idaction,$idnote ,$iddemande , $iduser,$typeuser;
		global $date_note, $ck_rapport, $ck_prive, $ck_vue,$id_note_parent, $PMBuserid,$demande_end;
		
		if(!$iduser){
			$iduser=$PMBuserid;
			$typeuser='0';
		}
		
		$note->id_note=$idnote;
		$note->num_action=$idaction;
		$note->num_demande=$iddemande;
		$note->contenu=$contenu_note;
		$note->num_note_parent=$id_note_parent;
		if(!$date_note){
			$note->date_note=date("Y-m-d h:i:s",time());
		}else{
			$note->date_note=$date_note;
		}
		if($ck_prive){
			$note->prive=1;
		}else{
			$note->prive=0;
		}
		if($ck_rapport){
			$note->rapport=1;
		}else{
			$note->rapport=0;
		}
		if($ck_vue){
			$note->notes_read_gestion=0;
		}else{
			$note->notes_read_gestion=1;
		}
		if($demande_end){
			$note->demande_end=1;
		}else{
			$note->demande_end=0;
		}
		
		$note->notes_num_user=$iduser;
		$note->notes_type_user=$typeuser;
	}
	
	/*
	 * Création/Modification d'une note
	 */
	public static function save(&$note){
	
		global $dbh; 
		global $demandes_email_demandes, $pmb_type_audit,$PMBuserid;
		
		if($note->id_note){
			//MODIFICATION
			$query = "UPDATE demandes_notes SET 
			contenu='".$note->contenu."',
			date_note='".$note->date_note."',
			prive='".$note->prive."',
			rapport='".$note->rapport."',
			num_action='".$note->num_action."',
			notes_num_user='".$note->notes_num_user."',
			notes_type_user='".$note->notes_type_user."',
			num_note_parent='".$note->num_note_parent."',
			notes_read_gestion='".$note->notes_read_gestion."',
			notes_read_opac='1' 
			WHERE id_note='".$note->id_note."'";
			
			pmb_mysql_query($query,$dbh);
			
			if($pmb_type_audit) audit::insert_modif(AUDIT_NOTE,$note->id_note);
		} else {
			//CREATION
			$query = "INSERT INTO demandes_notes SET
			contenu='".$note->contenu."',
			date_note='".$note->date_note."',
			prive='".$note->prive."',
			rapport='".$note->rapport."',
			num_action='".$note->num_action."',
			notes_num_user='".$note->notes_num_user."',
			notes_type_user='".$note->notes_type_user."',
			num_note_parent='".$note->num_note_parent."', 
			notes_read_gestion='".$note->notes_read_gestion."',
			notes_read_opac='1'";
			pmb_mysql_query($query,$dbh);
			$note->id_note=pmb_mysql_insert_id($dbh);
			
			if($pmb_type_audit) audit::insert_creation(AUDIT_NOTE,$note->id_note);
				
			if(!$note->prive) {
				if ($demandes_email_demandes){
					$note->fetch_data($note->id_note,$note->num_action);
					$note->send_alert_by_mail($note->notes_num_user,$note);					
				}
			}
		}
		
		// Générer la réponse finale de la demande avec cette note
		if($note->demande_end){
			global $f_message;
			$f_message=$note->contenu;
			$demande = new demandes($note->num_demande);
			$demande->save_repfinale($note->id_note);
			demandes_notes::note_majParent($note->id_note,$note->num_action,$note->num_demande,"_gestion");
		}
	}
	
	/*
	 * Suppression d'une note
	 */
	public static function delete($note){
		global $dbh;
		if($note->id_note){
			$req = "delete from demandes_notes where id_note='".$note->id_note."'";
			pmb_mysql_query($req,$dbh);
			$req = "delete from demandes_notes where num_note_parent='".$note->id_note."'";
			pmb_mysql_query($req,$dbh);
			audit::delete_audit(AUDIT_NOTE,$note->id_note);
		}
	}
	
	public static function show_dialog($notes,$num_action,$num_demande,$redirect_to='demandes_actions-show_consultation_form',$from_ajax=false){
		global $dbh, $msg, $charset;
		global $content_dialog_note, $form_dialog_note, $js_dialog_note;
		
		if($from_ajax) {
			$dialog_note = $content_dialog_note;
			$form_name = "liste_action";
		} else {
			$dialog_note = $js_dialog_note.$form_dialog_note;
			$form_name = "modif_notes";
		}
		$dialog_note = str_replace('!!redirectto!!',$redirect_to,$dialog_note);
		$dialog_note = str_replace('!!idaction!!',$num_action,$dialog_note);
		$dialog='';
		if(sizeof($notes)){
			foreach($notes as $idNote=>$note){
				//Utilisateur ou lecteur ? 
				if($note->notes_type_user==="1"){
					$side='note_opac';
				}elseif($note->notes_type_user==="0"){
					$side='note_gest';
				}

				$dialog.='<div class="'.$side.'" id="note_'.$note->id_note.'">';
				$dialog.='<div class="btn_note">';
				
				if($note->prive){
					$dialog.="<input type='image' src='".get_url_icon('interdit.gif')."' alt='".htmlentities($msg['demandes_note_privacy'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_privacy'],ENT_QUOTES,$charset)."' onclick='return false;'/>"; 
				}
				if($note->rapport){
					$dialog.="<input type='image' src='".get_url_icon('info.gif')."' alt='".htmlentities($msg['demandes_note_rapport'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_rapport'],ENT_QUOTES,$charset)."' onclick='return false;'/>";
				}
				if($note->notes_read_gestion){
					$dialog.="<input type='image' onclick=\"change_read_note('note_".$note->id_note."','$note->id_note','".$num_action."','".$num_demande."', true); return false;\" title=\"\" id=\"note_".$note->id_note."Img1\" class=\"img_plus\" src='".get_url_icon('notification_empty.png')."' style='display:none'>
								<input type='image' onclick=\"change_read_note('note_".$note->id_note."','$note->id_note','".$num_action."','".$num_demande."', true); return false;\" title=\"" . $msg['demandes_new']. "\" id=\"note_".$note->id_note."Img2\" class=\"img_plus\" src='".get_url_icon('notification_new.png')."'>";
				} else {
					$dialog .= "<input type='image' onclick=\"change_read_note('note_".$note->id_note."','$note->id_note','".$num_action."','".$num_demande."', true); return false;\" title=\"\" id=\"note_".$note->id_note."Img1\" class=\"img_plus\" src='".get_url_icon('notification_empty.png')."' >
								<input type='image' onclick=\"change_read_note('note_".$note->id_note."','$note->id_note','".$num_action."','".$num_demande."', true); return false;\" title=\"" . $msg['demandes_new']. "\" id=\"note_".$note->id_note."Img2\" class=\"img_plus\" src='".get_url_icon('notification_new.png')."' style='display:none'>";
				}
				
				$dialog.="<input type='image' src='".get_url_icon('cross.png')."' alt='".htmlentities($msg['demandes_note_suppression'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_suppression'],ENT_QUOTES,$charset)."' 
								onclick='if(confirm_delete_note()) {!!change_action_form!!document.forms[\"".$form_name."\"].act.value=\"suppr_note\";document.forms[\"".$form_name."\"].idnote.value=\"$note->id_note\";} else return false;' />";
				// affichage de l'audit des notes seulement si nécessaire
				$audit_note = new audit(16,$note->id_note);
				$audit_note->get_all();
				if(sizeof($audit_note->all_audit)>1){
					$dialog.="<input type='image' src='".get_url_icon('historique.gif')."'
					onClick=\"openPopUp('./audit.php?type_obj=16&object_id=$note->id_note', 'audit_popup'); return false;\" title=\"".$msg['audit_button']."\" value=\"".$msg['audit_button']."\" />";
				}				
				if(!$note->notes_read_gestion && !$note->notes_type_user){
					$req = "select  demande_note_num from demandes where demande_note_num='".$note->id_note."'" ;
					$res = pmb_mysql_query($req,$dbh);
					if(pmb_mysql_num_rows($res)){
						$color_img="red";
					}else $color_img="blue";
						
					$dialog.="<a href=\"javascript:change_demande_end('note_".$note->id_note."','$note->id_note','".$num_action."','".$num_demande."', true);\" ><i  id='note_".$note->id_note."Img3' class='fa fa-file-text-o fa-2x' style='color:$color_img' alt='".htmlentities($msg['demandes_note_demande_end'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_demande_end'],ENT_QUOTES,$charset)."' ></i></a>";
				}				
				$dialog.=' </div>';
				$dialog.="<div onclick='!!change_action_form!!document.forms[\"".$form_name."\"].act.value=\"modif_note\";document.forms[\"".$form_name."\"].idnote.value=\"$note->id_note\";document.forms[\"".$form_name."\"].submit();'>";
				$dialog.='<div class="entete_note">'.$note->createur_note.' '.$msg['381'].' '.formatdate($note->date_note).'</div>';
				$dialog.='<p>'.$note->contenu.'</p>';
				$dialog.='</div>';
				$dialog.='</div>';
				
				demandes_notes::note_read($note->id_note,true,"_gestion");				
			}
			$dialog.='<a name="fin"></a>';
		}
		
		$dialog_note = str_replace('!!dialog!!',$dialog,$dialog_note);
		if($from_ajax) {
			$dialog_note = str_replace('!!change_action_form!!','document.forms["'.$form_name.'"].action="./demandes.php?categ=notes#fin";',$dialog_note);
		} else {
			$dialog_note = str_replace('!!change_action_form!!','',$dialog_note);
		}
		return $dialog_note;
	}
	
	/*
	 * Inutile depuis la refonte
	 * Affichage de la liste des notes associées à une action
	 */
	public function show_list_notes($idaction=0){
		
		global $form_table_note, $dbh, $msg, $charset;
		
	
		$req = "select id_note, CONCAT(SUBSTRING(contenu,1,50),'','...') as titre, contenu, date_note, prive, rapport,notes_num_user,notes_type_user
		 from demandes_notes where num_action='".$idaction."' and num_note_parent=0  order by date_note desc ,id_note desc";
		$res = pmb_mysql_query($req,$dbh); 
		$liste ="";
		if(pmb_mysql_num_rows($res)){
			while(($note = pmb_mysql_fetch_object($res))){
				$createur = $this->getCreateur($note->notes_num_user,$note->notes_type_user);
				$contenu = "
					<div class='row'>
						<div class='left'>
							<input type='image' src='".get_url_icon('email_go.png')."' alt='".htmlentities($msg['demandes_note_reply_icon'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_reply_icon'],ENT_QUOTES,$charset)."' 
								onclick='document.forms[\"modif_notes\"].act.value=\"reponse\";document.forms[\"modif_notes\"].idnote.value=\"$note->id_note\";' />
							<input type='image' src='".get_url_icon('b_edit.png')."' alt='".htmlentities($msg['demandes_note_modif_icon'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_modif_icon'],ENT_QUOTES,$charset)."' 
								onclick='document.forms[\"modif_notes\"].act.value=\"modif_note\";document.forms[\"modif_notes\"].idnote.value=\"$note->id_note\";' />
							<input type='image' src='".get_url_icon('cross.png')."' alt='".htmlentities($msg['demandes_note_suppression'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_suppression'],ENT_QUOTES,$charset)."' 
								onclick='document.forms[\"modif_notes\"].act.value=\"suppr_note\";document.forms[\"modif_notes\"].idnote.value=\"$note->id_note\";' />
					</div>
					</div>
					<div class='row'>
						<label class='etiquette'>".$msg['demandes_note_privacy']." : </label>&nbsp;
						".( $note->prive ? $msg['40'] : $msg['39'])."
					</div>
					<div class='row'>
						<label class='etiquette'>".$msg['demandes_note_rapport']." : </label>&nbsp;
						".( $note->rapport ? $msg['40'] : $msg['39'])."
					</div>
					<div class='row'>
						<label class='etiquette'>".$msg['demandes_note_contenu']." : </label>&nbsp;
						".nl2br(htmlentities($note->contenu,ENT_QUOTES,$charset))."
					</div>
				";
				$contenu .= $this->getChilds($note->id_note);
				if(strlen($note->titre)<50){
					$note->titre = str_replace('...','',$note->titre);
				}
				$liste .= gen_plus("note_".$note->id_note,"[".formatdate($note->date_note)."] ".$note->titre.($createur ? " <i>".sprintf($msg['demandes_action_by'],$createur."</i>") : ""), $contenu);
			}
		} else {
			$liste .= htmlentities($msg['demandes_note_no_list'],ENT_QUOTES,$charset);
		}
		
		$form_table_note = str_replace('!!idaction!!',$this->num_action,$form_table_note);
		$form_table_note = str_replace('!!liste_notes!!',$liste,$form_table_note);
		print $form_table_note;
	}
	
	/*
	 * Inutile depuis la refonte
	 * Affichage des notes enfants
	 */
	public function getChilds($id_note){
		global $dbh, $charset, $msg;
		
		$req = "select id_note, CONCAT(SUBSTRING(contenu,1,50),'','...') as titre, contenu, date_note, prive, rapport, notes_num_user,notes_type_user 
		from demandes_notes where num_note_parent='".$id_note."' and num_action='".$this->num_action."' order by date_note desc, id_note desc";
		$res = pmb_mysql_query($req,$dbh);
		$display="";
		if(pmb_mysql_num_rows($res)){
			while(($fille = pmb_mysql_fetch_object($res))){
				$createur = $this->getCreateur($fille->notes_num_user,$fille->notes_type_user);
				$contenu = "
					<div class='row'>
						<div class='left'>
							<input type='image' src='".get_url_icon('email_go.png')."' alt='".htmlentities($msg['demandes_note_reply_icon'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_reply_icon'],ENT_QUOTES,$charset)."' 
										onclick='document.forms[\"modif_notes\"].act.value=\"reponse\";document.forms[\"modif_notes\"].idnote.value=\"$fille->id_note\";' />
							<input type='image' src='".get_url_icon('b_edit.png')."' alt='".htmlentities($msg['demandes_note_modif_icon'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_modif_icon'],ENT_QUOTES,$charset)."' 
										onclick='document.forms[\"modif_notes\"].act.value=\"modif_note\";document.forms[\"modif_notes\"].idnote.value=\"$fille->id_note\";' />
							<input type='image' src='".get_url_icon('cross.png')."' alt='".htmlentities($msg['demandes_note_suppression'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_suppression'],ENT_QUOTES,$charset)."' 
										onclick='document.forms[\"modif_notes\"].act.value=\"suppr_note\";document.forms[\"modif_notes\"].idnote.value=\"$fille->id_note\";' />
					</div>
					</div>
					<div class='row'>
						<label class='etiquette'>".$msg['demandes_note_privacy']." : </label>&nbsp;
						".( $fille->prive ? $msg['40'] : $msg['39'])."
					</div>
					<div class='row'>
						<label class='etiquette'>".$msg['demandes_note_rapport']." : </label>&nbsp;
						".( $fille->rapport ? $msg['40'] : $msg['39'])."
					</div>
					<div class='row'>
						<label class='etiquette'>".$msg['demandes_note_contenu']." : </label>&nbsp;
						".nl2br(htmlentities($fille->contenu,ENT_QUOTES,$charset))."
					</div>
				";
				$contenu .= $this->getChilds($fille->id_note);
				if(strlen($fille->titre)<50){
					$fille->titre = str_replace('...','',$fille->titre);
				}
				$display .= "<span style='margin-left:20px'>".gen_plus("note_".$fille->id_note,"[".formatdate($fille->date_note)."] ".$fille->titre.($createur ? " <i>".sprintf($msg['demandes_action_by'],$createur."</i>") : ""), $contenu)."</span>";
			}
		}
		return $display;
	}
	
	
	/*
	 * Alerte par mail
	 */	
	public function send_alert_by_mail($idsender,$note){
		
		global $msg, $PMBusernom, $PMBuserprenom, $PMBuseremail, $dbh,$opac_url_base,$pmb_url_base,$demandes_email_generic;
		
		$contenu = sprintf($msg['demandes_note_mail_new'],$PMBuserprenom." ".$PMBusernom." ",$note->libelle_action,$note->libelle_demande).'<br />';
		$contenu.=$note->contenu.'<br />';
		$lien_opac='<a href="'.$opac_url_base.'empr.php?tab=request&lvl=list_dmde&sub=open_demande&iddemande='.$note->num_demande.'&last_modified='.$note->num_action.'#fin">'.$msg['demandes_see_last_note'].'</a>';
		$lien_gestion='<a href="'.$pmb_url_base.'demandes.php?categ=gestion&act=see_dmde&iddemande='.$note->num_demande.'&last_modified='.$note->num_action.'#fin">'.$msg['demandes_see_last_note'].'</a>';
		$objet = $msg['demandes_note_mail_new_object'];
		
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1";
		
		//Envoi du mail aux autres documentalistes concernés par la demande
		$req = "SELECT user_email, prenom,nom FROM users
		JOIN demandes_users ON num_user=userid
		WHERE num_demande='".$note->num_demande."' AND num_user !='".$idsender."'";
		$res = pmb_mysql_query($req,$dbh);
		
		while(($user = pmb_mysql_fetch_object($res))){	
			if($user->user_email){
				if($user->prenom){
					$user->nom=$user->prenom.' '.$user->nom;
				}
				
				$envoi_OK = mailpmb($user->nom,$user->user_email,$objet,$contenu.$lien_gestion,$PMBuserprenom." ".$PMBusernom,$PMBuseremail,$headers,"" );
			}
		}
		
		//Envoi du mail au demandeur
		$req= "SELECT empr_prenom,empr_nom,  empr_mail FROM empr
		JOIN demandes ON id_empr=num_demandeur
		WHERE id_demande='".$note->num_demande."'";
		
		$res = pmb_mysql_query($req,$dbh);
		$empr = pmb_mysql_fetch_object($res);		
		if($empr->empr_mail) {
			if($empr->empr_prenom){
				$empr->empr_nom=$empr->empr_prenom.' '.$empr->empr_nom;
			}
			$envoi_OK = mailpmb($empr->empr_nom,$empr->empr_mail,$objet,$contenu.$lien_opac,$PMBuserprenom." ".$PMBusernom,$PMBuseremail,$headers,"");
		}
		
		// Envoi au mail générique
		if($demandes_email_generic){
			$param=explode(",", $demandes_email_generic);
			if(($param[0]==2 || $param[0]==3) && $param[1]){
				$envoi_OK = mailpmb("",$param[1],$objet,$contenu.$lien_gestion,$PMBuserprenom." ".$PMBusernom,$PMBuseremail,$headers,"",$param[2]);				
			}
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
			return (trim($createur->nom)  ? $createur->nom : $createur->username);
		}
		
		return "";
	}
	
	/*
	 * Met à jour les alertes sur l'action et la demande dont dépend la note
	*/
	public static function note_majParent($id_note,$id_action,$id_demande,$side="_gestion"){
		global $dbh;
		
		$id_note += 0;
		$id_action += 0;
		$id_demande += 0;
		$ok = false;
		if($id_note){
			$select = "SELECT notes_read".$side." FROM demandes_notes WHERE id_note=".$id_note;
			$result  = pmb_mysql_query($select,$dbh);
			$read = pmb_mysql_result($result,0,0);
			
			if($read == 1){
				if(demandes_actions::action_read($id_action,false,$side) && demandes::demande_read($id_demande,false,$side)){
					$ok = true;
				}
			} else {
				// maj action : controle s'il existe des notes non lues pour l'action en cours
				$query = "SELECT notes_read".$side." FROM demandes_notes WHERE num_action=".$id_action." AND id_note!=".$id_note." AND notes_read".$side."=1";
				$result = pmb_mysql_query($query,$dbh);
				
				if(pmb_mysql_num_rows($result)){
					$ok = demandes_actions::action_read($id_action,false,$side);
				} else {
					$ok = demandes_actions::action_read($id_action,true,$side);
				}
				// maj demande : controle s'il existe des actions non lues pour la demande en cours
				if($ok){
					$query = "SELECT actions_read".$side." FROM demandes_actions WHERE num_demande=".$id_demande." AND id_action!=".$id_action." AND actions_read".$side."=1";
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
	
	/*
	 * Met à jour les alertes sur l'action et la demande dont dépend la note
	*/
	public static function note_read($id_note,$booleen=true,$side="_gestion"){
		$id_note += 0;
		$value = "";
		if($booleen){
			$value = 0;
		} else {
			$value = 1;
		}
		$query = "UPDATE demandes_notes SET notes_read".$side."=".$value." WHERE id_note=".$id_note;
		pmb_mysql_query($query);
	}
	
	/*
	 * fonction qui renvoie un booléen indiquant si une note a été lue ou pas
	*/
	public static function read($note,$side="_gestion"){
		global $dbh;
		$read  = false;
		$query = "SELECT notes_read".$side." FROM demandes_notes WHERE id_note=".$note->id_note;
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
	 * Change l'alerte de la note : si elle est lue, elle passe en non lue et inversement
	*/
	public static function change_read($note,$side="_gestion"){
		global $dbh;
	
		$read = demandes_notes::read($note,$side);
		$value = "";
		if($read){
			$value = 1;
		} else {
			$value = 0;
		}
		$query = "UPDATE demandes_notes SET notes_read".$side."=".$value." WHERE id_note=".$note->id_note;
		if(pmb_mysql_query($query,$dbh)){
			return true;
		} else {
			return false;
		}
	}
}
?>