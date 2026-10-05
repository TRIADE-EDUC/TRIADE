<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mono_display_expl.class.php,v 1.31.4.2 2025/01/30 09:08:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
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
require_once($class_path."/mono_display.class.php");
require_once($class_path."/serial_display.class.php");
require_once($include_path."/templates/expl.tpl.php");

global $tdoc;
if (empty($tdoc)) $tdoc = marc_list_collection::get_instance('doctype');
// propriétés pour le selecteur de panier 
$cart_click = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=!!id!!&unq=!!unique!!', 'cart')\"";


// définition de la classe d'affichage des monographies en liste
class mono_display_expl {
	public $notice_id		= 0;	// id de la notice à afficher
	public $isbn		= 0;	// isbn ou code EAN de la notice à afficher
  	public $notice;			// objet notice (tel que fetché dans la table 'notices'
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
	public $categories =	array();// les categories
	public $show_resa		= 0;	// flag indiquant si on affiche les infos de resa
	public $p_perso;
	public $print_mode=0;
	public $show_explnum=1;
	public $show_statut=0;
	public $aff_statut = '' ; // carré de couleur pour signaler le statut de la notice
	public $tit_serie_lien_gestion ;
	public $childs=array(); //Filles de la notice
	public $anti_loop="";
	public $drag=""; //Notice draggable ?
	public $no_link;
	public $show_opac_hidden_fields=true;
	public $num_notice;
	public $id_bulletin;
	public $ajax_mode = 0;
	public $lien_suppr_cart = '';
	public $expl_data;
	
	// constructeur------------------------------------------------------------
	public function __construct($cb,$expl_id=0, $level=1, $action='', $expl=1, $expl_link='', $lien_suppr_cart="", $explnum_link='', $show_resa=0, $print=0, $show_explnum=1, $no_link=true,$ajax_mode=0 ) {
	  	// $id = id de la notice à afficher
	  	// $action	 = URL associée au header
		// $level :
		//		0 : juste le header (titre  / auteur principal avec le lien si applicable) 
		// 			suppression des niveaux entre 1 et 6, seul reste level
		//		1 : ISBD seul, pas de note, bouton modif, expl, explnum et résas
		// 		6 : cas général détaillé avec notes, categ, langues, indexation... + boutons
		// $expl -> affiche ou non les exemplaires associés
		// $expl_link -> lien associé à l'exemplaire avec !!expl_id!!, !!notice_id!! et !!expl_cb!! à mettre à jour
	  	// $lien_suppr_cart -> lien de suppression de la notice d'un caddie
	  	//
	  	// $show_resa = affichage des resa ou pas
		global $explr_visible_mod,$pmb_droits_explr_localises;
		
		$expl_id = intval($expl_id);
		if(!$expl_id && $cb) {
			$query = " select expl_id from exemplaires where expl_cb='".$cb."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$expl = pmb_mysql_fetch_object($result);
				$expl_id = $expl->expl_id;		
			}	
		}
		
	  	$this->expl_data = $this->get_expl_info($cb,$expl_id);
	 	$this->notice_id = $this->expl_data->expl_notice;
		$this->id_bulletin = $this->expl_data->expl_bulletin;
		
		if($this->id_bulletin) {
			$req="select num_notice from bulletins where bulletin_id=".$this->id_bulletin;
			$result = pmb_mysql_query($req);
			if(pmb_mysql_num_rows($result)) {
				$res = pmb_mysql_fetch_object($result);
				$this->num_notice = $res->num_notice;
			}	
		}
		$this->header=$this->expl_titre_diplay()." / ";			
		$this->isbd=$this->expl_info_display();
		// notice de monographie
		if($this->notice_id) {
			$display = new mono_display($this->notice_id,$level, "", $expl, $expl_link, '', $explnum_link,1, 0, 1, 0, "", 1   , $no_link,true,0);
			$link_notice = str_replace("!!id!!", $this->notice_id, $action );
		}
		// notice de bulletin
		if($this->num_notice) {
			$display = new mono_display($this->num_notice, $level, "", $expl, $expl_link, '', $explnum_link,1, 0, 1, 0, "", 1   , $no_link,true,0);
			$link_notice = str_replace("!!id!!", $this->id_bulletin, $action );			
		}	
		// bulletin sans notice
		if(!$this->num_notice && $this->id_bulletin) {
			$display = new bulletinage_display($this->id_bulletin);
			$link_notice = str_replace("!!id!!", $this->id_bulletin, $action );	
		}
			
		$this->header.= $display->header;
		$this->isbd.= "<a href='$link_notice'>".$display->header."</a>";
//		$this->isbd.= $display->isbd;
	
		//visibilité des exemplaires
		if ($pmb_droits_explr_localises) {
			$explr_tab_modif=explode(",",$explr_visible_mod);
			$as_modif = array_search($this->expl_data->idlocation,$explr_tab_modif);
		} else {
			$as_modif = true;
		}
		if (($expl_link) && ($as_modif!== FALSE && $as_modif!== NULL) ) {
			$expl_link = str_replace("!!notice_id!!", $this->notice_id, $expl_link );
			$expl_link = str_replace("!!expl_cb!!", $cb, $expl_link );
			$expl_link = str_replace("!!expl_id!!", $expl_id, $expl_link );
			$expl_link = str_replace("!!bulletin_id!!", $this->id_bulletin, $expl_link );
			
			$this->header= "<a href='".$expl_link."'>".$this->header."</a>";
		}
		
		switch($level) {
		case 0:
			// là, c'est le niveau 0 : juste le header
			$this->result = $this->header;
			break;
		default:
			// niveau 1 et plus : header + isbd à générer
			$this->init_javascript();
			$this->finalize();
			break;
		}	
	}

	public function expl_titre_diplay() {
		global $charset;
		global $pmb_expl_title_display_format,$p_perso, $perso_, $nb_param;
		
		if(!$pmb_expl_title_display_format) return'';
		$liste_aff = array();
		$liste_format=explode(",",$pmb_expl_title_display_format);
		
		foreach($liste_format as $format) {
			switch($format) {
				case "expl_cb":$liste_aff[]=htmlentities($this->expl_data->expl_cb,ENT_QUOTES,$charset);break;
				case "expl_notice":$liste_aff[]=htmlentities($this->expl_data->id_notice,ENT_QUOTES,$charset);break;	
				case "expl_bulletin":$liste_aff[]=htmlentities($this->expl_data->id_bulletin,ENT_QUOTES,$charset);break;		
				case "expl_typdoc":$liste_aff[]=htmlentities($this->expl_data->tdoc_libelle,ENT_QUOTES,$charset);break;
				case "expl_cote":$liste_aff[]=htmlentities($this->expl_data->expl_cote,ENT_QUOTES,$charset);break;
				case "expl_section":$liste_aff[]=htmlentities($this->expl_data->section_libelle,ENT_QUOTES,$charset);break;
				case "expl_statut":$liste_aff[]=htmlentities($this->expl_data->statut,ENT_QUOTES,$charset);break;
				case "expl_location":$liste_aff[]=htmlentities($this->expl_data->location_libelle,ENT_QUOTES,$charset);break;
				case "expl_codestat":$liste_aff[]=htmlentities($this->expl_data->codestat,ENT_QUOTES,$charset);break;
				case "expl_note":$liste_aff[]=htmlentities($this->expl_data->note,ENT_QUOTES,$charset);break;
				case "expl_prix":$liste_aff[]=htmlentities($this->expl_data->prix,ENT_QUOTES,$charset);break;
				case "expl_owner":$liste_aff[]=htmlentities($this->expl_data->lender_libelle,ENT_QUOTES,$charset);break;
				case "expl_lastempr":$liste_aff[]=htmlentities($this->expl_data->lastempr,ENT_QUOTES,$charset);break;
				case "last_loan_date":$liste_aff[]=htmlentities(format_date($this->expl_data->last_loan_date),ENT_QUOTES,$charset);break;
				case "create_date":$liste_aff[]=htmlentities(format_date($this->expl_data->create_date),ENT_QUOTES,$charset);break;
				case "update_date":$liste_aff[]=htmlentities(format_date($this->expl_data->update_date),ENT_QUOTES,$charset);break;
				case "expl_date_depot":$liste_aff[]=htmlentities(format_date($this->expl_data->expl_date_depot),ENT_QUOTES,$charset);break;
				case "expl_date_retour":$liste_aff[]=htmlentities(format_date($this->expl_data->expl_date_retour),ENT_QUOTES,$charset);break;
				case "type_antivol":$liste_aff[]=htmlentities($this->expl_data->type_antivol,ENT_QUOTES,$charset);break;
				case "transfert_location_origine":$liste_aff[]=htmlentities($this->expl_data->transfert_location_origine,ENT_QUOTES,$charset);break;
				case "transfert_statut_origine":$liste_aff[]=htmlentities($this->expl_data->transfert_statut_origine,ENT_QUOTES,$charset);break;						
				case "transfert_section_origine":$liste_aff[]=htmlentities($this->expl_data->transfert_section_origine,ENT_QUOTES,$charset);break;						
				default:
					if (is_numeric($format)) {
						// c'est un id de champ perso						
						if(!$p_perso) $p_perso=new parametres_perso("expl");
						if (!$p_perso->no_special_fields) {
							if(!$perso_) {							
								$perso_=$p_perso->show_fields($this->expl_data->expl_id);		
								$nb_param=count($perso_["FIELDS"]);							
							}	
							for ($i=0; $i<$nb_param; $i++) {	
								$p=$perso_["FIELDS"][$i];								
								if($p["ID"]==$format) {																			
									if($p["AFF"] !== '') $liste_aff[]=$p["AFF"];
								}	
							}		
						}			
					}
				break;
			}
		}
		if(empty($liste_aff)) {
			return'';
		}
		return implode(", ", $liste_aff);	
	}
	
	// récupération des infos exemplaires
	public function get_expl_info($cb, $id, $lien_notice=1) {
		if ($cb && !$id) $clause_where = " WHERE expl_cb = '$cb' ";
		if ( (!$cb && $id) || ($cb && $id) ) $clause_where = " WHERE expl_id = '$id' ";
		
		if ($cb || $id) {
			$query = " select * from exemplaires expl, docs_location location, docs_codestat, lenders ";
			$query .= ", docs_section section, docs_statut statut, docs_type dtype";
			$query .=  $clause_where;
			$query .= " and location.idlocation=expl.expl_location";
			$query .= " and section.idsection=expl.expl_section";
			$query .= " and statut.idstatut=expl.expl_statut";
			$query .= " and dtype.idtyp_doc=expl.expl_typdoc";
			$query .= " and idcode=expl.expl_codestat";
			$query .= " and idlender=expl.expl_owner";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$expl = pmb_mysql_fetch_object($result);
				if ($expl->expl_lastempr) {
					$lastempr = new emprunteur($expl->expl_lastempr, '', FALSE, 0) ;
					$expl->lastempr_nom = $lastempr->nom;
					$expl->lastempr_prenom = $lastempr->prenom;
					$expl->lastempr_cb = $lastempr->cb;
				}
				return $expl;		
			}
		}	
		return FALSE;	
	}
	
	public function expl_info_display($affichage_emprunteurs=1,$affichage_zone_notes=1) {
		global $msg,$expl_view_form,$pmb_expl_show_dates,$charset;
		$expl_aff=$expl_view_form;
		
		$expl_aff=str_replace('!!code_barre!!', htmlentities($this->expl_data->expl_cb,ENT_QUOTES,$charset), $expl_aff);
		$expl_aff=str_replace('!!cote!!', htmlentities($this->expl_data->expl_cote,ENT_QUOTES,$charset), $expl_aff);
		$expl_aff=str_replace('!!type_doc!!', htmlentities($this->expl_data->tdoc_libelle,ENT_QUOTES,$charset), $expl_aff);
		$expl_aff=str_replace('!!localisation!!', htmlentities($this->expl_data->location_libelle,ENT_QUOTES,$charset), $expl_aff);
		$expl_aff=str_replace('!!section!!', htmlentities($this->expl_data->section_libelle,ENT_QUOTES,$charset), $expl_aff);
		$expl_aff=str_replace('!!owner!!', htmlentities($this->expl_data->lender_libelle,ENT_QUOTES,$charset), $expl_aff);
		$expl_aff=str_replace('!!statut!!', htmlentities($this->expl_data->statut_libelle,ENT_QUOTES,$charset), $expl_aff);
		$expl_aff=str_replace('!!codestat!!', htmlentities($this->expl_data->codestat_libelle,ENT_QUOTES,$charset), $expl_aff);
		$expl_aff=str_replace('!!note!!', htmlentities($this->expl_data->expl_note,ENT_QUOTES,$charset), $expl_aff);
		$expl_aff=str_replace('!!comment!!', htmlentities($this->expl_data->expl_comment,ENT_QUOTES,$charset), $expl_aff);
		
		//on compte de nombre de prets pour cet exemplaire 
		$req = "select count(arc_expl_id) from pret_archive where arc_expl_id = ".$this->expl_data->expl_id;
		$res = pmb_mysql_query($req);
		$nb_prets = pmb_mysql_result($res,0,0);
		$expl_aff=str_replace('!!nb_prets!!', $nb_prets, $expl_aff);
		
		//Date de création / modification
		if($pmb_expl_show_dates=='1' || $pmb_expl_show_dates=='3') {
			$tmp="<tr>
				<td class='align_right'><label class='etiquette'>".htmlentities($msg['exp_cre_date'],ENT_QUOTES,$charset)."</label></td>
				<td class='align_left'>".htmlentities(format_date($this->expl_data->create_date),ENT_QUOTES,$charset)."</td>
				<td class='align_right'><label class='etiquette'>".htmlentities($msg['exp_upd_date'],ENT_QUOTES,$charset)."</label></td>
				<td class='align_left'>".htmlentities(format_date($this->expl_data->update_date),ENT_QUOTES,$charset)."</td>					
			</tr>";
			$expl_aff=str_replace('<!-- exp_date_admin -->', $tmp, $expl_aff);
		}
		
		//Date de dépôt / retour
		if($pmb_expl_show_dates=='2' || $pmb_expl_show_dates=='3') {
			$tmp="<tr>
				<td class='align_right'><label class='etiquette'>".htmlentities($msg['filing_date'],ENT_QUOTES,$charset)."</label></td>
				<td class='align_left'>".htmlentities(format_date($this->expl_data->expl_date_depot),ENT_QUOTES,$charset)."</td>
				<td class='align_right'><label class='etiquette'>".htmlentities($msg['return_date'],ENT_QUOTES,$charset)."</label></td>
				<td class='align_left'>".htmlentities(format_date($this->expl_data->expl_date_retour),ENT_QUOTES,$charset)."</td>					
			</tr>";
			$expl_aff=str_replace('<!-- exp_date_filing -->', $tmp, $expl_aff);
		}
		
		$perso_aff='';
		$p_perso=new parametres_perso("expl");
		if (!$p_perso->no_special_fields) {
			$perso_=$p_perso->show_fields($this->expl_data->expl_id);		
			$nb_param=count($perso_["FIELDS"]);
			for ($i=0; $i<$nb_param; $i++) {				
				$nb_colonne=2;	
				$perso_aff.="<tr>\n";
				for ($i; $i<$nb_param; $i++) {	
					$p=$perso_["FIELDS"][$i];			
					$perso_aff.="<td class='align_right'><label class='etiquette'>".$p["TITRE"]."</label></td>";
					$perso_aff.="<td class='align_left'>".$p["AFF"]."</td>";
					if(!--$nb_colonne) break;
				}	
				$perso_aff.="</tr>\n";
			}		
		}		
		$expl_aff=str_replace('!!champs_perso!!', $perso_aff, $expl_aff);
		
		return $expl_aff;
	
	}
	
	// finalisation du résultat (écriture de l'isbd)
	public function finalize() {
		$this->result = str_replace('!!ISBD!!', $this->isbd, $this->result);
	}
	
	// génération du template javascript---------------------------------------
	public function init_javascript() {
	    global $msg, $charset, $pmb_recherche_ajax_mode, $categ;
		
		// propriétés pour le selecteur de panier 
		$cart_click = "onClick=\"openPopUp('./cart.php?object_type=EXPL&item=!!id!!', 'cart')\"";
		$cart_over_out = "onMouseOver=\"show_div_access_carts(event,!!id!!,'EXPL');\" onMouseOut=\"set_flag_info_div(false);\"";
		$checkbox = '';
		if(isset($categ) && $categ == 'search') {
		    $checkbox = "<span class='notice-selection'><input type='checkbox' id='object_selection_!!id!!' name='objects_selection' value='!!id!!' title='".htmlentities($msg['list_ui_selection_checkbox'], ENT_QUOTES, $charset)."' /></span>";
		}
		if($pmb_recherche_ajax_mode && $this->ajax_mode){	
		   
			$javascript_template ="
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
                " . $checkbox . "
	    		<img src=\"".get_url_icon('plus.gif')."\" class=\"img_plus\" name=\"imEx\" id=\"el!!id!!Img\" param='".rawurlencode($this->mono_display_cmd)."' title=\"".$msg['admin_param_detail']."\" onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param')); return false;\" />
	    		<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
			</div>
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">
	        <img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click $cart_over_out>
	        !!ISBD!!
	 		</div>";
		
		}else{
			$javascript_template ="
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
                " . $checkbox . "
	    		<img src=\"".get_url_icon('plus.gif')."\" class=\"img_plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['admin_param_detail']."\" onClick=\"expandBase('el!!id!!', true); return false;\" />
	    		<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
			</div>
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\">
	        <img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click $cart_over_out>
	        !!ISBD!!
	 		</div>";
		}
		$id_part = "";
		if ($this->anti_loop) {
		    $id_part = "_p" . substr($this->anti_loop, count($this->anti_loop)-1, 1);
		}
		$this->result = str_replace('!!id!!', $this->expl_data->expl_id . $id_part, $javascript_template);
		$this->result = str_replace('!!heada!!', $this->lien_suppr_cart.$this->header, $this->result);
	}

// fin class
}