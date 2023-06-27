<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: demandes.class.php,v 1.53 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
			date_prevue, theme_demande, type_demande, titre_demande, libelle_theme,libelle_type, allowed_actions, dmde_read_gestion, dmde_read_opac, num_linked_notice
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
				$this->num_linked_notice = $dmde->num_linked_notice;
				
				if(!$this->allowed_actions || !count($this->allowed_actions)){
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
				$this->num_linked_notice = 0;
				foreach($this->allowed_actions as $allowed_action){
					$allowed_action['active'] = 1;
					$allowed_actions[] = $allowed_action;
					if($allowed_action['default']){
						$this->first_action = $allowed_action['id'];
					}
				}
			}
			$req = "select num_user, concat(prenom,' ',nom) as nom, username, users_statut from demandes_users, users where num_user=userid and num_demande='".$this->id_demande."' and users_statut=1";
			$res = pmb_mysql_query($req,$dbh);
			$i=0;
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
			$this->num_user = '';
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
			$this->num_linked_notice = 0;
			foreach($this->allowed_actions as $allowed_action){
				$allowed_action['active'] = 1;
				$allowed_actions[] = $allowed_action;
				if($allowed_action['default']){
					$this->first_action = $allowed_action['id'];
				}
			}
		}
		if(!$this->workflow){
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
	
	public function getStateValue($current_value=0){
		foreach($this->liste_etat as $key=>$val){
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
	
	/*
	 * Formulaire de création d'une demande
	 */
	public function show_modif_form(){
		global $form_modif_demande, $msg, $charset, $id_empr, $form_linked_record, $opac_demandes_allow_from_record;
		
		$themes = new demandes_themes('demandes_theme','id_theme','libelle_theme',$this->theme_demande);
		$types = new demandes_types('demandes_type','id_type','libelle_type',$this->type_demande);
		
		if(!$this->id_demande){
			$form_modif_demande = str_replace('!!form_title!!',htmlentities($msg['demandes_creation'],ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!sujet!!','',$form_modif_demande);
			$form_modif_demande = str_replace('!!progression!!','',$form_modif_demande);
			$form_modif_demande = str_replace('!!empr_txt!!','',$form_modif_demande);
			$form_modif_demande = str_replace('!!idempr!!',$id_empr,$form_modif_demande);
			$form_modif_demande = str_replace('!!iduser!!',"",$form_modif_demande);
			$form_modif_demande = str_replace('!!titre!!','',$form_modif_demande);
			
			$etat=$this->getStateValue();
			$form_modif_demande = str_replace('!!idetat!!',$etat['id'],$form_modif_demande);
			$form_modif_demande = str_replace('!!value_etat!!',$etat['comment'],$form_modif_demande);
			$form_modif_demande = str_replace('!!select_theme!!',$themes->getListSelector(),$form_modif_demande);
			$form_modif_demande = str_replace('!!select_type!!',$types->getListSelector(),$form_modif_demande);
			
			$date = formatdate(today());
			$date_debut=date("Y-m-d",time());
			
			$form_modif_demande = str_replace('!!date_fin_btn!!',$date,$form_modif_demande);
			$form_modif_demande = str_replace('!!date_debut_btn!!',$date,$form_modif_demande);
			$form_modif_demande = str_replace('!!date_debut!!',$date_debut,$form_modif_demande);
			$form_modif_demande = str_replace('!!date_fin!!',$date_debut,$form_modif_demande);
			$form_modif_demande = str_replace('!!date_prevue!!',$date_debut,$form_modif_demande);
			$form_modif_demande = str_replace('!!date_prevue_btn!!',$date,$form_modif_demande);
			
			$form_modif_demande = str_replace('!!iddemande!!','',$form_modif_demande);

			$form_modif_demande = str_replace("!!form_linked_record!!", "", $form_modif_demande);
		} else {			
			$form_modif_demande = str_replace('!!form_title!!',htmlentities(sprintf($msg['demandes_modification'],' : '.$this->titre_demande),ENT_QUOTES,$charset),$form_modif_demande);
			
			$form_modif_demande = str_replace('!!titre!!',htmlentities($this->titre_demande,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!sujet!!',htmlentities($this->sujet_demande,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!progression!!',htmlentities($this->progression,ENT_QUOTES,$charset),$form_modif_demande);
			
			$etat=$this->getStateValue($this->etat_demande);
			$form_modif_demande = str_replace('!!idetat!!',$etat['id'],$form_modif_demande);
			$form_modif_demande = str_replace('!!value_etat!!',$etat['comment'],$form_modif_demande);
			
			$form_modif_demande = str_replace('!!idempr!!',$this->num_demandeur,$form_modif_demande);
			$form_modif_demande = str_replace('!!titre!!',$this->titre_demande,$form_modif_demande);
			
			$form_modif_demande = str_replace('!!iduser!!',$this->num_user,$form_modif_demande);
			$form_modif_demande = str_replace('!!select_theme!!',$themes->getListSelector($this->theme_demande),$form_modif_demande);
			$form_modif_demande = str_replace('!!select_type!!',$types->getListSelector($this->type_demande),$form_modif_demande);
			
			$form_modif_demande = str_replace('!!date_fin_btn!!',formatdate($this->deadline_demande),$form_modif_demande);
			$form_modif_demande = str_replace('!!date_debut!!',htmlentities($this->date_demande,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!date_fin!!',htmlentities($this->deadline_demande,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!date_prevue_btn!!',formatdate($this->date_prevue),$form_modif_demande);
			$form_modif_demande = str_replace('!!date_prevue!!',htmlentities($this->date_prevue,ENT_QUOTES,$charset),$form_modif_demande);
			$form_modif_demande = str_replace('!!iddemande!!',$this->id_demande,$form_modif_demande);
			
			if ($opac_demandes_allow_from_record && $this->num_linked_notice) {
				$record_datas = record_display::get_record_datas($this->num_linked_notice);
				$form_modif_demande = str_replace("!!form_linked_record!!", $form_linked_record, $form_modif_demande);
				$form_modif_demande = str_replace("!!linked_record!!", htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset), $form_modif_demande);
				$form_modif_demande = str_replace("!!linked_record_id!!", htmlentities($this->num_linked_notice, ENT_QUOTES, $charset), $form_modif_demande);
				$form_modif_demande = str_replace("!!linked_record_link!!", htmlentities($record_datas->get_permalink(), ENT_QUOTES, $charset), $form_modif_demande);
			} else {
				$form_modif_demande = str_replace("!!form_linked_record!!", "", $form_modif_demande);
			}
		}
		
		$act_cancel = "document.location='./empr.php?tab=request&lvl=list_dmde'";
		$act_form = "./empr.php?tab=request&lvl=list_dmde&sub=save_demande";
		
		$form_modif_demande = str_replace('!!form_action!!',$act_form,$form_modif_demande);
		$form_modif_demande = str_replace('!!cancel_action!!',$act_cancel,$form_modif_demande);
		print $form_modif_demande;
	}
	
	public static function is_notice_visible($demande){
		global $dbh;
		
		if($demande->num_notice) {
			//La notice est-elle visible ?
			$req = "select notice_visible_opac as visible, notice_visible_opac_abon as visu_abo from notice_statut join notices on id_notice_statut=statut where notice_id='".$demande->num_notice."'";
			$res_vis = pmb_mysql_query($req,$dbh);
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
		global $form_filtre_demande, $form_liste_demande,$base_path,$opac_url_base;
		global $dbh, $charset, $msg;
		global $idetat,$iduser,$id_empr,$user_input;
		global $date_debut,$date_fin, $id_type, $id_theme, $dmde_loc;
		global $opac_demandes_affichage_simplifie,$demandes_notice_auto_tpl;
		global $view;
		
		$etat_demande_validee = 0;
		$etat_demande_a_valider = 0;
		
		if(!$idetat){
			$entete = "<th class='empr_demandes_col_etat'>".$msg['demandes_etat']."</th>";
			$form_liste_demande = str_replace('!!entete_etat!!',$entete,$form_liste_demande);
		} else{
			$entete = "<th class='empr_demandes_col_etat'></th>";
			$form_liste_demande = str_replace('!!entete_etat!!',$entete,$form_liste_demande);
		}
		
		$onChange="onchange=\"submit();\"";
		$form_liste_demande = str_replace('!!select_etat!!',$this->getStateSelector($idetat,$onChange,true),$form_liste_demande);
		
		$header_champs_perso = "";
		$p_perso=new parametres_perso("demandes");
		reset($p_perso->t_fields);
		foreach ($p_perso->t_fields as $key => $val) {
			if($val["OPAC_SHOW"]) $header_champs_perso .= "<th class='empr_demandes_col_".$val["NAME"]."'>".htmlentities($val["TITRE"],ENT_QUOTES,$charset)."</th>";
		}
		
		//Formulaire de la liste
		$req = self::getQueryFilter($idetat,$iduser,$id_empr,$user_input,$date_debut,$date_fin, $id_theme, $id_type,$dmde_loc);
		$res = pmb_mysql_query($req,$dbh);
		$liste ="";
		$nb_news=0;
		if($nb_demande=pmb_mysql_num_rows($res)){
			$parity=1;			
		
			while(($dmde = pmb_mysql_fetch_object($res))){
				$nb_td=0;
				$dmde=new demandes($dmde->id_demande);
				
				if ($parity % 2) {
					$pair_impair = "even";
				} else {
					$pair_impair = "odd";
				}
				$parity += 1;
			
				$tr_javascript = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
				$action = "onclick=\"document.location='./empr.php?tab=request&lvl=list_dmde&sub=open_demande&iddemande=".$dmde->id_demande;
				//Ajout des éléments de retour vers la bonne liste
				if($idetat){
					$action.="&idetat=".$idetat;
				}
				if($iduser){
					$action.="&iduser=".$iduser;
				}
// 				if($id_empr){
// 					$action.="&id_empr=".$id_empr;
// 				}
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
				$dmde->dmde_read_opac = demandes::dmde_majRead($dmde->id_demande,"_opac");
				if($dmde->dmde_read_opac == 1){
					$style=" style='cursor: pointer; font-weight:bold'";
					$nb_news++;
					$demande_id_new=$dmde->id_demande;
				} else {
					$style=" style='cursor: pointer'";
				}
				if($demandes_notice_auto_tpl){
					if(self::is_notice_visible($dmde)){
						$link_noti = "<td class='empr_demandes_col_linked'><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$dmde->num_notice."' alt='".$msg['demandes_see_notice']."' title='".$msg['demandes_see_notice']."'><img src='".get_url_icon('mois.gif')."' /></a></td>";
						$nb_td++;
					} else{
						$link_noti = "<td class='empr_demandes_col_linked'></td>";
						$nb_td++;
					}
				} else {
					$link_noti = "";
				}
				$nom_user='';
				if($dmde->users){
					foreach($dmde->users as $id=>$user){
						if($user['statut']==1){
							if($nom_user){
								$nom_user.="/ ";
							}
							$nom_user.=$user['nom'];
						}
					}
				}
				
				$liste .= "<tr class='".$pair_impair."' ".$tr_javascript.$style.">";
				
				$liste .= "<td class='empr_demandes_col1'><img hspace=\"3\" border=\"0\" onclick=\"expand_action('action".$dmde->id_demande."','$dmde->id_demande', true); return false;\" title=\"\" id=\"action".$dmde->id_demande."Img\" class=\"img_plus\" src=\"".get_url_icon("plus.gif")."\"></td>";
				$nb_td++;
				$liste .= "<td class='empr_demandes_col2'>";
				$nb_td++;
				if($dmde->dmde_read_opac == 1){
					$liste .= "<img hspace=\"3\" border=\"0\" onclick=\"change_read('read".$dmde->id_demande."','$dmde->id_demande', true); return false;\" title=\"\" id=\"read".$dmde->id_demande."Img1\" class=\"img_plus\" src=\"".get_url_icon('notification_empty.png')."\" style='display:none'>
								<img hspace=\"3\" border=\"0\" onclick=\"change_read('read".$dmde->id_demande."','$dmde->id_demande', true); return false;\" title=\"\" id=\"read".$dmde->id_demande."Img2\" class=\"img_plus\" src=\"".get_url_icon('notification_new.png')."\">";
				} else {
					$liste .= "<img hspace=\"3\" border=\"0\" onclick=\"change_read('read".$dmde->id_demande."','$dmde->id_demande', true); return false;\" title=\"\" id=\"read".$dmde->id_demande."Img1\" class=\"img_plus\" src=\"".get_url_icon('notification_empty.png')."\" >
								<img hspace=\"3\" border=\"0\" onclick=\"change_read('read".$dmde->id_demande."','$dmde->id_demande', true); return false;\" title=\"\" id=\"read".$dmde->id_demande."Img2\" class=\"img_plus\" src=\"".get_url_icon('notification_new.png')."\" style='display:none'>";
				}
				$liste .= "</td>";
				if ($dmde->get_num_linked_notice()) {
					$record_datas = record_display::get_record_datas($dmde->get_num_linked_notice());
				}
				if(!$opac_demandes_affichage_simplifie) {
					$liste .="
					<td class='empr_demandes_col_titre' $action>".htmlentities($dmde->titre_demande,ENT_QUOTES,$charset)."</td>
					<td class='empr_demandes_col_etat' $action>".htmlentities($dmde->workflow->getStateCommentById($dmde->etat_demande),ENT_QUOTES,$charset)."</td>
					<td class='empr_demandes_col_date_dmde' $action>".htmlentities(formatdate($dmde->date_demande),ENT_QUOTES,$charset)."</td>
					<td class='empr_demandes_col_date_butoir' $action>".htmlentities(formatdate($dmde->deadline_demande),ENT_QUOTES,$charset)."</td>
					<td class='empr_demandes_col_user' $action>".htmlentities($nom_user,ENT_QUOTES,$charset)."</td>
					<td class='empr_demandes_col_progression'>
						<img src='".get_url_icon('jauge.png')."' height='15px' width=\"".$dmde->progression."%\" alt='".$dmde->progression."%' />
					</td>";
					$nb_td+=6;
					$perso_=$p_perso->show_fields($dmde->id_demande);
					if(isset($perso_["FIELDS"])) {
						for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
							$p=$perso_["FIELDS"][$i];
							if($p["OPAC_SHOW"]){
								$liste .= "<td class='empr_demandes_col_".$p["NAME"]."'>".($p["TYPE"]=='html'?$p["AFF"]:nl2br($p["AFF"]))."</td>";
								$nb_td++;
							}
						}
					}
					$nb_td++;
					$liste .="<td>";
					if ($dmde->get_num_linked_notice()) {
						$liste .= "
						<a href='".htmlentities($record_datas->get_permalink(), ENT_QUOTES, $charset)."' title='".htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset)."' >".htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset)."</a>";
					} else {
						$liste .= "&nbsp;";
					}
					$liste .= "
					</td>
					$link_noti
					";
				} else {
					$liste .="
					<td class='empr_demandes_col_titre' $action>".htmlentities($dmde->titre_demande,ENT_QUOTES,$charset)."</td>
					<td class='empr_demandes_col_etat' $action>".htmlentities($dmde->workflow->getStateCommentById($dmde->etat_demande),ENT_QUOTES,$charset)."</td>
					<td class='empr_demandes_col_date_dmde' $action>".htmlentities(formatdate($dmde->date_demande),ENT_QUOTES,$charset)."</td>";					
					$nb_td+=3;
					$perso_=$p_perso->show_fields($dmde->id_demande);
					for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
						$p=$perso_["FIELDS"][$i];
						if($p["OPAC_SHOW"]){
							$liste .= "<td class='empr_demandes_col_".$p["NAME"]."'>".($p["TYPE"]=='html'?$p["AFF"]:nl2br($p["AFF"]))."</td>";
							$nb_td++;
						}
					}
					$nb_td++;
					$liste .="<td>";
					if ($dmde->get_num_linked_notice()) {
						$liste .= "
						<a href='".htmlentities($record_datas->get_permalink(), ENT_QUOTES, $charset)."' title='".htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset)."' >".htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset)."</a>";
					} else {
						$liste .= "&nbsp;";
					}
					$liste .= "	
					</td>
					$link_noti
					";
				}
				
				$liste .= "</tr>";
				//Le détail de l'action, contient les notes
				$liste .="<tr id=\"action".$dmde->id_demande."Child\" style=\"display:none\">
					<td></td>
					<td colspan=\"".($nb_td-1)."\" id=\"action".$dmde->id_demande."ChildTd\"></td>
				</tr>";
				if($dmde->etat_demande==2){
					$etat_demande_validee++;
					$demande_id_validee=$dmde->id_demande;
				}
				if($dmde->etat_demande==1){
					$etat_demande_a_valider++;
					$demande_id_a_valider=$dmde->id_demande;
				}				
			}
		} else {
			$liste .= "<tr><td>".$msg['demandes_liste_vide']."</td></tr>";
		}	
		/* plus demandé...
		if($etat_demande_validee==1 && $view!="all" && !$etat_demande_a_valider){// une seule validée:
			print "<script type='text/javascript'>document.location=\"./empr.php?tab=request&lvl=list_dmde&sub=open_demande&iddemande=".$demande_id_validee."&#fin\";</script>";
			return;
		}
		if($etat_demande_a_valider==1 && $view!="all" && !$etat_demande_validee){// une seul à valider: 
			print "<script type='text/javascript'>document.location=\"./empr.php?tab=request&lvl=list_dmde&sub=open_demande&iddemande=".$demande_id_a_valider."&#fin\";</script>";
			return;
		}
		*/	
		$form_liste_demande = str_replace('!!header_champs_perso!!',$header_champs_perso,$form_liste_demande);
		$form_liste_demande = str_replace('!!liste_dmde!!',$liste,$form_liste_demande);
		
		print $form_liste_demande;
	}
	
	public static function get_values_from_form(&$demande){
		global $sujet,$iddemande ,$idetat, $titre, $id_theme, $id_type;
		global $date_debut, $date_fin, $date_prevue, $idempr, $linked_record_id;
		global $iduser, $progression, $demandes_statut_notice;
		global $demandes_init_workflow;
		
		if(!$date_prevue)$date_prevue=$date_debut;
		if(!$date_fin)$date_fin=$date_debut;
		
		$demande->id_demande=$iddemande;
		$demande->titre_demande = $titre;
		$demande->sujet_demande = $sujet;
		$demande->date_demande = $date_debut;
		$demande->date_prevue = $date_prevue;
		$demande->deadline_demande = $date_fin;
		$demande->num_user = '';
		$demande->progression = $progression;
		$demande->num_demandeur = $idempr;
		$demande->type_demande = $id_type;
		$demande->theme_demande = $id_theme;
		$demande->etat_demande=$idetat;
		if (!isset($linked_record_id)) $linked_record_id = 0;
		$demande->set_num_linked_notice($linked_record_id);
		
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
			
			//Indexation de la notice
			demandes_notices::majNoticesTotal($demande->num_notice);
					
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
		
		if($demande->id_demande && $demande->num_user){
			$query = "UPDATE demandes_users SET users_statut=0 WHERE num_user NOT IN (".implode(',',$demande->num_user).") AND num_demande='".$demande->id_demande."'";
			pmb_mysql_query($query,$dbh);
			$query = "UPDATE demandes_users SET users_statut=1 WHERE num_user IN (".implode(',',$demande->num_user).") AND num_demande='".$demande->id_demande."'";
			pmb_mysql_query($query,$dbh);
			foreach($demande->num_user as $id_user){
				$query = "insert into demandes_users set num_user='".$id_user."', num_demande='".$demande->id_demande."', date_creation='".$date_creation."', users_statut=1";
				pmb_mysql_query($query,$dbh);
			}
		}
	}
	
	/*
	 * Création/Modification d'une demande
	*/
	public static function save(&$demande){
		global $dbh, $pmb_type_audit, $demandes_email_demandes;
	
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
			dmde_read_gestion='1',
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
			dmde_read_gestion='1',
			num_linked_notice = '".$demande->get_num_linked_notice()."'" ;
			pmb_mysql_query($query,$dbh);
			
			$demande->id_demande = pmb_mysql_insert_id($dbh);
			if($pmb_type_audit) audit::insert_creation(AUDIT_DEMANDE,$demande->id_demande);
			
			if ($demandes_email_demandes){
				self::send_alert_by_mail($demande->num_demandeur,$demande);
			}
		}
		
		//MAJ des users de la notice
		self::save_demandes_users($demande);
	}
	
	public static function send_alert_by_mail($idsender,$note){
	
		global $msg, $empr_nom, $empr_prenom, $empr_mail, $dbh,$opac_url_base,$pmb_url_base, $demandes_email_generic;
	
		$contenu =  $msg['demandes_mail_new_demande'];	
		$contenu=str_replace("!!nom!!", $empr_prenom." ".$empr_nom." ", $contenu);
		$contenu=str_replace("!!titre_demande!!", $note->titre_demande, $contenu);
		
		$contenu.='<br />'.$note->sujet_demande.'<br />';
		
		$lien_gestion='<a href="'.$pmb_url_base.'demandes.php?categ=gestion&act=see_dmde&iddemande='.$note->id_demande.'">'.$msg['demandes_see_last_note'].'</a>';
		$objet = $msg['demandes_mail_new_demande_object'];
	
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1";
	
		//Envoi aux utilisateurs
		$query_users = "select nom, prenom, user_email from users where user_email like('%@%') and user_alert_demandesmail=1";
		$result_users = @pmb_mysql_query($query_users, $dbh);
		if ($result_users) {
			if (pmb_mysql_num_rows($result_users) > 0) {
				while ($user=@pmb_mysql_fetch_object($result_users)) {
					@mailpmb(trim($user->prenom." ".$user->nom), $user->user_email,$objet,$contenu.$lien_gestion,$empr_prenom." ".$empr_nom,$empr_mail,$headers,"",$param[2]);
				}
			}
		}
		
		// Envoi au mail générique
		if($demandes_email_generic){
			$param=explode(",", $demandes_email_generic);
			if(($param[0]==1 || $param[0]==3) && $param[1]){
				$envoi_OK = mailpmb("",$param[1],$objet,$contenu.$lien_gestion,$empr_prenom." ".$empr_nom,$empr_mail,$headers,"",$param[2]);
			}
		}
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
			$req="select concat(prenom,' ',nom) as nom, userid, username
			from users 
			where rights>=16384";
		} else {
			$req="select concat(prenom,' ',nom) as nom, userid , if(isnull(num_demande),0,if((users_statut),1,0)) as actif, username
			from users
			left join demandes_users on (num_user=userid and num_demande='".$this->id_demande."') 
			where rights>=16384";
		}
		 
		$res = pmb_mysql_query($req,$dbh);
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
		global $pmb_type_audit,$opac_demandes_no_action,$base_path, $opac_demandes_allow_from_record;
		
		$form_consult_dmde = str_replace('!!form_title!!',htmlentities($this->titre_demande,ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!sujet_dmde!!',htmlentities($this->sujet_demande,ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!etat_dmde!!',htmlentities($this->workflow->getStateCommentById($this->etat_demande),ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!date_dmde!!',htmlentities(formatdate($this->date_demande),ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!date_butoir_dmde!!',htmlentities(formatdate($this->deadline_demande),ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!date_prevue_dmde!!',htmlentities(formatdate($this->date_prevue),ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!progression_dmde!!',htmlentities($this->progression.'%',ENT_QUOTES,$charset),$form_consult_dmde);
		
		$users = '';
		for($i=0;$i<sizeof($this->users);$i++){
			if($i == sizeof($this->users)-1)
				$users .= htmlentities($this->users[$i]['nom'],ENT_QUOTES,$charset);
			else $users .= htmlentities($this->users[$i]['nom'],ENT_QUOTES,$charset)." / ";	
		}
	
		$carac_empr = $this->getCaracEmpr($this->num_demandeur);
		$nom = $carac_empr['nom'];
		$cb = $carac_empr['empr_cb'];
		
		$form_consult_dmde = str_replace('!!demandeur!!',$nom,$form_consult_dmde);
		$form_consult_dmde = str_replace('!!attribution!!',$users,$form_consult_dmde);
		$form_consult_dmde = str_replace('!!iddemande!!',$this->id_demande,$form_consult_dmde);
		$form_consult_dmde = str_replace('!!theme_dmde!!',htmlentities($this->theme_libelle,ENT_QUOTES,$charset),$form_consult_dmde);
		$form_consult_dmde = str_replace('!!type_dmde!!',htmlentities($this->type_libelle,ENT_QUOTES,$charset),$form_consult_dmde);
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
				if ($p["AFF"] !== '' && $p["OPAC_SHOW"]) $perso_aff .="<br />".$p["TITRE"]." ".($p["TYPE"]=='html'?$p["AFF"]:nl2br($p["AFF"]));
			}
		}
		if ($perso_aff) {
			$form_consult_dmde = str_replace("!!champs_perso!!",$perso_aff,$form_consult_dmde);
		} else {
			$form_consult_dmde = str_replace("!!champs_perso!!","",$form_consult_dmde);
		}
		
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
		
		if((sizeof($this->users) && $demandes_init_workflow!=="2")&& !$opac_demandes_no_action){
			
			$listActionButton='&nbsp;'.$msg['demandes_action_type_add'].'&nbsp;';
			if(sizeof($this->allowed_actions)){
				foreach($this->allowed_actions as $key=>$actionType){
					if($actionType['active']==1){
						$listActionButton.="<input type='button' class='bouton' value='".htmlentities($actionType['comment'],ENT_QUOTES, $charset)."' onClick=\"document.location='./empr.php?tab=request&lvl=list_dmde&sub=add_action&type_action=".$actionType['id']."&iddemande=$this->id_demande'\" />";
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
		
		if(sizeof($this->users) && $demandes_init_workflow!=="2"){
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
		
		if($this->etat_demande == 1 && !sizeof($this->actions) && $this->dmde_read_opac == 1){
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
				$action_default->type_action = $demande->first_action;
				$action_default->sujet_action = $demande->titre_demande;
				$action_default->detail_action = $demande->sujet_demande;
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
						<a href=\"$base_path/explnum_doc.php?explnumdoc_id=".$doc->id."'\" target=\"_blank\"><img src='".get_url_icon("globe_orange.png")."' /></a>
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
	public static function dmde_majRead($id_demande,$side="_opac"){
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
	public static function read($demande,$side="_opac"){
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
	public static function change_read($demande,$side="_opac"){
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
	
	public static function get_first_tab(){
		global $id_empr,$dbh;
	
		$query="select id_demande from demandes where num_demandeur='".$id_empr."'";
		$result = pmb_mysql_query($query,$dbh);
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