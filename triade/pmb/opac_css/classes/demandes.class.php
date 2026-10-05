<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes.class.php,v 1.63.4.4 2025/05/23 07:00:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/demandes_actions.class.php");
require_once($class_path."/liste_simple.class.php");
require_once($class_path."/workflow.class.php");

// require_once("$include_path/templates/catalog.tpl.php");
// require_once("$class_path/notice.class.php");
// require_once("$class_path/tu_notice.class.php");
// require_once("$class_path/explnum.class.php");
require_once($class_path."/audit.class.php");
require_once($class_path."/demandes_types.class.php");
require_once($class_path."/acces.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/demandes_notices.class.php");

/*
 * Classe de gestion des demandes
 */
class demandes {

	public $id_demande = 0;
	public $etat_demande = 0;
	public $date_demande = '0000-00-00';
	public $deadline_demande = '0000-00-00';
	public $sujet_demande = '';
	public $num_demandeur = 0;
	public $users = array();
	public $progression = 0;
	public $theme_demande = 0;
	public $type_demande = 0;
	public $theme_libelle = '';
	public $type_libelle = '';
	public $date_prevue = '0000-00-00';
	public $titre_demande = '';
	public $liste_etat = array();
	public $workflow = array();
	public $num_notice = 0;
	public $allowed_actions=array();
	public $first_action= 1;
	public $actions=array();
	public $dmde_read_gestion=0;
	public $dmde_read_opac=0;
	public $last_modified=0;
	public $notice=0;
	public $num_user = '';
	public $allowed_pperso = array();
	public $num_user_cloture = '';

	/**
	 * Identifiant de la notice liée
	 * @var int
	 */
	protected $num_linked_notice = 0;

	/*
	 * Constructeur
	 */
	public function __construct($id=0,$lazzy_load=true){
		$id = (int) $id;
		$this->fetch_data($id,$lazzy_load);
	}

	public function fetch_data($id=0,$lazzy_load=true){
		if($this->id_demande && !$id){
			$id=$this->id_demande;
		}elseif(!$this->id_demande && $id){
			$this->id_demande=intval($id);
		}
		$this->etat_demande = 0;
		$this->date_demande = '0000-00-00';
		$this->deadline_demande = '0000-00-00';
		$this->sujet_demande = '';
		$this->num_demandeur = 0;
		$this->progression = 0;
		$this->date_prevue = '0000-00-00';
		$this->theme_demande = 0;
		$this->type_demande = 0;
		$this->titre_demande = '';
		$this->num_notice = 0;
		$this->dmde_read_gestion = 0;
		$this->dmde_read_opac = 0;
		$this->num_linked_notice = 0;
		if($this->id_demande){
			$req = "select etat_demande, date_demande, deadline_demande, sujet_demande, num_demandeur, progression, num_notice,
			date_prevue, theme_demande, type_demande, titre_demande, libelle_theme,libelle_type, allowed_actions, dmde_read_gestion, dmde_read_opac, num_linked_notice
			from demandes d, demandes_theme dt, demandes_type dy
			where dy.id_type=d.type_demande and dt.id_theme=d.theme_demande and id_demande='".$this->id_demande."'";
			$res=pmb_mysql_query($req);
			if(pmb_mysql_num_rows($res)){
				$dmde = pmb_mysql_fetch_object($res);
				$this->etat_demande = $dmde->etat_demande;
				$this->date_demande = $dmde->date_demande;
				$this->deadline_demande = $dmde->deadline_demande;
				$this->sujet_demande = $dmde->sujet_demande;
				$this->num_demandeur = $dmde->num_demandeur;
				$this->progression = $dmde->progression;
				$this->date_prevue = $dmde->date_prevue;
				$this->theme_demande = $dmde->theme_demande;
				$this->type_demande = $dmde->type_demande;
				$this->titre_demande = $dmde->titre_demande;
				$this->theme_libelle = $dmde->libelle_theme;
				$this->type_libelle = $dmde->libelle_type;
				$this->num_notice = $dmde->num_notice;
				$this->allowed_actions = unserialize($dmde->allowed_actions);
				$this->dmde_read_gestion = $dmde->dmde_read_gestion;
				$this->dmde_read_opac = $dmde->dmde_read_opac;
				$this->num_linked_notice = $dmde->num_linked_notice;

				if(empty($this->allowed_actions)){
					$workflow = new workflow('ACTIONS');
					$this->allowed_actions = $workflow->getTypeList();
					$allowed_actions = array();
					foreach($this->allowed_actions as $allowed_action){
						$allowed_action['active'] = 1;
						$allowed_actions[] = $allowed_action;
						if($allowed_action['default']){
							$this->first_action = $allowed_action['id'];
						}
					}
				}
			} else{
				$workflow = new workflow('ACTIONS');
				$this->allowed_actions = $workflow->getTypeList();
				$allowed_actions = array();
				foreach($this->allowed_actions as $allowed_action){
					$allowed_action['active'] = 1;
					$allowed_actions[] = $allowed_action;
					if($allowed_action['default']){
						$this->first_action = $allowed_action['id'];
					}
				}
			}
			$req = "select num_user, concat(prenom,' ',nom) as nom, username, users_statut from demandes_users, users where num_user=userid and num_demande='".$this->id_demande."' and users_statut=1";
			$res = pmb_mysql_query($req);
			$i=0;
			$this->users = array();
			while($user = pmb_mysql_fetch_object($res)){
				$this->users[$i]['nom'] = (trim($user->nom) ? $user->nom : $user->username);
				$this->users[$i]['id'] = $user->num_user;
				$this->users[$i]['statut'] = $user->users_statut;
				$i++;
			}
		} else {
			$this->num_user = '';
			$workflow = new workflow('ACTIONS');
			$this->allowed_actions = $workflow->getTypeList();
			$allowed_actions = array();
			foreach($this->allowed_actions as $allowed_action){
				$allowed_action['active'] = 1;
				$allowed_actions[] = $allowed_action;
				if($allowed_action['default']){
					$this->first_action = $allowed_action['id'];
				}
			}
		}
		if(empty($this->workflow)){
			$this->workflow = new workflow('DEMANDES','INITIAL');
			$this->liste_etat = $this->workflow->getStateList();
		}

		if($this->id_demande){
			$this->actions=array();
			//On charge la liste d'id des actions
			$query='SELECT id_action FROM demandes_actions WHERE num_demande='.$this->id_demande;
			$result=pmb_mysql_query($query);
			while($action=pmb_mysql_fetch_array($result,PMB_MYSQL_ASSOC)){
				if($lazzy_load){
					$this->actions[$action['id_action']]=new stdClass();
					$this->actions[$action['id_action']]->id_action=$action['id_action'];
				}else{
					$this->actions[$action['id_action']]=new demandes_actions($action['id_action']);
				}
			}

			if(!$lazzy_load){
				$this->last_modified=$this->get_last_modified_action();
			}
		}
	}

	public function get_last_modified_action(){
		$temp=0;
		foreach($this->actions as $action){
			//On cherche la dernière note modifiée
			if(!$temp && $action->last_modified){
				$temp=$action;
			}

			$dateTemp= new DateTime($temp->last_modified->date_note);
			$dateAction= new DateTime($action->last_modified->date_note);

			if($dateTemp->format('U') < $dateAction->format('U')){
				$temp = $action;
			}
		}

		if($temp){
			return $temp;
		}
	}

	public function getStateValue($current_value=0){
		foreach($this->liste_etat as $val){
			if(!$current_value){
				if($val['default']==true){
					return $val;
				}
			}else {
				if($val['id']==$current_value){
					return $val;
				}
			}
		}
	}

	public function get_content_form(){
	    global $charset;
	    global $opac_demandes_affichage_simplifie;
	    global $opac_demandes_allow_from_record;
	    
	    if(!$this->id_demande){
	        $this->date_demande = date("Y-m-d",time());
	        $this->date_prevue = date("Y-m-d",time());
	        $this->deadline_demande = date("Y-m-d",time());
	    }
	    
	    $themes = new demandes_themes('demandes_theme','id_theme','libelle_theme',$this->theme_demande);
	    $types = new demandes_types('demandes_type','id_type','libelle_type',$this->type_demande);
	    
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('id_theme', 'demandes_theme')
	    ->set_class('colonne3')
	    ->add_html_node($themes->getListSelector($this->theme_demande));
	    
	    $interface_content_form->add_element('id_type', 'demandes_type')
	    ->set_class('colonne3')
	    ->add_html_node($types->getListSelector($this->type_demande, "onchange='update_pperso(event)'"));
	    
	    $etat=$this->getStateValue($this->etat_demande);
	    $element_etat = $interface_content_form->add_element('etat', 'demandes_etat')
        ->set_class('colonne3');
        $element_etat->add_html_node($etat['comment']);
        $element_etat->add_input_node('hidden', $etat['id'])
        ->set_id('idetat')
        ->set_name('id_etat');
	    
	    $interface_content_form->add_element('titre', 'demandes_titre')
	    ->add_input_node('text', $this->titre_demande);
	    
	    $interface_content_form->add_element('sujet', 'demandes_sujet')
	    ->add_textarea_node($this->sujet_demande, 55, 4)
	    ->set_attributes(array('wrap' => 'virtual'));
	    
	    $interface_content_form->add_element('date_debut', 'demandes_date_dmde')
	    ->set_class('colonne3')
	    ->add_input_node('date', $this->date_demande);
	    
	    if(!$opac_demandes_affichage_simplifie){
	        $interface_content_form->add_element('date_prevue', 'demandes_date_prevue')
	        ->set_class('colonne3')
	        ->add_input_node('date', $this->date_prevue)
	        ->set_attributes(array('required' => 'true'));
	        
	        $interface_content_form->add_element('date_fin', 'demandes_date_butoir')
	        ->set_class('colonne3')
	        ->add_input_node('date', $this->deadline_demande)
	        ->set_attributes(array('required' => 'true'));
	    }
	    
	    $content_form = $interface_content_form->get_display();
	    $content_form .= "<div class='row' id='demandes_pperso'>" . $this->get_pperso_form_content($this->id_demande, $this->type_demande) . "</div>";
	    if($this->id_demande && $opac_demandes_allow_from_record && $this->num_linked_notice){
            $record_datas = record_display::get_record_datas($this->num_linked_notice);
            
            $interface_content_linked_record = new interface_content_form(static::class);
            $linked_record_html_node = "
            <a href='".htmlentities($record_datas->get_permalink(), ENT_QUOTES, $charset)."' title='".htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset)."' id='demandes_linked_record'>
                ".htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset)."
            </a>
            <input type='hidden' name='linked_record_id' value='".htmlentities($this->num_linked_notice, ENT_QUOTES, $charset)."'/>";
            
            $interface_content_linked_record->add_element('demandes_linked_record', 'demandes_linked_record')
            ->add_html_node($linked_record_html_node);
            
            $content_form .= $interface_content_linked_record->get_display();
	    }
	    $content_form .= "<div class='row'></div>";
	    return $content_form;
	}
	/*
	 * Formulaire de création d'une demande
	 */
	public function show_modif_form(){
		global $form_modif_demande, $msg, $charset, $id_empr;

		if(!$this->id_demande){
			$form_modif_demande = str_replace('!!form_title!!',htmlentities($msg['demandes_creation'],ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!empr_txt!!','',$form_modif_demande);
			$form_modif_demande = str_replace('!!idempr!!',$id_empr,$form_modif_demande);
			$form_modif_demande = str_replace('!!iduser!!',"",$form_modif_demande);

			$form_modif_demande = str_replace('!!iddemande!!','',$form_modif_demande);
		} else {
			$form_modif_demande = str_replace('!!form_title!!',htmlentities(sprintf($msg['demandes_modification'],' : '.$this->titre_demande),ENT_QUOTES,$charset),$form_modif_demande);

			$form_modif_demande = str_replace('!!idempr!!',$this->num_demandeur,$form_modif_demande);
			$form_modif_demande = str_replace('!!iduser!!',$this->num_user,$form_modif_demande);
			$form_modif_demande = str_replace('!!iddemande!!',$this->id_demande,$form_modif_demande);
		}

		$form_modif_demande = str_replace("!!content_form!!", $this->get_content_form(), $form_modif_demande);
		
		$act_cancel = "document.location='./empr.php?tab=request&lvl=list_dmde'";
		$act_form = "./empr.php?tab=request&lvl=list_dmde&sub=save_demande";

		$form_modif_demande = str_replace('!!form_action!!',$act_form,$form_modif_demande);
		$form_modif_demande = str_replace('!!cancel_action!!',$act_cancel,$form_modif_demande);
		print $form_modif_demande;
	}

	public static function is_notice_visible($demande){
		if($demande->num_notice) {
			//La notice est-elle visible ?
			$req = "select notice_visible_opac as visible, notice_visible_opac_abon as visu_abo from notice_statut join notices on id_notice_statut=statut where notice_id='".$demande->num_notice."'";
			$res_vis = pmb_mysql_query($req);
			$noti_display = pmb_mysql_fetch_object($res_vis);

			if($noti_display->visible || $noti_display->visu_abo){
				return true;
			}
		}
		return false;
	}

	/*
	 * Formulaire de création de la liste des demandes
	 */
	public function show_list_form(){
		global $form_liste_demande;
		global $id_empr, $idetat;
		global $view;

		$onChange="onchange=\"submit();\"";
		$form_liste_demande = str_replace('!!select_etat!!',$this->getStateSelector($idetat,$onChange,true),$form_liste_demande);
		
		/* plus demandé...
		if($etat_demande_validee==1 && $view!="all" && !$etat_demande_a_valider){// une seule validée:
			print "<script>document.location=\"./empr.php?tab=request&lvl=list_dmde&sub=open_demande&iddemande=".$demande_id_validee."&#fin\";</script>";
			return;
		}
		if($etat_demande_a_valider==1 && $view!="all" && !$etat_demande_validee){// une seul à valider:
			print "<script>document.location=\"./empr.php?tab=request&lvl=list_dmde&sub=open_demande&iddemande=".$demande_id_a_valider."&#fin\";</script>";
			return;
		}
		*/
		$liste = list_opac_demandes_ui::get_instance(array('demandeur' => $id_empr))->get_display_list();
		$form_liste_demande = str_replace('!!liste_dmde!!',$liste,$form_liste_demande);

		print $form_liste_demande;
	}

	public function set_properties_from_form() {
		global $sujet,$iddemande ,$idetat, $titre, $id_theme, $id_type;
		global $date_debut, $date_fin, $date_prevue, $idempr, $linked_record_id;
		global $progression, $demandes_statut_notice;

		if(!$date_prevue)$date_prevue=$date_debut;
		if(!$date_fin)$date_fin=$date_debut;

		$this->id_demande = intval($iddemande);
		$this->titre_demande = stripslashes($titre);
		$this->sujet_demande = stripslashes($sujet);
		$this->date_demande = $date_debut;
		$this->date_prevue = $date_prevue;
		$this->deadline_demande = $date_fin;
		$this->num_user = '';
		$this->progression = $progression;
		$this->num_demandeur = intval($idempr);
		$this->type_demande = intval($id_type);
		$this->theme_demande = intval($id_theme);
		$this->etat_demande = intval($idetat);
		if (!isset($linked_record_id)) $linked_record_id = 0;
		$this->set_num_linked_notice($linked_record_id);
		$this->num_user_cloture = '';
		if($this->etat_demande == 4 || $this->etat_demande == 5 ) {
			$this->num_user_cloture=SESSuserid;
		}
		//Création d'une stdClass (pour alleger) => doit matcher à une notice, mais un peu lourd ...
		$this->notice=new stdClass();
		$this->notice->tit1=$this->titre_demande;
		$this->notice->n_contenu=$this->sujet_demande;
		$this->notice->notice_id=$this->num_notice;
		$this->notice->statut=$demandes_statut_notice;
	}

	protected function save_notice(){
		global $demandes_notice_auto, $gestion_acces_active, $gestion_acces_user_notice, $gestion_acces_empr_notice;
		global $pmb_type_audit;

		//CREATION de la notice associée
		if($demandes_notice_auto === "1"){
			$query = "INSERT INTO notices SET
			tit1='".addslashes($this->notice->tit1)."',
			n_contenu='".addslashes($this->notice->n_contenu)."',
			statut ='".$this->notice->statut."',
			create_date='".date('Y-m-d H:i:s')."'";

			pmb_mysql_query($query);
			$this->num_notice= $this->notice->num_notice = pmb_mysql_insert_id();

			//Indexation de la notice
			demandes_notices::majNoticesTotal($this->num_notice);

			if($pmb_type_audit) audit::insert_creation(AUDIT_NOTICE,$this->num_notice);

			//droits d'acces
			if ($gestion_acces_active==1) {
				$ac= new acces();

				//traitement des droits acces user_notice
				if ($gestion_acces_user_notice==1) {
					$dom_1= $ac->setDomain(1);
					$dom_1->storeUserRights(0, $this->num_notice);
				}
				//traitement des droits acces empr_notice
				if ($gestion_acces_empr_notice==1) {
					$dom_2= $ac->setDomain(2);
					$dom_2->storeUserRights(0, $this->num_notice);
				}
			}
		}
	}

	protected function save_demandes_users() {
		//Enregistrement dans demandes_users
		$date_creation = date("Y-m-d",time());

		if (!empty($this->id_demande) && !empty($this->num_user)) {
			$query = "UPDATE demandes_users SET users_statut=0 WHERE num_user NOT IN (".implode(',',$this->num_user).") AND num_demande='".$this->id_demande."'";
			pmb_mysql_query($query);
			$query = "UPDATE demandes_users SET users_statut=1 WHERE num_user IN (".implode(',',$this->num_user).") AND num_demande='".$this->id_demande."'";
			pmb_mysql_query($query);
			foreach($this->num_user as $id_user) {
				$query = "insert into demandes_users set num_user='".$id_user."', num_demande='".$this->id_demande."', date_creation='".$date_creation."', users_statut=1";
				pmb_mysql_query($query);
			}
		}
	}

	/*
	 * Création/Modification d'une demande
	*/
	public function save(){
		global $pmb_type_audit, $demandes_email_demandes;

		if($this->id_demande){

			//MODIFICATION
			$query = "UPDATE demandes SET
			sujet_demande='".addslashes($this->sujet_demande)."',
			num_demandeur='".$this->num_demandeur."',
			date_demande='".$this->date_demande."',
			deadline_demande='".$this->deadline_demande."',
			date_prevue='".$this->date_prevue."',
			progression='".$this->progression."',
			titre_demande='".addslashes($this->titre_demande)."',
			type_demande='".$this->type_demande."',
			theme_demande='".$this->theme_demande."',
			num_user_cloture='".$this->num_user_cloture."',
			dmde_read_gestion='1',
			num_linked_notice = '".$this->get_num_linked_notice()."'
			WHERE id_demande='".$this->id_demande."'";

			pmb_mysql_query($query);
			if($pmb_type_audit) audit::insert_modif(AUDIT_DEMANDE,$this->id_demande);
		} else {
			//On ajoute une notice ?
			$this->save_notice();

			//CREATION de la demande
			$query = "INSERT INTO demandes SET
			sujet_demande='".addslashes($this->sujet_demande)."',
			etat_demande='".$this->etat_demande."',
			num_demandeur='".$this->num_demandeur."',
			date_demande='".$this->date_demande."',
			date_prevue='".$this->date_prevue."',
			deadline_demande='".$this->deadline_demande."',
			progression='".$this->progression."',
			titre_demande='".addslashes($this->titre_demande)."',
			type_demande='".$this->type_demande."',
			theme_demande='".$this->theme_demande."',
			num_notice='".$this->num_notice."',
			dmde_read_gestion='1',
			num_linked_notice = '".$this->get_num_linked_notice()."'" ;
			pmb_mysql_query($query);

			$this->id_demande = pmb_mysql_insert_id();
			if($pmb_type_audit) audit::insert_creation(AUDIT_DEMANDE,$this->id_demande);

			if ($demandes_email_demandes){
				$this->send_alert_by_mail($this->num_demandeur);
			}
		}

		//Vérification des champs personalisés
		$p_perso=new parametres_perso("demandes");
		$nberrors=$p_perso->check_submited_fields();
		if ($nberrors) {
			// error_message_history("",$p_perso->error_message,1);
		} else {
			//Insertion des champs personalisés
			$p_perso->rec_fields_perso($this->id_demande);
		}

		//MAJ des users de la notice
		$this->save_demandes_users();
	}

	public function send_alert_by_mail($idsender){
		global $msg, $empr_nom, $empr_prenom, $empr_mail,$pmb_url_base, $demandes_email_generic;

		$contenu =  $msg['demandes_mail_new_demande'];
		$contenu=str_replace("!!nom!!", $empr_prenom." ".$empr_nom." ", $contenu);
		$contenu=str_replace("!!titre_demande!!", $this->titre_demande, $contenu);

		$contenu.='<br />'.$this->sujet_demande.'<br />';

		$lien_gestion='<a href="'.$pmb_url_base.'demandes.php?categ=gestion&act=see_dmde&iddemande='.$this->id_demande.'">'.$msg['demandes_see_last_note'].'</a>';
		$objet = $msg['demandes_mail_new_demande_object'];

		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1";

		//Envoi aux utilisateurs
		$query_users = "select userid, nom, prenom, user_email from users where user_email like('%@%') and user_alert_demandesmail=1";
		$result_users = @pmb_mysql_query($query_users);
		if ($result_users) {
			if (pmb_mysql_num_rows($result_users) > 0) {
				while ($user=pmb_mysql_fetch_object($result_users)) {
					$mail_opac_user_demande = new mail_opac_user_demande();
					$mail_opac_user_demande->set_mail_to_id($user->userid);
					$mail_opac_user_demande->set_demande($this);
					$mail_opac_user_demande->send_mail();
				}
			}
		}

		// Envoi au mail générique
		if($demandes_email_generic){
			$param=explode(",", $demandes_email_generic);
			if(($param[0]==1 || $param[0]==3) && $param[1]){
				mailpmb("",$param[1],$objet,$contenu.$lien_gestion,$empr_prenom." ".$empr_nom,$empr_mail,$headers,"",$param[2]);
			}
		}
	}

	/*
	 * Suppression d'une demande
	 */
	public static function delete($demande){
		global $delnoti;

		if($demande->id_demande){
			$demande->fetch_data($demande->id_demande,false);
			if($delnoti){
				//Si on supprime la notice associée
				$query = "SELECT num_notice FROM demandes WHERE id_demande = ".$demande->id_demande." AND num_notice!=0";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					notice::del_notice(pmb_mysql_result($result,0,0));
				}
			}
			// suppression des actions et des notes
			if (!empty($demande->actions)) {
				foreach($demande->actions as $action) {
					demandes_actions::delete($action);
				}
			}
			// suppression des liens user - demande
			$req = "delete from demandes_users where num_demande='".$demande->id_demande."'";
			pmb_mysql_query($req);
			//suppression des doc num
			$req = "delete ed,eda from explnum_doc ed
			join explnum_doc_actions eda on ed.id_explnum_doc=eda.num_explnum_doc
			join demandes_actions da on eda.num_action=da.id_action
			where da.num_demande=".$demande->id_demande;
			pmb_mysql_query($req);
			// suppression des valeurs de CP
			$p_perso=new parametres_perso("demandes");
			$p_perso->delete_values($demande->id_demande);
			// suppression de la demande
			$req = "delete from demandes where id_demande='".$demande->id_demande."'";
			pmb_mysql_query($req);
			//suppression de l'audit
			audit::delete_audit(AUDIT_DEMANDE,$demande->id_demande);
		}
	}


	/*
	 * Retourne le sélecteur des états de la demandes
	 */
	public function getStateSelector($idetat=0,$action='',$default=false){
		global $charset, $msg;

		$selector = "<select name='idetat' $action>";
		$select="";
		if($default) $selector .= "<option value='0'>".htmlentities($msg['demandes_all_states'],ENT_QUOTES,$charset)."</option>";
		for($i=1;$i<=count($this->liste_etat);$i++){
			if($idetat == $i) $select = "selected";
			$selector .= "<option value='".$this->liste_etat[$i]['id']."' $select>".htmlentities($this->liste_etat[$i]['comment'],ENT_QUOTES,$charset)."</option>";
			$select = "";
		}
		$selector .= "</select>";

		return $selector;
	}

	/*
	 * Retourne le sélecteur des utilisateurs ayant le droit aux demandes
	 */
	public function getUsersSelector($action='',$default=false,$multiple=false,$nonassign=false){
		global $charset,$msg, $iduser;

		if($multiple)
			$mul = " name='iduser[]' multiple ";
		else $mul = " name='iduser' ";

		if(!$this->id_demande){
			$req="select concat(prenom,' ',nom) as nom, userid, username
			from users
			where rights>=16384";
		} else {
			$req="select concat(prenom,' ',nom) as nom, userid , if(isnull(num_demande),0,if((users_statut),1,0)) as actif, username
			from users
			left join demandes_users on (num_user=userid and num_demande='".$this->id_demande."')
			where rights>=16384";
		}

		$res = pmb_mysql_query($req);
		$select = "";
		$selector = "<select  $mul $action >";
		if($default) $selector .= "<option value='0'>".htmlentities($msg['demandes_all_users'],ENT_QUOTES,$charset)."</option>";
		if($nonassign) $selector .=  "<option value='-1' ".($iduser == -1 ?'selected' :'').">".htmlentities($msg['demandes_not_assigned'],ENT_QUOTES,$charset)."</option>";
		while(($user=pmb_mysql_fetch_object($res))){
			if($user->actif) $select="selected";
			$name = (trim($user->nom) ? $user->nom :$user->username);
			if($iduser == $user->userid) $select="selected";
			$selector .= "<option value='".$user->userid."' $select>".htmlentities($name,ENT_QUOTES,$charset)."</option>";
			$select = "";
		}
		$selector .= "</select>";

		return $selector;
	}

	/*
	 * Retourne le nom de l'utilisateur (celui qui traitera la demande)
	 */
	public function getUserLib($iduser){
	    $iduser = intval($iduser);
		$req = "select concat(prenom,' ',nom) as nom, userid, username from users where userid='".$iduser."'";
		$res = pmb_mysql_query($req);
		$user = pmb_mysql_fetch_object($res);
		return ( trim($user->nom) ? $user->nom : $user->username );
	}

	/*
	 * Retourne les caractéristiques de l'emprunteur qui effectue la demande
	 */
	public function getCaracEmpr($idempr){
	    $idempr = intval($idempr);
		$req = "select concat(empr_prenom,' ',empr_nom) as nom, id_empr,empr_cb from empr where id_empr='".$idempr."'";
		$res = pmb_mysql_query($req);
		$empr = pmb_mysql_fetch_array($res);
		return $empr;
	}

	public function get_consult_content_form() {
	    global $charset;
	    global $opac_demandes_affichage_simplifie;
	    
	    $interface_content_form = new interface_demandes_content_form(static::class);
	    $interface_content_form->set_grid_model('column_3');
	    $interface_content_form->add_element('id_theme', 'demandes_theme', 'flat')
	    ->add_class('empr_demande_theme')
	    ->add_text_node($this->theme_libelle);
        
	    $interface_content_form->add_element('etat', 'demandes_etat', 'flat')
	    ->add_class('empr_demande_etat')
	    ->add_text_node($this->workflow->getStateCommentById($this->etat_demande));
	    
	    $interface_content_form->add_element('date_debut', 'demandes_date_dmde', 'flat')
	    ->add_class('empr_demande_date_dmde')
	    ->add_text_node(formatdate($this->date_demande));
	    
	    $interface_content_form->add_element('sujet', 'demandes_sujet', 'flat')
	    ->add_class('empr_demande_sujet')
	    ->add_text_node($this->sujet_demande);
	    
	    if(!$opac_demandes_affichage_simplifie) {
	        $interface_content_form->add_element('date_prevue', 'demandes_date_prevue', 'flat')
	        ->add_class('empr_demande_date_prevue')
	        ->add_text_node(formatdate($this->date_prevue));
	        
	        $interface_content_form->add_element('empty_1', '', 'flat')
	        ->add_text_node('');
	        
	        $interface_content_form->add_element('id_type', 'demandes_type', 'flat')
	        ->add_class('empr_demande_type')
	        ->add_text_node($this->type_libelle);
	        
	        $users = '';
	        if (!empty($this->users) && is_countable($this->users)) {
	            for($i=0;$i<sizeof($this->users);$i++){
	                if($i == sizeof($this->users)-1) {
	                    $users .= $this->users[$i]['nom'];
	                } else {
	                    $users .= $this->users[$i]['nom']." / ";
	                }
	            }
	        }
	        $interface_content_form->add_element('attribution', 'demandes_attribution', 'flat')
	        ->add_class('empr_demandes_attribution')
	        ->add_text_node($users);
	        
	        $interface_content_form->add_element('date_fin', 'demandes_date_butoir', 'flat')
	        ->add_class('empr_demande_date_butoir')
	        ->add_text_node(formatdate($this->deadline_demande));
	        
	        $interface_content_form->add_element('empty_2', '', 'flat')
	        ->add_text_node('');
	        
	        $interface_content_form->add_element('progression', 'demandes_progression', 'flat')
	        ->add_class('empr_demande_progression')
	        ->add_html_node("<span id='progressiondemande_".$this->id_demande."' name='progressiondemande_".$this->id_demande."' dynamics='demandes,progressiondemande' dynamics_params='text'>".htmlentities($this->progression.'%',ENT_QUOTES,$charset)."</span>");
	    } else {
	        $interface_content_form->add_element('empty_1', '', 'flat')
	        ->add_text_node('');
	        
	        $interface_content_form->add_element('id_type', 'demandes_type', 'flat')
	        ->add_class('empr_demande_type')
	        ->add_text_node($this->type_libelle);
	    }
	    return $interface_content_form->get_display();
	}
	
	/*
	 * Affichage du formulaire de consultation d'une demande
	 */
	public function show_consult_form($last_modified=0){

		global $idetat,$iduser,$idempr,$user_input;
		global $date_debut,$date_fin, $id_type, $id_theme, $dmde_loc;
		global $form_consult_dmde, $charset, $msg,$demandes_init_workflow, $form_consult_linked_record;
		global $opac_demandes_no_action, $opac_demandes_allow_from_record, $opac_url_base;

		
		$form_consult_dmde = str_replace('!!form_title!!',htmlentities($this->titre_demande,ENT_QUOTES,$charset),$form_consult_dmde);
		
		$form_consult_dmde = str_replace('!!content_form!!', $this->get_consult_content_form(),$form_consult_dmde);


		$carac_empr = $this->getCaracEmpr($this->num_demandeur);
		$nom = $carac_empr['nom'];
// 		$cb = $carac_empr['empr_cb'];

		$form_consult_dmde = str_replace('!!demandeur!!',$nom,$form_consult_dmde);
		$form_consult_dmde = str_replace('!!iddemande!!',$this->id_demande,$form_consult_dmde);
		if ($opac_demandes_allow_from_record && $this->num_linked_notice) {
			$record_datas = record_display::get_record_datas($this->num_linked_notice);
			$form_consult_dmde = str_replace('!!form_linked_record!!', $form_consult_linked_record, $form_consult_dmde);
			$form_consult_dmde = str_replace('!!linked_record!!', htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset), $form_consult_dmde);
			$form_consult_dmde = str_replace('!!linked_record_id!!', htmlentities($this->num_linked_notice, ENT_QUOTES, $charset), $form_consult_dmde);
			$form_consult_dmde = str_replace('!!linked_record_link!!', htmlentities($record_datas->get_permalink(), ENT_QUOTES, $charset), $form_consult_dmde);
		} else {
			$form_consult_dmde = str_replace('!!form_linked_record!!',"",$form_consult_dmde);
		}

		//Champs personalisés
		$perso_aff = "" ;
		$p_perso = new parametres_perso("demandes");
		if (!$p_perso->no_special_fields) {
			$perso_=$p_perso->show_fields($this->id_demande);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if ($p["AFF"] !== '' && $p["OPAC_SHOW"]) {
				    $perso_aff .="<br />".$p["TITRE"]." ".($p["TYPE"]=='html'?$p["AFF"]:nl2br($p["AFF"]));
				}
			}
		}
		$form_consult_dmde = str_replace("!!champs_perso!!",$perso_aff,$form_consult_dmde);

		if(self::is_notice_visible($this)){
			$link_noti = "<a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->num_notice."' title='".$msg['demandes_see_notice']."'><img src='".get_url_icon('mois.gif')."' /></a>";
		} else{
			$link_noti = "";
		}
		$form_consult_dmde = str_replace('!!icone!!',$link_noti,$form_consult_dmde);

		//construction de l'url de retour
		$params_retour='';
		if($idetat){
			$params_retour.="&idetat=".$idetat;
		}
// 		if($iduser){
// 			$params_retour.="&iduser=".$iduser;
// 		}
// 		if($idempr){
// 			$params_retour.="&idempr=".$idempr;
// 		}
		if($user_input){
			$params_retour.="&user_input=".$user_input;
		}
		if($date_debut){
			$params_retour.="&date_debut=".$date_debut;
		}
		if($date_fin){
			$params_retour.="&date_fin=".$date_fin;
		}
		if($id_type){
			$params_retour.="&id_type=".$id_type;
		}
		if($id_theme){
			$params_retour.="&id_theme=".$id_theme;
		}
		if($dmde_loc){
			$params_retour.="&dmde_loc=".$dmde_loc;
		}

		if($params_retour){
			$form_consult_dmde=str_replace('!!params_retour!!',htmlentities(stripslashes($params_retour),ENT_QUOTES, $charset),$form_consult_dmde);
		}else{
			$form_consult_dmde=str_replace('!!params_retour!!',"",$form_consult_dmde);
		}

		if((is_countable($this->users) && sizeof($this->users) && $demandes_init_workflow!=="2")&& !$opac_demandes_no_action){

			$listActionButton='<h4>'.$msg['demandes_action_type_add'].'</h4>';
			if(is_countable($this->allowed_actions) && sizeof($this->allowed_actions)){
				foreach($this->allowed_actions as $key=>$actionType){
					if($actionType['active']==1){
						$listActionButton.="&nbsp;<input type='button' class='bouton' value='".htmlentities($actionType['comment'],ENT_QUOTES, $charset)."' onClick=\"document.location='./empr.php?tab=request&lvl=list_dmde&sub=add_action&type_action=".$actionType['id']."&iddemande=$this->id_demande'\" />";
					}
				}
			}
			if($listActionButton){
				$form_consult_dmde=str_replace('!!add_actions_list!!',$listActionButton,$form_consult_dmde);
			}else{
				$form_consult_dmde=str_replace('!!add_actions_list!!',"",$form_consult_dmde);
			}
		}else{
			$form_consult_dmde=str_replace('!!add_actions_list!!',"",$form_consult_dmde);
		}

		$modify_button="";
		foreach($this->workflow->object_states_by_id as $key=>$value){
			//id d'etat de demande par défaut
			if($this->workflow->object_workflow['STARTSTATE'][0]['value']==$value && $key==$this->etat_demande){
				$modify_button="<input type='button' class='bouton' value='".$msg['demandes_modify']."' onClick=\"document.location='./empr.php?tab=request&lvl=list_dmde&sub=add_demande&iddemande=$this->id_demande'\" />";
			}
		}

		$form_consult_dmde=str_replace('!!demande_modify!!',$modify_button,$form_consult_dmde);

		if(is_countable($this->users) && sizeof($this->users) && $demandes_init_workflow!=="2"){
			//Liste des actions
			$this->fetch_data($this->id_demande,false);

			if($this->etat_demande == 4 || $this->etat_demande == 5){
				$form_consult_dmde.=demandes_actions::show_list_actions($this->actions, $this->id_demande);
			}elseif($last_modified){
				$form_consult_dmde.=demandes_actions::show_list_actions($this->actions, $this->id_demande,$last_modified);
			}elseif($this->last_modified){
				$form_consult_dmde.=demandes_actions::show_list_actions($this->actions, $this->id_demande,$this->last_modified->id_action);
			}else{
				$form_consult_dmde.=demandes_actions::show_list_actions($this->actions, $this->id_demande);
			}
		}

		if ($this->etat_demande == 1 && empty($this->actions) && $this->dmde_read_opac == 1){
			demandes::demande_read($this->id_demande,true,"_opac");
			$this->fetch_data($this->id_demande,false);
		}
		print $form_consult_dmde;
	}

	/*
	 * Affiche la liste des boutons correspondants à l'état en cours
	 */
	public function getDisplayStateBtn($list_etat=array(),$multi=0){
		global $charset,$msg;

		if($multi){
			$message = $msg['demandes_change_checked_states'];
		} else $message = $msg['demandes_change_state'];
		$display = "<label class='etiquette'>".$message." : </label>";
		for($i=0;$i<count($list_etat);$i++){
			$display .= "&nbsp;<input class='bouton' type='submit' name='btn_".$list_etat[$i]['id']."' value='".htmlentities($list_etat[$i]['comment'],ENT_QUOTES,$charset)."' onclick='this.form.state.value=\"".$list_etat[$i]['id']."\"; this.form.act.value=\"change_state\";'/>";
		}

		return $display;
	}

	/*
	 * Changement d'etat d'une demande
	 */
	public function change_state($state){
		global $demandes_init_workflow, $demandes_default_action, $pmb_type_audit;
		global $PMBuserid;
		global $idstatut;

		if($demandes_init_workflow==="1" && $PMBuserid){
			//La demande est elle attribué à un utilisateur ?
			$query='SELECT 1 FROM demandes_users WHERE num_demande='.$this->id_demande;
			$result=pmb_mysql_query($query);
			if(!pmb_mysql_num_rows($result)){
				//si non, on attribue
				$query='INSERT INTO demandes_users SET num_user="'.$PMBuserid.'", num_demande="'.$this->id_demande.'", date_creation="'.date("Y-m-d",time()).'", users_statut=1';
				pmb_mysql_query($query);
			}
		}

		$req = "update demandes set etat_demande=$state where id_demande='".$this->id_demande."'";
		pmb_mysql_query($req);

		if($state == 2 && $demandes_default_action === "1"){
			$query = "SELECT id_action FROM demandes_actions WHERE num_demande=".$this->id_demande;
			$result = pmb_mysql_query($query);
			if(!pmb_mysql_num_rows($result)){

				$action_default = new demandes_actions();
				$action_default->num_demande = $this->id_demande;
				$action_default->actions_num_user = $PMBuserid;
				$action_default->date_action = date("Y-m-d",time());
				$action_default->deadline_action = date("Y-m-d",time());
				if($action_default->list_statut){
					$action_default->statut_action = "";
					for($i=1;$i<=count($action_default->list_statut);$i++){
						if($action_default->list_statut[$i]['default']) {
							$action_default->statut_action = $action_default->list_statut[$i]['id'];
						}
					}
					if($idstatut == "") {
						reset($action_default->list_statut);
						$first_statut = current($action_default->list_statut);
						$action_default->statut_action = $first_statut['id'];
					}
				}
				$action_default->type_action = $this->first_action;
				$action_default->sujet_action = $this->titre_demande;
				$action_default->detail_action = $this->sujet_demande;
				$action_default->save();
			}
		}

		if($pmb_type_audit) audit::insert_modif(AUDIT_DEMANDE,$this->id_demande);
	}

	/*
	 * Montre la liste des documents pouvant etre inclus dans le document
	 */
	public function show_docnum_to_attach(){
		global $form_liste_docnum, $msg, $charset, $base_path, $pmb_indexation_docnum_default;

		$req="select id_explnum_doc as id, explnum_doc_nomfichier as nom, num_explnum,
			concat(explnum_index_sew,'',explnum_index_wew) as indexer
			from explnum_doc
			join explnum_doc_actions on (id_explnum_doc=num_explnum_doc and rapport=1)
			join demandes_actions on num_action=id_action
			left join explnum on explnum_id=num_explnum
			where num_demande='".$this->id_demande."'";
		$res = pmb_mysql_query($req);
		$liste="";
		if(pmb_mysql_num_rows($res)){
			while(($doc = pmb_mysql_fetch_object($res))){
				if($doc->num_explnum) {
					$check = 'checked';
				}
				if($pmb_indexation_docnum_default || $doc->indexer){
					$check_index = 'checked';
				}
				$liste .= "
				<div class='row'>
					<div class='colonne3'>
						<input type='checkbox' id='chk[$doc->id]' value='$doc->id' name='chk[]' $check /><label for='chk[$doc->id]' class='etiquette'>".htmlentities($doc->nom,ENT_QUOTES,$charset)."</label>&nbsp;
						<a href=\"$base_path/explnum_doc.php?explnumdoc_id=".$doc->id."'\" target=\"_blank\"><img src='".get_url_icon('globe_orange.png')."' /></a>
					</div>
					<div class='colonne3'>
						<input type='checkbox' id='ck_index[$doc->id]' value='$doc->id' name='ck_index[]' $check_index/><label for='ck_index[$doc->id]' class='etiquette'>".htmlentities($msg['demandes_docnum_indexer'],ENT_QUOTES,$charset)."</label>&nbsp;
					</div>
				</div>
				<div class='row'></div>";
				$check = "";
				$check_index = "";
			}
			$btn_attach = "<input type='submit' class='bouton' value='".$msg['demandes_attach_checked_docnum']."' onClick='this.form.act.value=\"save_attach\" ; return verifChk();' />";
			$form_liste_docnum = str_replace('!!btn_attach!!',$btn_attach,$form_liste_docnum);
		} else {
			$liste = htmlentities($msg['demandes_no_docnum'],ENT_QUOTES,$charset);
			$form_liste_docnum = str_replace('!!btn_attach!!','',$form_liste_docnum);
		}

		$form_liste_docnum = str_replace('!!liste_docnum!!',$liste,$form_liste_docnum);
		$form_liste_docnum = str_replace('!!iddemande!!',$this->id_demande,$form_liste_docnum);

		print $form_liste_docnum;
	}

	/*
	 * Attache les documents numériques à la notice
	 */
	public function attach_docnum(){
		global $chk, $ck_index, $pmb_indexation_docnum;

		for($i=0;$i<count($chk);$i++){
			//On attache les documents numériques cochés
			$req = "select explnum_doc_nomfichier as nom ,explnum_doc_mimetype as mime,explnum_doc_data as data,explnum_doc_extfichier as ext
			from explnum_doc
			join explnum_doc_actions on num_explnum_doc=id_explnum_doc
			join demandes_actions on num_action=id_action
			where id_explnum_doc='".$chk[$i]."'
			and num_explnum = 0
			and num_demande='".$this->id_demande."'
			";
			$res = pmb_mysql_query($req);
			if(pmb_mysql_num_rows($res)){
				$expl = pmb_mysql_fetch_object($res);
				$req = "insert into explnum(explnum_notice,explnum_nom,explnum_nomfichier,explnum_mimetype,explnum_data,explnum_extfichier) values
					('".$this->num_notice."','".addslashes($expl->nom)."','".addslashes($expl->nom)."','".addslashes($expl->mime)."','".addslashes($expl->data)."','".addslashes($expl->ext)."')";
				pmb_mysql_query($req);
				$id_explnum = pmb_mysql_insert_id();
				$req = "update explnum_doc_actions set num_explnum='".$id_explnum."' where num_explnum_doc='".$chk[$i]."'";
				pmb_mysql_query($req);
				if($ck_index[$i] && $pmb_indexation_docnum){
					$expl = new explnum($id_explnum);
					$expl->indexer_docnum();
				}
			}
		}
			//On désattache les autres
			if($chk){
				$req = "select id_explnum_doc from explnum_doc where id_explnum_doc not in ('".implode('\',\'',$chk)."')";
				$res = pmb_mysql_query($req);
				while(($expl = pmb_mysql_fetch_object($res))){
					$req = "delete e from explnum e
					join explnum_doc_actions on num_explnum=explnum_id
					where num_explnum_doc='".$expl->id_explnum_doc."'";
					pmb_mysql_query($req);
					$req = "update explnum_doc_actions set num_explnum='0' where num_explnum_doc='".$expl->id_explnum_doc."'";
					pmb_mysql_query($req);
				}
			} else {
				$req ="select id_explnum_doc
					from explnum_doc
					join explnum_doc_actions on num_explnum_doc=id_explnum_doc
					join demandes_actions on num_action=id_action
					where num_explnum != 0
					and num_demande='".$this->id_demande."'";
				$res = pmb_mysql_query($req);
				while(($expl = pmb_mysql_fetch_object($res))){
					$req = "delete e from explnum e
					join explnum_doc_actions on num_explnum=explnum_id
					where num_explnum_doc='".$expl->id_explnum_doc."'";
					pmb_mysql_query($req);
					$req = "update explnum_doc_actions set num_explnum='0' where num_explnum_doc='".$expl->id_explnum_doc."'";
					pmb_mysql_query($req);
				}
			}
	}


	/*
	 * Affiche le formulaire de création/modification d'une notice
	 */
	public function show_notice_form(){

		// affichage du form de création/modification d'une notice
		$myNotice = new notice($this->num_notice);
		if(!$myNotice->id) {
			$myNotice->tit1 = $this->titre_demande;
		}

		$myNotice->action = "./demandes.php?categ=gestion&act=upd_notice&iddemande=".$this->id_demande."&id=";
		$myNotice->link_annul = $this->get_gestion_link();
		$myNotice->id_demande = $this->id_demande;
		print $myNotice->show_form();
	}

	/*
	 * Formulaire de validation de la suppression de notice
	 */
	public function suppr_notice_form(){
		global $msg, $chk, $iddemande, $current_module;

		$display = "
		<form class='form-$current_module' name='suppr_noti'  method='post' action='./demandes.php?categ=list'>
		<h3>".$msg["demandes_del_notice"]."</h3>
		<div class='form-contenu'>
			<div class='row'>
				<div>
					<img src='".get_url_icon('error.gif')."'  >
					<strong>".$msg["demandes_del_linked_notice"]."</strong>
				</div>
			</div>
		</div>
		<div></div>
		<div class='row'>
			<input type='hidden' name='delnoti' id='delnoti'>
			<input type='hidden' name='act' value='suppr'>
			<input type='hidden' name='iddemande' value='$iddemande'>";
		if($chk){
			$display .= "<input type='hidden' name='chk' value='".implode(',',$chk)."'>";
		}
		$display .=
		"<input type='submit' name='non_btn' class='bouton' value='$msg[39]' onclick='this.form.delnoti.value=\"0\";'>
		<input type='submit' class='bouton' name='ok_btn' value='$msg[40]' onclick='this.form.delnoti.value=\"1\";'>
		</div>

		</form>
		";

		print $display;
	}

	public function attribuer(){
		global $chk, $iduser;

		for($i=0;$i<count($chk);$i++){
			$req = "insert into demandes_users set num_user=$iduser, num_demande=$chk[$i], date_creation='".today()."', users_statut=1";
			pmb_mysql_query($req);
		}
	}

	public function create_notice(){
		global $demandes_statut_notice, $pmb_type_audit;

		// creation notice à partir de la demande
		$req = "insert into notices set
				tit1='".addslashes($this->titre_demande)."',
				n_contenu='".addslashes($this->sujet_demande)."',
				statut ='".$demandes_statut_notice."'
				";
				pmb_mysql_query($req);
				$id_notice = pmb_mysql_insert_id();
				notice::majNotices($id_notice);
				if($pmb_type_audit) audit::insert_creation(AUDIT_NOTICE,$id_notice);

		// mise à jour de la demande
		$req = "UPDATE demandes SET num_notice=".$id_notice." WHERE id_demande=".$this->id_demande;
		pmb_mysql_query($req);
		$this->num_notice=$id_notice;
	}

	public function delete_notice(){
		notice::del_notice($this->num_notice);
		// mise à jour de la demande
		$req = "UPDATE demandes SET num_notice=0 WHERE id_demande=".$this->id_demande;
		pmb_mysql_query($req);
		$this->num_notice=0;
	}

	// mise à jour de l'alerte en fonction des alertes présentes sur les actions de la demande en cours
	public static function dmde_majRead($id_demande,$side="_opac"){
		// on teste s'il y a des actions non lues
		$query = "SELECT id_action FROM demandes_actions WHERE num_demande=".$id_demande. " AND actions_read".$side."=1";
		$result = pmb_mysql_query($query);
		$value=0;
		if(pmb_mysql_num_rows($result)){
			$value=1;
		} else {
			// sinon, on teste si la demande est non lue et non validée en gestion et s'il n'y a aucune action de crée
			$query = "SELECT id_action FROM demandes_actions WHERE num_demande=".$id_demande;
			$res = pmb_mysql_query($query);
			if(!pmb_mysql_num_rows($res)){
				$query = "SELECT dmde_read".$side.", etat_demande FROM demandes WHERE id_demande=".$id_demande;
				$res2 = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($res2)){
					$etat = pmb_mysql_result($res2,0,"etat_demande");
					$read = pmb_mysql_result($res2,0,"dmde_read".$side);
					if($etat == 1 && $read == 1){
						$value = 1;
					}
				}
			}
		}
		$query2 = "UPDATE demandes SET dmde_read".$side."=".$value." WHERE id_demande=".$id_demande;
		pmb_mysql_query($query2);
		return $value;
	}

	// fonction qui renvoie un booléen indiquant si une demande a été lue ou pas
	public static function read($demande,$side="_opac"){
		$read  = false;
		$query = "SELECT dmde_read".$side." FROM demandes WHERE id_demande=".$demande->id_demande;
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
	 * Change l'alerte de la demande : si elle est lue, elle passe en non lue et inversement
	*/
	public static function change_read($demande,$side="_opac"){
		$read = demandes::read($demande,$side);
		$value = "";
		if($read){
			$value = 1;
		} else {
			$value = 0;
		}
		$query = "UPDATE demandes SET dmde_read".$side."=".$value." WHERE id_demande=".$demande->id_demande;
		if(pmb_mysql_query($query)){
			return true;
		} else {
			return false;
		}
	}

	public static function get_first_tab(){
		global $id_empr;

		$query="select id_demande from demandes where num_demandeur='".$id_empr."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			return ;
		}else{
			return "add_demande";
		}
	}

	/*
	 * changement forcé de la mention "lue" ou "pas lue" de l'action
	* true => action est déjà lue donc pas d'alerte
	* false => alerte
	*/
	public static function demande_read($id_demande,$booleen=true,$side="_gestion"){
		$value = "";
		if($booleen){
			$value = 0;
		} else {
			$value = 1;
		}
		$query = "UPDATE demandes SET dmde_read".$side."=".$value." WHERE id_demande=".$id_demande;
		if(pmb_mysql_query($query)){
			return true;
		} else {
			return false;
		}
	}

	public function get_sujet_demande() {
		return $this->sujet_demande;
	}

	public function get_titre_demande() {
		return $this->titre_demande;
	}

	/**
	 * Retourne l'identifiant de la notice liée
	 * @return int
	 */
	public function get_num_linked_notice() {
		return $this->num_linked_notice;
	}

	/**
	 * Setter de l'identifiant de la notice liée à la demande
	 * @param int $num_linked_notice
	 */
	public function set_num_linked_notice($num_linked_notice) {
		$this->num_linked_notice = $num_linked_notice;
	}

	public function get_gestion_link(){
		global $base_path;

		return $base_path.'/demandes.php?categ=gestion&act=see_dmde&iddemande='.$this->id_demande;
	}

	public function get_id() {
		return $this->id_demande;
	}

	/**
	 * Retourne le formulaire des champs personnalisés des demandes
	 * @param number $id_demande
	 * @param number $type_demande
	 * @return string
	 */
	public function get_pperso_form_content($id_demande = 0, $type_demande = 0)
	{
		$perso = '';
		$id_demande = intval($id_demande);
		$type_demande = intval($type_demande);

	    $p_perso=new parametres_perso("demandes");
		$types_allowed_pperso = array();

	    if (!$p_perso->no_special_fields) {
			$where_clause = "";
			if($type_demande != 0) {
				$where_clause = "WHERE id_type = $type_demande";
			}

			$query = "SELECT allowed_pperso FROM demandes_type $where_clause ORDER BY libelle_type LIMIT 1";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$allowed_pperso = pmb_mysql_result($result, 0, 0);
				if($allowed_pperso != '') {
					$types_allowed_pperso = encoding_normalize::json_decode($allowed_pperso, true);
					if(!is_array($types_allowed_pperso)) {
					    $types_allowed_pperso = [];
					}
				}
			}
			
	        $c=0;
	        $perso="<hr />";
	        $perso_=$p_perso->show_editable_fields($id_demande);

			//Application du tri des champs persos paramétré en gestion
			usort($perso_["FIELDS"], function($a, $b) use ($types_allowed_pperso) {
				$id_a = $a["ID"];
				$id_b = $b["ID"];
				if (!isset($types_allowed_pperso[$id_a])) {
					return 1;
				}
				if (!isset($types_allowed_pperso[$id_b])) {
					return -1;
				}
				return $types_allowed_pperso[$id_a]['order'] <=> $types_allowed_pperso[$id_b]['order'];
			});

	        for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
	            $p=$perso_["FIELDS"][$i];
				//Masquage du champ si non visible à l'OPAC
				if(isset($p["OPAC_SHOW"]) && intval($p["OPAC_SHOW"]) == 0) {
					continue;
				}
				//Gestion des champs persos par type
				if(! array_key_exists($p["ID"], $types_allowed_pperso) || $types_allowed_pperso[$p['ID']]['allowed'] == 0) {
					continue;
				}
	            if ($c==0) $perso.="<div class='row'>";
	            $perso.="<div class='colonne2'><label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]." </label>".$p["COMMENT_DISPLAY"]."<div class='row'>".$p["AFF"]."</div></div>";
	            $c++;
	            if ($c==2) {
	                $perso.="</div>";
	                $c=0;
	            }
	        }
	        if ($c==1) $perso.="<div class='colonne2'>&nbsp;</div></div>";
	        $perso=$perso_["CHECK_SCRIPTS"]."\n".$perso;
	    } else {
	        $perso="<script>function check_form() { return true; }</script>";
	    }
		return $perso;
	}
}
