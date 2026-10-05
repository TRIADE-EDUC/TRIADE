<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_display.class.php,v 1.32.4.1 2025/04/25 14:16:44 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once("$class_path/marc_table.class.php");
require_once("$class_path/author.class.php");
require_once("$class_path/editor.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/serialcirc_diff.class.php");
require_once("$include_path/notice_authors.inc.php");
require_once("$include_path/isbn.inc.php");

global $tdoc, $fonction_auteur;
if (!isset($tdoc)) $tdoc = marc_list_collection::get_instance('doctype');
if (!isset($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

// définition de la classe d'affichage des monographies en liste pour selecteur
class sel_mono_display {

	public $notice_id = 0;		//id notice
	public $notice = '';		//objet notice
  	public $header = '';		//entete
	public $result	= '';		//affichage final
	public $isbd = '';			//isbd notice
	public $responsabilites =	array("responsabilites" => array(),"auteurs" => array());  //auteurs
	public $statut = '' ;		//statut notice
	public $tit_serie = '';	//titre serie
	public $tit1 = '';			//titre 1
	public $nb_expl = 0;		//nb exemplaires

	public $base_url = '';					//URL a associer aux elements cliquables
  	public $action = '';					//action a effectuer pour retour des parametres
	public $action_values = array();		//tableau des elements à modifier dans l'action

	public $code = '';			//isbn ou code EAN de la notice à afficher
	public $titre = '';		//titre renvoye
	public $auteur1 = '';		//auteur1 renvoye
	public $editeur1 = '';		//editeur1 renvoye
	public $collection = '';	//collection renvoyee
	public $prix = '0.00';		//prix renvoye

	public $form_checker = ''; //Affichage d'une case à cocher sur le template


	// constructeur
	public function __construct($notice_id, $base_url, $form_checker='') {
		$this->notice_id=intval($notice_id);
	  	$this->base_url=$base_url;
	  	$this->form_checker=$form_checker;
	}


	//creation formulaire
	public function doForm() {
		$this->getData();
		$this->responsabilites = get_notice_authors($this->notice_id) ;
		$this->doHeader();
		$this->doContent();
		$this->finalize();
	}


	// récupération des valeurs en table
	public function getData() {
		$q = "SELECT * FROM notices WHERE notice_id='".$this->notice_id."' ";
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {
			$this->notice = pmb_mysql_fetch_object($r);
		}
		$q = "select count(*) from exemplaires where expl_notice='".$this->notice_id."' ";
		$r = pmb_mysql_query($q);
		$this->nb_expl = pmb_mysql_result($r,0,0);
	}


	// creation header
	public function doHeader() {
		global $charset;
		global $pmb_notice_reduit_format;

		//gen. statut
		if ($this->notice->statut) {
			$rqt_st = "SELECT class_html , gestion_libelle FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
			$res_st = pmb_mysql_query($rqt_st) or die ($rqt_st. " ".pmb_mysql_error()) ;
			$class_html = " class='".pmb_mysql_result($res_st, 0, 0)."' ";
			$txt = pmb_mysql_result($res_st, 0, 1);
		} else {
			$class_html = " class='statutnot1' " ;
			$txt = "" ;
		}
		if ($this->notice->commentaire_gestion) {
			if ($txt) $txt .= ":\r\n".$this->notice->commentaire_gestion ;
			else $txt = $this->notice->commentaire_gestion ;
		}
		if ($txt) {
			$statut = "<small><span $class_html style='margin-right: 3px;'><a href=# onmouseover=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display='none'; \"><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></a></span></small>";
			$statut .= "<div id='zoom_statut".$this->notice_id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'><b>".nl2br(htmlentities($txt,ENT_QUOTES, $charset))."</b></div>" ;
		} else $statut = "<small><span $class_html style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span></small>";
		$this->statut = $statut ;


		//aff. titre série
		if($this->notice->tparent_id) {
			$parent = new serie($this->notice->tparent_id);
			$this->tit_serie = $parent->name;
			$this->header.= $this->tit_serie;
			if($this->notice->tnvol) {
				$this->header .= ',&nbsp;'.$this->notice->tnvol;
			}
		}

		//aff. titre1
		$this->tit1 = $this->notice->tit1;
		$this->tit_serie ? $this->header.= '.&nbsp;'.$this->tit1 : $this->header.= $this->tit1;

		//aff. auteur1
		$as = array_search ("0", $this->responsabilites["responsabilites"]);
		$aut1_libelle = array() ;
		$lib_auteur='';
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$auteur = new auteur($auteur_0["id"]);
			if ($auteur->get_isbd()) {
				$lib_auteur=$auteur->get_isbd();
			}
		} else {
			$as = array_keys ($this->responsabilites["responsabilites"], "1" );
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice];
				$auteur = new auteur($auteur_1["id"]);
				$aut1_libelle[]= $auteur->get_isbd();
			}
			$auteurs_liste = implode ("; ",$aut1_libelle) ;
			if ($auteurs_liste) {
				$this->header .= ' / '. $auteurs_liste;
				$lib_auteur=$auteurs_liste;
			}
		}
		if ($lib_auteur!='') {
			$this->header .= ' / '. $lib_auteur;
		}

		//aff. annee
		switch ($pmb_notice_reduit_format) {
			case '1':
				if ($this->notice->year != '') $this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
				break;
			case "2":
				if ($this->notice->year != '') $this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
				if ($this->notice->code != '') $this->header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
				break;
			default :
				break;
		}

		//renv. code
		$this->code = $this->notice->code;

		//renvoi titre
		$this->titre = $this->tit_serie;
		if($this->notice->tnvol) {
			$this->titre.= ',&nbsp;'.$this->notice->tnvol;
		}
		$this->titre ? $this->titre .= '.&nbsp;'.$this->tit1 : $this->titre = $this->tit1;
		$this->titre = htmlentities($this->titre,ENT_NOQUOTES,$charset);

		//renv. auteur1
		$this->auteur1 = htmlentities($lib_auteur,ENT_NOQUOTES,$charset);

		//renv. editeur, collection
		if($this->notice->subcoll_id) {
			$collection = new subcollection($this->notice->subcoll_id);
			$ed_obj = new editeur($collection->editeur) ;
			$this->editeur1 = htmlentities($ed_obj->get_isbd(),ENT_NOQUOTES,$charset);
			$this->collection = htmlentities($collection->get_isbd(),ENT_NOQUOTES,$charset);
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$ed_obj = new editeur($collection->parent) ;
			$this->editeur1 = htmlentities($ed_obj->get_isbd(),ENT_NOQUOTES,$charset);
			$this->collection = htmlentities($collection->get_isbd(),ENT_NOQUOTES,$charset);
		} elseif ($this->notice->ed1_id) {
			$editeur = new editeur($this->notice->ed1_id);
			$this->editeur1 = htmlentities($editeur->get_isbd(),ENT_NOQUOTES,$charset);
		}
		$this->ed_date = htmlentities($this->notice->year,ENT_NOQUOTES,$charset);
		//renv. prix
		$this->prix=htmlentities($this->notice->prix,ENT_NOQUOTES,$charset);
	}


	// creation contenu
	public function doContent() {
		global $tdoc;
		global $fonction_auteur;
		global $msg;

		//mention titre
		$this->isbd = $this->titre;
		$this->isbd .= ' ['.$tdoc->table[$this->notice->typdoc].']';
		if($this->notice->tit3) $this->isbd .= "&nbsp;= ".$this->notice->tit3;
		if($this->notice->tit4) $this->isbd .= "&nbsp;: ".$this->notice->tit4;
		if($this->notice->tit2) $this->isbd .= "&nbsp;; ".$this->notice->tit2;

		//mention responsabilité
		$mention_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]);
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$auteur = new auteur($auteur_0["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_0["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_0["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}
		$as = array_keys ($this->responsabilites["responsabilites"], "1" );
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_1["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_1["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_1["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}
		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_2["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_2["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_2["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}
		$libelle_mention_resp = implode ("; ",$mention_resp) ;
		if($libelle_mention_resp) {
				$this->isbd .= "&nbsp;/ $libelle_mention_resp" ;
		}

		// mention edition
		if($this->notice->mention_edition) {
			$this->isbd .= ".&nbsp;-&nbsp;".$this->notice->mention_edition;
		}

		// zone de l'adresse
		// on récupère la collection au passage, si besoin est
		$editeurs = '';
		$collections = '';
		if($this->notice->subcoll_id) {
			$collection = new subcollection($this->notice->subcoll_id);
			$ed_obj = new editeur($collection->editeur) ;
			$editeurs .= $ed_obj->get_isbd();
			$collections = $collection->get_isbd();
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$ed_obj = new editeur($collection->parent) ;
				$editeurs .= $ed_obj->get_isbd();
				$collections = $collection->get_isbd();
		} elseif ($this->notice->ed1_id) {
			$editeur = new editeur($this->notice->ed1_id);
			$editeurs .= $editeur->get_isbd();
		}
		if($this->notice->ed2_id) {
			$editeur = new editeur($this->notice->ed2_id);
			$ed_isbd=$editeur->get_isbd();
			if ($editeurs) {
			    $editeurs .= '&nbsp;; '.$ed_isbd;
			} else {
			    $editeurs = $ed_isbd;
			}
		}
		if($this->notice->year) {
		    if ($editeurs) {
		        $editeurs .= ', '.$this->notice->year;
		    } else {
		        $editeurs = $this->notice->year;
		    }
		}
		if ($editeurs) {
			$this->isbd .= ".&nbsp;-&nbsp;$editeurs";
		}

		// zone de la collation
		$collation = '';
		if($this->notice->npages) {
			$collation .= $this->notice->npages;
		}
		if($this->notice->ill) {
			$collation .= '&nbsp;: '.$this->notice->ill;
		}
		if($this->notice->size) {
			$collation .= '&nbsp;; '.$this->notice->size;
		}
		if($this->notice->accomp) {
			$collation .= '&nbsp;+ '.$this->notice->accomp;
		}
		if($collation) {
			$this->isbd .= ".&nbsp;-&nbsp;$collation";
		}
		if($collections) {
			if($this->notice->nocoll) {
				$collections .= '; '.$this->notice->nocoll;
			}
			$this->isbd .= ".&nbsp;-&nbsp;($collections)".' ';
		}

		if(substr(trim($this->isbd), -1) != "."){
			$this->isbd .= '.';
		}
		$zoneNote = '';
		// ISBN ou NO. commercial
		if($this->notice->code) {
			if(isISBN($this->notice->code)) {
				$zoneNote = $msg['isbd_notice_isbn'].' ';
			}
			$zoneNote .= $this->notice->code;
		}

		//prix code
		if($this->notice->prix) {
			if($this->notice->code) {
				$zoneNote .= '&nbsp;: '.$this->notice->prix;
			} else {
				if ($zoneNote) {
					$zoneNote .= '&nbsp; '.$this->notice->prix;
				} else {
					$zoneNote = $this->notice->prix;
				}
			}
		}
		if($zoneNote) {
			$this->isbd .= "<br /><br />$zoneNote.";
		}

		//Indexation décimale
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$indexint_isbd=$indexint->display;
			$this->isbd .= "<br /><b>".$msg['indexint_catal_title']."</b>&nbsp;: ".$indexint_isbd;
		}
	}


	//génération du template javascript
	public function finalize() {

		global $msg,$charset;

		$javascript_template ="
						<div id='el_!!id!!_Parent' class='notice-parent'><span class='acq_sel_display_middle'>";
		if(trim($this->form_checker)){
			$javascript_template.="<input type='checkbox' name='".$this->form_checker."[]' id='".$this->form_checker."[!!id!!]' value='!!id!!'";
			if ($this->action) {
				if (count($this->action_values)) {
					foreach($this->action_values as $v) {
						$javascript_template.=" attr_".$v."=\"".htmlentities($this->$v,ENT_QUOTES,$charset)."\"";
					}
				}
			}
			$javascript_template.=">";
		}
		$javascript_template.="
							<img src='".get_url_icon('plus.gif')."' name='imEx' id='el_!!id!!_Img' title='".$msg['admin_param_detail']."' onClick=\"expandBase('el_!!id!!_', true); return false;\" />
							</span><span class='notice-heada'>!!header!!</span>
							<div id='el_!!id!!_Child' class='notice-child' style='width:inherit;display:none;' >
	   					 		!!isbd!!
							</div>
						</div>";

		if ($this->action) {
			$this->header = str_replace('!!display!!', $this->header, $this->action);
			$this->header = $this->statut.$this->header;
			$this->header = str_replace('!!id!!', $this->notice_id, $this->header);
			if (count($this->action_values)) {
				foreach($this->action_values as $v) {
					$this->header = str_replace("!!$v!!", htmlentities(addslashes($this->$v),ENT_QUOTES,$charset), $this->header);
				}
			}
		}

		$this->result = str_replace('!!id!!', $this->notice_id, $javascript_template);
		$this->result = str_replace('!!header!!', $this->header, $this->result);

		$this->result = str_replace('!!isbd!!', $this->isbd, $this->result);
	}
}



// définition de la classe d'affichage des périodiques en liste pour selecteur
class sel_serial_display {

	public $notice_id = 0;				// id de la notice à afficher
	public $notice;					// objet notice (tel que fetché dans la table 'notices'
	public $header	= '';				// chaine accueillant le chapeau de notice (peut-être cliquable)

	public $tit1 = '';					// valeur du titre 1
	public $result = '';				// affichage final
	public $level = 1;					// niveau d'affichage
	public $isbd = '';					// isbd de la notice
	public $nb_bull = 0;				// nombre de bulletins
	public $nb_expl = 0;				// nombre d'exemplaires
	public $nb_art = 0;				// nombre d'articles
	public $responsabilites = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
	public $show_statut = 1;
	public $aff_statut = '' ; 			// carré de couleur pour signaler le statut de la notice

  	public $base_url = '';				// URL à associer aux éléments cliquables
  	public $action = '';				// URL à associer aux notices
	public $action_values = array();	// tableau des elements à modifier dans l'action


	// constructeur
	public function __construct($notice_id, $base_url) {
		$this->notice_id = intval($notice_id);
	  	$this->base_url = $base_url;
	}

	//creation formulaire
	public function doForm() {
		$this->getData();
		$this->responsabilites = get_notice_authors($this->notice_id) ;
		$this->doHeader();
		$this->initJavascript();
		$this->doContent();
		$this->finalize();
		return;
	}


	// récupération des valeurs en table
	public function getData() {
		$q = "SELECT * FROM notices WHERE notice_id=".$this->notice_id;
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			$this->notice = pmb_mysql_fetch_object($r);
		}
	}


	// creation header
	public function doHeader() {
		global $charset;

		if ($this->notice->statut) {
			$rqt_st = "SELECT class_html , gestion_libelle FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
			$res_st = pmb_mysql_query($rqt_st);
			$class_html = " class='".pmb_mysql_result($res_st, 0, 0)."' ";
			$txt = pmb_mysql_result($res_st, 0, 1);
		} else {
			$class_html = " class='statutnot1' " ;
			$txt = '' ;
		}
		if ($this->notice->commentaire_gestion) {
			if ($txt) {
				$txt .= ":\r\n".$this->notice->commentaire_gestion ;
			} else {
				$txt = $this->notice->commentaire_gestion ;
			}
		}
		if ($txt) {
			$statut = "<small><span $class_html style='margin-right: 3px;'><a href=# onmouseover=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display='none'; \"><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></a></span></small>";
			$statut .= "<div id='zoom_statut".$this->notice_id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'><b>".nl2br(htmlentities($txt,ENT_QUOTES, $charset))."</b></div>" ;
		} else {
			$statut = "<small><span $class_html style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span></small>";
		}
		$this->aff_statut = $statut ;

		$this->header = htmlentities($this->notice->tit1,ENT_QUOTES, $charset);

		//$this->responsabilites
		$aut1_libelle = array() ;
		$lib_auteur='';
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$auteur = new auteur($auteur_0["id"]);
			if ($auteur->get_isbd()) {
				$lib_auteur=$auteur->get_isbd();
			}
		} else {
			$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
				$auteur = new auteur($auteur_1["id"]);
				$aut1_libelle[]= $auteur->get_isbd();
			}
			$auteurs_liste = implode ("; ",$aut1_libelle) ;
			if ($auteurs_liste) {
				$this->header .= ' / '. $auteurs_liste ;
				$lib_auteur=$auteurs_liste;
			}
		}
		if ($lib_auteur!='') {
			$this->header .= ' / '. $lib_auteur;
		}

		if ($this->action) {
			$this->header = str_replace('!!display!!', $this->header, $this->action);
			$this->header = str_replace('!!aut_id!!', $this->notice_id, $this->header);

			if (count($this->action_values)) {
				foreach($this->action_values as $v) {
					$this->header = str_replace("!!$v!!", addslashes($this->$v), $this->header);
				}
			}
		}

		if ($this->show_statut) {
			$this->header = $this->aff_statut.$this->header ;
		}
	}


	// génération du template javascript
	public function initJavascript() {
		$javascript_template ="
						<div id='el_!!id!!_Parent' class='notice-parent'>
                            ".get_expandBase_button('el_!!id!!_')."
							<span class='notice-heada'>!!header!!</span>
							<div id='el_!!id!!_Child' class='notice-child' style='width:inherit;display:none;' >
	   					 		!!serial_type!! !!isbd!!
							</div>
						</div>";
		$this->result = str_replace('!!id!!', $this->notice_id, $javascript_template);
		$this->result = str_replace('!!header!!', $this->header, $this->result);
	}


	// creation contenu
	public function doContent() {
		global $msg;
		global $fonction_auteur;
		global $pmb_etat_collections_localise, $pmb_droits_explr_localises, $explr_visible_mod;

		$this->isbd = $this->notice->tit1;

		// constitution de la mention de titre
		$tit3 = $this->notice->tit3;
		$tit4 = $this->notice->tit4;
		if($tit3) $this->isbd .= "&nbsp;= $tit3";
		if($tit4) $this->isbd .= "&nbsp;: $tit4";

		// constitution de la mention de responsabilité
		//$this->responsabilites
		$mention_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$auteur = new auteur($auteur_0["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_0["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_0["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}

		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_1["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_1["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_1["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}

		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_2["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_2["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_2["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}

		$libelle_mention_resp = implode ("; ",$mention_resp) ;
		if($libelle_mention_resp) {
			$this->isbd .= "&nbsp;/ ". $libelle_mention_resp ." " ;
		}

		// zone de l'adresse
		if($this->notice->ed1_id) {
			$editeur = new editeur($this->notice->ed1_id);
			$editeurs = $editeur->get_isbd();
		}
		if($this->notice->ed2_id) {
			$editeur = new editeur($this->notice->ed2_id);
			$ed_isbd=$editeur->get_isbd();
			if (!empty($editeurs)) {
				$editeurs .= '&nbsp;; '.$ed_isbd;
			} else {
				$editeurs = $ed_isbd;
			}
		}

		if($this->notice->year) {
		    if (!empty($editeurs)) {
		        $editeurs .= ', '.$this->notice->year;
		    } else {
		        $editeurs = $this->notice->year;
		    }
		}

		if (!empty($editeurs)) {
			$this->isbd .= ".&nbsp;-&nbsp;$editeurs";
		}

		//code (ISSN,...)
		if ($this->notice->code) $this->isbd .="<br /><b>{$msg[165]}</b>&nbsp;: ".$this->notice->code;

		//Indexation décimale
		if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$indexint_isbd=$indexint->display;
			$this->isbd .= "<br /><b>".$msg['indexint_catal_title']."</b>&nbsp;: ".$indexint_isbd;
		}

		// Si notice-mère alors on compte le nombre de numéros (bulletins)
		if($this->notice->niveau_biblio=="s") {
			$requete = "SELECT * FROM bulletins WHERE bulletin_notice=".$this->notice_id;
			$Query = pmb_mysql_query($requete);
			$this->nb_bull=pmb_mysql_num_rows($Query);
			while (($row = pmb_mysql_fetch_array($Query))) {
				$requete2 = "SELECT count( * )  AS nb_art FROM  analysis WHERE analysis_bulletin =".$row['bulletin_id'];
				$Query2 = pmb_mysql_query($requete2);
				$analysis_array=pmb_mysql_fetch_array($Query2);
				$this->nb_art+=$analysis_array['nb_art'];
				$requete3 = "SELECT count( expL_id ) AS nb_expl FROM  exemplaires WHERE expl_bulletin =".$row['bulletin_id'];
				$Query3 = pmb_mysql_query($requete3);
				$expl_array=pmb_mysql_fetch_array($Query3);
				$this->nb_expl+=$expl_array['nb_expl'];
			}

			// Cas général : au moins un bulletin
			if (pmb_mysql_num_rows($Query)>0)
				{$this->isbd .="<br /><br />\n
				<b>".$msg["serial_bulletinage_etat"]."</b>
				<table style='border:0px' class='expl-list' role='presentation'>
				    <tr>
                        <td>
                            <strong>$this->nb_bull</strong> ".$msg["serial_nb_bulletin"]."
            				<strong>$this->nb_expl</strong> ".$msg["bulletin_nb_ex"]."
            				<strong>$this->nb_art</strong> ".$msg["serial_nb_articles"]."
				        </td>
				    </tr>
                </table>";
			} else { // 0 bulletin
				$this->isbd .="<br /><br />\n
				<b>".$msg["serial_bulletinage_etat"]."</b>
				<table style='border:0px' class='expl-list' role='presentation'>
    				<tr>
                        <td>
                            <strong>$this->nb_bull</strong>
            				".$msg["serial_nb_bulletin"]." : <strong>".$msg["bull_no_expl"]."</strong>
                        </td>
    				</tr>
                </table>";
			}

			//état des collections
			if ($pmb_etat_collections_localise&&$pmb_droits_explr_localises&&$explr_visible_mod) {
				$restrict_location=" and location_id in (".$explr_visible_mod.") and idlocation=location_id";
				$table_location=",docs_location";
				$select_location=",location_libelle";
			} else {
				$restrict_location = " group by id_serial";
				$select_location = "";
				$table_location = "";
			}
			$rqt="select state_collections$select_location from collections_state$table_location where id_serial=".$this->notice_id.$restrict_location;
			$execute_query=pmb_mysql_query($rqt);
			if ($execute_query) {
				if (pmb_mysql_num_rows($execute_query)) {
					$bool = false;
					$affichage = "<br /><strong>".$msg["4001"]."</strong><br />";
					while ($r = pmb_mysql_fetch_object($execute_query)) {
						if (!empty($r->state_collections)) {
						    if (!empty($r->location_libelle)) {
						        $affichage .= "<strong>".$r->location_libelle."</strong> : ";
						    }
							$affichage .= $r->state_collections."<br />\n";
							$bool = true;
						}
					}
					if ($bool == true) {
					    $this->isbd .= $affichage;
					}
				}
			}
		}
		return;
	}


	// finalisation du résultat
	public function finalize() {
		global $msg ;

		$this->result = str_replace('!!isbd!!', $this->isbd, $this->result);
		$this->result = str_replace('!!serial_type!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>", $this->result);
	}

}



//définition de la classe d'affichage des bulletins en liste pour selecteur
class sel_bulletin_display {

	public $result = '';
	public $bulletin_id=0;
	public $bulletin = '';
	public $nb_expl=0;

 	public $base_url = '';				// URL à associer aux éléments cliquables
  	public $action = '';				// URL à associer aux notices
	public $action_values = array();	// tableau des elements à modifier dans l'action

	public $titre = '';				//titre renvoye
	public $editeur1 = '';				//editeur 1 renvoye
	public $numero = '';				//numero renvoye
	public $aff_date = '';				//date renvoyee
	public $prix = '0.00';				//prix renvoye
	public $code = '';					//code renvoye

	public $form_checker = ''; //Affichage d'une case à cocher sur le template

	//constructeur
	public function __construct($bulletin_id, $base_url, $form_checker='') {
		$this->bulletin_id=intval($bulletin_id);
		$this->base_url=$base_url;
		$this->form_checker=$form_checker;
	}

	public function doForm() {
		global $charset;

		$this->getData();

		$this->titre = $this->bulletin->tit1;
		if ($this->bulletin->ed1_id) {
			$ed1= new editeur($this->bulletin->ed1_id);
			$this->editeur1 = $ed1->get_isbd();
		}
		if ($this->bulletin->bulletin_numero!=='') {
			$this->numero = $this->bulletin->bulletin_numero;
		}
		if ($this->bulletin->libelle_periode) {
			$this->aff_date = "(".$this->bulletin->libelle_periode.")";
		}
		if ($this->bulletin->date_date!='0000-00-00') {
			$this->aff_date.= " [".$this->bulletin->aff_date_date."]";
		}
		if ($this->bulletin->bulletin_cb!='') {
			$this->code = $this->bulletin->bulletin_cb;
		}

		$aff = $this->titre;
		if($this->numero) {
			$aff.= '. '.$this->numero;
		}
		$aff.= ' '.$this->aff_date;

		$aff = htmlentities($aff, ENT_QUOTES, $charset);
		if ($this->action) {
			$aff= str_replace('!!display!!', "<b>$aff</b>", $this->action);
			if (count($this->action_values)) {
				foreach($this->action_values as $v) {
					$aff = str_replace("!!$v!!", addslashes($this->$v), $aff);
				}
			}
		}
		if(trim($this->form_checker)){
			$tmpAff ="<span class='acq_sel_display_middle'>
					<input type='checkbox' name='".$this->form_checker."[]' id='".$this->form_checker."[".$this->bulletin_id."]' value='".$this->bulletin_id."'";
			if ($this->action) {
				if (count($this->action_values)) {
					foreach($this->action_values as $v) {
						$tmpAff.=" attr_".$v."=\"".htmlentities($this->$v,ENT_QUOTES,$charset)."\"";
					}
				}
			}
			$this->result=$tmpAff.">".$aff."</span>";
		}else{
			$this->result = $aff;
		}
	}

	// récupération des valeurs en table
	public function getData() {
		global $msg;

		$q = "SELECT notices.tit1, notices.ed1_id, notices.code, bulletins.*, date_format(bulletins.date_date, '".$msg['format_date']."') as aff_date_date FROM bulletins join notices on bulletin_notice=notice_id WHERE bulletin_id='".$this->bulletin_id."' ";
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {
			$this->bulletin = pmb_mysql_fetch_object($r);
		}

		$q = "select count(*) from exemplaires where expl_bulletin='".$this->bulletin_id."' ";
		$r = pmb_mysql_query($q);
		$this->nb_expl = pmb_mysql_result($r,0,0);

	}
}



//définition de la classe d'affichage des abonnements en liste pour selecteur
class sel_abt_display {

	public $abt_id=0;					//id abonnement
	public $abt = '';					//objet abonnement
	public $header = '';				//entete
	public $result = '';				// affichage final
	public $isbd = '';					// isbd notice
	public $responsabilites =	array("responsabilites" => array(),"auteurs" => array());  //auteurs
	public $aff_date_echeance = '';	//date echeance abt actuel
	public $aff_nb_recipients = 0;	//nombre de destinataires abt actuel

 	public $base_url = '';				// URL à associer aux éléments cliquables
  	public $action = '';				// URL à associer aux notices
	public $action_values = array();	// tableau des elements à modifier dans l'action

	public $code = '';					//code renvoye
	public $titre = '';				//titre renvoye
	public $editeur1 = '';				//editeur 1 renvoye
	public $periodicite = '';			//periodicite
	public $duree = '';				//duree abt
	public $prix = '0.00';				//prix renvoye
	public $aff_date_debut = '';		//date debut abt renvoyee
	public $abt_name = '';		//nom abonnement renvoyee

	public $form_checker = ''; //Affichage d'une case à cocher sur le template

//TODO
	public $nb_num = 0;				//nb numeros


	//constructeur
	public function __construct($abt_id, $base_url, $form_checker='') {
		$this->abt_id=intval($abt_id);
		$this->base_url=$base_url;
		$this->form_checker=$form_checker;
	}


	//creation formulaire
	public function doForm() {
		$this->getData();
		$this->responsabilites = get_notice_authors($this->abt->num_notice) ;
		$this->doHeader();
		$this->doContent();
		$this->finalize();
	}


	// récupération des valeurs en table
	public function getData() {
		$q = "SELECT abts_abts.*, ";
		$q.= "notices.tit1, notices.tit3, notices.tit4, notices.ed1_id, notices.ed2_id, notices.year, notices.code, notices.prix, ";
		$q.= "abts_modeles.num_periodicite, abts_periodicites.libelle, ";
		$q.= "docs_location.location_libelle ";
		$q.= "FROM abts_abts ";
		$q.= "join abts_abts_modeles on abts_abts.abt_id=abts_abts_modeles.abt_id ";
		$q.= "join abts_modeles on abts_abts_modeles.modele_id=abts_modeles.modele_id ";
		$q.= "left join abts_periodicites on abts_modeles.num_periodicite=abts_periodicites.periodicite_id ";
		$q.= "join notices on abts_abts.num_notice=notices.notice_id ";
		$q.= "join docs_location on abts_abts.location_id=docs_location.idlocation ";
		$q.= "where abts_abts.abt_id='".$this->abt_id."' ";
		$r = pmb_mysql_query($q);
		$this->abt = pmb_mysql_fetch_object($r);
	}


	// creation header
	public function doHeader() {
		global $msg, $charset;
		global $pmb_serialcirc_active;

		//aff. nom pério
		$this->header = htmlentities($this->abt->tit1, ENT_QUOTES, $charset);

		//aff. nom abonnement
		$this->header.= "&nbsp;/&nbsp;".htmlentities($this->abt->abt_name, ENT_QUOTES, $charset);

		//aff. localisation
		$this->header.= "&nbsp;/&nbsp;".htmlentities($this->abt->location_libelle, ENT_QUOTES, $charset);

		//aff. periodicite
		if ($this->abt->num_periodicite) {
			$this->header.= "&nbsp;(".htmlentities($this->abt->libelle, ENT_QUOTES, $charset);
		} else {
			$this->header.= "&nbsp;(".htmlentities($msg['abonnements_periodicite_manuel'], ENT_QUOTES, $charset);
		}

		//aff. duree
		$this->header.= "&nbsp;-&nbsp;".$this->abt->duree_abonnement."&nbsp;".htmlentities($msg['abonnements_periodicite_unite_mois'], ENT_QUOTES, $charset).")";

		//aff. date echeance
		$this->aff_date_echeance = format_date($this->abt->date_fin);

		//aff. nb destinataire
		if($pmb_serialcirc_active) {
		    $serialcirc_diff=new serialcirc_diff(0,$this->abt_id);
		    $this->aff_nb_recipients = $serialcirc_diff->diffusion_nb_recipients;
		}

		//renv. nom abonnement
		$this->abt_name.= $this->abt->abt_name;

		//renv. code
		$this->code=$this->abt->code;

		//renv. titre
		$this->titre = $this->abt->tit1;
		if ($this->abt->tit3) $this->titre.= "&nbsp;= ".$this->abt->tit3;
		if ($this->abt->tit4) $this->titre.= "&nbsp;: ".$this->abt->tit4;

		//renv. editeur1
		if ($this->abt->ed1_id) {
			$editeur = new editeur($this->abt->ed1_id);
			$this->editeur1 = $editeur->get_isbd();
		}

		//renv. periodicite
		if ($this->abt->num_periodicite) {
			$this->periodicite=$this->abt->libelle;
		}

		//renv. duree
		$this->duree = $this->abt->duree_abonnement;

		//renv. date debut abt
		$q = "select date_add('".$this->abt->date_fin."', interval 1 day) as date_debut, ";
		$q.= " date_add('".$this->abt->date_fin."', interval ".$this->duree." month) as date_fin ";
		$r = pmb_mysql_query($q);
		$obj = pmb_mysql_fetch_object($r);
		$this->aff_date_debut = format_date($obj->date_debut);

//TODO A revoir, car le prix n'est pas accessible sur les notices de perio
		//renv. prix
		if ($this->abt->prix!=='') $this->prix=$this->abt->prix;

	}


	// creation contenu
	public function doContent() {
		global $fonction_auteur, $msg;

		//mention titre
		$this->isbd = $this->titre;

		//mention responsabilité
		$mention_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$auteur = new auteur($auteur_0["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_0["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_0["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}
		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_1["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_1["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_1["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}
		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_2["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_2["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_2["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}
		$libelle_mention_resp = implode ("; ",$mention_resp) ;
		if($libelle_mention_resp) {
			$this->isbd .= "&nbsp;/ ". $libelle_mention_resp ." " ;
		}

		// zone de l'adresse
		$ed_isbd = '';
		if($this->abt->ed1_id) {
			$editeur = new editeur($this->abt->ed1_id);
			$ed_isbd .= $editeur->get_isbd();
		}
		if($this->abt->year) {
		    if ($ed_isbd) {
		        $ed_isbd .= ', '.$this->abt->year;
		    } else {
		        $ed_isbd = $this->abt->year;
		    }
		}
		if($ed_isbd) {
			$this->isbd .= ".&nbsp;-&nbsp;$ed_isbd";
		}

		//code (ISSN,...)
		if ($this->abt->code) $this->isbd .="<br /><b>{$msg[165]}</b>&nbsp;: ".$this->abt->code;
	}


	//génération du template javascript
	public function finalize() {

		global $msg,$charset;

		$javascript_template ="
			<div id='el_!!id!!_Parent' class='notice-parent'><span class='acq_sel_display_middle'>";
		if(trim($this->form_checker)){
			$javascript_template.="<input type='checkbox' name='".$this->form_checker."[]' id='".$this->form_checker."[!!id!!]' value='!!id!!'";
			if ($this->action) {
				if (count($this->action_values)) {
					foreach($this->action_values as $v) {
						$javascript_template.=" attr_".$v."=\"".htmlentities($this->$v,ENT_QUOTES,$charset)."\"";
					}
				}
			}
			$javascript_template.=">";
		}
		$javascript_template.="
				<img src='".get_url_icon('plus.gif')."' name='imEx' id='el_!!id!!_Img' title='".$msg['admin_param_detail']."' onClick=\"expandBase('el_!!id!!_', true); return false;\" />
				</span><span class='notice-heada'>!!header!!</span>
				<div id='el_!!id!!_Child' class='notice-child' style='width:inherit;display:none;' >
   			 		!!serial_type!! !!isbd!!
				</div>
			</div>";

		if ($this->action) {
			$this->header = str_replace('!!display!!', $this->header, $this->action);
			$this->header = str_replace('!!id!!', $this->abt_id, $this->header);

			if (count($this->action_values)) {
				foreach($this->action_values as $v) {
					$this->header = str_replace("!!$v!!", htmlentities(addslashes($this->$v),ENT_QUOTES,$charset), $this->header);
				}
			}
		}

		$this->result = str_replace('!!id!!', $this->abt_id, $javascript_template);
		$this->result = str_replace('!!header!!', $this->header, $this->result);

		$this->result = str_replace('!!isbd!!', $this->isbd, $this->result);
		$this->result = str_replace('!!serial_type!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>", $this->result);
	}
}


require_once("$class_path/frais.class.php");
require_once($class_path.'/tva_achats.class.php');
//Classe d'affichage des frais dans un selecteur
class sel_frais_display extends frais {

	public $result='';
	public $lib_montant='';
	public $taux_tva = '0.00';

	public $base_url = '';			//URL a associer aux elements cliquables
	public $action = '';
	public $action_values = array();

	public $form_checker = ''; //Affichage d'une case à cocher sur le template

	//Constructeur.
	public function __construct($id_frais, $base_url, $form_checker='') {
		$id_frais = intval($id_frais);
		parent::__construct($id_frais);
		$this->base_url=$base_url;
		$this->form_checker=$form_checker;
	}

	public function doForm(){

		global $charset;
		global $acquisition_gestion_tva, $pmb_gestion_devise;

		if(!$this->id_frais) return;

		if ($acquisition_gestion_tva && $this->num_tva_achat) {
			$tva = new tva_achats($this->num_tva_achat);
			$this->taux_tva = $tva->taux_tva;
		}

		$aff = htmlentities($this->libelle, ENT_QUOTES, $charset);
		if ($this->action) {
			$aff= str_replace('!!display!!', "<b>$aff</b>", $this->action);
			if (count($this->action_values)) {
				foreach($this->action_values as $v) {
					$aff = str_replace("!!$v!!", addslashes($this->$v), $aff);
				}
			}
		}
		if(trim($this->form_checker)){
			$tmpAff ="<span class='acq_sel_display_middle'>
					<input type='checkbox' name='".$this->form_checker."[]' id='".$this->form_checker."[".$this->id_frais."]' value='".$this->id_frais."'";
			if ($this->action) {
				if (count($this->action_values)) {
					foreach($this->action_values as $v) {
						$tmpAff.=" attr_".$v."=\"".htmlentities($this->$v,ENT_QUOTES,$charset)."\"";
					}
				}
			}
			$this->result=$tmpAff.">".$aff."</span>";
		}else{
			$this->result = $aff;
		}
		$this->lib_montant = $this->montant.'&nbsp;'.$pmb_gestion_devise;
	}

}


// définition de la classe d'affichage des articles en liste pour selecteur
class sel_article_display {

	public $notice_id = 0;		//id notice
	public $notice = '';		//objet notice
  	public $header = '';		//entete
	public $result	= '';		//affichage final
	public $isbd = '';			//isbd notice
	public $responsabilites =	array("responsabilites" => array(),"auteurs" => array());  //auteurs
	public $statut = '' ;		//statut notice
	public $tit_serie = '';	//titre serie
	public $tit1 = '';			//titre 1

	public $parent_title = '';
	public $parent_numero = '';
	public $parent_date = '';
	public $parent_date_date = '';
	public $parent_aff_date_date = '';

	public $base_url = '';					//URL a associer aux elements cliquables
  	public $action = '';					//action a effectuer pour retour des parametres
	public $action_values = array();		//tableau des elements à modifier dans l'action

	public $code = '';			//isbn ou code EAN de la notice à afficher
	public $titre = '';		//titre renvoye
	public $auteur1 = '';		//auteur1 renvoye
	public $in_bull = '';		//lien bulletin renvoye
	public $prix = '0.00';		//prix renvoye

	public $form_checker = ''; //Affichage d'une case à cocher sur le template


	// constructeur
	public function __construct($notice_id, $base_url, $form_checker='') {
		$this->notice_id = intval($notice_id);
	  	$this->base_url = $base_url;
	  	$this->form_checker = $form_checker;
	}


	//creation formulaire
	public function doForm() {
		$this->getData();
		$this->responsabilites = get_notice_authors($this->notice_id) ;
		$this->doHeader();
		$this->doContent();
		$this->finalize();
	}


	// récupération des valeurs en table
	public function getData() {
		global $msg;

		$q = "SELECT * FROM notices WHERE notice_id='".$this->notice_id."' ";
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {
			$this->notice = pmb_mysql_fetch_object($r);
		}
		// récupération des données du bulletin et de la notice apparentée
		$requete = "SELECT b.tit1,c.*,date_format(date_date, '".$msg["format_date"]."') as aff_date_date ";
		$requete.= "from analysis a, notices b, bulletins c ";
		$requete.= "WHERE a.analysis_notice=".$this->notice_id." ";
		$requete.= "AND c.bulletin_id=a.analysis_bulletin ";
		$requete .= "AND c.bulletin_notice=b.notice_id ";
		$requete.= "LIMIT 1";
		$myQuery = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($myQuery)) {
			$parent = pmb_mysql_fetch_object($myQuery);
			$this->parent_title = $parent->tit1;
			$this->parent_numero = $parent->bulletin_numero;
			$this->parent_date = $parent->mention_date;
			$this->parent_date_date = $parent->date_date;
			$this->parent_aff_date_date = $parent->aff_date_date;
		}
	}

	// creation header
	public function doHeader() {
		global $charset;
		global $pmb_notice_reduit_format;

		//gen. statut
		if ($this->notice->statut) {
			$rqt_st = "SELECT class_html , gestion_libelle FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
			$res_st = pmb_mysql_query($rqt_st) or die ($rqt_st. " ".pmb_mysql_error()) ;
			$class_html = " class='".pmb_mysql_result($res_st, 0, 0)."' ";
			$txt = pmb_mysql_result($res_st, 0, 1) ;
		} else {
			$class_html = " class='statutnot1' " ;
			$txt = "" ;
		}
		if ($this->notice->commentaire_gestion) {
			if ($txt) $txt .= ":\r\n".$this->notice->commentaire_gestion ;
			else $txt = $this->notice->commentaire_gestion ;
		}
		if ($txt) {
			$statut = "<small><span $class_html style='margin-right: 3px;'><a href=# onmouseover=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display='none'; \"><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></a></span></small>";
			$statut .= "<div id='zoom_statut".$this->notice_id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'><b>".nl2br(htmlentities($txt,ENT_QUOTES, $charset))."</b></div>" ;
		} else $statut = "<small><span $class_html style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span></small>";
		$this->statut = $statut ;

		$this->tit1 = $this->notice->tit1;
		$this->header.= $this->tit1;

		//aff. auteur1
		$as = array_search ("0", $this->responsabilites["responsabilites"]);
		$aut1_libelle = array() ;
		$lib_auteur='';
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$auteur = new auteur($auteur_0["id"]);
			if ($auteur->get_isbd()) {
				$lib_auteur=$auteur->get_isbd();
			}
		} else {
			$as = array_keys ($this->responsabilites["responsabilites"], "1" );
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice];
				$auteur = new auteur($auteur_1["id"]);
				$aut1_libelle[]= $auteur->get_isbd();
			}
			$auteurs_liste = implode ("; ",$aut1_libelle) ;
			if ($auteurs_liste) {
				$this->header .= ' / '. $auteurs_liste;
				$lib_auteur=$auteurs_liste;
			}
		}
		if ($lib_auteur!='') {
			$this->header .= ' / '. $lib_auteur;
		}

		//renvoi lien bulletin
		$this->in_bull= "in ".$this->parent_title." (".$this->parent_numero." ".($this->parent_date?$this->parent_date:$this->parent_aff_date_date).")";

		$this->header=$this->header." <i>".$this->in_bull."</i> ";

		//aff. annee
		switch ($pmb_notice_reduit_format) {
			case '1':
				if ($this->notice->year != '') $this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
				break;
			case "2":
				if ($this->notice->year != '') $this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
				if ($this->notice->code != '') $this->header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
				break;
			default :
				break;
		}

		//renvoi titre
		$this->titre = $this->tit1;

		//renv. auteur1
		$this->auteur1=$lib_auteur;

		//renv. prix
		$this->prix=$this->notice->prix;

	}


	// creation contenu
	public function doContent() {
		global $tdoc;
		global $fonction_auteur;

		//mention titre
		$this->isbd = $this->titre;
		if($this->notice->tit4) $this->isbd .= "&nbsp;: ".$this->notice->tit4;
		if($this->notice->tit2) $this->isbd .= "&nbsp;; ".$this->notice->tit2;
		$this->isbd .= ' ['.$tdoc->table[$this->notice->typdoc].']';
		//mention responsabilité
		$mention_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]);
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$auteur = new auteur($auteur_0["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_0["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_0["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}
		$as = array_keys ($this->responsabilites["responsabilites"], "1" );
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_1["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_1["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_1["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}
		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_2["id"]);
			$mention_resp_lib = $auteur->get_isbd();
			if ($auteur_2["fonction"]) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_2["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}
		$libelle_mention_resp = implode ("; ",$mention_resp) ;
		if($libelle_mention_resp) {
				$this->isbd .= "&nbsp;/ $libelle_mention_resp" ;
		}

		// zone de la collation
		if($this->notice->npages) {
			$collation = $this->notice->npages;
		}
		if($collation) {
			$this->isbd .= ".&nbsp;-&nbsp;$collation";
		}
		$this->isbd .= '.';

		//prix
		if($this->notice->prix) {
			$zoneNote = $this->notice->prix;
		}
		if($zoneNote) {
			$this->isbd .= "<br /><br />$zoneNote.";
		}
	}


	//génération du template javascript
	public function finalize() {

		global $msg,$charset;

		$javascript_template ="
						<div id='el_!!id!!_Parent' class='notice-parent'><span class='acq_sel_display_middle'>";
		if(trim($this->form_checker)){
			$javascript_template.="<input type='checkbox' name='".$this->form_checker."[]' id='".$this->form_checker."[!!id!!]' value='!!id!!'";
			if ($this->action) {
				if (count($this->action_values)) {
					foreach($this->action_values as $v) {
						$javascript_template.=" attr_".$v."=\"".htmlentities($this->$v,ENT_QUOTES,$charset)."\"";
					}
				}
			}
			$javascript_template.=">";
		}
		$javascript_template.="
							<img src='".get_url_icon('plus.gif')."' name='imEx' id='el_!!id!!_Img' title='".$msg['admin_param_detail']."' onClick=\"expandBase('el_!!id!!_', true); return false;\" />
							</span><span class='notice-heada'>!!header!!</span>
							<div id='el_!!id!!_Child' class='notice-child' style='width:inherit;display:none;' >
	   					 		!!isbd!!
							</div>
						</div>";

		if ($this->action) {
			$this->header = str_replace('!!display!!', $this->header, $this->action);
			$this->header = $this->statut.$this->header;
			$this->header = str_replace('!!id!!', $this->notice_id, $this->header);
			if (count($this->action_values)) {
				foreach($this->action_values as $v) {
					$this->header = str_replace("!!$v!!", htmlentities(addslashes($this->$v),ENT_QUOTES,$charset), $this->header);
				}
			}
		}

		$this->result = str_replace('!!id!!', $this->notice_id, $javascript_template);
		$this->result = str_replace('!!header!!', $this->header, $this->result);

		$this->result = str_replace('!!isbd!!', $this->isbd, $this->result);
	}
}

?>