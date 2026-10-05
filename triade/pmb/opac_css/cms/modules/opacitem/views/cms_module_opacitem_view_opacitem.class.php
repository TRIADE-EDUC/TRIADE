<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_opacitem_view_opacitem.class.php,v 1.13 2023/12/07 10:12:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_opacitem_view_opacitem extends cms_module_common_view{
	protected $cadre_parent;
	
	public function __construct($id=0){
		parent::__construct($id+0);
	}
	
	public function render($datas){
		global $opac_url_base;
		global $base_path;
		global $include_path;
		global $class_path;
		global $msg;
		global $dbh;
		global $charset;
		
		if(is_array($datas['globals'])) {
			foreach($datas['globals'] as $globalName=>$globalValue){
				global ${$globalName};
				$savGlobals[$globalName]=${$globalName};
				${$globalName}=$globalValue['value'];
			}
		}
		
		//utiles dans le contexte
		//cms_module_opacitem_item_infopage
		
		//cms_module_opacitem_item_navperio
		global $a2z_perio_display;
		global $onglet_a2z;
		global $ongletSub_a2z;
		global $ongletSubList_a2z;
		global $a2z_perio,$a2z_tpl;
		global $a2z_tpl_ajax;
		global $abt_actif;
		global $avis_tpl_form_script;
		global $filtre_select;
		global $location;
		global $surloc;
		global $gestion_acces_active;
		global $gestion_acces_empr_notice;
		global $f_bull_deb_id;
		global $f_bull_end_id;
		global $page;
		global $bull_date_start;
		global $bull_date_end;
		global $bull_num_deb,$bull_num_end;
		global $flag_no_get_bulletin;
		global $recherche_ajax_mode;
		global $icon_doc;
		global $begin_result_liste;
		global $notice_header;
		global $fonction;
		global $popup_resa;
		global $allow_book;
		global $seule;
		//cms_module_opacitem_item_categ
		global $tpl_div_categories;
		global $tpl_div_category;
		global $tpl_subcategory;
		//cms_module_opacitem_item_bannettes
		global $liens_opac;
		global $date_diff;
		global $affiche_bannette_tpl;
		global $id_empr ;
		//cms_module_opacitem_item_section
		global $opac_view_filter_class;
		//cms_module_opacitem_item_margueritte
		
		//cms_module_opacitem_item_centcases
		
		//cms_module_opacitem_item_dernotices
		
		//cms_module_opacitem_item_etageres
		global $etageres_header;
		global $etageres_footer;
		global $notice_display_header;
		global $notice_display_footer;
		global $_mimetypes_bymimetype_;
		global $_mimetypes_byext_ ;
		global $empty_pwd;
		global $ext_pwd;
		global $action;
		global $callback_func;
		global $callback_url;
		global $new_tab;
		global $search_terms;
		global $show_tris_form;
		global $sort;
		global $show_sel_form;
		global $liste_criteres_tri;
		//cms_module_opacitem_item_rssflux
		//cms_module_opacitem_item_contact_form
		global $opac_contact_form;
		global $contact_form_form_tpl;
		global $contact_form_recipients_tpl;
		//cms_module_opacitem_item_collstate_bulletins_display
		global $pmb_collstate_advanced;
		global $id, $serial_id, $bulletin_id;
		
		$return='';
		switch ($datas['opacitem']){
			case 'cms_module_opacitem_item_infopage':
				require_once ($base_path.'/includes/show_infopages.inc.php');
				$return="<div id='infopages_top'>".show_infopages($opac_show_infopages_id_top)."</div>";
				break;
			case 'cms_module_opacitem_item_navperio':
				require_once($base_path.'/classes/perio_a2z.class.php');
				$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);
				$return=$a2z->get_form();
				break;
			case 'cms_module_opacitem_item_categ':
				$opac_show_categ_browser_tab=explode(" ",$opac_show_categ_browser);
				
				if ($opac_show_categ_browser_tab[1]){
					$opac_show_categ_browser_home_id_thes=$opac_show_categ_browser_tab[1];
				}	
				require_once ($base_path.'/classes/categorie.class.php');
				require_once ($base_path.'/includes/templates/categories.tpl.php');
				ob_start();
				require_once ($base_path.'/categ/categories.inc.php');
				$return=ob_get_contents();
				ob_clean();
				break;
			case 'cms_module_opacitem_item_bannettes_abo':
				require_once($base_path."/includes/bannette_func.inc.php");
				$affiche_bannette_tpl="
				<div class='bannette' id='banette_!!id_bannette!!'>
				!!diffusion!!
				</div>
				";
				$aff = pmb_bidi(affiche_bannettes($opac_bannette_nb_liste, "./empr.php?lvl=bannette&id_bannette=!!id_bannette!!","bannettes_private-container_2", "")) ;
				if($aff){	
					$bannettes= "<div id='bannettes_subscribed'>\n";
					$bannettes.= "<h3><span>".$msg['accueil_bannette_privee']."</span></h3>";
					$bannettes.= "<div id='bannettes_private-container'>";
					$bannettes.= "<script src='./includes/javascript/tablist.js'></script>" ;
					if ($opac_bannette_notices_depliables && $opac_bannette_notices_format!=8){
						$bannettes.= $begin_result_liste ;
					}
					$bannettes.= $aff;
					$bannettes.= "</div><!-- fermeture #bannettes-container -->\n";
					$bannettes.= "</div><!-- fermeture #bannettes -->\n";
					$return= $bannettes;
				}
				break;
			case 'cms_module_opacitem_item_bannettes_pub':
				require_once($base_path."/includes/bannette_func.inc.php");
				$affiche_bannette_tpl="
				<div class='bannette' id='banette_!!id_bannette!!'>
				!!diffusion!!
				</div>
				";
				$aff = pmb_bidi(affiche_bannettes($opac_bannette_nb_liste, "./index.php?lvl=bannette_see&id_bannette=!!id_bannette!!","bannettes-public-container_2", "",true)) ;
				if($aff){
					$bannettes= "<div id='bannettes_public'>\n";
					$bannettes.= "<h3><span>".$msg['accueil_bannette_public']."</span></h3>";
					$bannettes.= "<div id='bannettes_public-container'>";
					$bannettes.= "<script src='./includes/javascript/tablist.js'></script>" ;
					if ($opac_bannette_notices_depliables && $opac_bannette_notices_format!=8){
						$bannettes.= $begin_result_liste ;
					}
					$bannettes.= $aff;
					$bannettes.= "</div><!-- fermeture #bannettes-container -->\n";
					$bannettes.= "</div><!-- fermeture #bannettes -->\n";
					$return= $bannettes;
				}
				break;
			case 'cms_module_opacitem_item_section':
				ob_start();
				if ($opac_sur_location_activate==1){
					require_once($base_path."/includes/enter_sur_location.inc.php");
				}else{
					require_once($base_path."/includes/enter_localisation.inc.php");
				}
				$return=ob_get_contents();
				ob_clean();
				break;
			case 'cms_module_opacitem_item_margueritte':
				ob_start();
				require_once ($base_path.'/indexint/marguerite_browser.inc.php');
				$margueritte_brower=ob_get_contents();
				ob_clean();
				$return= $margueritte_brower;
				break;
			case 'cms_module_opacitem_item_centcases':
				ob_start();
				require_once ($base_path.'/indexint/100cases_browser.inc.php');
				$centcases=ob_get_contents();
				ob_clean();
				$return= $centcases;
				break;
			case 'cms_module_opacitem_item_dernotices':
				ob_start();
				require_once ($base_path.'/includes/last_records.inc.php');
				$return=ob_get_contents();
				ob_clean();
				break;
			case 'cms_module_opacitem_item_etageres':
				require_once ($base_path.'/includes/templates/etagere.tpl.php');
				require_once ($base_path.'/includes/etagere_func.inc.php');
				$aff_etagere = affiche_etagere(1, "", 1, $opac_etagere_nbnotices_accueil, $opac_etagere_notices_format, $opac_etagere_notices_depliables, "./index.php?lvl=etagere_see&id=!!id!!", $liens_opac);
				if ($aff_etagere) {
					$return=$etageres_header.$aff_etagere.$etageres_footer;
				}
				break;
			case 'cms_module_opacitem_item_rssflux':
				ob_start();
				require_once ($base_path.'/includes/rss.inc.php');
				$return=ob_get_contents();
				ob_clean();
				break;
			case 'cms_module_opacitem_item_contact_form':
				if($opac_contact_form) {
					require_once ($class_path.'/contact_forms/contact_form.class.php');
					if(empty($id)) $id = 1;
					$contact_form = new contact_form($id);
					$return=$contact_form->get_form();
				} else {
					$return='';
				}
				break;
			case 'cms_module_opacitem_item_collstate_bulletins_display':
				if($pmb_collstate_advanced) {
					require_once($class_path."/collstate.class.php");
					$collstate = new collstate($id*1, $serial_id*1, $bulletin_id*1);
					$return=$collstate->get_collstate_bulletins_display();
				}
				break;
		}
		
		if(is_array($savGlobals)) {
			foreach($savGlobals as $globalName=>$globalValue){
				${$globalName}=$globalValue;
			}	
		}
		
		if($return){
			return $return;
		}
	}

}