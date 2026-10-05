<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_notes.class.php,v 1.20.8.3 2025/05/23 07:00:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/mail.inc.php");
require_once("$class_path/audit.class.php");
require_once("$include_path/templates/demandes_notes.tpl.php");

class demandes_notes {
	
	public $id_note = 0;
	public $date_note = '0000-00-00 00:00:00';
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
	
	public static function show_dialog($notes,$num_action,$num_demande,$redirect_to='demandes_actions-show_consultation_form'){
		global $form_dialog_note, $msg, $charset,$id_empr;
		
		$form_dialog_note = str_replace('!!redirectto!!',$redirect_to,$form_dialog_note);
		$form_dialog_note = str_replace('!!idaction!!',$num_action,$form_dialog_note);
		$form_dialog_note = str_replace('!!iddemande!!',$num_demande,$form_dialog_note);
		
		$dialog='';
		if(is_countable($notes) && sizeof($notes)){
			foreach($notes as $note){
				
				//Utilisateur ou lecteur ? 
				if($note->notes_type_user==="1"){
					$side='note_opac bulle bulle_opac';
				}elseif($note->notes_type_user==="0"){
					$side='note_gest bulle bulle_gest';
				}

				$dialog.='<div class="container_bulle" id="note_'.$note->id_note.'">';
				$dialog.="<div class='".$side."'>";
				$dialog.='<div class="btn_note">';
				
				if($note->prive){
					$dialog.="<input type='image' src='".get_url_icon('interdit.gif')."' alt='".htmlentities($msg['demandes_note_privacy'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_privacy'],ENT_QUOTES,$charset)."' onclick='return false;'/>"; 
				}
				if($note->rapport){
					$dialog.="<input type='image' src='".get_url_icon('info.gif')."' alt='".htmlentities($msg['demandes_note_rapport'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_rapport'],ENT_QUOTES,$charset)."' onclick='return false;'/>";
				}
				if($note->notes_read_opac){
					$dialog.="<input type='image' src='".get_url_icon('notification_new.png')."' alt='".htmlentities($msg['demandes_note_vue'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_vue'],ENT_QUOTES,$charset)."' onclick='return false;'/>";
				}
				
				$dialog.=' </div>';
				$dialog.='<div class="entete_note bulle-heure"><b>'.$note->createur_note.'</b> - '.$msg['381'].' '.formatdate($note->date_note).'</div>';
				$dialog.='<p class="bulle-text">'.$note->contenu.'</p>';
				$dialog.='</div>';
				$dialog.='</div>';
				
				demandes_note::note_read($note->id_note,true,"_opac");				
			}
			$dialog.='<a name="fin"></a>';
		}
		
		// Annulation de l'alerte sur l'action dépliée après lecture des nouvelles notes si c'est la personne à laquelle est affectée l'action qui la lit
		demandes_actions::action_read($num_action,true,"_opac");
		// Mise à jour de la demande dont est issue l'action
		demandes_actions::action_majParentEnfant($num_action,$num_demande,"_opac");
		
		$form_dialog_note = str_replace('!!dialog!!',$dialog,$form_dialog_note);
		return $form_dialog_note;
	}
	
	/*
	 * Inutile depuis la refonte
	 * Affichage des notes enfants
	 */
	public function getChilds($id_note){
		global $charset, $msg;
		
		$req = "select id_note, CONCAT(SUBSTRING(contenu,1,50),'','...') as titre, contenu, date_note, prive, rapport, notes_num_user,notes_type_user 
		from demandes_notes where num_note_parent='".$id_note."' and num_action='".$this->num_action."' order by date_note desc, id_note desc";
		$res = pmb_mysql_query($req);
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
			return (trim($createur->nom)  ? $createur->nom : $createur->username);
		}
		
		return "";
	}
}