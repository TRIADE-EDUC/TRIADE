<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_lecture.class.php,v 1.65 2019-06-04 13:55:22 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/listes_lecture.class.php");
require_once ($class_path."/searcher.class.php");
require_once ($include_path."/templates/liste_lecture.tpl.php");
require_once ($include_path."/mail.inc.php") ;

class liste_lecture {
	
	public $id_liste;
	public $num_empr;
	public $login;
	public $display='';
	public $notices=array();
	public $notices_create_date = array();
	public $action='';
	public $nom_liste='';
	public $description='';
	public $public=0;
	public $num_owner=0;
	public $readonly=0;
	public $confidential=0;
	public $tag = '';
	public $empr = array();
	public $filtered_notices=array();
	public $subscribed = 0;
	
	/**
	 * Constructeur 
	 */
	public function __construct($id_liste=0, $act=''){
		$this->login = $_SESSION['user_code'];
		$this->num_empr = $this->get_num_empr($this->login);
		$this->id_liste = intval($id_liste);
		$this->action = $act;
		$this->fetch_data();
		$this->proceed();
	}
	
	protected function fetch_data() {
	    $this->nom_liste = '';
	    $this->description='';
	    $this->public=0;
	    $this->num_owner = 0;
	    $this->readonly=0;
	    $this->notices = array();
	    $this->notices_create_date = array();
	    $this->confidential=0;
	    $this->tag='';
	    $this->subscribed = 0;
	    if ($this->id_liste) {
	        $req = "select opac_liste_lecture.*, if(abo_liste_lecture.num_empr is null,0,1) as subscribed from opac_liste_lecture
				join empr on opac_liste_lecture.num_empr=empr.id_empr
				left join abo_liste_lecture on (abo_liste_lecture.num_liste=opac_liste_lecture.id_liste and abo_liste_lecture.num_empr='".$this->num_empr."') where id_liste='".$this->id_liste."'";
	        $res = pmb_mysql_query($req);
	        if(pmb_mysql_num_rows($res)){
	            $liste = pmb_mysql_fetch_object($res);
	            $this->nom_liste = $liste->nom_liste;
	            $this->description=$liste->description;
	            $this->public=$liste->public;
	            $this->num_owner = $liste->num_empr;
	            $this->readonly=$liste->read_only;
	            $this->confidential=$liste->confidential;
	            $this->tag=$liste->tag;
	            $this->subscribed = $liste->subscribed;
	            
	            $this->notices = array();
	            $this->notices_create_date = array();
	            $query = "select * from opac_liste_lecture_notices where opac_liste_lecture_num=" . $this->id_liste;
	            $result = pmb_mysql_query($query);
	            if (pmb_mysql_num_rows($result)) {
	                while ($row = pmb_mysql_fetch_object($result)) {
	                    $this->notices[] = $row->opac_liste_lecture_notice_num;
	                    $this->notices_create_date[$row->opac_liste_lecture_notice_num] = $row->opac_liste_lecture_create_date;
	                }
	            }
	        } 
	    } 
	}
	
	protected function proceed(){
		
		switch($this->action){
			case 'get_acces':
				$this->obtenir_acces();
				break;
			case 'suppr_acces':
				$this->supprimer_acces();
				break;
			case 'suppr_list':
				$this->supprimer_liste();
				break;
			case 'suppr_ck':
				$this->supprimer_coche();
				break;	
			case 'share_list':
				$this->share_liste();
				break;
			case 'unshare_list':
				$this->unshare_liste();
				break;
			case 'add_list':
			    $this->add_list();
			    break;
			case 'save':
				$this->enregistrer();
				break;
			case 'suppr':
				$this->supprimer_liste();
				break;
			case 'list_in':
				$this->remplir_liste();
				break;
			case 'list_out':
				$this->extraire_vers_panier();
				break;	
			case 'accept_acces':
				$this->accepter_acces_confidentiel();
				break;
			case 'refus_acces':
				$this->refuser_acces_confidentiel();
				break;
			case 'fetch_empr':
				$this->fetch_empr();
				break;
			default:
				$this->fetch_empr();
				break;	
		}
	}
	
	/**
	 * Obtenir l'accès à une liste partagée
	 */
	protected function obtenir_acces(){
		global $list_ck;
		
		if($list_ck){
			for($i=0;$i<sizeof($list_ck);$i++){
				$rqt = "insert into abo_liste_lecture (num_empr,num_liste, etat) values ('".$this->num_empr."', '".$list_ck[$i]."','2')";
				@pmb_mysql_query($rqt);
			}
		} elseif($this->id_liste){
			$rqt = "insert into abo_liste_lecture (num_empr,num_liste, etat) values ('".$this->num_empr."', '".$this->id_liste."','2')";
			@pmb_mysql_query($rqt);
		}
	}
	
	/**
	 * Supprime l'accès à une liste partagée
	 */
	protected function supprimer_acces(){
		global $list_ck;
		
		if($list_ck){
			for($i=0;$i<sizeof($list_ck);$i++){
				$rqt = "delete from abo_liste_lecture where num_empr='".$this->num_empr."' and num_liste='".$list_ck[$i]."'";
				pmb_mysql_query($rqt);
			}
		} elseif($this->id_liste){
			$rqt = "delete from abo_liste_lecture where num_empr='".$this->num_empr."' and num_liste='".$this->id_liste."'";
			pmb_mysql_query($rqt);
		}
	}
	
	/**
	 * Accepte l'accès aux listes confidentielles
	 */
	protected function accepter_acces_confidentiel(){		
		global $cb_demande,$opac_connexion_phrase ,$opac_url_base, $msg;
		
		for($i=0;$i<sizeof($cb_demande);$i++){
			$info = explode('-',$cb_demande[$i]);
			$req = " update abo_liste_lecture set etat=2 where num_empr='".$info[1]."' and num_liste='".$info[0]."'";
			pmb_mysql_query($req);
			
			$req ="select concat(empr_prenom,' ',empr_nom) as nom, empr_mail, empr_login from empr where id_empr='".$info[1]."'";
			$res = pmb_mysql_query($req);
			$destinataire = pmb_mysql_fetch_object($res);
			$req ="select concat(empr_prenom,' ',empr_nom) as nom, empr_mail, nom_liste from empr e, opac_liste_lecture oll where oll.num_empr=e.id_empr and id_liste='".$info[0]."'";
			$res = pmb_mysql_query($req);
			$sender= pmb_mysql_fetch_object($res);
			
			$date = time();
			$login = $destinataire->empr_login;
			$code=md5($opac_connexion_phrase.$login.$date);			
			$corps = sprintf($msg['list_lecture_intro_mail'],$destinataire->nom,$sender->nom_liste).", <br />".sprintf($msg['list_lecture_confirm_mail'],$sender->nom,$sender->nom_liste);
			$corps .= "<br /><br /><a href='".$opac_url_base."empr.php?code=$code&emprlogin=$login&date_conex=$date&tab=lecture&lvl=private_list&sub=shared_list' >".sprintf($msg['list_lecture_confirm_redir_mail'],$sender->nom_liste)."</a>";
			
			mailpmb($destinataire->nom,$destinataire->empr_mail,sprintf($msg['list_lecture_objet_confirm_mail'],$sender->nom_liste),stripslashes($corps),$sender->nom,$sender->empr_mail);
			
		}
	}
	
	/**
	 * Refuse l'accès aux listes confidentielles
	 */
	protected function refuser_acces_confidentiel(){
		global $cb_demande, $msg, $com,$opac_url_base,$opac_connexion_phrase;
		
		for($i=0;$i<sizeof($cb_demande);$i++){
			$info = explode('-',$cb_demande[$i]);
			$req = " update abo_liste_lecture set etat=0 where num_empr='".$info[1]."' and num_liste='".$info[0]."'";
			pmb_mysql_query($req);
			$req ="select concat(empr_prenom,' ',empr_nom) as nom, empr_mail, empr_login from empr where id_empr='".$info[1]."'";
			$res = pmb_mysql_query($req);
			$destinataire = pmb_mysql_fetch_object($res);
			$req ="select concat(empr_prenom,' ',empr_nom) as nom, empr_mail, nom_liste from empr e, opac_liste_lecture oll where oll.num_empr=e.id_empr and id_liste='".$info[0]."'";
			$res = pmb_mysql_query($req);
			$sender= pmb_mysql_fetch_object($res);
			
			$date = time();
			$login = $destinataire->empr_login;
			$code=md5($opac_connexion_phrase.$login.$date);			
			$corps = sprintf($msg['list_lecture_intro_mail'],$destinataire->nom,$sender->nom_liste).", <br />".sprintf($msg['list_lecture_refus_corps_mail'],$sender->nom,$sender->nom_liste);
			if($com) $corps .= sprintf("<br />".$msg['list_lecture_corps_com_mail'],$sender->nom," <br />".$com);
			$corps .= "<br /><br /><a href='".$opac_url_base."empr.php?code=$code&emprlogin=$login&date_conex=$date&tab=lecture&lvl=private_list' >".$msg['redirection_mail_link']."</a>";
			
			mailpmb($destinataire->nom,$destinataire->empr_mail,sprintf($msg['list_lecture_refus_mail'],$sender->nom_liste),stripslashes($corps),$sender->nom,$sender->empr_mail);
		}
	}
	
	/**
	 * Supprime la ou les listes sélectionnée(s)
	 */
	protected function supprimer_liste(){
		global $list_ck;
		
		if($list_ck){
			for($i=0;$i<sizeof($list_ck);$i++){
				$rqt = "delete from opac_liste_lecture where id_liste='".$list_ck[$i]."'";
				pmb_mysql_query($rqt);
				$rqt = "delete from abo_liste_lecture where num_liste='".$list_ck[$i]."'";
				pmb_mysql_query($rqt);
				$query = "delete from opac_liste_lecture_notices where opac_liste_lecture_num=" . $list_ck[$i];
				pmb_mysql_query($query);
			}
		} elseif($this->id_liste) {
			$rqt = "delete from opac_liste_lecture where id_liste='".$this->id_liste."'";
			pmb_mysql_query($rqt);
			$rqt = "delete from abo_liste_lecture where num_liste='".$this->id_liste."'";
			pmb_mysql_query($rqt);
			$query = "delete from opac_liste_lecture_notices where opac_liste_lecture_num=" . $this->id_liste;
			pmb_mysql_query($query);
			$this->id_liste = 0;
			$this->fetch_data();
		}
	}
	
	/**
	 * Supprime les notices cochées de la liste
	 */
	protected function supprimer_coche(){
		global $notice;
				
		$query = "DELETE FROM opac_liste_lecture_notices WHERE opac_liste_lecture_num=" . $this->id_liste . " 
            AND opac_liste_lecture_notice_num IN(" . implode(',', $notice) . ")";
		pmb_mysql_query($query);
        $this->fetch_data();
	}
	
	/**
	 * Partager la ou les listes sélectionnée(s)
	 */
	protected function share_liste(){
		global $list_ck;
		
		for($i=0;$i<sizeof($list_ck);$i++){
			$rqt = "update opac_liste_lecture set public=1 where num_empr='".$this->num_empr."' and id_liste='".$list_ck[$i]."' ";
			pmb_mysql_query($rqt);
		}
	}
	
	/**
	 * Ne plus partager la ou les listes sélectionnée(s)
	 */
	protected function unshare_liste(){
		global $list_ck;
		
		for($i=0;$i<sizeof($list_ck);$i++){
			$rqt = "update opac_liste_lecture set public=0 where num_empr='".$this->num_empr."' and id_liste='".$list_ck[$i]."'";
			pmb_mysql_query($rqt);
		}
	}
	
		
	/**
	 * récupération de l'id selon le login
	 */
	function get_num_empr($login){
		if($login){
			$rqt = "select id_empr from empr where empr_login='".addslashes($login)."'";
			$res = pmb_mysql_query($rqt);
			return pmb_mysql_result($res,0,0);
		}
		
		return 0;		
	}
	
	/**
	 * Enregistre une liste de lecture 
	 */
	function enregistrer(){
		global $list_name, $list_comment, $notice_filtre, $cb_share, $cb_readonly, $cb_confidential, $list_tag;
		
		$list_name = strip_tags($list_name);
		$list_comment = strip_tags($list_comment);
		$list_tag = strip_tags($list_tag);
		if(!$this->id_liste){
			$rqt="insert into opac_liste_lecture (description, public, num_empr, nom_liste, read_only, confidential, tag) 
				values ('".$list_comment."','".($cb_share ? 1 : 0)."', '".$this->num_empr."', '".$list_name."', '".($cb_readonly ? 1 : 0)."', '".($cb_confidential ? 1 : 0)."', '".$list_tag."')";
			pmb_mysql_query($rqt);
			$this->id_liste = pmb_mysql_insert_id();
		} elseif($this->id_liste) {
			$rqt="update opac_liste_lecture set description='".$list_comment."', public='".($cb_share ? 1 : 0)."', 
				nom_liste='".$list_name."', read_only='".($cb_readonly ? 1 : 0)."', confidential='".($cb_confidential ? 1 : 0)."', tag='".$list_tag."' where id_liste='".$this->id_liste."'";
			pmb_mysql_query($rqt);
		}
		$notices_associees = explode(",", $notice_filtre);
		foreach ($notices_associees as $notice_id) {
		    if ($notice_id) {
		        $query = "INSERT INTO opac_liste_lecture_notices SET opac_liste_lecture_num=". $this->id_liste . ",opac_liste_lecture_notice_num=" . $notice_id;
		        pmb_mysql_query($query);
		    }
		}
		$this->fetch_data();
	}
	
	/**
	 * Remplir la liste de lecture avec le panier
	 */
	protected function remplir_liste(){
				
		$notices = $this->notices;
		$cart = array();		
		for($i=0;$i<sizeof($_SESSION['cart']);$i++){
			if(array_search($_SESSION['cart'][$i],$notices) === false)
				$cart[] = $_SESSION['cart'][$i];
		}
		
		$notice_liste = array_merge($notices,$cart);
		
		foreach ($notice_liste as $notice_id) {
		    $query = "INSERT INTO opac_liste_lecture_notices SET opac_liste_lecture_num=". $this->id_liste . ",opac_liste_lecture_notice_num=" . $notice_id;
		    pmb_mysql_query($query);
		}
		$this->notices = $notice_liste;
	}
	
	/**
	 * Ajouter une notice à la liste de lecture
	 * @param integer $id_notice
	 */
	public function add_notice($id_notice) {
		$query = "select id_liste from opac_liste_lecture 
			where (id_liste = '".$this->id_liste."' and num_empr = '".$_SESSION['id_empr_session']."')
				or (id_liste in (select num_liste from abo_liste_lecture where num_liste = '".$this->id_liste."' and num_empr = '".$_SESSION['id_empr_session']."' and etat=2) and confidential = 0 and read_only = 0)";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			if(!in_array($id_notice, $this->notices)) {
				$this->notices[] = $id_notice;
				$query = "INSERT INTO opac_liste_lecture_notices SET opac_liste_lecture_num=". $this->id_liste . ",opac_liste_lecture_notice_num=" . $id_notice;
				pmb_mysql_query($query);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Extraire la liste dans le panier
	 */
	function extraire_vers_panier(){
		$cart = array();		
		$notices = $this->notices;
		for($i=0;$i<sizeof($notices);$i++){
			if(array_search($notices[$i],$_SESSION['cart']) === false)
				$cart[] = $notices[$i];
		}
		
		$notice_liste = array_merge($_SESSION['cart'],$cart);
		
		$_SESSION['cart'] = $notice_liste;
	}
	
	
	/****************************************************
	 * 													*
	 *			  Fonctions d'affichage		 			* 			
	 * 													*		
	 ****************************************************/
	
	/**
	 * Génère le formulaire pour les listes de l'utilisateur 
	 */
	public function generate_mylist(){
		
		$listes_lecture = new listes_lecture('my_reading_lists');
		$this->display = $listes_lecture->get_display_list();
	}
	
	/**
	 * Génère le formulaire pour les listes partagées 
	 */
	public function generate_sharedlist(){
		
		$listes_lecture = new listes_lecture('shared_reading_lists');
		$this->display = $listes_lecture->get_display_list();
	}
	
	/**
	 * Génère le formulaire pour les listes de l'utilisateur et les listes partagées
	 */
	public function generate_privatelist(){
		$listes_lecture = new listes_lecture('private_reading_lists');
		$this->display = $listes_lecture->get_display_list();
	}
	
	/**
	 * Génère le formulaire pour les listes publiques 
	 */
	public function generate_publiclist(){
		
		$listes_lecture = new listes_lecture('public_reading_lists');
		$this->display = $listes_lecture->get_display_list();
	}
	
	/*
	 * Fonction qui génère la liste des demandes
	 */
	public function generate_demandes(){
		global $dbh,$msg, $liste_demande, $emprlogin;
		
		$req = "select id_liste,nom_liste, id_empr, empr_nom, empr_prenom 
		from opac_liste_lecture oll, abo_liste_lecture abo, empr 
		where oll.id_liste=abo.num_liste 
		and abo.num_empr=id_empr
		and oll.num_empr='".($this->num_empr ? $this->num_empr : $this->get_num_empr($emprlogin))."'
		and oll.confidential=1
		and etat=1
		order by nom_liste";
		$res=pmb_mysql_query($req,$dbh);
		if(!pmb_mysql_num_rows($res)){		
			$affichage_liste = "<div class='row'><label>".$msg['list_lecture_no_demande']."</label></div>";	
			$liste_demande =  str_replace("!!accepter_btn!!",'',$liste_demande);
			$liste_demande =  str_replace("!!refuser_btn!!",'',$liste_demande);
			$liste_demande =  str_replace("!!demande_list!!",$affichage_liste,$liste_demande);
			$this->display = $liste_demande;
			return;
		} 
		
		$noms_listes = array();
		$aff_liste = "<script src='./includes/javascript/liste_lecture.js' type='text/javascript'></script>
				<script src='./includes/javascript/http_request.js' type='text/javascript'></script>";
		$aff_liste .= "<ul>";
		while(($liste = pmb_mysql_fetch_object($res))){			
			if(!isset($noms_listes[$liste->nom_liste])) {
				$aff_liste .= "<li><u>".$liste->nom_liste."</u></li>";
				$noms_listes[$liste->nom_liste] = $liste->nom_liste;
			}
			$aff_liste .= "<blockquote><div class='row'><input type='checkbox' name='cb_demande[]' value=\"".$liste->id_liste."-".$liste->id_empr."\"><label>".$liste->empr_prenom.' '.$liste->empr_nom."</label></div></blockquote>";
		}		
		$aff_liste .= "</ul>";
		$accept_btn = "<input type='submit' class='bouton' id='accept' name='accept' value=\"$msg[list_lecture_accept_demande]\" onclick='this.form.lvl.value=\"demande_list\"; this.form.act.value=\"accept_acces\";'/>";
		$refus_btn = "<input type='button' class='bouton' id='refus' name='refus' value=\"$msg[list_lecture_refus_demande]\"  onclick='make_refus_form(); '/>";
		$liste_demande =  str_replace("!!accepter_btn!!",$accept_btn,$liste_demande);
		$liste_demande =  str_replace("!!refuser_btn!!",$refus_btn,$liste_demande);
		$liste_demande =  str_replace("!!demande_list!!",$aff_liste,$liste_demande);
	
		
		$this->display = $liste_demande;	
		
	}
	public function add_list() {	    
	    global $liste_gestion, $charset, $msg, $opac_shared_lists_readonly;
	  
	    
	    $liste_gestion = str_replace('!!liste_lecture_gestion_boutons!!','',$liste_gestion);
	    $liste_gestion = str_replace('!!titre_liste!!',htmlentities($msg['list_lecture_create'],ENT_QUOTES,$charset),$liste_gestion);
	    $liste_gestion = str_replace('!!notice_filtre!!', '', $liste_gestion);
	    $liste_gestion = str_replace('!!name_list!!','',$liste_gestion);
	    $liste_gestion = str_replace('!!list_comment!!','',$liste_gestion);
	    if($opac_shared_lists_readonly)
	        $liste_gestion = str_replace('!!checked_only!!','checked',$liste_gestion);
        else 
            $liste_gestion = str_replace('!!checked_only!!','',$liste_gestion);
        $liste_gestion = str_replace('!!disabled_conf!!','disabled',$liste_gestion);
        $liste_gestion = str_replace('!!checked_conf!!','',$liste_gestion);
        $liste_gestion = str_replace('!!color_conf!!','gray',$liste_gestion);
        $liste_gestion = str_replace('!!checked!!','',$liste_gestion);
        $liste_gestion = str_replace('!!id_liste!!','',$liste_gestion);
        $liste_gestion = str_replace('!!liste_btn!!','',$liste_gestion);
        $liste_gestion = str_replace('!!print_btn!!','',$liste_gestion);
        $liste_gestion = str_replace('!!list_tag!!',$this->gen_selector_tags(),$liste_gestion);
        $liste_gestion = str_replace('!!add_empr!!','',$liste_gestion);
        $liste_gestion = str_replace('!!inscrit_list!!','',$liste_gestion);
        $liste_gestion = str_replace('!!search!!', '', $liste_gestion);
        
        $liste_gestion = str_replace('!!liste_notice!!', '', $liste_gestion);        
	    print $liste_gestion;
	}
	/**
	 * Génère le formulaire de gestion d'une liste 
	 */
	public function affichage_saveform($notice_asso=array()){
		
		global $liste_gestion, $liste_lecture_gestion_boutons, $dbh, $charset, $msg, $opac_search_results_per_page, $cart_aff_case_traitement, $page, $opac_shared_lists_readonly, $opac_show_suggest,$opac_allow_multiple_sugg;
		global $opac_shared_lists_add_empr;
		global $pmb_nb_max_tri;
		
		$affich='';
		
		if(!$this->id_liste){
			for($i=0;$i<sizeof($notice_asso);$i++){
				if (substr($notice_asso[$i],0,2)!="es") 
						$affich.= aff_notice($notice_asso[$i],1); 
				else $affich.=aff_notice_unimarc(substr($notice_asso[$i],2),1);
			}
			$liste_gestion = str_replace('!!liste_lecture_gestion_boutons!!','',$liste_gestion);
			$liste_gestion = str_replace('!!titre_liste!!',htmlentities($msg['list_lecture_create'],ENT_QUOTES,$charset),$liste_gestion);
			$liste_gestion = str_replace('!!notice_filtre!!',htmlentities(implode(',',$notice_asso),ENT_QUOTES,$charset),$liste_gestion);
			$liste_gestion = str_replace('!!name_list!!','',$liste_gestion);
			$liste_gestion = str_replace('!!list_comment!!','',$liste_gestion);
			if($opac_shared_lists_readonly)
				$liste_gestion = str_replace('!!checked_only!!','checked',$liste_gestion);			
			else $liste_gestion = str_replace('!!checked_only!!','',$liste_gestion);
			$liste_gestion = str_replace('!!disabled_conf!!','disabled',$liste_gestion);
			$liste_gestion = str_replace('!!checked_conf!!','',$liste_gestion);
			$liste_gestion = str_replace('!!color_conf!!','gray',$liste_gestion);
			$liste_gestion = str_replace('!!checked!!','',$liste_gestion);
			$liste_gestion = str_replace('!!id_liste!!','',$liste_gestion);		
			$liste_gestion = str_replace('!!liste_btn!!','',$liste_gestion);		
			$liste_gestion = str_replace('!!print_btn!!','',$liste_gestion);
			$liste_gestion = str_replace('!!list_tag!!',$this->gen_selector_tags(),$liste_gestion);
			$liste_gestion = str_replace('!!add_empr!!','',$liste_gestion);
			$liste_gestion = str_replace('!!inscrit_list!!','',$liste_gestion);	
			$liste_gestion = str_replace('!!search!!', '', $liste_gestion);
		} else {
			$print_btn="<input type='button' class='bouton' name='mail' 
				onclick=\"w=window.open('print.php?lvl=list&id_liste=$this->id_liste','print_window','width=500, height=750,scrollbars=yes,resizable=1'); w.focus();\" value='".$msg['list_lecture_mail']."' />";

			$liste_gestion = str_replace('!!liste_lecture_gestion_boutons!!',$liste_lecture_gestion_boutons,$liste_gestion);
			$liste_gestion = str_replace('!!titre_liste!!',htmlentities($msg['list_lecture_modify'],ENT_QUOTES,$charset),$liste_gestion);
			$liste_gestion = str_replace('!!name_list!!',htmlentities($this->nom_liste,ENT_QUOTES,$charset),$liste_gestion);
			$liste_gestion = str_replace('!!list_comment!!',htmlentities($this->description,ENT_QUOTES,$charset),$liste_gestion);
			$liste_gestion = str_replace('!!id_liste!!',$this->id_liste,$liste_gestion);
			$liste_gestion = str_replace('!!print_btn!!',$print_btn,$liste_gestion);	
			
			//Recherche
			$this->search_in_list();
			
			//Gestion de la liste des notices et de la pagination
			if($page=="") $page=1;
			$affich.= "<span><b>".sprintf($msg["show_cart_n_notices"],count($this->filtered_notices))."</b></span>";
						
			$affich.= $this->gestion_tri('view');
		
			$affich.= "<blockquote>";
			
			// case à cocher de suppression transférée dans la classe notice_affichage				
			$cart_aff_case_traitement = 1 ; 
//			$affich.= "<form action='./index.php?lvl=show_list&sub=view&id_liste=$this->id_liste&page=$page' method='post' name='list_form'>\n";
			for ($i=(($page-1)*$opac_search_results_per_page); (($i<count($this->filtered_notices))&&($i<($page*$opac_search_results_per_page))); $i++) {
				if (substr($this->filtered_notices[$i],0,2)!="es") 
					$affich.= aff_notice($this->filtered_notices[$i],1); 
				else $affich.=aff_notice_unimarc(substr($this->filtered_notices[$i],2),1);
			}
//			$affich.= "</form>";
			$affich.= "</blockquote>";
			$affich.= $this->aff_navigation_notices($this->filtered_notices, $this->id_liste, 'view');
				
			//Gestion des checkbox
			if($this->public) {
				$liste_gestion = str_replace('!!checked!!','checked',$liste_gestion);
				if($this->confidential){
					$liste_gestion = str_replace('!!checked_conf!!','checked',$liste_gestion);						
				} else {
					$liste_gestion = str_replace('!!checked_conf!!','',$liste_gestion);	
				}
				$liste_gestion = str_replace('!!disabled_conf!!','',$liste_gestion);
				$liste_gestion = str_replace('!!color_conf!!','black',$liste_gestion);								
			} else {					
				$liste_gestion = str_replace('!!checked!!','',$liste_gestion);
				$liste_gestion = str_replace('!!checked_conf!!','',$liste_gestion);
				$liste_gestion = str_replace('!!disabled_conf!!','disabled',$liste_gestion);
				$liste_gestion = str_replace('!!color_conf!!','gray',$liste_gestion);
			}				
			if($this->readonly) 
				$liste_gestion = str_replace('!!checked_only!!','checked',$liste_gestion);
			else $liste_gestion = str_replace('!!checked_only!!','',$liste_gestion);
			$liste_gestion = str_replace('!!notice_filtre!!', htmlentities(implode(',',$this->filtered_notices),ENT_QUOTES,$charset),$liste_gestion);
			$liste_gestion = str_replace('!!list_tag!!',$this->gen_selector_tags(),$liste_gestion);
			
			//Ajout de lecteurs à la liste
			if($opac_shared_lists_add_empr) {
				$tpl_add_empr = "
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<label class='etiquette'>".$msg['list_lecture_add_empr']."</label>
				</div>
				<div class='row'>
					<input type='hidden' name='list_add_empr_id' id='list_add_empr_id' />
					<input type='text' id='list_add_empr_label' name='list_add_empr_label' class='saisie-20em' completion='empr' autfield='list_add_empr_id' value='' expand_mode='1' onKeyPress='if (event.keyCode == 13) return false;'>
					<input type='button' id='list_add_empr_button' name='list_add_empr_button' class='bouton' value=\"".$msg['925']."\" 
						onclick=\"if(confirm('".addslashes($msg['list_lecture_add_empr_confirm'])."')){ liste_lecture_add_empr('".$this->id_liste."', document.getElementById('list_add_empr_id').value); }\">
				</div>";
				$liste_gestion = str_replace('!!add_empr!!', $tpl_add_empr, $liste_gestion);
			} else {
				$liste_gestion = str_replace('!!add_empr!!', '', $liste_gestion);
			}
			
			//Gestion de la liste d'inscrit
			$list_inscrit = "
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<label class='etiquette'>$msg[list_lecture_inscrits] &nbsp;</label>
				</div>	
				<br />
				<div style='height:150px ; overflow:auto ; border:1px solid #CCCCCC' id='inscrit_list'>
					!!list_inscrit!!
				</div>	";
			$list_inscrit = str_replace('!!list_inscrit!!', $this->get_display_empr(), $list_inscrit);
			$liste_gestion = str_replace('!!inscrit_list!!',$list_inscrit,$liste_gestion);
			
			$liste_gestion = str_replace('!!search!!', "<div class='reading_list_search_container'>" . $this->get_display_search() . "</div>", $liste_gestion);
		}

		$liste_gestion = str_replace('!!liste_notice!!', "<div class='row'>" . $affich . "</div>", $liste_gestion);
		print $liste_gestion;
	}

	public function search_in_list(){
		global $user_query, $avis_search;

		if($user_query == '' || $user_query == '*') {
			$this->filtered_notices = $this->notices;
		} else {
			//On fait la recherche tous les champs
			$search_all_fields = new searcher_all_fields(stripslashes($user_query));
			$this->filtered_notices = array_values(array_intersect($this->notices, explode(',', $search_all_fields->get_result())));
			if($avis_search){
				$query = "select num_notice as notice_id from avis
						where num_notice in(".implode(',',$this->notices).") and type_object=1 and valide=1 and (sujet like '%".$user_query."%' or commentaire like '%".$user_query."%')";
				if($_SESSION['id_empr_session']) {
					$query .= "
						and (
							avis_private = 0
							or (avis_private = 1 and num_empr='".$_SESSION['id_empr_session']."')
							or (avis_private = 1 and avis_num_liste_lecture <> 0
									and avis_num_liste_lecture in (
									select num_liste from abo_liste_lecture
										where abo_liste_lecture.num_empr='".$_SESSION['id_empr_session']."' and abo_liste_lecture.etat=2
									)
								)
							)";
				} else {
					$query .= " and avis_private = 0";
				}
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)) {
					while($row = pmb_mysql_fetch_object($result)){
						$this->filtered_notices[]=$row->notice_id;
					}
					$this->filtered_notices = array_unique($this->filtered_notices);
				}
			}
		}
	}
	
	/**
	 * Consultation d'une liste statique
	 */
	public function consulter_liste(){
		global $liste_lecture_consultation, $charset, $msg, $opac_search_results_per_page, $page;
		global $cart_aff_case_traitement;
		
		$liste_lecture_consultation = str_replace('!!nom_liste!!',sprintf($msg['list_lecture_view'],htmlentities($this->nom_liste,ENT_QUOTES,$charset)),$liste_lecture_consultation);
		$liste_lecture_consultation = str_replace('!!liste_comment!!',htmlentities($this->description,ENT_QUOTES,$charset),$liste_lecture_consultation);
		$liste_lecture_consultation = str_replace('!!id_liste!!',$this->id_liste,$liste_lecture_consultation);
			
		$liste_lecture_consultation = str_replace('!!proprio!!', $this->get_display_owner(), $liste_lecture_consultation);
		if($this->subscribed){
			$print_btn="<input type='button' class='bouton' name='mail'
				onclick=\"w=window.open('print.php?lvl=list&id_liste=$this->id_liste','print_window','width=500, height=750,scrollbars=yes,resizable=1'); w.focus();\" value='".$msg['list_lecture_mail']."' />";
			$liste_lecture_consultation = str_replace('!!print_btn!!',$print_btn,$liste_lecture_consultation);
			$desabo_btn = "<input type='submit'  class='bouton' name='desabo' onclick='this.form.act.value=\"suppr_acces\";this.form.action=\"empr.php?tab=lecture&lvl=public_list\";' value=\"".$msg['list_lecture_desabo']."\" />";
			$liste_lecture_consultation = str_replace('!!abo_btn!!',$desabo_btn,$liste_lecture_consultation);
			if(!$this->readonly)
				$add_noti_btn = "<input type='submit' class='bouton' name='list_in' onclick='this.form.act.value=\"list_in\";' value='".$msg['list_lecture_list_in']."' />";
			else $add_noti_btn ='';
			$liste_lecture_consultation = str_replace('!!add_noti_btn!!',$add_noti_btn,$liste_lecture_consultation);
		}else{
			$liste_lecture_consultation = str_replace('!!print_btn!!','',$liste_lecture_consultation);
			$abo_btn = "<input type='submit' class='bouton' name='abo' onclick='this.form.act.value=\"get_acces\";this.form.action=\"empr.php?tab=lecture&lvl=public_list\";' value=\"".$msg['list_lecture_abo']."\" />";
			$liste_lecture_consultation = str_replace('!!abo_btn!!',$abo_btn,$liste_lecture_consultation);
			$liste_lecture_consultation = str_replace('!!add_noti_btn!!','',$liste_lecture_consultation);
		}
		
		//Recherche
		$liste_lecture_consultation = str_replace('!!search!!', "<div class='reading_list_search_container'>" . $this->get_display_search() . "</div>", $liste_lecture_consultation);
		$this->search_in_list();
		//Gestion de la liste des notices et de la pagination
		if($page=="")$page=1;
		$affich = "<span><b>".sprintf($msg["show_cart_n_notices"],count($this->filtered_notices))."</b></span>";

		$affich.= $this->gestion_tri('consultation');
		
		$affich.= "<blockquote>";
		// case à cocher de suppression transférée dans la classe notice_affichage
		if($this->subscribed){
			$cart_aff_case_traitement = 1 ;
		}
		$affich.= "<form action='./index.php?lvl=show_list&sub=view&id_liste=$this->id_liste&page=$page' method='post' name='list_form'>\n";
		for ($i=(($page-1)*$opac_search_results_per_page); (($i<count($this->filtered_notices))&&($i<($page*$opac_search_results_per_page))); $i++) {
			if (substr($this->filtered_notices[$i],0,2)!="es") 
				$affich.= aff_notice($this->filtered_notices[$i],1); 
			else 
				$affich.=aff_notice_unimarc(substr($this->filtered_notices[$i],2),1);
		}
		$affich.= "</form>";
		$affich.= "</blockquote>";
		$affich.= $this->aff_navigation_notices($this->filtered_notices, $this->id_liste, 'consultation');
		
		$liste_lecture_consultation = str_replace('!!notice_filtre!!', htmlentities(implode(',',$this->filtered_notices),ENT_QUOTES,$charset),$liste_lecture_consultation);
		$liste_lecture_consultation = str_replace('!!liste_notice!!', "<div class='row'>" . $affich . "</div>", $liste_lecture_consultation);
		
		print $liste_lecture_consultation;
	}
	
	private function gestion_tri($sub = 'consultation') {
		global $pmb_nb_max_tri, $msg;
		//Tri
		$affich = '';
		if (isset($_SESSION["last_sortreading_list"]) && !isset($_GET['sort'])) {
		    $_GET['sort'] = $_SESSION["last_sortreading_list"];
		}
		if (isset($_GET['sort'])) {
			$_SESSION["last_sortreading_list"] = $_GET['sort'];
			$sort = new sort('reading_list', 'session');
			$sql = "SELECT notice_id FROM notices WHERE notice_id IN (";
			for ($z = 0; $z < count($this->filtered_notices); $z++) {
				$sql.= "'" . $this->filtered_notices[$z] . "',";
			}
			$sql = substr($sql, 0, strlen($sql) - 1) . ")";
		
			$sql = $sort->appliquer_tri($_SESSION["last_sortreading_list"], $sql, 'notice_id', 0, 0);
		} else {
			$sql = "select notice_id from notices where notice_id in ('" . implode("','",$this->filtered_notices) . "') order by tit1";
		}
		$res = pmb_mysql_query($sql);
		$this->filtered_notices = array();
		while ($r = pmb_mysql_fetch_object($res)) {
			$this->filtered_notices[] = $r->notice_id;
		}
		if (count($this->filtered_notices) <= $pmb_nb_max_tri) {
			$params = rawurlencode(serialize(array(
					'sub' => $sub,
					'id_liste' => $this->id_liste,
			)));
			$affich_tris_result_liste = sort::show_tris_selector("reading_list");
			$affich_tris_result_liste = str_replace('!!page_en_cours!!', 'lvl=show_list&params=' . $params . '&id_liste=' . $this->id_liste, $affich_tris_result_liste);
			$affich_tris_result_liste = str_replace('!!page_en_cours1!!', 'lvl=show_list&params=' . $params . '&sub=' . $sub . '&id_liste=' . $this->id_liste, $affich_tris_result_liste);
			$affich.=  $affich_tris_result_liste;
		}
		if (isset($_GET['sort'])) {
			$affich.=  "<span class='sort'>" . $msg['tri_par'] . ' ' . $sort->descriptionTriParId($_SESSION["last_sortreading_list"]) . '<span class="espaceCartAction">&nbsp;</span></span>';
		}
		return $affich;
	}
	/**
	 * Affiche la barre de navigation des notices
	 */
	public function aff_navigation_notices($notices=array(),$id_liste, $sub){
		global $opac_search_results_per_page, $msg, $page;
		
		$affichage ='';
		$nbepages = ceil(count($notices)/$opac_search_results_per_page);
		$suivante = $page+1;
		$precedente = $page-1;
	
		// affichage du lien précédent si nécéssaire
		$affichage .= "<hr /><table style='border:0px' class='center'><tr>";
	
		// affichage du lien pour retour au début
		if($precedente > 1) {
			$affichage .= "<td style='width:14px' class='center'><a href=\"index.php?lvl=show_list&sub=$sub&id_liste=$id_liste&page=1\"><img src=\"".get_url_icon('first.png')."\"";
			$affichage .= " style='border:0px' alt=\"$msg[start]\"";
			$affichage .= " title=\"$msg[first_page]\"></a></td>";
		} else {
			$affichage .= "<td style='width:14px' class='center'><img src=\"".get_url_icon('first-grey.png')."\">";
		}
	
		if($precedente > 0) {
			$affichage .= "<td style='width:14px' class='center'><a href=\"index.php?lvl=show_list&sub=$sub&id_liste=$id_liste&page=$precedente\"><img src=\"".get_url_icon('prev.png')."\"";
			$affichage .= " style='border:0px' alt=\"$msg[prec]\"";
			$affichage .= " title=\"$msg[prec]\"></a></td>";
		} else {
			$affichage .= "<td style='width:14px' class='center'><img src=\"".get_url_icon('prev-grey.png')."\">";
		}
	
		$affichage .= "<td class='center'>$msg[page] $page/$nbepages</td>";
	
		// lien suivant
		if($suivante<=$nbepages) {
			$affichage .= "<td style='width:14px' class='center'><a href=\"index.php?lvl=show_list&sub=$sub&id_liste=$id_liste&page=$suivante\"><img src=\"".get_url_icon('next.png')."\"";
			$affichage .= " style='border:0px' alt=\"$msg[next]\"";
			$affichage .= " title=\"$msg[next]\"></a></td>";
		} else {
			$affichage .= "<td style='width:14px' class='center'><img src=\"".get_url_icon('next-grey.png')."\">";
		}
	
		// affichage du lien vers la fin
		if($suivante < $nbepages) {
			$affichage .= "<td style='width:14px' class='center'><a href=\"index.php?lvl=show_list&sub=$sub&id_liste=$id_liste&page=$nbepages\"><img src=\"".get_url_icon('last.png')."\"";
			$affichage .= " style='border:0px' alt=\"$msg[end]\"";
			$affichage .= " title=\"$msg[end]\"></a></td>";
		} else {
			$affichage .= "<td style='width:14px' class='center'><img src=\"".get_url_icon('last-grey.png')."\">";
		}
	
		$affichage .= "</tr></table><br />";
		
		return $affichage;
	}
	
	protected function fetch_empr() {
		$query = "select id_empr, trim(concat(empr_prenom,' ',empr_nom)) as nom, empr_login, empr_mail, nom_liste, confidential
			from empr, abo_liste_lecture, opac_liste_lecture
			where abo_liste_lecture.num_empr=empr.id_empr 
			and opac_liste_lecture.id_liste=abo_liste_lecture.num_liste
			and etat=2 and num_liste='".$this->id_liste."'
			order by nom";
		$result = pmb_mysql_query($query);
		$this->empr = array();
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->empr[$row->id_empr] = $row;
			}
		}
	}
	
	public function get_display_empr() {
		global $msg, $charset;
		
		$display = '';
		if(count($this->empr)) {
			foreach ($this->empr as $empr) {
			    $display .= "<img style='border:0px' class='align_top' src='".get_url_icon('cross.png', 1)."' alt='".htmlentities($msg["list_lecture_delete_subscriber"] ,ENT_QUOTES, $charset)."'  onclick=\"delete_from_liste('".$this->id_liste."','".$empr->id_empr."');\" /> ";
				$display .= $empr->nom."<br />";
			}
		} else {
			$display .= $msg['list_lecture_no_user_inscrit'];
		}
		return $display;
	}
	
	public function get_display_owner() {
		global $msg;
		$query = "select empr_nom, empr_prenom from empr where id_empr = ".$this->num_owner;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		return "(".sprintf($msg['list_lecture_owner'],$row->empr_prenom." ".$row->empr_nom).")";
	}
	
	/**
	 * envoi du mail d'inscription
	 * @param unknown $id_empr
	 */
	protected function send_subscribe_mail($id_empr) {
		global $msg, $charset, $opac_url_base, $opac_connexion_phrase, $empr_nom, $empr_prenom,$empr_mail;
		
		$inscrit = $this->empr[$id_empr];
		$objet = sprintf($msg['list_lecture_objet_subscribe_mail'],$inscrit->nom_liste);
		$date = time();
		$login = $inscrit->empr_login;
		$code=md5($opac_connexion_phrase.$login.$date);
		$corps = sprintf($msg['list_lecture_intro_mail'],$inscrit->nom,$inscrit->nom_liste).", <br />".sprintf($msg['list_lecture_subscribe_mail'],$empr_prenom." ".$empr_nom,$inscrit->nom_liste);
		if($com) $corps .= sprintf("<br />".$msg['list_lecture_corps_com_mail'],$empr_prenom." ".$empr_nom,"<br />".$com."<br />");
		$corps .= "<br /><br /><a href='".$opac_url_base."empr.php?code=$code&emprlogin=$login&date_conex=$date&tab=lecture&lvl=private_list' >".$msg['redirection_mail_link']."</a>";
		
		if($empr_mail) {
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=".$charset."\n";
			mailpmb($inscrit->nom, $inscrit->empr_mail, $objet, $corps, $empr_prenom." ".$empr_nom, $empr_mail, $headers);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Fonction qui ajoute un inscrit au tableau
	 */
	protected function add_empr($id_empr) {
		$query = "select id_empr, trim(concat(empr_prenom,' ',empr_nom)) as nom, empr_login, empr_mail, nom_liste, confidential
			from empr, abo_liste_lecture, opac_liste_lecture
			where abo_liste_lecture.num_empr=empr.id_empr
			and opac_liste_lecture.id_liste=abo_liste_lecture.num_liste
			and etat=2 and num_liste='".$this->id_liste."'
			and abo_liste_lecture.num_empr=".$id_empr;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result) == 1) {
			$row = pmb_mysql_fetch_object($result);
			$this->empr[$row->id_empr] = $row;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Fonction qui ajoute un inscrit à une liste confidentielle
	 */
	public function add_empr_in_list($id_empr) {
		//inscription 
		if(!is_object($this->empr[$id_empr])) {
			$query = "select * from abo_liste_lecture where num_empr ='".$id_empr."' and num_liste = '".$this->id_liste."'";
			$result = pmb_mysql_query($query);
			if($result) {
				if(pmb_mysql_num_rows($result)) {
					$query = "update abo_liste_lecture set etat = 2 where num_empr = '".$id_empr."' and num_liste = '".$this->id_liste."'";
				} else {
					$query = "insert into abo_liste_lecture (num_empr,num_liste, etat) values ('".$id_empr."', '".$this->id_liste."','2')";
				}
				pmb_mysql_query($query);
				$added = $this->add_empr($id_empr);
				if($added) {
					//envoi du mail d'inscription
					$this->send_subscribe_mail($id_empr);
					return true;
				}
			} 
		}
		return false;
	}
	
	/**
	 * envoi du mail de désinscription
	 * @param unknown $id_empr
	 */
	protected function send_unsubscribe_mail($id_empr) {
		global $msg, $charset, $opac_url_base, $opac_connexion_phrase, $empr_nom, $empr_prenom,$empr_mail;
	
		$inscrit = $this->empr[$id_empr];
		$objet = sprintf($msg['list_lecture_objet_unsubscribe_mail'],$inscrit->nom_liste);
		$date = time();
		$login = $inscrit->empr_login;
		$code=md5($opac_connexion_phrase.$login.$date);
		$corps = sprintf($msg['list_lecture_intro_mail'],$inscrit->nom,$inscrit->nom_liste).", <br />".sprintf($msg['list_lecture_unsubscribe_mail'],$empr_prenom." ".$empr_nom,$inscrit->nom_liste);
		if($com) $corps .= sprintf("<br />".$msg['list_lecture_corps_com_mail'],$empr_prenom." ".$empr_nom,"<br />".$com."<br />");
		$corps .= "<br /><br /><a href='".$opac_url_base."empr.php?code=$code&emprlogin=$login&date_conex=$date&tab=lecture&lvl=private_list' >".$msg['redirection_mail_link']."</a>";
		
		if($empr_mail) {
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=".$charset."\n";
			mailpmb($inscrit->nom, $inscrit->empr_mail, $objet, $corps, $empr_prenom." ".$empr_nom, $empr_mail, $headers);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Fonction qui inscrit un inscrit du tableau
	 */
	protected function delete_empr($id_empr) {
		if(is_object($this->empr[$id_empr])) {
			unset($this->empr[$id_empr]);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Fonction qui supprime un inscrit à une liste confidentielle
	 */
	public function delete_empr_in_list($id_empr){
		//désinscription
		if(is_object($this->empr[$id_empr])) {
			$query = "delete from abo_liste_lecture where num_liste='".$this->id_liste."' and num_empr='".$id_empr."'";
			pmb_mysql_query($query);
			$deleted = $this->delete_empr($id_empr);
			if($deleted) {
				//envoi du mail de désinscription
				$this->send_unsubscribe_mail($id_empr);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Sélecteur des listes de lecture partagées
	 */
	public static function gen_selector_my_list($notice_id) {
		global $msg;
	
		$display = '';
		$query = "select id_liste, nom_liste from opac_liste_lecture 
			where num_empr = '".$_SESSION['id_empr_session']."'
				or (id_liste in (select num_liste from abo_liste_lecture where num_empr = '".$_SESSION['id_empr_session']."' and etat=2) and confidential = 0 and read_only = 0)
				order by nom_liste";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			$display .= '<ul>';
			while($row = pmb_mysql_fetch_object($result)) {
				$display .= "<li><a onclick='liste_lecture_add_notice(".$row->id_liste.", ".$notice_id."); return false;' style='cursor:pointer'>".$row->nom_liste."</a></li>";
			}
			$display .= '</ul>';
		} else {
			$display .= '<ul><li>'.$msg['avis_liste_lecture_empty'].'</li></ul>';
		}
// 		$display = gen_liste($query,'id_liste','nom_liste', 'listes_lecture_notice_'.$notice_id, 'liste_lecture_add_notice(this.value, '.$notice_id.'); return false;', '', 0, $msg['avis_liste_lecture_empty'], 0, $msg['notice_title_liste_lecture_default_value']);
		return $display;
	}
	
	protected function get_tags() {
		$tags = array();
		$query = "select distinct tag from opac_liste_lecture where num_empr = '".$this->num_empr."' and tag <> ''";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$tags[] = $row->tag;
			}
		}
		return $tags;
	}
	protected function gen_selector_tags() {
	    global $charset, $msg;
		
	    $display = "<option value='' ".(!$this->tag ? "selected='selected'" : "").">" . htmlentities($msg['list_lecture_no_classement']) . "</option>";
		$tags = $this->get_tags();
		if(count($tags)){
			foreach($tags as $value){
				if($this->tag==$value){
					$selected=" selected='selected' ";
				}else{
					$selected="";
				}
				$display .= "<option value='".htmlentities($value ,ENT_QUOTES, $charset)."' $selected>".htmlentities(stripslashes($value) ,ENT_QUOTES, $charset)."</option>";
			}
		}
		return $display;
	}
	
	public function get_display_search() {
		global $msg;
		global $user_query, $avis_search;
		global $opac_avis_allow, $allow_avis;
		$display = "
			<div class='row'>
				".$msg['list_lecture_search_in_list']."
				<br /><input class='text_query' type='text' size='65' name='user_query' id='user_query' value='".stripslashes($user_query)."'> ";
		if($opac_avis_allow && $allow_avis) {
			$display .= "<input id='avis_search' type='checkbox' value='1' name='avis_search' ".($avis_search ? "checked='checked'" : "")."> <label for='avis_search'>".$msg['list_lecture_avis_search']."</label>";	
		}		
		$display .= "</div>
			<div class='row'>
				<input class='boutonrechercher' type='submit' name='search' value='".$msg[10]."' >		
			</div>";
		
		return $display;
	}
	
	public static function check_rights($id, $mode) {
		global $opac_shared_lists, $allow_liste_lecture;
		
		if(!$opac_shared_lists || !$allow_liste_lecture) return false;
		if($id) {
			switch ($mode) {
				case 'consultation' :
					$query = "select count(*) as nb
						from opac_liste_lecture
						join empr on empr.id_empr = opac_liste_lecture.num_empr
						where id_liste = ".$id." and (num_empr = '".$_SESSION['id_empr_session']."'
						or id_liste in (select num_liste from abo_liste_lecture where num_empr = '".$_SESSION['id_empr_session']."' and etat=2) 
						or (public = 1 and confidential = 0))";
					$result = pmb_mysql_query($query);
					$row = pmb_mysql_fetch_object($result);
					if($row->nb) {
						return true;
					} else {
						return false;
					}
					break;
				case 'view' :
					$query = "select count(*) as nb from opac_liste_lecture where id_liste = ".$id." and num_empr = ".$_SESSION['id_empr_session'];
					$result = pmb_mysql_query($query);
					$row = pmb_mysql_fetch_object($result);
					if($row->nb) {
						return true;
					} else {
						return false;
					}
					break;
				default :
					return false;
					break;
			}
		} else {
			return true;
		}
	}
	
	public function sort_notices($notices_id) {
	    if (isset($_GET['sort'])) {
	        $_SESSION['last_sortreading_list'] = $_GET['sort'];
	    }
	    if (isset($_SESSION['last_sortreading_list']) && $_SESSION['last_sortreading_list'] != '') {
	        $sort = new sort('reading_list', 'session');
	        $query = "SELECT notice_id FROM notices WHERE notice_id IN (" . implode(',', $notices_id) . ")";
	        $query = $sort->appliquer_tri($_SESSION['last_sortreading_list'], $query, 'notice_id', 0, 0);
	    } else {
	        $query = "SELECT notice_id FROM notices WHERE notice_id IN (" . implode(",", $notices_id) . ") ORDER BY tit1";
	    }
	    $res = pmb_mysql_query($query);
	    $filtered_notices = array();
	    while ($row = pmb_mysql_fetch_object($res)) {
	        $filtered_notices[] = $row->notice_id;
	    }
	    return $filtered_notices;
	}
}