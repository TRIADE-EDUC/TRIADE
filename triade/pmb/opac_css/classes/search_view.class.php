<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_view.class.php,v 1.65.2.4 2024/12/23 15:43:49 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $include_path;

use Pmb\AI\Models\AiSessionSemanticModel;
use Pmb\AI\Opac\Views\AiView;
use Pmb\AI\Orm\AiSessionSemanticOrm;
use Pmb\AI\Orm\AISettingsOrm;
use Pmb\Common\Orm\EmprOrm;
use Pmb\Searchform\Views\SearchAutocompleteView;
require_once($base_path."/includes/simple_search.inc.php");

global $opac_search_other_function;
if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

class search_view {

	protected static $search_type;

	protected static $url_base;

	protected static $user_query;

	public function __construct(){
	}

	public static function get_search_others_tab($search_type_asked, $label) {
		return "<li ".(static::$search_type == $search_type_asked ? "id='current' aria-current='page'" : "")."><a href=\"".static::format_url("search_type_asked=".$search_type_asked)."\">".$label."</a></li>";
	}

	public static function get_search_others_tabs() {
		global $msg;
		global $opac_allow_personal_search;
		global $opac_allow_extended_search;
		global $opac_allow_extended_search_authorities;
		global $opac_allow_term_search;
		global $opac_allow_tags_search;
		global $opac_show_onglet_perio_a2z;
		global $opac_show_onglet_empr;
		global $opac_allow_external_search;
		global $opac_show_onglet_map, $opac_map_activate;
		global $onglet_persopac;
		global $ai_active, $ai_allow_semantic_search;

		$search_others_tabs = "";
		$search_others_tabs .= static::get_search_others_tab('simple_search', $msg["simple_search"]);

		if ($opac_allow_personal_search) {
			$search_others_tabs .= static::get_search_others_tab('search_perso', $msg["search_perso_menu"]);
		}
		$search_persopac = new search_persopac();
		$search_others_tabs .= $search_persopac->directlink_user;
		
		$onglet_persopac = intval($onglet_persopac);

		if ($opac_allow_extended_search) {
			if($onglet_persopac) {
				$search_others_tabs .= "<li><a href=\"".static::format_url("search_type_asked=extended_search")."\">".$msg["extended_search"]."</a></li>";
			} else {
				$search_others_tabs .= static::get_search_others_tab('extended_search', $msg["extended_search"]);
			}
		}
		if ($opac_allow_extended_search_authorities) {
			if($onglet_persopac) {
				$search_others_tabs .= "<li><a href=\"".static::format_url("search_type_asked=extended_search_authorities")."\">".$msg["extended_search_authorities"]."</a></li>";
			} else {
				$search_others_tabs .= static::get_search_others_tab('extended_search_authorities', $msg["extended_search_authorities"]);
			}
		}
		if ($opac_allow_term_search) {
			$search_others_tabs .= static::get_search_others_tab('term_search', $msg["term_search"]);
		}
		if ($opac_allow_tags_search) {
			$search_others_tabs .= static::get_search_others_tab('tags_search', $msg["tags_search"]);
		}
		if ($opac_show_onglet_perio_a2z) {
			$search_others_tabs .= static::get_search_others_tab('perio_a2z', $msg["a2z_onglet"]);
		}
		if (($opac_show_onglet_empr==1)||(($opac_show_onglet_empr==2)&&($_SESSION["user_code"]))) {
			if (!$_SESSION["user_code"]) {
				$search_others_tabs .= static::get_search_others_tab('connect_empr', $msg["onglet_empr_connect"]);
			} else {
				switch ($opac_show_onglet_empr) {
					case 1:
						$empr_link_onglet=static::format_url("search_type_asked=connect_empr");
						break;
					case 2:
						$empr_link_onglet="./empr.php";
						break;
				}
				$search_others_tabs .= "<li><a href=\"$empr_link_onglet\">".$msg["onglet_empr_compte"]."</a></li>";
			}
		}
		if ($opac_allow_external_search) {
			$search_others_tabs .= "<li ".(static::$search_type == 'external_search' ? "id='current' aria-current='page'" : "")."><a href=\"".static::format_url("search_type_asked=external_search&external_type=simple")."\">".$msg["connecteurs_external_search"]."</a></li>";
		}
		if ($opac_show_onglet_map && $opac_map_activate) {
			$search_others_tabs .= static::get_search_others_tab('map', $msg["search_by_map"]);
		}
		if ($ai_active) {
			if (!$ai_allow_semantic_search || EmprOrm::exist(intval($_SESSION["id_empr_session"]))) {
				$search_others_tabs .= static::get_search_others_tab('ai_search', $msg["ia_search_ia"]);
			}
		}
		return $search_others_tabs;
	}

	public static function get_search_tabs() {
		global $msg;
		global $opac_show_onglet_help;

		$search_tabs = "<ul class='search_tabs'>";
		$search_tabs .= static::get_search_others_tabs();
		$search_tabs .= ($opac_show_onglet_help ? "<li><a href=\"".static::$url_base."lvl=infopages&pagesid=$opac_show_onglet_help\">".$msg["search_help"]."</a></li>": '');
		$search_tabs .= "</ul>";
		return $search_tabs;
	}

	public static function get_display_info() {
		global $msg;

		$display = "<p class='p1'><span>";
		switch (static::$search_type) {
			case "simple_search":
				$display .= $msg['simple_search_tpl_text'];
				break;
			case "external_search":
				$display .= sprintf($msg["connecteurs_search_multi"], static::format_url("search_type_asked=external_search&external_type=multi"));
				break;
			case "tags_search":
				$display .= $msg['tags_search_tpl_text'];
				break;
		}
		$display .= "</span></p>";
		return $display;
	}

	public static function get_typdoc_field() {
		global $opac_search_show_typdoc;
		global $msg, $charset;
		global $typdoc;

		// les typ_doc
		if ($opac_search_show_typdoc) {
			$query = "SELECT typdoc FROM notices where typdoc!='' GROUP BY typdoc";
			$result = pmb_mysql_query($query);
			$toprint_typdocfield = "<label for='typdoc_select' class='visually-hidden'>".htmlentities($msg['rgaa_typdoc_select'], ENT_QUOTES, $charset)."</label><select name='typdoc' id='typdoc_select' title='".htmlentities($msg['rgaa_typdoc_select'], ENT_QUOTES, $charset)."'>";
			$toprint_typdocfield .= "  <option ";
			$toprint_typdocfield .=" value=''";
			if ($typdoc=='') $toprint_typdocfield .=" selected";
			$toprint_typdocfield .=">".$msg["simple_search_all_doc_type"]."</option>\n";
			$doctype = new marc_list('doctype');
			$obj=array();
			while (($rt = pmb_mysql_fetch_row($result))) {
				$obj[$rt[0]]=1;
			}
			foreach ($doctype->table as $key=>$libelle){
				if (isset($obj[$key]) && ($obj[$key]==1)){
					$toprint_typdocfield .= "  <option ";
					$toprint_typdocfield .= " value='$key'";
					if ($typdoc == $key) $toprint_typdocfield .=" selected";
					$toprint_typdocfield .= ">".htmlentities($libelle,ENT_QUOTES, $charset)."</option>\n";
				}
			}
			$toprint_typdocfield .= "</select>";
		} else $toprint_typdocfield="";
		return $toprint_typdocfield;
	}

	public static function get_display_simple_search_form() {
		global $msg;
		global $opac_autolevel2;
		global $opac_simple_search_suggestions;
		global $include_path;
		global $opac_show_help;
		global $base_path;
		global $opac_map_activate;
		global $opac_focus_user_query;
		global $opac_search_other_function;
		global $opac_recherches_pliables, $charset;
		global $opac_search_autocomplete;
		global $opac_rgaa_active;

		$form = "
		<form name='search_input' action='".($opac_autolevel2 ? static::format_url("lvl=more_results&autolevel1=1") : static::format_url("lvl=search_result"))."' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">
			".static::get_typdoc_field()."
			".($opac_search_other_function ? search_other_function_filters() : '')."
			<br />
			<input type='hidden' name='surligne' value='!!surligne!!'/>";
			$help_button = "";
		if ($opac_show_help) {
			if($opac_rgaa_active){
				$help_button = "<a href='#' class='bouton button_search_help' onClick='window.open(\"$base_path/help.php?whatis=simple_search\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' >$msg[search_help]</a>\n";
			}else{
				$help_button = "<input type='button' value='$msg[search_help]' class='bouton button_search_help' onClick='window.open(\"$base_path/help.php?whatis=simple_search\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' />\n";
			}
		}
		$submit_button = "<input type='submit' name='ok' value='".htmlentities($msg["142"], ENT_QUOTES, $charset)."' class='boutonrechercher'/>\n";
		if($opac_search_autocomplete) {
			$form .= static::get_autocomplete_input($submit_button . $help_button);
		} else {
			$form .= "
				<label for='user_query_lib' class='visually-hidden'>".htmlentities($msg['autolevel1_search'], ENT_QUOTES, $charset)."</label>
				<input type='text' name='user_query' title='".htmlentities($msg['autolevel1_search_title'], ENT_QUOTES, $charset)."' ";
			if($opac_simple_search_suggestions){
				$form .= "id='user_query_lib' class='text_query' value=\"" . htmlentities(stripslashes(static::$user_query),ENT_QUOTES,$charset) . "\" size='65' expand_mode='2' completion='suggestions' word_only='no'/>\n";
			}else{
				$form .= "id='user_query_lib' class='text_query' value=\"". htmlentities(stripslashes(static::$user_query),ENT_QUOTES,$charset) ."\" size='65' />\n";
			}
			$form .= $submit_button . $help_button;
		}
		switch ($opac_recherches_pliables) {
			case '1':
				$form .= "<div id='simple_search_zone'>".gen_plus_form("zsimples",$msg["rechercher_dans"], static::do_ou_chercher(),false)."</div>";
				break;
			case '2':
				$form .= "<div id='simple_search_zone'>".gen_plus_form("zsimples",$msg["rechercher_dans"], static::do_ou_chercher(),true)."</div>";
				break;
			case '3':
				$form .= static::do_ou_chercher_hidden();
				break;
			default:
				$form .= "<div id='simple_search_zone'>".static::do_ou_chercher()."</div>";
				break;
		}

		if($opac_map_activate==1 || $opac_map_activate==2) {
			$form .= "
				<div class='row'>
					<label class='etiquette'>".$msg["map_search"]."</label>
				</div>
				<div class='row'>
					!!map!!
				</div>";
		}
		$form .= "</form>
		<script src='".$include_path."/javascript/ajax.js'></script>
		<script>\n
                if (document.forms['search_input'] && document.forms['search_input'].elements['user_query']) {
                    ".($opac_focus_user_query ? 'document.forms["search_input"].elements["user_query"].focus();' : '')."
					".($opac_simple_search_suggestions ? 'ajax_pack_element(document.forms["search_input"].elements["user_query"]);' : '')."
                }
		</script>";
		return $form;
	}

	public static function get_display_extended_search_form() {
		global $base_path;
		global $msg, $charset;
		global $current_module;
		global $opac_extended_search_auto;
		global $opac_show_help;
		global $onglet_persopac;
		global $limitsearch;
		global $external_type;
		global $no_search;

		$onglet_persopac = intval($onglet_persopac);

		$form ="
		<script src=\"".$base_path."/includes/javascript/ajax.js\"></script>
		<script>var operators_to_enable = new Array();</script>";
		if($external_type=="multi"){
        	$form .= sprintf($msg["connecteurs_search_simple"], static::format_url("search_type_asked=external_search&external_type=simple"));
		}
		$form .= "<form class='form-$current_module' name='search_form' id='search_form' action='!!url!!' method='post'  onsubmit='enable_operators();valid_form_extented_search();'>
			<div class='form-contenu'>";
		if(!$limitsearch) {
			$form .= "<div id='choose_criteria'>".$msg["search_add_field"]."</div> !!field_list!!";
		}
		if(!$opac_extended_search_auto) {
			$form .= "<input type='button' class='bouton' value='".$msg["925"]."' onClick=\"if (this.form.add_field.value!='') { this.form.action='!!url!!'; this.form.target=''; this.form.submit();} else { alert('".htmlentities($msg["multi_select_champ"],ENT_QUOTES,$charset)."'); }\"/>";
		}
		if ($opac_show_help) {
			$form.="<input type='button' class='bouton button_search_help' name='?' value='$msg[search_help]' onClick='window.open(\"$base_path/help.php?whatis=search_multi\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' />";
		}
		$form.="<br /><br />
				<div class='row ".($onglet_persopac ? 'search_perso' : '')."'>
					!!already_selected_fields!!
				</div>
			</div>
			<input type='hidden' name='delete_field' value=''/>
			<input type='hidden' name='launch_search' value=''/>
			<input type='hidden' name='page' value='!!page!!'/>
			<input type='hidden' name='no_search' value='".(!empty($no_search) ? 1 : 0)."'/>
			".($onglet_persopac ? "<input type='hidden' name='onglet_persopac' value='".htmlentities($onglet_persopac, ENT_QUOTES, $charset)."'/>" : "")."
		</form>
		<script>ajax_parse_dom();</script>";
		return $form;
	}

	public static function get_display_external_search_form() {
		global $msg;
		global $include_path;
		global $opac_show_help;
		global $base_path, $charset, $opac_rgaa_active;

		$form = "
		<form name='search_input' action='".static::format_url("lvl=search_result&search_type_asked=external_search")."' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">
			".static::get_typdoc_field()."<br />
			<input type='hidden' name='surligne' value='!!surligne!!'/>
			<input type='text' name='user_query' class='text_query' value=\"" . htmlentities(static::$user_query,ENT_QUOTES,$charset) . "\" size='65' title='{$msg['autolevel1_search']}' />
			<input type='submit' name='ok' value='".$msg["142"]."' class='boutonrechercher'/>";
		if ($opac_show_help) {
			$form .= "<input type='button' title='".$msg['search_help']."' value='".$msg['search_help']."' class='bouton button_search_help' onClick='window.open(\"$base_path/help.php?whatis=simple_search\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' />\n";
		}
		$form .= "<div id='external_search_zone'>".static::do_ou_chercher()."</div>";

		if ($opac_rgaa_active) {
			$form .= "
				<button type='button' aria-controls='external_simple_search_zone' aria-label='" . htmlentities($msg['expandall'], ENT_QUOTES, $charset) . "' onclick='expandAll()' title='" . htmlentities($msg['expandall'], ENT_QUOTES, $charset) . "'>
					<img id='expandall' class='img_plusplus' src='" . get_url_icon("expand_all.gif") . "' alt='" . htmlentities($msg['expandall'], ENT_QUOTES, $charset) . "'>
					<span class='visually-hidden'>" . htmlentities($msg['expandall'], ENT_QUOTES, $charset) . "</span>
				</button>
				<button type='button' aria-controls='external_simple_search_zone' aria-label='" . htmlentities($msg['collapseall'], ENT_QUOTES, $charset) . "' onclick='collapseAll()' title='" . htmlentities($msg['collapseall'], ENT_QUOTES, $charset) . "'>
					<img id='collapseall' class='img_moinsmoins' src='" . get_url_icon("collapse_all.gif") . "' alt='" . htmlentities($msg['collapseall'], ENT_QUOTES, $charset) . "'>
					<span class='visually-hidden'>" . htmlentities($msg['collapseall'], ENT_QUOTES, $charset) . "</span>
				</button>
			";

		} else {
			$form .= "<br />
				<a href='javascript:expandAll()' title='".htmlentities($msg['expand'], ENT_QUOTES, $charset)."'>
					<img class='img_plusplus' src='".get_url_icon("expand_all.gif")."' style='border:0px' id='expandall'>
				</a>&nbsp;
				<a href='javascript:collapseAll()' title='".htmlentities($msg['collapse'], ENT_QUOTES, $charset)."'>
					<img class='img_moinsmoins' src='".get_url_icon("collapse_all.gif")."' style='border:0px' id='collapseall' />
				</a>\n
			";
		}


		$form .= "<div id='external_simple_search_zone'><!--!!sources!!--></div>
		</form>
		<script>\n
			document.search_input.user_query.focus();\n

			function change_source_checkbox(changing_control, source_id) {
				var i=0; var count=0;
				onoff = changing_control.checked;
				for(i=0; i<document.search_input.elements.length; i++)
				{
					if(document.search_input.elements[i].name == 'source[]')	{
						if (document.search_input.elements[i].value == source_id)
							document.search_input.elements[i].checked = onoff;
					}
				}
			}
		</script>";
		return $form;
	}

	public static function get_display_tags_search_form() {
		global $msg, $charset;

		$form = "
		<form name='search_input' action='".static::format_url("lvl=search_result&search_type_asked=tags_search")."' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">\n
			".static::get_typdoc_field()."<br />\n
			<input type='text' name='user_query' class='text_query' value=\"" . htmlentities(static::$user_query,ENT_QUOTES,$charset) . "\" size='65' title='".htmlentities($msg['autolevel1_search'], ENT_QUOTES, $charset)."' />\n
			<input type='submit' name='ok' value='".$msg["142"]."' class='boutonrechercher'/>\n
		</form>
		<script>
			document.search_input.user_query.focus();\n
		</script>
		";
		return $form;
	}

	public static function get_display_term_search_form() {
	    global $msg, $charset, $base_path, $current_module;
		global $lvl;
		global $opac_show_help;
		global $search_term;
		global $term_click;
		global $page_search;
		global $opac_thesaurus;
		global $id_thes;

		$page_search = intval($page_search);

		//recuperation du thesaurus session
		if(!$id_thes) $id_thes = thesaurus::getSessionThesaurusId();
		else thesaurus::setSessionThesaurusId($id_thes);

		//affichage du selectionneur de thesaurus et du lien vers les thesaurus
		$liste_thesaurus = thesaurus::getThesaurusList();
		$sel_thesaurus = '';

		if ($opac_thesaurus != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
			$sel_thesaurus = "<select class='saisie-30em' name='id_thes' ";
			$sel_thesaurus.= "onchange = \"document.location = '".static::$url_base."lvl=index&search_type_asked=term_search&id_thes='+this.value; \">" ;
			foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
				$sel_thesaurus.= "<option value='".$id_thesaurus."' "; ;
				if ($id_thesaurus == $id_thes) $sel_thesaurus.= " selected";
				$sel_thesaurus.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES, $charset)."</option>";
			}
			$sel_thesaurus.= "<option value=-1 ";
			if ((!$id_thes) || ($id_thes == -1)) $sel_thesaurus.= "selected ";
			$sel_thesaurus.= ">".htmlentities($msg['thes_all'],ENT_QUOTES, $charset)."</option>";
			$sel_thesaurus.= "</select>&nbsp;";
		}

		$form = "
		<form class='form-$current_module' name='term_search_form' method='post' action='".static::format_url("lvl=$lvl&search_type_asked=term_search")."'>
			<div class='form-contenu'>
			".$sel_thesaurus."
			<span class='libSearchTermes'>".$msg["term_search_search_for"]."</span>
			<input type='text' class='saisie-50em' id='search_term' name='search_term' completion='categories' autfield='search_term_id' linkfield='id_thes'  value='".htmlentities(stripslashes($search_term ?? ""), ENT_QUOTES, $charset)."' title='".htmlentities($msg['term_search_search_for'], ENT_QUOTES, $charset)."' />
			<input type='hidden' id='search_term_id' name='search_term_id' value='' />
			<!--	Bouton Rechercher -->
			<input type='submit' class='boutonrechercher' value='$msg[142]' onClick=\"this.form.page_search.value=''; this.form.term_click.value='';\"/>\n";
		if ($opac_show_help) $form .= "<input type='submit' class='bouton button_search_help' title='".$msg['search_help']."' value='$msg[search_help]' onClick='window.open(\"help.php?whatis=search_terms\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' />\n";
		$form .= "<input type='hidden' name='term_click' value='".htmlentities(stripslashes($term_click ?? ""),ENT_QUOTES,$charset)."'/>
			<input type='hidden' name='page_search' value='".$page_search."'/>
			</div>
		</form>
		<script src='".$base_path."/includes/javascript/ajax.js'></script>
		<script>
			ajax_pack_element(document.forms['term_search_form'].elements['search_term']);
			document.forms['term_search_form'].elements['search_term'].focus();
		</script>";
		return $form;
	}

	public static function get_display_search() {
		$display_search = "<div id='search' role='search'>";
		$display_search .= static::get_search_tabs();
		$display_search .= "<div id='search_crl'></div>";
		if(isset($_SESSION["ext_type"]) && ($_SESSION["ext_type"] != "multi")) $display_search .= static::get_display_info();
		$display_search .= "<div class='row'>";

		switch (static::$search_type) {
			// éléments pour la recherche simple
			case "search_universes":
			    $display_search .= static::get_display_search_universe_form();
			    break;
			case "simple_search":
				$display_search .= static::get_display_simple_search_form();
				break;
			case "extended_search":
				global $opac_autolevel2;
				global $es;
				global $lvl;
// 				$display_search .= static::get_display_extended_search_form();
				$es=new search();
				if($opac_autolevel2==2){
					$display_search .= $es->show_form(static::format_url("lvl=".$lvl."&search_type_asked=extended_search"), static::format_url("lvl=more_results&mode=extended"));
				}else{
					$display_search .= $es->show_form(static::format_url("lvl=".$lvl."&search_type_asked=extended_search"), static::format_url("lvl=search_result&search_type_asked=extended_search"));
				}
				break;
			case "extended_search_authorities":
				global $opac_autolevel2;
				global $es;
				global $lvl;
// 				$display_search .= static::get_display_extended_search_form();
				$es=new search_authorities('search_fields_authorities');
				if($opac_autolevel2==2){
					$display_search .= $es->show_form(static::format_url("lvl=".$lvl."&search_type_asked=extended_search_authorities"), static::format_url("lvl=more_results&mode=extended_authorities"));
				}else{
					$display_search .= $es->show_form(static::format_url("lvl=".$lvl."&search_type_asked=extended_search_authorities"), static::format_url("lvl=search_result&search_type_asked=extended_search_authorities"));
				}
				break;
			case "external_search":
				global $es;
				global $lvl;
				if ($_SESSION["ext_type"]!="multi") {
					$display_search .= static::get_display_external_search_form();
				} else {
					$display_search .= $es->show_form("./index.php?lvl=$lvl&search_type_asked=external_search","./index.php?lvl=search_result&search_type_asked=external_search");
				}
				break;
			case "tags_search":
				$display_search .= static::get_display_tags_search_form();
				break;
			case "term_search":
				$display_search .= static::get_display_term_search_form();
				break;
			case "connect_empr":
				$display_search .= affichage_onglet_compte_empr();
				break;
			case "search_perso":
				$search_p= new search_persopac();
				$display_search .= $search_p->do_list();
				break;
			case "perio_a2z":
				global $opac_perio_a2z_abc_search;
				global $opac_perio_a2z_max_per_onglet;

				// affichage des _perio_a2z
				$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);
				$display_search .= $a2z->get_form();
				break;
			case "map":
				//Géolocalisation
				$display_search .= static::get_search_form_map();
				break;
			case "ai_search":
				$display_search .= static::get_search_form_ai_search();
				break;
		}
		$display_search .= "</div>";
		$display_search .= static::get_display_search_perso();
		$display_search .= "</div>";
		return $display_search;
	}

	public static function get_display_search_perso() {
		$search_p= new search_persopac();
		$onglets_search_perso=$search_p->directlink_user;
		return $search_p->directlink_user_form;
	}

	public static function get_search_label($id, $mode, $location = '') {
		global $msg;

		$search_label = '';
		switch ($mode) {
			case 'etagere_see':
				$search_label = $msg["etagere_query"];
				break;
			case 'categ_see':
				$search_label = $msg["category"];
				break;
			case 'indexint_see':
				$search_label= $msg["indexint_search"];
				break;
			case 'section_see':
				$resultat=pmb_mysql_query("select location_libelle from docs_location where idlocation='".addslashes($location)."'");
				$j=pmb_mysql_fetch_array($resultat);
				pmb_mysql_free_result($resultat);
				$search_label = $j["location_libelle"]." => ".$msg["section"];
				break;
			case "author_see" :
				$search_label = $msg['author'];
				break;
			case "coll_see" :
				$search_label = $msg['coll_search'];
				break;
			case "subcoll_see" :
				$search_label = $msg['subcoll_search'];
				break;
			case "titre_uniforme_see" :
				$search_label = $msg['titre_uniforme_search'];
				break;
			case "publisher_see" :
				$search_label = $msg['publisher_search'];
				break;
			case "serie_see" :
				$search_label = $msg['serie_query'];
				break;
			case "concept_see" :
				$search_label = $msg['skos_concept'];
				break;
			case "authperso_see" :
				$authperso = new authperso_data($id);
				$search_label = $authperso->get_name();
				break;
		}
		return $search_label;
	}

	public static function get_search_isbd($id, $mode) {
		$search_isbd = '';
		switch ($mode) {
			case 'etagere_see':
				$t=array();
				$t=tableau_etagere($id);
				$search_isbd = $t[0]["nometagere"];
				break;
			case 'categ_see':
				$ourCateg = new categorie($id);
				$search_isbd = $ourCateg->libelle;
				break;
			case 'indexint_see':
				$ourIndexint = new indexint($id);
				$search_isbd = $ourIndexint->get_isbd();
				break;
			case 'section_see':
				$resultat=pmb_mysql_query("select section_libelle from docs_section where idsection='".addslashes($id)."'");
				$j=pmb_mysql_fetch_array($resultat);
				$search_isbd = $j["section_libelle"];
				$search_isbd .= static::get_search_section_complement_isbd($id, $mode);
				break;
			case "author_see" :
				$ourAuthor = new auteur($id);
				$search_isbd = $ourAuthor->get_isbd();
				break;
			case "coll_see" :
				$ourColl = new collection($id);
				$search_isbd = $ourColl->get_isbd();
				break;
			case "subcoll_see" :
				$ourSubcoll = new subcollection($id);
				$search_isbd = $ourSubcoll->get_isbd();
				break;
			case "titre_uniforme_see" :
				$ourTu = new titre_uniforme($id);
				$search_isbd = $ourTu->get_isbd();
				break;
			case "publisher_see" :
				$ourPub = new publisher($id);
				$search_isbd = $ourPub->get_isbd();
				break;
			case "serie_see" :
				$ourSerie = new serie($id);
				$search_isbd = $ourSerie->get_isbd();
				break;
			case "concept_see" :
				$ourConcept = new skos_concept($id);
				$search_isbd = $ourConcept->get_display_label();
				break;
			case "authperso_see" :
				$authperso = new authperso_data($id);
				$search_isbd = $authperso->get_isbd();
				break;
		}
		return $search_isbd;
	}

	public static function get_last_human_query() {
		$human_query = static::get_search_label($_SESSION["last_module_search"]["search_id"], $_SESSION["last_module_search"]["search_mod"], $_SESSION["last_module_search"]["search_location"]);
		$human_query .= " '".static::get_search_isbd($_SESSION["last_module_search"]["search_id"], $_SESSION["last_module_search"]["search_mod"])."'";
		return $human_query;
	}

	public static function set_search_type($search_type) {
		static::$search_type = $search_type;
	}

	public static function set_url_base($url_base) {
		static::$url_base = $url_base;
	}

	public static function get_display_map() {
		global $opac_map_activate;
		global $opac_map_base_layer_params;
		global $opac_map_base_layer_type;
		global $opac_map_size_search_edition;
		global $opac_map_bounding_box;

		$display = '';
		if($opac_map_activate){
			$layer_params = json_decode($opac_map_base_layer_params,true);
			$baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$opac_map_base_layer_type;
			if(is_array($layer_params) && count($layer_params)){
				if($layer_params['name']) $baselayer.=",baseLayerName:\"".$layer_params['name']."\"";
				if($layer_params['url']) $baselayer.=",baseLayerUrl:\"".$layer_params['url']."\"";
				if($layer_params['options']) $baselayer.=",baseLayerOptions:".json_encode($layer_params['options']);
			}
			$size=explode("*",$opac_map_size_search_edition);
			if(count($size)!=2) {
				$map_size="width:800px; height:480px;";
			} else {
				if (is_numeric($size[0])) $size[0].= 'px';
				if (is_numeric($size[1])) $size[1].= 'px';
				$map_size= "width:".$size[0]."; height:".$size[1].";";
			}
			$initialFit = '';
			$map_emprises_query = array();
			if( $opac_map_bounding_box) {
				$map_bounding_box = $opac_map_bounding_box;
			} else {
				$map_bounding_box = '-5 50,9 50,9 40,-5 40,-5 50';
			}
			$map_hold = new map_hold_polygon("bounding", 0, "polygon((".$map_bounding_box."))");
			if ($map_hold) {
				$coords = $map_hold->get_coords();
				$initialFit = explode(',', map_objects_controler::get_coord_initialFit($coords));
			} else{
				$initialFit = array(0, 0, 0, 0);
			}

			$map_holds=array();
			foreach($map_emprises_query as $map_hold){
				$map_holds[] = array(
						"wkt" => $map_hold,
						"type"=> "search",
						"color"=> null,
						"objects"=> array()
				);
			}
			$display .= "<div id='map_search' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='".$baselayer.",mode:\"search_criteria\",hiddenField:\"map_emprises_query\",initialFit:".json_encode($initialFit,true).",searchHolds:".json_encode($map_holds,true)."'></div>";
		}
		return $display;
	}

	public static function get_options_typdoc_field() {
		global $msg, $charset;
		global $typdoc;

		$query = "SELECT count(typdoc), typdoc ";
		$query .= "FROM notices where typdoc!='' GROUP BY typdoc";
		$result = pmb_mysql_query($query);
		$toprint_typdocfield = "  <option value=''>".$msg['tous_types_docs']."</option>\n";
		$doctype = new marc_list('doctype');
		$obj=array();
		$qte=array();
		while (($rt = pmb_mysql_fetch_row($result))) {
			$obj[$rt[1]]=1;
			$qte[$rt[1]]=$rt[0];
		}
		foreach ($doctype->table as $key=>$libelle){
			if ($obj[$key]==1){
				$toprint_typdocfield .= "  <option ";
				$toprint_typdocfield .= " value='$key'";
				if ($typdoc == $key) $toprint_typdocfield .=" selected='selected' ";
				$toprint_typdocfield .= ">".htmlentities($libelle." (".$qte[$key].")",ENT_QUOTES, $charset)."</option>\n";
			}
		}
		return $toprint_typdocfield;
	}

	public static function get_options_status_field() {
		global $msg, $charset;
		global $statut_query;

		// récupération des statuts de documents utilisés.
		$query = "SELECT count(statut), id_notice_statut, gestion_libelle ";
		$query .= "FROM notices, notice_statut where id_notice_statut=statut GROUP BY id_notice_statut order by gestion_libelle";
		$result = pmb_mysql_query($query);
		$toprint_statutfield = "  <option value=''>".$msg['tous_statuts_notice']."</option>";
		while ($obj = @pmb_mysql_fetch_row($result)) {
			$toprint_statutfield .= "  <option value='$obj[1]'";
			if ($statut_query==$obj[1]) $toprint_statutfield.=" selected";
			$toprint_statutfield .=">".htmlentities($obj[2]."  (".$obj[0].")",ENT_QUOTES, $charset)."</OPTION>\n";
		}
		return $toprint_statutfield;
	}

	public static function do_ou_chercher() {
		global $look_TITLE,
		$look_AUTHOR,
		$look_PUBLISHER,
		$look_TITRE_UNIFORME,
		$look_COLLECTION,
		$look_SUBCOLLECTION,
		$look_CATEGORY,
		$look_INDEXINT,
		$look_KEYWORDS,
		$look_ABSTRACT,
		$look_ALL,
		$look_DOCNUM,
		$look_CONTENT,
		$look_CONCEPT;

		global $look_FIRSTACCESS ; // si 0 alors premier Acces : la rech par defaut est cochee

		// pour mise en service de cette precision de recherche : commenter cette partie
		/*
		$look_TITLE = "1" ;
		$look_AUTHOR = "1" ;
		$look_PUBLISHER = "1" ;
		$look_COLLECTION = "1" ;
		$look_SUBCOLLECTION = "1" ;
		$look_CATEGORY = "1" ;
		$look_INDEXINT = "1" ;
		$look_KEYWORDS = "1" ;
		$look_ABSTRACT = "1" ;
		$look_CONTENT = "1" ;
		return "";
		*/
		// pour mise en service de cette precision de recherche : commenter jusque la

		// on recupere les globales de ce qui est autorise en recherche dans le parametrage de l'OPAC
		global	$opac_modules_search_title,
		$opac_modules_search_author,
		$opac_modules_search_publisher,
		$opac_modules_search_titre_uniforme,
		$opac_modules_search_collection,
		$opac_modules_search_subcollection,
		$opac_modules_search_category,
		$opac_modules_search_indexint,
		$opac_modules_search_keywords,
		$opac_modules_search_abstract,
		$opac_modules_search_all,
		$opac_modules_search_docnum,
		$pmb_indexation_docnum,
		$opac_modules_search_concept,
		$opac_allow_tags_search,
		$opac_autolevel2;
		// $opac_modules_search_content; inutilise pour l'instant, le search_abstract cherche aussi dans les notes de contenu

		global $msg,$get_query;

		if (!$look_FIRSTACCESS && !$get_query ) {
			// premier acces :
			if ($opac_modules_search_title==2) $look_TITLE=1;
			if ($opac_modules_search_author==2) $look_AUTHOR=1 ;
			if ($opac_modules_search_publisher==2) $look_PUBLISHER = 1 ;
			if ($opac_modules_search_titre_uniforme==2) $look_TITRE_UNIFORME = 1 ;
			if ($opac_modules_search_collection==2) $look_COLLECTION = 1 ;
			if ($opac_modules_search_subcollection==2) $look_SUBCOLLECTION = 1 ;
			if ($opac_modules_search_category==2) $look_CATEGORY = 1 ;
			if ($opac_modules_search_indexint==2) $look_INDEXINT = 1 ;
			if ($opac_modules_search_keywords==2) $look_KEYWORDS = 1 ;
			if ($opac_modules_search_abstract==2) $look_ABSTRACT = 1 ;
			if ($opac_modules_search_all==2) $look_ALL = 1 ;
			if ($opac_modules_search_docnum==2) $look_DOCNUM = 1;
			if ($opac_modules_search_concept==2) $look_CONCEPT = 1;
		}
		if ($look_TITLE) 			$checked_TITLE = "checked" ;   			else $checked_TITLE = "" ;
		if ($look_AUTHOR)			$checked_AUTHOR = "checked" ; 			else $checked_AUTHOR = "";
		if ($look_PUBLISHER)		$checked_PUBLISHER = "checked" ;		else $checked_PUBLISHER = "";
		if ($look_TITRE_UNIFORME)	$checked_TITRE_UNIFORME = "checked" ;	else $checked_TITRE_UNIFORME = "";
		if ($look_COLLECTION)		$checked_COLLECTION = "checked" ;		else $checked_COLLECTION = "";
		if ($look_SUBCOLLECTION)	$checked_SUBCOLLECTION = "checked" ;	else $checked_SUBCOLLECTION = "";
		if ($look_CATEGORY)			$checked_CATEGORY = "checked" ;			else $checked_CATEGORY = "";
		if ($look_INDEXINT)			$checked_INDEXINT = "checked" ;			else $checked_INDEXINT = "";
		if ($look_KEYWORDS)			$checked_KEYWORDS = "checked" ;			else $checked_KEYWORDS = "";
		if ($look_ABSTRACT)			$checked_ABSTRACT = "checked" ;			else $checked_ABSTRACT = "";
		if ($look_ALL)				$checked_ALL = "checked" ;				else $checked_ALL = "";
		if ($look_DOCNUM) 			$checked_DOCNUM = "checked";			else $checked_DOCNUM = "";
		if ($look_CONCEPT) 			$checked_CONCEPT = "checked";			else $checked_CONCEPT = "";

		$authpersos=authpersos::get_instance();
		$ou_chercher_authperso_tab=$authpersos->get_simple_seach_list_tpl();

		if (!($look_TITLE || $look_AUTHOR || $look_PUBLISHER || $look_TITRE_UNIFORME || $look_COLLECTION || $look_SUBCOLLECTION || $look_CATEGORY || $look_INDEXINT || $look_KEYWORDS || $look_ABSTRACT || $look_ALL || $look_DOCNUM || $look_CONCEPT || $authpersos->simple_seach_list_checked)) {
			$checked_TITLE = "checked" ;
			$look_TITLE = "1" ;
			$checked_AUTHOR = "checked" ;
			$look_AUTHOR = "1" ;
		}

		$cant_uncheck_look_all = "";
		if ($opac_autolevel2) {
		    // Prioritaire sur $opac_modules_search_all
		    $checked_ALL = "checked";
			$cant_uncheck_look_all = "onclick='return false;' title='".$msg['cant_uncheck_look_all']."'";
		}

		$ou_chercher_tab=array();
		if ($opac_modules_search_title>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_TITLE' id='look_TITLE' value='1' $checked_TITLE /><label for='look_TITLE'> $msg[titles] </label></span>";
		if ($opac_modules_search_author>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_AUTHOR' id='look_AUTHOR' value='1' $checked_AUTHOR /><label for='look_AUTHOR'> $msg[authors] </label></span>";
		if ($opac_modules_search_publisher>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_PUBLISHER' id='look_PUBLISHER' value='1' $checked_PUBLISHER /><label for='look_PUBLISHER'> $msg[publishers] </label></span>";
		if ($opac_modules_search_titre_uniforme>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_TITRE_UNIFORME' id='look_TITRE_UNIFORME' value='1' $checked_TITRE_UNIFORME/><label for='look_TITRE_UNIFORME'> ".$msg["titres_uniformes"]." </label></span>";
		if ($opac_modules_search_collection>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_COLLECTION' id='look_COLLECTION' value='1' $checked_COLLECTION /><label for='look_COLLECTION'> $msg[collections] </label></span>";
		if ($opac_modules_search_subcollection>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_SUBCOLLECTION' id='look_SUBCOLLECTION' value='1' $checked_SUBCOLLECTION /><label for='look_SUBCOLLECTION'> $msg[subcollections] </label></span>";
		if ($opac_modules_search_category>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_CATEGORY' id='look_CATEGORY' value='1' $checked_CATEGORY /><label for='look_CATEGORY'> $msg[categories] </label></span>";
		if ($opac_modules_search_indexint>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_INDEXINT' id='look_INDEXINT' value='1' $checked_INDEXINT /><label for='look_INDEXINT'> $msg[indexint] </label></span>";
		if ($opac_modules_search_keywords>0) {
			$ou_chercher_skey = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_KEYWORDS' id='look_KEYWORDS' value='1' $checked_KEYWORDS /><label for='look_KEYWORDS'> ";
			if($opac_allow_tags_search)	$ou_chercher_skey .= $msg['tag'];
			else $ou_chercher_skey .= $msg['keywords'];
			$ou_chercher_skey .= "</label></span>";
			$ou_chercher_tab[] = $ou_chercher_skey ;
		}
		if ($opac_modules_search_abstract>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_ABSTRACT' id='look_ABSTRACT' value='1' $checked_ABSTRACT /><label for='look_ABSTRACT'> $msg[abstract] </label></span>";
		if ($opac_modules_search_all>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_ALL' id='look_ALL' value='1' $checked_ALL $cant_uncheck_look_all /><label for='look_ALL'> ".$msg['tous']." </label></span>";
		if (($pmb_indexation_docnum && $opac_modules_search_docnum)>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_DOCNUM' id='look_DOCNUM' value='1' $checked_DOCNUM /><label for='look_DOCNUM'> ".$msg['docnum']." </label></span>";
		if ($opac_modules_search_concept>0) $ou_chercher_tab[] = "\n<span style='width: 30%; float: left;'><input type='checkbox' name='look_CONCEPT' id='look_CONCEPT' value='1' $checked_CONCEPT /><label for='look_CONCEPT'> ".$msg['skos_view_concepts_concepts']." </label></span>";

		$ou_chercher_tab=array_merge($ou_chercher_tab,$ou_chercher_authperso_tab);

		$ou_chercher = "<div class='row'>" ;
		for ($nbopac_smodules=0;$nbopac_smodules<count($ou_chercher_tab);$nbopac_smodules++) {
			if ((($nbopac_smodules+1)/3)==(($nbopac_smodules+1) % 3)) $ou_chercher .= "</div><div class='row'>" ;
			$ou_chercher .= $ou_chercher_tab[$nbopac_smodules];
		}

		$ou_chercher .= "</div><div style='clear: both;'><input type='hidden' name='look_FIRSTACCESS' value='1' /></div>" ;
		$ou_chercher = str_replace ("<div class='row'></div>", "", $ou_chercher ) ;
		return $ou_chercher;
	}

	public static function do_ou_chercher_hidden() {

		// on récupère les globales de ce qui est autorisé en recherche dans le paramétrage de l'OPAC
		global	$opac_modules_search_title,
		$opac_modules_search_author,
		$opac_modules_search_publisher,
		$opac_modules_search_titre_uniforme,
		$opac_modules_search_collection,
		$opac_modules_search_subcollection,
		$opac_modules_search_category,
		$opac_modules_search_indexint,
		$opac_modules_search_keywords,
		$opac_modules_search_abstract,
		$opac_modules_search_docnum,
		$opac_modules_search_all,
		$opac_modules_search_concept;

		$ou_chercher_hidden = '' ;
		if ($opac_modules_search_title>1) $ou_chercher_hidden .= "<input type='hidden' name='look_TITLE' id='look_TITLE' value='1' />";
		if ($opac_modules_search_author>1) $ou_chercher_hidden .= "<input type='hidden' name='look_AUTHOR' id='look_AUTHOR' value='1' />";
		if ($opac_modules_search_publisher>1) $ou_chercher_hidden .= "<input type='hidden' name='look_PUBLISHER' id='look_PUBLISHER' value='1' />";
		if ($opac_modules_search_titre_uniforme>1) $ou_chercher_hidden .= "<input type='hidden' name='look_TITRE_UNIFORME' id='look_TITRE_UNIFORME' value='1' />";
		if ($opac_modules_search_collection>1) $ou_chercher_hidden .= "<input type='hidden' name='look_COLLECTION' id='look_COLLECTION' value='1' />";
		if ($opac_modules_search_subcollection>1) $ou_chercher_hidden .= "<input type='hidden' name='look_SUBCOLLECTION' id='look_SUBCOLLECTION' value='1' />";
		if ($opac_modules_search_category>1) $ou_chercher_hidden .= "<input type='hidden' name='look_CATEGORY' id='look_CATEGORY' value='1' />";
		if ($opac_modules_search_indexint>1) $ou_chercher_hidden .= "<input type='hidden' name='look_INDEXINT' id='look_INDEXINT' value='1' />";
		if ($opac_modules_search_keywords>1) $ou_chercher_hidden .= "<input type='hidden' name='look_KEYWORDS' id='look_KEYWORDS' value='1' /> ";
		if ($opac_modules_search_abstract>1) $ou_chercher_hidden .= "<input type='hidden' name='look_ABSTRACT' id='look_ABSTRACT' value='1' />";
		if ($opac_modules_search_all>1) $ou_chercher_hidden .= "<input type='hidden' name='look_ALL' id='look_ALL' value='1' />";
		if ($opac_modules_search_docnum>1) $ou_chercher_hidden .= "<input type='hidden' name='look_DOCNUM' id='look_DOCNUM' value='1' />";
		if ($opac_modules_search_concept>1) $ou_chercher_hidden .= "<input type='hidden' name='look_CONCEPT' id='look_CONCEPT' value='1' />";

		$authpersos=authpersos::get_instance();
		$ou_chercher_hidden.=$authpersos->get_simple_seach_list_tpl_hiden();
		return $ou_chercher_hidden;
	}

	public static function get_field_text($n) {
		$typ_search=$_SESSION["notice_view".$n]["search_mod"];
		switch($_SESSION["notice_view".$n]["search_mod"]) {
			case 'title':
				$valeur_champ=$_SESSION["user_query".$n];
				$typ_search="look_TITLE";
				break;
			case 'all':
				$valeur_champ=$_SESSION["user_query".$n];
				$typ_search="look_ALL";
				break;
			case 'abstract':
				$valeur_champ=$_SESSION["user_query".$n];
				$typ_search="look_ABSTRACT";
				break;
			case 'keyword':
				$valeur_champ=$_SESSION["user_query".$n];
				$typ_search="look_KEYWORDS";
				break;
			case 'author_see':
				//Recherche de l'auteur
				$author_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select concat(author_name,', ',author_rejete) from authors where author_id='".addslashes($author_id)."'";
				$r_author=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_author)) {
					$valeur_champ=pmb_mysql_result($r_author,0,0);
				}
				$typ_search="look_AUTHOR";
			break;
			case 'categ_see':
				//Recherche de la categorie
				$categ_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select libelle_categorie from categories where num_noeud='".addslashes($categ_id)."'";
				$r_cat=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_cat)) {
					$valeur_champ=pmb_mysql_result($r_cat,0,0);
				}
				$typ_search="look_CATEGORY";
			break;
			case 'indexint_see':
				//Recherche de l'indexation
				$indexint_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select indexint_name from indexint where indexint_id='".addslashes($indexint_id)."'";
				$r_indexint=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_indexint)) {
					$valeur_champ=pmb_mysql_result($r_indexint,0,0);
				}
				$typ_search="look_INDEXINT";
			break;
			case 'coll_see':
				//Recherche de l'indexation
				$coll_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select collection_name from collections where collection_id='".addslashes($coll_id)."'";
				$r_coll=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_coll)) {
					$valeur_champ=pmb_mysql_result($r_coll,0,0);
				}
				$typ_search="look_COLLECTION";
			break;
			case 'publisher_see':
				//Recherche de l'editeur
				$publisher_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select ed_name from publishers where ed_id='".addslashes($publisher_id)."'";
				$r_pub=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_pub)) {
					$valeur_champ=pmb_mysql_result($r_pub,0,0);
				}
				$typ_search="look_PUBLISHER";
			break;
			case 'titre_uniforme_see':
				//Recherche de titre uniforme
				$tu_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select tu_name from titres_uniformes where ed_id='".addslashes($tu_id)."'";
				$r_tu=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_tu)) {
					$valeur_champ=pmb_mysql_result($r_tu,0,0);
				}
				$typ_search="look_TITRE_UNIFORME";
			break;
			case 'subcoll_see':
				//Recherche de l'editeur
				$subcoll_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select sub_coll_name from sub_collections where sub_coll_id='".addslashes($subcoll_id)."'";
				$r_subcoll=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_subcoll)) {
					$valeur_champ=pmb_mysql_result($r_subcoll,0,0);
				}
				$typ_search="look_SUBCOLLECTION";
			break;
			case 'authperso_see':
				$authpersos=authpersos::get_instance();
				$info=$authpersos->get_field_text($_SESSION["notice_view".$n]["search_id"]);
				$valeur_champ=$info['valeur_champ'];
				$typ_search=$info['typ_search'];
			break;
			case 'concept_see':
				$concept=new skos_concept($_SESSION["notice_view".$n]["search_id"]);
				$valeur_champ=$concept->get_display_label();
				$typ_search="look_CONCEPT";
			break;

		}
		return array($valeur_champ,$typ_search);
	}
	public static function get_search_form_map() {
		global $msg, $charset;
		global $current_module;
		global $pmb_indexation_docnum;
		global $pmb_indexation_docnum_allfields, $docnum_query;
		global $thesaurus_concepts_active;
		global $thesaurus_auto_postage_search, $auto_postage_query;
		global $all_query;
		global $categ_query;
		global $concept_query;
		global $map_echelle_query;
		global $map_projection_query;
		global $map_ref_query;
		global $map_equinoxe_query;

		$search_form_map = "
			<script src='javascript/ajax.js'></script>
			<script>
				function test_form(form) {
					if ((form.categ_query.value.length == 0) && (form.all_query.value.length == 0) && ((form.concept_query && form.concept_query.value.length == 0) || (!form.concept_query)) ) {
						//	form.all_query.value='*';
						return true;
					}
				}
			</script>
			<form class='form-$current_module' id='search_form_map' name='search_form_map' method='post' action='".static::format_url("lvl=search_result&search_type_asked=tags_search")."' onSubmit='return test_form(this)'>
			<div class='form-contenu'>
				<table class='map_search' role='presentation'><tr><td>
					<div class='row'>
						<label class='etiquette' for='all_query'>$msg[global_search]</label>
					</div>
					<div class='colonne'>
						<div class='row'>
							<input class='saisie-80em' type='text' value='".htmlentities(stripslashes($all_query),ENT_QUOTES, $charset)."' name='all_query' id='all_query' />
						</div>
					</div>";
		if($pmb_indexation_docnum){
			$search_form_map .= "
					<div class='colonne'>
						<div class='row'>
							<input type='checkbox' ".(($pmb_indexation_docnum_allfields || $docnum_query) ? 'checked' : '')." id='docnum_query' name='docnum_query'/><label for='docnum_query'>$msg[docnum_indexation]</label>
						</div>
					</div>";
		}
		$search_form_map .= "
					<div class='row'>
						<label class='etiquette' for='categ_query'>".$msg["search_categorie_title"]."</label>
					</div>
					<div class='colonne'>
						<div class='row'>
							<input class='saisie-80em' id='categ_query' type='text' value='".htmlentities(stripslashes($categ_query),ENT_QUOTES, $charset)."' size='36' name='categ_query' autfield='categ_query' completion='categories_mul' autocomplete='off' />
						</div>
					</div>
					";
		if($thesaurus_auto_postage_search){
			$search_form_map .= "
					<div class='colonne'>
						<div class='row'>
							<input type='checkbox' ".(($auto_postage_query) ? 'checked' : '')." id='auto_postage_query' name='auto_postage_query'/><label for='auto_postage_query'>".$msg["search_autopostage_check"]."</label>
						</div>
					</div>
					";
		}
		if($thesaurus_concepts_active){
			$search_form_map .= "
			<div class='row'>
				<label class='etiquette' for='concept_query'>".$msg["search_concept_title"]."</label>
			</div>
			<div class='colonne'>
				<div class='row'>
					<input class='saisie-80em' id='concept_query' type='text' value='".htmlentities(stripslashes($concept_query),ENT_QUOTES, $charset)."' size='36' name='concept_query' autfield='concept_query' completion='onto' autocomplete='off' att_id_filter='http://www.w3.org/2004/02/skos/core#Concept' />
				</div>
			</div>";
		}

		$search_form_map .= "
			<div class='row'>
				<label class='etiquette' for='map_echelle_query'>".$msg["map_echelle"]."</label>
			</div>
			<div class='row'>
				".gen_liste("SELECT map_echelle_id, map_echelle_name FROM map_echelles ORDER BY map_echelle_name ","map_echelle_id","map_echelle_name","map_echelle_query","",$map_echelle_query,0,"",0,$msg['map_echelle_vide'])."
			</div>
			<div class='row'>
				<label class='etiquette' for='map_projection_query'>".$msg["map_projection"]."</label>
			</div>
			<div class='row'>
				".gen_liste("SELECT map_projection_id, map_projection_name FROM map_projections ORDER BY map_projection_name ","map_projection_id","map_projection_name","map_projection_query","",$map_projection_query,0,"",0,$msg['map_projection_vide'])."
			</div>
			<div class='row'>
				<label class='etiquette' for='map_ref_query'>".$msg["map_ref"]."</label>
			</div>
			<div class='row'>
				".gen_liste("SELECT map_ref_id, map_ref_name FROM map_refs ORDER BY map_ref_name ","map_ref_id","map_ref_name","map_ref_query","",$map_ref_query,0,"",0,$msg['map_ref_vide'])."
			</div>
			<div class='row'>
				<label class='etiquette' for='map_equinoxe_query'>".$msg["map_equinoxe"]."</label>
			</div>
			<div class='row'>
				<input id='map_equinoxe_query' class='saisie-80em' type='text' value='".$map_equinoxe_query."' name='map_equinoxe_query'>
			</div>
			<div class='row'>
				<span class='saisie-contenu'>
					$msg[155]&nbsp;<a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
				</span>
			</div>
			<div class='colonne2'>
				<div class='row'>
					<label for='typdoc-query'>$msg[17]$msg[1901]</label>
				</div>
				<select id='typdoc-query' name='typdoc_query'>
					".static::get_options_typdoc_field()."
				</select>
			</div>
			<div class='colonne_suite'>
				<div class='row'>
					<label for='statut-query'>$msg[noti_statut_noti]</label>
				</div>
				<select id='statut-query' name='statut_query'>
					".static::get_options_status_field()."
				</select>
			</div>
		</td>
		<td>
			<div class='row'>
				<label class='etiquette'>".$msg["map_search"]."</label>
			</div>
			<div class='row'>
				".static::get_display_map()."
			</div>
		</td>
		</tr>
		</table>
		<div class='row'>&nbsp;</div>
		</div>
		<!--	Bouton Rechercher	-->
		<div class='row'>
			<input type='submit' class='bouton' value='$msg[142]' />
		</div>
		<input type='hidden' name='etat' value='first_search'/>
		</form>
		<script>
		document.forms['search_form_map'].elements['all_query'].focus();
		ajax_parse_dom();
		</script>";
		return $search_form_map;
	}

	public static function get_search_section_complement_isbd($id, $mode) {
		global $msg;

		$complement = '';
		$search_plettreaut = $_SESSION["last_module_search"]["search_plettreaut"];
		$search_dcote = $_SESSION["last_module_search"]["search_dcote"];
		$search_lcote = $_SESSION["last_module_search"]["search_lcote"];
		if($search_plettreaut){
			if($search_plettreaut == "num"){
				$complement=" > ".$msg["navigopac_aut_com_par_chiffre"];
			}elseif($search_plettreaut == "vide"){
				$complement=" > ".$msg["navigopac_ss_aut"];
			}else{
				$complement=" > ".$msg["navigopac_aut_com_par"]." ".$search_plettreaut;
			}
		}elseif($search_dcote || $search_lcote || $_SESSION["last_module_search"]["search_nc"] || $_SESSION["last_module_search"]["search_ssub"]){
			$requete="SELECT num_pclass FROM docsloc_section WHERE num_location='".$_SESSION["last_module_search"]["search_location"]."' AND num_section='".$id."' ";
			$res=pmb_mysql_query($requete);
			$type_aff_navigopac=0;
			if(pmb_mysql_num_rows($res)){
				$type_aff_navigopac=pmb_mysql_result($res,0,0);
			}
			if (strlen($search_dcote)) {
				if (!$_SESSION["last_module_search"]["search_ssub"]) {
					for ($i=0; $i<strlen($search_dcote); $i++) {
						$chemin="";
						$ccote=substr($search_dcote,0,$i+1);
						$ccote=$ccote.str_repeat("0",$search_lcote-$i-1);
						if ($i>0) {
							$cote_n_1=substr($search_dcote,0,$i);
							$compl_n_1=str_repeat("0",$search_lcote-$i);
							if (($ccote)==($cote_n_1.$compl_n_1)) $chemin=$msg["l_general"];
						}
						if (!$chemin) {
							$requete="select indexint_name,indexint_comment from indexint where indexint_name='".$ccote."' and num_pclass='".$type_aff_navigopac."'";
							$res_ch=pmb_mysql_query($requete);
							if (pmb_mysql_num_rows($res_ch))
								$chemin=pmb_mysql_result(pmb_mysql_query($requete),0,1);
								else
									$chemin=$msg["l_unclassified"];
						}
						$complement.=" > ".pmb_bidi($chemin);
					}
				} else {
					$t_dcote=explode(",",$search_dcote);
					$requete="select indexint_comment from indexint where indexint_name='".stripslashes($t_dcote[0])."' and num_pclass='".$type_aff_navigopac."'";
					$res_ch=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($res_ch))
						$chemin=pmb_mysql_result(pmb_mysql_query($requete),0,0);
						else
							$chemin=$msg["l_unclassified"];
							$complement=pmb_bidi(" > ".$chemin);
				}
			}
			if ($_SESSION["last_module_search"]["search_nc"]==1) {
				$complement=" > ".$msg["l_unclassified"];
			}
		}
		return $complement;
	}

	public static function get_display_search_tabs_form($value='', $css = "") {
		global $msg, $charset;
		global $css;
		global $es;
		global $lvl;
		global $include_path;
		global $id_thes;
		global $base_path;
		global $external_env;
		global $user_query;
		global $source;
		global $opac_term_search_height;
		global $onglet_persopac;
		global $search_in_perio;
		global $get_query;
		global $search_term, $term_click, $page_search;

		// pour la DSI
		global $opac_allow_bannette_priv ; // bannettes privees autorisees ?
		global $bt_cree_bannette_priv ;
		if ($opac_allow_bannette_priv && ($bt_cree_bannette_priv || (isset($_SESSION['abon_cree_bannette_priv']) && $_SESSION['abon_cree_bannette_priv']==1))) {
			$_SESSION['abon_cree_bannette_priv'] = 1 ;
		} else {
			$_SESSION['abon_cree_bannette_priv'] = 0 ;
		}
		global $bt_edit_bannette_priv ;
		if ($opac_allow_bannette_priv && ($bt_edit_bannette_priv || (isset($_SESSION['abon_edit_bannette_priv']) && $_SESSION['abon_edit_bannette_priv']==1))) {
			if($bt_edit_bannette_priv) {
				global $id_bannette;
				$_SESSION['abon_edit_bannette_id'] = $id_bannette;
				$_SESSION['abon_edit_bannette_priv'] = 1 ;
				$_SESSION['abon_edit_bannette_priv_visibility_until'] = (time()+180); //Validité 3 min
			}
		} else {
			$_SESSION['abon_edit_bannette_priv'] = 0 ;
			$_SESSION['abon_edit_bannette_id'] = 0;
		}

		global $script_test_form;

		switch (static::$search_type) {
			case "simple_search":
				// les tests de formulaire
				$result = $script_test_form;
				$tests = test_field("search_input", "query", "recherche");
				$result = str_replace("!!tests!!", $tests, $result);

				// le contenu
				static::set_user_query($value);
				$result .= static::get_display_search();

				// map
				$result = str_replace("!!map!!", static::get_display_map(),  $result);
				break;
				//Recherche avancee
			case "extended_search":
				$es=new search();
				global $mode_aff;
				if ($mode_aff) {
					if ($mode_aff=="aff_module") {
						//ajout de la recherche dans l'historique
						$_SESSION["nb_queries"] = intval($_SESSION["nb_queries"]) + 1;
						$n=$_SESSION["nb_queries"];
						$_SESSION["notice_view".$n]=$_SESSION["last_module_search"];
						$_SESSION["human_query".$n]=static::get_last_human_query();
						$_SESSION["search_type".$n]="module";
					} else {
						if ($_SESSION["last_query"]) {
							$n=$_SESSION["last_query"];
							if ($_SESSION["lq_facette"]) $facette=true;
						} else {
							$n=$_SESSION["nb_queries"];
						}
					}
					//générer les critères de la multi_critères
					//Attention ! si on est déjà dans une facette !
					if ($facette) {
					    $es->unserialize_search($_SESSION["lq_facette_search"]["lq_search"]);
					} else {
						global $search;
						if(empty($search)) {
							$search=array();
						}
						$search[0]="s_1";
						$op_="EQ";

						//operateur
						$op="op_0_".$search[0];
						global ${$op};
						${$op}=$op_;

						//contenu de la recherche
						$field="field_0_".$search[0];
						$field_=array();
						$field_[0]=$n;
						global ${$field};
						${$field}=$field_;

						//opérateur inter-champ
						$inter="inter_0_".$search[0];
						global ${$inter};
						${$inter}="";

						//variables auxiliaires
						$fieldvar_="fieldvar_0_".$search[0];
						global ${$fieldvar_};
						${$fieldvar_}="";
						$fieldvar=${$fieldvar_};
					}
				}

				if($search_in_perio){
					global $search;
					if(empty($search)) {
						$search=array();
					}
					$search[0]="f_34";
					//opérateur
					$op="op_0_".$search[0];
					global ${$op};
					$op_ ="EQ";
					${$op}=$op_;
					//contenu de la recherche
					$field="field_0_".$search[0];
					$field_=array();
					$field_[0]=$search_in_perio;
					global ${$field};
					${$field}=$field_;

					$search[1]="f_42";
					//opérateur
					$op="op_1_".$search[0];
					global ${$op};
					$op_ ="BOOLEAN";
					${$op}=$op_;
				} else {
					if ($get_query) {
						if (($_SESSION["last_query"]==$get_query)&&(!empty($_SESSION["lq_facette_test"]))) {
							$es->unserialize_search($_SESSION["lq_facette_search"]["lq_search"]);
						} else get_history($get_query);
					}
				}
				if($onglet_persopac){
					global $search;
					if (empty($search) && ($_GET['onglet_persopac'] || $_SERVER['REQUEST_METHOD'] == "GET")) {
						//On ne charge les champs de la prédéfinie que si l'on vient de cliquer sur le lien
						//EDIT 13/12/17 - AR : ou si on y accède pas via un formulaire (utilisation du paramètres first_page_params)
						$search_p_direct= new search_persopac($onglet_persopac);
						$es->unserialize_search($search_p_direct->query);
					}
				}
				if (($onglet_persopac)&&($lvl=="search_result")) $es->reduct_search();

				$result = static::get_display_search();
				break;
				//Recherche avancee
			case "external_search":
				//Si c'est une multi-critere, on l'affiche telle quelle
				global $external_type;
				if ($external_type) $_SESSION["ext_type"]=$external_type;
				global $mode_aff;
				//Affinage
				if ($mode_aff) {
					if ($mode_aff=="aff_module") {
						//ajout de la recherche dans l'historique
						$_SESSION["nb_queries"]=$_SESSION["nb_queries"]+1;
						$n=$_SESSION["nb_queries"];
						$_SESSION["notice_view".$n]=$_SESSION["last_module_search"];
						$_SESSION["human_query".$n]=static::get_last_human_query();
						$_SESSION["search_type".$n]="module";
					} else {
						if ($_SESSION["last_query"]) {
							$n=$_SESSION["last_query"];
						} else {
							$n=$_SESSION["nb_queries"];
						}
					}
				}

				if ($_SESSION["ext_type"]=="multi") {
					global $search;

					if (!$search) {
						$search=array();
						$search[0]="s_2";
						global $op_0_s_2;
						$op_0_s_2="EQ";
						global $field_0_s_2;
						$field_0_s_2=array();
					} else {
						//Recherche du champp source, s'il n'est pas present, on decale tout et on l'ajoute
						$flag_found=false;
						for ($i=0; $i<count($search); $i++) {
							if ($search[$i]=="s_2") { $flag_found=true; break; }
						}
						if (!$flag_found) {
							//Pas trouve, on decale tout !!
							for ($i=count($search)-1; $i>=0; $i--) {
								$search[$i+1]=$search[$i];
								decale("field_".$i."_".$search[$i],"field_".($i+1)."_".$search[$i]);
								decale("op_".$i."_".$search[$i],"op_".($i+1)."_".$search[$i]);
								decale("inter_".$i."_".$search[$i],"inter_".($i+1)."_".$search[$i]);
								decale("fieldvar_".$i."_".$search[$i],"fieldvar_".($i+1)."_".$search[$i]);
							}
							$search[0]="s_2";
							global $op_0_s_2;
							$op_0_s_2="EQ";
							global $field_0_s_2;
							$field_0_s_2=array();
						}
					}
					if ($mode_aff) {
						//générer les critères de la multi_critères
						$search[1]="s_1";
						$op_="EQ";

						//opérateur
						$op="op_1_".$search[1];
						global ${$op};
						${$op}=$op_;

						//contenu de la recherche
						$field="field_1_".$search[1];
						$field_=array();
						$field_[0]=$n;
						global ${$field};
						${$field}=$field_;

						//opérateur inter-champ
						$inter="inter_1_".$search[1];
						global ${$inter};
						${$inter}="and";

						//variables auxiliaires
						$fieldvar_="fieldvar_1_".$search[1];
						global ${$fieldvar_};
						${$fieldvar_}="";
						$fieldvar=${$fieldvar_};
					}
					$es = new search("search_fields_unimarc");
					$result = static::get_display_search();
				} else {
					global $mode_aff;
					//Si il y a une mode d'affichage demandé, on construit l'écran correspondant
					if ($mode_aff) {
						$f=static::get_field_text($n);
						$user_query=$f[0];
						$look=$f[1];
						global ${$look};
						${$look}=1;
						global $look_FIRSTACCESS;
						$look_FIRSTACCESS=1;
					} else {
						if ($external_env) {
							$external_env=unserialize(stripslashes($external_env));
							foreach ($external_env as $varname=>$varvalue) {
								global ${$varname};
								${$varname}=$varvalue;
							}
						}
					}
					static::set_user_query(stripslashes($user_query));
					$result=static::get_display_search();
					$result = str_replace("<!--!!sources!!-->", do_sources(), $result);
				}
				break;
				//Recherche par termes
			case "term_search":
				$result .= static::get_display_search();

				if (!$opac_term_search_height) {
				    $height=300;
				} else {
				    $height=$opac_term_search_height;
				}
				$result.="
					<a name='search_frame'/>
					<iframe style='border: solid 1px black; width : 100%; height : $height;' name='term_search' id='frame_term_search' class='frame_term_search' src='".$base_path."/term_browse.php?search_term=".rawurlencode(stripslashes($search_term ?? ""))."&term_click=".rawurlencode(stripslashes($term_click ?? ""))."&page_search=$page_search&id_thes=$id_thes' title='".htmlentities($msg['term_search_search_for'], ENT_QUOTES, $charset)."'></iframe>
					<br /><br />";
				break;
			case "tags_search":
				// les tests de formulaire
				$result = $script_test_form;
				$tests = test_field("search_input", "query", "recherche");
				$result = str_replace("!!tests!!", $tests, $result);

				// le contenu
				static::set_user_query($value);
				$result .= static::get_display_search();

				// Ajout de la liste des tags
				if($user_query=="") {
					$result.= "<h3><span>$msg[search_result_for]<b>".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."</b></span></h3>";
					$tag = new tags();
					$result.=  $tag->listeAlphabetique();
				}
				break;
			case "connect_empr":
			case "search_perso":
			case "perio_a2z":
			case "map":
				$result = static::get_display_search();
				break;
			case "extended_search_authorities":
				$es=new search_authorities("search_fields_authorities");
				global $mode_aff;
				if ($mode_aff) {
					if ($mode_aff=="aff_module") {
						//ajout de la recherche dans l'historique
						$_SESSION["nb_queries"]=$_SESSION["nb_queries"]+1;
						$n=$_SESSION["nb_queries"];
						$_SESSION["notice_view".$n]=$_SESSION["last_module_search"];
						$_SESSION["human_query".$n]=static::get_last_human_query();
						$_SESSION["search_type".$n]="module";
					} else {
						if ($_SESSION["last_query"]) {
							$n=$_SESSION["last_query"];
							if ($_SESSION["lq_facette"]) $facette=true;
						} else {
							$n=$_SESSION["nb_queries"];
						}
					}
					//générer les critères de la multi_critères
					//Attention ! si on est déjà dans une facette !
					if ($facette)
						$es->unserialize_search($_SESSION["lq_facette_search"]["lq_search"]);
						else {
							global $search;
							$search[0]="s_1";
							$op_="EQ";

							//operateur
							$op="op_0_".$search[0];
							global ${$op};
							${$op}=$op_;

							//contenu de la recherche
							$field="field_0_".$search[0];
							$field_=array();
							$field_[0]=$n;
							global ${$field};
							${$field}=$field_;

							//opérateur inter-champ
							$inter="inter_0_".$search[0];
							global ${$inter};
							${$inter}="";

							//variables auxiliaires
							$fieldvar_="fieldvar_0_".$search[0];
							global ${$fieldvar_};
							${$fieldvar_}="";
							$fieldvar=${$fieldvar_};
						}
				}

				if($search_in_perio){
					global $search;
					if(empty($search)) {
						$search=array();
					}
					$search[0]="f_34";
					//opérateur
					$op="op_0_".$search[0];
					global ${$op};
					$op_ ="EQ";
					${$op}=$op_;
					//contenu de la recherche
					$field="field_0_".$search[0];
					$field_=array();
					$field_[0]=$search_in_perio;
					global ${$field};
					${$field}=$field_;

					$search[1]="f_42";
					//opérateur
					$op="op_1_".$search[0];
					global ${$op};
					$op_ ="BOOLEAN";
					${$op}=$op_;
				} else {
					if ($get_query) {
						if (($_SESSION["last_query"]==$get_query)&&($_SESSION["lq_facette_test"])) {
							$es->unserialize_search($_SESSION["lq_facette_search"]["lq_search"]);
						} else get_history($get_query);
					}
				}
				if($onglet_persopac){
					global $search;
					if (!$search && ($_GET['onglet_persopac'] || $_SERVER['REQUEST_METHOD'] == "GET")) {
						//On ne charge les champs de la prédéfinie que si l'on vient de cliquer sur le lien
						//EDIT 13/12/17 - AR : ou si on y accède pas via un formulaire (utilisation du paramètres first_page_params)
						$search_p_direct= new search_persopac($onglet_persopac);
						$es->unserialize_search($search_p_direct->query);
					}
				}
				if (($onglet_persopac)&&($lvl=="search_result")) $es->reduct_search();

				$result = static::get_display_search();
				break;
			case "ai_search":
				$result = static::get_display_search();
				break;
		}
		return $result;
	}

	public static function format_url($url) {
		if(strpos(static::$url_base, "lvl=search_segment")) {
			return static::$url_base.str_replace('lvl', 'action', $url);
		} else {
			return static::$url_base.$url;
		}
	}

	public static function set_user_query($user_query) {
		static::$user_query = $user_query;
	}

	public static function get_display_search_universe_form() {
	    global $msg, $charset;
	    global $opac_autolevel2;
	    global $include_path;
	    global $base_path;
	    global $opac_map_activate;
	    global $opac_focus_user_query;
	    global $opac_simple_search_suggestions;

	    $form = "
		<form name='search_input' action='".($opac_autolevel2 ? static::format_url("lvl=more_results&autolevel1=1") : static::format_url("lvl=search_result"))."' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">
			".static::get_typdoc_field()."
			<br />
			<input type='hidden' name='surligne' value='!!surligne!!'/>";
        $form .= "
				<input type='text' name='user_query' class='text_query' value=\"".static::$user_query."\" size='65' title='".htmlentities($msg['autolevel1_search'], ENT_QUOTES, $charset)."' />\n";
	    $form .= "
				<input type='submit' name='ok' value='".$msg["142"]."' class='boutonrechercher'/>\n";

	    if($opac_map_activate==1 || $opac_map_activate==2) {
	        $form .= "
				<div class='row'>
					<label class='etiquette'>".$msg["map_search"]."</label>
				</div>
				<div class='row'>
					!!map!!
				</div>";
	    }
	    $form .= "</form>
		<script src='".$include_path."/javascript/ajax.js'></script>
		<script>\n
			if (document.forms['search_input'] && document.forms['search_input'].elements['user_query']) {
				".($opac_focus_user_query ? 'document.forms["search_input"].elements["user_query"].focus();' : '')."
				".($opac_simple_search_suggestions ? 'ajax_pack_element(document.forms["search_input"].elements["user_query"]);' : '')."
			}
		</script>";
	    return $form;
	}
	private static function get_autocomplete_input($html = "")
	{
		global $msg, $charset;
	    $searchView = new SearchAutocompleteView("searchform/searchautocomplete", [
	        "input_id" => "user_query_lib",
			"input_name" => "user_query",
	        "input_value" => htmlentities(stripslashes(static::$user_query),ENT_QUOTES,$charset),
	        "input_class" => "text_query",
	        "input_size" => "65",
	        "input_placeholder" => $msg["autolevel1_search"],
	        "show_entities" => 1,
			"form_id" => "search_input",
			"html" => $html
	    ]);
	    return $searchView->render();
	}

	protected static function get_search_form_ai_search() {
		global $opac_url_base, $msg, $get_query, $user_query;
		global $ai_allow_semantic_search, $ai_active;

		$aiSettings = AISettingsOrm::getAiSettingActive();
		if(empty($aiSettings)) {
			return "";
		}

		if ($ai_active) {
			if ($ai_allow_semantic_search && !EmprOrm::exist(intval($_SESSION["id_empr_session"]))) {
				http_response_code(403);
				header("Location: " . $opac_url_base . "index.php");
				exit;
			}
		}

		if ($get_query) {
			AiSessionSemanticModel::get_history($get_query);
		}

		global $retry_query, $wait;
		if (
            !$wait &&
			$retry_query &&
			!empty($_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]) &&
			true === $_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]["retry"]
		) {
            // On a eu une 429, on rejoue une recherche. Donc on redefinit la user_query
			// !! Attention, ici $wait ne doit pas être defini. (Si c'est le cas, c'est l'affichage d'attente)
			unset($_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]["retry"]);
			$user_query = $_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]["user_query"];
		}

		$fetch_text_generation = false;
		if ($user_query && !$get_query) {
			$fetch_text_generation = true;
		}

		// Ne pas mettre ces globals au debut de la fonction,
		// car elle peut être redéfini dans "AiSessionSemanticModel::get_history"
		global $ai_session, $ai_session_index_question;
		if ($ai_session && !AiSessionSemanticModel::exist($ai_session)) {
			$ai_session = 0;
		}

		$iaView = new AiView("ai/search", [
            "webservice_url" => $opac_url_base . "rest.php/aiapi/",
            "welcome_message" => sprintf($msg["ia_search_welcome"], $aiSettings->settings_ai_settings->name),
            "ai_session" => $ai_session ?? null,
            "ai_session_index_question" => $ai_session_index_question ?? null,
			"fetch_text_generation" => $fetch_text_generation
        ]);
		return $iaView->render();
	}
}