<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mono_display_unimarc.class.php,v 1.81.2.1.2.1 2025/03/12 17:27:47 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path, $tdoc, $fonction_auteur;

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
require_once($include_path."/notice_authors.inc.php");
require_once($include_path."/notice_categories.inc.php");
require_once($include_path."/explnum.inc.php");
require_once($include_path."/isbn.inc.php");
require_once($include_path."/resa_func.inc.php");
require_once($class_path."/thumbnail.class.php");

if (empty($tdoc)) $tdoc = marc_list_collection::get_instance('doctype');
if (empty($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}
// propriétés pour le selecteur de panier
$cart_click = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=!!id!!&unq=!!unique!!', 'cart')\"";


function cmpexpl($a, $b)
{
	$c1 = isset($a["priority"]) ? $a["priority"] : "";
	$c2 = isset($b["priority"]) ? $b["priority"] : "";
	if ($c1 == $c2) {
		$c1 = isset($a["content"]["v"]) ? $a["content"]["v"] : "";
		$c2 = isset($b["content"]["v"]) ? $b["content"]["v"] : "";
		return strcmp($c1, $c2);
	}
	return $c2-$c1;
}

// définition de la classe d'affichage des monographies en liste
class mono_display_unimarc {
	public $notice_id		= 0;	// id de la notice à afficher
	public $isbn		= 0;	// isbn ou code EAN de la notice à afficher
  	public $notice;			// objet notice (tel que fetchï¿½ dans la table 'notices'
	public $langues = array();
	public $languesorg = array();
  	public $action		= '';	// URL à associer au header
	public $header		= '';	// chaine accueillant le chapeau de notice (peut-être cliquable)
	public $tit_serie		= '';	// titre de série si applicable
	public $tit1		= '';	// valeur du titre 1
	public $result		= '';	// affichage final
	public $level		= 1;	// niveau d'affichage
	public $isbd		= '';	// isbd de la notice en fonction du level défini
	public $expl		= 0;	// flag indiquant si on affiche les infos d'exemplaire
	public $nb_expl	= 0;	//nombre d'exemplaires
	public $link_expl		= '';	// lien associé à un exemplaire
	public $responsabilites =	array("responsabilites" => array(),"auteurs" => array());  // les auteurs
	public $auteurs_principaux;
	public $auteurs_tous;
	public $categories_toutes;
	public $collections;
	public $publishers;
	public $print_mode=0;
	public $show_explnum=1;
	public $no_link;
	public $entrepots_localisations=array();
	public $docnums;
	public $source_id;

	// constructeur------------------------------------------------------------
	public function __construct($id, $level=1, $expl=1, $print=0, $show_explnum=1, $no_link=false, $entrepots_localisations=array()) {
	  	// $id = id de la notice à afficher
	  	// $action	 = URL associée au header
		// $level :
		//		0 : juste le header (titre  / auteur principal avec le lien si applicable)
		// 			suppression des niveaux entre 1 et 6, seul reste level
		//		1 : ISBD seul, pas de note, bouton modif, expl, explnum et rï¿½sas
		// 		6 : cas général détaillé avec notes, categ, langues, indexation... + boutons
		// $expl -> affiche ou non les exemplaires associés

	  	$this->notice_id = intval($id);
		$this->mono_display_fetch_data();
		$this->fetch_auteurs();
		$this->fetch_categories();
		$this->level=$level;
		$this->expl = $expl;
		$this->entrepots_localisations = $entrepots_localisations;

		// mise à jour des catégories
		$this->categories = get_notice_categories($this->notice_id) ;

		$this->do_header();

		switch($level) {
			case 0:
				// là, c'est le niveau 0 : juste le header
				$this->result = $this->header;
				break;
			default:
				// niveau 1 et plus : header + isbd à générer
				$this->init_javascript();
				$this->do_isbd();
				$this->finalize();
				break;
		}
	}

	public function fetch_auteurs() {
		global $fonction_auteur;

		$this->responsabilites  = array() ;
		$res = array();
		$responsabilites = array();
		$auteurs = array() ;

		$res["responsabilites"] = array() ;
		$res["auteurs"] = array() ;

		$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->notice_id);
		$myQuery = pmb_mysql_query($requete);
		$source_id = pmb_mysql_result($myQuery, 0, 0);

		$rqt = "select recid,ufield,field_order,usubfield,subfield_order,value from entrepot_source_$source_id where recid='".addslashes($this->notice_id)."' and ufield like '7%' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
		$res_sql=pmb_mysql_query($rqt);

		$id_aut="";
		$n_aut=-1;
		while ($l=pmb_mysql_fetch_object($res_sql)) {
			if ($l->field_order!=$id_aut) {
				$n_aut++;
				switch ($l->ufield) {
					case "700":
					case "710":
						$responsabilites[$n_aut]=0;
						break;
					case "701":
					case "711":
						$responsabilites[$n_aut]=1;
						break;
					case "702":
					case "712":
						$responsabilites[$n_aut]=2;
						break;
				}
				switch (substr($l->ufield,0,2)) {
					case "70":
						$auteurs[$n_aut]["type"]=1;
						break;
					case "71":
						$auteurs[$n_aut]["type"]=2;
						break;
				}
				$auteurs[$n_aut]["id"]=$l->recid.$l->field_order;
				$id_aut=$l->field_order;
			}
			switch ($l->usubfield) {
				case '4':
					$auteurs[$n_aut]['fonction']=$l->value;
					if (isset($fonction_auteur[$l->value])) {
						$auteurs[$n_aut]['fonction_aff']=$fonction_auteur[$l->value];
					} else {
						$auteurs[$n_aut]['fonction_aff']=$l->value;
					}
					break;
				case 'a':
					$auteurs[$n_aut]['name']=$l->value;
					break;
				case 'b':
					if ($auteurs[$n_aut]['type']==2) {
						$auteurs[$n_aut]['subdivision']=$l->value;
					} else {
						$auteurs[$n_aut]['rejete']=$l->value;
					}
					break;
				case 'd':
					if ($auteurs[$n_aut]['type']==2) {
						$auteurs[$n_aut]['numero']=$l->value;
					}
					break;
				case 'e':
					if ($auteurs[$n_aut]['type']==2) {
						$auteurs[$n_aut]['lieu'].=(($auteurs[$n_aut]['lieu'])?'; ':'').$l->value;
					}
					break;
				case 'f':
					$auteurs[$n_aut]['date']=$l->value;
					break;
				case 'g':
					if ($auteurs[$n_aut]['type']==2) {
						$auteurs[$n_aut]['rejete']=$l->value;
					}
					break;
			}
		}

		foreach($auteurs as $n_aut=>$auteur) {
			$auteurs[$n_aut]['auteur_titre']=(!empty($auteurs[$n_aut]['rejete']) ? $auteurs[$n_aut]['rejete'].' ' : '').$auteurs[$n_aut]['name'];
			if ($auteur['type']==2 && (!empty($auteurs[$n_aut]['subdivision']) || !empty($auteurs[$n_aut]['numero']) || !empty($auteurs[$n_aut]['date']) || !empty($auteurs[$n_aut]['lieu']))) {
				$c='';
				$c.=$auteurs[$n_aut]['subdivision'];
				$c.=($c && $auteurs[$n_aut]['numero'])?(', '.$auteurs[$n_aut]['numero']):($auteurs[$n_aut]['numero']);
				$c.=($c && $auteurs[$n_aut]['date'])?(', '.$auteurs[$n_aut]['date']):($auteurs[$n_aut]['date']);
				$c.=($c && $auteurs[$n_aut]['lieu'])?(', '.$auteurs[$n_aut]['lieu']):($auteurs[$n_aut]['lieu']);
				$auteurs[$n_aut]['auteur_titre'].=' ('.$c.')';
			}
			$auteurs[$n_aut]['auteur_isbd']=$auteurs[$n_aut]['auteur_titre'].(!empty($auteurs[$n_aut]['fonction_aff']) ? ' ,'.$auteurs[$n_aut]['fonction_aff'] : '');
		}

		if (empty($responsabilites)) $responsabilites = array();
		if (!$auteurs) $auteurs = array();
		$res["responsabilites"] = $responsabilites ;
		$res["auteurs"] = $auteurs ;
		$this->responsabilites = $res;

		// $this->auteurs_principaux
		// on ne prend que le auteur_titre = "Prenom NOM"
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$this->auteurs_principaux = $auteur_0["auteur_titre"];
			} else {
				$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
				$aut1_libelle = array();
				for ($i = 0 ; $i < count($as) ; $i++) {
					$indice = $as[$i] ;
					$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
					$aut1_libelle[]= $auteur_1["auteur_titre"];
					}
				$auteurs_liste = implode ("; ",$aut1_libelle) ;
				if ($auteurs_liste) $this->auteurs_principaux = $auteurs_liste ;
				}

		// $this->auteurs_tous
		$mention_resp = array() ;
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$mention_resp_lib = $auteur_0["auteur_isbd"];
			$mention_resp[] = $mention_resp_lib ;
			}

		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_1["auteur_isbd"];
			$mention_resp[] = $mention_resp_lib ;
			}

		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_2 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib = $auteur_2["auteur_isbd"];
			$mention_resp[] = $mention_resp_lib ;
			}

		$libelle_mention_resp = implode ("; ",$mention_resp) ;
		if ($libelle_mention_resp) $this->auteurs_tous = $libelle_mention_resp ;
			else $this->auteurs_tous ="" ;
	} // fin fetch_auteurs

	// récupération des categories ------------------------------------------------------------------
	public function fetch_categories() {
		$this->categories_toutes="";
		$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->notice_id);
		$myQuery = pmb_mysql_query($requete);
		$source_id = pmb_mysql_result($myQuery, 0, 0);

		$rqt = "select ufield,field_order,usubfield,subfield_order,value from entrepot_source_$source_id where recid='".addslashes($this->notice_id)."' and ufield like '60%' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
		$res_sql=pmb_mysql_query($rqt);

		$id_categ="";
		$n_categ=-1;
		$categ_l=array();
		while ($l=pmb_mysql_fetch_object($res_sql)) {
			if ($l->field_order!=$id_categ) {
				if ($n_categ!=-1) {
					$categ_libelle=$categ_l["a"][0].(!empty($categ_l["x"]) ? " - ".implode(" - ",$categ_l["x"]):"").(!empty($categ_l["y"]) ? " - ".implode(" - ",$categ_l["y"]):"").(!empty($categ_l["z"]) ? " - ".implode(" - ",$categ_l["z"]):"");
					$this->categories_toutes.=($this->categories_toutes?"<br />":"").$categ_libelle;
				}
				$categ_l=array();
				$n_categ++;
				$id_categ=$l->field_order;
			}
			$categ_l[$l->usubfield][]=$l->value;
		}
		if ($n_categ>=0) {
			$categ_libelle=$categ_l["a"][0].(!empty($categ_l["x"])?" - ".implode(" - ",$categ_l["x"]):"").(!empty($categ_l["y"])?" - ".implode(" - ",$categ_l["y"]):"").(!empty($categ_l["z"])?" - ".implode(" - ",$categ_l["z"]):"");
			$this->categories_toutes.=($this->categories_toutes?"<br />":"").$categ_libelle;
		}
	}

	public function fetch_langues($quelle_langues=0) {
		global $marc_liste_langues ;
		if (!$marc_liste_langues) $marc_liste_langues=new marc_list('lang');

		$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->notice_id);
		$myQuery = pmb_mysql_query($requete);
		$source_id = pmb_mysql_result($myQuery, 0, 0);

		$rqt = "select ufield,field_order,usubfield,subfield_order,value from entrepot_source_$source_id where recid='".addslashes($this->notice_id)."' and ufield like '101' group by ufield,usubfield,field_order,subfield_order,value order by recid,field_order,subfield_order";
		$res_sql=pmb_mysql_query($rqt);

		$langues = array() ;

		$subfield=array("0"=>"a","1"=>"c");

		while ($l=pmb_mysql_fetch_object($res_sql)) {
			if ($l->usubfield==$subfield[$quelle_langues]) {
				if ($marc_liste_langues->table[$l->value]) {
					$langues[] = array(
						'lang_code' => $l->value,
						'langue' => $marc_liste_langues->table[$l->value]
					) ;
				}
			}
		}

		if (!$quelle_langues) $this->langues = $langues;
			else $this->languesorg = $langues;
	}

	// finalisation du résultat (écriture de l'isbd)
	public function finalize() {
		$this->result = str_replace('!!ISBD!!', $this->isbd, $this->result);
	}

	// génération du template javascript---------------------------------------
	public function init_javascript() {
		global $msg, $notice_id;

		if (isset($notice_id))
			$notice_id_info = "&notice_id=".$notice_id;
		else
			$notice_id_info = "";

		// propriétés pour le selecteur de panier
		$cart_click = "onClick=\"document.search_form.action='catalog.php?categ=search&mode=7&sub=integre".$notice_id_info."&item=!!id!!'; document.search_form.submit()\"";
		$suppr_click = "onClick=\"if(confirm('".$msg['confirm_suppr_notice']."')){;document.search_form.action='catalog.php?categ=search&mode=7&sub=suppr".$notice_id_info."&item=!!id!!'; document.search_form.submit()}\"";

		$javascript_template ="
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
                ".get_expandBase_button('el!!id!!')."
	    		<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
			</div>
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">
	        <img src='".get_url_icon('sauv.gif')."' class='align_middle' alt='basket' title=\"".$msg["connecteurs_integre"]."\" alt=\"".$msg["connecteurs_integre"]."\" $cart_click>
			&nbsp;&nbsp;<img src='".get_url_icon('trash.png')."' class='align_middle' alt='basket' title=\"".$msg["connecteurs_suppr"]."\" alt=\"".$msg["connecteurs_suppr"]."\" $suppr_click>
				 !!ISBD!!
	 		</div>";

		$this->result = str_replace('!!id!!', $this->notice_id.(!empty($this->anti_loop) ? "_p".$this->anti_loop[count($this->anti_loop)-1] : ""), $javascript_template);
		$this->result = str_replace('!!heada!!', (!empty($this->lien_suppr_cart) ? $this->lien_suppr_cart : '').$this->header, $this->result);
	}

	// génération de l'isbd----------------------------------------------------
	public function do_isbd() {
		global $msg;
		global $tdoc;
		global $fonction_auteur;
		global $charset;
		global $pmb_show_notice_id,$pmb_show_permalink;


		// constitution de la mention de titre
		if($this->tit_serie) {
			$this->isbd = $this->tit_serie;
			if(!empty($this->notice->tnvol))
				$this->isbd .= ',&nbsp;'.$this->notice->tnvol;
		}
		if($this->isbd) {
			$this->isbd .= '.&nbsp;'. htmlentities($this->tit1, ENT_QUOTES, $charset);
		} else {
			$this->isbd = htmlentities($this->tit1, ENT_QUOTES, $charset);
		}

		$this->isbd .= !empty($tdoc->table[$this->notice->typdoc]) ? ' ['.$tdoc->table[$this->notice->typdoc].']' : '';
		$tit2 = !empty($this->notice->tit2) ? htmlentities($this->notice->tit2, ENT_QUOTES, $charset) : '';
		$tit3 = !empty($this->notice->tit3) ? htmlentities($this->notice->tit3, ENT_QUOTES, $charset) : '';
		$tit4 = !empty($this->notice->tit4) ? htmlentities($this->notice->tit4, ENT_QUOTES, $charset) : '';
		if($tit3) $this->isbd .= "&nbsp;= $tit3";
		if($tit4) $this->isbd .= "&nbsp;: $tit4";
		if($tit2) $this->isbd .= "&nbsp;; $tit2";

		$mention_resp = array() ;

		// constitution de la mention de responsabilité
		//$this->responsabilites
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			$mention_resp_lib=$auteur_0["auteur_titre"];
			if (!empty($auteur_0["fonction"])) $mention_resp_lib .= ", ".$fonction_auteur[$auteur_0["fonction"]];
			$mention_resp[] = $mention_resp_lib ;
		}

		$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
			$mention_resp_lib=$auteur_1["auteur_titre"];
			if (!empty($auteur_1["fonction"])) {
			    $mention_resp_lib .= ", ".$fonction_auteur[$auteur_1["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}

		$as = array_keys ($this->responsabilites["responsabilites"], "2" ) ;
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i];

			$auteur_2 = $this->responsabilites["auteurs"][$indice];
			$mention_resp_lib=$auteur_2["auteur_titre"];
			if ($auteur_2["fonction"] && isset($fonction_auteur[$auteur_2["fonction"]])) {
				$mention_resp_lib .= ", ".$fonction_auteur[$auteur_2["fonction"]];
			}
			$mention_resp[] = $mention_resp_lib ;
		}

		$libelle_mention_resp = implode ("; ",$mention_resp) ;
		if($libelle_mention_resp) $this->isbd .= "&nbsp;/ $libelle_mention_resp" ;

		// mention d'édition
		if(!empty($this->notice->mention_edition)) $this->isbd .= ".&nbsp;-&nbsp;".$this->notice->mention_edition;

		// zone de l'adresse
		// on récupère la collection au passage, si besoin est
		if ($this->collections) {
			$collections = $this->collections[0]["name"];
		}
		$editeurs_tab=array();
		$editeurs = '';
		if(!empty($this->publishers)) {
			for ($i=0; $i<count($this->publishers); $i++) {
			    $editeurs_tab[] = ($this->publishers[$i]["name"] ?? "").(!empty($this->publishers[$i]["city"]) ? " (".$this->publishers[$i]["city"].")" : "");
			}
			$editeurs=implode("&nbsp;; ",$editeurs_tab);
		}
		if($editeurs !== ''){
		    $editeurs .= ', ';
		}
		if(!empty($this->notice->year)) {
		    $editeurs .= $this->notice->year;
		}else{
		    $editeurs.= "[s.d.]";
		}

		$this->isbd .= ".&nbsp;-&nbsp;$editeurs";

		// zone de la collation (ne concerne que a2)
		$collation = '';
		if(!empty($this->notice->npages)) {
			$collation.= $this->notice->npages;
		}
		if(!empty($this->notice->ill)) {
			$collation.= '&nbsp;: '.$this->notice->ill;
		}
		if(!empty($this->notice->size)) {
			$collation.= '&nbsp;; '.$this->notice->size;
		}
		if(!empty($this->notice->accomp)) {
			$collation.= '&nbsp;+ '.$this->notice->accomp;
		}

		if(!empty($collation)) {
			$this->isbd.= ".&nbsp;-&nbsp;$collation";
		}


		if(!empty($collections)) {
			if($this->notice->nocoll) {
				$collections.= '; '.$this->notice->nocoll;
			}
			$this->isbd.= ".&nbsp;-&nbsp;($collections)".' ';
		}

		$this->isbd.= '.';

		// note générale
		$zoneNote = '';
		if(!empty($this->notice->n_gen)) {
			$zoneNote = nl2br(htmlentities(implode("\n", $this->notice->n_gen),ENT_QUOTES, $charset)).' ';
		}

		// ISBN ou NO. commercial
		if(!empty($this->notice->code)) {
			if(isISBN($this->notice->code)) {
				if ($zoneNote) {
					$zoneNote .= '.&nbsp;-&nbsp;'.$msg['isbd_notice_isbn'].' ';
				} else {
					$zoneNote = $msg['isbd_notice_isbn'].' ';
				}
			} else {
				if($zoneNote) $zoneNote .= '.&nbsp;-&nbsp;';
			}
			$zoneNote .= $this->notice->code;
		}
		if(!empty($this->notice->prix)) {
			if(!empty($this->notice->code)) {
				$zoneNote .= '&nbsp;: '.$this->notice->prix;
			} else {
				if ($zoneNote) {
					$zoneNote.= '&nbsp; '.$this->notice->prix;
				} else {
					$zoneNote = $this->notice->prix;
				}
			}
		}

		if($zoneNote)
			$this->isbd .= "<br /><br />$zoneNote.";

		if($pmb_show_notice_id || $pmb_show_permalink) $this->isbd .= "<br />";
		if($pmb_show_notice_id){
	       	$prefixe = explode(",",$pmb_show_notice_id);
			$this->isbd .= "<b>".$msg['notice_id_libelle']."&nbsp;</b>".(!empty($prefixe[1]) ? $prefixe[1] : '').$this->notice_id."<br />";
		}
		// Permalink OPAC
		if ($pmb_show_permalink) {
		    $this->isbd .= "<b>".$msg["notice_permalink_opac"]."&nbsp;</b><a href='".$this->get_permalink()."' target=\"_blank\">".$this->get_permalink()."</a><br />";
		}
		// niveau 1
		if($this->level == 1) {
			$this->isbd .= "<!-- !!bouton_modif!! -->";
			if ($this->expl) {
				$this->isbd .= "<br /><b>{$msg[285]}</b>";
				$this->isbd .= $this->show_expl_per_notice($this->notice->notice_id, $this->link_expl);
				//if ($this->show_explnum) {
				//	$explnum_assoc = show_explnum_per_notice($this->notice->notice_id, 0,$this->link_explnum);
				//	if ($explnum_assoc) $this->isbd .= "<b>$msg[explnum_docs_associes]</b>".$explnum_assoc;
				//	}
				}
			thumbnail::do_image($this->isbd, $this->notice) ;
			return;
		}

		// résumé
		if(!empty($this->notice->n_resume)) {
			$this->isbd.= "<br /><b>{$msg[267]}</b>&nbsp;: ".nl2br(htmlentities(implode("\n", $this->notice->n_resume),ENT_QUOTES, $charset));
		}

		// note de contenu
		if(!empty($this->notice->n_contenu)) {
			$this->isbd.= "<br /><b>{$msg[266]}</b>&nbsp;: ".nl2br(htmlentities(implode("\n", $this->notice->n_contenu),ENT_QUOTES, $charset));
		}

		// catégories
		if (!empty($this->categories_toutes)) {
			$this->isbd.= "<br /><b>{$msg[23]}</b>&nbsp;: ".$this->categories_toutes;
		}

		// langues
		$langues = '';
		if(count($this->langues)) {
			$langues = "<b>{$msg[537]}</b>&nbsp;: ".construit_liste_langues($this->langues);
		}
		if(count($this->languesorg)) {
			$langues .= " <b>{$msg[711]}</b>&nbsp;: ".construit_liste_langues($this->languesorg);
		}
		if($langues) {
			$this->isbd .= "<br />$langues";
		}

		// indexation libre
		if(!empty($this->notice->index_l)) {
			$this->isbd .= "<br /><b>{$msg[324]}</b>&nbsp;: ".$this->notice->index_l;
		}

		// indexation interne
		if(!empty($this->notice->indexint_name)) {
			$this->isbd .= "<br /><b>".$msg['indexint_catal_title']."</b>&nbsp;: ".$this->notice->indexint_name;
		}

		//lien vers la notice déjà existante dans le catalogue
		if(!empty($this->permalink)) {
			$this->isbd.="<br /><b>".$msg['catalog_link']."</b>&nbsp;: <a target='_blank' href='".$this->permalink."'>".$this->permalink."</a>";
		}

		if (!empty($this->docnums)) {
			create_tableau_mimetype();
			$this->isbd .= "<br /><br />";
			$this->isbd .= "<b>".$msg["entrepot_notice_docnum"]."</b>";
			$nb_doc = 0;
			$display = "<table>
							<tbody>";
			$i=0;
			foreach($this->docnums as $docnum) {
				if (!$docnum["a"])
					continue;
				$extension = substr($docnum['b'], strrpos($docnum['b'], '.')+1);
				$i++;
				$nb_doc++;
				if($nb_doc == 1) $display .= "<tr>";
				$alt = '';
				//$alt = htmlentities($docnum_tab[$i]['explnum_doc_nomfichier'],ENT_QUOTES,$charset).' - '.htmlentities($docnum_tab[$i]['explnum_doc_mimetype'],ENT_QUOTES,$charset);
				$display .=
					"<td class='docnum' style='width:25%;border:0px solid #CCCCCC;padding : 5px 5px'>
						<a target='_blank' alt='$alt' title='$alt' href=\"".htmlentities($docnum["a"],ENT_QUOTES,$charset)."\">
							<img src='".get_url_icon('mimetype/'.icone_mimetype(trouve_mimetype('', $extension), $extension))."' alt='$alt' title='$alt' >
						</a>
						<br />";
				if($docnum["b"]){
					$display .=
						"<a href=\"".htmlentities($docnum["a"],ENT_QUOTES,$charset)."\">".htmlentities($docnum["b"],ENT_QUOTES,$charset)."</a>
					</td>";
				}
				if($nb_doc == 4) {
					$display .= "</tr>";
					$nb_doc=0;
				}
			}
			$display .= "</tbody></table>";
			$this->isbd .=$display;
		}

		thumbnail::do_image($this->isbd, $this->notice) ;
		if(!empty($this->expl)) {
			$expl_aff = $this->show_expl_per_notice();
			if ($expl_aff) {
				$this->isbd .= "<br /><br /><b>{$msg[285]}</b>";
				$this->isbd .= $expl_aff;
			}
		}
		$this->isbd .= "<!-- !!bouton_modif!! -->";
		//if ($this->show_explnum) {
		//	$explnum_assoc = show_explnum_per_notice($this->notice->notice_id, 0, $this->link_explnum);
		//	if ($explnum_assoc) $this->isbd .= "<b>$msg[explnum_docs_associes]</b>".$explnum_assoc;
		//}
		return;
	}

	// génération du header----------------------------------------------------
	public function do_header() {
		global $charset;
		global $pmb_notice_reduit_format;
		global $msg;
		global $no_aff_doc_num_image;

		$aut1_libelle = array() ;
		// récupération du titre de série
		if (!empty($this->notice->is_article)) {
			$this->header = $this->notice->serie_name;
			if(!empty($this->notice->bull_num) && !empty($this->notice->bull_periode)){
				$this->header.= " (".$this->notice->bull_num." - ".$this->notice->bull_periode.")";
			}elseif(!empty($this->notice->bull_num)){
				$this->header.= " (".$this->notice->bull_num.")";
			}elseif(!empty($this->notice->bull_periode)){
				$this->header.= " (".$this->notice->bull_periode.")";
			}
		} elseif (!empty($this->notice->serie_name)) {
			$this->tit_serie = $this->notice->serie_name;
			$this->header = $this->tit_serie;
			if(!empty($this->notice->tnvol))
				$this->header .= ',&nbsp;'.$this->notice->tnvol;
		}

		$this->tit1 = $this->notice->tit1;
		if($this->header) {
			$this->header .= '.&nbsp;'.htmlentities($this->tit1, ENT_QUOTES, $charset);
		} else {
			$this->header = htmlentities($this->tit1, ENT_QUOTES, $charset);
		}

		if ($this->source_name) {
			$this->header=$this->source_name." : ".$this->header;
		}

		//on ajoute la checkbox pour l'intégration en lot...
		//calcul de la checkbox
		$checkbox = "<input type='checkbox' name='external_notice_to_integer[]' value='".$this->notice_id."'";

		//on commence par regarder si cette notice n'a pas déjà été intégrér
		$query = "select rid, notices_externes.num_notice from notices_externes join external_count on external_count.recid = notices_externes.recid where rid=".$this->notice_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
		    $row = pmb_mysql_fetch_object($result);
		    if($row->num_notice) {
                $checkbox.=" checked='checked' disabled='disabled'";
		    }
		}
		$checkbox.= "/>";
		//ajout dans le header;
		$this->header = $checkbox."&nbsp;".$this->header;

		//$this->responsabilites
		$as = array_search ("0", $this->responsabilites["responsabilites"]) ;
		if ($as!== FALSE && $as!== NULL) {
			$auteur_0 = $this->responsabilites["auteurs"][$as] ;
			//$auteur = new auteur($auteur_0["id"]);
			if ($auteur_0["auteur_isbd"]) $this->header .= ' / '. $auteur_0["auteur_titre"];
		} else {
			$as = array_keys ($this->responsabilites["responsabilites"], "1" ) ;
			for ($i = 0 ; $i < count($as) ; $i++) {
				$indice = $as[$i] ;
				$auteur_1 = $this->responsabilites["auteurs"][$indice] ;
				$aut1_libelle[]= $auteur_1["auteur_titre"];
			}
			$auteurs_liste = implode ("; ",$aut1_libelle) ;
			if ($auteurs_liste) $this->header .= ' / '. $auteurs_liste ;
		}

		switch ($pmb_notice_reduit_format) {
			case "1":
				if (isset($this->notice->year) && $this->notice->year != '') $this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
				break;
			case "2":
				if (isset($this->notice->year) && $this->notice->year != '') $this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
				if ($this->notice->code != '') $this->header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
				break;
			default :
				break;
		}
		if (!empty($this->drag)) {
			$this->drag = "<span id=\"NOTI_drag_".$this->notice_id.($this->anti_loop?"_p".$this->anti_loop[count($this->anti_loop)-1]:"")."\"  dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".$this->header."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".get_url_icon('notice_drag.png')."\"/></span>";
		}
		if($this->action) {
			$this->header = "<a href=\"".$this->action."\">".$this->header.'</a>';
		}
		if ($this->notice->niveau_biblio=='b') {
			$rqt="select tit1 from bulletins,notices where bulletins.num_notice='".$this->notice_id."' and notices.notice_id=bulletins.bulletin_notice";
			$execute_query=pmb_mysql_query($rqt);
			$row=pmb_mysql_fetch_object($execute_query);
			$this->header.=" <i>".str_replace("%s",$row->tit1,$msg["bul_titre_perio"])."</i>";
			pmb_mysql_free_result($execute_query);
		}

		if(!empty($this->notice->lien)) {
			// ajout du lien pour les ressourcenotice_parent_useds électroniques
			$this->header .= "<a href=\"".$this->notice->lien."\" target=\"_blank\">";
			$this->header .= "<img src='".get_url_icon('globe.gif')."' style='border:0px; margin:3px 3px' class='align_middle'";
			$this->header .= " alt=\"";
			$this->header .= (isset($this->notice->eformat) ? $this->notice->eformat : '');
			$this->header .= "\" title=\"";
			$this->header .= (isset($this->notice->eformat) ? $this->notice->eformat : '');
			$this->header .= "\">";
			$this->header .='</a>';
		}
		if(!$this->print_mode || $this->print_mode=='2' && !$no_aff_doc_num_image)	{
			if (!empty($this->docnums) && count($this->docnums) == 1) {
				foreach ($this->docnums as $docnum) {
					$this->header .= "<a href=\"".$docnum['a']."\" target=\"_blank\">";
					$this->header .= "<img src=\"".get_url_icon('globe_orange.png')."\" style='border:0px; margin:3px 3px' align=\"middle\"";
					$this->header .= " alt=\"";
					$this->header .= htmlentities($docnum['b'],ENT_QUOTES,$charset);
					$this->header .= "\" title=\"";
					$this->header .= htmlentities($docnum['b'],ENT_QUOTES,$charset);
					$this->header .= "\">";
					$this->header .='</a>';
				}
			} else if (!empty($this->docnums) && count($this->docnums) > 1) {
				$this->header .= "<img src=\"".get_url_icon('globe_rouge.png')."\" style='border:0px; margin:3px 3px' align=\"middle\" alt=\"".$msg['info_docs_num_notice']."\" title=\"".$msg['info_docs_num_notice']."\" />";
			}
		}
	}

	// récupération des valeurs en table---------------------------------------
	public function mono_display_fetch_data() {
		global $pmb_url_base;

		$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->notice_id);
		$myQuery = pmb_mysql_query($requete);
		$source_id = pmb_mysql_result($myQuery, 0, 0);

		$requete="select * from entrepot_source_".$source_id." where recid='".addslashes($this->notice_id)."' order by ufield,field_order,usubfield,subfield_order,value";
		$myQuery = pmb_mysql_query($requete);

		$notice="";
		$lpfo="";
		$n_ed=-1;
		$n_coll=-1;
		$exemplaires = array();
		$doc_nums = array();

		if(pmb_mysql_num_rows($myQuery)) {
			$notice = new stdClass();
			$notice->notice_id=$this->notice_id;
			$notice->is_external = true;
			while ($l=pmb_mysql_fetch_object($myQuery)) {
				if (empty($this->source_id)) {
					$this->source_id=$l->source_id;
					$requete="select name from connectors_sources where source_id=".$l->source_id;
					$rsname=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($rsname)) $this->source_name=pmb_mysql_result($rsname,0,0);
				}
				switch ($l->ufield) {
					//dt
					case "dt":
						$notice->typdoc=$l->value;
						break;
					case "bl":
	//					$notice->niveau_biblio=$l->value;
						$notice->niveau_biblio='m'; //On force le document au type monographie
						break;
					case "hl":
						$notice->niveau_hierar=0; //On force le niveau à zéro.
	//					$notice->niveau_hierar=$l->value;
						break;
					//ISBN
					case "010":
						if ($l->usubfield=="a") $notice->code=$l->value;
						break;
					//Titres
					case "200":
						switch ($l->usubfield) {
							case "a":
								if (empty($notice->tit1)) $notice->tit1 = '';
								$notice->tit1.= ($notice->tit1 ? " " : "").$l->value;
								break;
							case "c":
								if (empty($notice->tit2)) $notice->tit2 = '';
								$notice->tit2.= ($notice->tit2?" ":"").$l->value;
								break;
							case "d":
								if (empty($notice->tit3)) $notice->tit3 = '';
								$notice->tit3.= ($notice->tit3?" ":"").$l->value;
								break;
							case "e":
								if (empty($notice->tit4)) $notice->tit4 = '';
								$notice->tit4.= ($notice->tit4?" ":"").$l->value;
								break;
						}
						break;
					//Editeur
					case "210":
					case "214":
					case "219":
						if($l->field_order!=$lpfo) {
							$lpfo=$l->field_order;
							$n_ed++;
						}
						switch ($l->usubfield) {
							case "a":
								$this->publishers[$n_ed]["city"]=$l->value;
								break;
							case "c":
								$this->publishers[$n_ed]["name"]=$l->value;
								break;
							case "d":
								$this->publishers[$n_ed]["year"]=$l->value;
								$this->year=$l->value;
								$notice->year=$l->value;
								break;
						}
						break;
					//Collation
					case "215":
						switch ($l->usubfield) {
							case "a":
								$notice->npages=$l->value;
								break;
							case "c":
								$notice->ill=$l->value;
								break;
							case "d":
								$notice->size=$l->value;
								break;
							case "e":
								$notice->accomp=$l->value;
				 				break;
						}
						break;
					case "225":
						if($l->field_order!=$lpfo) {
							$lpfo=$l->field_order;
							$n_coll++;
						}
						switch ($l->usubfield) {
							case "a":
								$this->collections[$n_coll]["name"]=$l->value;
								break;
							case "x":
								$this->collections[$n_coll]["ISSN"]=$l->value;
								break;
							case "i":
								$this->collections[$n_coll]["subcoll_name"]=$l->value;
								break;
							case "v":
								$this->collections[$n_coll]["volume"]=$l->value;
								$notice->nocoll=$l->value;
								break;
						}
					//Note générale
					case "300":
						if(empty($notice->n_gen)) {
							$notice->n_gen = array();
						}
						if($l->value) {
							$notice->n_gen[] = $l->value;
						}
						break;
					//Note de contenu
					case "327":
						if(empty($notice->n_contenu)) {
							$notice->n_contenu = array();
						}
						if($l->value) {
							$notice->n_contenu[] = $l->value;
						}
						break;
					//Note de résumé
					case "330":
						if(empty($notice->n_resume)) {
							$notice->n_resume = array();
						}
						if($l->value) {
							$notice->n_resume[] = $l->value;
						}
						break;
					//Série
					case "461":
						if ($l->usubfield=="t") $notice->serie_name=$l->value;
						if ($l->usubfield=="v") $notice->tnvol=$l->value;
						break;
					//bulletin
					case "463":
						if ($l->usubfield=="9" && $l->value == "lnk:bull") $notice->is_article=true;
						if ($l->usubfield=="v") $notice->bull_num=$l->value;
						if ($l->usubfield=="e") $notice->bull_periode=$l->value;
						if ($l->usubfield=="d") $notice->bull_date=$l->value;
						if ($l->usubfield=="t") $notice->bull_titre=$l->value;
						break;
					//Mots clés
					case "610":
						switch ($l->usubfield) {
							case "a":
								if (empty($notice->index_l)) $notice->index_l = '';
								$notice->index_l.= ($notice->index_l ? " / " : "").$l->value;
								break;
						}
						break;
					case "676":
						switch ($l->usubfield) {
							case "a":
								$notice->indexint_name=$l->value;
								break;
						}
					//URL
					case "856":
						switch ($l->usubfield) {
							case "u":
								$notice->lien=$l->value;
								break;
							case "q":
								$notice->eformat=$l->value;
								break;
							case "t":
								$notice->lien_texte=$l->value;
								break;
						}
						break;
					case "996":
						$exemplaires[$l->field_order][$l->usubfield] = $l->value;
						break;
					//Thumbnail
					case "896":
						switch ($l->usubfield) {
							case "a":
								$notice->thumbnail_url=$l->value;
						}
						break;
					//Documents numériques
					case "897":
						$doc_nums[$l->field_order][$l->usubfield] = $l->value;
						break;
				}
			}
		}
		$this->exemplaires = $exemplaires;
		$this->docnums = $doc_nums;
		$this->notice=$notice;
		if (!$this->notice->typdoc) $this->notice->typdoc='a';

	/*	$requete = "SELECT * FROM notices WHERE notice_id='".$this->notice_id."' ";
		$myQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($myQuery)) {
			$this->notice = pmb_mysql_fetch_object($myQuery);
			}
		$this->langues	= get_notice_langues($this->notice_id, 0) ;	// langues de la publication
		$this->languesorg	= get_notice_langues($this->notice_id, 1) ; // langues originales*/

		//on regarde si cette notice externe a déjà été intégrée dans le catalogue
		$req = "select num_notice, niveau_biblio from external_count join notices_externes on external_count.recid = notices_externes.recid join notices on num_notice = notice_id where rid='".addslashes($this->notice_id)."'";
		$result = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($result)>0){
			$row = pmb_mysql_fetch_object($result);
			$this->permalink = "";
			$row->num_notice = intval($row->num_notice);
			switch($row->niveau_biblio){
				case "m":
					$this->permalink = $pmb_url_base."catalog.php?categ=isbd&id=".$row->num_notice;
					break;
				case "s" :
					$this->permalink = $pmb_url_base."catalog.php?categ=serials&sub=view=serial_id=".$row->num_notice;
					break;
				case "b" :
					//on va chercher le numéro de bulletin...
					$query = "select bulletin_id from bulletins where num_notice = ".$row->num_notice;
					$res = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($res)){
						$bull_row = pmb_mysql_fetch_object($res);
						$this->permalink = $pmb_url_base."catalog.php?categ=serials&sub=view&bul_id=" . intval($bull_row->bulletin_id);
					}
					break;
				case "a" :
					$query = "select analysis_bulletin from analysis where analysis_notice = " . $row->num_notice;
					$res = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($res)){
						$analysis_row = pmb_mysql_fetch_object($res);
						$this->permalink = $pmb_url_base."catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=" . intval($analysis_row->analysis_bulletin) . "&art_to_show=".$row->num_notice;
					}
					break;
			}
		}

		$this->isbn = !empty($this->notice->code) ? $this->notice->code : '';
		return pmb_mysql_num_rows($myQuery);
	}

	// fonction retournant les infos d'exemplaires pour une notice donnée
	public function show_expl_per_notice() {
		global $msg;

		if (!$this->exemplaires)
			return;

		$expl_output = "<table style='border:0px' class='expl-list'>";
		$count = 1;

		$expl996 = array(
			"f" => $msg["extexpl_codebar"],
			"k" => $msg["extexpl_cote"],
			"v" => $msg["extexpl_location"],
			"x" => $msg["extexpl_section"],
			"1" => $msg["extexpl_statut"],
			"a" => $msg["extexpl_emprunteur"],
			"e" => $msg["extexpl_doctype"],
			"u" => $msg["extexpl_note"]
		);

		$final_location = array();
		foreach ($this->exemplaires as $expl) {
			$alocation = array();
			//Si on trouve une localisation, on la convertie en libelle et on l'oublie si spécifié
			if (isset($expl["v"]) && preg_match("/\d{9}/", $expl["v"]) && $this->entrepots_localisations) {
				if (isset($this->entrepots_localisations[$expl["v"]])) {
					if (!$this->entrepots_localisations[$expl["v"]]["visible"]) {
						continue;
					}
					$alocation["priority"] = $this->entrepots_localisations[$expl["v"]]["visible"];

					$expl["v"] = $this->entrepots_localisations[$expl["v"]]["libelle"];
				}
			}
			if (!isset($alocation["priority"]))
				$alocation["priority"] = 1;
			$alocation["content"] = $expl;
			$final_location[] = $alocation;
		}

		if (!$final_location)
			return;

		$expl_output .= "<tr>";
		foreach ($expl996 as $caption996) {
			$expl_output .= "<th>".$caption996."</th>";
		}
		$expl_output .= "</tr>";

		//trions
		usort($final_location, "cmpexpl");

		foreach ($final_location as $expl) {
			$axepl_output = "<tr>";
			foreach ($expl996 as $key996 => $caption996) {
				if (isset($expl["content"][$key996])) {
					$axepl_output .= "<td>".$expl["content"][$key996]."</td>";
				}
				else {
					$axepl_output .= "<td></td>";
				}
			}
			$axepl_output .= "</tr>";
			$expl_output .= $axepl_output;
			$count++;
		}
		$expl_output .= "</table>";

		return $expl_output;

	}

	/**
	 * Retourne le permalink
	 * @return string
	 */
	public function get_permalink() {
	    global $pmb_ark_activate;
	    global $pmb_opac_url;

	    if ($pmb_ark_activate) {
	        // report des ARKs pour les notices externes
	    }
	    return $pmb_opac_url."index.php?lvl=notice_display&id=".$this->notice_id;
	}
}