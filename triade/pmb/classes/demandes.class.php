<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes.class.php,v 1.63 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/demandes_actions.class.php");
require_once($class_path."/liste_simple.class.php");
require_once($class_path."/workflow.class.php");

require_once("$include_path/templates/catalog.tpl.php");
require_once("$class_path/notice.class.php");
require_once("$class_path/tu_notice.class.php");
require_once("$class_path/explnum.class.php");
require_once($class_path."/audit.class.php");

require_once($class_path."/acces.class.php");
require_once($class_path."/parametres_perso.class.php");

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
	public $last_modified=0;
	public $notice=0;
	public $reponse_finale='';
	public $dmde_read_opac=0;
	public $num_faq_question = 0;
	
	/**
	 * Identifiant de la notice liée
	 * @var int
	 */
	protected $num_linked_notice = 0;
	
	/*
	 * Constructeur
	 */
	public function __construct($id=0,$lazzy_load=true){
		$id += 0;
		$this->fetch_data($id,$lazzy_load);
	}
	
	public function fetch_data($id=0,$lazzy_load=true){
		global $base_path, $dbh;
		
		if($this->id_demande && !$id){
			$id=$this->id_demande;
		}elseif(!$this->id_demande && $id){
			$this->id_demande=$id;
		}
		
		if($this->id_demande){
			$req = "select etat_demande, date_demande, deadline_demande, sujet_demande, num_demandeur, progression, num_notice,
			date_prevue, theme_demande, type_demande, titre_demande, libelle_theme,libelle_type, allowed_actions, 
			dmde_read_gestion, reponse_finale , dmde_read_opac, num_linked_notice 
			from demandes d, demandes_theme dt, demandes_type dy
			where dy.id_type=d.type_demande and dt.id_theme=d.theme_demande and id_demande='".$this->id_demande."'";
			$res=pmb_mysql_query($req,$dbh);
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
				$this->reponse_finale = $dmde->reponse_finale;
				$this->num_linked_notice = $dmde->num_linked_notice;
				
				if(!count($this->allowed_actions)){
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
				
				//recherche de l'entrée dans la FAQ
				$query = "select id_faq_question from faq_questions where faq_question_num_demande = ".$this->id_demande;
				$result = pmb_mysql_query($query,$dbh);
				if(pmb_mysql_num_rows($result)){
					$this->num_faq_question = pmb_mysql_result($result,0,0);
				}
			} else{
				$this->id_demande = 0;
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
				$workflow = new workflow('ACTIONS');
				$this->allowed_actions = $workflow->getTypeList();
				$allowed_actions = array();
				$this->dmde_read_gestion = 0;
				$this->dmde_read_opac = 0;
				$this->reponse_finale = '';
				$this->num_linked_notice = 0;
				foreach($this->allowed_actions as $allowed_action){
					$allowed_action['active'] = 1;
					$allowed_actions[] = $allowed_action;
					if($allowed_action['default']){
						$this->first_action = $allowed_action['id'];
					}
				}
			}
			$req = "select num_user, concat(prenom,' ',nom) as nom, username, users_statut from demandes_users, users where num_user=userid 
					and num_demande='".$this->id_demande."' and users_statut=1";
			$res = pmb_mysql_query($req,$dbh);
			$i=0;
			$this->users = array();
			while($user = pmb_mysql_fetch_object($res)){
				$this->users[$i]['nom'] = (trim($user->nom) ? $user->nom : $user->username);
				$this->users[$i]['id'] = $user->num_user;
				$this->users[$i]['statut'] = $user->users_statut;
				$i++;
			}					

		} else {
			$this->id_demande = 0;
			$this->etat_demande = 0;
			$this->date_demande = '0000-00-00';
			$this->deadline_demande = '0000-00-00';
			$this->sujet_demande = '';
			$this->num_demandeur = 0;
			$this->num_user = array();
			$this->progression = 0;
			$this->date_prevue = '0000-00-00';
			$this->theme_demande = 0;
			$this->type_demande = 0;
			$this->titre_demande = '';
			$this->num_notice = 0;
			$workflow = new workflow('ACTIONS');
			$this->allowed_actions = $workflow->getTypeList();
			$allowed_actions = array();
			$this->dmde_read_gestion = 0;
			$this->dmde_read_opac = 0;
			$this->reponse_finale = '';
			$this->num_linked_notice = 0;
			foreach($this->allowed_actions as $allowed_action){
				$allowed_action['active'] = 1;
				$allowed_actions[] = $allowed_action;
				if($allowed_action['default']){
					$this->first_action = $allowed_action['id'];
				}
			}
		}
		if(!sizeof($this->workflow)){
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
				$this->last_modified=self::get_last_modified_action($this->actions);
			}
		}
	}
	
	public static function get_last_modified_action($actions){
		$temp=0;
		foreach($actions as $id_action=>$action){
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
	
	/*
	 * Formulaire de création d'une demande
	 */
	public function show_modif_form(){
		global $form_modif_demande, $msg, $charset, $form_linked_record, $url_base;
		
		$themes = new demandes_themes('demandes_theme','id_theme','libelle_theme',$this->theme_demande);
		$types = new demandes_types('demandes_type','id_type','libelle_type',$this->type_demande);
		
		if(!$this->id_demande){
			$form_modif_demande = str_replace('!!form_title!!',htmlentities($msg['demandes_creation'],ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!sujet!!','',$form_modif_demande);
			$form_modif_demande = str_replace('!!progression!!','',$form_modif_demande);
			$form_modif_demande = str_replace('!!empr_txt!!','',$form_modif_demande);
			$form_modif_demande = str_replace('!!id_empr!!','',$form_modif_demande);
			$form_modif_demande = str_replace('!!titre!!','',$form_modif_demande);
			$form_modif_demande = str_replace('!!select_etat!!',$this->getStateSelector(),$form_modif_demande);
			$form_modif_demande = str_replace('!!select_user!!',$this->getUsersSelector('',false,true),$form_modif_demande);
			$form_modif_demande = str_replace('!!select_theme!!',$themes->getListSelector(),$form_modif_demande);
			$form_modif_demande = str_replace('!!select_type!!',$types->getListSelector(),$form_modif_demande);
			
			$date = formatdate(today());
			$date_debut=date("Y-m-d",time());
			$date_dmde = "<input type='button' class='bouton' id='date_debut_btn' name='date_debut_btn' value='!!date_debut_btn!!' 
				onClick=\"openPopUp('./select.php?what=calendrier&caller=modif_dmde&date_caller=!!date_debut!!&param1=date_debut&param2=date_debut_btn&auto_submit=NO&date_anterieure=YES', 'calendar')\"/>";
			$form_modif_demande = str_replace('!!date_demande!!',$date_dmde,$form_modif_demande);
			
			$form_modif_demande = str_replace('!!date_fin_btn!!',$date,$form_modif_demande);
			$form_modif_demande = str_replace('!!date_debut_btn!!',$date,$form_modif_demande);
			$form_modif_demande = str_replace('!!date_debut!!',$date_debut,$form_modif_demande);
			$form_modif_demande = str_replace('!!date_fin!!',$date_debut,$form_modif_demande);
			$form_modif_demande = str_replace('!!date_prevue!!',$date_debut,$form_modif_demande);
			$form_modif_demande = str_replace('!!date_prevue_btn!!',$date,$form_modif_demande);
			
			
			$form_modif_demande = str_replace('!!btn_suppr!!','',$form_modif_demande);		
			$form_modif_demande = str_replace('!!iddemande!!','',$form_modif_demande);	
			$act_cancel = "document.location='./demandes.php?categ=list'";
			$act_form = "./demandes.php?categ=list";

		} else {
			$btn_suppr = "<input type='submit' class='bouton' value='$msg[63]' onclick='this.form.act.value=\"suppr\"; return confirm_delete();' />";			
			$form_modif_demande = str_replace('!!form_title!!',htmlentities(sprintf($msg['demandes_modification'],' : '.$this->titre_demande),ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!btn_suppr!!',$btn_suppr,$form_modif_demande);
			
			$form_modif_demande = str_replace('!!titre!!',htmlentities($this->titre_demande,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!sujet!!',htmlentities($this->sujet_demande,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!progression!!',htmlentities($this->progression,ENT_QUOTES,$charset),$form_modif_demande);
			$carac_empr = $this->getCaracEmpr($this->num_demandeur);
			$nom = $carac_empr['nom'];
			$form_modif_demande = str_replace('!!empr_txt!!',htmlentities($nom,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!idempr!!',$this->num_demandeur,$form_modif_demande);
			$form_modif_demande = str_replace('!!titre!!',$this->titre_demande,$form_modif_demande);
			$form_modif_demande = str_replace('!!select_etat!!',$this->workflow->getStateCommentById($this->etat_demande),$form_modif_demande);
			$form_modif_demande = str_replace('!!select_user!!',$this->getUsersSelector('',false,true),$form_modif_demande);
			$form_modif_demande = str_replace('!!select_theme!!',$themes->getListSelector($this->theme_demande),$form_modif_demande);
			$form_modif_demande = str_replace('!!select_type!!',$types->getListSelector($this->type_demande),$form_modif_demande);
			
			$form_modif_demande = str_replace('!!date_fin_btn!!',formatdate($this->deadline_demande),$form_modif_demande);
			$form_modif_demande = str_replace('!!date_demande!!',formatdate($this->date_demande),$form_modif_demande);
			$form_modif_demande = str_replace('!!date_debut!!',htmlentities($this->date_demande,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!date_fin!!',htmlentities($this->deadline_demande,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!date_prevue_btn!!',formatdate($this->date_prevue),$form_modif_demande);
			$form_modif_demande = str_replace('!!date_prevue!!',htmlentities($this->date_prevue,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!iddemande!!',$this->id_demande,$form_modif_demande);

			$act_cancel = "document.location='./demandes.php?categ=gestion&act=see_dmde&iddemande=$this->id_demande'";
			$act_form = "./demandes.php?categ=gestion";

		}
		$form_modif_demande = str_replace('!!form_linked_record!!', $form_linked_record, $form_modif_demande);
		if ($this->num_linked_notice) {
			$display = new mono_display($this->num_linked_notice, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
			$form_modif_demande = str_replace('!!linked_record!!', htmlentities($display->result, ENT_QUOTES, $charset), $form_modif_demande);
			$form_modif_demande = str_replace('!!linked_record_id!!', htmlentities($this->num_linked_notice, ENT_QUOTES, $charset), $form_modif_demande);
		} else {
			$form_modif_demande = str_replace('!!linked_record!!', "", $form_modif_demande);
			$form_modif_demande = str_replace('!!linked_record_id!!', 0, $form_modif_demande);
		}
		
		$perso = '';
		$p_perso=new parametres_perso("demandes");
		if (!$p_perso->no_special_fields) {
			$c=0;
			$perso="<hr />";
			$perso_=$p_perso->show_editable_fields($this->id_demande);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
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
		$form_modif_demande = str_replace("!!champs_perso!!",$perso,$form_modif_demande);
		
		$form_modif_demande = str_replace('!!form_action!!',$act_form,$form_modif_demande);
		$form_modif_demande = str_replace('!!cancel_action!!',$act_cancel,$form_modif_demande);
		print $form_modif_demande;
	}
	
	/*
	 * Formulaire de création de la liste des demandes
	 */
	public function show_list_form(){
		global $form_filtre_demande, $form_liste_demande;
		global $dbh, $charset, $msg;
		global $idetat,$iduser,$idempr,$user_input;
		global $date_debut,$date_fin, $id_type, $id_theme, $dmde_loc;
		
		//Formulaire des filtres
		$date_deb="
			<input type='hidden' id='date_debut' name='date_debut' value='!!date_debut!!' />
			<input type='button' class='bouton' id='date_debut_btn' name='date_debut_btn' value='!!date_debut_btn!!' onClick=\"openPopUp('./select.php?what=calendrier&caller=search&date_caller=!!date_debut!!&param1=date_debut&param2=date_debut_btn&auto_submit=NO&date_anterieure=YES', 'calendar')\"/>
		";
		$date_but="
			<input type='hidden' id='date_fin' name='date_fin' value='!!date_fin!!' />
			<input type='button' class='bouton' id='date_fin_btn' name='date_fin_btn' value='!!date_fin_btn!!' onClick=\"openPopUp('./select.php?what=calendrier&caller=search&date_caller=!!date_fin!!&param1=date_fin&param2=date_fin_btn&auto_submit=NO&date_anterieure=YES', 'calendar')\"/>
		";
		//Affichage de l'état en entete de page (si séléctioné)
		if($idetat){
			$form_filtre_demande=str_replace('!!etat_demandes!!',htmlentities(stripslashes($this->liste_etat[$idetat]['comment']),ENT_QUOTES, $charset),$form_filtre_demande);
		}else{
			$form_filtre_demande=str_replace('!!etat_demandes!!',"",$form_filtre_demande);
		}
		
		if($date_debut && $date_fin){
			$date_deb = str_replace('!!date_debut_btn!!',formatdate($date_debut),$date_deb);
			$date_but = str_replace('!!date_fin_btn!!',formatdate($date_fin),$date_but);
			$date_deb = str_replace('!!date_debut!!',$date_debut,$date_deb);
			$date_but = str_replace('!!date_fin!!',$date_fin,$date_but);
		} else {
			$date_lib = formatdate(today());
			$date_sql = date("Y-m-d",time());		
			$date_deb = str_replace('!!date_debut_btn!!',$date_lib,$date_deb);
			$date_but = str_replace('!!date_fin_btn!!',$date_lib,$date_but);
			$date_deb = str_replace('!!date_debut!!',$date_sql,$date_deb);
			$date_but = str_replace('!!date_fin!!',$date_sql,$date_but);
		}
		if($idempr){
			$form_filtre_demande = str_replace('!!idempr!!',$idempr,$form_filtre_demande);
			$carac_empr = $this->getCaracEmpr($idempr);
			$nom = $carac_empr['nom'];
			$form_filtre_demande = str_replace('!!empr_txt!!',$nom,$form_filtre_demande);
		} else {
			$form_filtre_demande = str_replace('!!idempr!!','',$form_filtre_demande);
			$form_filtre_demande = str_replace('!!empr_txt!!','',$form_filtre_demande);
		}
		
		$form_filtre_demande = str_replace('!!user_input!!',htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$form_filtre_demande);
		$form_filtre_demande = str_replace('!!periode!!',sprintf($msg['demandes_filtre_periode_lib'],$date_deb,$date_but),$form_filtre_demande);
		$onchange = "onchange='this.form.act.value=\"search\";submit()'";
		$form_filtre_demande = str_replace('!!affectation!!',$this->getUsersSelector($onchange,true,false,true),$form_filtre_demande);
		$form_filtre_demande = str_replace('!!state!!',$this->getStateSelector($idetat,$onchange,true),$form_filtre_demande);
		
		$themes = new demandes_themes('demandes_theme','id_theme','libelle_theme',$id_type);
		$types = new demandes_types('demandes_type','id_type','libelle_type',$id_theme);
		
		$form_filtre_demande = str_replace('!!theme!!',$themes->getListSelector($id_theme,$onchange,true),$form_filtre_demande);
		$form_filtre_demande = str_replace('!!type!!',$types->getListSelector($id_type,$onchange,true),$form_filtre_demande);
		
		$req_loc = "select idlocation, location_libelle from docs_location";
		$res_loc = pmb_mysql_query($req_loc,$dbh);
		$sel_loc = "<select id='dmde_loc' name='dmde_loc' onchange='this.form.act.value=\"search\";submit()' >";
		$sel_loc .= "<option value='0' ".(!$dmde_loc ? 'selected' : '').">".htmlentities($msg['demandes_localisation_all'],ENT_QUOTES,$charset)."</option>";
		while($loc = pmb_mysql_fetch_object($res_loc)){
			$sel_loc .= "<option value='".$loc->idlocation."' ".(($dmde_loc==$loc->idlocation) ? 'selected' : '').">".htmlentities($loc->location_libelle,ENT_QUOTES,$charset)."</option>";
		}
		$sel_loc.= "</select>";
		$form_filtre_demande = str_replace('!!localisation!!',$sel_loc,$form_filtre_demande);
		
		$perso = '';
		$p_perso=new parametres_perso("demandes");
		if (!$p_perso->no_special_fields) {
			$c=0;
			$perso_=$p_perso->show_search_fields();
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if ($c==0) $perso.="<div class='row'>";
				$perso.="<div class='colonne3'><label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]."</label><div class='row'>".$p["AFF"]."</div></div>";
				$c++;
				if ($c==3) {
					$perso.="</div>";
					$c=0;
				}
			}
			if ($c==1) {
				$perso.="<div class='colonne2'>&nbsp;</div><div class='colonne2'>&nbsp;</div></div>";
			} elseif ($c==2) {
				$perso.="<div class='colonne2'>&nbsp;</div></div>";
			}
		}
		$form_filtre_demande = str_replace("!!champs_perso!!",$perso,$form_filtre_demande);
		
		print $form_filtre_demande;
		
		$header_champs_perso = "";
		reset($p_perso->t_fields);
		$nb_cp_column = 0;
		foreach ($p_perso->t_fields as $key => $val) {
			$header_champs_perso .= "<th>".htmlentities($val["TITRE"],ENT_QUOTES,$charset)."</th>";
			$nb_cp_column++;
		}
		
		//Formulaire de la liste
		$req = self::getQueryFilter($idetat,$iduser,$idempr,$user_input,$date_debut,$date_fin, $id_theme, $id_type,$dmde_loc);
		$res = pmb_mysql_query($req,$dbh);
		
		$liste ="";
		$states_btn ="";
		$affectation_btn ="";
		$btn_suppr ="";
		$nb_demandes = pmb_mysql_num_rows($res);
		if($nb_demandes){
			$parity=1;						
			while(($dmde = pmb_mysql_fetch_object($res))){
				
				$dmde=new demandes($dmde->id_demande);
				if ($parity % 2) {
					$pair_impair = "even";
				} else {
					$pair_impair = "odd";
				}
				$parity += 1;
				$tr_javascript = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
				$action = "onclick=\"document.location='./demandes.php?categ=gestion&act=see_dmde&iddemande=".$dmde->id_demande;
				//Ajout des éléments de retour vers la bonne liste
				if($idetat){
					$action.="&idetat=".$idetat;
				}
				if($iduser){
					$action.="&iduser=".$iduser;
				}
				if($idempr){
					$action.="&idempr=".$idempr;
				}
				if($user_input){
					$action.="&user_input=".$user_input;
				}
				if($date_debut){
					$action.="&date_debut=".$date_debut;
				}
				if($date_fin){
					$action.="&date_fin=".$date_fin;
				}
				if($id_type){
					$action.="&id_type=".$id_type;
				}
				if($id_theme){
					$action.="&id_theme=".$id_theme;
				}
				if($dmde_loc){
					$action.="&dmde_loc=".$dmde_loc;
				}
				$action.="'\"";
				
				// affichage en gras si nouveauté du côté des notes ou des actions		
				$dmde->dmde_read_gestion = demandes::dmde_majRead($dmde->id_demande,"_gestion");
				if($dmde->dmde_read_gestion == 1){
					$style=" style='cursor: pointer; font-weight:bold'";					
				} else {
					$style=" style='cursor: pointer'";
				}
				
				$liste .= "<tr id='dmde".$dmde->id_demande."' class='".$pair_impair."' ".$tr_javascript.$style."  >";
				
				$carac_empr = $dmde->getCaracEmpr($dmde->num_demandeur);
				$nom_empr = $carac_empr['nom'];
				
				$nom_user='';
				if(sizeof($dmde->users)){
					foreach($dmde->users as $id=>$user){
						if($user['statut']==1){
							if($nom_user){
								$nom_user.="/ ";
							}
							$nom_user.=$user['nom'];
						}
					}
				}

				$liste .= "<td><img hspace=\"3\" border=\"0\" onclick=\"expand_action('action".$dmde->id_demande."','$dmde->id_demande', true); return false;\" title=\"\" id=\"action".$dmde->id_demande."Img\" name=\"imEx\" class=\"img_plus\" src=\"".get_url_icon('plus.gif')."\"></td>";
				$liste .= "<td>";
				if($dmde->dmde_read_gestion == 1){
					// remplacer $action le jour où on décide d'activer la modif d'état manuellement par onclick=\"change_read_dmde('dmde".$dmde->id_demande."','$dmde->id_demande', true); return false;\"
					$liste .= "<img hspace=\"3\" border=\"0\" title=\"\" ".$action." id=\"dmde".$dmde->id_demande."Img1\" class=\"img_plus\" src='".get_url_icon('notification_empty.png')."' style='display:none'>
								<img hspace=\"3\" border=\"0\" title=\"" . $msg['demandes_new']. "\" ".$action." id=\"dmde".$dmde->id_demande."Img2\" class=\"img_plus\" src='".get_url_icon('notification_new.png')."'>";
				} else {
					// remplacer $action le jour où on décide d'activer la modif d'état manuellement par onclick=\"change_read_dmde('dmde".$dmde->id_demande."','$dmde->id_demande', true); return false;\"
					$liste .= "<img hspace=\"3\" border=\"0\" title=\"\" ".$action." id=\"dmde".$dmde->id_demande."Img1\" class=\"img_plus\" src='".get_url_icon('notification_empty.png')."' >
								<img hspace=\"3\" border=\"0\" title=\"" . $msg['demandes_new']. "\" ".$action." id=\"dmde".$dmde->id_demande."Img2\" class=\"img_plus\" src='".get_url_icon('notification_new.png')."' style='display:none'>";
				}				
				$liste .= "
					</td>
					<td $action>".htmlentities($themes->getLabel($dmde->theme_demande),ENT_QUOTES,$charset)."</td>
					<td $action>".htmlentities($types->getLabel($dmde->type_demande),ENT_QUOTES,$charset)."</td>
					<td $action>".htmlentities($dmde->titre_demande,ENT_QUOTES,$charset)."</td>
					<td $action>".htmlentities($dmde->workflow->getStateCommentById($dmde->etat_demande),ENT_QUOTES,$charset)."</td>
					<td $action>".htmlentities(formatdate($dmde->date_demande),ENT_QUOTES,$charset)."</td>
					<td $action>".htmlentities(formatdate($dmde->date_prevue),ENT_QUOTES,$charset)."</td>
					<td $action>".htmlentities(formatdate($dmde->deadline_demande),ENT_QUOTES,$charset)."</td>
					<td $action>".htmlentities($nom_empr,ENT_QUOTES,$charset)."</td>
					<td $action>".htmlentities($nom_user,ENT_QUOTES,$charset)."</td>
					<td><span id='progressiondemande_".$dmde->id_demande."'  dynamics='demandes,progressiondemande' dynamics_params='img/img' >
						<img src='".get_url_icon('jauge.png')."' height='15px' width=\"".$dmde->progression."%\" title='".$dmde->progression."%' />
						</span>
					</td>";
					$perso_=$p_perso->show_fields($dmde->id_demande);
					if(isset($perso_["FIELDS"])) {
						for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
							$p=$perso_["FIELDS"][$i];
							$liste .= "<td>".($p["TYPE"]=='html'?$p["AFF"]:nl2br($p["AFF"]))."</td>";
						}
					}
					if($dmde->num_notice)
						$liste .= "<td><a href='./catalog.php?categ=isbd&id=$dmde->num_notice'><img style='border:0px' class='align_middle' src='".get_url_icon('notice.gif')."' alt='".htmlentities($msg['demandes_see_notice'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_see_notice'],ENT_QUOTES,$charset)."'></a></td>";
					else $liste .= "<td></td>";
					$liste .= "<td ><input type='checkbox' id='chk[".$dmde->id_demande."]' name='chk[]' value='".$dmde->id_demande."'></td>";
				
				$liste .= "</tr>";		

				//Le détail de l'action, contient les notes
				$liste .="<tr id=\"action".$dmde->id_demande."Child\" style=\"display:none\">
				<td></td>
				<td colspan=\"".($nb_cp_column+13)."\" id=\"action".$dmde->id_demande."ChildTd\">";
					
				$liste .="</td>
				</tr>";
				
			}
			$btn_suppr = "<input type='submit' class='bouton' value='$msg[63]' onclick='this.form.act.value=\"suppr_noti\"; return verifChk(\"suppr\");'/>";

			//afficher la liste des boutons de changement d'état
			if($idetat){
				$states = $this->workflow->getStateList($idetat);
				$states_btn = $this->getDisplayStateBtn($states,1);
			}
			if($iduser==-1){
				$affectation_btn = "<input type='submit' class='bouton' name='affect_btn' id='affect_btn' onclick='this.form.act.value=\"affecter\";return verifChk();' value='".htmlentities($msg['demandes_attribution_checked'],ENT_QUOTES,$charset)."' />&nbsp;".$this->getUsersSelector();
			}
			
		} else {
			$liste .= "<tr><td>".$msg['demandes_liste_vide']."</td></tr>";
		}
		$form_liste_demande = str_replace('!!header_champs_perso!!',$header_champs_perso,$form_liste_demande);
		$form_liste_demande = str_replace('!!btn_etat!!',$states_btn,$form_liste_demande);
		$form_liste_demande = str_replace('!!btn_attribue!!',$affectation_btn,$form_liste_demande);
		$form_liste_demande = str_replace('!!btn_suppr!!',$btn_suppr,$form_liste_demande);
		$form_liste_demande = str_replace('!!count_dmde!!',($nb_demandes ? '('.$nb_demandes.')' : ''),$form_liste_demande);
		$form_liste_demande = str_replace('!!liste_dmde!!',$liste,$form_liste_demande);
		
		
		print $form_liste_demande;
	}
	
	public static function get_values_from_form(&$demande){
		global $sujet,$iddemande ,$idetat, $titre, $id_theme, $id_type;
		global $date_debut, $date_fin, $date_prevue, $idempr;
		global $iduser, $progression, $demandes_statut_notice, $linked_record_id;
		global $demandes_init_workflow;
		
		
		$demande->id_demande=$iddemande;
		$demande->titre_demande = $titre;
		$demande->sujet_demande = $sujet;
		$demande->date_demande = $date_debut;
		$demande->date_prevue = $date_prevue;
		$demande->deadline_demande = $date_fin;
		$demande->num_user = $iduser;
		$demande->progression = $progression;
		$demande->num_demandeur = $idempr;
		$demande->type_demande = $id_type;
		$demande->theme_demande = $id_theme;
		$demande->etat_demande=$idetat;
		if (!isset($linked_record_id)) $linked_record_id = 0;
		$demande->set_num_linked_notice($linked_record_id);
		$demande->num_user_cloture = '';
		if($idetat == 4 || $idetat == 5 ) {
			$demande->num_user_cloture=SESSuserid;
		}
		//Création d'une stdClass (pour alleger) => doit matcher à une notice, mais un peu lourd ...
		$demande->notice=new stdClass();
		$demande->notice->tit1=$demande->titre_demande;
		$demande->notice->n_contenu=$demande->sujet_demande;
		$demande->notice->notice_id=$demande->num_notice;
		$demande->notice->statut=$demandes_statut_notice;
	}
	
	public static function save_notice(&$demande){
		global $dbh;
		global $demandes_notice_auto, $gestion_acces_active, $gestion_acces_user_notice, $gestion_acces_empr_notice;
		
		//CREATION de la notice associée
		if($demandes_notice_auto === "1"){
			$query = "INSERT INTO notices SET
			tit1='".$demande->notice->tit1."',
			n_contenu='".$demande->notice->n_contenu."',
			statut ='".$demande->notice->statut."',
			create_date='".date('Y-m-d H:i:s')."'";
			
			pmb_mysql_query($query,$dbh);
			$demande->num_notice= $demande->notice->num_notice = pmb_mysql_insert_id($dbh);
			
			notice::majNotices($demande->num_notice);
			if($pmb_type_audit) audit::insert_creation(AUDIT_NOTICE,$demande->num_notice);
			
			//droits d'acces
			if ($gestion_acces_active==1) {
				$ac= new acces();
			
				//traitement des droits acces user_notice
				if ($gestion_acces_user_notice==1) {
					$dom_1= $ac->setDomain(1);
					$dom_1->storeUserRights(0, $demande->num_notice);
				}
				//traitement des droits acces empr_notice
				if ($gestion_acces_empr_notice==1) {
					$dom_2= $ac->setDomain(2);
					$dom_2->storeUserRights(0, $demande->num_notice);
				}
			}
		}		
	}
	
	public static function save_demandes_users(&$demande){
		global $dbh;
		
		//Enregistrement dans demandes_users
		$date_creation=date("Y-m-d",time());
		
		if($demande->id_demande && sizeof($demande->num_user)){
			$query = "UPDATE demandes_users SET users_statut=0 WHERE num_user NOT IN (".implode(',',$demande->num_user).") AND num_demande='".$demande->id_demande."'";
			pmb_mysql_query($query,$dbh);
			$query = "UPDATE demandes_users SET users_statut=1 WHERE num_user IN (".implode(',',$demande->num_user).") AND num_demande='".$demande->id_demande."'";
			pmb_mysql_query($query,$dbh);
			foreach($demande->num_user as $id_user){
				$query = "insert into demandes_users set num_user='".$id_user."', num_demande='".$demande->id_demande."', date_creation='".$date_creation."', users_statut=1";
				pmb_mysql_query($query,$dbh);
			}
		} elseif($demande->id_demande && !sizeof($demande->num_user)){
			$query = "UPDATE demandes_users SET users_statut=0 WHERE num_demande='".$demande->id_demande."'";
			pmb_mysql_query($query,$dbh);
		}
	}
	
	/*
	 * Création/Modification d'une demande
	*/
	public static function save(&$demande){
		global $dbh, $pmb_type_audit;
	
		if($demande->id_demande){
				
			//MODIFICATION
			$query = "UPDATE demandes SET
			sujet_demande='".$demande->sujet_demande."',
			num_demandeur='".$demande->num_demandeur."',
			date_demande='".$demande->date_demande."',
			deadline_demande='".$demande->deadline_demande."',
			date_prevue='".$demande->date_prevue."',
			progression='".$demande->progression."',
			titre_demande='".$demande->titre_demande."',
			type_demande='".$demande->type_demande."',
			theme_demande='".$demande->theme_demande."',
			num_user_cloture='".$demande->num_user_cloture."',
			num_linked_notice = '".$demande->get_num_linked_notice()."'
			WHERE id_demande='".$demande->id_demande."'";
			
			pmb_mysql_query($query,$dbh);
	
			if($pmb_type_audit) audit::insert_modif(AUDIT_DEMANDE,$demande->id_demande);
			
		} else {
			//On ajoute une notice ?
			self::save_notice($demande);
			
			//CREATION de la demande
			$query = "INSERT INTO demandes SET
			sujet_demande='".$demande->sujet_demande."',
			etat_demande='".$demande->etat_demande."',
			num_demandeur='".$demande->num_demandeur."',
			date_demande='".$demande->date_demande."',
			date_prevue='".$demande->date_prevue."',
			deadline_demande='".$demande->deadline_demande."',
			progression='".$demande->progression."',
			titre_demande='".$demande->titre_demande."',
			type_demande='".$demande->type_demande."',
			theme_demande='".$demande->theme_demande."',
			num_notice='".$demande->num_notice."',
			dmde_read_opac='1',
			num_linked_notice = '".$demande->get_num_linked_notice()."'" ;
			pmb_mysql_query($query,$dbh);
			
			$demande->id_demande = pmb_mysql_insert_id($dbh);
			if($pmb_type_audit) audit::insert_creation(AUDIT_DEMANDE,$demande->id_demande);
		}
		
		//Vérification des champs personalisés
		$p_perso=new parametres_perso("demandes");
		$nberrors=$p_perso->check_submited_fields();
		if ($nberrors) {
			error_message_history("",$p_perso->error_message,1);
		} else {
			//Insertion des champs personalisés
			$p_perso->rec_fields_perso($demande->id_demande);
		}
		
		//MAJ des users de la demande
		self::save_demandes_users($demande);
	}
	
	/*
	 * Suppression d'une demande
	 */
	public static function delete($demande){
		global $dbh, $delnoti;
		
		if($demande->id_demande){
			$demande->fetch_data($demande->id_demande,false);
			if($delnoti){
				//Si on supprime la notice associée
				$query = "SELECT num_notice FROM demandes WHERE id_demande = ".$demande->id_demande." AND num_notice!=0";
				$result = pmb_mysql_query($query,$dbh);
				if(pmb_mysql_num_rows($result)){
					notice::del_notice(pmb_mysql_result($result,0,0));
				}				
			} 		
			// suppression des actions et des notes		
			if(sizeof($demande->actions)){
				foreach($demande->actions as $action){
					demandes_actions::delete($action);
				}
			}
			// suppression des liens user - demande
			$req = "delete from demandes_users where num_demande='".$demande->id_demande."'";
			pmb_mysql_query($req,$dbh);
			//suppression des doc num
			$req = "delete ed,eda from explnum_doc ed 
			join explnum_doc_actions eda on ed.id_explnum_doc=eda.num_explnum_doc 
			join demandes_actions da on eda.num_action=da.id_action
			where da.num_demande=".$demande->id_demande;
			pmb_mysql_query($req, $dbh);
			// suppression des valeurs de CP
			$p_perso=new parametres_perso("demandes");
			$p_perso->delete_values($demande->id_demande);
			// suppression de la demande
			$req = "delete from demandes where id_demande='".$demande->id_demande."'"; 
			pmb_mysql_query($req,$dbh);
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
		global $dbh,$charset,$msg, $iduser;
		
		if($multiple)
			$mul = " name='iduser[]' multiple ";
		else $mul = " name='iduser' ";
		
		if(!$this->id_demande){
			$req="select TRIM(concat(prenom,' ',nom)) as name, userid, 0 as actif, username
			from users 
			where rights&16384 order by name";
		} else {
			$req="select TRIM(concat(prenom,' ',nom)) as name, userid , if(isnull(num_demande),0,if((users_statut),1,0)) as actif, username
			from users
			left join demandes_users on (num_user=userid and num_demande='".$this->id_demande."') 
			where rights&16384 order by name";
		}
		 
		$res = pmb_mysql_query($req,$dbh);
		$select = "";
		$selector = "<select  $mul $action >";
		if($default) $selector .= "<option value='0'>".htmlentities($msg['demandes_all_users'],ENT_QUOTES,$charset)."</option>";
		if($nonassign) $selector .=  "<option value='-1' ".($iduser == -1 ?'selected' :'').">".htmlentities($msg['demandes_not_assigned'],ENT_QUOTES,$charset)."</option>";
		while(($user=pmb_mysql_fetch_object($res))){			
			if($user->actif) $select="selected";
			$name = (trim($user->name) ? $user->name :$user->username);
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
		global $dbh;

		$req = "select concat(prenom,' ',nom) as nom, userid, username from users where userid='".$iduser."'";
		$res = pmb_mysql_query($req,$dbh);
		$user = pmb_mysql_fetch_object($res);
		
		return ( trim($user->nom) ? $user->nom : $user->username );		
	}
	
	/*
	 * Retourne les caractéristiques de l'emprunteur qui effectue la demande
	 */
	public function getCaracEmpr($idempr){
		global $dbh;

		$req = "select concat(empr_prenom,' ',empr_nom) as nom, id_empr,empr_cb from empr where id_empr='".$idempr."'";
		$res = pmb_mysql_query($req,$dbh);
		$empr = pmb_mysql_fetch_array($res);

		return $empr;		
	}
	
	
	/*
	 * Fonction qui retourne la requete de filtre
	 */
	public static function getQueryFilter($idetat,$iduser,$idempr,$user_input,$date_dmde,$date_but,$id_theme,$id_type,$dmde_loc){
		
		$date_deb = str_replace('-','',$date_dmde);
		$date_fin = str_replace('-','',$date_but);
		
		
		$params = array();
		
		//Filtre d'etat
		if($idetat){
			$etat = " etat_demande = '".$idetat."'";
			$params[] = $etat;
		}
		//Filtre d'utilisateur
		$join_filtre_user="";
		if($iduser){
			if($iduser == -1)
				$user = " nom is null ";
			else $user = " duf.num_user = '".(is_array($iduser) ? $iduser[0] : $iduser)."' and duf.users_statut=1";
			$join_filtre_user = "left join demandes_users duf on (duf.num_demande=d.id_demande )"; 
			$params[] = $user;
		}
		
		//Filtre de demandeur
		if($idempr){
			$empr = " num_demandeur = '".$idempr."'";	
			$params[] = $empr;
		}
		
		//Filtre de recherche
		if($user_input){
			$user_input = str_replace('*','%',$user_input);
			$saisie = " titre_demande like '%".$user_input."%'";
			$params[] = $saisie;
		}
		
		//Filtre date
		if($date_deb<$date_fin){
			$date = " (date_demande >= '".$date_dmde."' and deadline_demande <= '".$date_but."' )"; 
			$params[] = $date;		
		}
		//Filtre theme
		if($id_theme){
			$theme = " theme_demande = '".$id_theme."'";
			$params[] = $theme;
		}
		
		//Filtre type
		if($id_type){
			$type = " type_demande = '".$id_type."'";
			$params[] = $type;		
		}
		
		//Filtre localisation
		$join_loc="";
		if($dmde_loc){
			$join_loc = "left join empr on (num_demandeur=id_empr)";
			$loc =  " empr_location = '".$dmde_loc."'";
			$params[] = $loc;		
		}
		
		//Champs perso
		$join_cp="";
		$p_perso=new parametres_perso("demandes");
		$perso_=$p_perso->read_search_fields_from_form();
		if(isset($perso_["FIELDS"])) {
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if(is_array($p["VALUE"]) && count($p["VALUE"])) {
					$join_cp .= " join demandes_custom_values as d_c_v_".$i." on (d_c_v_".$i.".demandes_custom_origine=d.id_demande)";
					$join_cp .= " and d_c_v_".$i.".demandes_custom_".$p["DATATYPE"]." IN ('".implode("','", $p["VALUE"])."') ";
				}
			}
		}
		
		if($params) $clause = "where ".implode(" and ",$params);
		else $clause = "";
		$req = "select id_demande
				from demandes d 
				join demandes_type dy on d.type_demande=dy.id_type
				join demandes_theme dt on d.theme_demande=dt.id_theme				
				left join demandes_users du on du.num_demande=d.id_demande
				left join users on (du.num_user=userid and du.users_statut=1)
				$join_filtre_user
				$join_loc
				$join_cp
				$clause
				group by id_demande
				order by date_demande desc";
		return $req;
		
	}
	
	/*
	 * Affichage du formulaire de consultation d'une demande
	 */
	public function show_consult_form($last_modified=0){
		
		global $idetat,$iduser,$idempr,$user_input;
		global $date_debut,$date_fin, $id_type, $id_theme, $dmde_loc;
		global $form_consult_dmde, $charset, $msg, $dbh,$demandes_init_workflow, $form_consult_linked_record;
		global $pmb_type_audit, $reponse_finale;
		
		$form_consult_dmde = str_replace('!!form_title!!',htmlentities($this->titre_demande,ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!sujet_dmde!!',htmlentities($this->sujet_demande,ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!etat_dmde!!',htmlentities($this->workflow->getStateCommentById($this->etat_demande),ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!date_dmde!!',htmlentities(formatdate($this->date_demande),ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!date_butoir_dmde!!',htmlentities(formatdate($this->deadline_demande),ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!date_prevue_dmde!!',htmlentities(formatdate($this->date_prevue),ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!progression_dmde!!',htmlentities($this->progression.'%',ENT_QUOTES,$charset),$form_consult_dmde);
		
		$nom_user='';
		if(sizeof($this->users)){
			foreach($this->users as $id=>$user){
				if($user['statut']==1){
					if($nom_user){
						$nom_user.="/ ";
					}
					$nom_user.=$user['nom'];
				}
			}
		}
		
		$carac_empr = $this->getCaracEmpr($this->num_demandeur);
		$nom = $carac_empr['nom'];
		$cb = $carac_empr['empr_cb'];
		$nom_emprunteur ="";
		if(SESSrights & CIRCULATION_AUTH)
			$nom_emprunteur = "<a href=\"circ.php?categ=pret&form_cb=$cb\" >".htmlentities($nom,ENT_QUOTES,$charset)."</a>";
		
		$form_consult_dmde = str_replace('!!demandeur!!',($nom_emprunteur ? $nom_emprunteur :$nom),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!attribution!!',$nom_user,$form_consult_dmde);
		$form_consult_dmde = str_replace('!!iddemande!!',$this->id_demande,$form_consult_dmde);
		$form_consult_dmde = str_replace('!!theme_dmde!!',htmlentities($this->theme_libelle,ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!type_dmde!!',htmlentities($this->type_libelle,ENT_QUOTES,$charset),$form_consult_dmde);
		if ($this->num_linked_notice) {
			$display = new mono_display($this->num_linked_notice, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
			$form_consult_dmde = str_replace('!!form_linked_record!!',$form_consult_linked_record,$form_consult_dmde);
			$form_consult_dmde = str_replace('!!linked_record!!',htmlentities($display->result, ENT_QUOTES, $charset),$form_consult_dmde);
			$form_consult_dmde = str_replace('!!linked_record_id!!',htmlentities($this->num_linked_notice, ENT_QUOTES, $charset),$form_consult_dmde);
			$form_consult_dmde = str_replace('!!linked_record_link!!',htmlentities($url_base."catalog.php?categ=isbd&id=".$this->num_linked_notice, ENT_QUOTES, $charset),$form_consult_dmde);
		} else {
			$form_consult_dmde = str_replace('!!form_linked_record!!',"&nbsp;",$form_consult_dmde);
		}
		
		//Champs personalisés
		$perso_aff = "" ;
		$p_perso = new parametres_perso("demandes");
		if (!$p_perso->no_special_fields) {
			$perso_=$p_perso->show_fields($this->id_demande);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if ($p["AFF"] !== '') $perso_aff .="<br />".$p["TITRE"]." ".($p["TYPE"]=='html'?$p["AFF"]:nl2br($p["AFF"]));
			}
		}
		if ($perso_aff) {
			$form_consult_dmde = str_replace("!!champs_perso!!",$perso_aff,$form_consult_dmde);
		} else {
			$form_consult_dmde = str_replace("!!champs_perso!!","",$form_consult_dmde);
		}
		
		//afficher la liste des boutons de changement d'état
		if(($this->etat_demande && sizeof($this->users)) || $demandes_init_workflow!=="2"){
			$states = $this->workflow->getStateList($this->etat_demande);
			$states_btn = $this->getDisplayStateBtn($states);		
			$form_consult_dmde = str_replace('!!btn_etat!!',$states_btn,$form_consult_dmde);
		} else {
			$form_consult_dmde = str_replace('!!btn_etat!!',"",$form_consult_dmde);
		}
		
		//afficher la liste des boutons de la notice
		if($this->num_notice != 0){
			$notice = "<a onclick=\"show_notice('".$this->num_notice."')\" href='#'><img style='border:0px' class='align_top' src='".get_url_icon('search.gif')."' alt='".htmlentities($msg['demandes_see_notice'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_see_notice'],ENT_QUOTES,$charset)."' /></a>";
		} else {
			$notice = "";
		}
		$form_consult_dmde = str_replace('!!icone!!',$notice,$form_consult_dmde);
		
		if((sizeof($this->users)) || $demandes_init_workflow!=="2"){
			$req = "select count(1) as nb from demandes join demandes_actions on id_demande=num_demande join explnum_doc_actions on num_action=id_action where id_demande='".$this->id_demande."'";
			$res = pmb_mysql_query($req, $dbh);
			$docnum = pmb_mysql_fetch_object($res);
			// bouton doc num
			if($docnum->nb){
				$btn_attach = "&nbsp;<input type='submit' class='bouton' value='".$msg['demandes_attach_docnum']."' onClick='this.form.act.value=\"attach\" ; ' />";
			} else $btn_attach = "";
			// boutons notice
			if($this->num_notice != 0){
				$btn_notices = "<input type='submit' class='bouton' value='".$msg['demandes_complete_notice']."' onClick='this.form.act.value=\"notice\" ; ' />".
				$btn_attach."&nbsp;<input type='submit' class='bouton' value='".$msg['demandes_generate_rapport']."' onClick='this.form.act.value=\"rapport\" ; ' />";
				$btn_suppr_notice = "<input type='submit' class='bouton' value='".$msg['demandes_delete_notice']."' onClick='this.form.act.value=\"delete_notice\"  ; return confirm_delete(); ' />";
			} else {
				$btn_notices = "<input type='submit' class='bouton' value='".$msg['demandes_create_notice']."' onClick='this.form.act.value=\"create_notice\" ; ' />".
						$btn_attach."&nbsp;<input type='hidden' class='bouton' value='".$msg['demandes_generate_rapport']."' onClick='this.form.act.value=\"rapport\" ; ' />".
				$btn_attach."&nbsp;<input type='submit' class='bouton' value='".$msg['demandes_generate_rapport']."' onClick='this.form.act.value=\"rapport\" ; ' />";
				$btn_suppr_notice = "";
			}
			// bouton audit
			if($pmb_type_audit){
				$btn_audit = audit::get_dialog_button($this->id_demande, 14);
			} else {
				$btn_audit = "";
			}
			
			// affichage des boutons de création de la réponse finale
			$btn_repfinal = $btn_faq ="";
			if($this->etat_demande == 4){
				if(!$this->reponse_finale || $this->reponse_finale==''){
					$btn_repfinal = "&nbsp;<input type='submit' class='bouton' value='".$msg['demandes_repfinale_creation']."' onclick='this.form.act.value=\"final_response\" ; ' />&nbsp;";
				}				
			}			
			if($this->etat_demande == 4){
				if(!$this->num_faq_question){
					$btn_faq = "&nbsp;<input type='button' class='bouton' value='".$msg['demandes_creation_faq_question']."' onclick='document.location=\"./demandes.php?categ=faq&sub=question&action=new&num_demande=".$this->id_demande."\" ; ' />&nbsp;";
				}else{
					$btn_faq = "&nbsp;<input type='button' class='bouton' value='".$msg['demandes_edit_faq_question']."' onclick='document.location=\"./demandes.php?categ=faq&sub=question&action=edit&id=".$this->num_faq_question."\" ; ' />&nbsp;";
				}
			}
			
			$form_consult_dmde = str_replace('!!btns_notice!!',$btn_notices,$form_consult_dmde);
			$form_consult_dmde = str_replace('!!btn_suppr_notice!!',$btn_suppr_notice,$form_consult_dmde);
			$form_consult_dmde = str_replace('!!btn_audit!!',$btn_audit,$form_consult_dmde);
			$form_consult_dmde = str_replace('!!btn_repfinal!!',$btn_repfinal,$form_consult_dmde);
			$form_consult_dmde = str_replace('!!btn_faq!!',$btn_faq,$form_consult_dmde);
		} else {
			$form_consult_dmde = str_replace('!!btns_notice!!',"",$form_consult_dmde);
			$form_consult_dmde = str_replace('!!btn_suppr_notice!!',"",$form_consult_dmde);
			$form_consult_dmde = str_replace('!!btn_audit!!',"",$form_consult_dmde);
			$form_consult_dmde = str_replace('!!btn_repfinal!!',$btn_repfinal,$form_consult_dmde);
			$form_consult_dmde = str_replace('!!btn_faq!!',"",$form_consult_dmde);
		}
		
		//construction de l'url de retour
		$params_retour='';
		if($idetat){
			$params_retour.="&idetat=".$idetat;
		}
		if($iduser){
			$params_retour.="&iduser=".$iduser;
		}
		if($idempr){
			$params_retour.="&idempr=".$idempr;
		}
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
		
		if((sizeof($this->users)) || $demandes_init_workflow!=="2"){
			//Liste des actions
			$this->fetch_data($this->id_demande,false);
			
			if($this->etat_demande == 4 || $this->etat_demande == 5){
				$form_consult_dmde.=demandes_actions::show_list_actions($this->actions, $this->id_demande);
			}elseif($last_modified){
				$form_consult_dmde.=demandes_actions::show_list_actions($this->actions, $this->id_demande,$last_modified);
			}elseif($this->last_modified){
				$form_consult_dmde.=demandes_actions::show_list_actions($this->actions, $this->id_demande,$this->last_modified->id_action);
			}elseif($this->last_modified){
				$form_consult_dmde.=demandes_actions::show_list_actions($this->actions, $this->id_demande,$this->last_modified->id_action);
			}else{
				$form_consult_dmde.=demandes_actions::show_list_actions($this->actions, $this->id_demande);
			}
		}
		
		if($this->etat_demande == 4 && $this->reponse_finale != ''){
			$reponse_finale  = str_replace('!!repfinale!!',$this->reponse_finale,$reponse_finale);
			$reponse_finale  = str_replace('!!iddemande!!',htmlentities($this->id_demande,ENT_QUOTES,$charset),$reponse_finale);
			$act_form = "./demandes.php?categ=gestion";
			$reponse_finale  = str_replace('!!form_action!!',htmlentities($act_form,ENT_QUOTES,$charset),$reponse_finale);
			$form_consult_dmde.= $reponse_finale;
		} 
		
		if($this->etat_demande == 1 && !sizeof($this->actions) && $this->dmde_read_gestion == 1){
			demandes::demande_read($this->id_demande,true,"_gestion");
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
	public static function change_state($state,$demande){
		global $dbh,$demandes_init_workflow, $demandes_default_action, $pmb_type_audit;
		global $PMBuserid;
		global $sujet, $idtype, $idstatut;
		global $date_debut, $date_fin, $detail;
		global $time_elapsed, $progression,$cout,$iddemande, $ck_prive;
		
		if($demandes_init_workflow==="1" && $PMBuserid){
			//La demande est elle attribué à un utilisateur ?
			$query='SELECT 1 FROM demandes_users WHERE num_demande='.$demande->id_demande;
			$result=pmb_mysql_query($query,$dbh);
			if(!pmb_mysql_num_rows($result)){
				//si non, on attribue
				$query='INSERT INTO demandes_users SET num_user="'.$PMBuserid.'", num_demande="'.$demande->id_demande.'", date_creation="'.date("Y-m-d",time()).'", users_statut=1';
				pmb_mysql_query($query,$dbh);
			}
		}
		
		$req = "update demandes set etat_demande=$state where id_demande='".$demande->id_demande."'";
		pmb_mysql_query($req,$dbh);
		
		if($state == 2 && $demandes_default_action === "1"){
			$query = "SELECT id_action FROM demandes_actions WHERE num_demande=".$demande->id_demande;
			$result = pmb_mysql_query($query,$dbh);
			if(!pmb_mysql_num_rows($result)){
				
				$action_default = new demandes_actions();
				$action_default->num_demande = $demande->id_demande;
				$action_default->actions_num_user = $demande->num_demandeur;
				$action_default->actions_type_user =1;
				$action_default->date_action = date("Y-m-d",time());
				$action_default->deadline_action = date("Y-m-d",time());
				if($action_default->list_statut){
					$action_default->statut_action = "";				
					foreach($action_default->list_statut as $key=>$value){
						if($value['default']) {
							$action_default->statut_action = $value['id'];							
						} 
					}
					if($idstatut == "" && !$action_default->statut_action) {
						reset($action_default->list_statut);
						$first_statut = current($action_default->list_statut);
						$action_default->statut_action = $first_statut['id'];						
					}
				}
				$action_default->type_action = $demande->first_action;
				$action_default->sujet_action = addslashes($demande->titre_demande);
				$action_default->detail_action = addslashes($demande->sujet_demande);
				
				demandes_actions::save($action_default);				
			}
		}
		
		if($pmb_type_audit) audit::insert_modif(AUDIT_DEMANDE,$demande->id_demande);
	}
	
	/*
	 * Montre la liste des documents pouvant etre inclus dans le document
	 */
	public function show_docnum_to_attach(){
		
		global $dbh, $form_liste_docnum, $msg, $charset, $base_path, $pmb_indexation_docnum_default;
		
		$req="select id_explnum_doc as id, explnum_doc_nomfichier as nom, num_explnum, 
			concat(explnum_index_sew,'',explnum_index_wew) as indexer
			from explnum_doc 
			join explnum_doc_actions on (id_explnum_doc=num_explnum_doc and rapport=1)
			join demandes_actions on num_action=id_action
			left join explnum on explnum_id=num_explnum
			where num_demande='".$this->id_demande."'";
		$res = pmb_mysql_query($req,$dbh);
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
		
		global $dbh, $chk, $ck_index, $pmb_indexation_docnum;

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
			$res = pmb_mysql_query($req,$dbh);
			if(pmb_mysql_num_rows($res)){
				$expl = pmb_mysql_fetch_object($res);			
				$req = "insert into explnum(explnum_notice,explnum_nom,explnum_nomfichier,explnum_mimetype,explnum_data,explnum_extfichier) values 
					('".$this->num_notice."','".addslashes($expl->nom)."','".addslashes($expl->nom)."','".addslashes($expl->mime)."','".addslashes($expl->data)."','".addslashes($expl->ext)."')";
				pmb_mysql_query($req,$dbh);
				$id_explnum = pmb_mysql_insert_id();			
				$req = "update explnum_doc_actions set num_explnum='".$id_explnum."' where num_explnum_doc='".$chk[$i]."'";
				pmb_mysql_query($req,$dbh);
				if($ck_index[$i] && $pmb_indexation_docnum){
					$expl = new explnum($id_explnum);
					$expl->indexer_docnum();
				}
			}
		}	
			//On désattache les autres
			if($chk){
				$req = "select id_explnum_doc from explnum_doc where id_explnum_doc not in ('".implode('\',\'',$chk)."')"; 
				$res = pmb_mysql_query($req,$dbh);
				while(($expl = pmb_mysql_fetch_object($res))){
					$req = "delete e from explnum e 
					join explnum_doc_actions on num_explnum=explnum_id 
					where num_explnum_doc='".$expl->id_explnum_doc."'";
					pmb_mysql_query($req,$dbh);
					$req = "update explnum_doc_actions set num_explnum='0' where num_explnum_doc='".$expl->id_explnum_doc."'";
					pmb_mysql_query($req,$dbh);
				}
			} else {
				$req ="select id_explnum_doc
					from explnum_doc 
					join explnum_doc_actions on num_explnum_doc=id_explnum_doc
					join demandes_actions on num_action=id_action
					where num_explnum != 0
					and num_demande='".$this->id_demande."'";
				$res = pmb_mysql_query($req,$dbh);
				while(($expl = pmb_mysql_fetch_object($res))){
					$req = "delete e from explnum e 
					join explnum_doc_actions on num_explnum=explnum_id 
					where num_explnum_doc='".$expl->id_explnum_doc."'";
					pmb_mysql_query($req,$dbh);
					$req = "update explnum_doc_actions set num_explnum='0' where num_explnum_doc='".$expl->id_explnum_doc."'";
					pmb_mysql_query($req,$dbh);
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
		$myNotice->link_annul = "./demandes.php?categ=gestion&act=see_dmde&iddemande=".$this->id_demande;
		
		print $myNotice->show_form();
	}
	
	/*
	 * Formulaire de validation de la suppression de notice
	 */
	public function suppr_notice_form(){
		global $msg, $chk, $iddemande;
		
		
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
		global $chk, $iduser,$dbh;
		
		for($i=0;$i<count($chk);$i++){
			$req = "insert into demandes_users set num_user=$iduser, num_demande=$chk[$i], date_creation='".today()."', users_statut=1";
			pmb_mysql_query($req,$dbh);
		}
	}
	
	public function create_notice(){
		global $dbh, $iddemande;
		global $demandes_statut_notice, $pmb_type_audit;				
		
		// creation notice à partir de la demande
		$req = "insert into notices set
				tit1='".$this->titre_demande."',
				n_contenu='".$this->sujet_demande."',
				statut ='".$demandes_statut_notice."'
				";				
				pmb_mysql_query($req,$dbh);
				$id_notice = pmb_mysql_insert_id();
				notice::majNotices($id_notice);
				if($pmb_type_audit) audit::insert_creation(AUDIT_NOTICE,$id_notice);
		
		// mise à jour de la demande
		$req = "UPDATE demandes SET num_notice=".$id_notice." WHERE id_demande=".$this->id_demande;
		pmb_mysql_query($req,$dbh);	
		$this->num_notice=$id_notice;		
	}
	
	public function delete_notice(){
		global $dbh;
		global $demandes_statut_notice, $pmb_type_audit;
	
		notice::del_notice($this->num_notice);
		// mise à jour de la demande
		$req = "UPDATE demandes SET num_notice=0 WHERE id_demande=".$this->id_demande;
		pmb_mysql_query($req,$dbh);
		$this->num_notice=0;				
	}
	
	// mise à jour de l'alerte en fonction des alertes présentes sur les actions de la demande en cours
	public static function dmde_majRead($id_demande,$side="_gestion"){
		global $dbh;
		// on teste s'il y a des actions non lues
		$query = "SELECT id_action FROM demandes_actions WHERE num_demande=".$id_demande. " AND actions_read".$side."=1";
		$result = pmb_mysql_query($query,$dbh);		
		$value=0;
		if(pmb_mysql_num_rows($result)){
			$value=1;
		} else {
			// sinon, on teste si la demande est non lue et non validée en gestion et s'il n'y a aucune action de crée
			$query = "SELECT id_action FROM demandes_actions WHERE num_demande=".$id_demande;
			$res = pmb_mysql_query($query,$dbh);
			if(!pmb_mysql_num_rows($res)){
				$query = "SELECT dmde_read".$side.", etat_demande FROM demandes WHERE id_demande=".$id_demande;
				$res2 = pmb_mysql_query($query,$dbh);
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
		pmb_mysql_query($query2,$dbh);
		return $value;
	}
	
	// fonction qui renvoie un booléen indiquant si une demande a été lue ou pas
	public static function read($demande,$side="_gestion"){
		global $dbh;
		$read  = false;
		$query = "SELECT dmde_read".$side." FROM demandes WHERE id_demande=".$demande->id_demande;
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
	 * Change l'alerte de la demande : si elle est lue, elle passe en non lue et inversement
	*/
	public static function change_read($demande,$side="_gestion"){
		global $dbh;
	
		$read = demandes::read($demande,$side);
		$value = "";
		if($read){
			$value = 1;
		} else {
			$value = 0;
		}
		$query = "UPDATE demandes SET dmde_read".$side."=".$value." WHERE id_demande=".$demande->id_demande;
		if(pmb_mysql_query($query,$dbh)){
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * changement forcé de la mention "lue" ou "pas lue" de l'action
	* true => action est déjà lue donc pas d'alerte
	* false => alerte
	*/
	public static function demande_read($id_demande,$booleen=true,$side="_gestion"){
		global $dbh;
	
		$value = "";
		if($booleen){
			$value = 0;
		} else {
			$value = 1;
		}
		$query = "UPDATE demandes SET dmde_read".$side."=".$value." WHERE id_demande=".$id_demande;
		if(pmb_mysql_query($query,$dbh)){
			return true;
		} else {
			return false;
		}		
	}
	
	// mise à jour de l'alerte sur les actions et les notes de la demande
	public static function dmde_propageLu($id_demande,$side="_gestion"){
		global $dbh;
		
		if($id_demande){
			$dmde = new demandes($id_demande);
			$read = demandes::read($dmde,$side);
			if($read){				
				$query1 = "UPDATE demandes_actions SET actions_read".$side."=0 WHERE num_demande=".$id_demande;
				$result1 = pmb_mysql_query($query1,$dbh);
				
				$query2 = "SELECT id_action FROM demandes_actions WHERE num_demande=".$id_demande;
				$result2 = pmb_mysql_query($query2,$dbh);
				
				if(pmb_mysql_num_rows($result2)){
					while($action = pmb_mysql_fetch_object($result2)){
						$query3 = "UPDATE demandes_notes SET notes_read".$side."=0 WHERE num_action=".$action->id_action;
						pmb_mysql_query($query3,$dbh);
					}
				}
				return "lu";
			} else {
				return "nonlu";
			}			
		}
	}
	
	/*
	 * Formulaire de création d'une demande
	*/
	public function show_repfinale_form(){
		global $form_reponse_final, $msg, $charset, $dbh, $pmb_javascript_office_editor,$base_path;
		
		if($pmb_javascript_office_editor){
			print $pmb_javascript_office_editor;
			print "<script type='text/javascript' src='".$base_path."/javascript/tinyMCE_interface.js'></script>";
		}
		
		$form_reponse_final = str_replace('!!titre_dmde!!',htmlentities($this->titre_demande,ENT_QUOTES,$charset),$form_reponse_final);
		$form_reponse_final = str_replace('!!form_title!!',htmlentities($msg['demandes_reponse_finale'],ENT_QUOTES,$charset),$form_reponse_final);
		$form_reponse_final = str_replace('!!theme_dmde!!',htmlentities($this->theme_libelle,ENT_QUOTES,$charset),$form_reponse_final);
		$form_reponse_final = str_replace('!!type_dmde!!',htmlentities($this->type_libelle,ENT_QUOTES,$charset),$form_reponse_final);
		$form_reponse_final = str_replace('!!sujet_dmde!!',htmlentities($this->sujet_demande,ENT_QUOTES,$charset),$form_reponse_final);
		$form_reponse_final = str_replace('!!iddemande!!',htmlentities($this->id_demande,ENT_QUOTES,$charset),$form_reponse_final);
		
		$act_cancel = "document.location='./demandes.php?categ=gestion&act=see_dmde&iddemande=".$this->id_demande."'";
		$act_form = "./demandes.php?categ=gestion";
		
		if(!$this->reponse_finale){
			$form_reponse_final = str_replace('!!reponse!!','',$form_reponse_final);
			$form_reponse_final = str_replace('!!btn_suppr!!','',$form_reponse_final);
		} else {
			$form_reponse_final = str_replace('!!reponse!!',htmlentities($this->reponse_finale,ENT_QUOTES,$charset),$form_reponse_final);
			$btn_suppr = "<input type='submit' class='bouton' value='".$msg['demandes_repfinale_delete']."' onClick='this.form.act.value=\"suppr_repfinale\" ; return confirm_delete();' />";
			$form_reponse_final = str_replace('!!btn_suppr!!',$btn_suppr,$form_reponse_final);
		}
		$form_reponse_final = str_replace('!!form_action!!',$act_form,$form_reponse_final);
		$form_reponse_final = str_replace('!!cancel_action!!',$act_cancel,$form_reponse_final);
		print $form_reponse_final;
	}
	
	/*
	 * Formulaire de création d'une demande
	*/
	public function save_repfinale($id_note=0){
		global $dbh, $f_message;
		$f_message=strip_tags($f_message);
		if($this->id_demande){
			$query = "UPDATE demandes SET reponse_finale='".$f_message."', demande_note_num=$id_note WHERE id_demande=".$this->id_demande;
			pmb_mysql_query($query,$dbh);				
		}
	}
	
	/*
	 * Formulaire de création d'une demande
	*/
	public function suppr_repfinale(){
		global $dbh, $f_message;	
		
		if($this->id_demande){
			$query = "UPDATE demandes SET reponse_finale='' WHERE id_demande=".$this->id_demande;			
			pmb_mysql_query($query,$dbh);
		}
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
}
?>