<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes_note.class.php,v 1.1.4.2 2025/05/06 15:16:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/mail.inc.php");
require_once("$class_path/audit.class.php");

class demandes_note {
    
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
    
    public function __construct($id_note=0,$id_action=0){
        $this->id_note = intval($id_note);
        $this->num_action = intval($id_action);
        $this->fetch_data();
    }
    
    public function fetch_data(){
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
            $res = pmb_mysql_query($req);
            $obj = pmb_mysql_fetch_object($res);
            $this->libelle_action = $obj->sujet_action;
            $this->libelle_demande = $obj->titre_demande;
            $this->num_demande = $obj->id_demande;
        }
        
        if($this->notes_num_user){
            $this->createur_note = $this->getCreateur($this->notes_num_user,$this->notes_type_user);
        }
    }
    
    public function get_path() {
        global $charset;
        $path = "<a href=./demandes.php?categ=gestion&act=see_dmde&iddemande=$this->num_demande>".htmlentities($this->libelle_demande,ENT_QUOTES,$charset)."</a>";
        $path .= " > <a href=./demandes.php?categ=action&act=see&idaction=$this->num_action>".htmlentities($this->libelle_action,ENT_QUOTES,$charset)."</a>";
        return $path;
    }
    
    public function get_content_form($reply=false) {
        global $demandes_include_note;
        
        if(!$this->id_note || ($this->id_note && $reply)) {
            $this->date_note=date("Ymd",time());
        }
        if(!($this->id_note && !$reply)){
            if($demandes_include_note) {
                $this->rapport = 1;
            }
        }
        
        $interface_content_form = new interface_content_form(static::class);
        
        //Champs cachés
        $interface_content_form->add_element('iduser')
        ->add_input_node('hidden', $this->notes_num_user);
        $interface_content_form->add_element('typeuser')
        ->add_input_node('hidden', $this->notes_type_user);
        $interface_content_form->add_element('idaction')
        ->add_input_node('hidden', $this->num_action);
        $interface_content_form->add_element('iddemande')
        ->add_input_node('hidden', $this->num_demande);
        
        $html_note_date = "
        <input type='hidden' id='date_note' name='date_note' value='".$this->date_note."' />
        <input type='button' class='bouton' id='date_note_btn' name='date_note_btn' value='".formatdate($this->date_note)."' onClick=\"openPopUp('./select.php?what=calendrier&caller=modif_note&date_caller=".$this->date_note."&param1=date_note&param2=date_note_btn&auto_submit=NO&date_anterieure=YES', 'calendar')\"/>";
        $interface_content_form->add_element('date_note', 'demandes_note_date')
        ->add_html_node($html_note_date);
        
        $interface_content_form->add_element('contenu_note', 'demandes_note_contenu')
        ->add_textarea_node($this->contenu, 0, 15)
        ->set_attributes(array('wrap' => 'virtual'));
        $interface_content_form->add_element('ck_prive')
        ->add_input_node('boolean', $this->prive)
        ->set_label_code('demandes_note_privacy');
        $interface_content_form->add_element('ck_rapport')
        ->add_input_node('boolean', $this->rapport)
        ->set_label_code('demandes_note_rapport');
        $interface_content_form->add_element('ck_vue')
        ->add_input_node('boolean', !$this->notes_read_gestion)
        ->set_label_code('demandes_note_vue');
        $interface_content_form->add_element('demande_end')
        ->add_input_node('boolean', ($this->demande_final_note_num == $this->id_note ? 1 : 0))
        ->set_label_code('demandes_note_demande_end');
        return $interface_content_form->get_display();
    }
    
    /*
     * Formulaire d'ajout/modification
     */
    public function show_modif_form($reply=false){
        global $msg;
        
        print "<h2>".$this->get_path()."</h2>";
        
        $content_form = $this->get_content_form($reply);
        
        $interface_form = new interface_demandes_form('modif_note');
        $interface_form->set_num_action($this->num_action);
        
        $title = (strlen($this->contenu)>30 ? substr($this->contenu,0,30).'...' : $this->contenu);
        if($this->id_note && !$reply){
            $interface_form->set_label($msg['demandes_note_modif'].' : '.$title);
            $content_form = str_replace('!!idnote!!',$this->id_note,$content_form);
        } elseif($this->id_note && $reply) {
            $interface_form->set_label($msg['demandes_note_reply'].' : '.$title);
            $content_form = str_replace('!!idnote!!','',$content_form);
        } else {
            $interface_form->set_label($msg['demandes_note_creation']);
        }
        $interface_form->set_object_id($this->id_note)
        ->set_confirm_delete_msg($msg['demandes_note_confirm_suppr'])
        ->set_content_form($content_form)
        ->set_table_name('demandes_notes')
        ->set_field_focus('contenu_note');
        print $interface_form->get_display();
    }
    
    public function set_properties_from_form() {
        global $contenu_note, $idaction,$idnote ,$iddemande , $iduser,$typeuser;
        global $date_note, $ck_rapport, $ck_prive, $ck_vue,$id_note_parent, $PMBuserid,$demande_end;
        
        if(!$iduser){
            $iduser=$PMBuserid;
            $typeuser='0';
        }
        $this->id_note = intval($idnote);
        $this->num_action = intval($idaction);
        $this->num_demande = intval($iddemande);
        $this->contenu = stripslashes($contenu_note);
        $this->num_note_parent = intval($id_note_parent);
        if(!$date_note){
            $this->date_note=date("Y-m-d h:i:s",time());
        }else{
            $this->date_note=$date_note;
        }
        if($ck_prive){
            $this->prive=1;
        }else{
            $this->prive=0;
        }
        if($ck_rapport){
            $this->rapport=1;
        }else{
            $this->rapport=0;
        }
        if($ck_vue){
            $this->notes_read_gestion=0;
        }else{
            $this->notes_read_gestion=1;
        }
        if($demande_end){
            $this->demande_end=1;
        }else{
            $this->demande_end=0;
        }
        $this->notes_num_user = intval($iduser);
        $this->notes_type_user=$typeuser;
    }
    
    /*
     * Création/Modification d'une note
     */
    public function save(){
        global $demandes_email_demandes, $pmb_type_audit;
        
        if($this->id_note){
            //MODIFICATION
            $query = "UPDATE demandes_notes SET
			contenu='".addslashes($this->contenu)."',
			date_note='".$this->date_note."',
			prive='".$this->prive."',
			rapport='".$this->rapport."',
			num_action='".$this->num_action."',
			notes_num_user='".$this->notes_num_user."',
			notes_type_user='".$this->notes_type_user."',
			num_note_parent='".$this->num_note_parent."',
			notes_read_gestion='".$this->notes_read_gestion."',
			notes_read_opac='1'
			WHERE id_note='".$this->id_note."'";
            
            pmb_mysql_query($query);
            
            if($pmb_type_audit) audit::insert_modif(AUDIT_NOTE,$this->id_note);
        } else {
            //CREATION
            $query = "INSERT INTO demandes_notes SET
			contenu='".addslashes($this->contenu)."',
			date_note='".$this->date_note."',
			prive='".$this->prive."',
			rapport='".$this->rapport."',
			num_action='".$this->num_action."',
			notes_num_user='".$this->notes_num_user."',
			notes_type_user='".$this->notes_type_user."',
			num_note_parent='".$this->num_note_parent."',
			notes_read_gestion='".$this->notes_read_gestion."',
			notes_read_opac='1'";
            pmb_mysql_query($query);
            $this->id_note=pmb_mysql_insert_id();
            
            if($pmb_type_audit) audit::insert_creation(AUDIT_NOTE,$this->id_note);
            
            if(!$this->prive) {
                if ($demandes_email_demandes){
                    $this->fetch_data($this->id_note,$this->num_action);
                    $this->send_alert_by_mail($this->notes_num_user);
                }
            }
        }
        
        // Générer la réponse finale de la demande avec cette note
        if($this->demande_end){
            global $f_message;
            $f_message=$this->contenu;
            $demande = new demandes($this->num_demande);
            $demande->save_repfinale($this->id_note);
			$this->note_majParent($this->num_demande,"_gestion");
        }
    }
    
    /*
     * Suppression d'une note
     */
    public static function delete($note){
	    $note->id_note = intval($note->id_note);
        if($note->id_note){
            $req = "delete from demandes_notes where id_note='".$note->id_note."'";
            pmb_mysql_query($req);
            $req = "delete from demandes_notes where num_note_parent='".$note->id_note."'";
            pmb_mysql_query($req);
            audit::delete_audit(AUDIT_NOTE,$note->id_note);
        }
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
     * Alerte par mail
     */
    public function send_alert_by_mail($idsender){
        global $msg, $PMBusernom, $PMBuserprenom, $PMBuseremail,$opac_url_base,$pmb_url_base,$demandes_email_generic;
        
        $contenu = sprintf($msg['demandes_note_mail_new'],$PMBuserprenom." ".$PMBusernom." ",$this->libelle_action,$this->libelle_demande).'<br />';
        $contenu.=$this->contenu.'<br />';
        //$lien_opac='<a href="'.$opac_url_base.'empr.php?tab=request&lvl=list_dmde&sub=open_demande&iddemande='.$this->num_demande.'&last_modified='.$this->num_action.'#fin">'.$msg['demandes_see_last_note'].'</a>';
        $lien_gestion='<a href="'.$pmb_url_base.'demandes.php?categ=gestion&act=see_dmde&iddemande='.$this->num_demande.'&last_modified='.$this->num_action.'#fin">'.$msg['demandes_see_last_note'].'</a>';
        $objet = $msg['demandes_note_mail_new_object'];
        
        $headers  = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1";
        
        //Envoi du mail aux autres documentalistes concernés par la demande
        $req = "SELECT userid, user_email, prenom,nom FROM users
		JOIN demandes_users ON num_user=userid
		WHERE num_demande='".$this->num_demande."' AND num_user !='".$idsender."'";
        $res = pmb_mysql_query($req);
        
        while(($user = pmb_mysql_fetch_object($res))){
            if($user->user_email){
                $mail_user_demande_note = new mail_user_demande_note();
                $mail_user_demande_note->set_mail_to_id($user->userid);
                $mail_user_demande_note->set_demande_note($this);
                $mail_user_demande_note->send_mail();
            }
        }
        
        //Envoi du mail au demandeur
        $req= "SELECT id_empr, empr_prenom, empr_nom, empr_mail FROM empr
		JOIN demandes ON id_empr=num_demandeur
		WHERE id_demande='".$this->num_demande."'";
        
        $res = pmb_mysql_query($req);
        $empr = pmb_mysql_fetch_object($res);
        if($empr->empr_mail) {
            $mail_reader_demande_note = new mail_reader_demande_note();
            $mail_reader_demande_note->set_mail_to_id($empr->id_empr);
            $mail_reader_demande_note->set_demande_note($this);
            $mail_reader_demande_note->send_mail();
        }
        
        // Envoi au mail générique
        if($demandes_email_generic){
            $param=explode(",", $demandes_email_generic);
            if(($param[0]==2 || $param[0]==3) && $param[1]){
                mailpmb("",$param[1],$objet,$contenu.$lien_gestion,$PMBuserprenom." ".$PMBusernom,$PMBuseremail,$headers,"",$param[2]);
            }
        }
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
    
    /*
     * Met à jour les alertes sur l'action et la demande dont dépend la note
     */
	public function note_majParent($id_demande,$side="_gestion"){
        $id_demande = intval($id_demande);
        $ok = false;
		if($this->id_note){
		    $select = "SELECT notes_read".$side." FROM demandes_notes WHERE id_note=".$this->id_note;
            $result  = pmb_mysql_query($select);
            $read = pmb_mysql_result($result,0,0);
            
            if($read == 1){
			    if(demandes_actions::action_read($this->num_action,false,$side) && demandes::demande_read($id_demande,false,$side)){
                    $ok = true;
                }
            } else {
                // maj action : controle s'il existe des notes non lues pour l'action en cours
			    $query = "SELECT notes_read".$side." FROM demandes_notes WHERE num_action=".$this->num_action." AND id_note!=".$this->id_note." AND notes_read".$side."=1";
                $result = pmb_mysql_query($query);
                
                if(pmb_mysql_num_rows($result)){
				    $ok = demandes_actions::action_read($this->num_action,false,$side);
                } else {
				    $ok = demandes_actions::action_read($this->num_action,true,$side);
                }
                // maj demande : controle s'il existe des actions non lues pour la demande en cours
                if($ok){
				    $query = "SELECT actions_read".$side." FROM demandes_actions WHERE num_demande=".$id_demande." AND id_action!=".$this->num_action." AND actions_read".$side."=1";
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
    
    /*
     * Met à jour les alertes sur l'action et la demande dont dépend la note
     */
    public static function note_read($id_note,$booleen=true,$side="_gestion"){
        $id_note = intval($id_note);
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
	public function read($side="_gestion"){
        $read  = false;
		$query = "SELECT notes_read".$side." FROM demandes_notes WHERE id_note=".$this->id_note;
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
     * Change l'alerte de la note : si elle est lue, elle passe en non lue et inversement
     */
	public function change_read($side="_gestion"){
		$read = $this->read($side);
        $value = "";
        if($read){
            $value = 1;
        } else {
            $value = 0;
        }
		$query = "UPDATE demandes_notes SET notes_read".$side."=".$value." WHERE id_note=".$this->id_note;
        if(pmb_mysql_query($query)){
            return true;
        } else {
            return false;
        }
    }
}