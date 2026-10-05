<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mono_display.class.php,v 1.349.2.5.2.3 2025/03/14 08:23:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path, $tdoc, $fonction_auteur;

require_once($class_path."/record_display.class.php");
require_once("$class_path/marc_table.class.php");
require_once("$class_path/author.class.php");
require_once("$class_path/editor.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/indexint.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/category.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/emprunteur.class.php");
require_once("$class_path/transfert.class.php");
require_once($include_path."/notice_authors.inc.php");
require_once($include_path."/notice_categories.inc.php");
require_once($include_path."/explnum.inc.php");
require_once($include_path."/isbn.inc.php");
require_once($include_path."/resa_func.inc.php");
require_once("$class_path/tu_notice.class.php");
require_once("$class_path/sur_location.class.php");
require_once("$class_path/notice_tpl_gen.class.php");
require_once($class_path."/index_concept.class.php");
require_once("$class_path/authperso_notice.class.php");
require_once("$class_path/map/map_objects_controler.class.php");
require_once("$class_path/map_info.class.php");
require_once($class_path."/nomenclature/nomenclature_record_ui.class.php");
require_once("$class_path/groupexpl.class.php");
require_once("$class_path/collstate.class.php");
require_once ($class_path."/map/map_locations_controler.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path."/thumbnail.class.php");
require_once($class_path."/pnb/pnb_record_orders.class.php");

if (empty($tdoc)) $tdoc = marc_list_collection::get_instance('doctype');
if (empty($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

// propriétés pour le selecteur de panier
$cart_click = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=!!id!!&unq=!!unique!!', 'cart')\"";


// définition de la classe d'affichage des monographies en liste
class mono_display extends record_display {
	public $isbn 		= 0;	// isbn ou code EAN de la notice à afficher
  	public $action		= '';	// URL à associer au header
	public $tit_serie	= '';	// titre de série si applicable
	public $simple_isbd = "";	// isbd de la notice en fonction du level défini, sans l'image
	public $show_resa	= 0;	// flag indiquant si on affiche les infos de resa
	public $show_planning	= 0;	// flag indiquant si on affiche les infos de prévision
	public $tit_serie_lien_gestion ;
	public $drag=""; 			//Notice draggable ?
	public $no_link;
	public $ajax_mode=0;
	public $link_explnum = "";
	public $lien_suppr_cart = "";
	public $icon_is_new = "";
	public $memo_titre = "";
	public $memo_complement_titre = "";
	public $memo_titre_parallele = "";
	public $mono_display_cmd = "";
	public $show_avis=1;

	// constructeur------------------------------------------------------------
	public function __construct(	$id,							// $id = id de la notice à afficher
						$level=1, 						// $level :
														//	0 : juste le header (titre  / auteur principal avec le lien si applicable)
														//	1 : ISBD seul, pas de note, bouton modif, expl, explnum et résas
														// 	6 : cas général détaillé avec notes, categ, langues, indexation... + boutons
						$action='', 					// $action	 = URL associée au header
						$expl=1, 						// $expl -> affiche ou non les exemplaires associés
						$expl_link='', 					// $expl_link -> lien associé à l'exemplaire avec !!expl_id!!, !!notice_id!! et !!expl_cb!! à mettre à jour
						$lien_suppr_cart="", 			// $lien_suppr_cart -> lien de suppression de la notice d'un caddie
						$explnum_link='',
						$show_resa=0,   				// $show_resa = affichage des resa ou pas
						$print=0, 						// $print = 0 affichage normal
														//			1 affichage impression sans liens
														//			2 affichage impression avec liens sur documents numeriques
														//			4 affichage email : sans lien sauf url associée
						$show_explnum=1,
						$show_statut=0,
						$anti_loop = array(),
						$draggable=0,
						$no_link=false,
						$show_opac_hidden_fields=true,
						$ajax_mode=0,
						$show_planning=0, 				// $show_planning = affichage des prévisions ou pas
						$show_map=1,                    // $show_map = affichage de la map
						$context_dsi_id_bannette=0,      // $context_dsi_id_bannette = dans le contexte de la dsi
						$context_parameters = array(),	// Elements de contexte (ex : in_search, in_selector)
	    				$show_avis=1					// $show_avis = affichage des avis
						) {

	  	global $pmb_recherche_ajax_mode;
	  	global $categ;
	  	global $id_empr;
		
	  	if (!is_array($anti_loop)) {
	  		$anti_loop = array();
	  	}
	  	$this->show_map=$show_map;
		$this->context_dsi_id_bannette=$context_dsi_id_bannette;
		$this->context_parameters = $context_parameters;
	
	  	if($pmb_recherche_ajax_mode){
			$this->ajax_mode=$ajax_mode;
		  	if($this->ajax_mode) {
		  		$param=array();
				if (is_object($id)){
					$param['id']=$id->notice_id;
				} else {
					$param['id']=$id;
				}
				$param['function_to_call']="mono_display";
			  	//if($level)$param['level']=$level;	// à 6
		  		if($action)$param['action']=$action;
		  		if($expl)$param['expl']=$expl;
		  		if($expl_link)$param['expl_link']=$expl_link;
	//		  	if($lien_suppr_cart)$param['lien_suppr_cart']=$lien_suppr_cart;
			  	if($explnum_link)$param['explnum_link']=$explnum_link;
				//if($show_resa)$param['show_resa']=$show_resa;
			  	if($print)$param['print']=$print;
			  	//if($show_explnum)$param['show_explnum']=$show_explnum;
			  	//if($show_statut)$param['show_statut']=$show_statut;
			  	//if($anti_loop)$param['anti_loop']=$anti_loop;
			  	//if($draggable)$param['draggable']=$draggable;
			  	if($no_link)$param['no_link']=$no_link;
			  	if($categ)$param['categ']=$categ;
			  	if($id_empr)$param['id_empr']=$id_empr;
			  	//if($show_opac_hidden_fields)$param['show_opac_hidden_fields']=$show_opac_hidden_fields;
			  	$this->mono_display_cmd=serialize($param);
		  	}
	  	}

	   	if(!$id)
	  		return;
		else {
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
			$this->notice_relations = notice_relations_collection::get_object_instance($this->notice_id);
			if(!$this->ajax_mode || !$level) {
				$this->childs = $this->notice_relations->get_childs();
			}
	   	}
	   	global $memo_p_perso_notice;
		if(!$this->ajax_mode || !$level) {
			if(!$memo_p_perso_notice) {
				$memo_p_perso_notice=new parametres_perso("notices");
			}
			$this->p_perso=$memo_p_perso_notice;
		}
		$this->level = $level;
		$this->expl  = $expl;
		$this->show_resa  = $show_resa;
	
		$this->link_expl = $expl_link;
		$this->link_explnum = $explnum_link;
		$this->lien_suppr_cart = $lien_suppr_cart;
		// mise à jour des liens
		$this->action = $action;
		$this->drag=$draggable;
	
		$this->print_mode=$print;
		$this->show_explnum=$show_explnum;
		$this->show_statut=$show_statut;
		$this->no_link=$no_link;
		$this->show_avis=$show_avis;
		
		$this->anti_loop=$anti_loop;
	
		//affichage ou pas des champs persos OPAC masqués
		$this->show_opac_hidden_fields=$show_opac_hidden_fields;
	
		$this->action = str_replace('!!id!!', $this->notice_id, $this->action);
	
		$this->responsabilites = get_notice_authors($this->notice_id) ;
	
		// mise à jour des catégories
		if(!$this->ajax_mode || !$level) $this->categories = get_notice_categories($this->notice_id) ;
	
		$this->show_planning  = $show_planning;
		$this->do_header();
		switch($level) {
			case 0:
				// là, c'est le niveau 0 : juste le header
				$this->result = $this->header;
				break;
			default:
				global $pmb_map_activate;
				$this->map=new stdClass();
				$this->map_info=new stdClass();
				if($pmb_map_activate){
					$ids=array();
					$ids[]=$this->notice_id;
					$this->map=new map_objects_controler(TYPE_RECORD,$ids);
					$this->map_info=new map_info($this->notice_id);
				}
				// niveau 1 et plus : header + isbd à générer
				$this->init_javascript();
				if(!$this->ajax_mode) $this->do_isbd();
				$this->finalize();
				break;
		}
		return;
	}


	// finalisation du résultat (écriture de l'isbd)
	public function finalize() {
		$this->result = str_replace('!!ISBD!!', $this->isbd, $this->result);
	}
	
	protected function get_display_open_tag() {
		return '';
	}
	
	protected function get_display_anchor() {
		return '';
	}
	
	// génération de l'isbd----------------------------------------------------
	public function do_isbd() {
		global $msg, $base_path;
		global $tdoc;
		global $charset;
		global $lang;
		global $categ;
		global $id_empr;
		global $pmb_show_notice_id,$pmb_opac_url,$pmb_show_permalink;
		global $sort_children;
		global $pmb_resa_planning;
		global $thesaurus_concepts_active;
		global $pmb_map_activate;
		global $pmb_nomenclature_activate;
		global $pmb_resa_records_no_expl;
		global $pmb_show_exemplaires_pnb;
	
		// constitution de la mention de titre
		if($this->tit_serie) {
			if ($this->print_mode) $this->isbd = htmlentities($this->tit_serie, ENT_QUOTES, $charset);
				else $this->isbd = $this->tit_serie_lien_gestion;
			if($this->notice->tnvol)
				$this->isbd .= ',&nbsp;'.$this->notice->tnvol;
		}
		if($this->isbd) {
			$this->isbd .= '.&nbsp;'.htmlentities($this->notice->tit1, ENT_QUOTES, $charset);
		} else {
			$this->isbd = htmlentities($this->notice->tit1, ENT_QUOTES, $charset);
		}
	
		$tit2 = $this->notice->tit2;
		$tit3 = $this->notice->tit3;
		$tit4 = $this->notice->tit4;
		if($tit3) $this->isbd .= "&nbsp;= ".htmlentities($tit3, ENT_QUOTES, $charset);
		if($tit4) $this->isbd .= "&nbsp;: ".htmlentities($tit4, ENT_QUOTES, $charset);
		if($tit2) $this->isbd .= "&nbsp;; ".htmlentities($tit2, ENT_QUOTES, $charset);
		$this->isbd .= (!empty($tdoc->table[$this->notice->typdoc]) ? ' ['.$tdoc->table[$this->notice->typdoc].']' : '');
		
		// constitution de la mention de responsabilité
		if($libelle_mention_resp = gen_authors_isbd($this->responsabilites, $this->print_mode)) {
			$this->isbd .= "&nbsp;/ ". $libelle_mention_resp ." " ;
		}
	
		// mention d'édition
		if($this->notice->mention_edition) $this->isbd .= ".&nbsp;-&nbsp;".htmlentities($this->notice->mention_edition, ENT_QUOTES, $charset);
	
		if($pmb_map_activate){
			if($mapisbd=$this->map_info->get_isbd())	$this->isbd .=$mapisbd;
		}
	
		// zone de l'adresse
		// on récupère la collection au passage, si besoin est
		$editeurs = '';
		$collections = '';
		if($this->notice->subcoll_id) {
			$collection = authorities_collection::get_authority(AUT_TABLE_SUB_COLLECTIONS, $this->notice->subcoll_id);
			$ed_obj = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $collection->editeur);
			if ($this->print_mode) {
				$editeurs .= $ed_obj->get_isbd();
				$collections = $collection->get_isbd();
			} else {
				$editeurs .= $ed_obj->isbd_entry_lien_gestion;
				$collections = $collection->isbd_entry_lien_gestion;
			}
		} elseif ($this->notice->coll_id) {
			$collection = authorities_collection::get_authority(AUT_TABLE_COLLECTIONS, $this->notice->coll_id);
			$ed_obj = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $collection->parent);
			if ($this->print_mode) {
				$editeurs .= $ed_obj->get_isbd();
				$collections = $collection->get_isbd();
			} else {
				$editeurs .= $ed_obj->isbd_entry_lien_gestion;
				$collections = $collection->isbd_entry_lien_gestion;
			}
		} elseif ($this->notice->ed1_id) {
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
		        $editeurs .= ', '.htmlentities($this->notice->year, ENT_QUOTES, $charset);
		    } else {
		        $editeurs = htmlentities($this->notice->year, ENT_QUOTES, $charset);
		    }
		} elseif ($this->notice->niveau_biblio!='b') {
		    if ($editeurs) {
		        $editeurs .= ', [s.d.]';
		    } else {
		        $editeurs = "[s.d.]";
		    }
		}
	
		if($editeurs) {
			$this->isbd .= ".&nbsp;-&nbsp;$editeurs";
		}
	
	
		// zone de la collation (ne concerne que a2)
		$collation = $this->get_display_collation();
	
		if($collation)
			$this->isbd .= ".&nbsp;-&nbsp;$collation";
	
	
		if($collections) {
			if($this->notice->nocoll) $collections .= '; '.htmlentities($this->notice->nocoll, ENT_QUOTES, $charset);
			$this->isbd .= ".&nbsp;-&nbsp;(".$collections.")".' ';
			}
		if(substr(trim($this->isbd), -1) != "."){
			$this->isbd .= '.';
		}
	
		$zoneISBN = '';
		
		// ISBN ou NO. commercial
		if($this->notice->code) {
			if(isISBN($this->notice->code)) {
				$zoneISBN .= $msg['isbd_notice_isbn'].' ';
			}
			$zoneISBN .= htmlentities($this->notice->code, ENT_QUOTES, $charset);
		}
	
		if($this->notice->prix) {
			if($this->notice->code) {
				$zoneISBN .= '&nbsp;: '.htmlentities($this->notice->prix, ENT_QUOTES, $charset);
			} else {
				if ($zoneISBN) 	{ 
					$zoneISBN .= '&nbsp; '.htmlentities($this->notice->prix, ENT_QUOTES, $charset);
				} else {
					$zoneISBN = htmlentities($this->notice->prix, ENT_QUOTES, $charset);
				}
			}
		}
	
		if($zoneISBN) $this->isbd .= "<br /><br />$zoneISBN.";
	
		//In
		//Recherche des notices parentes
		if (!$this->no_link) {
			$this->isbd .= $this->notice_relations->get_display_links('parents', $this->print_mode, $this->show_explnum, $this->show_statut, $this->show_opac_hidden_fields);
		}
	
		if($pmb_show_notice_id || $pmb_show_permalink) $this->isbd .= "<br />";
		if($pmb_show_notice_id){
	       	$prefixe = explode(",",$pmb_show_notice_id);
			$this->isbd .= "<b>".$msg['notice_id_libelle']."&nbsp;</b>".(isset($prefixe[1]) ? htmlentities($prefixe[1], ENT_QUOTES, $charset) : '').$this->notice_id."<br />";
		}
		// Permalink OPAC
		if ($pmb_show_permalink) {
		    $this->isbd .= "<b>".$msg["notice_permalink_opac"]."&nbsp;</b><a href='".$this->get_permalink()."' target=\"_blank\">".$this->get_permalink()."</a><br />";
		}
		// niveau 1
		if($this->level == 1) {
			if(!$this->print_mode) $this->isbd .= "<!-- !!bouton_modif!! -->";
			if ($this->expl) {				
				if (!$this->notice->is_numeric) {
					$this->isbd .= "<br /><b>{$msg[285]}</b> (".$this->nb_expl.")";
					$this->isbd .= $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl);
				} else {
					$this->isbd .= $this->show_orders_pnb($this->notice->notice_id);
				}	
			}
			if ($this->show_explnum) {
				$explnum_assoc = show_explnum_per_notice($this->notice->notice_id, 0,$this->link_explnum);
				
				if ($explnum_assoc) {
				    $explnum_length = show_explnum_per_notice($this->notice->notice_id, 0, $this->link_explnum, array(), true);
				    
				    if($explnum_length >= 8) {
    				    $this->isbd .= gen_plus(
    				        "explnum_list_container_record_" . $this->notice->notice_id,
    				        $msg["docnum"] . " (" . $explnum_length . ")",
    				        $explnum_assoc,
    				        0
				        );
				    } else {
        				$this->isbd .= "<div id='explnum_list_container_record_" . $this->notice->notice_id . "'><b>" . $msg["explnum_docs_associes"] . "</b>" . $explnum_assoc . "</div>";
				    }
				}
			}
			if($this->show_resa) {
				$aff_resa=resa_list ($this->notice_id, 0, 0) ;
				if ($aff_resa) $this->isbd .= "<b>$msg[resas]</b>".$aff_resa;
			}
			if($this->show_planning && $pmb_resa_planning) {
				$aff_resa_planning=planning_list($this->notice_id,0,0) ;
				if ($aff_resa_planning)	$this->isbd .= "<b>$msg[resas_planning]</b>".$aff_resa_planning;
			}
			$this->simple_isbd=$this->isbd;
			if (isset($this->notice)) {
			    thumbnail::do_image($this->isbd, $this->notice);
			}
			return;
		}
		
		// map
		if($pmb_map_activate && $this->show_map){
			$this->isbd.=$this->map->get_map();
		}
		if($pmb_nomenclature_activate){
			$nomenclature= new nomenclature_record_ui($this->notice_id);
			$this->isbd.=$nomenclature->get_isbd();
		}
		// note générale
		if($this->notice->n_gen) {
			$this->isbd .= "<br /><b>".$msg['265']."</b>:&nbsp;".nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		}
		// note de contenu : non-applicable aux périodiques ??? Ha bon pourquoi ?
		if($this->notice->n_contenu) {
			$this->isbd .= "<br /><b>$msg[266]</b>:&nbsp;".nl2br($this->notice->n_contenu);
		}
		// résumé
		if($this->notice->n_resume) {
			$this->isbd .= "<br /><b>$msg[267]</b>:&nbsp;".nl2br($this->notice->n_resume);
		}
	
		// catégories
		$tmpcateg_aff = $this->get_display_categories();
		if ($tmpcateg_aff) {
		    $this->isbd .= "<br />$tmpcateg_aff";
		}
	
		// Concepts
		if ($thesaurus_concepts_active == 1) {
			$index_concept = new index_concept($this->notice_id, TYPE_NOTICE);
			if ($index_concept->get_concepts()) {
    			$this->isbd .= "<br /><b>".$msg['param_concepts']."&nbsp;:</b> ".$index_concept->get_isbd_display();
			}
		}
	
		// langues
		$langues = '';
		if(count($this->langues)) {
			$langues .= "<b>{$msg[537]}&nbsp;:</b> ".construit_liste_langues($this->langues);
		}
		if(count($this->languesorg)) {
			$langues .= " <b>{$msg[711]}</b>&nbsp;: ".construit_liste_langues($this->languesorg);
		}
		if($langues)
			$this->isbd .= "<br />$langues";
	
		// indexation libre
		if($this->notice->index_l)
			$this->isbd .= "<br /><b>{$msg[324]}</b>&nbsp;: ".nl2br($this->notice->index_l);
	
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
	
		$tu= new tu_notice($this->notice_id);
		if(($tu_liste=$tu->get_print_type())) {
			$this->isbd .= "<br />".$tu_liste;
		}
	
		$authperso = new authperso_notice($this->notice_id);
		$this->isbd .=$authperso->get_notice_display();
	
		//Champs personalisés
		$perso_aff = $this->get_display_pperso();
		if ($perso_aff) $this->isbd.=$perso_aff ;
	
		//Source externe ?
		$this->isbd .= $this->get_display_external();
		
		//Notices liées
		$this->isbd .= $this->get_display_relations_links();
	
		if(!$this->print_mode && !count($this->anti_loop)) $this->isbd .= "<!-- !!bouton_modif!! -->";
		thumbnail::do_image($this->isbd, $this->notice);
		if( !count($this->anti_loop)) {
		    if(!$this->print_mode) {
		        if ($this->show_avis) {
                    $this->isbd .= "<!-- !!avis_notice!! -->";
		        }
			    $this->isbd .= "<!-- !!caddies_notice!! -->";
			}
		}
		$this->isbd.= '<div id="expl_area_' . $this->notice_id . '">';
		// map
		if($pmb_map_activate && $this->show_map){
			$this->isbd.=map_locations_controler::get_map_location($this->notice_id);		
		}
		if($this->expl) {
			$collstate_aff = "";
			if ($this->notice->niveau_biblio=='b' && $this->notice->niveau_hierar==2) { // on est face à une notice de bulletin
				$requete="select bulletin_id from bulletins where num_notice=".$this->notice->notice_id;
				$result=pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($result)) {
					$bull = pmb_mysql_fetch_object($result);					
					if (!$this->notice->is_numeric) {
						$expl_aff = $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl,$bull->bulletin_id);
					} else {
					    $expl_aff = $this->show_orders_pnb($this->notice->notice_id);
					    switch ($pmb_show_exemplaires_pnb) {
					        case 1:
					            // Les exemplaires en cours de prêt
					            $expl_aff .= $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl, 0, true);
					            break;
					        case 2:
					            // Tous les exemplaires associés
					            $expl_aff .= $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl, 0, false);
					            break;
					        case 0:
					        default:
					            break;
					    }
					}
					//on affiche les états des collections en condition identique des exemplaires
					$list_collstate_ui = new list_collstate_ui(array('serial_id' => 0, 'bulletin_id' => $bull->bulletin_id), array('all_on_page' => true));
					if(count($list_collstate_ui->get_objects())) {
						$collstate_aff = $list_collstate_ui->get_display_list();
					}
				}
			}else{
				if (!$this->notice->is_numeric) {
					$expl_aff = $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl);
				} else {
				    $expl_aff = $this->show_orders_pnb($this->notice->notice_id);
				    switch ($pmb_show_exemplaires_pnb) {
				        case 1:
				            // Les exemplaires en cours de prêt
				            $expl_aff .= $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl, 0, true);
				            break;
				        case 2:
				            // Tous les exemplaires associés
				            $expl_aff .= $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl, 0, false);
				            break;
				        case 0:
				        default:
				            break;
				    }
				}
			}
			if ($expl_aff) {
				$this->isbd .= "<br /><b>{$msg[285]} </b>(".$this->nb_expl.")";
				$this->isbd .= $expl_aff;
			}
			if($collstate_aff) {
				$this->isbd .= "<br /><b>".$msg["abts_onglet_collstate"]." (".count($list_collstate_ui->get_objects()).")</b><br />";
				$this->isbd .= $collstate_aff;
			}
		}
		if ($this->show_explnum) {
			$explnum_assoc = show_explnum_per_notice($this->notice->notice_id, 0, $this->link_explnum,array(),false,$this->context_dsi_id_bannette);
			
			if ($explnum_assoc) {
			    $explnum_length = show_explnum_per_notice($this->notice->notice_id, 0, $this->link_explnum, array(), true);
			    
			    if($explnum_length >= 8) {
        			$this->isbd .= gen_plus(
        			    "explnum_list_container_record_" . $this->notice->notice_id,
        			    $msg["docnum"]. " (" . $explnum_length . ")",
        			    $explnum_assoc,
        			    0
        			);
			    } else {
			        $this->isbd .= "<div id='explnum_list_container_record_" . $this->notice->notice_id . "'><b>" . $msg["explnum_docs_associes"] . "</b> (" . $explnum_length . ")" . $explnum_assoc . '</div>';
			    }
			}
		}
		$this->isbd.= '</div>';
		//documents numériques en relation...
		$explnum_in_relation = $this->show_explnum_in_relation();
		if ($explnum_in_relation) $this->isbd .= "<b>".$msg["explnum_docs_in_relation"]."</b>".$explnum_in_relation;
	
		//reservations et previsions
		if (($this->show_resa || ($this->show_planning && $pmb_resa_planning)) && !$this->notice->is_numeric) {
			$rqt_nt = "SELECT count(*) FROM exemplaires
				JOIN docs_statut ON exemplaires.expl_statut=docs_statut.idstatut";
			if ($this->notice->niveau_biblio=='b') {
				$rqt_nt .= " JOIN bulletins ON exemplaires.expl_bulletin=bulletins.bulletin_id 
						JOIN notices ON notices.notice_id=bulletins.num_notice";
			} else {
				$rqt_nt .= " JOIN notices ON notices.notice_id=exemplaires.expl_notice";
			}
			$rqt_nt .= " WHERE statut_allow_resa=1 AND notices.notice_id=".$this->notice_id;
			$result = pmb_mysql_query($rqt_nt);
			$nb_expl_reservables = pmb_mysql_result($result,0,0);
	
			if($this->show_resa) {
				$aff_resa=resa_list($this->notice_id, 0, 0) ;
				if ($this->notice->niveau_biblio=='b') {
					$query = "select bulletin_id from bulletins where num_notice=".$this->notice->notice_id;
					$result = pmb_mysql_query($query);
					if (pmb_mysql_num_rows($result)) {
						$bull = pmb_mysql_fetch_object($result);
						$ouvrir_reserv = "onclick=\"parent.location.href='".$base_path."/circ.php?categ=resa_from_catal&id_bulletin=".$bull->bulletin_id."'; return(false) \"";
						$force_reserv = "onclick=\"parent.location.href='".$base_path."/circ.php?categ=resa_from_catal&id_bulletin=".$bull->bulletin_id."&force_resa=1'; return(false) \"";
					}
				} else {
					$ouvrir_reserv = "onclick=\"parent.location.href='".$base_path."/circ.php?categ=resa_from_catal&id_notice=".$this->notice_id."'; return(false) \"";
					$force_reserv = "onclick=\"parent.location.href='".$base_path."/circ.php?categ=resa_from_catal&id_notice=".$this->notice_id."&force_resa=1'; return(false) \"";
				}
				if ($aff_resa){
					$this->isbd .= "<b>".$msg['resas']."</b><br />";
					if($nb_expl_reservables && !($categ=="resa") && !$id_empr) $this->isbd .= "<input type='button' class='bouton' value='".$msg['351']."' $ouvrir_reserv><br /><br />";
					if(!$nb_expl_reservables && $pmb_resa_records_no_expl){
					    $this->isbd.= "<input type='button' class='bouton' value='".$msg['resa_force']."' $force_reserv><br /><br />";
					}
					$this->isbd .= $aff_resa."<br />";
				} else {
					if ($nb_expl_reservables && !($categ=="resa") && !$id_empr){
						$this->isbd .= "<b>".$msg['resas']."</b><br /><input type='button' class='bouton' value='".$msg['351']."' $ouvrir_reserv><br /><br />";
					}else if(!$nb_expl_reservables && $pmb_resa_records_no_expl){
						$this->isbd .= "<b>".$msg['resas']."</b><br /><input type='button' class='bouton' value='".$msg['resa_force']."' $force_reserv><br /><br />";
					}
				}
			}
			if($this->show_planning && $pmb_resa_planning) {
				$aff_resa_planning=planning_list($this->notice_id,0,0);
				if ($this->notice->niveau_biblio=='b') {
					$query = "select bulletin_id from bulletins where num_notice=".$this->notice->notice_id;
					$result = pmb_mysql_query($query);
					if (pmb_mysql_num_rows($result)) {
						$bull = pmb_mysql_fetch_object($result);
						$ouvrir_reserv = "onclick=\"parent.location.href='".$base_path."/circ.php?categ=resa_planning_from_catal&id_bulletin=".$bull->bulletin_id."'; return(false) \"";
					}
				} else {
					$ouvrir_reserv = "onclick=\"parent.location.href='".$base_path."/circ.php?categ=resa_planning_from_catal&id_notice=".$this->notice_id."'; return(false) \"";
				}
				if ($aff_resa_planning){
					$this->isbd .= "<b>".$msg['resas_planning']."</b><br />";
					if($nb_expl_reservables && !($categ=="resa_planning") && !$id_empr) $this->isbd .= "<input type='button' class='bouton' value='".$msg['resa_planning_add']."' $ouvrir_reserv><br /><br />";
					$this->isbd .= $aff_resa_planning."<br />";
				} else {
					if ($nb_expl_reservables && !($categ=="resa_planning") && !$id_empr) $this->isbd .= "<b>".$msg['resas_planning']."</b><br /><input type='button' class='bouton' value='".$msg['resa_planning_add']."' $ouvrir_reserv><br /><br />";
				}
			}
		}
		return;
	}

	// génération du header----------------------------------------------------
	public function do_header() {
		global $base_path, $use_opac_url_base;
		global $charset,$msg;
		global $pmb_notice_reduit_format;
		global $tdoc;
		global $opac_url_base;
		global $no_aff_doc_num_image;
	
		$type_reduit = substr($pmb_notice_reduit_format,0,1);
	
		//Icone type de Document
	    $this->icondoc = $this->get_icondoc();
	
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
		
		if ($type_reduit!="H") {
			// récupération du titre de série
			if (!empty($this->tit_serie)) {
				$this->header =$this->header_texte= $this->tit_serie;
				if($this->notice->tnvol) {
					$this->header .= ',&nbsp;'.htmlentities($this->notice->tnvol, ENT_QUOTES, $charset);
					$this->header_texte .= ', '.$this->notice->tnvol;
				}
			} elseif (!empty($this->notice->tnvol)) {
				$this->header .= htmlentities($this->notice->tnvol, ENT_QUOTES, $charset);
				$this->header_texte .= $this->notice->tnvol;
			}
			$this->tit1 = $this->notice->tit1;
			$this->header ? $this->header .= '.&nbsp;'.htmlentities($this->tit1, ENT_QUOTES, $charset) : $this->header = htmlentities($this->tit1, ENT_QUOTES, $charset);
			$this->header_texte ? $this->header_texte .= '. '.$this->tit1 : $this->header_texte = $this->tit1;
			$this->memo_titre = $this->header_texte;
			$this->memo_complement_titre = $this->notice->tit4;
			$this->memo_titre_parallele = $this->notice->tit3;
		}
	
		if ($type_reduit=='4') {
			if ($this->memo_titre_parallele != "") {
				$this->header .= "&nbsp;=&nbsp;".htmlentities($this->memo_titre_parallele, ENT_QUOTES, $charset);
	 			$this->header_texte .= ' = '.$this->memo_titre_parallele;
			}
		}
	
		if ($type_reduit=="T" && $this->memo_complement_titre) {
			$this->header.="&nbsp;:&nbsp;".htmlentities($this->memo_complement_titre, ENT_QUOTES, $charset);
			$this->header_texte.=" : ".$this->memo_complement_titre;
		}
	
		if (($type_reduit!='3') && ($type_reduit!='H')) {		
			if($auteurs_header = gen_authors_header($this->responsabilites, ';')) {
				$this->header .= ' / '. $auteurs_header;
				$this->header_texte .= ' / '. $auteurs_header;
			}
		}
	
		$editeur_reduit = "";
		if ($type_reduit=="E") {
			$editeur_reduit .= $this->get_aff_editeur_reduit();
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
	
		if (($this->drag) && (!$this->print_mode)) {
// 			$drag="<span onMouseOver='if(init_drag) init_drag();' id=\"NOTI_drag_".$this->notice_id.(is_array($this->anti_loop) && count($this->anti_loop)?"_p".$this->anti_loop[count($this->anti_loop)-1]:"")."\"  dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".strip_tags($this->header)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".get_url_icon('notice_drag.png')."\"/></span>";
			$drag="";
		}
		if($this->action) {
			$this->header = "<a href=\"".$this->action."\">".$this->header.'</a>';
		}
		if (isset($this->icon_is_new)) $this->header = $this->header." ".$this->icon_is_new;
		if ($this->notice->niveau_biblio=='b') {
			$rqt="select tit1, date_format(date_date, '".$msg["format_date"]."') as aff_date_date, bulletin_numero as num_bull from bulletins,notices where bulletins.num_notice='".$this->notice_id."' and notices.notice_id=bulletins.bulletin_notice";
			$execute_query=pmb_mysql_query($rqt);
			if (pmb_mysql_num_rows($execute_query)) {
    			$row=pmb_mysql_fetch_object($execute_query);
    			
    			if (empty($row->aff_date_date)) {
    			    $header_issue = sprintf($msg["bul_titre_perio"], htmlentities($row->tit1, ENT_QUOTES, $charset));
    			    $header_issue_text = sprintf($msg["bul_titre_perio"], $row->tit1);
    			} else {
    			    $header_issue = sprintf($msg["bul_titre_perio"], htmlentities($row->tit1.", ".$row->num_bull." [".$row->aff_date_date."]", ENT_QUOTES, $charset));
    			    $header_issue_text = sprintf($msg["bul_titre_perio"], $row->tit1.", ".$row->num_bull." [".$row->aff_date_date."]");
    			}
    			
    			$this->header.=" <i>".$header_issue."</i>";
    			$this->header_texte.=" ".$header_issue_text;
			}
			pmb_mysql_free_result($execute_query);
		}
		if (($this->drag) && (!$this->print_mode)) $this->header.=$drag;
	
		if($this->notice->lien) {
			// ajout du lien pour les ressources électroniques
			$this->header .= $this->get_resources_link();
		}
		if(!$this->print_mode || $this->print_mode=='2' && !$no_aff_doc_num_image)	{
			if ($this->notice->niveau_biblio=='b')
				$sql_explnum = "SELECT explnum_id, explnum_nom FROM explnum, bulletins WHERE bulletins.num_notice = ".$this->notice_id." AND bulletins.bulletin_id = explnum.explnum_bulletin order by explnum_id";
			else
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
			else if ($explnumscount > 1) {
				$this->header .= "<img src='".get_url_icon('globe_rouge.png')."' style='border:0px; margin:3px 3px' class='align_middle' alt='".htmlentities($msg['info_docs_num_notice'], ENT_QUOTES, $charset)."' title='".htmlentities($msg['info_docs_num_notice'], ENT_QUOTES, $charset)."'>";
			}
		}
		if (isset($this->icondoc)) $this->header = $this->icondoc." ".$this->header;
		if ($this->show_statut) $this->header = $this->aff_statut." ".$this->header ;
	}

	// récupération des valeurs en table---------------------------------------
	public function fetch_data() {
		parent::fetch_data();
		//Récupération titre de série
		if (!empty($this->notice->tparent_id)) {
			$parent = new serie($this->notice->tparent_id);
			$this->tit_serie = $parent->name;
			$this->tit_serie_lien_gestion = $parent->isbd_entry_lien_gestion;
		}
		$this->isbn = isset($this->notice->code) ? $this->notice->code : '';
	}
}