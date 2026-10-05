<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_notes.class.php,v 1.44.4.2 2025/05/06 15:16:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
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
	public $demande_end = 0;
	
	public static function show_dialog($notes,$num_action,$num_demande,$redirect_to='demandes_actions-show_consultation_form',$from_ajax=false){
		global $msg, $charset;
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
		$dialog = '';
		if (!empty($notes)) {
			foreach($notes as $note){
				//Utilisateur ou lecteur ? 
				if($note->notes_type_user==="1"){
					$side='bulle bulle_opac';
				}elseif($note->notes_type_user==="0"){
					$side='bulle bulle_gest';
				}

				$dialog.='<div class="container_bulle" id="note_'.$note->id_note.'">';
				$dialog .="<div class=\"".$side."\">";
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
								onclick='if(confirm_delete_note(".$num_action.")) {!!change_action_form!!document.forms[\"".$form_name."\"].act.value=\"suppr_note\";document.forms[\"".$form_name."\"].idnote.value=\"$note->id_note\";} else return false;' />";
				// affichage de l'audit des notes seulement si nécessaire
				$audit_note = new audit(16,$note->id_note);
				$audit_note->get_all();
				if (count($audit_note->all_audit) > 1) {
					$dialog.="<input type='image' src='".get_url_icon('historique.gif')."'
					onClick=\"openPopUp('./audit.php?type_obj=16&object_id=$note->id_note', 'audit_popup'); return false;\" title=\"".$msg['audit_button']."\" value=\"".$msg['audit_button']."\" />";
				}				
				if(!$note->notes_read_gestion && !$note->notes_type_user){
					$req = "select  demande_note_num from demandes where demande_note_num='".$note->id_note."'" ;
					$res = pmb_mysql_query($req);
					if(pmb_mysql_num_rows($res)){
						$color_img="red";
					}else $color_img="blue";
						
					$dialog.="<a href=\"javascript:change_demande_end('note_".$note->id_note."','$note->id_note','".$num_action."','".$num_demande."', true);\" ><i  id='note_".$note->id_note."Img3' class='fa fa-file-text-o fa-2x' style='color:$color_img' alt='".htmlentities($msg['demandes_note_demande_end'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_note_demande_end'],ENT_QUOTES,$charset)."' ></i></a>";
				}				
				$dialog.=' </div>';
				$dialog.="<div onclick='!!change_action_form!!document.forms[\"".$form_name."\"].act.value=\"modif_note\";document.forms[\"".$form_name."\"].idnote.value=\"$note->id_note\";document.forms[\"".$form_name."\"].submit();'>";
				$dialog.='<div class="bulle-heure"><b>'.$note->createur_note.'</b> - '.formatdate($note->date_note).'</div>';
				$dialog.='<p class="bulle-text">'.$note->contenu.'</p>';
				$dialog.='</div>';
				$dialog.='</div>';
				$dialog.='</div>';
				
				//demandes_note::note_read($note->id_note,true,"_gestion");				
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
