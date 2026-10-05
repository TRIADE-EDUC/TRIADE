<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_display.class.php,v 1.245.2.6.2.1 2025/01/31 09:50:05 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/record_display.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/marc_table.class.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/collstate.class.php");
require_once($include_path."/notice_authors.inc.php");
require_once($include_path."/notice_categories.inc.php");
require_once($include_path."/explnum.inc.php");
require_once("$class_path/authperso_notice.class.php");
require_once("$class_path/map/map_objects_controler.class.php");
require_once("$class_path/map_info.class.php");
require_once($class_path."/tu_notice.class.php");
require_once ($class_path."/map/map_locations_controler.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path."/thumbnail.class.php");

// récupération des codes de fonction
global $fonction_auteur;
if (!isset($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

// propriétés pour le selecteur de panier (kinda template)
$cart_click = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=!!item!!&unq=!!unique!!', 'cart')\"";

// définition de la classe d'affichage des périodiques
class serial_display extends record_display {
	public $bul_id	= 0 ;					// id de bulletin récupéré pour l'ISBD
  	public $action_serial_org = '';		// lien à activer si la notice est s1 (notice chapeau)
  	public $action_analysis_org = '';		// lien à activer si la notice est a2 (dépouillment)
	public $action_serial = '';			// lien modifié pour le header
	public $action_analysis = '';			// lien modifié pour le header (nécessite !!bul_id!!)
	public $action_bulletin = '';			// action pour la notion de bulletin
	public $parent_id = 0;					// id de la notice parent
	public $parent_title = '';				// titre de la notice parent si a2
	public $parent_numero = '';			// mention de numérotation dans le bulletinage associé
	public $parent_date = '';				// mention de date (txt) dans le bulletinage associé
	public $parent_date_date = '';			// mention de date (date) dans le bulletinage associé
	public $parent_aff_date_date = '';		// mention de date (date) dans le bulletinage associé au format correct pour affichage
	public $lien_explnum = '';				// Lien de gestion des documents numériques associés
	public $bouton_explnum = 0 ;			// bouton ou pas d'ajout de doc numérique

	public $drag = 0;
	public $no_link = false;
	public $serial_nb_bulletins = 0;
	public $serial_nb_exemplaires = 0;
	public $serial_nb_articles = 0;
	public $serial_nb_etats_collection = 0;
	public $serial_nb_abo_actif = 0;
	public $show_abo_actif = 0;
	public $icon_abo_actif = '';
	public $ajax_mode = '';
	public $lien_suppr_cart = '';
	public $memo_titre = '';
	public $memo_complement_titre = '';
	public $memo_titre_parallele = '';
	public $mono_display_cmd = '';
	public $code = '';
	public $aff_date_date;
	public $isbn;
	public $show_avis=1;

	// constructeur
	public function __construct(	$id,						// $id = id de la notice à afficher
								$level='1', 				// $level :
															// 0 : juste le header (titre  / auteur principal avec le lien si applicable)
															// 	6 : cas général détaillé avec notes, categ, langues, indexation... + boutons
								$action_serial='', 			// $action_serial = URL à atteindre si la notice est une notice chapeau
								$action_analysis='', 		// $action_analysis = URL à atteindre si la notice est un dépouillement
															// note dans ces deux variable, '!!id!!' sera remplacé par l'id de cette notice
															// les deux liens s'excluent mutuellement, bien sur.
								$action_bulletin='',
								$lien_suppr_cart="", 		// $lien_suppr_cart = lien de suppression de la notice d'un caddie
								$lien_explnum="",
								$bouton_explnum=1,
								$print=0,					// $print = 0 affichage normal
															//			1 affichage impression sans liens
															//			2 affichage impression avec liens sur documents numeriques
															// 			4 affichage email : sans lien sauf url associée

								$show_explnum=1,
								$show_statut=0,
								$show_opac_hidden_fields=true,
								$draggable=0,
								$ajax_mode=0 ,
								$anti_loop = array(),
								$no_link=false,
								$show_map=1,                // $show_map = affichage de la map
								$context_dsi_id_bannette=0,  // $context_dsi_id_bannette = dans le contexte de la dsi
								$show_abo_actif = 0,
								$expl=0,						// $expl -> affiche ou non les exemplaires associé
								$context_parameters = array(),	// Elements de contexte (ex : in_search, in_selector)
                                $show_avis=1
								) {

		global $pmb_recherche_ajax_mode;
		
		if (!is_array($anti_loop)) {
			$anti_loop = array();
		}
		
		$this->show_map=$show_map;
		$this->context_dsi_id_bannette=$context_dsi_id_bannette;
		$this->context_parameters=$context_parameters;
		
	  	if($pmb_recherche_ajax_mode){
			$this->ajax_mode=$ajax_mode;
		  	if($this->ajax_mode) {
		  		$param=array();
				if (is_object($id)){
					$param['id']=$id->notice_id;
				} else {
					$param['id']=$id;
				}
				$param['function_to_call']="serial_display";
			  	//if($level)$param['level']=$level;	//6
				if($action_serial)$param['action_serial']=$action_serial;
				if($action_analysis)$param['action_analysis']=$action_analysis;
				if($action_bulletin)$param['action_bulletin']=$action_bulletin;
//			  	if($lien_suppr_cart)$param['lien_suppr_cart']=$lien_suppr_cart;
 			  	if($lien_explnum)$param['lien_explnum']=$lien_explnum;
				if($bouton_explnum)$param['bouton_explnum']=$bouton_explnum;
			  	if($print)$param['print']=$print;
			  //	if($show_explnum)$param['show_explnum']=$show_explnum;
			  	//if($show_statut)$param['show_statut']=$show_statut;
			  	//if($show_opac_hidden_fields)$param['show_opac_hidden_fields']=$show_opac_hidden_fields;
			  	//if($draggable)$param['draggable']=$draggable;//1

			  	$this->mono_display_cmd=serialize($param);
		  	}
	  	}
		$this->lien_explnum = $lien_explnum ;
		$this->bouton_explnum = $bouton_explnum ;
		$this->print_mode=$print;
		$this->show_explnum=$show_explnum;
		$this->show_statut=$show_statut;
		$this->show_abo_actif=$show_abo_actif;
		$this->expl=$expl;
		$this->anti_loop=$anti_loop;
		$this->no_link=$no_link;
		$this->show_avis=$show_avis;
		if(!$id) return; else {
			if (is_object($id)){
				$this->notice_id = $id->notice_id;
				$this->notice = $id;
				$this->langues	= get_notice_langues($this->notice_id, 0) ;	// langues de la publication
				$this->languesorg	= get_notice_langues($this->notice_id, 1) ; // langues originales
				$this->isbn = $id->code ;
				//Récupération titre de série
				if($id->tparent_id) {
					$parent = new serie($id->tparent_id);
					$this->tit_serie = $parent->name;
					$this->tit_serie_lien_gestion = $parent->isbd_entry_lien_gestion;
				}
			} else {
				$this->notice_id = $id;
				$this->fetch_data();
			}
		}

		$this->show_opac_hidden_fields=$show_opac_hidden_fields;
		if(!$this->ajax_mode)$this->p_perso=new parametres_perso("notices");

		$this->responsabilites = get_notice_authors($this->notice_id) ;

		// mise à jour des catégories
		if(!$this->ajax_mode)$this->categories = get_notice_categories($this->notice_id) ;

		$this->level = $level;
		$this->lien_suppr_cart = $lien_suppr_cart;

		// si la notice est a2 (dépouillement), on récupère les données du bulletinage
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) {
			$this->get_bul_info();
		}

		// mise à jour des liens
		if (SESSrights & CATALOGAGE_AUTH){
			$this->action_serial_org = $action_serial;
			$this->action_analysis = $action_analysis;
			$this->action_bulletin = $action_bulletin;
			if ($action_serial && $this->notice->niveau_biblio == 's' && $this->notice->niveau_hierar == '1')
				$this->action_serial = str_replace('!!id!!', $this->notice_id, $action_serial);
			if ($action_analysis && $this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == '2') {
				$this->action_analysis = str_replace('!!id!!', $this->notice_id, $this->action_analysis);
				$this->action_analysis = str_replace('!!bul_id!!', $this->bul_id, $this->action_analysis);
				}
			$this->lien_explnum = str_replace('!!serial_id!!', $this->notice_id ?? "", $this->lien_explnum ?? "");
			$this->lien_explnum = str_replace('!!analysis_id!!', $this->notice_id ?? "", $this->lien_explnum ?? "");
			$this->lien_explnum = str_replace('!!bul_id!!', $this->bul_id ?? "", $this->lien_explnum ?? "");
			$this->drag=$draggable;
		}else{
			$this->action_serial_org = "";
			$this->action_analysis = "";
			$this->action_bulletin = "";
			$this->action_serial = "";
			$this->lien_explnum = "";
			$this->drag="";
		}

		$this->do_header();

		if($level) {
		    $this->init_javascript();
		}
		$this->isbd = 'ISBD';

		$this->notice_relations = notice_relations_collection::get_object_instance($this->notice_id);
		if(!$this->ajax_mode) {
			$this->childs = $this->notice_relations->get_childs();
		}

		switch($level) {
			case 0:
				// là, c'est le niveau 0 : juste le header
				//$this->do_header();
				$this->result = $this->header;
				break;
			default:
				global $pmb_map_activate;
				$this->map=array();
				if($pmb_map_activate){
					$ids=array();
					$ids[]=$this->notice_id;
					$this->map=new map_objects_controler(TYPE_RECORD,$ids);
					$this->map_info=new map_info($this->notice_id);
				}
				// niveau 1 et plus : header + isbd à générer
				//$this->do_header();
				if(!$this->ajax_mode) $this->do_isbd();
				if(!$this->ajax_mode) $this->finalize();
				break;
		}
		return;
	}

	// récupération des info de bulletinage (si applicable)
	public function get_bul_info() {
		global $msg ;

		// récupération des données du bulletin et de la notice apparentée
		$requete = "SELECT b.tit1,b.notice_id,b.code,a.*,c.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date ";
		$requete .= "from analysis a, notices b, bulletins c";
		$requete .= " WHERE a.analysis_notice=".$this->notice_id;
		$requete .= " AND c.bulletin_id=a.analysis_bulletin";
		$requete .= " AND c.bulletin_notice=b.notice_id";
		$requete .= " LIMIT 1";
		$myQuery = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($myQuery)) {
			$parent = pmb_mysql_fetch_object($myQuery);
			$this->parent_title = $parent->tit1;
			$this->parent_id = $parent->notice_id;
			$this->code=$parent->code;
			$this->bul_id = $parent->bulletin_id;
			$this->parent_numero = $parent->bulletin_numero;
			$this->parent_date = $parent->mention_date;
			$this->parent_date_date = $parent->date_date;
			$this->parent_aff_date_date = $parent->aff_date_date;
		}
	}

	// finalisation du résultat (écriture de l'isbd)
	public function finalize() {
		global $msg;

		// Différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$this->result = str_replace('!!serial_type!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>", $this->result);
		} else {
			$this->result = str_replace('!!serial_type!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>", $this->result);
            if ($this->notice->niveau_biblio =='a' && !$this->print_mode) {
                if ($this->show_avis) {
                    global $avis_quoifaire,$valid_id_avis;
                    $this->isbd = str_replace('<!-- !!avis_notice!! -->', avis_notice($this->notice_id,$avis_quoifaire,$valid_id_avis), $this->isbd);
                } else {
                    $this->isbd = str_replace('<!-- !!avis_notice!! -->', '', $this->isbd);
                }
                $this->isbd = str_replace('<!-- !!caddies_notice!! -->', caddie_controller::get_display_list_from_item('display', 'NOTI', $this->notice_id), $this->isbd);
                if(explnum::get_default_upload_directory()){
                    $this->isbd = str_replace('<!-- !!explnum_drop_zone!! -->', explnum::get_drop_zone($this->notice_id, 'article', $this->bul_id), $this->isbd);
                }
            }
		}
		parent::finalize();
	}

	protected function get_display_open_tag() {
		global $art_to_show;
		
		$display = '';
		if($art_to_show && ($art_to_show == $this->notice_id)){
			$display .= "startOpen=\"Yes\"";
			if (!empty($this->ajax_mode)) {
    			$display .= " data-expand-ajax=\"1\"";
			}
		}
		return $display;
	}
	
	protected function get_display_anchor() {
		global $art_to_show;
	
		$display = '';
		if($art_to_show && ($art_to_show == $this->notice_id)){
			$display .= "<a name='anchor_$art_to_show'></a>";
		}
		return $display;
	}
	
	// génération de l'isbd
	public function do_isbd() {
		global $msg, $base_path;
		global $tdoc;
		global $charset;
		global $pmb_show_notice_id,$pmb_opac_url,$pmb_show_permalink;
		global $thesaurus_concepts_active;
		global $pmb_map_activate;
		global $pmb_scan_request_activate;
		global $pmb_type_audit;
		global $pmb_show_exemplaires_analysis;
		
		$this->isbd = htmlentities($this->notice->tit1, ENT_QUOTES, $charset);

		// constitution de la mention de titre
		$tit3 = htmlentities($this->notice->tit3 ?? "", ENT_QUOTES, $charset);
		$tit4 = htmlentities($this->notice->tit4 ?? "", ENT_QUOTES, $charset);
		if($tit3) $this->isbd .= "&nbsp;= $tit3";
		if($tit4) $this->isbd .= "&nbsp;: $tit4";
		$this->isbd .= (!empty($tdoc->table[$this->notice->typdoc]) ? ' ['.$tdoc->table[$this->notice->typdoc].']' : '');
	
		// constitution de la mention de responsabilité
		if($libelle_mention_resp = gen_authors_isbd($this->responsabilites, $this->print_mode)) {
			$this->isbd .= "&nbsp;/ ". $libelle_mention_resp ." " ;
		}

		if($pmb_map_activate){
			if($mapisbd=$this->map_info->get_isbd())	$this->isbd .=$mapisbd;
		}
		// zone de l'adresse (ne concerne que s1)
		if ($this->notice->niveau_biblio == 's' && $this->notice->niveau_hierar == 1) {
			$editeurs = "";
			if($this->notice->ed1_id) {
				$editeur = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $this->notice->ed1_id);
				if ($this->print_mode) {
					$editeurs .= $editeur->get_isbd();
				} else {
					$editeurs .= $editeur->isbd_entry_lien_gestion;
				}
			}
			if($this->notice->ed2_id) {
				$editeur = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $this->notice->ed2_id);
				if ($this->print_mode) {
					$ed_isbd=$editeur->get_isbd();
				} else {
					$ed_isbd=$editeur->isbd_entry_lien_gestion;
				}
				if($editeurs) {
					$editeurs .= '&nbsp;; '.$ed_isbd;
				} else {
					$editeurs .= $ed_isbd;
				}
			}

			if($this->notice->year) {
			    if ($editeurs) {
			        $editeurs .= ', '.$this->notice->year;
			    } else {
			        $editeurs = $this->notice->year;
			    }
			}

			if($editeurs) {
				$this->isbd .= ".&nbsp;-&nbsp;$editeurs";
			}
		}

		// zone de la collation (ne concerne que a2, mention de pagination)
		// pour les périodiques, on rebascule en zone de note
		// avec la mention du périodique parent
		$mention_parent = "";
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) {

			$bulletin = htmlentities($this->parent_title, ENT_QUOTES, $charset);
			if($this->parent_numero) {
				$bulletin .= ' '.$this->parent_numero;
			}
			// affichage de la mention de date utile : mention_date si existe, sinon date_date
			if ($this->parent_date)
				$date_affichee = " (".$this->parent_date.")";
			else if ($this->parent_date_date)
				$date_affichee = " [".formatdate($this->parent_date_date)."]";
			else
				$date_affichee="" ;
			$bulletin .= $date_affichee;

			if($this->action_bulletin) {
				$this->action_bulletin = str_replace('!!id!!', $this->bul_id, $this->action_bulletin);
				$bulletin = "<a href=\"".$this->action_bulletin."\">".$bulletin."</a>";
			}
			$mention_parent = "in <b>$bulletin</b>";
		}

		if($mention_parent) {
			$this->isbd .= "<br />$mention_parent";
			$pagination = htmlentities($this->notice->npages,ENT_QUOTES, $charset);
			if($pagination)
				$this->isbd .= ".&nbsp;-&nbsp;$pagination";
		}

		//In
		//Recherche des notices parentes
		if (!$this->no_link) {
			$this->isbd .= $this->notice_relations->get_display_links('parents', $this->print_mode, $this->show_explnum, $this->show_statut, $this->show_opac_hidden_fields);
		}

		if($pmb_show_notice_id || $pmb_show_permalink) $this->isbd .= "<br />";
		if($pmb_show_notice_id){
    	   	$prefixe = explode(",",$pmb_show_notice_id);
			$this->isbd .= "<b>".$msg['notice_id_libelle']."&nbsp;</b>".(isset($prefixe[1]) ? $prefixe[1] : '').$this->notice_id."<br />";
		}
		// Permalink OPAC
		if ($pmb_show_permalink) {
		    $this->isbd .= "<b>".$msg["notice_permalink_opac"]."&nbsp;</b><a href='".$this->get_permalink()."' target=\"_blank\">".$this->get_permalink()."</a><br />";
		}
		// fin du niveau 1
		if($this->level == 1) {
			if ($this->show_explnum) {
				$this->isbd .= '<div id="expl_area_' . $this->notice_id . '">'; 
				$explnum = show_explnum_per_notice($this->notice_id, 0, $this->lien_explnum);
				if ($explnum) $this->isbd .= "<br /><div id='explnum_list_container_serial_".$this->notice->notice_id."'><b>$msg[explnum_docs_associes]</b><br />".$explnum."</div>";
				if ($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == '2' && (SESSrights & CATALOGAGE_AUTH) && $this->bouton_explnum) $this->isbd .= "<br /><input type='button' class='bouton' value=' ".htmlentities($msg['explnum_ajouter_doc'], ENT_QUOTES, $charset)." ' onClick=\"document.location='".$base_path."/catalog.php?categ=serials&analysis_id=$this->notice_id&sub=analysis&action=explnum_form&bul_id=$this->bul_id'\">" ;
				if ($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == '2' && (SESSrights & CIRCULATION_AUTH) && $pmb_scan_request_activate && !$this->context_dsi_id_bannette){
					$this->isbd .= "<input type='button' class='bouton' value='".htmlentities($msg["scan_request_record_button"], ENT_QUOTES, $charset)."' onclick='document.location=\"./circ.php?categ=scan_request&sub=request&action=edit&from_record=".$this->notice_id."\"' />";
				}
				$this->isbd .= '</div>'; 
			}
			return;
		}
		// début du niveau 2

		// map
		if($pmb_map_activate && $this->show_map){
			$this->isbd.=$this->map->get_map();
		}
		// note générale
		if($this->notice->n_gen) {
			$this->isbd .= "<br /><b>$msg[265]</b>:&nbsp;".nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		}
		// note de contenu : non-applicable aux périodiques ??? Ha bon pourquoi ?
		if($this->notice->n_contenu) {
			$this->isbd .= "<br /><b>$msg[266]</b>:&nbsp;".nl2br($this->notice->n_contenu);
		}
		// résumé
		if($this->notice->n_resume) {
			$this->isbd .= "<br /><b>$msg[267]</b>:&nbsp;".nl2br($this->notice->n_resume);
		}

		// fin du niveau 2
		if($this->level == 2)
			return;

		// début du niveau 3
		// fin du niveau 3
		if($this->level == 3)
			return;

		// début du niveau 4
		// catégories
		$tmpcateg_aff = $this->get_display_categories();
		if ($tmpcateg_aff) $this->isbd .= "<br />$tmpcateg_aff";

		// Concepts
		if ($thesaurus_concepts_active == 1) {
			$index_concept = new index_concept($this->notice_id, TYPE_NOTICE);
			if ($index_concept->get_concepts()) {
			    $this->isbd .= "<br /><b>". $msg['param_concepts']. "</b>&nbsp;: ".$index_concept->get_isbd_display();
			}
		}

		// fin du niveau 4
		if($this->level == 4)
			return;

		// début du niveau 5
		// langues
		$langues = '';
		if(count($this->langues)) {
			$langues .= "<b>{$msg[537]}</b>&nbsp;: ".construit_liste_langues($this->langues);
			}
		if(count($this->languesorg)) {
			$langues .= " <b>{$msg[711]}</b>&nbsp;: ".construit_liste_langues($this->languesorg);
			}
		if($langues)
			$this->isbd .= "<br />$langues";

		// indexation libre
		if($this->notice->index_l)
			$this->isbd .= "<br /><b>{$msg[324]}</b>&nbsp;: ".htmlentities($this->notice->index_l,ENT_QUOTES, $charset);

		// indexation interne
		if($this->notice->indexint) {
			$indexint = authorities_collection::get_authority(AUT_TABLE_INDEXINT, $this->notice->indexint);
			if ($this->print_mode) {
				$indexint_isbd=$indexint->display;
			} else {
				$indexint_isbd=$indexint->isbd_entry_lien_gestion;
			}
			$this->isbd .= "<br /><b>".$msg['indexint_catal_title']."</b>&nbsp;: ".$indexint_isbd;
		}

		//code (ISSN,...)
		if ($this->notice->code) $this->isbd .="<br /><b>{$msg[165]}</b>&nbsp;: ".$this->notice->code;

		$tu= new tu_notice($this->notice_id);
		if(($tu_liste=$tu->get_print_type())) {
			$this->isbd .= "<br />".$tu_liste;
		}
		
		$authperso = new authperso_notice($this->notice_id);
		$this->isbd .=$authperso->get_notice_display();
		
		//Champs personalisés
		$perso_aff = $this->get_display_pperso();
		if ($perso_aff) $this->isbd.=$perso_aff ;

		//Notices liées
		$this->isbd .= $this->get_display_relations_links();

		thumbnail::do_image($this->isbd, $this->notice);
		if ($this->show_avis) {
		    $this->isbd .= "<!-- !!avis_notice!! -->";
		}
		if (!$this->print_mode) {
			$this->isbd .= "<!-- !!caddies_notice!! -->";
		}
		// map
		if($pmb_map_activate && $this->show_map){
			$this->isbd.=map_locations_controler::get_map_location($this->notice_id);
		}

		if($this->expl) {
			$expl_aff = "";
			if ($pmb_show_exemplaires_analysis && $this->notice->niveau_biblio=='a' && $this->notice->niveau_hierar==2) { // on est face à une notice d'article
				$requete="select analysis_bulletin from analysis where analysis_notice=".$this->notice->notice_id;
				$result=@pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($result)) {
					$bull = pmb_mysql_fetch_object($result);
					if (!$this->notice->is_numeric) {
						$expl_aff = $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl,$bull->analysis_bulletin);
					} else {
						$expl_aff = $this->show_orders_pnb($this->notice->notice_id);
					}
				}
			}
			if ($expl_aff) {
				$this->isbd .= "<br /><b>{$msg[285]} </b>(".$this->nb_expl.")";
				$this->isbd .= $expl_aff;
			}
		}
		
		//Documents numériques
		$this->isbd.= '<div id="expl_area_' . $this->notice_id . '">';			
		$boutons='';
		if ($this->show_explnum) {
			$explnum = show_explnum_per_notice($this->notice_id, 0, $this->lien_explnum,array(),false,$this->context_dsi_id_bannette);
			if ($explnum) {
				$this->isbd.= "<br /><div id='explnum_list_container_serial_".$this->notice->notice_id."'><b>$msg[explnum_docs_associes]</b> (".show_explnum_per_notice($this->notice->notice_id, 0, $this->lien_explnum,array(),true).")<br />".$explnum."</div>";
			}			
			if ($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == '2' && (SESSrights & CATALOGAGE_AUTH) && $this->bouton_explnum) {
				$boutons.= "<br /><input type='button' class='bouton' value=' $msg[explnum_ajouter_doc] ' onClick=\"document.location='".$base_path."/catalog.php?categ=serials&analysis_id=".$this->notice_id."&sub=analysis&action=explnum_form&bul_id=".$this->bul_id."&explnum_id=0'\">" ;
			}
			if ($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == '2' && (SESSrights & CIRCULATION_AUTH) && $this->bouton_explnum && $pmb_scan_request_activate && !$this->context_dsi_id_bannette){
				$boutons .= "<input type='button' class='bouton' value='".$msg["scan_request_record_button"]."' onclick='document.location=\"./circ.php?categ=scan_request&sub=request&action=edit&from_record=".$this->notice_id."\"' />";
			}
			if ((SESSrights & CATALOGAGE_AUTH) && $this->bouton_explnum && $pmb_type_audit && $this->notice->niveau_biblio == 'a') {
				$boutons.= audit::get_dialog_button($this->notice_id, 1);
			}
		}
		if (!$this->print_mode) {
			$boutons.= form_mapper::get_action_button('notice', $this->notice_id);
			$this->isbd.= $boutons;
		}
		$this->isbd.= '</div>'; // end of <div id="expl_area_' . $this->notice_id . '">

		$event = new event_record('record', 'display');
		$event->set_record_id($this->notice_id);
		$event_handler = events_handler::get_instance();
		$event_handler->send($event);
		if ($event->get_result()) {
			$this->isbd .= $event->get_result();
		}
		
		// fin du niveau 5
		if($this->level == 5)
			return;

		// début du niveau 6
		if($this->notice->niveau_biblio=="s") {
			// Si notice-mère alors on compte le nombre de numéros (bulletins)
			$this->isbd.=$this->get_etat_periodique();
			$this->isbd.=$this->print_etat_periodique();
			//état des collections
			$list_collstate_ui = new list_collstate_ui(array('serial_id' => $this->notice_id, 'bulletin_id' => 0), array('all_on_page' => true));
			if(count($list_collstate_ui->get_objects())) {
				$this->isbd .= "<br /><b>".$msg["abts_onglet_collstate"]."</b><br />";
				$this->isbd.=$list_collstate_ui->get_display_list();
			}
		}
		//Ajout de l'upload de documents numérique en lot
		$this->isbd.= "<!-- !!explnum_drop_zone!! -->";
		
		// fin du niveau 6
		return;


	}

	public function get_etat_periodique() {
		$bulletins=0;
		$nb_expl=0;
		$nb_notices=0;
		if($this->notice->niveau_biblio=="s") {
			$requete = "SELECT * FROM bulletins WHERE bulletin_notice=".$this->notice_id;
			$Query = pmb_mysql_query($requete);
			$bulletins=pmb_mysql_num_rows($Query);
			while (($row = pmb_mysql_fetch_array($Query))) {
				$requete2 = "SELECT count( * )  AS nb_notices FROM  analysis WHERE analysis_bulletin =".$row['bulletin_id'];
				$Query2 = pmb_mysql_query($requete2);
				$analysis_array=pmb_mysql_fetch_array($Query2);
				$nb_notices+=$analysis_array['nb_notices'];
				$requete3 = "SELECT count( expL_id )  AS nb_expl FROM  exemplaires WHERE expl_bulletin =".$row['bulletin_id'];
				$Query3 = pmb_mysql_query($requete3);
				$expl_array=pmb_mysql_fetch_array($Query3);
				$nb_expl+=$expl_array['nb_expl'];
			};
			$requete="SELECT COUNT(collstate_id) FROM collections_state WHERE id_serial='".$this->notice_id."'";
			$Query=pmb_mysql_query($requete);
			if($Query && pmb_mysql_num_rows($Query)){
				$this->serial_nb_etats_collection=pmb_mysql_result($Query,0,0);
			}
			$requete="SELECT COUNT(abt_id) FROM abts_abts WHERE num_notice='".$this->notice_id."' AND date_fin > CURDATE()";
			$Query=pmb_mysql_query($requete);
			if($Query && pmb_mysql_num_rows($Query)){
				$this->serial_nb_abo_actif=pmb_mysql_result($Query,0,0);
			}
			$this->serial_nb_bulletins=$bulletins;
			$this->serial_nb_exemplaires=$nb_expl;
			$this->serial_nb_articles=$nb_notices;
		}
	}

	public function print_etat_periodique() {
		global $msg;
		$affichage = '';
		if($this->notice->niveau_biblio=="s") {
			// Cas général : au moins un bulletin
			if ($this->serial_nb_bulletins > 0)
				{$affichage .="<br />\n
				<b>".$msg["serial_bulletinage_etat"]."</b>
				<table style='border:0px' class='expl-list' role='presentation'>
				    <tr>
                        <td>
                            <strong>".$this->serial_nb_bulletins."</strong> ".$msg["serial_nb_bulletin"]."
				            <strong>".$this->serial_nb_exemplaires."</strong> ".$msg["bulletin_nb_ex"]."
				            <strong>".$this->serial_nb_articles."</strong> ".$msg["serial_nb_articles"]."
				        </td>
				    </tr>
                </table>";
			} else { // 0 bulletin
				$affichage .="<br /><br />\n
				<b>".$msg["serial_bulletinage_etat"]."</b>
				<table style='border:0px' class='expl-list' role='presentation'>
				    <tr>
                        <td>
                            <strong>".$this->serial_nb_bulletins."</strong>
				            ".$msg["serial_nb_bulletin"]." : <strong>".$msg["bull_no_expl"]."</strong>
                        </td>
				    </tr>
                </table>";
			}
		}
		return $affichage;
	}

	protected function get_icon_abo_actif() {
		global $msg;
		
		$this->icon_abo_actif = "";
		if($this->notice->niveau_biblio == 's' && $this->notice->niveau_hierar == 1) {
			$req = "select abt_id from abts_abts  where num_notice=".$this->notice_id." and date_fin >= CURDATE() ";
			$res = pmb_mysql_query($req);
			if (pmb_mysql_num_rows($res)) {
				$icon = "check.png";
				$info_bulle_icon_abo_actif=$msg['abonnements_actif_img_title'];
				$this->icon_abo_actif="<img src=\"".get_url_icon($icon)."\" alt=\"$info_bulle_icon_abo_actif\" title=\"$info_bulle_icon_abo_actif\" class='align_top' />";
			} else {
				$icon = "spacer.gif";
				$this->icon_abo_actif="<img src=\"".get_url_icon($icon)."\" width=\"10\" height=\"10\" />";
			}
		}
		return $this->icon_abo_actif;
	}
	
	// génération du header
	public function do_header() {
		global $base_path;
		global $charset,$msg;
		global $pmb_notice_reduit_format;
		global $use_opac_url_base, $opac_url_base;
		global $no_aff_doc_num_image;

		$type_reduit = substr($pmb_notice_reduit_format,0,1);

		//Icone type de Document
		$this->icondoc = $this->get_icondoc();

		//Icone abonnement actif
		$this->icon_abo_actif = $this->get_icon_abo_actif(); 

    	//Icone nouveauté
    	$this->icon_is_new = $this->get_icon_is_new();
		
		$this->aff_statut = $this->get_aff_statut();

		if ($type_reduit=="H"){
			$id_tpl=substr($pmb_notice_reduit_format,2);
			if($id_tpl){
				$tpl = notice_tpl_gen::get_instance($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$this->header=$notice_tpl_header;
					$this->header_texte=$notice_tpl_header;
				}
			}
    		if (!$this->header) {
    		    $type_reduit = "1";
    		}
		}
		
		if ($type_reduit!="H"){
			$this->header = htmlentities($this->notice->tit1,ENT_QUOTES, $charset);
			$this->header_texte = $this->notice->tit1;
			$this->memo_titre=$this->notice->tit1;
			$this->memo_complement_titre=$this->notice->tit4;
			$this->memo_titre_parallele=$this->notice->tit3;
		}

		if ($type_reduit=='4') {
			if ($this->memo_titre_parallele != "") {
			    $this->header .= "&nbsp;=&nbsp;".htmlentities($this->memo_titre_parallele,ENT_QUOTES, $charset);
				$this->header_texte .= ' = '.$this->memo_titre_parallele;
			}
		}

// 		if ((floor($type_reduit/10) == 1)&&($this->memo_complement_titre)) {
// 			$this->header.="&nbsp;:&nbsp;".htmlentities($this->memo_complement_titre,ENT_QUOTES,$charset);
// 		}

		if ($type_reduit=="T" && $this->memo_complement_titre) {
			$this->header.="&nbsp;:&nbsp;".htmlentities($this->memo_complement_titre,ENT_QUOTES,$charset);
			$this->header_texte.=" : ".$this->memo_complement_titre;
		}

		if (($type_reduit!='3') && ($type_reduit!='H')) {		
			if($auteurs_header = gen_authors_header($this->responsabilites, ';')) {
				$this->header .= ' / '.$auteurs_header;
				$this->header_texte .= ' / '.$auteurs_header;
			}
		}
		$editeur_reduit = '';
		if ($type_reduit=="E") {
			$editeur_reduit = $this->get_aff_editeur_reduit();
		}
		if ($editeur_reduit) {
			$this->header .= ' / '. $editeur_reduit ;
			$this->header_texte .= ' / '. $editeur_reduit ;
		}
		$perso_voulu_aff = "";
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			$perso_voulu_aff = $this->get_aff_perso();
		}
		if ($perso_voulu_aff) {
			$this->header .= ' / '. $perso_voulu_aff ;
			$this->header_texte .= ' / '. $perso_voulu_aff ;
		}

		switch ($type_reduit) {
			case "1":
				if ($this->notice->year != '') {
					$this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					$this->header_texte.=' ('.$this->notice->year.')';
				}
				break;
			case "2":
				if ($this->notice->year != '') {
					$this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					$this->header_texte.=' ('.$this->notice->year.')';
				}
				if ($this->notice->code != '') {
					$this->header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
					$this->header_texte.=' / '.$this->notice->code;
				}
				break;
			default :
				break;
		}

		if (!$this->print_mode) {
			if($this->notice->niveau_biblio == 's' && $this->notice->niveau_hierar == 1) {
			    if($this->action_serial) {
			        $this->header = "<a href=\"".$this->action_serial."\">".$this->header.'</a>';
			    }
			}
			if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) {
			    if($this->action_analysis) {
					$this->header= "<a href=\"".$this->action_analysis."\">".$this->header.'</a>';
			    }
				if ($this->level!=2) {
				    $this->header .= " <i>in ".htmlentities($this->parent_title, ENT_QUOTES, $charset)." ".$this->parent_numero;
				    if ($this->parent_date) {
				        $this->header .= " (".$this->parent_date.")";
				    } else if ($this->parent_date_date) {
				        $this->header .= " [".$this->parent_aff_date_date."]";
				    }
				    $this->header .= "</i>";
				}
			}
		}
		if (isset($this->icon_is_new)) $this->header = $this->header." ".$this->icon_is_new;
		
		if($this->notice->lien) {
			// ajout du lien pour les ressources électroniques
			$this->header .= $this->get_resources_link();
		}
		if (!$this->print_mode || $this->print_mode=='2' && !$no_aff_doc_num_image) {
			$sql_explnum = "SELECT explnum_id, explnum_nom FROM explnum WHERE explnum_notice = ".$this->notice_id;
			$explnums = pmb_mysql_query($sql_explnum);
			$explnumscount = pmb_mysql_num_rows($explnums);
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				if (!$use_opac_url_base) $this->header .= "<a href=\"".$base_path."/doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"_blank\">";
				else $this->header .= "<a href=\"".$opac_url_base."doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"_blank\">";
				$this->header .= "<img src='".get_url_icon('globe_orange.png')."' style='border:0px; margin:3px 3px' class='align_middle'";
				$this->header .= " alt=\"";
				$this->header .= htmlentities($explnumrow->explnum_nom,ENT_QUOTES,$charset);
				$this->header .= "\" title=\"";
				$this->header .= htmlentities($explnumrow->explnum_nom,ENT_QUOTES,$charset);
				$this->header .= "\">";
				$this->header .='</a>';
			}
			else if ($explnumscount > 1 ) {
				$this->header .= "<img src='".get_url_icon('globe_rouge.png')."' style='border:0px; margin:3px 3px' class='align_middle' alt='".htmlentities($msg['info_docs_num_notice'], ENT_QUOTES, $charset)."' title='".htmlentities($msg['info_docs_num_notice'], ENT_QUOTES, $charset)."'>";
			}
			if (($this->drag) && (!$this->print_mode)) {
// 			    $this->header.="<span onMouseOver='if(init_drag) init_drag();' id=\"NOTI_drag_".$this->notice_id."\" dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".htmlentities($this->notice->tit1,ENT_QUOTES, $charset)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".get_url_icon('notice_drag.png')."\"/></span>";
			}
		}
		if (isset($this->icondoc)) $this->header = $this->icondoc." ".$this->header;
		if ($this->show_abo_actif && isset($this->icon_abo_actif)) $this->header = $this->icon_abo_actif." ".$this->header;
		if ($this->show_statut) $this->header = $this->aff_statut." ".$this->header ;
	}

} // fin classe serial_display

// -------------------------------------------------------------------
//   classe bulletinage_display : utilisée pour le prêt de documents
// -------------------------------------------------------------------
class bulletinage_display {
	public $bul_id = 0;		// id du bulletinage à afficher
	public $display = '';		// texte à afficher
	public $parent_title = '';		// titre général de la revue à laquelle fait référence ce bulletinage
	public $bulletin_titre = '';	// titre de ce bulletin
	public $numerotation = '';		// mention de numérotation sur la revue
	public $periode	  = '';		// mention de date de la revue (txt)
	public $date_date	  = '';		// mention de date de la revue (date)
	public $header	  = '';		// pour affichage réduit
	public $aff_date_date;

	// constructeur
	public function __construct($id=0) {
		if(!$id) {
			$this->display = "Error : bul_id is null";
			return $this->bul_id;
		}

		$this->bul_id = $id;

		$this->fetch_bulletinage_data();
		$this->make_display();

		return $this->bul_id;
	}

	// fabrication de la mention à afficher
	public function make_display() {
		if ($this->parent_title) {
			$this->display = $this->parent_title;
			} else {
				$this->display = "error: unknown record";
				return;
				}

		if((!$this->numerotation && !$this->periode && !$this->bulletin_titre && !$this->date_date) || !$this->bul_id) {
			$this->display .= " error : missing information";
			}

		if($this->numerotation)
			$this->display .= '. '.$this->numerotation;

		$this->header = $this->display;

		// affichage de la mention de date utile : mention_date si existe, sinon date_date
		if ($this->periode) {
			$date_affichee = " (".$this->periode.") ";
		} else {
			$date_affichee = " [".$this->aff_date_date."]";
		}
		$this->display .= $date_affichee;

		if ($date_affichee) $this->header .= $date_affichee ;
		}

	// récupération des infos bulletinage en base
	public function fetch_bulletinage_data() {
		global $msg;

		$requete = "SELECT bulletins.*, notices.tit1, date_format(date_date, '".$msg["format_date"]."') as aff_date_date FROM bulletins, notices ";
		$requete .= " WHERE bulletins.bulletin_id=".$this->bul_id;
		$requete .= " AND notices.notice_id=bulletins.bulletin_notice";
		$requete .= " AND notices.niveau_biblio='s' AND notices.niveau_hierar='1' LIMIT 1";

		$myQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($myQuery)) {
			$result = pmb_mysql_fetch_object($myQuery);
			$this->parent_title = $result->tit1;
			$this->bulletin_titre = $result->bulletin_titre;
			$this->numerotation = $result->bulletin_numero;
			$this->periode = $result->mention_date;
			$this->date_date = $result->date_date;
			$this->aff_date_date = $result->aff_date_date;
			$this->bul_id = $result->bulletin_id;
		}

		return;
	}
} // class serial_display