<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_lecture.class.php,v 1.84.2.13.2.2 2025/01/30 09:08:05 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once ($class_path."/listes_lecture.class.php");
require_once ($class_path."/searcher.class.php");
require_once ($include_path."/templates/liste_lecture.tpl.php");
require_once ($include_path."/mail.inc.php");

use Pmb\AI\Library\searcher\SearcherSharedList;
use Pmb\AI\Opac\Views\AiView;
use Pmb\AI\Models\AiSessionSemanticModel;
use Pmb\AI\Models\SharedListModel;
use Pmb\AI\Opac\Controller\AiApiSharedListController;
use Pmb\AI\Orm\AiSharedListOrm;
use Pmb\AI\Models\AiSharedListDocnumModel;
use Pmb\AI\Orm\AiSharedListDocnumOrm;

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
	public $allow_add_records=1;
	public $allow_remove_records=1;
	public $empr = array();
	public $filtered_notices=array();
	public $subscribed = 0;
	public $from_cart = 0;

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
		global $opac_shared_lists_readonly;

	    $this->nom_liste = '';
	    $this->description='';
	    $this->public=0;
	    $this->num_owner = 0;
	    $this->readonly=$opac_shared_lists_readonly;
	    $this->notices = array();
	    $this->notices_create_date = array();
	    $this->confidential=0;
	    $this->tag='';
	    $this->allow_add_records=($opac_shared_lists_readonly ? 1 : 0);
	    $this->allow_remove_records=($opac_shared_lists_readonly ? 1 : 0);
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
	            $this->allow_add_records=$liste->allow_add_records;
	            $this->allow_remove_records=$liste->allow_remove_records;
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

		if(is_countable($list_ck) && sizeof($list_ck)) {
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
		global $cb_demande;

		for($i=0;$i<sizeof($cb_demande);$i++){
			$info = explode('-',$cb_demande[$i]);
			$req = " update abo_liste_lecture set etat=2 where num_empr='".$info[1]."' and num_liste='".$info[0]."'";
			pmb_mysql_query($req);

			$mail_opac_reader_readinglist_accept_access = new mail_opac_reader_readinglist_accept_access();
			$mail_opac_reader_readinglist_accept_access->set_mail_to_id($info[1]);
			$mail_opac_reader_readinglist_accept_access->set_id_liste($info[0]);
			$mail_opac_reader_readinglist_accept_access->send_mail();
		}
	}

	/**
	 * Refuse l'accès aux listes confidentielles
	 */
	protected function refuser_acces_confidentiel(){
		global $cb_demande;

		for($i=0;$i<sizeof($cb_demande);$i++){
			$info = explode('-',$cb_demande[$i]);
			$req = " update abo_liste_lecture set etat=0 where num_empr='".$info[1]."' and num_liste='".$info[0]."'";
			pmb_mysql_query($req);

			$mail_opac_reader_readinglist_refuse_access = new mail_opac_reader_readinglist_refuse_access();
			$mail_opac_reader_readinglist_refuse_access->set_mail_to_id($info[1]);
			$mail_opac_reader_readinglist_refuse_access->set_id_liste($info[0]);
			$mail_opac_reader_readinglist_refuse_access->send_mail();
		}
	}

	/**
	 * Supprime la ou les listes sélectionnée(s)
	 */
	protected function supprimer_liste(){
		global $list_ck, $ai_active;

		if ($list_ck) {
			for ($i = 0; $i < sizeof($list_ck); $i++) {
				$list_ck[$i] = intval($list_ck[$i]);

				$docnums = AiSharedListDocnumModel::fetchAllDocnumsByListId($list_ck[$i]);
				array_walk($docnums, function (&$docnum) {
					$docnum->delete();
				});

				$rqt = "delete from opac_liste_lecture where id_liste='".$list_ck[$i]."'";
				pmb_mysql_query($rqt);

				$rqt = "delete from abo_liste_lecture where num_liste='".$list_ck[$i]."'";
				pmb_mysql_query($rqt);

				$query = "delete from opac_liste_lecture_notices where opac_liste_lecture_num=" . $list_ck[$i];
				pmb_mysql_query($query);
			}

			if($ai_active && !is_null(AiSharedListOrm::getAiSettingActive())) {
				$aiAPIController = new AiApiSharedListController();

				$structure = SharedListModel::getStructureToDeleteIndexation("deleteLists", 0, array_map('intval', $list_ck));
				$aiAPIController->getApi()->cleanElementsContainer($structure);
			}

		} elseif($this->id_liste) {

			$docnums = AiSharedListDocnumModel::fetchAllDocnumsByListId($this->id_liste);
			array_walk($docnums, function (&$docnum) {
				$docnum->delete();
			});

			$rqt = "delete from opac_liste_lecture where id_liste='".$this->id_liste."'";
			pmb_mysql_query($rqt);

			$rqt = "delete from abo_liste_lecture where num_liste='".$this->id_liste."'";
			pmb_mysql_query($rqt);

			$query = "delete from opac_liste_lecture_notices where opac_liste_lecture_num=" . $this->id_liste;
			pmb_mysql_query($query);

			if($ai_active && !is_null(AiSharedListOrm::getAiSettingActive())) {
				$aiAPIController = new AiApiSharedListController();

				$structure = SharedListModel::getStructureToDeleteIndexation("deleteList", intval($this->id_liste));
				$aiAPIController->getApi()->cleanElementsContainer($structure);
			}

			$this->id_liste = 0;
			$this->fetch_data();
		}
	}

	/**
	 * Supprime les notices cochées de la liste
	 */
	protected function supprimer_coche(){
		global $notice, $ai_active;

		if(empty($this->empr)) {
			$this->fetch_empr();
		}
		if($this->num_owner == $_SESSION['id_empr_session'] || (array_key_exists($_SESSION['id_empr_session'], $this->empr) && $this->allow_remove_records)) {
			if(is_array($notice)) {
				$query = "DELETE FROM opac_liste_lecture_notices WHERE opac_liste_lecture_num=" . $this->id_liste . "
		            AND opac_liste_lecture_notice_num IN(" . implode(',', $notice) . ")";
				pmb_mysql_query($query);

				if($ai_active && !is_null(AiSharedListOrm::getAiSettingActive())) {
					$AiAPIController = new AiApiSharedListController();

					$structure = SharedListModel::getStructureToDeleteIndexation("deleteRecordsInList", intval($this->id_liste), array_map('intval', $notice));
					$AiAPIController->getApi()->cleanElementsContainer($structure);
				}

	        	$this->fetch_data();
			}
		}
	}

	/**
	 * Partager la ou les listes sélectionnée(s)
	 */
	protected function share_liste(){
		global $list_ck;
		if (is_countable($list_ck)) {
			for($i=0;$i<sizeof($list_ck);$i++){
				$rqt = "update opac_liste_lecture set public=1 where num_empr='".$this->num_empr."' and id_liste='".$list_ck[$i]."' ";
				pmb_mysql_query($rqt);
			}
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
	public function get_num_empr($login){
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
	public function enregistrer(){
		global $list_name, $list_comment, $notice_filtre, $cb_share, $cb_readonly, $cb_confidential, $list_tag, $allow_add_records, $allow_remove_records;

		$list_name = strip_tags($list_name);
		$list_comment = strip_tags($list_comment);
		$list_tag = strip_tags($list_tag);
		if(!$this->id_liste){
			$rqt="insert into opac_liste_lecture (description, public, num_empr, nom_liste, read_only, confidential, tag, allow_add_records, allow_remove_records)
				values ('".$list_comment."','".($cb_share ? 1 : 0)."', '".$this->num_empr."', '".$list_name."', '".($cb_readonly ? 1 : 0)."', '".($cb_confidential ? 1 : 0)."', '".$list_tag."', '".($allow_add_records ? 1 : 0)."', '".($allow_remove_records ? 1 : 0)."')";
			pmb_mysql_query($rqt);
			$this->id_liste = pmb_mysql_insert_id();
		} elseif($this->id_liste) {
			$rqt="update opac_liste_lecture set description='".$list_comment."', public='".($cb_share ? 1 : 0)."',
				nom_liste='".$list_name."', read_only='".($cb_readonly ? 1 : 0)."', confidential='".($cb_confidential ? 1 : 0)."', tag='".$list_tag."', allow_add_records='".($allow_add_records ? 1 : 0)."', allow_remove_records='".($allow_remove_records ? 1 : 0)."' where id_liste='".$this->id_liste."'";
			pmb_mysql_query($rqt);
		}
		$notices_associees = explode(",", $notice_filtre);
		foreach ($notices_associees as $notice_id) {
		    if ($notice_id) {
		        $query = "INSERT IGNORE INTO opac_liste_lecture_notices SET opac_liste_lecture_num=". $this->id_liste . ",opac_liste_lecture_notice_num=" . $notice_id;
		        pmb_mysql_query($query);
		        //On retire la notice du panier ?
		        $this->delete_cart_record($notice_id);
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
		    //On retire la notice du panier ?
		    $this->delete_cart_record($notice_id);
		}
		$this->notices = $notice_liste;
	}

	public function delete_cart_record($notice_id) {
		global $opac_cart_records_remove, $from_cart;

		if(!$this->from_cart && !empty($from_cart)) {
			$this->from_cart = $from_cart;
		}
		if($opac_cart_records_remove && $this->from_cart) {
			$as=array_search($notice_id,$_SESSION["cart"]);
			if (($as!==null)&&($as!==false)) {
				unset($_SESSION["cart"][$as]);
			}
		}
	}

	/**
	 * Ajouter une notice à la liste de lecture
	 * @param integer $id_notice
	 */
	public function add_notice($id_notice) {
		$query = "select id_liste from opac_liste_lecture
			where (id_liste = '".$this->id_liste."' and num_empr = '".$_SESSION['id_empr_session']."')
				or (id_liste in (select num_liste from abo_liste_lecture where num_liste = '".$this->id_liste."' and num_empr = '".$_SESSION['id_empr_session']."' and etat=2) and allow_add_records = 1 and read_only = 0)";
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
	public function extraire_vers_panier(){
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
		global $msg, $liste_demande, $emprlogin;

		$req = "select id_liste,nom_liste, id_empr, empr_nom, empr_prenom
		from opac_liste_lecture oll, abo_liste_lecture abo, empr
		where oll.id_liste=abo.num_liste
		and abo.num_empr=id_empr
		and oll.num_empr='".($this->num_empr ? $this->num_empr : $this->get_num_empr($emprlogin))."'
		and oll.confidential=1
		and etat=1
		order by nom_liste";
		$res=pmb_mysql_query($req);
		if(!pmb_mysql_num_rows($res)){
			$affichage_liste = "<div class='row'><label>".$msg['list_lecture_no_demande']."</label></div>";
			$liste_demande =  str_replace("!!accepter_btn!!",'',$liste_demande);
			$liste_demande =  str_replace("!!refuser_btn!!",'',$liste_demande);
			$liste_demande =  str_replace("!!demande_list!!",$affichage_liste,$liste_demande);
			$this->display = $liste_demande;
			return;
		}

		$noms_listes = array();
		$aff_liste = "<script src='./includes/javascript/liste_lecture.js' ></script>
				<script src='./includes/javascript/http_request.js' ></script>";
		$aff_liste .= "<ul>";
		while(($liste = pmb_mysql_fetch_object($res))){
			if(!isset($noms_listes[$liste->nom_liste])) {
				$aff_liste .= "<li><u>".$liste->nom_liste."</u></li>";
				$noms_listes[$liste->nom_liste] = $liste->nom_liste;
			}
			$aff_liste .= "<blockquote role='presentation'><div class='row'><input type='checkbox' name='cb_demande[]' value=\"".$liste->id_liste."-".$liste->id_empr."\"><label>".$liste->empr_prenom.' '.$liste->empr_nom."</label></div></blockquote>";
		}
		$aff_liste .= "</ul>";
		$accept_btn = "<input type='submit' class='bouton' id='accept' name='accept' value=\"$msg[list_lecture_accept_demande]\" onclick='this.form.lvl.value=\"demande_list\"; this.form.act.value=\"accept_acces\";'/>";
		$refus_btn = "<input type='button' class='bouton' id='refus' name='refus' value=\"$msg[list_lecture_refus_demande]\"  onclick='make_refus_form(); '/>";
		$liste_demande =  str_replace("!!accepter_btn!!",$accept_btn,$liste_demande);
		$liste_demande =  str_replace("!!refuser_btn!!",$refus_btn,$liste_demande);
		$liste_demande =  str_replace("!!demande_list!!",$aff_liste,$liste_demande);


		$this->display = $liste_demande;

	}

	public function get_form_ai_buttons() {
		global $ai_active, $opac_url_base, $ai_index_nb_elements, $ai_upload_max_size;

		$ai_buttons = "";
		if($ai_active && !is_null(AiSharedListOrm::getAiSettingActive())) {
			$iaView = new AiView("ai/sharedList", [
				"webservice_url" => $opac_url_base . "rest.php/aiapi/",
				"shared_list_id" => intval($this->id_liste),
				"indexation_packet_size" => intval($ai_index_nb_elements),
				"nb_records" => intval(SharedListModel::countNotIndexedRecords(intval($this->id_liste))),
				"nb_docnums" => intval(AiSharedListDocnumModel::countNotIndexedDocnum(intval($this->id_liste))),
				"upload_max_size" => intval($ai_upload_max_size)
			]);
			$ai_buttons = $iaView->render();
		}

		return $ai_buttons;
	}

	public function get_form() {
		global $liste_gestion, $charset, $msg;
		global $liste_lecture_gestion_boutons;
		global $opac_shared_lists_add_empr;
		global $page, $opac_search_results_per_page;
		global $ai_active;

		$form = $liste_gestion;
		$form = str_replace('!!name_list!!', htmlentities($this->nom_liste,ENT_QUOTES,$charset), $form);
		$form = str_replace('!!list_comment!!', htmlentities($this->description,ENT_QUOTES,$charset), $form);

		if(!$this->id_liste){
			$form = str_replace('!!liste_lecture_gestion_boutons!!', '', $form);
			$form = str_replace('!!titre_liste!!',htmlentities($msg['list_lecture_create'],ENT_QUOTES,$charset),$form);

			$form = str_replace('!!print_btn!!','',$form);
		} else {
			$ai_buttons = $this->get_form_ai_buttons();

			$liste_lecture_gestion_boutons = str_replace('!!liste_lecture_gestion_ai_boutons!!', $ai_buttons, $liste_lecture_gestion_boutons);

			$form = str_replace('!!liste_lecture_gestion_boutons!!', $liste_lecture_gestion_boutons, $form);
			$form = str_replace('!!titre_liste!!',htmlentities($msg['list_lecture_modify'],ENT_QUOTES,$charset),$form);

			$print_btn="<input type='button' class='bouton' name='mail'
				onclick=\"w=window.open('print.php?lvl=list&id_liste=$this->id_liste','print_window','width=500, height=750,scrollbars=yes,resizable=1'); w.focus();\" value='".$msg['list_lecture_mail']."' />";
			$form = str_replace('!!print_btn!!',$print_btn,$form);
		}

		//Gestion des checkbox
		if($this->readonly) {
			$form = str_replace('!!checked_only!!','checked',$form);
			$form = str_replace('!!checked_allow_add_records!!','',$form);
			$form = str_replace('!!checked_allow_remove_records!!','',$form);
			$form = str_replace('!!disabled_allow_add_records!!','disabled',$form);
			$form = str_replace('!!disabled_allow_remove_records!!','disabled',$form);
			$form = str_replace('!!color_allow_add_records!!','gray',$form);
			$form = str_replace('!!color_allow_remove_records!!','gray',$form);
		} else {
			$form = str_replace('!!checked_only!!','',$form);
			$form = str_replace('!!checked_allow_add_records!!',($this->allow_add_records ? 'checked' : ''),$form);
			$form = str_replace('!!checked_allow_remove_records!!',($this->allow_remove_records ? 'checked' : ''),$form);
			$form = str_replace('!!disabled_allow_add_records!!','',$form);
			$form = str_replace('!!disabled_allow_remove_records!!','',$form);
			$form = str_replace('!!color_allow_add_records!!','black',$form);
			$form = str_replace('!!color_allow_remove_records!!','black',$form);
		}
		if($this->public) {
			$form = str_replace('!!checked!!','checked',$form);
			if($this->confidential){
				$form = str_replace('!!checked_conf!!','checked',$form);
			} else {
				$form = str_replace('!!checked_conf!!','',$form);
			}
			$form = str_replace('!!disabled_conf!!','',$form);
			$form = str_replace('!!color_conf!!','black',$form);
		} else {
			$form = str_replace('!!checked!!','',$form);
			$form = str_replace('!!checked_conf!!','',$form);
			$form = str_replace('!!disabled_conf!!','disabled',$form);
			$form = str_replace('!!color_conf!!','gray',$form);
		}
		$form = str_replace('!!id_liste!!', $this->id_liste, $form);
		$form = str_replace('!!liste_btn!!','',$form);
		$form = str_replace('!!list_tag!!',$this->gen_selector_tags(),$form);

		//Ajout de lecteurs à la liste
		if($this->id_liste && $opac_shared_lists_add_empr) {
			$tpl_add_empr = "
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<label class='etiquette' for='list_add_empr_label'>".$msg['list_lecture_add_empr']."</label>
				</div>
				<div class='row'>
					<input type='hidden' name='list_add_empr_id' id='list_add_empr_id' />
					<input type='text' id='list_add_empr_label' name='list_add_empr_label' class='saisie-20em' completion='empr' autfield='list_add_empr_id' value='' expand_mode='1' onKeyPress='if (event.keyCode == 13) return false;'>
					<input type='button' id='list_add_empr_button' name='list_add_empr_button' class='bouton' value=\"".$msg['925']."\"
						onclick=\"if(confirm('".addslashes($msg['list_lecture_add_empr_confirm'])."')){ liste_lecture_add_empr('".$this->id_liste."', document.getElementById('list_add_empr_id').value); }\">
				</div>";
			$form = str_replace('!!add_empr!!', $tpl_add_empr, $form);
		} else {
			$form = str_replace('!!add_empr!!', '', $form);
		}
		if($this->id_liste) {
			//Gestion de la liste d'inscrit
			$list_inscrit = "
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<label class='etiquette'>".$msg['list_lecture_inscrits']." &nbsp;</label>
				</div>
				<br />
				<div style='height:150px ; overflow:auto ; border:1px solid #CCCCCC' id='inscrit_list'>
					!!list_inscrit!!
				</div>	";
			$list_inscrit = str_replace('!!list_inscrit!!', $this->get_display_empr(), $list_inscrit);
			$form = str_replace('!!inscrit_list!!',$list_inscrit,$form);

			if($ai_active) {
				//Gestion des tabs de la recherche si le module IA est activé
				$search_tabs = "
						<script>
							document.addEventListener('DOMContentLoaded', function() {
								const searchContainers = {
									simple: document.querySelector('.reading_list_search_content .reading_list_search_container'),
									ai: document.querySelector('.reading_list_search_content .ai_search_container'),
									docnums: document.querySelector('.reading_list_search_content .docnums_container'),
								};
								const searchTabs = {
									simple: document.querySelector('.reading_list_search_tabs li.reading_list_search_simple'),
									ai: document.querySelector('.reading_list_search_tabs li.reading_list_search_ai'),
									docnums: document.querySelector('.reading_list_search_tabs .reading_list_search_docnums'),
								};

								// Rendre la fonction globale
								window.switchSearch = function (tab) {
									Object.keys(searchContainers).forEach(key => {
										const isActive = key === tab;
										searchContainers[key].style.display = isActive ? 'block' : 'none';
										searchTabs[key].classList.toggle('active', isActive);
										searchTabs[key].setAttribute('aria-expanded', isActive);
									});

									window.dispatchEvent(new CustomEvent('search-tab-change', { detail: tab }));
								}

								// Initialiser avec l'onglet
								if (window.location.hash === '#ai_search') {
									switchSearch('ai');
									window.location.hash = 'ai_search';
								} else {
									switchSearch('simple');
								}
							});
						</script>
					<ul class='search_tabs reading_list_search_tabs'>
						<li class='reading_list_search_simple active' aria-expanded='true'>
							<input type='button' value='". htmlentities($msg['sharedlist_simple_search'], ENT_QUOTES, $charset) ."' onclick=\"switchSearch('simple');\">
						</li>
						<li class='reading_list_search_ai' aria-expanded='false'>
							<input type='button' value='". htmlentities($msg['sharedlist_ai_search'], ENT_QUOTES, $charset) ."' onclick=\"switchSearch('ai');\">
						</li>
						<li class='reading_list_search_docnums' aria-expanded='false'>
							<input type='button' value='". htmlentities($msg['sharedlist_docnums_list'], ENT_QUOTES, $charset) ."' onclick=\"switchSearch('docnums');\">
						</li>
					</ul>
				";
				$form = str_replace('!!search_tabs!!', $search_tabs, $form);

				$form = str_replace('!!ai_answer!!', '<div class="ai_search_container" style="display:none">' . $this->get_search_form_ai_search() . '</div>', $form);
				$form = str_replace('!!docnums_list!!', '<div class="docnums_container" style="display:none">' . $this->get_display_docnums() . '</div>', $form);

			} else {
				$form = str_replace('!!search_tabs!!', '', $form);
				$form = str_replace('!!ai_answer!!', '', $form);
				$form = str_replace('!!docnums_list!!', '', $form);
			}
			
			$form = str_replace('!!search!!', "<div class='reading_list_search_container'>" . $this->get_display_search() . "</div>", $form);
		} else {
			$form = str_replace('!!inscrit_list!!','',$form);
			$form = str_replace('!!search!!', '', $form);
			$form = str_replace('!!search_tabs!!', '', $form);
			$form = str_replace('!!ai_answer!!', '', $form);
			$form = str_replace('!!docnums_list!!', '', $form);

		}
		//Gestion de la liste des notices et de la pagination
		if ($page=="") {
			$page=1;
		}

		$form = str_replace('!!page!!', htmlentities($page, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!nb_per_page_custom!!', htmlentities($opac_search_results_per_page, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!from_cart!!',$this->from_cart,$form);
		return $form;
	}

	/**
	 * Récupère le formulaire de recherche pour la recherche AI.
	 *
	 * @return string Le HTML du formulaire de recherche AI rendu avec VueJS.
	 */
	public function get_search_form_ai_search() {
		global $opac_url_base, $msg, $user_query;

		$aiSettings = AiSharedListOrm::getAiSettingActive();
		if (empty($aiSettings)) {
			return "";
		}

		$fetch_text_generation = !empty($user_query);

		// Ne pas mettre ces globals au debut de la fonction,
		// car elle peut être redéfini dans "AiSessionSemanticModel::get_history"
		global $ai_session, $ai_session_index_question;

		if ($ai_session && !AiSessionSemanticModel::exist($ai_session, AiSessionSemanticModel::TYPE_SHARED_LIST)) {
			$ai_session = 0;
			$ai_session_index_question = null;
		}

		$iaView = new AiView("ai/search", [
            "webservice_url" => $opac_url_base . "rest.php/aiapi/AiApiSharedList/",
            "welcome_message" => $msg["sharedlist_ia_search_welcome"],
            "ai_session" => $ai_session ?? null,
            "ai_session_index_question" => $ai_session_index_question ?? null,
			"fetch_text_generation" => $fetch_text_generation,
			"type" => AiSessionSemanticModel::TYPE_SHARED_LIST,
			"list_id" => $this->id_liste
        ]);
		return $iaView->render();
	}

	public function add_list() {
	    $form = $this->get_form();
	    $form = str_replace('!!liste_notice!!', '', $form);
	    $form = str_replace('!!notice_filtre!!', '', $form);
	    $form = str_replace('!!navbar!!', '', $form);
	    print $form;
	}
	/**
	 * Génère le formulaire de gestion d'une liste
	 */
	public function affichage_saveform($notice_asso=array()){
		global $charset, $msg, $opac_search_results_per_page, $cart_aff_case_traitement, $page;

		$affich = '';

		if (!$this->id_liste) {
			for ($i = 0; $i < sizeof($notice_asso); $i++) {
				if (substr($notice_asso[$i],0,2) != "es") {
					$affich .= aff_notice($notice_asso[$i],1);
				} else {
					$affich .= aff_notice_unimarc(substr($notice_asso[$i], 2), 1);
				}
			}
			$form = str_replace(
				'!!notice_filtre!!',
				htmlentities(implode(',',$notice_asso),ENT_QUOTES,$charset),
				$this->get_form()
			);
		} else {
			//Recherche
			$this->search_in_list();

			// Pour la recherche sémantique, il faut faire dans un premier temps la recherche dans la liste de lecture
			// Puis générer le formulaire
			$form = $this->get_form();

			//Gestion de la liste des notices et de la pagination
			$length = count($this->filtered_notices);
			if ($page=="") {
				$page = 1;
			} else {
				$page = intval($page);
			}


			$affich.= "<span><b>";

			global $ai_session;
			if (isset($ai_session)) {
				$notices = array_filter($this->filtered_notices, function($notice) {
					return substr($notice, 0, 6) !== "docnum";
				});
				$ndNotices = count($notices);
				$affich.= sprintf($msg["show_sharedlist_n_result"], $ndNotices, $length - $ndNotices);
			} else {
				$affich.= sprintf($msg["show_cart_n_notices"], $length);
			}

			$affich.= "</b></span>";

			$affich .= $this->gestion_tri('view');

			$affich.= "<script>
				function sendDocnumToVisionneuse(id) {
					const visionneuseIframe = document.getElementById('visionneuseIframe');
					if (visionneuseIframe) {
						visionneuseIframe.src = './visionneuse.php?driver=pmb_document&lvl=visionneuse&cms_type=shared_list&id=' + id;
					} else {
						console.error('visionneuseIframe not found');
					}
				}
			</script>
			";
			$affich.= "<blockquote role='presentation'>";

			// case à cocher de suppression transférée dans la classe notice_affichage
			$cart_aff_case_traitement = 1 ;

			$startPage = ($page-1) * $opac_search_results_per_page;
			$maxNbElement = ($page * $opac_search_results_per_page);
			for ($i = $startPage; ($i < $length) && ($i < $maxNbElement); $i++) {
				switch (true) {
					case substr($this->filtered_notices[$i],0,6) === "docnum":
						$affich .= $this->aff_docnum($this->filtered_notices[$i]);
						break;

					case substr($this->filtered_notices[$i],0,2) === "es":
						$affich .= aff_notice_unimarc(substr($this->filtered_notices[$i], 2), 1);
						break;

					default:
						$affich .= aff_notice($this->filtered_notices[$i],1);
						break;
				}
			}
			$affich .= "</blockquote>";
			$navbar = $this->aff_navigation_notices($this->filtered_notices, $this->id_liste, 'view');

			$form = str_replace('!!notice_filtre!!', htmlentities(implode(',',$this->filtered_notices),ENT_QUOTES,$charset),$form);
		}

		$form = str_replace('!!liste_notice!!', "<div class='row'>" . $affich . "</div>", $form);
		$form = str_replace('!!navbar!!', $navbar, $form);

		print $form;
	}

	public function search_in_list(){
		global $user_query, $ai_session;

		if (isset($ai_session) && !empty($user_query)) {
			$this->make_ai_search();
		} else {
			$this->make_simple_search();
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
			if(!$this->readonly && $this->allow_add_records) {
				$add_noti_btn = "<input type='submit' class='bouton' name='list_in' onclick='this.form.act.value=\"list_in\";' value='".$msg['list_lecture_list_in']."' />";
			} else {
				$add_noti_btn ='';
			}
			$liste_lecture_consultation = str_replace('!!add_noti_btn!!',$add_noti_btn,$liste_lecture_consultation);

			$ai_buttons = $this->get_form_ai_buttons();
			$liste_lecture_consultation = str_replace('!!liste_lecture_gestion_ai_boutons!!', $ai_buttons, $liste_lecture_consultation);
		}else{
			$liste_lecture_consultation = str_replace('!!print_btn!!','',$liste_lecture_consultation);
			$abo_btn = "<input type='submit' class='bouton' name='abo' onclick='this.form.act.value=\"get_acces\";this.form.action=\"empr.php?tab=lecture&lvl=public_list\";' value=\"".$msg['list_lecture_abo']."\" />";
			$liste_lecture_consultation = str_replace('!!abo_btn!!',$abo_btn,$liste_lecture_consultation);
			$liste_lecture_consultation = str_replace('!!add_noti_btn!!','',$liste_lecture_consultation);
		}

		//Gestion des tabs de la recherche si le module IA est activé
		$search_tabs = "
			<script>
				document.addEventListener('DOMContentLoaded', function() {
					const searchContainers = {
						simple: document.querySelector('.reading_list_search_content .reading_list_search_container'),
						ai: document.querySelector('.reading_list_search_content .ai_search_container'),
						docnums: document.querySelector('.reading_list_search_content .docnums_container'),
					};
					const searchTabs = {
						simple: document.querySelector('.reading_list_search_tabs li.reading_list_search_simple'),
						ai: document.querySelector('.reading_list_search_tabs li.reading_list_search_ai'),
						docnums: document.querySelector('.reading_list_search_tabs .reading_list_search_docnums'),
					};

					// Rendre la fonction globale
					window.switchSearch = function (tab) {
						Object.keys(searchContainers).forEach(key => {
							const isActive = key === tab;
							searchContainers[key].style.display = isActive ? 'block' : 'none';
							searchTabs[key].classList.toggle('active', isActive);
							searchTabs[key].setAttribute('aria-expanded', isActive);
						});
					}

					// Initialiser avec l'onglet
					if (window.location.hash === '#ai_search') {
						switchSearch('ai');
						window.location.hash = 'ai_search';
					} else {
						switchSearch('simple');
					}
				});
			</script>
			<ul class='search_tabs reading_list_search_tabs'>
				<li class='reading_list_search_simple active' aria-expanded='true'>
					<input type='button' value='". htmlentities($msg['sharedlist_simple_search'], ENT_QUOTES, $charset) ."' onclick=\"switchSearch('simple');\">
				</li>
				<li class='reading_list_search_ai' aria-expanded='false'>
					<input type='button' value='". htmlentities($msg['sharedlist_ai_search'], ENT_QUOTES, $charset) ."' onclick=\"switchSearch('ai');\">
				</li>
				<li class='reading_list_search_docnums' aria-expanded='false'>
					<input type='button' value='". htmlentities($msg['sharedlist_docnums_list'], ENT_QUOTES, $charset) ."' onclick=\"switchSearch('docnums');\">
				</li>
			</ul>
		";
		$liste_lecture_consultation = str_replace('!!search_tabs!!', $search_tabs, $liste_lecture_consultation);

		$liste_lecture_consultation = str_replace('!!ai_answer!!', '<div class="ai_search_container" style="display:none">' . $this->get_search_form_ai_search() . '</div>', $liste_lecture_consultation);
		$liste_lecture_consultation = str_replace('!!docnums_list!!', '<div class="docnums_container" style="display:none">' . $this->get_display_docnums() . '</div>', $liste_lecture_consultation);
		$liste_lecture_consultation = str_replace('!!search!!', "<div class='reading_list_search_container'>" . $this->get_display_search() . "</div>", $liste_lecture_consultation);

		$this->search_in_list();
		//Gestion de la liste des notices et de la pagination
		if($page=="")$page=1;
		$affich = "<span><b>".sprintf($msg["show_cart_n_notices"],count($this->filtered_notices))."</b></span>";

		$affich.= $this->gestion_tri('consultation');

		if(count($this->filtered_notices)) {
			if(!$this->readonly && $this->allow_remove_records) {
				$affich.= "<blockquote role='presentation'>";
				$affich.= "<img src='".get_url_icon('suppr_coche.gif')."'
								title='".htmlentities($msg['list_lecture_suppr_checked'], ENT_QUOTES, $charset)."'
								alt='".htmlentities($msg['list_lecture_suppr_checked'], ENT_QUOTES, $charset)."'
								onclick=\"if(confirm_delete_shared_noti()) {document.forms['liste_lecture_search'].action += '&act=suppr_ck'; document.forms['liste_lecture_search'].submit();} return false;\" style='cursor:pointer'/>";
				$affich.= "</blockquote>";
			}
		}

		$affich.= "<blockquote role='presentation'>";
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
		$liste_lecture_consultation = str_replace('!!page!!', htmlentities($page, ENT_QUOTES, $charset), $liste_lecture_consultation);
		$liste_lecture_consultation = str_replace('!!nb_per_page_custom!!', htmlentities($opac_search_results_per_page, ENT_QUOTES, $charset), $liste_lecture_consultation);
		$liste_lecture_consultation = str_replace('!!liste_notice!!', "<div class='row'>" . $affich . "</div>", $liste_lecture_consultation);

		print $liste_lecture_consultation;
	}

	/**
	 * Gestion du tri
	 *
	 * @param string $sub
	 * @return string
	 */
	private function gestion_tri($sub = 'consultation') {
		global $pmb_nb_max_tri, $msg, $ai_session;


		// On  ne fait pas de tris si on est sur une recherche semantique
		if (!isset($ai_session)) {
			if (isset($_SESSION["last_sortreading_list"]) && !isset($_GET['sort'])) {
				$_GET['sort'] = $_SESSION["last_sortreading_list"];
			}

			if (isset($_GET['sort'])) {
				$_SESSION["last_sortreading_list"] = $_GET['sort'];
				$sort = new sort('reading_list', 'session');
				$sql = "SELECT notice_id FROM notices WHERE notice_id IN ";
				$sql .= "(" . implode(',', $this->filtered_notices) . ")";
				$sql = $sort->appliquer_tri($_SESSION["last_sortreading_list"], $sql, 'notice_id', 0, 0);
			} else {
				$sql = "SELECT notice_id FROM notices WHERE notice_id IN ('" . implode("','",$this->filtered_notices) . "')";
				$sql .= "ORDER BY index_serie, tnvol, index_sew";
			}

			$this->filtered_notices = array();
			$res = pmb_mysql_query($sql);
			if (pmb_mysql_num_rows($res)) {
				while ($r = pmb_mysql_fetch_assoc($res)) {
					$this->filtered_notices[] = $r['notice_id'];
				}
				pmb_mysql_free_result($res);
			}
		}

		$affich = '';
		if (count($this->filtered_notices) <= $pmb_nb_max_tri) {
			$params = rawurlencode(serialize(array(
				'sub' => $sub,
				'id_liste' => $this->id_liste,
			)));
			$affich_tris_result_liste = sort::show_tris_selector("reading_list");
			$affich_tris_result_liste = str_replace('!!page_en_cours!!', urlencode('lvl=show_list').'&params=' . $params . '&id_liste=' . $this->id_liste, $affich_tris_result_liste);
			$affich_tris_result_liste = str_replace('!!page_en_cours1!!', 'lvl=show_list&params=' . $params . '&sub=' . $sub . '&id_liste=' . $this->id_liste, $affich_tris_result_liste);
			$affich.=  $affich_tris_result_liste;
			$affich.= '<script>
				window.addEventListener("search-tab-change", function(event) {
					const tab = event.detail;
					const triSelector = document.getElementById("tri_selector");
					if (!triSelector) {
						return false;
					}

					if (["simple", "docnums"].includes(tab)) {
						triSelector.style.removeProperty("display");
					} else {
						triSelector.style.display = "none";
					}
				})

			</script>';
		}

		if (isset($_GET['sort'])) {
			$affich.=  "<span class='sort'>" . $msg['tri_par'] . ' ' . $sort->descriptionTriParId($_SESSION["last_sortreading_list"]) . '<span class="espaceCartAction">&nbsp;</span></span>';
		}

		return $affich;
	}
	/**
	 * Affiche la barre de navigation des notices
	 */
	public function aff_navigation_notices($notices = array(), $id_liste = 0, $sub = '') {
	    global $opac_search_results_per_page, $page;

	    $count = count($notices);
	    if (empty($count)) {
	        return '';
	    }
	    $id_liste = intval($id_liste);
		$catal_navbar = "<div class='row'>&nbsp;</div>";
	    $url_page = "javascript:document.liste_lecture.page.value=!!page!!;document.liste_lecture.action=\"./index.php?lvl=show_list&sub=$sub&id_liste=$id_liste\";document.liste_lecture.submit()";
	    $nb_per_page_custom_url = "javascript:document.liste_lecture.nb_per_page_custom.value=!!nb_per_page_custom!!";
	    $action = "javascript:document.liste_lecture.page.value=document.form.page.value;document.liste_lecture.action = \"./index.php?lvl=show_list&sub=$sub&id_liste=$id_liste\";document.liste_lecture.submit()";

	    $catal_navbar .= "<div id='navbar_list'>\n<div style='text-align:center'>".printnavbar($page, $count, $opac_search_results_per_page, $url_page, $nb_per_page_custom_url, $action)."</div></div>";
		return $catal_navbar;
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
	 * @param int $id_empr
	 */
	protected function send_subscribe_mail($id_empr) {
		$mail_opac_reader_readinglist_subscribe = new mail_opac_reader_readinglist_subscribe();
		$mail_opac_reader_readinglist_subscribe->set_mail_to_id($id_empr);
		$mail_opac_reader_readinglist_subscribe->set_id_liste($this->id_liste);
		return $mail_opac_reader_readinglist_subscribe->send_mail();
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
	 * @param int $id_empr
	 */
	protected function send_unsubscribe_mail($id_empr) {
		$mail_opac_reader_readinglist_unsubscribe = new mail_opac_reader_readinglist_unsubscribe();
		$mail_opac_reader_readinglist_unsubscribe->set_mail_to_id($id_empr);
		$mail_opac_reader_readinglist_unsubscribe->set_id_liste($this->id_liste);
		return $mail_opac_reader_readinglist_unsubscribe->send_mail();
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
				or (id_liste in (select num_liste from abo_liste_lecture where num_empr = '".$_SESSION['id_empr_session']."' and etat=2) and allow_add_records = 1 and read_only = 0)
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
		global $opac_avis_allow, $allow_avis, $charset;

		$display = "
				<div class='row'>
					<div>
						<label for='text_query'>".$msg['list_lecture_search_in_list']."</label>
						<br />
						<input id='text_query' class='text_query' type='text' size='65' name='user_query' id='user_query' value='".htmlentities(stripslashes($user_query), ENT_QUOTES, $charset)."' title='".htmlentities($msg['autolevel1_search'],ENT_QUOTES, $charset)."'>
					</div>";
		if($opac_avis_allow && $allow_avis) {
			$display .= "<div>
				<input id='avis_search' type='checkbox' value='1' name='avis_search' ".($avis_search ? "checked='checked'" : "").">
				<label for='avis_search'>".$msg['list_lecture_avis_search']."</label>
			</div>";
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

	public static function get_name_from_id($id) {
		$id = intval($id);
		$query = "SELECT nom_liste FROM opac_liste_lecture WHERE id_liste= ".$id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 'nom_liste');
		}
		return '';
	}

	protected function get_display_docnums()
	{
		global $opac_url_base, $opac_visionneuse_allow;

		$iaView = new AiView("ai/sharedDocnumsList", [
			'webservice_url' => $opac_url_base . 'rest.php/aiapi/AiApiSharedList/',
			'shared_list_id' => intval($this->id_liste),
			'visionneuse_allow' => $opac_visionneuse_allow == '1'
		]);
		return $iaView->render();
	}

	protected function make_simple_search()
	{
		global $user_query, $avis_search;

		if($user_query == '' || $user_query == '*') {
		    $filter_results = new filter_results($this->notices);
		    $this->filtered_notices = $filter_results->get_array_results();
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
	 * Permet de faire une recherche semantique sur la liste de lecture.
	 * La cherche ne prend pas en compte les tris
	 */
	protected function make_ai_search()
	{
		global $user_query;

		AiSessionSemanticModel::rec_history(AiSessionSemanticModel::TYPE_SHARED_LIST, $this->id_liste);

		// Comment on fais egalement une recherche sur les documents perso de la liste de lecture.
		// On ne peut pas prendre en compte les tris. Donc on le fait seulement sur la pertinance.

		$searcher = new SearcherSharedList(stripslashes($user_query), $this->id_liste);
		$this->filtered_notices = array_filter(explode(',', $searcher->get_result()), function ($value) {
			return !empty($value);
		});
	}

	/**
	 * Permet d'afficher le document personnel de la liste de lecture
	 *
	 * @param string $docnum Exemple docnum_1
	 * @return string
	 */
	protected function aff_docnum(string $docnum) {
		// On recupere l'identifiant du docnum en supprimant "docnum_" de la chaine.
		$id = substr($docnum, 7);
		$id = intval($id);

		if (0 === $id || !AiSharedListDocnumOrm::exist($id)) {
			return '';
		}

		$aiSharedListDocnumOrm = new AiSharedListDocnumOrm($id);


		global $base_path;
        $template_path = $base_path . '/includes/templates/list/docnum_in_result_display_subst.tpl.html';
        if (!is_file($template_path)) {
			$template_path = $base_path . '/includes/templates/list/docnum_in_result_display.tpl.html';
        }

        $h2o = \H2o_collection::get_instance($template_path);
		return $h2o->render([
			'docnum' => [
				'id' => $id,
				'name' => $aiSharedListDocnumOrm->name_ai_shared_list_docnum,
				'url' => $aiSharedListDocnumOrm->getUrl(),
			]
		]);
	}

	/**
	 * Permet d'afficher le docnum
	 *
	 * @param integer $id
	 * @return false|void Retourne false si le docnum n'existe pas
	 */
	public function show_docnum(int $id) {
		if (0 === $id || !AiSharedListDocnumOrm::exist($id)) {
			return false;
		}

		$aiSharedListDocnumOrm = new AiSharedListDocnumOrm($id);

		$path = $aiSharedListDocnumOrm->getPath();
		if (is_file($path)) {
			header('Content-Disposition:	inline; filename="' . $aiSharedListDocnumOrm->name_ai_shared_list_docnum . '"');
			header('Content-Type: ' . $aiSharedListDocnumOrm->mimetype_ai_shared_list_docnum);
			print file_get_contents($path);
		} else {
			return false;
		}
	}
}