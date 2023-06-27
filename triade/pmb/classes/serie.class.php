<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serie.class.php,v 1.85 2018-12-04 10:26:44 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'titres de séries'
if ( ! defined( 'SERIE_CLASS' ) ) {
  define( 'SERIE_CLASS', 1 );

require_once($class_path."/notice.class.php");
require_once("$class_path/aut_link.class.php");
require_once("$class_path/aut_pperso.class.php");
require_once("$class_path/audit.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path.'/authorities_statuts.class.php');
require_once($class_path."/indexation_authority.class.php");
require_once($class_path."/authority.class.php");
require_once ($class_path.'/indexations_collection.class.php');
require_once ($class_path.'/indexation_stack.class.php');

class serie {

	// ---------------------------------------------------------------
	//	propriétés de la classe
	// ---------------------------------------------------------------
	public $s_id=0;			// MySQL s_id in table 'series'
	public	$name='';			// nom de la série
	public	$index='';			// forme pour l'index
	public $isbd_entry_lien_gestion ; // lien sur le nom vers la gestion
	public $num_statut = 1; //Statut
	public $cp_error_message = '';
	protected static $controller;
	
	// ---------------------------------------------------------------
	//		série($s_id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->s_id = $id+0;
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos du titre
	// ---------------------------------------------------------------
	public function getData() {
		$this->name			=	'';
		$this->index			=	'';
		$this->num_statut = 1;
		if($this->s_id) {
			$requete = "SELECT * FROM series WHERE serie_id='".$this->s_id."' " ;
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->s_id = $row->serie_id;
				$this->name = $row->serie_name;
				$this->index = $row->serie_index;
				$authority = new authority(0, $this->s_id, AUT_TABLE_SERIES);
				$this->num_statut = $authority->get_num_statut();
				// Ajoute un lien sur la fiche série si l'utilisateur à accès aux autorités
				if (SESSrights & AUTORITES_AUTH){ 
				    $this->isbd_entry_lien_gestion = "<a href='./autorites.php?categ=see&sub=serie&id=".$this->s_id."' class='lien_gestion'>".$this->name."</a>";
				}else{
				    $this->isbd_entry_lien_gestion = $this->name;
				}
			}
		}
	}
	
	
	public function build_header_to_export() {
	    global $msg;
	    
	    $data = array(
	        $msg[67],
	        $msg[4019],
	    );
	    return $data;
	}
	
	public function build_data_to_export() {
	    $data = array(
	        $this->name,
	        $this->num_statut,
	    );
	    return $data;
	}
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form($duplicate = false) {
	
		global $msg;
		global $charset;
		global $serie_form;
		global $pmb_type_audit;
		global $thesaurus_concepts_active;
	
		if($this->s_id && !$duplicate) {
			$action = static::format_url("&sub=update&id=".$this->s_id);
			$libelle = $msg[337];
			$button_remplace = "<input type='button' class='bouton' value='$msg[158]' ";
			$button_remplace .= "onclick='unload_off();document.location=\"".static::format_url("&sub=replace&id=".$this->s_id)."\"'>";
			$button_delete = "<input type='button' class='bouton' value='$msg[63]' ";
			$button_delete .= "onClick=\"confirm_delete();\">";
			$button_voir = "<input type='button' class='bouton' value='$msg[voir_notices_assoc]' ";
			$button_voir .= "onclick='unload_off();document.location=\"./catalog.php?categ=search&mode=10&etat=aut_search&aut_type=tit_serie&aut_id=$this->s_id\"'>";
		} else {
			$action = static::format_url('&sub=update&id=');
			$libelle = $msg[336];
			$button_remplace = '';
			$button_delete ='';
			$button_voir="" ;
		}
		$aut_link= new aut_link(AUT_TABLE_SERIES,$this->s_id);
		$serie_form = str_replace('<!-- aut_link -->', $aut_link->get_form('saisie_serie') , $serie_form);
		
		$aut_pperso= new aut_pperso("serie",$this->s_id);
		$serie_form = str_replace('!!aut_pperso!!',	$aut_pperso->get_form(), $serie_form);
		/**
		 * Gestion du selecteur de statut d'autorité
		 */
		$serie_form = str_replace('!!auth_statut_selector!!', authorities_statuts::get_form_for(AUT_TABLE_SERIES, $this->num_statut), $serie_form);
		
		$serie_form = str_replace('!!id!!', $this->s_id, $serie_form);
		$serie_form = str_replace('!!libelle!!', $libelle, $serie_form);
		$serie_form = str_replace('!!action!!', $action, $serie_form);
		$serie_form = str_replace('!!cancel_action!!', static::format_back_url(), $serie_form);
		$serie_form = str_replace('!!id!!', $this->s_id, $serie_form);
		$serie_form = str_replace('!!serie_nom!!', htmlentities($this->name,ENT_QUOTES, $charset), $serie_form);
		$serie_form = str_replace('!!remplace!!', $button_remplace,  $serie_form);
		$serie_form = str_replace('!!voir_notices!!', $button_voir,  $serie_form);
		$serie_form = str_replace('!!delete!!', $button_delete,  $serie_form);
		$serie_form = str_replace('!!delete_action!!', static::format_delete_url("&id=".$this->s_id), $serie_form);
		// pour retour à la bonne page en gestion d'autorités
		// &user_input=".rawurlencode(stripslashes($user_input))."&nbr_lignes=$nbr_lignes&page=$page
		global $user_input, $nbr_lignes, $page ;
		$serie_form = str_replace('!!user_input!!',			htmlentities($user_input,ENT_QUOTES, $charset),		$serie_form);
		$serie_form = str_replace('!!nbr_lignes!!',			$nbr_lignes,										$serie_form);
		$serie_form = str_replace('!!page!!',				$page,												$serie_form);
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->s_id, TYPE_SERIE);
			$serie_form = str_replace('!!concept_form!!',	$index_concept->get_form('saisie_serie'),			$serie_form);
		}else{
			$serie_form = str_replace('!!concept_form!!',	"",			$serie_form);
		}
		if ($this->name) {
			$serie_form = str_replace('!!document_title!!', addslashes($this->name.' - '.$libelle), $serie_form);
		} else {
			$serie_form = str_replace('!!document_title!!', addslashes($libelle), $serie_form);
		}
		$authority = new authority(0, $this->s_id, AUT_TABLE_SERIES);
		$serie_form = str_replace('!!thumbnail_url_form!!', thumbnail::get_form('authority', $authority->get_thumbnail_url()), $serie_form);
		if ($pmb_type_audit && $this->s_id && !$duplicate) {
			$bouton_audit= audit::get_dialog_button($this->s_id, AUDIT_SERIE);
		} else {
			$bouton_audit= "";
		}
		$serie_form = str_replace('!!audit_bt!!',				$bouton_audit,												$serie_form);
		$serie_form = str_replace('!!controller_url_base!!', static::format_url(), $serie_form);
		
		print $serie_form;
	}
	
	// ---------------------------------------------------------------
	//		replace_form : affichage du formulaire de remplacement
	// ---------------------------------------------------------------
	public function replace_form() {
		global $serie_replace;
		global $msg;
		global $include_path;
		
		if(!$this->s_id || !$this->name) {
			require_once("$include_path/user_error.inc.php");
			error_message($msg[161], $msg[162], 1, static::format_url('&sub=&id='));
			return false;
			}
	
		$serie_replace=str_replace('!!id!!', $this->s_id, $serie_replace);
		$serie_replace=str_replace('!!serie_name!!', $this->name, $serie_replace);
		$serie_replace=str_replace('!!controller_url_base!!', static::format_url(), $serie_replace);
		$serie_replace=str_replace('!!cancel_action!!', static::format_back_url(), $serie_replace);
		print $serie_replace;
	}
	
	// ---------------------------------------------------------------
	//		delete() : suppression du titre de série
	// ---------------------------------------------------------------
	public function delete() {
		global $dbh;
		global $msg;
		
		if(!$this->s_id)
			// impossible d'accéder à cette notice de titre de série
			return $msg[409];
	
		if(($usage=aut_pperso::delete_pperso(AUT_TABLE_SERIES, $this->s_id,0) )){
			// Cette autorité est utilisée dans des champs perso, impossible de supprimer
			return '<strong>'.$this->display.'</strong><br />'.$msg['autority_delete_error'].'<br /><br />'.$usage['display'];
		}
		// récupération du nombre de notices affectées
		$requete = "SELECT COUNT(1) AS qte FROM notices WHERE tparent_id=".$this->s_id;
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = pmb_mysql_result($res, 0, 0);
	
		if(!$nbr_lignes) {
	
			// On regarde si l'autorité est utilisée dans des vedettes composées
			$attached_vedettes = vedette_composee::get_vedettes_built_with_element($this->s_id, TYPE_SERIE);
			if (count($attached_vedettes)) {
				// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
				return '<strong>'.$this->name."</strong><br />".$msg["vedette_dont_del_autority"].'<br/>'.vedette_composee::get_vedettes_display($attached_vedettes);
			}
			
			// titre de série non-utilisé dans des notices : Suppression OK
			// effacement dans la table des titres de série
			$requete = "DELETE FROM series WHERE serie_id=".$this->s_id;
			$result = pmb_mysql_query($requete, $dbh);
			// liens entre autorités
			$aut_link= new aut_link(AUT_TABLE_SERIES,$this->s_id);
			$aut_link->delete();		
			$aut_pperso= new aut_pperso("serie",$this->s_id);
			$aut_pperso->delete();
			
			// nettoyage indexation concepts
			$index_concept = new index_concept($this->s_id, TYPE_SERIE);
			$index_concept->delete();
			
			// nettoyage indexation
			indexation_authority::delete_all_index($this->s_id, "authorities", "id_authority", AUT_TABLE_SERIES);
			
			// effacement de l'identifiant unique d'autorité
			$authority = new authority(0, $this->s_id, AUT_TABLE_SERIES);
			$authority->delete();
			
			audit::delete_audit(AUDIT_SERIE,$this->s_id);
			return false;
			} else {
				// Ce titre de série est utilisé dans des notices, impossible de le supprimer
				return '<strong>'.$this->name."</strong><br />${msg[410]}";
			}
		}
	
	// ---------------------------------------------------------------
	//		replace($by) : remplacement du titre
	// ---------------------------------------------------------------
	public function replace($by,$link_save=0) {
	
		// à compléter
		global $msg;
		global $dbh;
	
		if(!$by) {
			// pas de valeur de remplacement !!!
			return "serious error occured, please contact admin...";
		}
		if (($this->s_id == $by) || (!$this->s_id))  {
			// impossible de remplacer une autorité par elle-même
			return $msg[411];
		}
		
		$aut_link= new aut_link(AUT_TABLE_SERIES,$this->s_id);
		// "Conserver les liens entre autorités" est demandé
		if($link_save) {
			// liens entre autorités
			$aut_link->add_link_to(AUT_TABLE_SERIES,$by);		
		}
		$aut_link->delete();
		
		// a) remplacement dans les notices
		$requete = "UPDATE notices SET tparent_id=$by WHERE tparent_id=".$this->s_id;
		$res = pmb_mysql_query($requete, $dbh);
		
		$rqt_notice="select notice_id,tit1,tit2,tit3,tit4 from notices where tparent_id=".$by;
		$r_notice=pmb_mysql_query($rqt_notice);
		while ($r=pmb_mysql_fetch_object($r_notice)) {
			$rq_serie="update notices, series set notices.index_serie=serie_index, notices.index_wew=concat(serie_name,' ',tit1,' ',tit2,' ',tit3,' ',tit4),notices.index_sew=concat(' ',serie_index,' ','".addslashes(strip_empty_words($r->tit1." ".$r->tit2." ".$r->tit3." ".$r->tit4))."',' ') where notice_id=".$r->notice_id." and serie_id=tparent_id";
			pmb_mysql_query($rq_serie);
			}
		
		// b) suppression du titre de série à remplacer
		$requete = "DELETE FROM series WHERE serie_id=".$this->s_id;
		$res = pmb_mysql_query($requete, $dbh);
		
		//Remplacement dans les champs persos sélecteur d'autorité
		aut_pperso::replace_pperso(AUT_TABLE_SERIES, $this->s_id, $by);
		
		audit::delete_audit (AUDIT_SERIE, $this->s_id);
			
			// nettoyage indexation
			indexation_authority::delete_all_index($this->s_id, "authorities", "id_authority", AUT_TABLE_SERIES);
			
			// effacement de l'identifiant unique d'autorité
			$authority = new authority(0, $this->s_id, AUT_TABLE_SERIES);
			$authority->delete();
			
		serie::update_index($by);
	
		return FALSE;
		}
	
	// ---------------------------------------------------------------
	//		update($value) : mise à jour du titre de série
	// ---------------------------------------------------------------
	public function update($value) {
	
		global $dbh;
		global $msg;
		global $include_path;
		global $thesaurus_concepts_active;
		global $authority_statut;
		global $authority_thumbnail_url;
		
		if(!$value)
			return false;
	
		// nettoyage de la chaîne en entrée
		$value = clean_string($value);
	
		$requete = 'SET serie_name="'.$value.'", ';
			$requete .= 'serie_index=" '.strip_empty_words($value).' "';
	
		if($this->s_id) {
			// update
			$requete = 'UPDATE series '.$requete;
			$requete .= ' WHERE serie_id='.$this->s_id.' LIMIT 1;';
			if(pmb_mysql_query($requete, $dbh)) {
				$rqt_notice="select notice_id,tit1,tit2,tit3,tit4 from notices where tparent_id=".$this->s_id;
				$r_notice=pmb_mysql_query($rqt_notice);
				while ($r=pmb_mysql_fetch_object($r_notice)) {
					$rq_serie="update notices, series set notices.index_serie=serie_index, notices.index_wew=concat(serie_name,' ',tit1,' ',tit2,' ',tit3,' ',tit4),notices.index_sew=concat(' ',serie_index,' ','".addslashes(strip_empty_words($r->tit1." ".$r->tit2." ".$r->tit3." ".$r->tit4))."',' ') where notice_id=".$r->notice_id." and serie_id=tparent_id";
					pmb_mysql_query($rq_serie);
				}
				
				audit::insert_modif (AUDIT_SERIE, $this->s_id) ;
				
				$aut_link= new aut_link(AUT_TABLE_SERIES,$this->s_id);
				$aut_link->save_form();
				$aut_pperso= new aut_pperso("serie",$this->s_id);
				if($aut_pperso->save_form()){
					$this->cp_error_message = $aut_pperso->error_message;
					return false;
				}
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg[337], $msg[341]);
				return FALSE;
			}
		} else {
			// création : s'assurer que le titre n'existe pas déjà
			$dummy = "SELECT * FROM series WHERE serie_name REGEXP '^$value$' LIMIT 1 ";
			$check = pmb_mysql_query($dummy, $dbh);
			if(pmb_mysql_num_rows($check)) {
				require_once("$include_path/user_error.inc.php");
				warning($msg[336], $msg[340]);
				return FALSE;
			}
			$requete = 'INSERT INTO series '.$requete.';';
			if(pmb_mysql_query($requete, $dbh)) {
				$this->s_id=pmb_mysql_insert_id();
	
				audit::insert_creation(AUDIT_SERIE, $this->s_id) ;
				
				$aut_link= new aut_link(AUT_TABLE_SERIES,$this->s_id);
				$aut_link->save_form();			
				$aut_pperso= new aut_pperso("serie",$this->s_id);
				if($aut_pperso->save_form()){
					$this->cp_error_message = $aut_pperso->error_message;
					return false;
				}
			} else {
				require_once("$include_path/user_error.inc.php");
				warning($msg[336], $msg[342]);
				return FALSE;
			}
		}
			//update authority informations
			$authority = new authority(0, $this->s_id, AUT_TABLE_SERIES);
			$authority->set_num_statut($authority_statut);
			$authority->set_thumbnail_url($authority_thumbnail_url);
			$authority->update();
			
		// Indexation concepts
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($this->s_id, TYPE_SERIE);
			$index_concept->save();
		}
			
		// Mise à jour des vedettes composées contenant cette autorité
		vedette_composee::update_vedettes_built_with_element($this->s_id, TYPE_SERIE);
		
		serie::update_index($this->s_id);
		
		return TRUE;
	}
	
	// 	---------------------------------------------------------------
	// 			import() : import d'un titre de série
	// 	---------------------------------------------------------------
	// 	fonction d'import de notice auteur (membre de la classe 'author');
	public static function import($title, $statut=1, $thumbnail_url='') {
	
		global $dbh;
	
		// check sur la variable passée en paramètre
		if(!$title) {
			return 0;
		}
	
		// tentative de récupérer l'id associée dans la base (implique que l'autorité existe)
		// préparation de la requête
		$key = addslashes($title);
	
		$query = "SELECT serie_id FROM series WHERE serie_name='".rtrim(substr($key,0,255))."' LIMIT 1 ";
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't SELECT series ".$query);
		// résultat
	
		// récupération du résultat de la recherche
		$tserie  = pmb_mysql_fetch_object($result);
		// du résultat et récupération éventuelle de l'id
		if($tserie->serie_id)
			return $tserie->serie_id;
	
		// id non-récupérée, il faut créer la forme.
		$index = addslashes(strip_empty_words($title));
		
			$query = 'INSERT INTO series SET serie_name="'.$key.'", serie_index=" '.$index.' "';
	
		$result = @pmb_mysql_query($query, $dbh);
		if(!$result) die("can't INSERT into series".$query);
		
		$id=pmb_mysql_insert_id($dbh);
		audit::insert_creation (AUDIT_SERIE, $id) ;
			
			//update authority informations
			$authority = new authority(0, $id, AUT_TABLE_SERIES);
			$authority->set_num_statut($statut);
			$authority->set_thumbnail_url($thumbnail_url);
			$authority->update();
			
			serie::update_index($id);
		return $id;
	}
	
	// ---------------------------------------------------------------
	//		search_form() : affichage du form de recherche
	// ---------------------------------------------------------------
	public static function search_form() {
		global $user_query, $user_input;
		global $msg, $charset;
		global $authority_statut;
	
		$user_query = str_replace ('!!user_query_title!!', $msg[357]." : ".$msg[333] , $user_query);
		$user_query = str_replace ('!!action!!', static::format_url('&sub=reach&id='), $user_query);
		$user_query = str_replace ('!!add_auth_msg!!', $msg[339] , $user_query);
		$user_query = str_replace ('!!add_auth_act!!', static::format_url('&sub=serie_form'), $user_query);
		$user_query = str_replace ('<!-- lien_derniers -->', "<a href='".static::format_url('&sub=serie_last')."'>$msg[1314]</a>", $user_query);
		$user_query = str_replace('<!-- sel_authority_statuts -->', authorities_statuts::get_form_for(AUT_TABLE_SERIES, $authority_statut, true), $user_query);
		$user_query = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);
		print pmb_bidi($user_query) ;
// 		print "<br />
// 			<input class='bouton' type='button' value='$msg[339]' onClick=\"document.location='./autorites.php?categ=series&sub=serie_form'\" />
// 			";
	}
	
	//---------------------------------------------------------------
		// update_index($id) : maj des index
	//---------------------------------------------------------------
	public static function update_index($id, $datatype = 'all') {
		indexation_stack::push($id, TYPE_SERIE, $datatype);
		
		// On cherche tous les n-uplet de la table notice correspondant à cette série.
		$query = "select distinct(notice_id) from notices where tparent_id='".$id."'";	
		authority::update_records_index($query, 'serie');
	}
	
	public function get_header() {
		return $this->name;
	}
	
	public function get_cp_error_message(){
		return $this->cp_error_message;
	}
	
	public function get_gestion_link(){
		return './autorites.php?categ=see&sub=serie&id='.$this->s_id;
	}
	
	public function get_isbd() {
		return $this->name;
	}
	
	public static function get_format_data_structure($antiloop = false) {
		global $msg;
	
		$main_fields = array();
		$main_fields[] = array(
				'var' => "name",
				'desc' => $msg['233']
		);
		$authority = new authority(0, 0, AUT_TABLE_SERIES);
		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
	}
	
	public function format_datas($antiloop = false){
		$formatted_data = array(
				'name' => $this->name
		);
		$authority = new authority(0, $this->s_id, AUT_TABLE_SERIES);
		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	public static function set_controller($controller) {
		static::$controller = $controller;
	}
	
	protected static function format_url($url='') {
		global $base_path;
		
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_url_base().$url;
		} else {
			return $base_path.'/autorites.php?categ=series'.$url;
		}
	}
	
	protected static function format_back_url() {
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_back_url();
		} else {
			return "history.go(-1)";
		}
	}
	
	protected static function format_delete_url($url='') {
		global $base_path;
			
		if(isset(static::$controller) && is_object(static::$controller)) {
			return 	static::$controller->get_delete_url();
		} else {
			return static::format_url("&sub=delete".$url);
		}
	}
} # fin de définition de la classe serie

} # fin de délaration

