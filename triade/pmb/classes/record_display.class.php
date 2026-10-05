<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: record_display.class.php,v 1.73.2.6.2.3 2025/03/14 08:23:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Ark\Entities\ArkRecord;
use Pmb\Ark\Models\ArkModel;
use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

global $class_path;
// require_once($class_path."/record_datas.class.php");
// require_once($class_path."/parametres_perso.class.php");
// include_once($include_path."/templates/demandes.tpl.php");
// require_once($class_path."/demandes.class.php");
// require_once($class_path.'/scan_request/scan_request.class.php');
// require_once($class_path."/serialcirc.class.php");
// require_once($class_path."/serialcirc_empr.class.php");
// require_once($class_path."/acces.class.php");
// require_once($base_path.'/includes/bul_list_func.inc.php');
// require_once($base_path.'/includes/notice_affichage.inc.php');
require_once "$class_path/event/events/event_expl_display_overload.class.php";

/**
 * Classe d'affichage d'une notice
 *
 */
class record_display {
	public $notice_id = 0;					// id de la notice à afficher
	public $notice;						// objet notice (tel que fetché dans la table 'notices'
	public $header	= '';					// chaine accueillant le chapeau de notice (peut-être cliquable)
	public $header_texte	= '';			// chaine accueillant le chapeau de notice sans html
	public $tit1 = '';						// valeur du titre 1
	public $result = '';					// affichage final
	public $level = 1;						// niveau d'affichage
	public $isbd = '';						// isbd de la notice en fonction du level défini
	public $responsabilites = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
	public $categories = array();			// les categories
	public $p_perso;
	public $expl		= 0;	// flag indiquant si on affiche les infos d'exemplaire
	public $nb_expl	= 0;	//nombre d'exemplaires
	public $link_expl	= '';	// lien associé à un exemplaire
	public $show_explnum = 1;
	public $show_statut = 0;
	public $childs= array(); 				//Filles de la notice
	public $print_mode = 0;				// 0 affichage normal
										// 1 affichage impression sans liens
										// 2 affichage impression avec liens sur documents numeriques
										// 4 affichage email : sans lien sauf url associée
	public $langues = array();
	public $languesorg = array();
	public $aff_statut = '' ; 				// carré de couleur pour signaler le statut de la notice
	public $show_opac_hidden_fields = true;
// 	public $drag = 0;
	public $anti_loop = array();
	public $map = null;
	public $map_info = null;
	public $show_map=1;
	public $context_dsi_id_bannette=0;
	public $notice_relations;	//Objet notice_relations
	public $is_child=false;

	public $icondoc = '';
	public $unique_id = '';
	public $icon_is_new = '';
	protected $context_parameters;

	/**
	 * Tableau d'instances de record_datas
	 * @var record_datas
	 */
	static private $records_datas = array();
	static private $special;

	public static $linked_permalink;

	/**
	 * lien ark
	 * @var string
	 */
	protected $ark_link;

	// récupération des valeurs en table
	public function fetch_data() {
		$query = "SELECT * FROM notices WHERE notice_id='".$this->notice_id."' ";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)){
			$this->notice = pmb_mysql_fetch_object($result);
		}
		$this->langues	= get_notice_langues($this->notice_id, 0) ;	// langues de la publication
		$this->languesorg	= get_notice_langues($this->notice_id, 1) ; // langues originales
	}

	// génération du template javascript---------------------------------------
	public function init_javascript() {
		global $msg, $base_path, $pmb_recherche_ajax_mode;
		global $caller, $callback, $charset;

		// propriétés pour le selecteur de panier
		$cart_click = "onClick=\"openPopUp('".$base_path."/cart.php?object_type=NOTI&item=!!notice_id!!', 'cart')\"";
		$cart_over_out = "onMouseOver=\"show_div_access_carts(event,!!notice_id!!);\" onMouseOut=\"set_flag_info_div(false);\"";
		$current=$_SESSION["CURRENT"];
		if ($current!==false) {
			$print_action = "&nbsp;<a href='#' onClick=\"openPopUp('".$base_path."/print.php?current_print=$current&notice_id=!!notice_id!!&action_print=print_prepare','print'); w.focus(); return false;\"><img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
		} else {
			$print_action = "";
		}
		$javascript_template = $this->get_display_anchor()."
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">";
		if(isset($this->context_parameters['in_search']) && $this->context_parameters['in_search']) {
			$javascript_template .= "<span class='notice-selection'><input type='checkbox' id='object_selection_!!notice_id!!' name='objects_selection' value='!!notice_id!!' title='".htmlentities($msg['list_ui_selection_checkbox'], ENT_QUOTES, $charset)."' /></span>";
		}
		if($pmb_recherche_ajax_mode && $this->ajax_mode){
			$javascript_template .= "
	    		<img src='".get_url_icon('plus.gif')."' class=\"img_plus\" name=\"imEx\" id=\"el!!id!!Img\" param='".rawurlencode($this->mono_display_cmd)."' title=\"".htmlentities($msg['admin_param_detail'], ENT_QUOTES, $charset)."\" alt=\"".htmlentities($msg['admin_param_detail'], ENT_QUOTES, $charset)."\" onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param')); return false;\" />
	    		<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
			</div>
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".$this->get_display_open_tag().">
	 		</div>";
		} else{
			$javascript_template .= "
	    		<img src='".get_url_icon('plus.gif')."' class=\"img_plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".htmlentities($msg['admin_param_detail'], ENT_QUOTES, $charset)."\" alt=\"".htmlentities($msg['admin_param_detail'], ENT_QUOTES, $charset)."\" onClick=\"expandBase('el!!id!!', true); return false;\" />
	    		<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
			</div>
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" ".$this->get_display_open_tag().">";
			if(SESSrights & CATALOGAGE_AUTH){
				$javascript_template.="<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click $cart_over_out>".$print_action;
				if(isset($this->context_parameters['in_selector']) && $this->context_parameters['in_selector']) {
					$javascript_template.="&nbsp;
						<a href='".static::get_record_datas($this->notice_id)->get_permalink()."' target='_blank'>
							<img src='".get_url_icon('search.gif')."' class='align_middle' title='".htmlentities($msg["noti_see_gestion"], ENT_QUOTES, $charset)."' />
						</a>";
				}
			}else{
				$javascript_template.=$print_action;
			}
			$javascript_template .=" !!ISBD!!
				</div>";
		}
		if($this->is_child)
			$javascript_template .= "</div>";
		$microtime = md5(microtime());
		$this->unique_id = $this->notice_id.(is_array($this->anti_loop) && count($this->anti_loop)?"_p".implode("_",$this->anti_loop):"").'_'.$microtime;
		$this->result = str_replace('!!id!!', $this->unique_id, $javascript_template);
		$this->result = str_replace('!!notice_id!!', $this->notice_id, $this->result);
		$this->result = str_replace('!!item!!', $this->notice_id, $this->result);
		$this->result = str_replace('!!unique!!', $microtime, $this->result);
		if(isset($this->context_parameters['in_selector']) && $this->context_parameters['in_selector']) {


			$this->result = str_replace('!!heada!!',
				($this->show_statut ? $this->aff_statut.' ' : '').
				($this->get_icondoc() ? $this->icondoc : '').
			    str_replace('!!notice_id!!', $this->notice_id, $this->lien_suppr_cart)."<a href='#' data-element-id='".$this->notice_id."' data-element-type='records' onclick=\"set_parent('".$caller."', '".$this->notice_id."', '".htmlentities(addslashes(str_replace("\r"," ",str_replace("\n"," ",$this->header_texte))), ENT_QUOTES, $charset)."','".$callback."')\">".strip_tags($this->header)."</a>", $this->result);
		} else {
			$this->result = str_replace('!!heada!!', str_replace('!!notice_id!!', $this->notice_id, $this->lien_suppr_cart).$this->header, $this->result);
		}
	}

	protected function get_icondoc() {
		global $tdoc;

		//Icone type de Document
		$icon_doc = marc_list_collection::get_instance('icondoc');
		$icon = (!empty($icon_doc->table[$this->notice->niveau_biblio.$this->notice->typdoc]) ? $icon_doc->table[$this->notice->niveau_biblio.$this->notice->typdoc] : '');
		$this->icondoc = '';
		if ($icon) {
			$biblio_doc = marc_list_collection::get_instance('nivbiblio');
			$info_bulle_icon=(!empty($biblio_doc->table[$this->notice->niveau_biblio]) ? $biblio_doc->table[$this->notice->niveau_biblio] : '')." : ".(!empty($tdoc->table[$this->notice->typdoc]) ? $tdoc->table[$this->notice->typdoc] : '');
			$this->icondoc="<img src=\"".get_url_icon($icon)."\" alt=\"$info_bulle_icon\" title=\"$info_bulle_icon\" class='align_top' />";
		}
		return $this->icondoc;
	}

	protected function get_icon_is_new() {
		global $msg;

		$this->icon_is_new = "";
		if (!empty($this->notice->notice_is_new)) {
			$info_bulle_icon_new=$msg["notice_is_new_gestion"];
			$this->icon_is_new="<img src=\"".get_url_icon('icone_nouveautes.png')."\" alt=\"$info_bulle_icon_new\" title=\"$info_bulle_icon_new\" class='align_top' />";
		}
		return $this->icon_is_new;
	}

	protected function get_aff_statut() {
		global $charset;

		if (!empty($this->notice->statut)) {
			$rqt_st = "SELECT class_html , gestion_libelle FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
			$res_st = pmb_mysql_query($rqt_st);
			$class_html = " class='".pmb_mysql_result($res_st, 0, 0)."' ";
			$txt = pmb_mysql_result($res_st, 0, 1);
		} else {
			$class_html = " class='statutnot1' " ;
			$txt = "" ;
		}
		if (!empty($this->notice->commentaire_gestion)) {
			if ($txt) $txt .= ":\r\n".$this->notice->commentaire_gestion ;
			else $txt = $this->notice->commentaire_gestion ;
		}
		if ($txt && empty($this->context_parameters['in_selector'])) {
			$this->aff_statut = "<small><span $class_html style='margin-right: 3px;'><a href=# onmouseover=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display='none'; \"><img src='".get_url_icon('spacer.gif')."' alt='".htmlentities($txt,ENT_QUOTES, $charset)."' style='width:10px; height:10px' /></a></span></small>";
			$this->aff_statut .= "<div id='zoom_statut".$this->notice_id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'><b>".nl2br(htmlentities($txt,ENT_QUOTES, $charset))."</b></div>" ;
		} else {
		    $this->aff_statut = "<small><span $class_html style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' alt='' style='width:10px; height:10px' /></span></small>";
		}
		return $this->aff_statut;
	}

	protected function get_aff_editeur_reduit() {
		$aff_editeur_reduit = '';
		// zone de l'éditeur
		if ($this->notice->ed1_id) {
			$editeur = new editeur($this->notice->ed1_id);
			$aff_editeur_reduit = $editeur->display ;
			if ($this->notice->year) $aff_editeur_reduit .= " (".$this->notice->year.")";
		} elseif ($this->notice->year) {
			// année mais pas d'éditeur et si pas un article
			if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$aff_editeur_reduit = $this->notice->year." ";
		}
		return $aff_editeur_reduit;
	}
	protected function get_aff_perso() {
		global $pmb_notice_reduit_format;

		// peut-être veut-on des personnalisés ?
		$perso_voulus_temp = substr($pmb_notice_reduit_format,2) ;
		$perso_voulus = array();
		if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);

		if (!is_object($this->p_perso)) $this->p_perso = new parametres_perso("notices");
		//Champs personalisés à ajouter au réduit
		$perso_voulu_aff = '';
		if (!$this->p_perso->no_special_fields) {
			if (count($perso_voulus)) {
				$this->p_perso->get_values($this->notice_id) ;
				for ($i=0; $i<count($perso_voulus); $i++) {
					$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
				}
				$perso_voulu_aff=trim($perso_voulu_aff);
			}
		}
		return $perso_voulu_aff;
	}

	protected function get_resources_link() {
		global $use_dsi_diff_mode;

		$resources_link = '';
		if (!$this->print_mode || $this->print_mode=='2' || $use_dsi_diff_mode) {
			$resources_link .= "<a href=\"".$this->notice->lien."\" target=\"_blank\">";
			$resources_link .= "<img src='".get_url_icon('globe.gif')."' style='border:0px; margin:3px 3px' class='align_middle'";
			$resources_link .= " alt=\"".$this->notice->eformat."\"";
			if (!empty($this->notice->eformat)) {
			    $resources_link .= " title=\"".$this->notice->eformat."\"";
			}
			$resources_link .= ">";
			$resources_link .="</a>";
		} elseif ($this->print_mode=='4') {
			$resources_link .= '<br />';
			$resources_link .= "<a href=\"".$this->notice->lien."\" target=\"_blank\">";
			$resources_link .= $this->notice->lien;
			$resources_link .='</a>';
		} else {
			$resources_link .= "<br />";
			$resources_link .= $this->notice->lien;
		}
		return $resources_link;
	}

	protected function get_display_collation() {
		$collation = '';
		if($this->notice->npages) {
			$collation = $this->notice->npages;
		}
		if($this->notice->ill) {
			$collation .= ' : '.$this->notice->ill;
		}
		if($this->notice->size) {
			$collation .= ' ; '.$this->notice->size;
		}
		if($this->notice->accomp) {
			$collation .= ' + '.$this->notice->accomp;
		}
		return $collation;
	}

	protected function get_display_categories() {
		global $base_path;
		global $thesaurus_mode_pmb, $thesaurus_categories_categ_in_line, $pmb_keyword_sep, $thesaurus_categories_affichage_ordre;
		global $lang;
		global $categories_memo,$libelle_thesaurus_memo;
		global $categories_top,$use_opac_url_base,$opac_url_base,$thesaurus_categories_show_only_last, $opac_categories_show_only_last;

		$categ_repetables = array() ;
		if ($this->context_dsi_id_bannette) {
			$categories_show_only_last = $opac_categories_show_only_last;
		} else {
			$categories_show_only_last = $thesaurus_categories_show_only_last;
		}
		if(!is_array($categories_top)) {
			$categories_top = array();
		}
		if(!count($categories_top)) {
			$q = "select id_noeud from noeuds where autorite='TOP' ";
			$r = pmb_mysql_query($q);
			while($res = pmb_mysql_fetch_object($r)) {
				$categories_top[]=$res->id_noeud;
			}
		}
		$requete = "select * from (
			select libelle_thesaurus, if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as categ_libelle, noeuds.id_noeud , noeuds.num_parent, langue_defaut,id_thesaurus, if(catdef.langue = '".$lang."',2, if(catdef.langue= thesaurus.langue_defaut ,1,0)) as p, ordre_vedette, ordre_categorie
			FROM ((noeuds
			join thesaurus ON thesaurus.id_thesaurus = noeuds.num_thesaurus
			left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = thesaurus.langue_defaut
			left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."'))
			,notices_categories
			where notices_categories.num_noeud=noeuds.id_noeud and
			notices_categories.notcateg_notice=".$this->notice_id."	order by id_thesaurus, noeuds.id_noeud, p desc
			) as list_categ group by id_noeud";
		if ($thesaurus_categories_affichage_ordre==1) $requete .= " order by ordre_vedette, ordre_categorie";

		$result_categ=@pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($result_categ)) {
			$anti_recurse=array();
			while($res_categ = pmb_mysql_fetch_object($result_categ)) {
				$libelle_thesaurus=$res_categ->libelle_thesaurus;
				$categ_id=$res_categ->id_noeud 	;
				$libelle_categ=$res_categ->categ_libelle ;
				$num_parent=$res_categ->num_parent ;
				$langue_defaut=$res_categ->langue_defaut ;
				$categ_head=0;
				if(in_array($num_parent,$categories_top)) $categ_head=1;

				if ($categories_show_only_last || $categ_head) {
					if ($use_opac_url_base) $url_base_lien_aut = $opac_url_base."index.php?&lvl=categ_see&id=" ;
					else $url_base_lien_aut=$base_path."/autorites.php?categ=see&sub=category&id=";
					if ( (SESSrights & AUTORITES_AUTH || $use_opac_url_base) && (!$this->print_mode) ) $libelle_aff_complet = "<a href='".$url_base_lien_aut.$categ_id."' class='lien_gestion'>".$libelle_categ."</a>";
					else $libelle_aff_complet =$libelle_categ;
					if ($thesaurus_mode_pmb) {
						$categ_repetables[$libelle_thesaurus][] = $libelle_aff_complet;
					} else $categ_repetables['MONOTHESAURUS'][] = $libelle_aff_complet;

				} else {
					if(!isset($categories_memo[$categ_id])) {
						$anti_recurse[$categ_id]=1;
						$path_table = array();
						$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle, num_renvoi_voir as categ_see, note_application as categ_comment, if(langue = '".$lang."',2, if(langue= '".$langue_defaut."' ,1,0)) as p
							FROM noeuds, categories where id_noeud ='".$num_parent."'
							AND noeuds.id_noeud = categories.num_noeud
							order by p desc limit 1";

						$result=@pmb_mysql_query($requete);
						if (pmb_mysql_num_rows($result)) {
							$parent = pmb_mysql_fetch_object($result);
							$anti_recurse[$parent->categ_id]=1;
							$path_table[] = array(
									'id' => $parent->categ_id,
									'libelle' => $parent->categ_libelle);

							// on remonte les ascendants
							if(!isset($parent->categ_parent)) $parent->categ_parent = 0;
							if(!isset($anti_recurse[$parent->categ_parent])) $anti_recurse[$parent->categ_parent] = 0;
							while (($parent->categ_parent)&&(!$anti_recurse[$parent->categ_parent])) {
								$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment, if(langue = '".$lang."',2, if(langue= '".$langue_defaut."' ,1,0)) as p
									FROM noeuds, categories where id_noeud ='".$parent->categ_parent."'
									AND noeuds.id_noeud = categories.num_noeud
									order by p desc limit 1";
								$result=@pmb_mysql_query($requete);
								if (pmb_mysql_num_rows($result)) {
									$parent = pmb_mysql_fetch_object($result);
									$anti_recurse[$parent->categ_id]=1;
									$path_table[] = array(
											'id' => $parent->categ_id,
											'libelle' => $parent->categ_libelle);
									if(!isset($parent->categ_parent)) $parent->categ_parent = 0;
									if(!isset($anti_recurse[$parent->categ_parent])) $anti_recurse[$parent->categ_parent] = 0;
								} else {
									break;
								}
							}
							$anti_recurse=array();
						} else $path_table=array();
						// ceci remet le tableau dans l'ordre général->particulier
						$path_table = array_reverse($path_table);
						if (!empty($path_table)) {
							$temp_table = array();
							foreach ($path_table as $l) {
								$temp_table[] = $l['libelle'];
							}
							$parent_libelle = implode(':', $temp_table);
							$catalog_form = $parent_libelle.':'.$libelle_categ;
						} else {
							$catalog_form = $libelle_categ;
						}

						if ($use_opac_url_base) $url_base_lien_aut = $opac_url_base."index.php?&lvl=categ_see&id=" ;
						else $url_base_lien_aut=$base_path."/autorites.php?categ=see&sub=category&id=";
						if ((SESSrights & AUTORITES_AUTH || $use_opac_url_base) && (!$this->print_mode) ) $libelle_aff_complet = "<a href='".$url_base_lien_aut.$categ_id."' class='lien_gestion'>".$catalog_form."</a>";
						else $libelle_aff_complet =$catalog_form;
						if ($thesaurus_mode_pmb) {
							$categ_repetables[$libelle_thesaurus][] = $libelle_aff_complet;
						} else $categ_repetables['MONOTHESAURUS'][] = $libelle_aff_complet;

						$categories_memo[$categ_id]=$libelle_aff_complet;
						$libelle_thesaurus_memo[$categ_id]=$libelle_thesaurus;

					} else {
						if ($thesaurus_mode_pmb) $categ_repetables[$libelle_thesaurus_memo[$categ_id]][] =$categories_memo[$categ_id];
						else $categ_repetables['MONOTHESAURUS'][] =$categories_memo[$categ_id] ;
					}
				}
			}
		}

		$tmpcateg_aff = '';
		foreach ($categ_repetables as $nom_thesaurus => $val_lib) {
			//c'est un tri par libellé qui est demandé
			if ($thesaurus_categories_affichage_ordre==0){
				$tmp=array();
				foreach ( $val_lib as $key => $value ) {
					$tmp[$key]=strip_tags($value);
				}
				$tmp=array_map("convert_diacrit",$tmp);//On enlève les accents
				$tmp=array_map("strtoupper",$tmp);//On met en majuscule
				asort($tmp);//Tri sur les valeurs en majuscule sans accent
				foreach ( $tmp as $key => $value ) {
					$tmp[$key]=$val_lib[$key];//On reprend les bons couples clé / libellé
				}
				$val_lib=$tmp;
			}

			if ($thesaurus_mode_pmb) {
				if (!$thesaurus_categories_categ_in_line) $categ_repetables_aff = "[".$nom_thesaurus."] ".implode("<br />[".$nom_thesaurus."] ",$val_lib) ;
				else $categ_repetables_aff = "<b>".$nom_thesaurus."</b><br />".implode(" $pmb_keyword_sep ",$val_lib) ;
			} else if (!$thesaurus_categories_categ_in_line) $categ_repetables_aff = implode("<br />",$val_lib) ;
			else $categ_repetables_aff = implode(" $pmb_keyword_sep ",$val_lib) ;

			if($categ_repetables_aff) $tmpcateg_aff .= "<br />$categ_repetables_aff";
		}
		return $tmpcateg_aff;
	}

	protected function get_display_relations_links() {
		global $base_path;
		global $load_tablist_js;

		$display = '';
		if ((count($this->childs) || $this->notice_relations->get_nb_pairs())&&(!$this->no_link)) {
			if(!$load_tablist_js) $this->isbd.="<script type='text/javascript' src='".$base_path."/javascript/tablist.js'></script>\n";
			$load_tablist_js=1;
			$this->isbd.="<br />";
			$anti_loop=$this->anti_loop;
			$anti_loop[]=$this->notice_id;
			//Notices horizontales liées
			if($this->notice_relations->get_nb_pairs()) {
				$display .= $this->notice_relations->get_display_links('pairs', $this->print_mode, $this->show_explnum, 1, $this->show_opac_hidden_fields, $anti_loop);
			}
			if(count($this->childs) && !$this->print_mode) {
				$display .= $this->notice_relations->get_display_links('childs', $this->print_mode, $this->show_explnum, 1, $this->show_opac_hidden_fields, $anti_loop);
			}
		}
		return $display;
	}

	protected function get_display_pperso() {
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			$perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				// ajout de && ($p['OPAC_SHOW']||$this->show_opac_hidden_fields) afin de masquer les champs masqué de l'OPAC en diff de bannette.
				if ($p["AFF"] !== '' && ($p['OPAC_SHOW'] || $this->show_opac_hidden_fields)) $perso_aff .="<br />".$p["TITRE"]." ".($p["TYPE"]=='html'?$p["AFF"]:nl2br($p["AFF"]));
			}
		}
		return $perso_aff;
	}

	protected function get_display_external() {
		global $msg;

		$display = '';

		$record_datas = static::get_record_datas($this->notice_id);
		$label = $record_datas->get_source_label();
		if($label) {
			$display .= "<br /><b>".$msg['external_source']." : </b>".$label;
		}
		return $display;
	}


	/**
	 * fonction retournant les infos d'exemplaires pour une notice donnée
	 * @param int $no_notice
	 * @param string $link_expl
	 * @param int $expl_bulletin
	 * @param boolean $loaned FALSE = tous les exemplaires, TRUE les exemplaires en cours de pret
	 * @return void|string
	 */
	public function show_expl_per_notice($no_notice, $link_expl='',$expl_bulletin=0, $loaned=false ) {
		global $msg, $base_path;
		global $explr_invisible, $explr_visible_unmod, $explr_visible_mod, $pmb_droits_explr_localises;
		global $pmb_expl_list_display_comments;
		global $pmb_sur_location_activate;
		global $pmb_expl_data,$charset;
		global $pmb_expl_display_location_without_expl;
		global $pmb_html_allow_expl_cote;
		global $pmb_transferts_actif, $pmb_pret_groupement;
		global $pmb_expl_order, $class_path;
		// params :
		// $no_notice= id de la notice
		// $link_expl= lien associé à l'exemplaire avec !!expl_id!! et !!expl_cb!! à mettre à jour

		if(!$no_notice && !$expl_bulletin) return '';

		$evth = events_handler::get_instance();
		$evt = new event_expl_display_overload('notice', 'show_expl_per_notice_action');
		$evt->set_params([
		    'no_notice' => $no_notice,
		    'link_expl' => $link_expl,
		    'expl_bulletin' => $expl_bulletin,
		    'loaned' => $loaned
		]);
		$evth->send($evt);
		$evt_result = $evt->get_overloads();
		if (isset($evt_result)) {
		    $entry = '';
		    foreach ($evt_result as $overload) {
		        $entry .= $overload;
		    }
		    if (!empty($entry)) {
		        $this->nb_expl = $evt->get_nb_expl();
    		    return $entry;
		    }
		}

		$explr_tab_invis=explode(",",$explr_invisible);
		$explr_tab_unmod=explode(",",$explr_visible_unmod);
		$explr_tab_modif=explode(",",$explr_visible_mod);

		// récupération du nombre total d'exemplaires
		if($expl_bulletin){
			$requete = "SELECT COUNT(1) FROM exemplaires WHERE expl_bulletin='$expl_bulletin' ";
		}else{
			$requete = "SELECT COUNT(1) FROM exemplaires WHERE expl_notice='$no_notice' ";
		}

		$res = pmb_mysql_query($requete);
		$nb_ex = pmb_mysql_result($res, 0, 0);

		if($nb_ex) {
			$expl_liste = '';
			// on récupère les données des exemplaires
			// visibilité des exemplaires:
			if ($pmb_droits_explr_localises && $explr_invisible) $where_expl_localises = "and expl_location not in ($explr_invisible)";
			else $where_expl_localises = "";

			//Liste des champs d'exemplaires
			if($pmb_sur_location_activate) $surloc_field="surloc_libelle,";
			if (!$pmb_expl_data) $pmb_expl_data="expl_cb,expl_cote,".$surloc_field."location_libelle,section_libelle,statut_libelle,tdoc_libelle";
			$colonnesarray=explode(",",$pmb_expl_data);
			if (!in_array("expl_cb", $colonnesarray)) array_unshift($colonnesarray, "expl_cb");
			$total_columns = count($colonnesarray);
			if ($pmb_pret_groupement || $pmb_transferts_actif) $total_columns++;

			//Présence de champs personnalisés
			if (strstr($pmb_expl_data, "#")) {
				$cp=new parametres_perso("expl");
			}
			if($expl_bulletin){
				$where_expl_notice_expl_bulletin = " and expl_bulletin='$expl_bulletin' ";
				$prefix ="bull_".$expl_bulletin;
			}else{
				$where_expl_notice_expl_bulletin = " and expl_notice='$no_notice' ";
				$prefix ="noti_".$no_notice;
			}
			$where_loaned = "";
			if($loaned) {
			    $where_loaned = " and pret.pret_idexpl is not null";
			}
			$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, docs_codestat.*, lenders.*, tdoc_libelle, ";
			if(in_array("surloc_libelle", $colonnesarray)){
				$requete .= "sur_location.*, ";
			}
			$requete .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
			$requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
			$requete .= " IF(pret_retour>sysdate(),0,1) as retard " ;
			$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
			$requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
			if(in_array("surloc_libelle", $colonnesarray)){
				$requete .= " left join sur_location on docs_location.surloc_num=sur_location.surloc_id ";
			}
			$requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
			$requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
			$requete .= " left join docs_codestat on exemplaires.expl_codestat=docs_codestat.idcode ";
			$requete .= " left join lenders on exemplaires.expl_owner=lenders.idlender ";
			$requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
			$requete .= " WHERE 1 $where_expl_notice_expl_bulletin $where_expl_localises $where_loaned";
			if($pmb_expl_order) {
			    $requete .= " order by ".$pmb_expl_order;
			}elseif(in_array("surloc_libelle", $colonnesarray)){
				$requete .= " order by surloc_libelle,location_libelle, section_libelle, expl_cote, expl_cb ";
			}else{
				$requete .= " order by location_libelle, section_libelle, expl_cote, expl_cb ";
			}
			$result = pmb_mysql_query($requete) or die ("<br />".pmb_mysql_error()."<br />".$requete);

			$nbr_expl = pmb_mysql_num_rows($result);
			if ($nbr_expl) {
				$expl_list_id = array();
				if($pmb_transferts_actif) $expl_list_id_transfer = array();
				while($expl = pmb_mysql_fetch_object($result)) {
					$expl->section_libelle = translation::get_translated_text($expl->idsection, "docs_section", "section_libelle", $expl->section_libelle);
					$expl->statut_libelle = translation::get_translated_text($expl->expl_statut, "docs_statut", "statut_libelle", $expl->statut_libelle);
					$expl_list_id[] = $expl->expl_id;
					//visibilité des exemplaires
					if ($pmb_droits_explr_localises) {
						$as_invis = array_search($expl->idlocation,$explr_tab_invis);
						$as_unmod = array_search($expl->idlocation,$explr_tab_unmod);
						$as_modif = array_search($expl->idlocation,$explr_tab_modif);
					} else {
						$as_invis = false;
						$as_unmod = false;
						$as_modif = true;
					}
					$tlink="";
					if ($link_expl) {
						if($expl_bulletin){
							$tlink="./catalog.php?categ=serials&sub=bulletinage&action=expl_form&bul_id=!!bull_id!!&expl_id=!!expl_id!!";
							$tlink = str_replace('!!bull_id!!', $expl_bulletin, $tlink);
							$tlink = str_replace('!!expl_id!!', $expl->expl_id, $tlink);
							$tlink = str_replace('!!expl_cb!!', rawurlencode($expl->expl_cb), $tlink);
						}else{
							$tlink = str_replace('!!expl_id!!', $expl->expl_id, $link_expl);
							$tlink = str_replace('!!expl_cb!!', rawurlencode($expl->expl_cb), $tlink);
							$tlink = str_replace('!!notice_id!!', $expl->expl_notice, $tlink);
						}

					}
					$expl_liste .= "<tr>";

					for ($i=0; $i<count($colonnesarray); $i++) {
						if (!(substr($colonnesarray[$i],0,1)=="#") && ($colonnesarray[$i] != "groupexpl_name")) {
							eval ("\$colencours=\$expl->".$colonnesarray[$i].";");
						}

						if (($i == 0) && ($expl->expl_note || $expl->expl_comment) && $pmb_expl_list_display_comments) $expl_rowspan = "rowspan='2'";
						else $expl_rowspan = "";
						$aff_column = "";
						$id_column = "";
						if (substr($colonnesarray[$i],0,1)=="#") {
							//champs personnalisés
							$id=substr($colonnesarray[$i],1);
							$cp->get_values($expl->expl_id);
							if (!$cp->no_special_fields) {
								$temp=$cp->get_formatted_output((isset($cp->values[$id]) ? $cp->values[$id] : array()), $id);
								if (!$temp) $temp="&nbsp;";
								$aff_column.=$temp;
							}
						} else if ($colonnesarray[$i]=="expl_cb") {
							if (($tlink) && ($as_modif!== FALSE && $as_modif!== NULL) ) {
								$aff_column .= "<a href='$tlink'>".$colencours."</a>";
							} else $aff_column .= $colencours;
						} else if ($colonnesarray[$i]=="expl_cote") {
							if ($pmb_html_allow_expl_cote) {
								$aff_column.="<strong>".$colencours."</strong>";
							} else {
								$aff_column.="<strong>".htmlentities($colencours,ENT_QUOTES, $charset)."</strong>";
							}
						} else if ($colonnesarray[$i]=="statut_libelle") {
							if($expl->pret_retour) {
								// exemplaire sorti
								$rqt_empr = "SELECT empr_nom, empr_prenom, id_empr, empr_cb FROM empr WHERE id_empr='$expl->pret_idempr' ";
								$res_empr = pmb_mysql_query($rqt_empr) ;
								$res_empr_obj = pmb_mysql_fetch_object($res_empr) ;
								$situation = "<strong>{$msg[358]} ".$expl->aff_pret_retour."</strong>";
								global $empr_show_caddie;
								if ($empr_show_caddie && (SESSrights & CIRCULATION_AUTH)) {
									$img_ajout_empr_caddie="<img src='".get_url_icon('basket_empr.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" onClick=\"openPopUp('".$base_path."/cart.php?object_type=EMPR&item=".$expl->pret_idempr."', 'cart')\">&nbsp;";
								} else $img_ajout_empr_caddie="";
								switch ($this->print_mode) {
									case '2':
										$situation .= "<br />$res_empr_obj->empr_prenom $res_empr_obj->empr_nom";
										break;
									default :
										$situation .= "<br />$img_ajout_empr_caddie<a href='".$base_path."/circ.php?categ=pret&form_cb=".rawurlencode($res_empr_obj->empr_cb)."'>$res_empr_obj->empr_prenom $res_empr_obj->empr_nom</a>";
										break;
								}
							} else {
								// tester si réservé
								$result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($expl->expl_cb)."' ") or die ("<br />".pmb_mysql_error()."<br />".$requete);
								$reserve = pmb_mysql_num_rows($result_resa);

								// tester à ranger
								$result_aranger = pmb_mysql_query(" select 1 from resa_ranger where resa_cb='".addslashes($expl->expl_cb)."' ") or die ("<br />".pmb_mysql_error()."<br />".$requete);
								$aranger = pmb_mysql_num_rows($result_aranger);

								if ($reserve) $situation = "<strong>".$msg['expl_reserve']."</strong>"; // exemplaire réservé
								elseif($expl->expl_retloc) $situation = $msg['resa_menu_a_traiter'];  // exemplaire à traiter
								elseif ($aranger) $situation = "<strong>".$msg['resa_menu_a_ranger']."</strong>"; // exemplaire à ranger
								elseif ($expl->pret_flag) $situation = "<strong>{$msg[359]}</strong>"; // exemplaire disponible
								else $situation = "";
							}

							$aff_column .= htmlentities($colencours,ENT_QUOTES, $charset);
							if ($situation) $aff_column .= "<br />$situation";
						} else if ($colonnesarray[$i]=="groupexpl_name") {
							$id_column = "id='groupexpl_name_".$expl->expl_cb."'";
							$colencours = groupexpls::get_group_name_expl($expl->expl_cb);
							$aff_column = htmlentities($colencours,ENT_QUOTES, $charset);
						} else if ($colonnesarray[$i]=="nb_prets") {
							$colencours = exemplaire::get_nb_prets_from_id($expl->expl_id);
							$aff_column = ($colencours ? htmlentities($colencours,ENT_QUOTES, $charset) : '');
						} else {
							$aff_column = htmlentities($colencours,ENT_QUOTES, $charset);
						}
						if ($i == 0 && $id_column ==""){
							$expl_liste .= "<td ".$expl_rowspan." id='expl_".$expl->expl_id."' class='expl-column-".$i."'>".$aff_column."</td>";
						} else {
							$expl_liste .= "<td ".$expl_rowspan." ".$id_column." class='expl-column-".$i."'>".$aff_column."</td>";
						}
					}
					if ($this->print_mode) {
						$expl_liste .= "<td>&nbsp;</td>";
					} else {

					    $cart_click = "";
					    $cart_link = "";
					    $drag_link = "";
						if(SESSrights & CATALOGAGE_AUTH){
							//le panier d'exemplaire
							$cart_click = "onClick=\"openPopUp('".$base_path."/cart.php?object_type=EXPL&item=".$expl->expl_id."', 'cart')\"";
							$cart_over_out = "onMouseOver=\"show_div_access_carts(event,".$expl->expl_id.",'EXPL',1);\" onMouseOut=\"set_flag_info_div(false);\"";
							$cart_link = "<a href='#' $cart_click $cart_over_out><img src='".get_url_icon('basket_small_20x20.gif')."' class='center' alt='basket' title=\"{$msg[400]}\"></a>";
							//l'icon pour le drag&drop de panier
// 							$drag_link = "<span onMouseOver='if(init_drag) init_drag();' id='EXPL_drag_" . $expl->expl_id . "'  dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".htmlentities ( $expl->expl_cb,ENT_QUOTES, $charset)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".get_url_icon('notice_drag.png')."\"/></span>";
						}

						//l'impression de la fiche exemplaire
						$fiche_click = "onClick=\"openPopUp('".$base_path."/pdf.php?pdfdoc=fiche_catalographique&expl_id=".$expl->expl_id."', 'Fiche', 500, 400, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\"";
						$fiche_link = "<a href='#' $fiche_click><img src='".get_url_icon('print.gif')."' class='center' alt='".$msg ['print_fiche_catalographique']."' title='".$msg ['print_fiche_catalographique']."'></a>";

						global $pmb_transferts_actif;

						//si les transferts sont activés
						if ($pmb_transferts_actif) {
							//si l'exemplaire n'est pas transferable on a une image vide
							$transfer_link = "<img src='".get_url_icon('spacer.gif')."' class='center' style='width:20px; height:20px' alt=''>";

							$dispo_pour_transfert = transfert::est_transferable ( $expl->expl_id );
							if (SESSrights & TRANSFERTS_AUTH && $dispo_pour_transfert) {
								//l'icon de demande de transfert
								$transfer_link = "<a href=\"#\" onClick=\"openPopUp('".$base_path."/catalog/transferts/transferts_popup.php?expl=".$expl->expl_id."', 'cart', 600, 450, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes');\"><img src='".get_url_icon('peb_in.png')."' class='center' border=0 alt=\"".$msg ["transferts_alt_libelle_icon"]."\" title=\"".$msg ["transferts_alt_libelle_icon"]."\"></a>";
								$expl_list_id_transfer[] = $expl->expl_id;
							}
						} else {
							$transfer_link = "";
						}

						//on met tout dans la colonne
						$expl_liste .= "<td>".((isset($fiche_link) && $fiche_link) ? $fiche_link." " : "").((isset($cart_link) && $cart_link) ? $cart_link." " : "").((isset($transfer_link) && $transfer_link) ? $transfer_link." " : "").((isset($drag_link) && $drag_link) ? $drag_link : "")."</td>";
					}
					if (($pmb_pret_groupement || $pmb_transferts_actif) && !$this->print_mode) {
						$expl_liste .= "<td class='center'><input type='checkbox' id='checkbox_expl[".$expl->expl_id."]' name='checkbox_expl[".$expl->expl_id."]' /></td>";
					}
					$expl_liste .= "</tr>";
					if (($expl->expl_note || $expl->expl_comment) && $pmb_expl_list_display_comments) {
						$notcom=array();
						$expl_liste .= "<tr><td colspan='".$total_columns."'>";
						if ($expl->expl_note && ($pmb_expl_list_display_comments & 1)) {
						    $notcom[] = "<span class='erreur'>$expl->expl_note</span>";
						}
						if ($expl->expl_comment && ($pmb_expl_list_display_comments & 2)) {
						    $notcom[] = "<span class='expl_list_comment'>".nl2br($expl->expl_comment)."</span>";
						}
						$expl_liste .= implode("<br />",$notcom);
						$expl_liste .= "</tr>";
					}
				} // fin while
			} // fin il y a des expl visibles

			if ($expl_liste) {
				$entry = "";
				if(($pmb_pret_groupement || $pmb_transferts_actif) && !$this->print_mode) {
					if ($pmb_pret_groupement) $on_click_groupexpl = "if(check_if_checked(document.getElementById('".$prefix."_expl_list_id').value,'groupexpl')) openPopUp('./select.php?what=groupexpl&caller=form_".$prefix."_expl&expl_list_id='+get_expl_checked(document.getElementById('".$prefix."_expl_list_id').value), 'selector')";
					if ($pmb_transferts_actif) $on_click_transferts = "if(check_if_checked(document.getElementById('".$prefix."_expl_list_id_transfer').value,'transfer')) openPopUp('./catalog/transferts/transferts_popup.php?expl='+get_expl_checked(document.getElementById('".$prefix."_expl_list_id_transfer').value), 'selector')";
					$entry .= "
								<script type='text/javascript' src='./javascript/expl_list.js'></script>
								<script type='text/javascript'>
			 						var msg_select_all = '".$msg["notice_expl_check_all"]."';
			 						var msg_unselect_all = '".$msg["notice_expl_uncheck_all"]."';
			 						var msg_have_select_expl = '".$msg["notice_expl_have_select_expl"]."';
			 						var msg_have_select_transfer_expl = '".$msg["notice_expl_have_select_transfer_expl"]."';
			 						var msg_have_same_loc_expl = '".$msg["notice_expl_have_same_loc_expl"]."';
			 					</script>
			 					<table style='border:0px' class='expl-list'>
									<tr>
										<th colspan='".count($colonnesarray)."'>
											".$msg["notice_for_expl_checked"]."
											".($pmb_pret_groupement ? "<input class='bouton' type='button' value=\"".$msg["notice_for_expl_checked_groupexpl"]."\" onClick=\"".$on_click_groupexpl."\" />&nbsp;&nbsp;" : "")."
											".($pmb_transferts_actif ? "<input class='bouton' type='button' value=\"".$msg["notice_for_expl_checked_transfert"]."\" onClick=\"".$on_click_transferts."\" />" : "")."
										</th>
									</tr>
								</table>";
				}
				// On ne propose pas le tri en impression ainsi qu'avec les commentaires (on ne sait pas encore faire)
				if ($this->print_mode || $pmb_expl_list_display_comments) {
					$entry .= "<table style='border:0px' class='expl-list'>";
				} else {
					$entry .= "<table style='border:0px' class='expl-list sortable'>";
				}
				$entry .= "<tr>";
				for ($i=0; $i<count($colonnesarray); $i++) {
					if (substr($colonnesarray[$i],0,1)=="#") {
						//champs personnalisés
						if (!$cp->no_special_fields) {
							$id=substr($colonnesarray[$i],1);
							$entry.="<th>".htmlentities($cp->t_fields[$id]['TITRE'],ENT_QUOTES,$charset)."</th>";
						}
					} else {
						eval ("\$colencours=\$msg['expl_header_".$colonnesarray[$i]."'];");
						$entry.="<th>".htmlentities($colencours,ENT_QUOTES, $charset)."</th>";
					}
				}
				if(!$this->print_mode) {
					$entry.="<th>&nbsp;</th>";
				}
				if(($pmb_pret_groupement || $pmb_transferts_actif) && !$this->print_mode) {
					if(empty($expl_list_id_transfer)) {
						$expl_list_id_transfer = array();
					}
					$entry.="<th class='center'>
									<input type='checkbox' onclick=\"check_all_expl(this,document.getElementById('".$prefix."_expl_list_id').value)\" title='".$msg["notice_expl_check_all"]."' id='".$prefix."_select_all' name='".$prefix."_select_all' />
									<input type='hidden' id='".$prefix."_expl_list_id' name='".$prefix."_expl_list_id' value='".implode(",", $expl_list_id)."' />
									<input type='hidden' id='".$prefix."_expl_list_id_transfer' name='".$prefix."_expl_list_id_transfer' value='".implode(",", $expl_list_id_transfer)."' />
								</th>";
				}
				$entry .="</tr>$expl_liste</table>";
			} else $entry = "";

			if($pmb_expl_display_location_without_expl){
				if ($pmb_sur_location_activate) {
					$array_surloc = array();
					$requete = "SELECT * FROM sur_location ORDER BY surloc_libelle";
					$result = pmb_mysql_query($requete) or die ("<br />".pmb_mysql_error()."<br />".$requete);
					$nb_surloc = pmb_mysql_num_rows($result);
					if ($nb_surloc) {
						while($surloc = pmb_mysql_fetch_object($result)) {
							$array_surloc[]=array("id"=>$surloc->surloc_id, "libelle"=>$surloc->surloc_libelle, "locations"=>array());
						}
					}
					if (count($array_surloc)) {
						foreach ($array_surloc as $key=>$surloc) {
							$requete = "SELECT idlocation, location_libelle from docs_location where surloc_num=".$surloc["id"]." AND
							idlocation not in (SELECT expl_location from exemplaires WHERE expl_notice=$no_notice) order by location_libelle";

							$result = pmb_mysql_query($requete) or die ("<br />".pmb_mysql_error()."<br />".$requete);
							$nb_loc = pmb_mysql_num_rows($result);
							if ($nb_loc) {
								while($loc = pmb_mysql_fetch_object($result)) {
									$array_surloc[$key]["locations"][] = array("id"=>$loc->idlocation, "libelle"=>$loc->location_libelle);
								}
							} else {
								unset($array_surloc[$key]);
							}
						}
					}
					//Au moins une surloc à afficher
					if (count($array_surloc)) {
						$tr_surloc="";
						foreach ($array_surloc as $key => $surloc) {
							$tr_surloc.="<tr><td>";
							$tr_loc="";
							foreach ($surloc["locations"] as $loc) {
								$tr_loc.="<tr><td>".$loc["libelle"]."</td></tr>";
							}
							$tpl_surloc= "
							<table style='border:0px' class='expl-list'>
							$tr_loc
							</table>";
							$tr_surloc.=gen_plus('surlocation_without_expl'.$key.'_'.$no_notice,$surloc["libelle"],$tpl_surloc,0);
							$tr_surloc.="</td></tr>";
						}
						$tpl = "
						<table style='border:0px' class='expl-list'>
						$tr_surloc
						</table>";
						$entry.=gen_plus('location_without_expl'.$no_notice,$msg['expl_surlocation_without_expl'],$tpl,0);
					}
				} else {
					$requete = "SELECT location_libelle from docs_location where
					idlocation not in (SELECT expl_location from exemplaires WHERE expl_notice=$no_notice) order by location_libelle";

					$result = pmb_mysql_query($requete) or die ("<br />".pmb_mysql_error()."<br />".$requete);
					$nb_loc = pmb_mysql_num_rows($result);
					if ($nb_loc) {
						$items="";
						while($loc = pmb_mysql_fetch_object($result)) {
							$items.="<tr><td>".$loc->location_libelle."</td></tr>";
						}

						$tpl = "
						<table style='border:0px' class='expl-list'>
						$items
						</table>";
						$tpl=gen_plus('location_without_expl'.$no_notice,$msg['expl_location_without_expl'],$tpl,0);
						$entry.=$tpl;
					}
				}
			}
			$this->nb_expl=$nbr_expl;
			return $entry;
		} else {
			return "";
		}
	}

	public function show_orders_pnb($notice_id) {
		$pnb_record_orders = new pnb_record_orders($notice_id);
		$this->nb_expl = $pnb_record_orders->get_orders_number();
		return $pnb_record_orders->get_display_orders();
	}

	// finalisation du résultat (écriture de l'isbd)
	public function finalize() {
		$this->result = str_replace('!!ISBD!!', $this->isbd, $this->result);
	}

	public function get_context_parameters() {
		return $this->context_parameters;
	}

	public function set_context_parameters($context_parameters=array()) {
		$this->context_parameters = $context_parameters;
	}

	public function add_context_parameter($key, $value) {
		$this->context_parameters[$key] = $value;
	}

	public function delete_context_parameter($key) {
		unset($this->context_parameters[$key]);
	}

	/**
	 * Retourne une instance de record_datas
	 * @param int $notice_id Identifiant de la notice
	 * @return record_datas
	 */
	static public function get_record_datas($notice_id) {
		if (!isset(self::$records_datas[$notice_id])) {
			self::$records_datas[$notice_id] = new record_datas($notice_id);
		}
		return self::$records_datas[$notice_id];
	}

	static public function unset_record_datas($notice_id) {
	    if (isset(self::$records_datas[$notice_id])) {
	        unset(self::$records_datas[$notice_id]);
	    }
	}

	/**
	 * Ré-initialisation du singleton record_datas
	 * @param int $notice_id Identifiant de la notice
	 * @return record_datas
	 */
	static public function init_record_datas($notice_id) {
		self::$records_datas[$notice_id] = new record_datas($notice_id);
		return self::$records_datas[$notice_id];
	}

	static public function lookup($name, $object) {
		$return = null;
		// Si on le nom commence par record. on va chercher les méthodes
		if (substr($name, 0, 8) == ":record.") {
			$attributes = explode('.', $name);
			$notice_id = $object->getVariable('notice_id');

			// On va chercher dans record_display
			$return = static::look_for_attribute_in_class("record_display", $attributes[1], array($notice_id));

			if (!$return) {
				// On va chercher dans record_datas
				$record_datas = static::get_record_datas($notice_id);
				$return = static::look_for_attribute_in_class($record_datas, $attributes[1]);
			}

			// On regarde les attributs enfants recherchés
			if ($return && count($attributes) > 2) {
				for ($i = 2; $i < count($attributes); $i++) {
					// On regarde si c'est un tableau ou un objet
					if (is_array($return)) {
						$return = (isset($return[$attributes[$i]]) ? $return[$attributes[$i]] : '');
					} else if (is_object($return)) {
						$return = static::look_for_attribute_in_class($return, $attributes[$i]);
					} else {
						$return = null;
						break;
					}
				}
			}
		} else {
			$attributes = explode('.', $name);
			// On regarde si on a directement une instance d'objet, dans le cas des boucles for
			if (is_object($obj = $object->getVariable(substr($attributes[0], 1))) && (count($attributes) > 1)) {
				$return = $obj;
				for ($i = 1; $i < count($attributes); $i++) {
					// On regarde si c'est un tableau ou un objet
					if (is_array($return)) {
						$return = $return[$attributes[$i]];
					} else if (is_object($return)) {
						$return = static::look_for_attribute_in_class($return, $attributes[$i]);
					} else {
						$return = null;
						break;
					}
				}
			}
		}
		return $return;
	}

	static protected function look_for_attribute_in_class($class, $attribute, $parameters = array()) {
		if (is_object($class) && (isset($class->{$attribute}) || method_exists($class, '__get'))) {
			return $class->{$attribute};
		} else if (method_exists($class, $attribute)) {
			return call_user_func_array(array($class, $attribute), $parameters);
		} else if (method_exists($class, "get_".$attribute)) {
			return call_user_func_array(array($class, "get_".$attribute), $parameters);
		} else if (method_exists($class, "is_".$attribute)) {
			return call_user_func_array(array($class, "is_".$attribute), $parameters);
		}
		return null;
	}

	static private function render($notice_id, $tpl, $unimarc = false, $parameters = '') {
		$h2o = H2o_collection::get_instance($tpl);
		H2o_collection::addLookup("record_display::lookup");
		return $h2o->render(array('notice_id' => $notice_id, 'unimarc' => $unimarc, 'parameters'=>$parameters));
	}

	/**
	 * Génère le titre nécessaire à Zotéro
	 * @param int $id_notice Identifiant de la notice
	 * @return string
	 */
	static public function get_display_coins_title($notice_id){
		$display = '';
		$record_datas = static::get_record_datas($notice_id);
		$coins = $record_datas->get_coins();
		foreach ($coins as $key=>$value) {
			if(is_array($value)) { //Spécifique rft.aut
				foreach ($value as $sub_value) {
					$display .= "&amp;".$key."=".rawurlencode(encoding_normalize::utf8_normalize($sub_value));
				}
			} else {
				$display .= "&amp;".$key."=".rawurlencode(encoding_normalize::utf8_normalize($value));
			}
		}
		return $display;
	}

	/**
	 * Génère le span nécessaire à Zotéro
	 * @param int $id_notice Identifiant de la notice
	 * @return string
	 */
	static public function get_display_coins_span($notice_id){
		// Attention!! Fait pour Zotero qui ne traite pas toute la norme ocoins

		$record_datas = static::get_record_datas($notice_id);

		// http://generator.ocoins.info/?sitePage=info/book.html&
		// http://ocoins.info/cobg.html
		$coins_span="<span class='Z3988' title='ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3A";

		switch ($record_datas->get_niveau_biblio()){
			case 's':// periodique
// 				$coins_span.="book";
			break;
			case 'a': // article
				$coins_span.="journal";
			break;
			case 'b': //Bulletin
// 				$coins_span.="book";
			break;
			case 'm':// livre
			default:
				$coins_span.="book";
			break;
		}
		$coins_span.=static::get_display_coins_title($notice_id);
		$coins_span.="'></span>";
		return 	$coins_span;
	}

	static public function get_display_column($label='', $expl=array()) {
		global $msg, $charset;

		$column = '';
		if (($label == "location_libelle") && $expl['num_infopage']) {
			if ($expl['surloc_id'] != "0") $param_surloc="&surloc=".$expl['surloc_id'];
			else $param_surloc="";
			$column .="<td class='".$label."'><a href=\"".static::get_parameter_value('url_base')."index.php?lvl=infopages&pagesid=".$expl['num_infopage']."&location=".$expl['expl_location'].$param_surloc."\" title=\"".$msg['location_more_info']."\">".htmlentities($expl[$label], ENT_QUOTES, $charset)."</a></td>";
		} else if ($label=="expl_comment") {
			$column.="<td class='".$label."'>".nl2br(htmlentities($expl[$label],ENT_QUOTES, $charset))."</td>";
		} elseif ($label=="expl_cb") {
			$column.="<td id='expl_" . $expl['expl_id'] . "' class='".$label."'>".htmlentities($expl[$label],ENT_QUOTES, $charset)."</td>";
		} else {
			$column .="<td class='".$label."'>".htmlentities($expl[$label],ENT_QUOTES, $charset)."</td>";
		}
		return $column;
	}

	 static public function get_display_situation($expl) {
		global $msg, $charset;
		global $opac_show_empr ;
		global $pmb_transferts_actif, $transferts_statut_transferts;

		$situation = "";
		if ($expl['statut_libelle_opac'] != "") $situation .= $expl['statut_libelle_opac']."<br />";
		if ($expl['flag_resa']) {
			$situation .= "<strong>".$msg['expl_reserve']."</strong>";
		} else {
			if ($expl['pret_flag']) {
				if($expl['pret_retour']) { // exemplaire sorti
					if ((($opac_show_empr==1) && ($_SESSION["user_code"])) || ($opac_show_empr==2)) {
						$situation .= $msg['entete_show_empr'].htmlentities(" ".$expl['empr_prenom']." ".$expl['empr_nom'],ENT_QUOTES, $charset)."<br />";
					}
					$situation .= "<strong>".str_replace('!!date!!', formatdate($expl['pret_retour']), $msg['out_until'] )."</strong>";
					// ****** Affichage de l'emprunteur
				} else { // pas sorti
					$situation .= "<strong>".$msg['available']."</strong>";
				}
			} else { // pas prêtable
				// exemplaire pas prêtable, on affiche juste "exclu du pret"
				if (($pmb_transferts_actif=="1") && ("".$expl['expl_statut'].""==$transferts_statut_transferts)) {
					$situation .= "<strong>".$msg['reservation_lib_entransfert']."</strong>";
				} else {
					$situation .= "<strong>".$msg['exclu']."</strong>";
				}
			}
		} // fin if else $flag_resa
		return $situation;
	}

	/**
	 * Génère la liste des exemplaires
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_expl_list($notice_id) {
		global $msg, $charset;
		global $expl_list_header, $expl_list_footer;
		global $pmb_transferts_actif,$transferts_statut_transferts;
		global $memo_p_perso_expl;
		global $opac_show_empty_items_block ;
		global $opac_show_exemplaires_analysis;
		global $expl_list_header_loc_tpl,$opac_aff_expl_localises;

		$nb_expl_visible = 0;
		$nb_expl_autre_loc=0;
		$nb_perso_aff=0;

		$record_datas = static::get_record_datas($notice_id);

		$type = $record_datas->get_niveau_biblio();
		$id = $record_datas->get_id();
		$bull = $record_datas->get_bul_info();
		$bull_id = $bull['bulletin_id'];

		// les dépouillements ou périodiques n'ont pas d'exemplaire
		if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "" ;
		if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
		$header_found_p_perso=0;

		$expls_datas = $record_datas->get_expls_datas();

		$expl_list_header_deb="";
		if (isset($expls_datas['colonnesarray']) && is_array($expls_datas['colonnesarray'])) {
    		foreach ($expls_datas['colonnesarray'] as $colonne) {
    			$expl_list_header_deb .= "<th class='expl_header_".$colonne."'>".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."</th>";
    		}
		}
		$expl_list_header_deb.="<th class='expl_header_statut'>".$msg['statut']."</th>";
		$expl_liste="";

		if(is_array($expls_datas['expls']) && count($expls_datas['expls'])) {
			foreach ($expls_datas['expls'] as $expl) {
				$expl_liste .= "<tr>";

				foreach ($expls_datas['colonnesarray'] as $colonne) {
					$expl_liste .= static::get_display_column($colonne, $expl);
				}

				if ($expl['flag_resa']) {
					$class_statut = "expl_reserve";
				} else {
					if ($expl['pret_flag']) {
						if($expl['pret_retour']) { // exemplaire sorti
							$class_statut = "expl_out";
						} else { // pas sorti
							$class_statut = "expl_available";
						}
					} else { // pas prêtable
						// exemplaire pas prêtable, on affiche juste "exclu du pret"
						if (($pmb_transferts_actif=="1") && ("".$expl['expl_statut'].""==$transferts_statut_transferts)) {
							$class_statut = "expl_transfert";
						} else {
							$class_statut = "expl_unavailable";
						}
					}
				} // fin if else $flag_resa
				$expl_liste .= "<td class='expl_situation'>".static::get_display_situation($expl)." </td>";
				$expl_liste = str_replace("!!class_statut!!", $class_statut, $expl_liste);

				//Champs personalisés
				$perso_aff = "" ;
				if (!$memo_p_perso_expl->no_special_fields) {
					$perso_=$memo_p_perso_expl->show_fields($expl['expl_id']);
					for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
						$p=$perso_["FIELDS"][$i];
						if ($p['OPAC_SHOW'] ) {
							if(!$header_found_p_perso) {
								$header_perso_aff.="<th class='expl_header_tdoc_libelle'>".$p["TITRE_CLEAN"]."</th>";
								$nb_perso_aff++;
							}
							if( $p["AFF"] !== '')	{
								$perso_aff.="<td class='p_perso'>".$p["AFF"]."</td>";
							}
							else $perso_aff.="<td class='p_perso'></td>";
						}
					}
				}
				$header_found_p_perso=1;
				$expl_liste.=$perso_aff;

				$expl_liste .="</tr>";
				$expl_liste_all.=$expl_liste;

				if($opac_aff_expl_localises && $_SESSION["empr_location"]) {
					if($expl['expl_location']==$_SESSION["empr_location"]) {
						$expl_liste_loc.=$expl_liste;
					} else $nb_expl_autre_loc++;
				}
				$expl_liste="";
				$nb_expl_visible++;

			} // fin foreach
		}
		$expl_list_header_deb="<tr class='thead'>".$expl_list_header_deb;
		//S'il y a des titres de champs perso dans les exemplaires
		if($header_perso_aff) {
			$expl_list_header_deb.=$header_perso_aff;
		}
		$expl_list_header_deb.="</tr>";

		if($opac_aff_expl_localises && $_SESSION["empr_location"] && $nb_expl_autre_loc) {
			// affichage avec onglet selon la localisation
			if(!$expl_liste_loc) {
				$expl_liste_loc="<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1+$nb_perso_aff)."'>".$msg["no_expl"]."</td></tr>";
			}
			$expl_liste_all=str_replace("!!EXPL!!",$expl_list_header_deb.$expl_liste_all,$expl_list_header_loc_tpl);
			$expl_liste_all=str_replace("!!EXPL_LOC!!",$expl_list_header_deb.$expl_liste_loc,$expl_liste_all);
			$expl_liste_all=str_replace("!!mylocation!!",$_SESSION["empr_location_libelle"],$expl_liste_all);
			$expl_liste_all=str_replace("!!id!!",$id+$bull_id,$expl_liste_all);
		} else {
			// affichage de la liste d'exemplaires calculée ci-dessus
			if (!$expl_liste_all && $opac_show_empty_items_block==1) {
				$expl_liste_all = $expl_list_header.$expl_list_header_deb."<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1)."'>".$msg["no_expl"]."</td></tr>".$expl_list_footer;
			} elseif (!$expl_liste_all && $opac_show_empty_items_block==0) {
				$expl_liste_all = "";
			} else {
				$expl_liste_all = $expl_list_header.$expl_list_header_deb.$expl_liste_all.$expl_list_footer;
			}
		}
		$expl_liste_all=str_replace("<!--nb_expl_visible-->",($nb_expl_visible ? " (".$nb_expl_visible.")" : ""),$expl_liste_all);
		return $expl_liste_all;

	} // fin function get_display_expl_list

	/**
	 * Génère la liste des exemplaires
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_expl_responsive_list($notice_id) {
		global $msg, $charset;
		global $expl_list_header, $expl_list_footer;
		global $opac_expl_data, $opac_expl_order, $opac_url_base;
		global $pmb_transferts_actif,$transferts_statut_transferts;
		global $memo_p_perso_expl;
		global $opac_show_empty_items_block ;
		global $opac_show_exemplaires_analysis;
		global $expl_list_header_loc_tpl,$opac_aff_expl_localises;

		$nb_expl_visible = 0;
		$nb_expl_autre_loc=0;
		$nb_perso_aff=0;

		$record_datas = static::get_record_datas($notice_id);
		if(!$record_datas->is_numeric()){
			$type = $record_datas->get_niveau_biblio();
			$id = $record_datas->get_id();
			$bull = $record_datas->get_bul_info();
			$bull_id = (isset($bull['bulletin_id']) ? $bull['bulletin_id'] : '');

			// les dépouillements ou périodiques n'ont pas d'exemplaire
			if (($type=="a" && !$opac_show_exemplaires_analysis) || $type=="s") return "" ;
			if(!$memo_p_perso_expl)	$memo_p_perso_expl=new parametres_perso("expl");
			$header_found_p_perso=0;

			$expls_datas = $record_datas->get_expls_datas();
			$expl_list_header_deb="<tr class='thead'>";
			foreach ($expls_datas['colonnesarray'] as $colonne) {
				$expl_list_header_deb .= "<th class='expl_header_".$colonne."'>".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."</th>";
			}
			$expl_list_header_deb.="<th class='expl_header_statut'>".$msg['statut']."</th>";
			$expl_liste="";
			$expl_liste_all="";
			$header_perso_aff="";

			if(count($expls_datas['expls'])) {
				$customization_expl_columns = array();
				$special = static::get_special($notice_id);
				if(!empty($special) && method_exists($special, "get_customization_expl_columns")) {
				    $customization_expl_columns = $special->get_customization_expl_columns();
				}
				foreach ($expls_datas['expls'] as $expl) {
					$expl_liste .= "<tr class='item_expl !!class_statut!!'>";

					foreach ($expls_datas['colonnesarray'] as $colonne) {
						if(isset($customization_expl_columns[$colonne])) {
							$expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."'>";
							if(isset($customization_expl_columns[$colonne]['htmlentities']) && $customization_expl_columns[$colonne]['htmlentities'] == false) {
								$expl_liste .=strip_tags($expl[$colonne], $customization_expl_columns[$colonne]['keep_tags']);
							} else {
								$expl_liste .=htmlentities($expl[$colonne],ENT_QUOTES, $charset);
							}
							$expl_liste .="</td>";
						} elseif (($colonne == "location_libelle") && $expl['num_infopage']) {
							if ($expl['surloc_id'] != "0") {
								$param_surloc="&surloc=".$expl['surloc_id'];
							} else {
								$param_surloc="";
							}
							$expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."'><a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$expl['num_infopage']."&location=".$expl['expl_location'].$param_surloc."\" title=\"".$msg['location_more_info']."\">".htmlentities($expl[$colonne], ENT_QUOTES, $charset)."</a></td>";
						} elseif($colonne == "expl_cb") {
							$expl_liste .="<td id='expl_" . $expl['expl_id'] . "' class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."'>".htmlentities($expl[$colonne],ENT_QUOTES, $charset)."</td>";
						} else {
							$expl_liste .="<td class='".htmlentities($msg['expl_header_'.$colonne],ENT_QUOTES, $charset)."'>".htmlentities($expl[$colonne],ENT_QUOTES, $charset)."</td>";
						}

					}

					if ($expl['flag_resa']) {
						$class_statut = "expl_reserve";
					} else {
						if ($expl['pret_flag']) {
							if($expl['pret_retour']) { // exemplaire sorti
								$class_statut = "expl_out";
							} else { // pas sorti
								$class_statut = "expl_available";
							}
						} else { // pas prêtable
							// exemplaire pas prêtable, on affiche juste "exclu du pret"
							if (($pmb_transferts_actif=="1") && ("".$expl['expl_statut'].""==$transferts_statut_transferts)) {
								$class_statut = "expl_transfert";
							} else {
								$class_statut = "expl_unavailable";
							}
						}
					} // fin if else $flag_resa
					$expl_liste .= "<td class='".$msg['statut']."'>".static::get_display_situation($expl)." </td>";
					$expl_liste = str_replace("!!class_statut!!", $class_statut, $expl_liste);

					//Champs personalisés
					$perso_aff = "" ;
					if (!$memo_p_perso_expl->no_special_fields) {
						$perso_=$memo_p_perso_expl->show_fields($expl['expl_id']);
						for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
							$p=$perso_["FIELDS"][$i];
							if ($p['OPAC_SHOW'] ) {
								if(!$header_found_p_perso) {
									$header_perso_aff.="<th class='expl_header_tdoc_libelle'>".$p["TITRE_CLEAN"]."</th>";
									$nb_perso_aff++;
								}
								if( $p["AFF"] !== '')	{
									$perso_aff.="<td class='p_perso'>".$p["AFF"]."</td>";
								}
								else $perso_aff.="<td class='p_perso'></td>";
							}
						}
					}
					$header_found_p_perso=1;
					$expl_liste.=$perso_aff;

					$expl_liste .="</tr>";
					$expl_liste_all.= $expl_liste;

					if($opac_aff_expl_localises && $_SESSION["empr_location"]) {
						if($expl['expl_location']==$_SESSION["empr_location"]) {
							$expl_liste_loc.=$expl_liste;
						} else {
							$nb_expl_autre_loc++;
						}
					}
					$expl_liste="";
					$nb_expl_visible++;
				} // fin foreach
			}
			//S'il y a des titres de champs perso dans les exemplaires
			if($header_perso_aff) {
				$expl_list_header_deb.=$header_perso_aff;
			}
			$expl_list_header_deb.="</tr>";
			if($opac_aff_expl_localises && $_SESSION["empr_location"] && $nb_expl_autre_loc) {
				// affichage avec onglet selon la localisation
				if(!$expl_liste_loc) {
					$expl_liste_loc="<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1+$nb_perso_aff)."'>".$msg["no_expl"]."</td></tr>";
				}
				$expl_liste_all=str_replace("!!EXPL!!",$expl_list_header_deb.$expl_liste_all,$expl_list_header_loc_tpl);
				$expl_liste_all=str_replace("!!EXPL_LOC!!",$expl_list_header_deb.$expl_liste_loc,$expl_liste_all);
				$expl_liste_all=str_replace("!!mylocation!!",$_SESSION["empr_location_libelle"],$expl_liste_all);
				$expl_liste_all=str_replace("!!id!!",$id+$bull_id,$expl_liste_all);
			} else {
				// affichage de la liste d'exemplaires calculée ci-dessus
				if (!$expl_liste_all && $opac_show_empty_items_block==1) {
					$expl_liste_all = $expl_list_header.$expl_list_header_deb."<tr class=even><td colspan='".(count($expls_datas['colonnesarray'])+1)."'>".$msg["no_expl"]."</td></tr>".$expl_list_footer;
				} elseif (!$expl_liste_all && $opac_show_empty_items_block==0) {
					$expl_liste_all = "";
				} else {
					$expl_liste_all = $expl_list_header.$expl_list_header_deb.$expl_liste_all.$expl_list_footer;
				}
			}
			$expl_liste_all=str_replace("<!--nb_expl_visible-->",($nb_expl_visible ? " (".$nb_expl_visible.")" : ""),$expl_liste_all);
			return $expl_liste_all;
		}
		return '';
	} // fin function get_display_expl_responsive_list

	/**
	 * Fontion qui génère le bloc H3 + table des autres lectures
	 * @param number $notice_id Identifiant de la notice
	 * @param number $bulletin_id Identifiant du bulletin
	 * @return string
	 */
	static public function get_display_other_readings($notice_id) {
		global $dbh, $msg;
		global $opac_autres_lectures_tri;
		global $opac_autres_lectures_nb_mini_emprunts;
		global $opac_autres_lectures_nb_maxi;
		global $opac_autres_lectures_nb_jours_maxi;
		global $opac_autres_lectures;
		global $gestion_acces_active,$gestion_acces_empr_notice;

		$record_datas = static::get_record_datas($notice_id);
		$bull = $record_datas->get_bul_info();
		$bulletin_id = (isset($bull['bulletin_id']) ? $bull['bulletin_id'] : '');

		if (!$opac_autres_lectures || (!$notice_id && !$bulletin_id)) return "";

		if (!$opac_autres_lectures_nb_maxi) $opac_autres_lectures_nb_maxi = 999999 ;
		if ($opac_autres_lectures_nb_jours_maxi) $restrict_date=" date_add(oal.arc_fin, INTERVAL $opac_autres_lectures_nb_jours_maxi day)>=sysdate() AND ";
		else $restrict_date="";
		if ($notice_id) $pas_notice = " oal.arc_expl_notice!=$notice_id AND ";
		else $pas_notice = "";
		if ($bulletin_id) $pas_bulletin = " oal.arc_expl_bulletin!=$bulletin_id AND ";
		else $pas_bulletin = "";
		// Ajout ici de la liste des notices lues par les lecteurs de cette notice
		$rqt_autres_lectures = "SELECT oal.arc_expl_notice, oal.arc_expl_bulletin, count(*) AS total_prets,
					trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '%d/%m/%Y'),')') ,'')))) as tit, if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id
				FROM ((((pret_archive AS oal JOIN
					(SELECT distinct arc_id_empr FROM pret_archive nbec where (nbec.arc_expl_notice='".$notice_id."' AND nbec.arc_expl_bulletin='".$bulletin_id."') AND nbec.arc_id_empr !=0) as nbec
					ON (oal.arc_id_empr=nbec.arc_id_empr and oal.arc_id_empr!=0 and nbec.arc_id_empr!=0))
					LEFT JOIN notices AS notices_m ON arc_expl_notice = notices_m.notice_id )
					LEFT JOIN bulletins ON arc_expl_bulletin = bulletins.bulletin_id)
					LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				WHERE $restrict_date $pas_notice $pas_bulletin oal.arc_id_empr !=0
				GROUP BY oal.arc_expl_notice, oal.arc_expl_bulletin
				HAVING total_prets>=$opac_autres_lectures_nb_mini_emprunts
				ORDER BY $opac_autres_lectures_tri
				";

		$res_autres_lectures = pmb_mysql_query($rqt_autres_lectures) or die ("<br />".pmb_mysql_error()."<br />".$rqt_autres_lectures."<br />");
		if (pmb_mysql_num_rows($res_autres_lectures)) {
			$odd_even=1;
			$inotvisible=0;
			$ret="";

			//droits d'acces emprunteur/notice
			$acces_j='';
			if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
				$ac= new acces();
				$dom_2= $ac->setDomain(2);
				$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
			}

			if($acces_j) {
				$statut_j='';
				$statut_r='';
			} else {
				$statut_j=',notice_statut';
				$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			}

			while (($data=pmb_mysql_fetch_array($res_autres_lectures))) { // $inotvisible<=$opac_autres_lectures_nb_maxi
				$requete = "SELECT  1  ";
				$requete .= " FROM notices $acces_j $statut_j  WHERE notice_id='".$data['not_id']."' $statut_r ";
				$myQuery = pmb_mysql_query($requete, $dbh);
				if (pmb_mysql_num_rows($myQuery) && $inotvisible<=$opac_autres_lectures_nb_maxi) { // pmb_mysql_num_rows($myQuery)
					$inotvisible++;
					$titre = $data['tit'];
					// **********
					$responsab = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
					$responsab = get_notice_authors($data['not_id']) ;
					$as = array_search ("0", $responsab["responsabilites"]) ;
					if ($as!== FALSE && $as!== NULL) {
						$auteur_0 = $responsab["auteurs"][$as] ;
						$auteur = new auteur($auteur_0["id"]);
						$mention_resp = $auteur->get_isbd();
					} else {
						$aut1_libelle = array();
						$as = array_keys ($responsab["responsabilites"], "1" ) ;
						for ($i = 0 ; $i < count($as) ; $i++) {
							$indice = $as[$i] ;
							$auteur_1 = $responsab["auteurs"][$indice] ;
							$auteur = new auteur($auteur_1["id"]);
							$aut1_libelle[]= $auteur->get_isbd();
						}
						$mention_resp = implode (", ",$aut1_libelle) ;
					}
					$mention_resp ? $auteur = $mention_resp : $auteur="";

					// on affiche les résultats
					if ($odd_even==0) {
						$pair_impair="odd";
						$odd_even=1;
					} else if ($odd_even==1) {
						$pair_impair="even";
						$odd_even=0;
					}
					if ($data['arc_expl_notice']) $tr_javascript=" class='$pair_impair' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./index.php?lvl=notice_display&id=".$data['not_id']."&seule=1';\" style='cursor: pointer' ";
						else $tr_javascript=" class='$pair_impair' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./index.php?lvl=bulletin_display&id=".$data['arc_expl_bulletin']."';\" style='cursor: pointer' ";
					$ret .= "<tr $tr_javascript>";
					$ret .= "<td>".$titre."</td>";
					$ret .= "<td>".$auteur."</td>";
					$ret .= "</tr>\n";
				}
			}
			if ($ret) $ret = "<h3 class='autres_lectures'>".$msg['autres_lectures']."</h3><table style='width:100%;'>".$ret."</table>";
		} else $ret="";

	return $ret;
	} // fin autres_lectures ($notice_id=0,$bulletin_id=0)

	/**
	 * Ajoute l'image
	 * @param int $notice_id Identifiant de la notice
	 * @param string $entree Contenu avant l'ajout
	 * @param int $depliable
	 */
	static public function do_image($notice_id, &$entree,$depliable) {
		global $charset;
		global $msg;
		global $use_opac_url_base;

		$record_datas = static::get_record_datas($notice_id);
		$image = "";
		if ($record_datas->get_code() || $record_datas->get_thumbnail_url()) {
			if (static::get_parameter_value('show_book_pics')=='1' && (static::get_parameter_value('book_pics_url') || $record_datas->get_thumbnail_url())) {
			    $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
			    if($use_opac_url_base) {
			        $url_image_ok = $thumbnailSourcesHandler->generateSrcBase64(TYPE_NOTICE, $record_datas->get_id());
			    } else {
			        $url_image_ok = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $record_datas->get_id());
			    }
				$title_image_ok = "";
				if(!$record_datas->get_thumbnail_url()){
					$title_image_ok = htmlentities(static::get_parameter_value('book_pics_msg'), ENT_QUOTES, $charset);
				}
				if(!trim($title_image_ok)){
					$title_image_ok = htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset);
				}
				if ($depliable) {
					$image = "<img class='vignetteimg align_right' src='".static::get_parameter_value('url_base')."images/vide.png' title=\"".$title_image_ok."\" vigurl=\"".$url_image_ok."\"  alt='".htmlentities($msg["opac_notice_vignette_alt"],ENT_QUOTES,$charset)."'/>";
				} else {
					$image = "<img class='vignetteimg align_right' src='".$url_image_ok."' title=\"".$title_image_ok."\" alt='".htmlentities($msg["opac_notice_vignette_alt"],ENT_QUOTES,$charset)."' />";
				}
			}
		}
		if ($image) {
			$entree = "<table style='width:100%'><tr><td style='vertical-align:top'>$entree</td><td style='vertical-align:top' class='align_right'>$image</td></tr></table>" ;
		} else {
			$entree = "<table style='width:100%'><tr><td>$entree</td></tr></table>" ;
		}
	}
	 /**
	  * Retourne le script des notices similaires
	  * @return string
	  */
	static public function get_display_simili_script($notice_id) {
		switch (static::get_parameter_value('allow_simili_search')) {
			case "0" :
				$script_simili_search = "";
				break;
			case "1" :
				$script_simili_search = "show_simili_search('".$notice_id."');";
				$script_simili_search.= "show_expl_voisin_search('".$notice_id."');";
				break;
			case "2" :
				$script_simili_search = "show_expl_voisin_search('".$notice_id."');";
				break;
			case "3" :
				$script_simili_search = "show_simili_search('".$notice_id."');";
				break;
		}
		return $script_simili_search;
	}

	/**
	 * Retourne les notices similaires
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_simili_search($notice_id) {
		$simili_search = "";
		switch(static::get_parameter_value('allow_simili_search')){
			case "1" :
				$simili_search="
					<div id='expl_voisin_search_".$notice_id."' class='expl_voisin_search'></div>
					<div id='simili_search_".$notice_id."' class='simili_search'></div>";
				$simili_search.="
					<script type='text/javascript'>
						".static::get_display_simili_script($notice_id)."
					</script>";
				break;
			case "2" :
				$simili_search="
					<div id='expl_voisin_search_".$notice_id."' class='expl_voisin_search'></div>";
				$simili_search.="
					<script type='text/javascript'>
						".static::get_display_simili_script($notice_id)."
					</script>";
				break;
			case "3" :
				$simili_search="
					<div id='simili_search_".$notice_id."' class='simili_search'></div>";
				$simili_search.="
					<script type='text/javascript'>
						".static::get_display_simili_script($notice_id)."
					</script>";
				break;
		}
		return $simili_search;
	}

	/**
	 * Renvoie les états de collections
	 * @param int $notice_id Identifiant de la notice
	 * @return mixed
	 */
	static public function get_display_collstate($notice_id) {
		global $msg;
		global $pmb_etat_collections_localise;

		$affichage = "";
		$record_datas = static::get_record_datas($notice_id);
		$collstate = $record_datas->get_collstate();
		if($pmb_etat_collections_localise) {
			$collstate->get_display_list("",0,0,0,1);
		} else {
			$collstate->get_display_list("",0,0,0,0);
		}
		if($collstate->nbr) {
			$affichage.= "<h3><span class='titre_exemplaires'>".$msg["perio_etat_coll"]."</span></h3>";
			$affichage.=$collstate->liste;
		}
		return $affichage;
	}

	static public function get_lang_list($tableau) {
		$langues = "";
		$nb_langues = count($tableau);
		for ($i = 0 ; $i < $nb_langues; $i++) {
		    if (!empty($langues)) {
		        $langues .= " ";
		    }
			$langues .= $tableau[$i]["langue"]." (<i>".$tableau[$i]["lang_code"]."</i>)";
		}
		return $langues;
	}

	/**
	 * Fonction d'affichage des avis
	 * @param int $notice_id Identifiant de la notice
	 */
	static public function get_display_avis($notice_id) {
		$record_datas = static::get_record_datas($notice_id);
		$avis = $record_datas->get_avis();
		return $avis->get_display();
	}

	static public function get_display_avis_detail($notice_id) {
		$record_datas = static::get_record_datas($notice_id);
		$avis = $record_datas->get_avis();
		return $avis->get_display_detail();
	}

	static public function get_display_avis_only_stars($notice_id) {
		$record_datas = static::get_record_datas($notice_id);
		$avis = $record_datas->get_avis();

		return $avis->get_display_only_stars();
	}
	/**
	 * Retourne l'affichage des étoiles
	 * @param float $moyenne
	 */

	/**
	 * Fonction d'affichage des suggestions
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_suggestion($notice_id){
		global $msg;
		$do_suggest="<a href='#' title=\"".$msg['suggest_notice_opac']."\" onclick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup&id_notice=".$notice_id."','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\">".$msg['suggest_notice_opac']."</a>";
		return $do_suggest;
	}

	/**
	 * Fonction d'affichage des tags
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_tag($notice_id){
		global $msg;
		return "<a href='#' title=\"".$msg['notice_title_tag']."\" onclick=\"open('addtags.php?noticeid=".$notice_id."','ajouter_un_tag','width=350,height=150,scrollbars=yes,resizable=yes'); return false;\">".$msg['notice_title_tag']."</a>";

	}

	/**
	 * Fonction d'affichage des listes de lecture
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_liste_lecture($notice_id){
		global $msg;
		return "
			<script type='text/javascript' src='./includes/javascript/liste_lecture.js'></script>
			<script type='text/javascript'>
				msg_notice_title_liste_lecture_added = '".$msg["notice_title_liste_lecture_added"]."';
				msg_notice_title_liste_lecture_failed = '".$msg["notice_title_liste_lecture_failed"]."';
			</script>
			<a id='liste_lecture_display_tooltip_notice_".$notice_id."'>
				<span class='ExtnotCom imgListeLecture'>
					<img src='".get_url_icon('liste_lecture_w.png')."' align='absmiddle' style='border:0px' title=\"".$msg['notice_title_liste_lecture']."\" alt=\"".$msg['notice_title_liste_lecture']."\" />
				</span>
				<span class='listeLectureN'>
					".$msg['notice_title_liste_lecture']."
				</span>
			</a>
			<div data-dojo-type='dijit/Tooltip' data-dojo-props=\"connectId:'liste_lecture_display_tooltip_notice_".$notice_id."', position:['below']\">
				<div class='row'>
					".$msg['notice_title_liste_lecture']."
				</div>
				<div class='row'>
					".liste_lecture::gen_selector_my_list($notice_id)."
				</div>
			</div>";
	}

	/**
	 * Retourne l'affichage étendu d'une notice
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_extended($notice_id, $django_directory = "") {
		$record_datas = static::get_record_datas($notice_id);

		$template = static::get_template("record_extended_display", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage d'une notice dans un résultat de recherche
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_in_result($notice_id, $django_directory = "") {
		$record_datas = static::get_record_datas($notice_id);

		$template = static::get_template("record_in_result_display", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur imprimante format court
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_printer_short($notice_id, $django_directory = "", $parameters = '') {
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_for_printer_short", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur imprimante format court
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_printer_short_unimarc($notice_id, $django_directory = "", $parameters = '') {
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_unimarc_for_printer_short", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template, true, $parameters);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur imprimante format long
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_printer_extended($notice_id, $django_directory = "", $parameters = '') {
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_for_printer_extended", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur imprimante format long
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_printer_extended_unimarc($notice_id, $django_directory = "", $parameters = '') {
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_unimarc_for_printer_extended", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template, true, $parameters);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur pdf format court
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_pdf_short($notice_id, $django_directory = "", $parameters = '') {
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_for_pdf_short", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur pdf format court
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_pdf_short_unimarc($notice_id, $django_directory = "", $parameters = '') {
	    $record_datas = static::get_record_datas($notice_id);
	    $record_datas->set_external_parameters($parameters);
	    $template = static::get_template("record_unimarc_for_pdf_short", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

	    return static::render($notice_id, $template, true, $parameters);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur pdf format long
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_pdf_extended($notice_id, $django_directory = "", $parameters = '') {
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_for_pdf_extended", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage d'une notice pour impression sur pdf format long
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_pdf_extended_unimarc($notice_id, $django_directory = "", $parameters = '') {
	    $record_datas = static::get_record_datas($notice_id);
	    $record_datas->set_external_parameters($parameters);
	    $template = static::get_template("record_unimarc_for_pdf_extended", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

	    return static::render($notice_id, $template, true, $parameters);
	}

	/**
	 * Retourne l'affichage du titre de la notice sur un flux RSS
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_rss_title($notice_id, $django_directory = "", $parameters = '') {
	    $record_datas = static::get_record_datas($notice_id);
	    $record_datas->set_external_parameters($parameters);
	    $template = static::get_template("record_for_rss_title", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

	    return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage de la description d'une notice sur un flux RSS
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_rss_description($notice_id, $django_directory = "", $parameters = '') {
	    $record_datas = static::get_record_datas($notice_id);
	    $record_datas->set_external_parameters($parameters);
	    $template = static::get_template("record_for_rss_description", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

	    return static::render($notice_id, $template);
	}

	/**
	 * Retourne l'affichage du lien de la notice sur un flux RSS
	 * @param int $notice_id Identifiant de la notice
	 * @param string $django_directory Répertoire Django à utiliser
	 * @param array $parameters Permet un affichage dynamique en fonction de champs de formulaire par exemple
	 * @return string Code html d'affichage de la notice
	 */
	static public function get_display_for_rss_link($notice_id, $django_directory = "", $parameters = '') {
		$record_datas = static::get_record_datas($notice_id);
		$record_datas->set_external_parameters($parameters);
		$template = static::get_template("record_for_rss_link", $record_datas->get_niveau_biblio(), $record_datas->get_typdoc(), $django_directory);

		return static::render($notice_id, $template);
	}

    /**
     * Retourne le bon template
     * @param string $template_name Nom du template : record_extended ou record_in_result
     * @param string $niveau_biblio Niveau bibliographique
     * @param string $typdoc Type de document
     * @param string $django_directory Répertoire Django à utiliser (paramètre opac_notices_format_django_directory par défaut)
     * @param bool $opac_template Permet d'utilise les template OPAC
     * @return string Nom du template à appeler
     */
    static public function get_template($template_name, $niveau_biblio, $typdoc, $django_directory = "", $connector_id = "", $source_id = "", $opac_template = false, $dsi_template = false) {
        global $include_path, $base_path;

        $template_path = "{$include_path}/templates";
        if ($opac_template) {
            $template_path = "$base_path/opac_css/includes/templates";
        }

        if($dsi_template) {
            $template_path .= "/dsi";
        }

        if (!$django_directory) {
            $django_directory = static::get_parameter_value('notices_format_django_directory');
        }

        if (file_exists($template_path."/record/".$django_directory."/".$template_name."_".$source_id."_".$niveau_biblio.$typdoc.".tpl.html")) {
            return $template_path."/record/".$django_directory."/".$template_name."_".$source_id."_".$niveau_biblio.$typdoc.".tpl.html";
        }
        if (file_exists($template_path."/record/".$django_directory."/".$template_name."_".$connector_id."_".$niveau_biblio.$typdoc.".tpl.html")) {
            return $template_path."/record/".$django_directory."/".$template_name."_".$connector_id."_".$niveau_biblio.$typdoc.".tpl.html";
        }
        if (file_exists($template_path."/record/".$django_directory."/".$template_name."_".$source_id."_".$niveau_biblio.".tpl.html")) {
            return $template_path."/record/".$django_directory."/".$template_name."_".$source_id."_".$niveau_biblio.".tpl.html";
        }
        if (file_exists($template_path."/record/".$django_directory."/".$template_name."_".$connector_id."_".$niveau_biblio.".tpl.html")) {
            return $template_path."/record/".$django_directory."/".$template_name."_".$connector_id."_".$niveau_biblio.".tpl.html";
        }
        if (file_exists($template_path."/record/".$django_directory."/".$template_name."_".$source_id.".tpl.html")) {
            return $template_path."/record/".$django_directory."/".$template_name."_".$source_id.".tpl.html";
        }
        if (file_exists($template_path."/record/".$django_directory."/".$template_name."_".$connector_id.".tpl.html")) {
            return $template_path."/record/".$django_directory."/".$template_name."_".$connector_id.".tpl.html";
        }
        if (file_exists($template_path."/record/".$django_directory."/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html")) {
            return $template_path."/record/".$django_directory."/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html";
        }
        if (file_exists($template_path."/record/".$django_directory."/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html")) {
            return $template_path."/record/".$django_directory."/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html";
        }
        if (file_exists($template_path."/record/common/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html")) {
            return $template_path."/record/common/".$template_name."_".$niveau_biblio.$typdoc.".tpl.html";
        }
        if (file_exists($template_path."/record/".$django_directory."/".$template_name."_".$niveau_biblio.".tpl.html")) {
            return $template_path."/record/".$django_directory."/".$template_name."_".$niveau_biblio.".tpl.html";
        }
        if (file_exists($template_path."/record/common/".$template_name."_".$niveau_biblio.".tpl.html")) {
            return $template_path."/record/common/".$template_name."_".$niveau_biblio.".tpl.html";
        }
        if (file_exists($template_path."/record/".$django_directory."/".$template_name.".tpl.html")) {
            return $template_path."/record/".$django_directory."/".$template_name.".tpl.html";
        }
        return $template_path."/record/common/".$template_name.".tpl.html";
    }

	static public function get_liens_opac() {
		global $liens_opac;

		return $liens_opac;
	}

	static public function get_linked_permalink() {
		global $base_path;
		global $use_opac_url_base, $opac_url_base;

		$use_opac_url_base = intval($use_opac_url_base);
		if(!isset(static::$linked_permalink[$use_opac_url_base])) {
			if($use_opac_url_base) {
				static::$linked_permalink[$use_opac_url_base] = array(
						'author' => $opac_url_base."index.php?lvl=author_see&id=!!id!!",
						'category' => $opac_url_base."index.php?lvl=categ_see&id=!!id!!",
						'publisher' => $opac_url_base."index.php?lvl=publisher_see&id=!!id!!",
						'collection' => $opac_url_base."index.php?lvl=coll_see&id=!!id!!",
						'subcollection' => $opac_url_base."index.php?lvl=subcoll_see&id=!!id!!",
						'serie' => $opac_url_base."index.php?lvl=serie_see&id=!!id!!",
						'titre_uniforme' => $opac_url_base."index.php?lvl=titre_uniforme_see&id=!!id!!",
						'indexint' => $opac_url_base."index.php?lvl=indexint_see&id=!!id!!",
						'authperso' => $opac_url_base."index.php?lvl=authperso_see&id=!!id!!",
						"concept" => $opac_url_base."index.php?lvl=concept_see&id=!!id!!"
				);
			} else {
				static::$linked_permalink[$use_opac_url_base] = array(
						'author' => $base_path."/autorites.php?categ=see&sub=author&id=!!id!!",
						'category' => $base_path."/autorites.php?categ=see&sub=category&id=!!id!!",
						'publisher' => $base_path."/autorites.php?categ=see&sub=publisher&id=!!id!!",
						'collection' => $base_path."/autorites.php?categ=see&sub=collection&id=!!id!!",
						'subcollection' => $base_path."/autorites.php?categ=see&sub=subcollection&id=!!id!!",
						'serie' => $base_path."/autorites.php?categ=see&sub=serie&id=!!id!!",
						'titre_uniforme' => $base_path."/autorites.php?categ=see&sub=titre_uniforme&id=!!id!!",
						'indexint' => $base_path."/autorites.php?categ=see&sub=indexint&id=!!id!!",
						'authperso' => $base_path."/autorites.php?categ=see&sub=authperso&id=!!id!!",
						'concept' => $base_path."/autorites.php?categ=see&sub=concept&id=!!id!!"
				);
			}
		}
		return static::$linked_permalink[$use_opac_url_base];
	}

	/**
	 * Retourne l'affichage des documents numériques
	 * @param int $notice_id Identifiant de la notice
	 * @return string Rendu html des documents numériques
	 */
	static public function get_display_explnums($notice_id) {
		global $include_path;
		global $msg;
		global $nb_explnum_visible;

		require_once($include_path."/explnum.inc.php");

		$record_datas = static::get_record_datas($notice_id);
		$bull = $record_datas->get_bul_info();
		$bulletin_id = (isset($bull['bulletin_id']) ? $bull['bulletin_id'] : '');

		if ($record_datas->get_niveau_biblio() == "b" && ($explnums = show_explnum_per_notice(0, $bulletin_id, ''))) {
			return "<a name='docnum'><h3><span id='titre_explnum'>".$msg['explnum']." (".$nb_explnum_visible.")</span></h3></a>".$explnums;
		}
		if ($explnums = show_explnum_per_notice($notice_id, 0, '')) {
			return "<a name='docnum'><h3><span id='titre_explnum'>".$msg['explnum']." (".$nb_explnum_visible.")</span></h3></a>".$explnums;
		}
		return "";
	}

	static public function get_display_size($notice_id) {
		$record_datas = static::get_record_datas($notice_id);

		$size = array();
		if ($record_datas->get_npages()) $size[] = $record_datas->get_npages();
		if ($record_datas->get_ill()) $size[] = $record_datas->get_ill();
		if ($record_datas->get_size()) $size[] = $record_datas->get_size();

		return implode(" / ", $size);
	}

	static public function get_display_demand($notice_id) {
		global $msg, $charset, $form_modif_demande, $form_linked_record, $demandes_active, $opac_demandes_allow_from_record;

		if ($demandes_active && $opac_demandes_allow_from_record && $_SESSION['id_empr_session']) {
			$record_datas = static::get_record_datas($notice_id);
			$demande = new demandes();
			$themes = new demandes_themes('demandes_theme','id_theme','libelle_theme',$demande->theme_demande);
			$types = new demandes_types('demandes_type','id_type','libelle_type',$demande->type_demande);

			$f_modif_demande = $form_modif_demande;
			$f_modif_demande = str_replace('!!form_title!!',htmlentities($msg['demandes_creation'],ENT_QUOTES,$charset),$f_modif_demande);
			$f_modif_demande = str_replace('!!sujet!!','',$f_modif_demande);
			$f_modif_demande = str_replace('!!progression!!','',$f_modif_demande);
			$f_modif_demande = str_replace('!!empr_txt!!','',$f_modif_demande);
			$f_modif_demande = str_replace('!!idempr!!',$_SESSION['id_empr_session'],$f_modif_demande);
			$f_modif_demande = str_replace('!!iduser!!',"",$f_modif_demande);
			$f_modif_demande = str_replace('!!titre!!','',$f_modif_demande);

			$etat=$demande->getStateValue();
			$f_modif_demande = str_replace('!!idetat!!',$etat['id'],$f_modif_demande);
			$f_modif_demande = str_replace('!!value_etat!!',$etat['comment'],$f_modif_demande);
			$f_modif_demande = str_replace('!!select_theme!!',$themes->getListSelector(),$f_modif_demande);
			$f_modif_demande = str_replace('!!select_type!!',$types->getListSelector(),$f_modif_demande);

			$date = formatdate(today());
			$date_debut=date("Y-m-d",time());
			$date_dmde = "<input type='button' class='bouton' id='date_debut_btn' name='date_debut_btn' value='!!date_debut_btn!!'
					onClick=\"openPopUp('./select.php?what=calendrier&caller=modif_dmde&date_caller=!!date_debut!!&param1=date_debut&param2=date_debut_btn&auto_submit=NO&date_anterieure=YES', 'date_debut', 250, 300, -2, -2, 'toolbar=no, dependent=yes, resizable=yes')\"/>";
			$f_modif_demande = str_replace('!!date_demande!!',$date_dmde,$f_modif_demande);

			$f_modif_demande = str_replace('!!date_fin_btn!!',$date,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_debut_btn!!',$date,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_debut!!',$date_debut,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_fin!!',$date_debut,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_prevue!!',$date_debut,$f_modif_demande);
			$f_modif_demande = str_replace('!!date_prevue_btn!!',$date,$f_modif_demande);

			$f_modif_demande = str_replace('!!iddemande!!', '', $f_modif_demande);

			$f_modif_demande = str_replace('!!form_linked_record!!', $form_linked_record, $f_modif_demande);
			$f_modif_demande = str_replace('!!linked_record!!', $record_datas->get_tit1(), $f_modif_demande);
			$f_modif_demande = str_replace("!!linked_record_id!!", $notice_id, $f_modif_demande);
			$f_modif_demande = str_replace("!!linked_record_link!!", $record_datas->get_permalink(), $f_modif_demande);

			$act_cancel = "demandDialog_".$notice_id.".hide();";
			$act_form = "./empr.php?tab=request&lvl=list_dmde&sub=save_demande";

			$f_modif_demande = str_replace('!!form_action!!',$act_form,$f_modif_demande);
			$f_modif_demande = str_replace('!!cancel_action!!',$act_cancel,$f_modif_demande);

			// Requires et début de formulaire
			$html = "
					<script type='text/javascript'>
						require(['dojo/parser', 'apps/pmb/PMBDialog']);
						document.body.setAttribute('class', 'tundra');
					</script>
					<div data-dojo-type='apps/pmb/PMBDialog' data-dojo-id='demandDialog_".$notice_id."' title='".$msg['do_demande_on_record']."' style='display:none;width:75%;'>
						".$f_modif_demande."
					</div>
					<a href='#' onClick='demandDialog_".$notice_id.".show();return false;'>
						".$msg['do_demande_on_record']."
					</a>";

			return $html;
		}
		return "";
	}

	/**
	 * Retourne le rendu html des documents numériques du bulletin parent de la notice d'article
	 * @param int $notice_id Identifiant de la notice
	 * @return string Rendu html des documents numériques du bulletin parent
	 */
	static public function get_display_bull_for_art_expl_num($notice_id) {

		$record_datas = static::get_record_datas($notice_id);
		$bul_infos = $record_datas->get_bul_info();
		$paramaff=array();
		$paramaff["mine_type"]=1;
		$retour = show_explnum_per_notice(0, $bul_infos['bulletin_id'],"",$paramaff);

		return $retour;
	}

	static public function get_special($notice_id) {
		global $include_path;
		$classpath = $include_path."/templates/record/".static::get_parameter_value('notices_format_django_directory')."/special/".static::get_parameter_value('notices_format_django_directory')."_special.class.php";
		$class = "";
		if(file_exists($classpath)){
			require_once $classpath;
			$class = static::get_parameter_value('notices_format_django_directory')."_special";
		}
		if(!class_exists($class)){
			return null;
		}
		if (!isset(self::$special[$notice_id])) {
			self::$special[$notice_id] = new $class(self::$records_datas[$notice_id]);
		}
		return self::$special[$notice_id];
	}

	static public function get_display_scan_request($notice_id) {
		$html = "";
		$record_datas = static::get_record_datas($notice_id);
		if(is_null($record_datas->get_dom_1()) && $record_datas->is_visu_scan_request() && (!$record_datas->is_visu_scan_request_abon() || ($record_datas->is_visu_scan_request_abon() && $_SESSION["user_code"])) || ($record_datas->get_rights() & 32)) {
			$scan_request = new scan_request();
			if($record_datas->get_niveau_biblio() == 'b') {
				$bul_infos = $record_datas->get_bul_info();
// 				$scan_request->add_linked_records(array('bulletins' => array($bul_infos['bulletin_id'])));
				$html = $scan_request->get_link_in_record($bul_infos['bulletin_id'], 'bulletins');
			} else {
// 				$scan_request->add_linked_records(array('notices' => array($notice_id)));
				$html = $scan_request->get_link_in_record($notice_id);
			}
		}
		return $html;
	}

	/**
	 * Fonction d'affichage des réseaux sociax
	 * @param int $notice_id Identifiant de la notice
	 * @return string
	 */
	static public function get_display_social_network($notice_id){
		$opac_view = "";
		if(isset($_SESSION["opac_view"])) {
			$opac_view = $_SESSION["opac_view"];
		}
		return "
			<div id='el".$notice_id."addthis' class='addthis_toolbox addthis_default_style '
				addthis:url='".static::get_parameter_value('url_base')."fb.php?&opac_view=" . $opac_view . "id=" . intval($notice_id) . "&type=" . intval(TYPE_NOTICE) . "'>
            </div>
			<script type='text/javascript'>
				if(param_social_network){
					creeAddthis('el".$notice_id."');
				}else{
					waitingAddthisLoaded('el".$notice_id."');
				}
			</script>";

	}

	static public function get_display_serialcirc_form_actions($notice_id) {
		global $msg, $charset;
		global $allow_serialcirc;

		$html = "";
		$record_datas = static::get_record_datas($notice_id);
		if($_SESSION['id_empr_session'] && static::get_parameter_value('serialcirc_active') && $record_datas->get_opac_serialcirc_demande() && $allow_serialcirc) {
			if($record_datas->get_niveau_biblio() == "s"){
				// pour un pério, on affiche un bouton pour demander l'inscription à un liste de diffusion
				//TODO si le statut le permet...
				$html .= "
					<div class='row'>&nbsp;</div>
					<div class='row'>&nbsp;</div>
					<div class='row'>
						<form method='post' action='empr.php?tab=serialcirc&lvl=ask&action=subscribe'>
							<input type='hidden' name='serial_id' value='".htmlentities($notice_id,ENT_QUOTES,$charset)."'/>
							<input type='submit' class='bouton' value='".htmlentities($msg['serialcirc_ask_subscribtion'],ENT_QUOTES,$charset)."'/>
						</form>
					</div>";
			}else if ($record_datas->get_niveau_biblio() == "b"){
				// pour un bulletin, on regarde s'il est pas en cours d'inscription...
				// récup la circulation si existante...
				$query = "select id_serialcirc from serialcirc join abts_abts on abt_id = num_serialcirc_abt join bulletins on bulletin_notice = abts_abts.num_notice where bulletins.num_notice = ".$notice_id;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$id_serialcirc = pmb_mysql_result($result,0,0);
					$serialcirc = new serialcirc($id_serialcirc);
					if($serialcirc->is_virtual()){
						if($serialcirc->empr_is_subscribe($_SESSION['id_empr_session'])){
							$query ="select num_serialcirc_expl_id from serialcirc_expl where num_serialcirc_expl_serialcirc = ".$id_serialcirc." and serialcirc_expl_start_date = 0";
							$result = pmb_mysql_query($query);
							if(pmb_mysql_num_rows($result)){
								$expl_id = pmb_mysql_result($result,0,0);
								$serialcirc_empr_circ = new serialcirc_empr_circ($_SESSION['id_empr_session'],$id_serialcirc,$expl_id);
								$html.= $serialcirc_empr_circ->get_actions_form();
							}
						}
					}
				}
			}
		}
		return $html;
	}

	static public function get_display_bulletins_list($notice_id, $bulletins_id = array()) {
		global $page, $premier, $f_bull_deb_id, $nb_per_page_custom;
		global $bull_date_start, $bull_date_end;
		global $msg;
		global $opac_show_links_invisible_docnums;
		global $gestion_acces_active, $gestion_acces_empr_notice, $gestion_acces_empr_docnum;
		global $opac_bull_results_per_page;
		global $opac_fonction_affichage_liste_bull;

		$notice_id = intval($notice_id);

		if ($notice_id) {
			$record_datas = static::get_record_datas($notice_id);
			if ($record_datas->get_niveau_biblio() != 's') {
				return null;
			}
		}

		global ${"bull_num_deb_".$notice_id};
		$html = '';

		//Recherche dans les numéros
		$start_num = ${"bull_num_deb_".$notice_id};
		$restrict_date = "";
		if($f_bull_deb_id){
			$restrict_num = " and date_date >='".$f_bull_deb_id."' ";
		} else if($start_num){
			$restrict_num = " and bulletin_numero like '%".$start_num."%' ";
		} else {
			$restrict_num = "";
		}

		// Recherche dans les dates et libellés de période
		if(!$restrict_num) {
			if($bull_date_start && $bull_date_end){
				if($bull_date_end < $bull_date_start){
					$restrict_date = " and date_date between '".$bull_date_end."' and '".$bull_date_start."' ";
				} else if($bull_date_end == $bull_date_start) {
					$restrict_date = " and date_date='".$bull_date_start."' ";
				} else {
					$restrict_date = " and date_date between '".$bull_date_start."' and '".$bull_date_end."' ";
				}
			} else if($bull_date_start){
				$restrict_date = " and date_date >='".$bull_date_start."' ";
			} else if($bull_date_end){
				$restrict_date = " and date_date <='".$bull_date_end."' ";
			}
		}

		// nombre de références par pages (12 par défaut)
		if (!isset($opac_bull_results_per_page)) $opac_bull_results_per_page = 12;
		if(!$page) $page = 1;
		$debut = ($page-1)*$opac_bull_results_per_page;
		$limiter = " LIMIT ".$debut.",".$opac_bull_results_per_page;

		//Recherche par numéro
		$num_field_start = "
		<input type='hidden' name='f_bull_deb_id' id='f_bull_deb_id' />
		<input id='bull_num_deb_".$notice_id."' name='bull_num_deb_".$notice_id."' type='text' size='10' completion='bull_num' autfield='f_bull_deb_id' value='".$start_num."'>";

		//Recherche par date
		$deb_value = str_replace("-","",$bull_date_start);
		$fin_value = str_replace("-","",$bull_date_end);
		$date_deb_value = ($deb_value ? formatdate($deb_value) : '...');
		$date_fin_value = ($fin_value ? formatdate($fin_value) : '...');
		$date_debut = "<input type='date' style='width: 10em;' name='bull_date_start' id='bull_date_start' value='".$bull_date_start."' />";
		$date_fin = "<input type='date' style='width: 10em;' name='bull_date_end' id='bull_date_end' value='".$bull_date_end."' />";

		$tableau = "
		<a name='tab_bulletin'></a>
		<h3>".$msg['perio_list_bulletins']."</h3>
		<div id='form_search_bull'>
			<div class='row'></div>\n
			<script src='./includes/javascript/ajax.js'></script>
			<form name=\"form_values\" action='#tab_bulletin' method=\"post\" onsubmit=\"if (document.getElementById('onglet_isbd".$notice_id."').className=='isbd_public_active') document.form_values.premier.value='ISBD'; else document.form_values.premier.value='PUBLIC';document.form_values.page.value=1;\">\n
				<input type=\"hidden\" name=\"premier\" value=\"\">\n
				<input type=\"hidden\" name=\"page\" value=\"".$page."\">\n
				<input type=\"hidden\" name=\"nb_per_page_custom\" value=\"".$nb_per_page_custom."\">\n
				<table>
					<tr>
						<td class='align_left' rowspan=2><strong>".$msg["search_bull"]."&nbsp;:&nbsp;</strong></td>
						<td class='align_right'><strong>".$msg["search_per_bull_num"]." : ".$msg["search_bull_exact"]."</strong></td>
						<td >".$num_field_start."</td>
						<td >&nbsp;</td>
					</tr>
					<tr>
						<td class='align_right'><strong>".$msg["search_per_bull_date"]." : ".$msg["search_bull_start"]."</strong></td>
						<td>".$date_debut."</td>
						<td><strong>".$msg["search_bull_end"]."</strong> ".$date_fin."</td>
						<td>&nbsp;&nbsp;<input type='button' class='boutonrechercher' value='".$msg["142"]."' onclick='submit();'></td>
					</tr>
				</table>
			</form>
			<div class='row'></div><br />
		</div>\n";
		$html.= $tableau;

		//quel affichage de notice il faut utiliser (Public, ISBD) (valeur postée)
		if ($premier) {
			$html.= "<script> show_what('".$premier."','".$notice_id."'); </script>";
		}

		$html.= "<script type='text/javascript'>ajax_parse_dom();</script>";

		$join_docnum_noti = $join_docnum_bull = "";
		if (($gestion_acces_active == 1) && ($gestion_acces_empr_notice == 1)) {
			$ac = new acces();
			$dom_2= $ac->setDomain(2);
			$join_noti = $dom_2->getJoin($_SESSION["id_empr_session"],4,"bulletins.num_notice");
			$join_bull = $dom_2->getJoin($_SESSION["id_empr_session"],4,"bulletins.bulletin_notice");
			if(!$opac_show_links_invisible_docnums){
				$join_docnum_noti = $dom_2->getJoin($_SESSION["id_empr_session"],16,"bulletins.num_notice");
				$join_docnum_bull = $dom_2->getJoin($_SESSION["id_empr_session"],16,"bulletins.bulletin_notice");
			}
		}else{
			$join_noti = "join notices on bulletins.num_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			$join_bull = "join notices on bulletins.bulletin_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
			if(!$opac_show_links_invisible_docnums){
				$join_docnum_noti = "join notices on bulletins.num_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
				$join_docnum_bull = "join notices on bulletins.bulletin_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
			}
		}
		$join_docnum_explnum = "";
		if(!$opac_show_links_invisible_docnums) {
			if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
				$ac = new acces();
				$dom_3= $ac->setDomain(3);
				$join_docnum_explnum = $dom_3->getJoin($_SESSION["id_empr_session"],16,"explnum_id");
			}else{
				$join_docnum_explnum = "join explnum_statut on explnum_docnum_statut=id_explnum_statut and ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
			}
		}

		$restriction = " 1";
		if (count($bulletins_id)) {
			$restriction = " bulletins.bulletin_id in (".implode(",", $bulletins_id).")";
		} else if ($notice_id) {
			$restriction = " bulletin_notice = ".$notice_id;
		}

		$requete_docnum_noti = "select bulletin_id, count(explnum_id) as nbexplnum from explnum join bulletins on explnum_bulletin = bulletin_id and explnum_notice = 0 ".$join_docnum_explnum." where ".$restriction." and explnum_bulletin in (select bulletin_id from bulletins ".$join_docnum_noti." where ".$restriction.") group by bulletin_id";
		$requete_docnum_bull = "select bulletin_id, count(explnum_id) as nbexplnum from explnum join bulletins on explnum_bulletin = bulletin_id and explnum_notice = 0 ".$join_docnum_explnum." where ".$restriction." and explnum_bulletin in (select bulletin_id from bulletins ".$join_docnum_bull." where ".$restriction.") group by bulletin_id";
		$requete_noti = "select bulletins.*,ifnull(nbexplnum,0) as nbexplnum from bulletins ".$join_noti." left join (".$requete_docnum_noti.") as docnum_noti on bulletins.bulletin_id = docnum_noti.bulletin_id where bulletins.num_notice != 0 and ".$restriction." ".$restrict_num." ".$restrict_date." GROUP BY bulletins.bulletin_id";
		$requete_bull = "select bulletins.*,ifnull(nbexplnum,0) as nbexplnum from bulletins ".$join_bull." left join ($requete_docnum_bull) as docnum_bull on bulletins.bulletin_id = docnum_bull.bulletin_id where bulletins.num_notice = 0 and ".$restriction." ".$restrict_num." ".$restrict_date." GROUP BY bulletins.bulletin_id";

		$requete = "select * from (".$requete_noti." union ".$requete_bull.") as uni where 1 ".$restrict_num." ".$restrict_date;
		$rescount1 = pmb_mysql_query($requete);
		$count1 = pmb_mysql_num_rows($rescount1);

		//si on recherche par date ou par numéro, le résultat sera trié par ordre croissant
		if ($restrict_num || $restrict_date) {
			$requete.=" ORDER BY date_date, bulletin_numero*1 ";
		} else {
			$requete.= " ORDER BY date_date DESC, bulletin_numero*1 DESC";
		}
		$requete.= $limiter;
		$res = pmb_mysql_query($requete);
		$count = pmb_mysql_num_rows($res);

		if ($count) {
			if ($opac_fonction_affichage_liste_bull) {
				$html.= call_user_func_array($opac_fonction_affichage_liste_bull, array($res, false));
			} else {
				affichage_liste_bulletins_normale($res);
			}
		} else {
			$html.= "<br /><strong>".$msg["bull_no_found"]."</strong>";
		}
		$html.= "<br /><br /><div class='row'></div>";
		// constitution des liens
		if (!$count1) $count1 = $count;
		$url_page = "javascript:if (document.getElementById(\"onglet_isbd".$notice_id."\")) if (document.getElementById(\"onglet_isbd".$notice_id."\").className==\"isbd_public_active\") document.form_values.premier.value=\"ISBD\"; else document.form_values.premier.value=\"PUBLIC\"; document.form_values.page.value=!!page!!; document.form_values.submit()";
		$nb_per_page_custom_url = "javascript:document.form_values.nb_per_page_custom.value=!!nb_per_page_custom!!";
		$action = "javascript:if (document.getElementById(\"onglet_isbd".$notice_id."\")) if (document.getElementById(\"onglet_isbd".$notice_id."\").className==\"isbd_public_active\") document.form_values.premier.value=\"ISBD\"; else document.form_values.premier.value=\"PUBLIC\"; document.form_values.page.value=document.form.page.value; document.form_values.submit()";
		if ($count) $form = "<div class='row'></div><div id='navbar'><br />\n<div style='text-align:center'>".printnavbar($page, $count1, $opac_bull_results_per_page, $url_page, $nb_per_page_custom_url, $action)."</div></div>";
		$html.= pmb_bidi($form);
		return $html;
	}

	public static function get_display_isbd_with_link($notice_id = 0, $bulletin_id = 0) {
		global $opac_url_base;

		if ($notice_id) {
			$display = aff_notice($notice_id, 0, 1, 0, AFF_ETA_NOTICES_REDUIT, '', 1, 0);
		} else {
			$display = "<a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$bulletin_id."'>".bulletin_header($bulletin_id)."</a><br />";
		}
		return $display;
	}

	public static function get_record_rights($notice_id = 0, $bulletin_id = 0) {
		global $gestion_acces_active,$gestion_acces_empr_notice;

		$rights = array(
				'visible' => false
		);
		$id_for_right = 0;
		if($bulletin_id) {
			$query = "select num_notice,bulletin_notice from bulletins where bulletin_id = '".$bulletin_id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$infos = pmb_mysql_fetch_object($result);
				if($infos->num_notice){
					//notice de bulletin
					$id_for_right = $infos->num_notice;
				}else{
					//notice de pério
					$id_for_right = $infos->bulletin_notice;
				}
			}
		} else {
			$id_for_right = $notice_id;
		}
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$ac = new acces();
			$dom_2= $ac->setDomain(2);
			if($dom_2->getRights($_SESSION['id_empr_session'],$id_for_right, 4)) {
				$rights['visible'] = true;
			}
		} else {
			$query = "SELECT notice_visible_opac, notice_visible_opac_abon FROM notice_statut JOIN notices ON notices.statut = notice_statut.id_notice_statut WHERE notice_id='".$id_for_right."' ";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				if($row->notice_visible_opac && (!$row->notice_visible_opac_abon || ($row->notice_visible_opac_abon && $_SESSION['id_empr_session']))) {
					$rights['visible'] = true;
				}
			}
		}
		return $rights;
	}

	static public function get_display_links_for_serials($notice_id) {
		global $msg;
		global $opac_photo_filtre_mimetype;

		$record_datas = static::get_record_datas($notice_id);

		$links_for_serials = '';

		if ($record_datas->get_niveau_biblio() == "s") {

			$voir_bulletins = '';
			$voir_docnum_bulletins = '';
			$search_in_serial = '';

			if ($record_datas->get_bulletins()) {
				$voir_bulletins="<a href='#tab_bulletin'><i>".$msg["see_bull"]."</i></a>";
			}
			if (static::get_parameter_value('visionneuse_allow') && $record_datas->get_opac_visible_bulletinage()) {
			    $nb_bulletins_docnums = 0;
			    $allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
			    $bulletins_docnums = $record_datas->get_bulletins_docnums();
			    foreach($bulletins_docnums as $bulletin_docnum) {
			        if ($allowed_mimetype && in_array($bulletin_docnum->explnum_mimetype,$allowed_mimetype)){
			            $nb_bulletins_docnums++;
			        }
			    }
			    if ($nb_bulletins_docnums) {
					$voir_docnum_bulletins="
					<a href='#' onclick=\"open_visionneuse(sendToVisionneusePerio".$notice_id.");return false;\">".$msg["see_docnum_bull"]."</a>
					<script type='text/javascript'>
						function sendToVisionneusePerio".$notice_id."(){
							document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$notice_id."&bull_only=1';
						}
					</script>";
				}
			}
			if ($record_datas->is_open_to_search()) {
				$search_in_serial ="<a href='index.php?lvl=index&search_type_asked=extended_search&search_in_perio=".$notice_id."'><i>".$msg["rechercher_in_serial"]."</i></a>";
			}

			if ($voir_bulletins || $voir_docnum_bulletins || $search_in_serial) {
				$links_for_serials = "<div class='links_for_serials'>
						<ul>";
				if ($voir_bulletins) {
					$links_for_serials .= "<li class='see_bull'>".$voir_bulletins."</li>";
				}
				if ($voir_docnum_bulletins) {
					$links_for_serials .= "<li class='see_docsnums'>".$voir_docnum_bulletins."</li>";
				}
				if ($search_in_serial) {
					$links_for_serials .= "<li class='rechercher_in_serial'>".$search_in_serial."</li>";
				}
				$links_for_serials.="
						</ul>
					</div>";
			}
		}

		return $links_for_serials;
	}

	static public function get_display_pnb_loan_button($notice_id) {
		global $msg;
		global $charset;
		return '<div id="zone_exemplaires">
					<h3>'.htmlentities($msg['pnb_digital_expl'], ENT_QUOTES, $charset).'</h3>
					<a id="pnb_loan_book_' . $notice_id . '" href="#" >' . htmlentities($msg['empr_bt_checkout'], ENT_QUOTES, $charset).'</a>
					<script type="text/javascript">
						require(["dojo/dom", "dojo/on", "dojo/_base/lang", "dojox/widget/DialogSimple", "dijit/registry"], function(dom, on, lang, Dialog, registry){
							on(dom.byId("pnb_loan_book_' . $notice_id . '"), "click", lang.hitch(this, function(){
								var dialog = new Dialog({
							        title: "'.htmlentities($msg['empr_bt_checkout'], ENT_QUOTES, $charset).'",
							        href: "./ajax.php?module=ajax&categ=pnb&action=get_loan_form&notice_id='.$notice_id.'",
							        id: "loan_popup",
            						executeScripts: true,
							    });
								dialog.show();
							    var oldHide = dialog.hide;
							    dialog.hide = lang.hitch(dialog, function(){
							    	oldHide();
							       	dialog.destroyRecursive();
					        		var standby = registry.byId("standby_loan");
									if(standby){
					        			standby.destroy();
					        		}
							    });
							}));
						});
					</script>

				</div>';
	}

	public static function get_locations_list($notice_id, $property) {
	    $locations = [];

	    //Localisations des exemplaires
	    $query = "SELECT distinct location_libelle FROM exemplaires JOIN docs_location ON docs_location.idlocation = exemplaires.expl_location WHERE expl_notice = '".$notice_id."'";
	    $query .= " AND docs_location.location_visible_opac=1";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
		    while ($row = pmb_mysql_fetch_object($result)) {
		        $locations[] = array(
		            'label' => $row->location_libelle
		        );
		    }
		    pmb_mysql_free_result($result);
	    }
	    //Localisations des documents numériques
	    $query = "SELECT distinct location_libelle FROM explnum JOIN explnum_location ON explnum_location.num_explnum = explnum.explnum_id JOIN docs_location ON docs_location.idlocation = explnum_location.num_location WHERE explnum_notice = '".$notice_id."'";
	    $query .= " AND docs_location.location_visible_opac=1";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
		    while ($row = pmb_mysql_fetch_object($result)) {
		        $locations[] = array(
		            'label' => $row->location_libelle
		        );
		    }
		    pmb_mysql_free_result($result);
	    }

	    $return_data = [];
	    foreach ($locations as $infos) {
	        if (!empty($infos[$property])) {
	            $return_data[] = $infos[$property];
	        }
	    }
	    $return_data = array_unique($return_data);
	    return $return_data;
	}

	public static function get_lenders_list($notice_id, $property) {
	    $lenders = array();

	    //Localisations des exemplaires
	    $query = "SELECT distinct lender_libelle FROM exemplaires JOIN lenders ON lenders.idlender = exemplaires.expl_owner WHERE expl_notice = '".$notice_id."'";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        while ($row = pmb_mysql_fetch_object($result)) {
	            $lenders[] = array(
	                'label' => $row->lender_libelle
	            );
	        }
	        pmb_mysql_free_result($result);
	    }

	    //Localisations des documents numériques
        $query = "SELECT distinct lender_libelle FROM explnum JOIN explnum_lenders ON explnum_lenders.explnum_lender_num_explnum = explnum.explnum_id JOIN lenders ON lenders.idlender = explnum_lenders.explnum_lender_num_lender WHERE explnum_notice = '".$notice_id."'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
		    while ($row = pmb_mysql_fetch_object($result)) {
		        $lenders[] = array(
		            'label' => $row->lender_libelle
		        );
		    }
		    pmb_mysql_free_result($result);
        }

	    $return_data = [];
	    foreach ($lenders as $infos) {
	        if (!empty($infos[$property])) {
	            $return_data[] = $infos[$property];
	        }
	    }
	    $return_data = array_unique($return_data);
	    return $return_data;
	}

	static public function get_display_caddies_list($notice_id) {
		return caddie_controller::get_display_list_from_item('display', 'NOTI', $notice_id);
	}

	static public function get_directories_options($selected = '') {
	    if (!$selected) {
	        $selected = static::get_parameter_value('notices_format_django_directory');
	    }
	    if (!$selected) {
	        $selected = 'common';
	    }
	    $dirs = array_filter(glob('./includes/templates/record/*'), 'is_dir');
	    $tpl = "";
	    foreach($dirs as $dir){
	        if(basename($dir) != "CVS"){
	            $tpl.= "<option ".(basename($dir) == basename($selected) ? "selected='selected'" : "")." value='".basename($dir)."'>
				".basename($dir)."</option>";
	        }
	    }
	    return $tpl;
	}

	static public function get_directories() {
	    $directories = array();
// 	    $dirs = array_filter(glob('./includes/templates/record/*'), 'is_dir');
	    $dirs = array_filter(glob('./opac_css/includes/templates/record/*'), 'is_dir');
	    foreach($dirs as $dir){
	        if(basename($dir) != "CVS"){
	            $directories[basename($dir)] = "[Django] ".basename($dir);
	        }
	    }
	    return $directories;
	}

	static protected function get_parameter_value($name) {
		$parameter_name = 'pmb_'.$name;
		global ${$parameter_name};
		return ${$parameter_name};
	}

	/**
	 * Retourne le permalink
	 * @return string
	 */
	public function get_permalink() {
	    global $pmb_ark_activate;
	    global $pmb_opac_url;

	    if ($pmb_ark_activate && !empty($this->get_ark_link())) {
	        return $this->get_ark_link();
	    }
	    return $pmb_opac_url."index.php?lvl=notice_display&id=".$this->notice_id;
	}

	public function get_ark_link() {
	    if (empty($this->ark_link)) {
	        global $pmb_ark_activate;
	        if ($pmb_ark_activate) {
                $arkEntity = new ArkRecord(intval($this->notice_id));
	            $ark = ArkModel::getArkFromEntity($arkEntity);
	            $this->ark_link = $ark->getArkLink();
	        }
	    }
	    return $this->ark_link;
	}
	
	// fonction retournant les infos d'exemplaires numeriques en relation avec une notice donne
	public function show_explnum_in_relation($param_aff = array()) {
	    global $charset;
	    global $base_path, $msg;
	    global $_mimetypes_bymimetype_, $_mimetypes_byext_;
	    global $prefix_url_image;
	    
	    if (!$this->notice->notice_id) {
	        return "";
	    }
	        
        create_tableau_mimetype();
	        
        // recuperation du nombre d'exemplaires
        $requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut
		FROM explnum
		JOIN index_concept on index_concept.num_object = explnum_id AND index_concept.type_object=11
		JOIN vedette_link on vedette_link.num_object=index_concept.num_concept AND vedette_link.type_object=1
		JOIN vedette_object on vedette_object.num_vedette=vedette_link.num_vedette AND vedette_object.object_type=10 and vedette_object.object_id='".$this->notice->notice_id."'
		ORDER BY explnum_mimetype, explnum_id ";
        $res = pmb_mysql_query($requete) or die($requete . " " . pmb_mysql_error());
        $nb_ex = pmb_mysql_num_rows($res);
        if ($nb_ex) {
            // on recupere les donnees des exemplaires
            $i = 1;
            $ligne_finale = '';
            while (($expl = pmb_mysql_fetch_object($res))) {
                // couleur de l'img en fonction du statut
                if ($expl->explnum_docnum_statut) {
                    $rqt_st = "SELECT * FROM explnum_statut WHERE  id_explnum_statut='" . $expl->explnum_docnum_statut . "' ";
                    $Query_statut = pmb_mysql_query($rqt_st) or die($rqt_st . " " . pmb_mysql_error());
                    $r_statut = pmb_mysql_fetch_object($Query_statut);
                    $class_img = " class='docnum_" . $r_statut->class_html . "' ";
                    if ($expl->explnum_docnum_statut > 1) {
                        $txt = $r_statut->opac_libelle;
                    } else {
                        $txt = "";
                    }
                    $statut_libelle_div = "
					<div id='zoom_statut_docnum" . $expl->explnum_id . "' style='border: 2px solid rgb(85, 85, 85); background-color: rgb(255, 255, 255); position: absolute; z-index: 2000; display: none;'>
					<b>$txt</b>
					</div>
					";
                } else {
                    $class_img = " class='docnum_statutnot1' ";
                    $txt = "";
                }
                if ($i == 1) {
                    $ligne = "<tr><td class='docnum' width='25%'>!!1!!</td><td class='docnum' width='25%'>!!2!!</td><td class='docnum' width='25%'>!!3!!</td><td class='docnum' width='25%'>!!4!!</td></tr>";
                }
                $tlink = '';
                if ($this->link_explnum) {
                    $tlink = str_replace("!!explnum_id!!", $expl->explnum_id, $this->link_explnum);
                    $tlink = str_replace("!!notice_id!!", $expl->explnum_notice, $tlink);
                    $tlink = str_replace("!!bulletin_id!!", $expl->explnum_bulletin, $tlink);
                }
                $alt = htmlentities($expl->explnum_nom . " - " . $expl->explnum_mimetype, ENT_QUOTES, $charset);
	                    
                if ($prefix_url_image) {
                    $tmpprefix_url_image = $prefix_url_image;
                } else {
                    $tmpprefix_url_image = "./";
                }
                if ($expl->explnum_vignette) {
                    $thumbnail_url = explnum::get_thumbnail_url($expl->explnum_vignette, $expl->explnum_id);
                    $obj = "<img src='" . $thumbnail_url . "' alt='$alt' title='$alt' style='border:0px' loading='lazy'>";
                } else { // trouver l'icone correspondant au mime_type
                    $obj = "<img src='" . $tmpprefix_url_image . "images/mimetype/" . icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier) . "' alt='$alt' title='$alt' border='0' loading='lazy'>";
                }
                $obj_suite = "$statut_libelle_div
    			<a  href='#' onmouseout=\"z=document.getElementById('zoom_statut_docnum" . $expl->explnum_id . "'); z.style.display='none'; \" onmouseover=\"z=document.getElementById('zoom_statut_docnum" . $expl->explnum_id . "'); z.style.display=''; \">
    			<div class='vignette_doc_num' ><img $class_img width='10' height='10' src='" . get_url_icon('spacer.gif') . "'></div>
    			</a>
    			";
                $expl_liste_obj = "";
                $expl_liste_obj .= "<a href='" . $tmpprefix_url_image . "doc_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' target='_blank'>" . $obj . "</a>$obj_suite<br />";
	                            
                if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) {
                    $explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"];
                } elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) {
                    $explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"];
                } else {
                    $explmime_nom = $expl->explnum_mimetype;
                }
                if (isset($param_aff["mine_type"])) {
                    $explmime_nom = "";
                }
                if ($tlink) {
                    $expl_liste_obj .= "<a href='$tlink'>";
                    $expl_liste_obj .= htmlentities($expl->explnum_nom, ENT_QUOTES, $charset) . "</a><div class='explnum_type'>" . htmlentities($explmime_nom, ENT_QUOTES, $charset) . "</div>";
                } else {
                    $expl_liste_obj .= htmlentities($expl->explnum_nom, ENT_QUOTES, $charset) . "<div class='explnum_type'>" . htmlentities($explmime_nom, ENT_QUOTES, $charset) . "</div>";
                }
                // recherche des concepts...
                $query = "select num_concept,value from index_concept join skos_fields_global_index on num_concept = id_item and code_champ = 1  where num_object = " . $expl->explnum_id . " and type_object = 11 order by order_concept";
                $result = pmb_mysql_query($query);
                $concept = "";
                if (pmb_mysql_num_rows($result)) {
                    while ($row = pmb_mysql_fetch_object($result)) {
                        if ($concept) {
                            $concept .= " / ";
                        }
                        if (SESSrights & AUTORITES_AUTH) {
                            $concept .= "<a href='" . $base_path . "/autorites.php?categ=see&sub=concept&id=" . $row->num_concept . "' title='" . addslashes($msg['concept_menu'] . ": " . htmlentities($row->value, ENT_QUOTES, $charset)) . "'>" . htmlentities($row->value, ENT_QUOTES, $charset) . "</a>";
                        } else {
                            $concept .= "<span title='" . addslashes($msg['concept_menu'] . ": " . htmlentities($row->value, ENT_QUOTES, $charset)) . "'>" . htmlentities($row->value, ENT_QUOTES, $charset) . "</span>";
                        }
                    }
                }
                $expl_liste_obj .= $concept . "";
                $ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
                $i ++;
                if ($i == 5) {
                    $ligne_finale .= $ligne;
                    $i = 1;
                }
            }
            if (! $ligne_finale) {
                $ligne_finale = $ligne;
            } elseif ($i != 1) {
                $ligne_finale .= $ligne;
            }
            $ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
            $ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
            $ligne_finale = str_replace('!!4!!', "&nbsp;", $ligne_finale);
        } else {
            return "";
        }
        $entry = "<table class='docnum' role='presentation'>$ligne_finale</table>";
        return $entry;
	}
}