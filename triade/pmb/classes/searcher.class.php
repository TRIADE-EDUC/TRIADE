<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher.class.php,v 1.240.2.3.2.8 2025/05/22 08:51:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Common\Library\Navbar\Navbar;

//Classe de recherche en catalogage...

global $class_path, $include_path, $pmb_map_activate;
global $gestion_acces_active, $gestion_acces_user_notice, $aut_type, $PMBuserid;

require_once("$class_path/analyse_query.class.php");
require_once("$class_path/thesaurus.class.php");
require_once("$class_path/sort.class.php");
require_once("$include_path/templates/searcher_templates.tpl.php");
require_once($class_path."/searcher/searcher_records_title.class.php");
require_once($class_path."/searcher/searcher_authorities_authors.class.php");
require_once($class_path."/authority.class.php");
if(isset($pmb_map_activate) && $pmb_map_activate){
	require_once($class_path."/map/map_search_controler.class.php");
}

require_once($class_path."/searcher/searcher_factory.class.php");
require_once($class_path.'/elements_list/elements_records_list_ui.class.php');
require_once($class_path.'/record_display.class.php');

//droits d'acces lecture notice
$acces_j='';
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	if(!isset($aut_type)) $aut_type = '';
	switch ($aut_type) {
		case "concept" :
			$acces_j = $dom_1->getJoin($PMBuserid,4,'num_object');
			break;
		default :
			$acces_j = $dom_1->getJoin($PMBuserid,4,'notice_id');
			break;
	}
}


//Classe générique de recherche

define("AUT_LIST",1);
define("NOTICE_LIST",2);
define("AUT_SEARCH",3);

class searcher {

public $type;                          //Type de recherche
	public $etat;                      //Etat de la recherche
	public $page = 0;                  //Page courante de la recherche
	public $nbresults;                 //Nombre de résultats de la dernière recherche
	public $nbepage;
	public $nb_per_page;
	public $id = 0;                    //Numéro d'autorité pour la recherche
	public $store_form;                //Formulaire contenant les infos de navigation plus des champs pour la recherche
	public $base_url;
	public $first_search_result;
	public $text_query;
    public $text_query_tri;            //pour les tris texte de la requête d'origine modifié par la classe tri
	public $human_title;
	public $human_query;
	public $human_notice_query;
	public $human_aut_query;
	public $docnum;
	public $direct=0;
	public $rec_history=false;
	public $sort;
	public $current_search;
	public $auto_postage_query;

	//Constructeur
	public function __construct($base_url,$rec_history=false) {
		global $type, $etat, $aut_id, $page, $docnum_query, $auto_postage_query;

		$this->sort = new sort('notices','base');
		$this->type=$type;
		$this->etat=$etat;
		$this->page = intval($page);
		$this->id = intval($aut_id);
		$this->base_url=$base_url;
		$this->rec_history=$rec_history;
		$this->docnum = ($docnum_query?1:0);
		$this->auto_postage_query = ($auto_postage_query?1:0);
		$this->run();
	}

	public function make_store_form() {
		$this->store_form="<form name='store_search' action='".$this->base_url."' method='post' style='display:none'>
		<input type='hidden' name='type' value='".$this->type."'/>
		<input type='hidden' name='etat' value='".$this->etat."'/>
		<input type='hidden' name='page' value='".$this->page."'/>";
		$this->store_form.="!!first_search_variables!!";
		$this->store_form.="</form>";
	}

	public function pager() {
		if (!$this->nbresults) return;
		if (!$this->page) {
		    $current_page = 1 ;
		} else {
		    $current_page = $this->page;
		}
		$navbar = new Navbar($current_page, $this->nbresults, $this->nb_per_page);
		$navbar->setHiddenFormName('store_search');
		// affichage de la barre de navigation
		print "<div id='results_pager' class='center'>".$navbar->render()."</div>";
	}

	public function show_notice() {
	}


	public function run() {
		global $pmb_map_activate;
		if (!$this->etat) {
				$this->show_form();
		} else {
				switch ($this->etat) {
					case "first_search":
						$r=$this->make_first_search();
						//echo "req first:".$this->text_query."<br />";
						$this->first_search_result=$r;
						switch ($r) {
								case AUT_LIST:
									$this->make_store_form();
									$this->store_search();
									$this->aut_list();
									$this->pager();
									break;
								case NOTICE_LIST:
									$this->make_store_form();
									$this->store_search();
									$this->sort_notices();
									$this->notice_list();
									$this->pager();
									break;
								case AUT_SEARCH:
									$this->etat="aut_search";
									$this->direct=1;
									$this->make_aut_search();
									$this->make_store_form();
									$this->aut_store_search();
									$this->sort_notices();
									$this->aut_notice_list();
									$this->pager();
									break;
						}
						if ($this->rec_history){
							$this->rec_env();
							if($pmb_map_activate){
								$this->check_emprises();
							}

						}
						break;
					case "aut_search":
						$this->make_aut_search();
						$this->make_store_form();
						$this->aut_store_search();
						$this->sort_notices();
						$this->aut_notice_list();
						$this->pager();
						if ($this->rec_history){
							$this->rec_env();
							if($pmb_map_activate){
								$this->check_emprises();
							}
						}
						break;
				}
		}
	}


	public function show_form() {
		//A surcharger par la fonction qui affiche le formulaire de recherche
	}

	public function make_first_search() {
		//A surcharger par la fonction qui fait la première recherche après la soumission du formulaire de recherche
		//La fonction renvoie AUT_LIST (le résultat de la recherche est une liste d'autorité)
		//ou NOTICE_LIST (le résultat de la recherche est une liste de notices)
		//La fonction doit mettre à jour le nombre de résultats dans $this->nbresults
	}

	public function make_aut_search() {
		//A surcharger par la fonction qui fait la recherche des notices à partir d'un numéro d'autorité (stoqué dans $this->id)
		//La fonction doit mettre à jour le nombre de résultats dans $this->nbresults
	}

	public function store_search() {
		//A surcharger par la fonction qui écrit les variables du formulaire "store_search" pour stoquer les champs de recherche
		//En liste de résultat de la première recherche. Il faut remplacer la chaine "!!first_search_variables!!" dans $this->store_form
	}

	public function aut_store_search() {
		//A surcharger par la fonction qui écrit les variables du formulaire "store_search" pour stoquer les champs de recherche
		//En liste de résultat de la première recherche. Il faut remplacer la chaine "!!first_search_variables!!" dans $this->store_form
	}

	public function aut_list() {
		//A surcharger par la fonction qui affiche la liste des autorités issues de la première recherche
	}

	protected function get_display_icon_sort() {
		global $msg;
		global $pmb_nb_max_tri;

		$display = '';
		// on affiche l'icone de tri seulement si on a atteint un nb maxi de résultats
		if ($this->nbresults<=$pmb_nb_max_tri) {
			//affichage de l'icone de tri
			$display .= "<a href=# onClick=\"document.getElementById('history').src='./sort.php?type_tri=notices'; document.getElementById('history').style.display='';return false;\" ";
			$display .= "alt=\"".$msg['tris_dispos']."\" title=\"".$msg['tris_dispos']."\">";
			$display .= "<img src='".get_url_icon('orderby_az.gif')."' class='align_middle' style='margin:0px 3px'></a>";
			//si on a un tri actif on affiche sa description
			if ($_SESSION["tri"]) {
				$display .= $msg['tri_par']." ".$this->sort->descriptionTriParId($_SESSION["tri"]);
			}
		}
		return $display;
	}

	protected function get_display_icons($current, $from_mode=0) {
		global $msg;
		global $pmb_allow_external_search;
		$display = '';
		$display .= "<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare','print',600,700,-2,-2,'scrollbars=yes,menubar=0,resizable=yes'); return false;\">";
		$display .= "<img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>";
		$tri_id_info = $_SESSION["tri"] ? "&sort_id=".$_SESSION["tri"] : "";
		$display .= "<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare".$tri_id_info."','print'); return false;\">";
		$display .= "<img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
		$display .= "<a href='#' onClick=\"openPopUp('./download.php?current_download=$current&action_download=download_prepare".$tri_id_info."','download'); return false;\">";
		$display .= "<img src='".get_url_icon('upload_docnum.gif')."' style='border:0px' class='center' alt=\"".$msg["docnum_download"]."\" title=\"".$msg["docnum_download"]."\"/></a>";
		if ($pmb_allow_external_search) {
			$display .= "<a href='catalog.php?categ=search&mode=7&from_mode=".$from_mode."&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>";
			$display .= "<img src='".get_url_icon('external_search.png')."' style='border:0px' class='center' alt=\"".$msg["connecteurs_external_search_sources"]."\"/></a>";
		}
		$display .= $this->get_display_icon_sort();
		$display .= self::get_quick_actions();
		$display .= self::get_search_back_button($this->base_url);
		$display .= self::get_check_uncheck_all_buttons();
		return $display;
	}

	public static function get_check_uncheck_all_buttons() {
		global $msg, $charset;
		$display = "<br/><input type='button' onclick='checkAllObjects(\"check\",\"objects_selection\")' class='bouton' value='".htmlentities($msg["tout_cocher_checkbox"],ENT_QUOTES,$charset)."' name='check_all'/>
					<input type='button' onclick='checkAllObjects(\"uncheck\",\"objects_selection\")' class='bouton' value='".htmlentities($msg["tout_decocher_checkbox"],ENT_QUOTES,$charset)."' name='check_all'/>";
		return $display;
	}

	public static function get_search_back_button($url, $type = 'NOTI') {
	    global $msg;

	    switch ($type) {
	        case 'AUT':
	            $form_variable = 'action';
	            break;
	        case 'NOTI':
	        case 'EXPL':
	        default:
	            $form_variable = 'etat';
	            break;
	    }

        return "<br /><input value=\"".$msg["search_back"]."\" type='button' class='bouton' onClick=\"document.store_search.action='$url'; document.store_search.$form_variable.value=''; document.store_search.submit(); return false;\" />";
	}

	public static function get_quick_actions($type = 'NOTI') {
		global $msg;
		$actions_to_remove = array(
		        'transfert' => true,
				'edit_cart' => true,
				'supprpanier' => true
		);
		switch ($type) {
			case 'AUT':
				$array_actions = authorities_caddie::get_array_actions("!!id_caddie!!", "AUT", $actions_to_remove);
				$module = "autorites";
				break;
			case 'NOTI':
			case 'EXPL':
			default:
				$array_actions = caddie::get_array_actions("!!id_caddie!!", $type, $actions_to_remove);
				$module = "catalog";
				break;
		}
		$lines = '
					<div data-dojo-type="dijit/form/DropDownButton" class="tooltip">
  						<span class="tooltiptext">'.$msg["caddie_shortaction_tooltip_title"].'</span>
						<span>'.$msg["caddie_menu_action"].'</span>
						<div data-dojo-type="dijit/DropDownMenu">';
		if(is_array($array_actions) && count($array_actions)){
			foreach($array_actions as $item_action){
				$lines .= '
							<div data-dojo-type="dijit/MenuItem" data-dojo-props="onClick:function(){quickActionsEvent(\''.urlencode($item_action["location"]).'\');}">
								<span>'.$item_action['msg'].'</span>
							</div>';
			}
		}
		$lines .= '</div>
				</div>
				<script>
					function quickActionsEvent(callback) {
						var list = [];
						var elements = document.querySelectorAll("input[name=\'objects_selection\']");
						elements.forEach(function(element) {
							if (element.checked) {
								list.push(element.value);
							}
						})
		                if (list.length == 0) {
		                    if (window.confirm("'.$msg["caddie_shortaction_confirmation"].'")) {
		                        document.location = "./'.$module.'.php?categ=caddie&sub=remplir&type='.$type.'&callback="+ callback +"&elements="+list.join(\',\');
		                    }
	                    } else {
		                    document.location = "./'.$module.'.php?categ=caddie&sub=remplir&type='.$type.'&callback="+ callback +"&elements="+list.join(\',\');
		                }
					}
				</script>';
		return $lines;
	}

	protected function get_display_records_list() {
		$records = array();
		while($nz = pmb_mysql_fetch_object($this->t_query)) {
			$records[] = $nz->notice_id;
		}
		$elements_records_list_ui = new elements_records_list_ui($records, count($records), false);
		$elements_records_list_ui->add_context_parameter('in_search', '1');

		//Réinitialisation des facettes
		facettes::destroy_global_env();

		facettes::set_search_mode('simple_search');

		if (count($records) < $this->nbresults && pmb_strpos($this->text_query, 'limit') !== false) {
		    //on recalcule le tableau de records pour les facettes
		    $records = array();
		    $query = pmb_substr($this->text_query, 0, pmb_strpos($this->text_query, 'limit'));
		    $result = pmb_mysql_query($query);
		    while($nz = pmb_mysql_fetch_object($result)) {
		        $records[] = $nz->notice_id;
		    }
		}
		session::set_value('search', ['notices' => ['simple_search' => implode(',', $records)]]);
		session::set_value('filtered_search', ['notices' => ['simple_search' => ""]]);
		facettes::set_facet_type('notices');
		facettes::set_hidden_form_name('store_search');
		facettes::set_elements_list_nb_per_page($this->nb_per_page);
		return "
        <div class='content_details'>
    		<div id='facettes_list' class='facettes_list'>".facettes::call_ajax_facettes()."</div>
    		<div id='results_list' class='results_list'>
                ".$elements_records_list_ui->get_elements_list()."
    		</div>
        </div>";
	}

	public function notice_list_common($title) {

	}

	public function notice_list() {
		//A surcharger par la fonction qui affiche la liste des notices issues de la première recherche
	}

	public function aut_notice_list() {
		//A surcharger par la fonction qui affiche la liste des notice sous l'autorité $this->id
	}

	public function rec_env() {
		//A surcharger pa la fonction qui enregistre
	}

	public static function convert_simple_multi($id_champ) {
		//A surcharger par la fonction qui convertit des recherches simples en multi-critères
	}

	public function sort_notices() {
		global $pmb_nb_max_tri;

		if ($this->nbresults<=$pmb_nb_max_tri) {
            $tri = !empty($_SESSION["tri"]) ? $_SESSION["tri"] : "default";
			if ($this->nb_per_page) {
				$this->text_query_tri = $this->sort->appliquer_tri($tri,$this->text_query,"notice_id", $this->get_start_page(), $this->nb_per_page);
			} else {
				$this->text_query_tri = $this->sort->appliquer_tri($tri,$this->text_query,"notice_id",0,0);
			}
			$this->t_query = @pmb_mysql_query($this->text_query_tri);

			if (!$this->t_query) {
				print pmb_mysql_error();
			}
		} else {
			if (strpos($this->text_query,"limit")===false) {
				if ($this->nb_per_page) {
					$this->text_query .= "limit ".$this->get_start_page().",".$this->nb_per_page;
				}
			} else {
				if ($this->nb_per_page) {
				    $this->text_query = str_replace("limit 0,".$this->nbresults,"limit ".$this->get_start_page().",".$this->nb_per_page,$this->text_query);
                }
			}
			$this->t_query = @pmb_mysql_query($this->text_query);
		}
	}

	public function get_current_search_map($mode_search=0){
		global $pmb_map_activate;
		global $page;
		global $aut_id;
		$map = "";
		if($pmb_map_activate){
			if(isset($_SESSION["MAP_CURRENT"])){
				$this->current_search=$_SESSION["MAP_CURRENT"];
				unset($_SESSION["MAP_CURRENT"]);
			}else{
			    if (isset($_SESSION['session_history'])) {
    				$this->current_search=count($_SESSION['session_history']);
    				switch($mode_search) {
    	  				case 2 :
    	  				case 3 :
    	  				case 9 :
    	  					$this->current_search--;
    				}
    				if(!empty($aut_id))$this->current_search--;
    				if(empty($aut_id) && isset($page)) $this->current_search--;
			    }
			}
			if($this->current_search<=0) $this->current_search = 0;
			$map = "<div id='map_container'><div id='map_search' ></div></div>";
		}
		return $map;
	}

	public function check_emprises(){
		global $pmb_map_activate;
		global $pmb_map_max_holds;
		global $pmb_map_size_search_result;

		$map = "";
		$size=explode("*",$pmb_map_size_search_result);
		if(count($size)!=2) {
			$map_size="width:800px; height:480px;";
		} else {
			if (is_numeric($size[0])) $size[0].= 'px';
			if (is_numeric($size[1])) $size[1].= 'px';
			$map_size= "width:".$size[0]."; height:".$size[1].";";
		}

		$map_search_controler = new map_search_controler(null, $this->current_search, $pmb_map_max_holds,false);
		$json = $map_search_controler->get_json_informations();
		//Obligatoire pour supprimer les {}
		$json = substr($json, 1, strlen($json)-2);
		if($map_search_controler->have_results()){
			$map.= "<script type='text/javascript'>
						require(['dojo/ready', 'dojo/dom-attr', 'dojo/parser', 'dojo/dom'], function(ready, domAttr, parser, dom){
							ready(function(){
								domAttr.set('map_search', 'data-dojo-type', 'apps/map/map_controler');
								domAttr.set('map_search', 'data-dojo-props','searchId: ".$this->current_search.", mode:\"search_result\", ".$json."');
								domAttr.set('map_search', 'style', '$map_size');
								parser.parse('map_container');
							});
						});
			</script>";
		}else{
			$map.= "<script type='text/javascript'>
						require(['dojo/ready', 'dojo/dom-construct'], function(ready, domConstruct){
							ready(function(){
								domConstruct.destroy('map_container');
							});
						});
			</script>";
		}
		print $map;
	}

	public function show_error($car,$input,$error_message) {
		global $browser_url;
		global $browser,$search_form_editeur;
		global $msg;
		$search_form_editeur=str_replace("!!base_url!!",$this->base_url,$search_form_editeur);
		print $search_form_editeur;
		error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$car,$input,$error_message));
		$browser=str_replace("!!browser_url!!",$browser_url,$browser);
		print $browser;
	}

	public function is_first_page() {
	    if($this->page == 0 || $this->page == "") {
	        return true;
	    }
	    return false;
	}

	public function get_start_page() {
	    if ($this->page) {
	        return ($this->page-1)*$this->nb_per_page;
	    }
	    return 0;
	}
}


class searcher_title extends searcher {
	public $t_query;
	public $sorted_result;

	public function show_form() {
		global $msg;
		global $charset;
		global $NOTICE_author_query;
		global $all_query, $docnum_query, $pmb_indexation_docnum_allfields, $pmb_indexation_docnum;
		global $title_query;
		global $author_query, $author_query_id;
		global $categ_query, $categ_query_id, $thesaurus_auto_postage_search, $auto_postage_query;
		global $thesaurus_concepts_active, $concept_query, $concept_query_id;
		global $ex_query,$typdoc_query, $statut_query;
		global $date_parution_start_query, $date_parution_end_query, $date_parution_exact_query;
		global $thesaurus_concepts_autopostage, $concepts_autopostage_query;

		// on commence par créer le champ de sélection de document
		// récupération des types de documents utilisés.
		$query = "SELECT count(typdoc), typdoc ";
		$query .= "FROM notices where typdoc!='' GROUP BY typdoc";
		$result = pmb_mysql_query($query);
		$toprint_typdocfield = "  <option value='' ".(empty($typdoc_query) || empty($typdoc_query[0]) ? 'selected' : '').">$msg[tous_types_docs]</option>\n";
		$doctype = new marc_list('doctype');
		$obj = array();
		$qte = array();
		while (($rt = pmb_mysql_fetch_row($result))) {
			$obj[$rt[1]]=1;
			$qte[$rt[1]]=$rt[0];
		}
		foreach ($doctype->table as $key=>$libelle){
			if (isset($obj[$key]) && ($obj[$key]==1)){
				$toprint_typdocfield .= "  <option ";
				$toprint_typdocfield .= " value='$key'";
				if (!empty($typdoc_query) && in_array($key, $typdoc_query)) $toprint_typdocfield .=" selected='selected' ";
				$toprint_typdocfield .= ">".htmlentities($libelle." (".$qte[$key].")",ENT_QUOTES, $charset)."</option>\n";
			}
		}

		// récupération des statuts de documents utilisés.
		$query = "SELECT count(statut), id_notice_statut, gestion_libelle ";
		$query .= "FROM notices, notice_statut where id_notice_statut=statut GROUP BY id_notice_statut order by gestion_libelle";
		$result = pmb_mysql_query($query);
		$toprint_statutfield = "  <option value='' ".(empty($statut_query) || empty($statut_query[0]) ? 'selected' : '').">$msg[tous_statuts_notice]</option>\n";
		while ($obj = @pmb_mysql_fetch_row($result)) {
				$toprint_statutfield .= "  <option value='$obj[1]'";
				if (!empty($statut_query) && in_array($obj[1], $statut_query)) $toprint_statutfield.=" selected";
				$toprint_statutfield .=">".htmlentities($obj[2]."  (".$obj[0].")",ENT_QUOTES, $charset)."</OPTION>\n";
		}

		$NOTICE_author_query = str_replace("!!typdocfield!!", $toprint_typdocfield, $NOTICE_author_query);
		$NOTICE_author_query = str_replace("!!statutfield!!", $toprint_statutfield, $NOTICE_author_query);
		$NOTICE_author_query = str_replace("!!title_query!!",  htmlentities(stripslashes($title_query ?? ""),ENT_QUOTES, $charset),  $NOTICE_author_query);
		$NOTICE_author_query = str_replace("!!all_query!!", htmlentities(stripslashes($all_query ?? ""),ENT_QUOTES, $charset),  $NOTICE_author_query);
		$NOTICE_author_query = str_replace("!!author_query!!", htmlentities(stripslashes($author_query ?? ""),ENT_QUOTES, $charset),  $NOTICE_author_query);
		$NOTICE_author_query = str_replace("!!author_query_id!!", htmlentities(stripslashes($author_query_id ?? ""),ENT_QUOTES, $charset),  $NOTICE_author_query);
		$NOTICE_author_query = str_replace("!!categ_query!!", htmlentities(stripslashes($categ_query ?? ""),ENT_QUOTES, $charset),  $NOTICE_author_query);
		$NOTICE_author_query = str_replace("!!categ_query_id!!", htmlentities(stripslashes($categ_query_id ?? ""),ENT_QUOTES, $charset),  $NOTICE_author_query);
		$NOTICE_author_query = str_replace("!!date_parution_start!!", $date_parution_start_query ?? "", $NOTICE_author_query);
		$NOTICE_author_query = str_replace("!!date_parution_end!!", $date_parution_end_query ?? "", $NOTICE_author_query);
		if($date_parution_exact_query) {
			$NOTICE_author_query = str_replace("!!date_parution_exact_checked!!", 'checked', $NOTICE_author_query);
			$NOTICE_author_query = str_replace("!!date_parution_no_exact_checked!!", '', $NOTICE_author_query);
			$NOTICE_author_query = str_replace("!!date_parution_end_disabled!!", 'disabled', $NOTICE_author_query);
		}else {
			$NOTICE_author_query = str_replace("!!date_parution_exact_checked!!", '', $NOTICE_author_query);
			$NOTICE_author_query = str_replace("!!date_parution_no_exact_checked!!", 'checked', $NOTICE_author_query);
			$NOTICE_author_query = str_replace("!!date_parution_end_disabled!!", '', $NOTICE_author_query);
		}
		if($thesaurus_concepts_active){
			$NOTICE_author_query = str_replace("!!concept_query!!", htmlentities(stripslashes($concept_query ?? ""),ENT_QUOTES, $charset),  $NOTICE_author_query);
			$NOTICE_author_query = str_replace("!!concept_query_id!!", htmlentities(stripslashes($concept_query_id ?? ""),ENT_QUOTES, $charset),  $NOTICE_author_query);
		}

		$checkbox="";
		if($thesaurus_auto_postage_search){
			$checkbox = "
			<div class='colonne'>
				<div class='row'>
					<input type='checkbox' !!auto_postage_checked!! id='auto_postage_query' name='auto_postage_query'/><label for='auto_postage_query'>".$msg["search_autopostage_check"]."</label>
				</div>
			</div>";
			$checkbox = str_replace("!!auto_postage_checked!!",   (($auto_postage_query) ? 'checked' : ''),  $checkbox);
		}
		$NOTICE_author_query = str_replace("!!auto_postage!!",   $checkbox,  $NOTICE_author_query);

		$checkbox_concepts_autopostage = "";
		if($thesaurus_concepts_autopostage){
			$checkbox_concepts_autopostage = "
			<div class='colonne'>
				<div class='row'>
					<input type='checkbox' !!concepts_autopostage_checked!! id='concepts_autopostage_query' name='concepts_autopostage_query'/><label for='concepts_autopostage_query'>".$msg["search_concepts_autopostage_check"]."</label>
				</div>
			</div>";
			$checkbox_concepts_autopostage = str_replace("!!concepts_autopostage_checked!!",   (($concepts_autopostage_query) ? 'checked' : ''),  $checkbox_concepts_autopostage);
		}
		$NOTICE_author_query = str_replace("!!concepts_autopostage!!", $checkbox_concepts_autopostage, $NOTICE_author_query);

		$NOTICE_author_query = str_replace("!!ex_query!!", htmlentities(stripslashes($ex_query ?? ""),ENT_QUOTES, $charset), $NOTICE_author_query);
		if($pmb_indexation_docnum){
			$checkbox = "<div class='colonne'>
				<div class='row'>
				  <input type='checkbox' !!docnum_query_checked!! id='docnum_query' name='docnum_query'/><label for='docnum_query'>$msg[docnum_indexation]</label>
				</div>
			</div>";
			$checkbox = str_replace("!!docnum_query_checked!!",   (($pmb_indexation_docnum_allfields || $docnum_query) ? 'checked' : ''),  $checkbox);
			$NOTICE_author_query = str_replace("!!docnum_query!!",   $checkbox,  $NOTICE_author_query);
		} else {
    		$NOTICE_author_query = str_replace("!!docnum_query!!", '', $NOTICE_author_query);
		}
		$NOTICE_author_query = str_replace("!!base_url!!",     $this->base_url,$NOTICE_author_query);
		print pmb_bidi($NOTICE_author_query);
	}

	public function make_first_search() {
		global $all_query, $docnum_query, $title_query;
		global $author_query, $author_query_id;
		global $categ_query, $categ_query_id, $thesaurus_auto_postage_search, $auto_postage_query;
		global $thesaurus_concepts_active, $concept_query, $concept_query_id, $thesaurus_concepts_autopostage, $concepts_autopostage_query;
		global $ex_query,$typdoc_query, $statut_query, $etat ;
		global $nb_per_page_a_search;
		global $class_path;
		global $pmb_default_operator;
		global $acces_j;
		global $date_parution_exact_query, $date_parution_start_query, $date_parution_end_query, $title_sql_query;

		if ($nb_per_page_a_search) $this->nb_per_page=$nb_per_page_a_search; else $this->nb_per_page=3;
		$author_per_page=10;
		$restrict='';
		$queries = array();
		if (!empty($typdoc_query) && !empty($typdoc_query[0])) $restrict = "and typdoc in ('".implode("','",$typdoc_query)."') ";
		if (!empty($statut_query) && !empty($statut_query[0])) $restrict.= "and statut in ('".implode("','",$statut_query)."') ";

		if($date_parution_start_query) {
			$date_parution_start = detectFormatDate($date_parution_start_query);
		} else {
			$date_parution_start = '';
		}
		if($date_parution_end_query) {
			$date_parution_end = detectFormatDate($date_parution_end_query);
		} else {
			$date_parution_end = '';
		}
		if($date_parution_start && $date_parution_exact_query) {
			$restrict.= " and date_parution = '".$date_parution_start."' ";
		} else {
			if($date_parution_start) {
				$restrict.= " and date_parution >= '".$date_parution_start."' ";
			}
			if($date_parution_end) {
				$restrict.= " and date_parution <='".$date_parution_end."' ";
			}
		}

		//traitons les cas particuliers...
		if($author_query && !$author_query_id*1 && !$all_query && !$title_query && !$categ_query && !$concept_query){
			// Recherche sur l'auteur uniquement :
			$searcher_authorities_authors = searcher_factory::get_searcher("authors", "",stripslashes($author_query));
// 			$searcher_authorities_authors = new searcher_authorities_authors(stripslashes($author_query));
			$this->nbresults=$searcher_authorities_authors->get_nb_results();
			$this->sorted_result = $searcher_authorities_authors->get_sorted_result('default', $this->page*$author_per_page, $author_per_page);
			$this->nbepage=ceil($this->nbresults/$author_per_page);
			return AUT_LIST;
		}else{
			//sinon, on liste des notices, c'est assez simple...
			$no_results = false;
			//tous les champs
			if($all_query){
				$searcher = searcher_factory::get_searcher("records", "all_fields",stripslashes($all_query));
//   				$searcher = new searcher_records_all_fields(stripslashes($all_query));
  				$queries[]=$searcher->get_full_query()." ";
			}

			//pour la suite, avant de déclencher les recherches, on vérifie si la recherche est différente de celle tous les champs (on s'économise quelques requetes qui ne serviront à rien)

			//les concepts
			if($thesaurus_concepts_active && $concept_query && $concept_query != $all_query){
				if($concept_query_id*1) {
					$queries[] = searcher_records_concepts::get_full_query_from_authority($concept_query_id);
				} else {
					$concept_searcher = searcher_factory::get_searcher("records", "concepts",stripslashes($concept_query));
// 					$concept_searcher = new searcher_records_concepts(stripslashes($concept_query));
					if($concept_searcher->get_nb_results()){
						$queries[]=$concept_searcher->get_full_query()." ";
					}else{
						$no_results =true;
					}
				}
			}
			//le titre
			if($title_query && $title_query != $all_query){
				$title_searcher = searcher_factory::get_searcher("records", "title",stripslashes($title_query));
// 				$title_searcher = new searcher_records_title(stripslashes($title_query));
				if($title_searcher->get_nb_results()){
					//hack, un petit espace à la fin de la requete nous évite une régression avec le tri...
					$queries[]=$title_searcher->get_full_query()." ";
				}else{
					$no_results =true;
				}
				$this->text_query = $title_sql_query;
			}
			//auteur
			if($author_query && $author_query != $all_query){
			    $author_query_id = intval($author_query_id);
				if($author_query_id) {
					$queries[] = searcher_records_authors::get_full_query_from_authority($author_query_id);
				} else {
					$author_searcher = searcher_factory::get_searcher("records", "authors",stripslashes($author_query));
// 					$author_searcher = new searcher_records_authors(stripslashes($author_query));
					if($author_searcher->get_nb_results()){
						$queries[]=$author_searcher->get_full_query()." ";
					}else{
						$no_results =true;
					}
				}
			}
			//catégorie
			if($categ_query && $categ_query != $all_query){
				$categ_query_id = intval($categ_query_id);
				if($categ_query_id) {
					if($thesaurus_auto_postage_search && $auto_postage_query){
						$limit_montant = pmb_mysql_result(pmb_mysql_query("select case valeur_param when '0' then '{0,0}' when '*' then '*' else concat('{0,',valeur_param,'}') end  as limit_montant from parametres where sstype_param='auto_postage_search_nb_montant' and type_param='thesaurus'"), 0, 0);
						$limit_descendant = pmb_mysql_result(pmb_mysql_query("select case valeur_param when '0' then '{0,0}' when '*' then '*' else concat('{0,',valeur_param,'}') end  as limit_montant from parametres where sstype_param='auto_postage_search_nb_descendant' and type_param='thesaurus'"), 0, 0);
						pmb_mysql_query("drop table if exists catdef");
						pmb_mysql_query("create temporary table catdef as select uni.* from (select n.id_noeud,n.path from noeuds as n join noeuds as term on n.path like concat(term.path,'%') where n.path regexp concat('^',term.path,'(\/[0-9]+)".$limit_descendant."$') and term.id_noeud = ".$categ_query_id." union select n.id_noeud,n.path from noeuds as n join noeuds as term on term.path like concat(n.path,'%') where n.path != 0 and term.path regexp concat('^',n.path,'(\/[0-9]+)".$limit_montant."$') and term.id_noeud = ".$categ_query_id.")as uni");
						$queries[] = "select distinct notcateg_notice as notice_id, 100 as pert from catdef join notices_categories on catdef.id_noeud = notices_categories.num_noeud";
					} else {
						$queries[] = searcher_records_categories::get_full_query_from_authority($categ_query_id);
					}
				} else {
					if($thesaurus_auto_postage_search && $auto_postage_query){
						$aq_auth=new analyse_query(stripslashes($categ_query),0,0,0,0);
						if (!$aq_auth->error) {
							$members_auth=$aq_auth->get_query_members("categories","path_word_categ","index_path_word_categ","num_noeud");
							$requete_count = "select count(distinct notice_id) from notices ";
							$requete_count.= $acces_j;
							$requete_count.= ", categories, noeuds, notices_categories ";
							$requete_count.= "where (".$members_auth["where"].")  ";
							$requete_count.= "and id_noeud= categories.num_noeud and notices_categories.num_noeud=categories.num_noeud and notcateg_notice = notice_id ";
							$requete_count.= $restrict;

							$requete = "select notice_id, ".$members_auth["select"]." as pert from notices ";
							$requete.= $acces_j;
							$requete.= ", categories, noeuds, notices_categories ";
							$requete.= "where (".$members_auth["where"].") ";
							$requete.= "and id_noeud= categories.num_noeud and notices_categories.num_noeud=categories.num_noeud and notcateg_notice = notice_id ";
							$requete.= $restrict." group by notice_id ";
							$requete.= "order by pert desc ";

							$nbresults=@pmb_mysql_result(@pmb_mysql_query($requete_count),0,0);
							if($nbresults){
								$queries[]=$requete;
							}else{
								$no_results = true;
							}
						}
					}else{
						$categ_searcher = searcher_factory::get_searcher('records','categories',stripslashes($categ_query));
// 						$categ_searcher = new searcher_records_categories(stripslashes($categ_query));
						if($categ_searcher->get_nb_results()){
							$queries[]=$categ_searcher->get_full_query()." ";
						}else{
							$no_results = true;
						}
					}
				}
			}

			//on fait un et donc si un élément ne renvoi rien ,on s'embete pas avec les jointures...
			if($no_results){
				$this->nbresults = 0;
				$this->text_query = "select notice_id from notices where notice_id = 0 ";//l'espace à la fin est important
			}else{
				//TODO le tri sur la pertinance desc, titre devrait être automatique...
				$from = "";
				$select_pert = "";
				for($i=0 ; $i<count($queries) ; $i++){
					if($i==0){
						$from = "(".$queries[$i].") as t".$i;
						$select_pert = "t".$i.".pert";
					}else {
						$from.= " inner join (".$queries[$i].") as t".$i." on t".$i.".notice_id = t".($i-1).".notice_id";
						$select_pert.= " + t".$i.".pert";
					}
				}

				//Vu avec AR (à reprendre plus tard)
				$this->text_query = "select t0.notice_id, (".$select_pert.") as pert from ".$from." join notices on t0.notice_id = notices.notice_id ".str_replace("notice_id",'t0.notice_id',$acces_j)." group by t0.notice_id  order by pert desc, notices.index_sew ";
				$result = pmb_mysql_query($this->text_query);

				if($result) {
					$this->nbresults = pmb_mysql_num_rows($result);
				} else {
					$this->nbresults = 0;
				}
			}
			$this->nbepage=ceil($this->nbresults/$this->nb_per_page);
			return NOTICE_LIST;
		}
	}


	public function make_aut_search() {
		global $nb_per_page_a_search;
		global $typdoc_query, $statut_query;
		global $acces_j;
		global $aut_type;

		$restrict='';
		if (!empty($typdoc_query) && !empty($typdoc_query[0])) $restrict = "and typdoc in ('".implode("','",$typdoc_query)."') ";
		if (!empty($statut_query) && !empty($statut_query[0])) $restrict.= "and statut in ('".implode("','",$statut_query)."') ";

		if ($nb_per_page_a_search) $this->nb_per_page=$nb_per_page_a_search; else $this->nb_per_page=3;

		switch ($aut_type) {
			case 'concept':
				$requete_count = "select count(num_object) from index_concept ";
				$requete_count .= $acces_j;
				$requete_count .= "where num_concept = ".$this->id." and type_object = ".TYPE_NOTICE." ";
				$requete_count .= $restrict;

				$requete = "select num_object as notice_id from index_concept ";
				$requete .= $acces_j;
				$requete .= "where num_concept = ".$this->id." and type_object = ".TYPE_NOTICE." ";
				$requete .= $restrict." ";
				break;
			default:
				$requete_count = "select count(distinct notice_id) from notices ";
				$requete_count.= $acces_j;
				$requete_count.= ", responsability where notice_id=responsability_notice and responsability_author=".$this->id." ";
				$requete_count.= $restrict;

				$requete = "select distinct notice_id from notices ";
				$requete.= $acces_j;
				$requete.= ", responsability where notice_id=responsability_notice and responsability_author=".$this->id." ";
				$requete.= $restrict." ";
				$requete.= "order by index_serie, tnvol, index_sew ";
		//		$requete.= "limit ".($this->get_start_page()).", ".$this->nb_per_page;
				break;
		}

		$this->nbresults=@pmb_mysql_result(@pmb_mysql_query($requete_count),0,0);
		$this->t_query=@pmb_mysql_query($requete);
		$this->nbepage=ceil($this->nbresults/$this->nb_per_page);
		$this->text_query=$requete;
	}

	public function store_search() {
		global $title_query,$all_query, $author_query,$typdoc_query, $statut_query,$categ_query,$docnum_query,$pmb_indexation_docnum, $author_query_id, $categ_query_id;
		global $thesaurus_concepts_active,$concept_query, $concept_query_id, $thesaurus_concepts_autopostage, $concepts_autopostage_query;
		global $date_parution_start_query, $date_parution_end_query, $date_parution_exact_query;
		global $charset;

		if(!empty($author_query_id)) $author_query_id = intval($author_query_id); else $author_query_id = 0;
		if(!empty($categ_query_id)) $categ_query_id = intval($categ_query_id); else $categ_query_id = 0;
		if(!empty($concept_query_id)) $concept_query_id = intval($concept_query_id); else $concept_query_id = 0;

		$champs="<input type='hidden' name='title_query' value='".htmlentities(stripslashes($title_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='all_query' value='".htmlentities(stripslashes($all_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='author_query' value='".htmlentities(stripslashes($author_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='author_query_id' value='".$author_query_id."'/>";
		foreach($typdoc_query as $typdoc) {
		    $champs.="<input type='hidden' name='typdoc_query[]' value='".htmlentities(stripslashes($typdoc),ENT_QUOTES,$charset)."'/>";
		}
		foreach($statut_query as $statut) {
		    $champs.="<input type='hidden' name='statut_query[]' value='".htmlentities(stripslashes($statut),ENT_QUOTES,$charset)."'/>";
		}
		$champs.="<input type='hidden' name='categ_query' value='".htmlentities(stripslashes($categ_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='categ_query_id' value='".$categ_query_id."'/>";
		$champs.="<input type='hidden' name='date_parution_start_query' value='".htmlentities(stripslashes($date_parution_start_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='date_parution_end_query' value='".htmlentities(stripslashes($date_parution_end_query),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='date_parution_exact_query' value='".htmlentities(stripslashes($date_parution_exact_query),ENT_QUOTES,$charset)."'/>";

		if($thesaurus_concepts_active){
			$champs.="<input type='hidden' name='concept_query' value='".htmlentities(stripslashes($concept_query),ENT_QUOTES,$charset)."'/>";
			$champs.="<input type='hidden' name='concept_query_id' value='".$concept_query_id."'/>";
			if ($thesaurus_concepts_autopostage) {
				$champs.="<input type='hidden' name='concepts_autopostage_query' value='".$concepts_autopostage_query."'/>";
			}
		}
		if ($pmb_indexation_docnum) {
			$docnum_query ??= "";
			$champs.="<input type='hidden' name='docnum_query' value='".htmlentities(stripslashes($docnum_query),ENT_QUOTES,$charset)."'/>";
		}
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function aut_store_search() {
		global $typdoc_query, $statut_query, $aut_type;
		global $charset;
		$champs="<input type='hidden' name='aut_id' value='".htmlentities(stripslashes($this->id),ENT_QUOTES,$charset)."'/>";
		if (!empty($typdoc_query) && !empty($typdoc_query[0])){
		    foreach ($typdoc_query as $typdoc) {
		        $champs.="<input type='hidden' name='typdoc_query[]' value='".htmlentities(stripslashes($typdoc),ENT_QUOTES,$charset)."'/>";
		    }
		}
		if (!empty($statut_query) && !empty($statut_query[0])){
		    foreach ($statut_query as $statut) {
		        $champs.="<input type='hidden' name='statut_query[]' value='".htmlentities(stripslashes($statut),ENT_QUOTES,$charset)."'/>";
		    }
		}
		$champs.="<input type='hidden' name='aut_type' value='".htmlentities(stripslashes($aut_type),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function aut_list() {
		global $msg;
		global $charset;
		global $author_query;
		global $typdoc_query, $statut_query;
		global $pmb_allow_external_search;

		$research="<b>{$msg[234]}</b>&nbsp;".htmlentities(stripslashes($author_query),ENT_QUOTES,$charset);
		$this->human_query=$research;
		$this->human_aut_query=$research;

		if ($this->nbresults) {
				$research .= " => ".$this->nbresults." ".$msg["search_resultat"];
				print pmb_bidi("<div class='othersearchinfo'>$research</div>");
				$author_list="<table>\n";
				$parity = 0 ;
				if(isset($this->sorted_result) && is_array($this->sorted_result)) {
				    $link_typdoc_query = '';
				    if (!empty($typdoc_query) && !empty($typdoc_query[0])){
				        foreach ($typdoc_query as $typdoc) {
				            $link_typdoc_query .= "&typdoc_query[]=".$typdoc;
				        }
				    }
				    $link_statut_query = '';
				    if (!empty($statut_query) && !empty($statut_query[0])){
				        foreach ($statut_query as $statut) {
				            $link_statut_query .= "&statut_query[]=".$statut;
				        }
				    }
					foreach ($this->sorted_result as $id_authority) {
						if ($parity % 2) {
							$pair_impair = "even";
						} else {
							$pair_impair = "odd";
						}
						$parity += 1;
						$authority = new authority($id_authority);
						$auteur = new auteur($authority->get_num_object());

						$notice_count_sql = "SELECT count(DISTINCT responsability_notice) FROM responsability WHERE responsability_author = ".$authority->get_num_object();
						$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);

						if($auteur->see) {
							$notice_auteur_see_count_sql = "SELECT count(DISTINCT responsability_notice) FROM responsability WHERE responsability_author = ".$auteur->see;
							$notice_auteur_see_count = pmb_mysql_result(pmb_mysql_query($notice_auteur_see_count_sql), 0, 0);

							$link = $this->base_url."&aut_id=".$auteur->id."&etat=aut_search".$link_typdoc_query.$link_statut_query;
							$link_see = $this->base_url."&aut_id=".$auteur->see."&etat=aut_search".$link_typdoc_query.$link_statut_query;
							$forme = $auteur->display.".&nbsp;- ".$msg["see"]."&nbsp;: <a href='$link_see' class='lien_gestion'>$auteur->see_libelle</a> (".$notice_auteur_see_count.") ";
						} else {
						    $link = $this->base_url."&aut_id=".$auteur->id."&etat=aut_search".$link_typdoc_query.$link_statut_query;
							$forme = $auteur->display;
						}

						$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='$link';\" ";
						$author_list .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'><td class='searcher_aut_list_label'>$forme</td><td class='searcher_aut_list_number'>".$notice_count."</td></tr>";
					}
				}
				$author_list.="</table>\n";
				print pmb_bidi($author_list);
		} else {
				$this->show_form();
				$cles="<strong>".htmlentities(stripslashes($author_query),ENT_QUOTES, $charset)."</strong>";
				//if ($pmb_allow_external_search) $external="<a href='catalog.php?categ=search&mode=7&from_mode=0&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a>";
 				//error_message($msg[357], sprintf($msg["connecteurs_no_title"],$cles,$external), 0, "./catalog.php?categ=search&mode=0");
				error_message($msg[357], $msg[362]." ".$cles, 0, "./catalog.php?categ=search&mode=0");
		}
	}


	public function notice_list_common($title) {
		global $begin_result_liste;
		global $end_result_liste;
		global $msg, $charset;
		global $pmb_allow_external_search;
		global $load_tablist_js;

		if ($this->nbresults) {
				$research=$title;
				$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
				print pmb_bidi("<div class='othersearchinfo'>$research</div>");
 				print $this->get_current_search_map(0);
				print $begin_result_liste;
				$load_tablist_js=1;
				//Affichage des liens paniers et impression
				if ($this->rec_history) {

					if (($this->etat=='first_search')&&($this->is_first_page())) {
						$current=(isset($_SESSION["session_history"]) ? count($_SESSION["session_history"]) : 0);
					} else {
						$current=$_SESSION["CURRENT"];
					}
					if ($current!==false) {
						print $this->get_display_icons($current);
					}
				}
				print $this->get_display_records_list();

				// fin de liste
				print $end_result_liste;
		} else {
			$this->show_form();
			$cles="<strong>".$title."</strong>";
			if ($pmb_allow_external_search) $external="<a href='catalog.php?categ=search&mode=7&from_mode=0&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a>";
			error_message($msg[357], sprintf($msg["connecteurs_no_title"],$cles,$external), 0, "./catalog.php?categ=search&mode=0");
		}
	}


	public function notice_list() {
		global $msg;
		global $charset;
		global $title_query,$author_query,$all_query,$categ_query;
		global $thesaurus_concepts_active,$concept_query;
		global $typdoc_query, $statut_query;
		global $date_parution_start_query, $date_parution_end_query, $date_parution_exact_query;

		$research = '';
		if($this->docnum){
			$libelle = " [".$msg['docnum_search_with']."]";
		} else $libelle ='';
		if ($title_query) {
			$research .= "<b>{$msg[233]}</b>&nbsp;".htmlentities(stripslashes($title_query),ENT_QUOTES,$charset);
		}
		if ($all_query && !$title_query) {
			$research.="<b>".$msg['global_search'].$libelle."</b>&nbsp;".htmlentities(stripslashes($all_query),ENT_QUOTES,$charset);
		} else if (($all_query && $title_query)) {
			$research.= ", <b>".$msg['global_search'].$libelle."</b>&nbsp;".htmlentities(stripslashes($all_query),ENT_QUOTES,$charset);
		}
		if ($categ_query) {
			if ($research != "") $research .= ", ";
			$research .= "<b>{$msg["search_categorie_title"]}</b>&nbsp;".htmlentities(stripslashes($categ_query),ENT_QUOTES,$charset);
		}
		if ($author_query) {
			$research.=", <b>{$msg[234]}</b>&nbsp;".htmlentities(stripslashes($author_query),ENT_QUOTES,$charset);
		}

		if ($thesaurus_concepts_active && $concept_query) {
			if ($research != "") $research .= ", ";
			$research.="<b>{$msg['search_concept_title']}</b>&nbsp;".htmlentities(stripslashes($concept_query),ENT_QUOTES,$charset);
		}

		if (!empty($typdoc_query) && !empty($typdoc_query[0])){
			$doctype = new marc_list('doctype');
			$lib_typdocs = array();
			if ($research != "") $research .= ", ";
			foreach ($typdoc_query as $typdoc) {
				$lib_typdocs[]= $doctype->table[$typdoc];
			}
			$research.= "<b>".$msg["17"].$msg["1901"]."</b>&nbsp;".implode(", ", $lib_typdocs);
		}
		if (!empty($statut_query) && !empty($statut_query[0])){
			if ($research != "") $research .= ", ";
			$query = "SELECT gestion_libelle FROM notice_statut WHERE id_notice_statut in ('".implode("','", $statut_query)."') ";
			$result = pmb_mysql_query($query);
			$rows = array();
			$statut_libelle = "";
			if(pmb_mysql_num_rows($result)){
				while ($row = pmb_mysql_fetch_assoc($result)) {
					$rows[] = $row['gestion_libelle'];
				}
				$statut_libelle = implode(", ", $rows);
			}
			$research.= "<b>".$msg["noti_statut_noti"].$msg["1901"]."</b>&nbsp;".htmlentities(stripslashes($statut_libelle),ENT_QUOTES,$charset);
		}
		if ($date_parution_start_query || $date_parution_end_query){
			if ($research != "") $research .= ", ";
			if ($date_parution_start_query && $date_parution_end_query && !$date_parution_exact_query){
				$research.= '<b>'.$msg['search_date_parution'].':&nbsp;'.$msg['search_date_parution_start'].'</b>&nbsp;'.$date_parution_start_query
						.'<b>,&nbsp;'.$msg['search_date_parution_end'].'</b>&nbsp;'.$date_parution_end_query;
			}elseif ($date_parution_start_query && $date_parution_exact_query){
				$research.= '<b>'.$msg['search_date_parution'].':</b>&nbsp;'.$date_parution_start_query;
			}elseif ($date_parution_start_query ){
				$research.= '<b>'.$msg['search_date_parution'].':&nbsp;'.$msg['search_date_parution_start'].'</b>&nbsp;'.$date_parution_start_query;
			}elseif ($date_parution_end_query ){
				$research.= '<b>'.$msg['search_date_parution'].':&nbsp;'.$msg['search_date_parution_end'].'</b>&nbsp;'.$date_parution_end_query;
			}
		}

		$this->human_query=$research;
		$this->human_notice_query=$research;

		$this->notice_list_common($research);
	}


	public function aut_notice_list() {
		global $msg;
		global $aut_type;

		$research = "";
		switch ($aut_type) {
			case 'concept' :
				$concept = new concept($this->id);
				$research.="<b>{$msg['search_concept_title']}</b>&nbsp;".$concept->get_display_label();
				break;
			default :
				$auteur = new auteur($this->id);
				$research.="<b>{$msg[234]}</b>&nbsp;".$auteur->display;
				break;
		}
		$this->human_notice_query=$research;

		$this->notice_list_common($research);
	}


	public function rec_env() {
		global $msg;
		global $memo_tempo_table_to_rebuild;

		switch ($this->etat) {
				case 'first_search':
				    if ($this->is_first_page()) {
						if(isset($_SESSION["session_history"])) $_SESSION["CURRENT"] = count($_SESSION["session_history"]);
						else $_SESSION["CURRENT"] = 0;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]=$this->base_url;
						$_POST["etat"]="";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]=array();
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]=array();
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$this->human_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$msg["354"];
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["DOCNUM_QUERY"]=$this->docnum;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["AUTO_POSTAGE_QUERY"]=$this->auto_postage_query;
					}
					if ($this->is_first_page()) {
					    $_POST["page"]=1;
					    $this->page=1;
					}
					if (($this->first_search_result==AUT_LIST)&&($_SESSION["CURRENT"]!==false)) {
						$_POST["etat"]="first_search";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]["HUMAN_QUERY"]=$this->human_aut_query;
					}
					if (($this->first_search_result==NOTICE_LIST)&&($_SESSION["CURRENT"]!==false)) {
						$_POST["etat"]="first_search";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_LIST_QUERY']=$memo_tempo_table_to_rebuild;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["HUMAN_QUERY"]=$this->human_notice_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["DOCNUM_QUERY"]=$this->docnum;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["AUTO_POSTAGE_QUERY"]=$this->auto_postage_query;
					}
					break;
				case 'aut_search':
					if(!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
					if(isset($_SESSION["CURRENT"])) {
						$_SESSION["CURRENT"]=intval($_SESSION["CURRENT"]);
					}
					if(!is_int($_SESSION["CURRENT"])) {
						$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
					}
					if (($_SESSION["CURRENT"]!==false) && (is_int($_SESSION["CURRENT"]))) {
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_LIST_QUERY']=$memo_tempo_table_to_rebuild;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['HUMAN_QUERY']=$this->human_notice_query;
					}
					break;
		}
		$_SESSION["last_required"]=false;
	}

	public static function convert_simple_multi($id_champ) {
		global $search;

		$x=0;

		if (!empty($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["title_query"])) {
			$op_="BOOLEAN";
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["title_query"];

			$search[$x]="f_6";
			//opérateur
    		$op="op_".$x."_".$search[$x];
    		global ${$op};
    		${$op}=$op_;

    		//contenu de la recherche
    		$field="field_".$x."_".$search[$x];
    		$field_=array();
    		$field_[0]=$valeur_champ;
    		global ${$field};
    		${$field}=$field_;

    		//opérateur inter-champ
    		$inter="inter_".$x."_".$search[$x];
    		global ${$inter};
    		${$inter}="";

    		//variables auxiliaires
    		$fieldvar_="fieldvar_".$x."_".$search[$x];
    		global ${$fieldvar_};
    		${$fieldvar_}="";
    		$fieldvar=${$fieldvar_};
			$x++;
		}
		if (!empty($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["all_query"])) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["all_query"];
			$op_="BOOLEAN";

			$search[$x]="f_7";
			//opérateur
    		$op="op_".$x."_".$search[$x];
    		global ${$op};
    		${$op}=$op_;

    		//contenu de la recherche
    		$field="field_".$x."_".$search[$x];
    		$field_=array();
    		$field_[0]=$valeur_champ;
    		global ${$field};
    		${$field}=$field_;

    		//opérateur inter-champ
    		$inter="inter_".$x."_".$search[$x];
    		global ${$inter};
    		${$inter}="";

    		//variables auxiliaires
    		$fieldvar_="fieldvar_".$x."_".$search[$x];
    		global ${$fieldvar_};
    		$t["is_num"][0]=$_SESSION["session_history"][$id_champ]["NOTI"]["DOCNUM_QUERY"];
    		$t["ck_affiche"][0]=$_SESSION["session_history"][$id_champ]["NOTI"]["DOCNUM_QUERY"];
    		${$fieldvar_}=$t;
    		$fieldvar=${$fieldvar_};
			$x++;
		}
		if (!empty($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["author_query"])) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["author_query"];

			$op_="BOOLEAN";
			$search[$x]="f_8";

			//opérateur
    		$op="op_".$x."_".$search[$x];
    		global ${$op};
    		${$op}=$op_;

    		//contenu de la recherche
    		$field="field_".$x."_".$search[$x];
    		$field_=array();
    		$field_[0]=$valeur_champ;
    		global ${$field};
    		${$field}=$field_;

    		//opérateur inter-champ
    		$inter="inter_".$x."_".$search[$x];
    		global ${$inter};
    		if ($x>0) {
    			${$inter}="and";
    		} else {
    			${$inter}="";
    		}
    		//variables auxiliaires
    		$fieldvar_="fieldvar_".$x."_".$search[$x];
    		global ${$fieldvar_};
    		${$fieldvar_}="";
    		$fieldvar=${$fieldvar_};
			$x++;
		} else {
			if (!empty($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"])) {
				$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];

				$op_="EQ";
				$search[$x]="f_8";
				//opérateur
    			$op="op_".$x."_".$search[$x];
    			global ${$op};
    			${$op}=$op_;

    			//contenu de la recherche
    			$field="field_".$x."_".$search[$x];
    			$field_=array();
    			$field_[0]=$valeur_champ;
    			global ${$field};
    			${$field}=$field_;

    			//opérateur inter-champ
    			$inter="inter_".$x."_".$search[$x];
    			global ${$inter};
    			if ($x>0) {
    				${$inter}="and";
    			} else {
    				${$inter}="";
    			}

    			//variables auxiliaires
    			$fieldvar_="fieldvar_".$x."_".$search[$x];
    			global ${$fieldvar_};
    			${$fieldvar_}="";
    			$fieldvar=${$fieldvar_};
				$x++;
			}
		}
		if (is_array($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["typdoc_query"]) && $_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["typdoc_query"][0]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["typdoc_query"];
			$op_="EQ";
			$search[$x]="f_9";
			//opérateur
    		$op="op_".$x."_".$search[$x];
    		global ${$op};
    		${$op}=$op_;

    		//contenu de la recherche
    		$field="field_".$x."_".$search[$x];
    		$field_=array();
    		$field_=$valeur_champ;
    		global ${$field};
    		${$field}=$field_;

    		//opérateur inter-champ
    		$inter="inter_".$x."_".$search[$x];
    		global ${$inter};
    		if ($x>0) {
    			${$inter}="and";
    		} else {
    			${$inter}="";
    		}
    		//variables auxiliaires
    		$fieldvar_="fieldvar_".$x."_".$search[$x];
    		global ${$fieldvar_};
    		${$fieldvar_}="";
    		$fieldvar=${$fieldvar_};
			$x++;
		}
		if (!empty($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["categ_query"])) {
		    $valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["categ_query"];
		    $op_="EQ";
		    $search[$x]="f_1";
		    //opérateur
		    $op="op_".$x."_".$search[$x];
		    global ${$op};
		    ${$op}=$op_;

		    //contenu de la recherche
		    $field="field_".$x."_".$search[$x];
		    $field_=array();
		    $field_[0]=$valeur_champ;
		    global ${$field};
		    ${$field}=$field_;

		    //opérateur inter-champ
		    $inter="inter_".$x."_".$search[$x];
		    global ${$inter};
		    if ($x>0) {
		        ${$inter}="and";
		    } else {
		        ${$inter}="";
		    }
		    $x++;
		}
		if (!empty($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["concept_query"])) {
		    $valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["concept_query"];
		    $op_="BOOLEAN";
		    $search[$x]="f_1000";
		    //opérateur
		    $op="op_".$x."_".$search[$x];
		    global ${$op};
		    ${$op}=$op_;

		    //contenu de la recherche
		    $field="field_".$x."_".$search[$x];
		    $field_=array();
		    $field_[0]=$valeur_champ;
		    global ${$field};
		    ${$field}=$field_;

		    //opérateur inter-champ
		    $inter="inter_".$x."_".$search[$x];
		    global ${$inter};
		    if ($x>0) {
		        ${$inter}="and";
		    } else {
		        ${$inter}="";
		    }
		    $x++;
		}
		if (is_array($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["statut_query"]) && $_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["statut_query"][0]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["statut_query"];
			$op_="EQ";
			$search[$x]="f_10";
			//opérateur
    		$op="op_".$x."_".$search[$x];
    		global ${$op};
    		${$op}=$op_;

    		//contenu de la recherche
    		$field="field_".$x."_".$search[$x];
    		$field_=array();
    		$field_=$valeur_champ;
    		global ${$field};
    		${$field}=$field_;

    		//opérateur inter-champ
    		$inter="inter_".$x."_".$search[$x];
    		global ${$inter};
    		if ($x>0) {
    			${$inter}="and";
    		} else {
    			${$inter}="";
    		}
    		//variables auxiliaires
    		$fieldvar_="fieldvar_".$x."_".$search[$x];
    		global ${$fieldvar_};
    		${$fieldvar_}="";
    		$fieldvar=${$fieldvar_};
		}
	}

	public static function convert_simple_multi_unimarc($id_champ) {
		global $search;

		$x=0;

		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["title_query"]) {
			$op_="BOOLEAN";
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["title_query"];

			$search[$x]="f_6";
			//opérateur
    		$op="op_".$x."_".$search[$x];
    		global ${$op};
    		${$op}=$op_;

    		//contenu de la recherche
    		$field="field_".$x."_".$search[$x];
    		$field_=array();
    		$field_[0]=$valeur_champ;
    		global ${$field};
    		${$field}=$field_;

    		//opérateur inter-champ
    		$inter="inter_".$x."_".$search[$x];
    		global ${$inter};
    		${$inter}="";

    		//variables auxiliaires
    		$fieldvar_="fieldvar_".$x."_".$search[$x];
    		global ${$fieldvar_};
    		${$fieldvar_}="";
    		$fieldvar=${$fieldvar_};
			$x++;
		}
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["all_query"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["all_query"];
			$op_="BOOLEAN";

			$search[$x]="f_7";
			//opérateur
    		$op="op_".$x."_".$search[$x];
    		global ${$op};
    		${$op}=$op_;

    		//contenu de la recherche
    		$field="field_".$x."_".$search[$x];
    		$field_=array();
    		$field_[0]=$valeur_champ;
    		global ${$field};
    		${$field}=$field_;

    		//opérateur inter-champ
    		$inter="inter_".$x."_".$search[$x];
    		global ${$inter};
    		${$inter}="";

    		//variables auxiliaires
    		$fieldvar_="fieldvar_".$x."_".$search[$x];
    		global ${$fieldvar_};
    		${$fieldvar_}="";
    		$fieldvar=${$fieldvar_};
			$x++;
		}
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["author_query"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["author_query"];

			$op_="BOOLEAN";
			$search[$x]="f_8";

			//opérateur
    		$op="op_".$x."_".$search[$x];
    		global ${$op};
    		${$op}=$op_;

    		//contenu de la recherche
    		$field="field_".$x."_".$search[$x];
    		$field_=array();
    		$field_[0]=$valeur_champ;
    		global ${$field};
    		${$field}=$field_;

    		//opérateur inter-champ
    		$inter="inter_".$x."_".$search[$x];
    		global ${$inter};
    		if ($x>0) {
    			${$inter}="and";
    		} else {
    			${$inter}="";
    		}
    		//variables auxiliaires
    		$fieldvar_="fieldvar_".$x."_".$search[$x];
    		global ${$fieldvar_};
    		${$fieldvar_}="";
    		$fieldvar=${$fieldvar_};
			$x++;
		} else {
			if ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"]) {
				$author_id=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];
				$requete="select concat(author_name,', ',author_rejete) from authors where author_id=".$author_id;
				$r_author=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_author)) {
					$valeur_champ=pmb_mysql_result($r_author,0,0);
				}
				$op_="BOOLEAN";
				$search[$x]="f_8";
				//opérateur
    			$op="op_".$x."_".$search[$x];
    			global ${$op};
    			${$op}=$op_;

    			//contenu de la recherche
    			$field="field_".$x."_".$search[$x];
    			$field_=array();
    			$field_[0]=$valeur_champ;
    			global ${$field};
    			${$field}=$field_;

    			//opérateur inter-champ
    			$inter="inter_".$x."_".$search[$x];
    			global ${$inter};
    			if ($x>0) {
    				${$inter}="and";
    			} else {
    				${$inter}="";
    			}

    			//variables auxiliaires
    			$fieldvar_="fieldvar_".$x."_".$search[$x];
    			global ${$fieldvar_};
    			${$fieldvar_}="";
    			$fieldvar=${$fieldvar_};
				$x++;
			}
		}
		if (is_array($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["typdoc_query"]) && $_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["typdoc_query"][0]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["typdoc_query"];
			$op_="EQ";
			$search[$x]="f_9";
			//opérateur
    		$op="op_".$x."_".$search[$x];
    		global ${$op};
    		${$op}=$op_;

    		//contenu de la recherche
    		$field="field_".$x."_".$search[$x];
    		$field_=array();
    		$field_=$valeur_champ;
    		global ${$field};
    		${$field}=$field_;

    		//opérateur inter-champ
    		$inter="inter_".$x."_".$search[$x];
    		global ${$inter};
    		if ($x>0) {
    			${$inter}="and";
    		} else {
    			${$inter}="";
    		}
    		//variables auxiliaires
    		$fieldvar_="fieldvar_".$x."_".$search[$x];
    		global ${$fieldvar_};
    		${$fieldvar_}="";
    		$fieldvar=${$fieldvar_};
			$x++;
		}
		//Pas de statut !
	}
}

class searcher_subject extends searcher {
	public $s_query="";
	public $i_query="";
	public $id_query="";
	public $nb_s;
	public $nb_i;
	public $nb_id;
	public $t_query;


	public function show_form() {
		global $search_subject;
		global $search_indexint,$search_indexint_id;
		global $search_form_categ,$browser;
		global $browser_url;
		global $id_thes;

		//affichage du selectionneur de thesaurus et du lien vers les thésaurus
		$search_form_categ=str_replace("<!-- sel_thesaurus -->", thesaurus::getSelector($id_thes, $this->base_url), $search_form_categ);

		//affichage du choix de langue pour la recherche
		//		$sel_langue = '';
		//		$sel_langue = "<div class='row'>";
		//		$sel_langue.= "<input type='checkbox' name='lg_search' id='lg_search' value='1' />&nbsp;".htmlentities($msg['thes_sel_langue'],ENT_QUOTES, $charset);
		//		$sel_langue.= "</div><br />";
		//		$search_form_categ=str_replace("<!-- sel_langue -->",$sel_langue,$search_form_categ);


		$search_form_categ=str_replace("!!base_url!!",$this->base_url,$search_form_categ);
		$browser=str_replace("!!browser_url!!",$browser_url,$browser);
		print pmb_bidi($search_form_categ.$browser);
	}


	public function show_error($car,$input,$error_message) {
		global $browser_url;
		global $browser,$search_form_categ;
		global $msg;


		$search_form_categ=str_replace("!!base_url!!",$this->base_url,$search_form_categ);
		print pmb_bidi($search_form_categ);
		error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$car,$input,$error_message));
		$browser=str_replace("!!browser_url!!",$browser_url,$browser);
		print pmb_bidi($browser);
	}

	public function make_first_search() {
		global $search_subject;
		global $search_indexint,$search_indexint_id,$aut_type;
		global $msg;
		global $browser,$search_form_categ,$browser_url;
		global $lang;
		global $id_thes;

		if ($search_indexint_id) {
				$this->id=$search_indexint_id;
				$aut_type="indexint";
				return AUT_SEARCH;
		}

		$this->nbresults=0;

		if ($search_subject) {
				$aq=new analyse_query(stripslashes($search_subject));
				if (!$aq->error) {

					if($id_thes !=-1){
						$thes= new thesaurus($id_thes);
					}
					$requete = "SELECT SQL_CALC_FOUND_ROWS noeuds.id_noeud AS categ_id, ";
					if(($id_thes !=-1) && ($thes->langue_defaut == $lang)){
						$members = $aq->get_query_members("categories", "libelle_categorie", "index_categorie", "num_noeud");

						$requete.= $members["select"]." AS pert ";
						$requete.= "FROM noeuds JOIN categories ON noeuds.id_noeud = categories.num_noeud AND categories.langue='".addslashes($lang)."' ";
						$requete.= "WHERE noeuds.num_thesaurus = '".$id_thes."' ";
						$requete.= "AND (".$members["where"].") ";
						$requete.= "AND categories.libelle_categorie NOT LIKE '~%' ";
						$requete.= "ORDER BY pert DESC,categories.index_categorie";
					}else{
						$members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
						$members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");

						$requete.= "IF (catlg.num_noeud IS NULL, catdef.index_categorie, catlg.index_categorie) as index_categorie, ";
						$requete.= "IF (catlg.num_noeud IS NULL, ".$members_catdef["select"].", ".$members_catlg["select"].") as pert ";

						if($id_thes !=-1){
							$requete.= "FROM noeuds JOIN categories AS catdef ON noeuds.id_noeud = catdef.num_noeud AND catdef.langue = '".$thes->langue_defaut."' ";
							$requete.= "LEFT JOIN categories AS catlg ON catdef.num_noeud = catlg.num_noeud AND catlg.langue = '".$lang."' ";
							$requete.= "WHERE noeuds.num_thesaurus = '".$id_thes."' ";
						}else{
							//Plusieurs thésaurus
							$requete.= "FROM noeuds JOIN thesaurus ON thesaurus.id_thesaurus = noeuds.num_thesaurus ";
							$requete.= "JOIN categories AS catdef ON noeuds.id_noeud = catdef.num_noeud AND catdef.langue = thesaurus.langue_defaut ";
							$requete.= "LEFT JOIN categories AS catlg on catdef.num_noeud = catlg.num_noeud AND catlg.langue = '".$lang."' ";
							$requete.= "WHERE 1 ";
						}
						$requete.= "AND catdef.libelle_categorie NOT LIKE '~%' ";
						$requete.= "AND (IF (catlg.num_noeud IS NULL, ".$members_catdef["where"].", ".$members_catlg["where"].") ) ORDER BY pert DESC,index_categorie";
					}
					$this->s_query = pmb_mysql_query($requete);

					$qry = "SELECT FOUND_ROWS() AS NbRows";
					if($resnum = pmb_mysql_query($qry)){
						$this->nb_s = pmb_mysql_result($resnum,0,0);
					}

				} else {

					$this->show_error($aq->current_car,$aq->input_html,$aq->error_message);
					return;
				}
		}

		if ($search_indexint) {
				$aq=new analyse_query(stripslashes($search_indexint));
				if (!$aq->error) {

					$this->nb_id=@pmb_mysql_result(@pmb_mysql_query("select count(distinct indexint_id) from indexint where indexint_name like '".str_replace("*","%",$search_indexint)."'"),0,0);
					if ($this->nb_id) {
						$this->id_query=@pmb_mysql_query("select indexint_id from indexint where indexint_name like '".str_replace("*","%",$search_indexint)."' order by indexint_name*1, indexint_name");
						if ($this->nb_id==1) {
								$id=@pmb_mysql_fetch_object($this->id_query);
								$this->id=$id->indexint_id;
								$aut_type="indexint";
								return AUT_SEARCH;
						}
					}
					$this->nb_i=@pmb_mysql_result(@pmb_mysql_query($aq->get_query_count("indexint","indexint_comment","index_indexint","indexint_id")),0,0);
					if ($this->nb_i)
						$this->i_query=@pmb_mysql_query($aq->get_query("indexint","indexint_comment","index_indexint","indexint_id"));
				} else {
					$this->show_error($aq->current_car,$aq->input_html,$aq->error_message);
					return;
				}
		}

		if (($this->nb_s+$this->nb_i+$this->nb_id)==0) {


			//affichage du selectionneur de thesaurus et du lien vers les thésaurus
			$search_form_categ=str_replace("<!-- sel_thesaurus -->", thesaurus::getSelector($id_thes, $this->base_url), $search_form_categ);

			//affichage du choix de langue pour la recherche
			//		$sel_langue = '';
			//		$sel_langue = "<div class='row'>";
			//		$sel_langue.= "<input type='checkbox' name='lg_search' id='lg_search' value='1' />&nbsp;".htmlentities($msg['thes_sel_langue'],ENT_QUOTES, $charset);
			//		$sel_langue.= "</div><br />";
			//		$search_form_categ=str_replace("<!-- sel_langue -->",$sel_langue,$search_form_categ);



			$search_form_categ=str_replace("!!base_url!!",$this->base_url,$search_form_categ);
			print pmb_bidi($search_form_categ);
			error_message($msg["searcher_no_result"],$msg["searcher_no_result_desc"]);
			$browser=str_replace("!!browser_url!!",$browser_url,$browser);
			print pmb_bidi($browser);
			return;

		}

		return AUT_LIST;
	}

	public function make_aut_search() {
		global $aut_type,$nb_per_page_a_search;
		global $thesaurus_auto_postage_montant,$thesaurus_auto_postage_descendant,$thesaurus_auto_postage_nb_montant,$thesaurus_auto_postage_nb_descendant;
		global $thesaurus_auto_postage_etendre_recherche,$nb_level_enfants,$nb_level_parents,$msg;
		global $acces_j;

		if ($nb_per_page_a_search) $this->nb_per_page=$nb_per_page_a_search; else $this->nb_per_page=3;

		switch ($aut_type) {
			case "indexint":
				$requete_count="select count(distinct notice_id) from notices ";
				$requete_count.= $acces_j;
				$requete_count.= "where indexint=".$this->id." ";

				$requete="select notice_id from notices ";
				$requete.= $acces_j;
				$requete.= "where indexint=".$this->id." ";
//				$requete.= "order by index_serie,tnvol,index_sew limit ".($this->get_start_page()).",".$this->nb_per_page;
				break;

			case "categ":
				//Lire le champ path du noeud pour étendre la recherche éventuellement au fils et aux père de la catégorie
				// lien Etendre auto_postage
				if(!isset($nb_level_enfants)) {
					// non defini, prise des valeurs par défaut
					if(isset($_SESSION["nb_level_enfants"]) && $thesaurus_auto_postage_etendre_recherche) $nb_level_descendant=$_SESSION["nb_level_enfants"];
					else $nb_level_descendant=$thesaurus_auto_postage_nb_descendant;
				} else {
					$nb_level_descendant=$nb_level_enfants;
				}
				// lien Etendre auto_postage
				if(!isset($nb_level_parents)) {
					// non defini, prise des valeurs par défaut
					if(isset($_SESSION["nb_level_parents"]) && $thesaurus_auto_postage_etendre_recherche) $nb_level_montant=$_SESSION["nb_level_parents"];
					else $nb_level_montant=$thesaurus_auto_postage_nb_montant;
				} else {
					$nb_level_montant=$nb_level_parents;
				}
				$_SESSION["nb_level_enfants"]=	$nb_level_descendant;
				$_SESSION["nb_level_parents"]=	$nb_level_montant;

				$q = "select path from noeuds where id_noeud = '".$this->id."' ";
				$r = pmb_mysql_query($q);
				$path=pmb_mysql_result($r, 0, 0);
				$nb_pere=substr_count($path,'/');

				// Si un path est renseigné et le paramètrage activé
				if ($path && ($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_montant || $thesaurus_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){
					//Recherche des fils
					if(($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_etendre_recherche)&& $nb_level_descendant) {
						if($nb_level_descendant != '*' && is_numeric($nb_level_descendant))
							$liste_fils=" path regexp '^$path(\\/[0-9]*){0,$nb_level_descendant}$' ";
						else
							$liste_fils=" path like '$path/%' or id_noeud='".$this->id."' ";
					} else {
						$liste_fils=" id_noeud = '".$this->id."' ";
					}
					// recherche des pères
					$liste_pere = "";
					if(($thesaurus_auto_postage_montant || $thesaurus_auto_postage_etendre_recherche) && $nb_level_montant) {
						$id_list_pere=explode('/',$path);
						$stop_pere=0;
						if($nb_level_montant != '*' && is_numeric($nb_level_montant)) $stop_pere = $nb_pere - intval($nb_level_montant);
						// si les fils intégré, il y a déjà la categ courant dans la requête
						if($liste_fils) $i=$nb_pere-1;
						else $i=$nb_pere;
						for($i;$i>=$stop_pere; $i--) {
							$liste_pere.= " or id_noeud='".$id_list_pere[$i]."' ";
						}
					}
					// requete permettant de remonter les notices associées à la liste des catégories trouvées;
					$suite_req = "FROM noeuds inner join notices_categories on id_noeud=num_noeud inner join notices on notcateg_notice=notice_id ";
					$suite_req.= $acces_j;
					$suite_req.= "WHERE ($liste_fils $liste_pere) and notices_categories.notcateg_notice = notices.notice_id ";
				} else {
					// cas normal d'avant
					$suite_req = "FROM notices_categories, notices ";
					$suite_req.= $acces_j;
					$suite_req.= "WHERE notices_categories.num_noeud = '".$this->id."' and notices_categories.notcateg_notice = notices.notice_id ";
				}
				if ($path) {
					if ($thesaurus_auto_postage_etendre_recherche == 1 || ($thesaurus_auto_postage_etendre_recherche == 2 && !$nb_pere)) {
						$input_txt="<input name='nb_level_enfants' type='text' size='2' value='$nb_level_descendant'
							onchange=\"document.location='".$this->base_url."&aut_id=".$this->id."&aut_type=categ&etat=aut_search&no_rec_history=1&nb_level_enfants='+this.value\">";
						$auto_postage_form=str_replace("!!nb_level_enfants!!",$input_txt,$msg["categories_autopostage_enfants"]);

					}elseif ($thesaurus_auto_postage_etendre_recherche == 2 && $nb_pere) {
						$input_txt="<input name='nb_level_enfants' id='nb_level_enfants' type='text' size='2' value='$nb_level_descendant'
							onchange=\"document.location='".$this->base_url."&aut_id=".$this->id."&aut_type=categ&etat=aut_search&no_rec_history=1&nb_level_enfants='+this.value+'&nb_level_enfants='+this.value+'&nb_level_parents='+document.getElementById('nb_level_parents').value;\">";
						$auto_postage_form=str_replace("!!nb_level_enfants!!",$input_txt,$msg["categories_autopostage_parents_enfants"]);

						$input_txt="<input name='nb_level_parents' id='nb_level_parents' type='text' size='2' value='$nb_level_montant'
							onchange=\"document.location='".$this->base_url."&aut_id=".$this->id."&aut_type=categ&etat=aut_search&no_rec_history=1&nb_level_parents='+this.value+'&nb_level_enfants='+document.getElementById('nb_level_enfants').value;\">";
						$auto_postage_form=str_replace("!!nb_level_parents!!",$input_txt,$auto_postage_form);

					}elseif ($thesaurus_auto_postage_etendre_recherche == 3 ) {
						if($nb_pere) {
							$input_txt="<input name='nb_level_parents' type='text' size='2' value='$nb_level_montant'
								onchange=\"document.location='".$this->base_url."&aut_id=".$this->id."&aut_type=categ&etat=aut_search&no_rec_history=1&nb_level_parents='+this.value\">";
							$auto_postage_form=str_replace("!!nb_level_parents!!",$input_txt,$msg["categories_autopostage_parents"]);
						}
					}elseif (!isset($auto_postage_form)) {
					    $auto_postage_form = "";
					}
					$this->auto_postage_form=$auto_postage_form;
				}
				$requete_count="select count(distinct notice_id) ".$suite_req;
				$requete = "select distinct notice_id ".$suite_req."order by index_serie,tnvol,index_sew ";//limit ".($this->get_start_page()).",".$this->nb_per_page;
				break;
		}
		$this->nbresults=@pmb_mysql_result(@pmb_mysql_query($requete_count),0,0);
		$this->t_query=@pmb_mysql_query($requete);
		$this->nbepage=ceil($this->nbresults/$this->nb_per_page);
		$this->text_query=$requete;
	}

	public function store_search() {
		global $search_subject;
		global $search_indexint,$search_indexint_id,$show_empty;
		global $charset;
		$champs="<input type='hidden' name='search_subject' value='".htmlentities(stripslashes($search_subject),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='search_indexint' value='".htmlentities(stripslashes($search_indexint),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='search_indexint_id' value='".htmlentities(stripslashes($search_indexint_id),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='show_empty' value='".$show_empty."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print pmb_bidi($this->store_form);
	}

	public function aut_store_search() {
		global $charset,$aut_type;
		$champs="<input type='hidden' name='aut_id' value='".htmlentities(stripslashes($this->id),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='aut_type' value='".htmlentities(stripslashes($aut_type),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print pmb_bidi($this->store_form);
	}

	public function aut_list() {
		global $search_subject;
		global $search_indexint,$search_indexint_id;
		global $msg;
		global $charset;
		global $show_empty;
		$pair_impair = "";
		$parity = 0;

		$human = array();
		if ($search_subject) $human[]="<b>".$msg["histo_subject"]."</b> ".htmlentities(stripslashes($search_subject),ENT_QUOTES,$charset);
		if ($search_indexint) $human[]="<b>".$msg["histo_indexint"]."</b> ".htmlentities(stripslashes($search_indexint),ENT_QUOTES,$charset);
		$this->human_query=implode(", ",$human);
		$this->human_aut_query=implode(", ",$human);
		if ($this->nb_s) {
				$empty=false;
				print "<strong>{$msg[23]} : ".sprintf($msg["searcher_results"],$this->nb_s)."</strong><hr /><table>";
				while($categ=@pmb_mysql_fetch_object($this->s_query)) {
					$pair_impair = $parity % 2 ? "even" : "odd";

					$temp = new category($categ->categ_id);
					if($temp->voir_id) {
						$cr=$temp->catalog_form;
						$temp = new category($temp->voir_id);
						$display = htmlentities($cr,ENT_QUOTES,$charset)." -> <i>".htmlentities($temp->catalog_form,ENT_QUOTES,$charset)."@</i>";
					} else {
						$display = htmlentities($temp->catalog_form,ENT_QUOTES,$charset);
					}
					if($temp->has_notices()) {
						$notice_count = $temp->notice_count(false);
						$link_categ = "<td><a href='".$this->base_url."&aut_id=".$temp->id."&aut_type=categ&etat=aut_search'>$display</a></td><td>$notice_count</td>";
					}
					else {
						$empty=true;
						if ($show_empty) $link_categ = "<td>$display</td><td></td>"; else $link_categ="";
					}
					if ($link_categ)
						print "<tr class=\"$pair_impair\">$link_categ</tr>";
				}
				print "</table>";
				if (($empty)&&(!$show_empty)) print "<a href='#' onClick=\"document.store_search.show_empty.value=1; document.store_search.page.value=0; document.store_search.submit(); return false;\">".$msg["searcher_categ_empty_results"]."</a>";
		}
		if (($this->nb_i)||($this->nb_id)) {
				if ($this->nb_id) {
					print "<br /><strong>".$msg['indexint_catal_title']." ".$msg["searcher_exact_indexint"].": ".sprintf($msg["searcher_results"],$this->nb_id)."</strong><hr /><table>";
					$id_=array();
					$empty=false;
					while($indexint=@pmb_mysql_fetch_object($this->id_query)) {
						$pair_impair = $parity % 2 ? "even" : "odd";

						$id_[$indexint->indexint_id]=1;
						$temp = new indexint($indexint->indexint_id);
						$display = htmlentities($temp->name." - ".$temp->comment,ENT_QUOTES,$charset);
						if($temp->has_notices()) {
							$notice_count_sql = "SELECT count(*) FROM notices WHERE indexint = ".$temp->indexint_id;
							$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
							$link = "<td><a href='".$this->base_url."&aut_id=".$temp->indexint_id."&aut_type=indexint&etat=aut_search'>$display</a></td><td>".$notice_count."</td>";
						}
						else {
								$empty=true;
								if ($show_empty) $link = "<td>$display</td><td></td>"; else $link="";
						}
						if ($link) {
							print "<tr class=\"$pair_impair\">$link</tr>";
							$parity += 1;
						}
					}
					print "</table>";
					if (($empty)&&(!$show_empty)) print "<a href='#' onClick=\"document.store_search.show_empty.value=1; document.store_search.page.value=0; document.store_search.submit(); return false;\">".$msg["searcher_indexint_empty_results"]."</a><br /><br />";
				}
				$i_="";
				if ($this->nb_i) {
					$empty=false;
					while($indexint=@pmb_mysql_fetch_object($this->i_query)) {
						$pair_impair = $parity % 2 ? "even" : "odd";
						if (!$id_[$indexint->indexint_id]) {
								$temp = new indexint($indexint->indexint_id);
								$display = htmlentities($temp->name." - ".$temp->comment,ENT_QUOTES,$charset);
								if($temp->has_notices()) {
									$notice_count_sql = "SELECT count(*) FROM notices WHERE indexint = ".$temp->indexint_id;
									$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
									$link = "<td><a href='".$this->base_url."&aut_id=".$temp->indexint_id."&aut_type=indexint&etat=aut_search'>$display</a></td><td>".$notice_count."</td>";
								}
								else {
									$empty=true;
									if ($show_empty) $link = "<td>$display</td><td></td>"; else $link="";
								}
								if ($link) {
									$i_.="<tr class=\"$pair_impair\">$link</tr>";
									$parity += 1;
								}
						} else $this->nb_i--;
					}
					$i_="<br /><strong>".$msg['indexint_catal_title']." ".$msg["searcher_descr_indexint"]." : ".sprintf($msg["searcher_results"],$this->nb_i)."</strong><hr /><table>".$i_;
					$i_.="</table>";
					if (($empty)&&(!$show_empty)) $i_.="<a href='#' onClick=\"document.store_search.show_empty.value=1; document.store_search.page.value=0; document.store_search.submit(); return false;\">".$msg["searcher_indexint_empty_results"]."</a>";
					print $i_;
				}
		}
	}

	public function notice_list_common($title) {
		global $begin_result_liste;
		global $end_result_liste;
		global $msg;
		global $pmb_allow_external_search;
		global $load_tablist_js;
		$research=$title;
		$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
		if (isset($this->auto_postage_form)) {
		    $research.="&nbsp;&nbsp;".$this->auto_postage_form;
		}
		print "<div class='othersearchinfo'>$research</div>";
		print $begin_result_liste;
		$load_tablist_js=1;
		//Affichage des liens paniers et impression
		if ($this->rec_history) {
		    if ((($this->etat=='first_search')&&($this->is_first_page()))||($this->direct)) {
		        if (isset($_SESSION["session_history"]) && is_countable($_SESSION["session_history"])) {
                    $current=count($_SESSION["session_history"]);
		        } else {
		            $current=false;
		        }
		    } else {
				$current=$_SESSION["CURRENT"];
		    }
			if ($current!==false) {
				print $this->get_display_icons($current, 1);
			}
		}

		print $this->get_current_search_map(1);

		print $this->get_display_records_list();

		// fin de liste
		print $end_result_liste;
	}

	public function aut_notice_list() {
		global $msg;
		global $charset;
		global $aut_type;
		global $search_subject,$search_indexint;

		if ($this->direct) {
		    $human = array();
		    if ($search_subject) {
		        $human[]="<b>".$msg["histo_subject"]."</b> ".htmlentities(stripslashes($search_subject),ENT_QUOTES,$charset);
		    }
		    if ($search_indexint) {
		        $human[]="<b>".$msg["histo_indexint"]."</b> ".htmlentities(stripslashes($search_indexint),ENT_QUOTES,$charset);
		    }
			$this->human_query=implode(", ",$human);
			$this->human_aut_query=implode(", ",$human);
		}
		switch ($aut_type) {
			case "indexint":
				$temp = new indexint($this->id);
				$display = "<b>".$msg["searcher_indexint"]."</b>&nbsp;".htmlentities($temp->name." - ".$temp->comment,ENT_QUOTES,$charset);
				$this->human_notice_query=$display;
				break;
			case "categ":
				$display = "<b>".$msg["searcher_categ"]."</b>&nbsp;";
				$temp = new category($this->id);
				if($temp->voir_id) {
					$cr=$temp->catalog_form;
					$temp = new category($temp->voir_id);
					$display.=htmlentities($cr,ENT_QUOTES,$charset)." -> <i>".htmlentities($temp->catalog_form,ENT_QUOTES,$charset)."@</i>";
				} else {
							$display.=htmlentities($temp->catalog_form,ENT_QUOTES,$charset);
				}
				$this->human_notice_query=$display;
				break;
		}
		$this->notice_list_common($display);
	}

	public function rec_env() {
		global $msg;
		switch ($this->etat) {
				case 'first_search':
				    if ($this->is_first_page()) {
						$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]=$this->base_url;
						$_POST["etat"]="";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]=array();
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]=array();
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$this->human_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$msg["355"];
					}
					if ($this->is_first_page()) {
					    $_POST["page"]=1;
					    $this->page=1;
					}
					if (($this->first_search_result==AUT_LIST)&&($_SESSION["CURRENT"]!==false)) {
						$_POST["etat"]="first_search";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]["HUMAN_QUERY"]=$this->human_aut_query;
					}
					if (($this->first_search_result==NOTICE_LIST)&&($_SESSION["CURRENT"]!==false)) {
						$_POST["etat"]="first_search";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["HUMAN_QUERY"]=$this->human_notice_query;
					}
					break;
				case 'aut_search':
					if(!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
					if(isset($_SESSION["CURRENT"])) {
						$_SESSION["CURRENT"]=intval($_SESSION["CURRENT"]);
					}
					if(!is_int($_SESSION["CURRENT"])) {
						$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
					}
					if ($this->direct) {
						$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]=$this->base_url;
					//	$_POST["etat"]="";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]=array();
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]=array();
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$this->human_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["HUMAN_TITLE"]=$msg["335"];
					}
					if ($this->is_first_page()) {
					    $_POST["page"]=1;
					    $this->page=1;
					}
					if (($_SESSION["CURRENT"]!==false) && (is_int($_SESSION["CURRENT"]))) {
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['HUMAN_QUERY']=$this->human_notice_query;
					}

					break;
		}
		$_SESSION["last_required"]=false;
	}

	public static function convert_simple_multi($id_champ) {
		global $search;

		$search=array();
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_indexint_id"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_indexint_id"];
			$op_="EQ";
			$search[0]="f_2";
		} elseif ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_indexint"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_indexint"];
			$op_="EXACT";
			$search[0]="f_2";
		} elseif ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_subject"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_subject"];
			$op_="EQ";
			$search[0]="f_1";
		} elseif ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"]) {
			switch ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_type"]) {
				case "indexint":
					$search[0]="f_2";
					break;
				case "categ":
					$search[0]="f_1";
					break;
			}
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];
			$op_="EQ";
		}

		//opérateur
    	$op="op_0_".$search[0];
    	global ${$op};
    	${$op}=$op_;

    	//contenu de la recherche
    	$field="field_0_".$search[0];
    	$field_=array();
    	$field_[0]=$valeur_champ;
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

	public static function convert_simple_multi_unimarc($id_champ) {
		global $search;

		$search=array();
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_indexint_id"]) {
			$indexint_id=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_indexint_id"];
			//Recherche de l'indexation
			$requete="select indexint_name from indexint where indexint_id=".$indexint_id;
			$r_indexint=pmb_mysql_query($requete);
			if (@pmb_mysql_num_rows($r_indexint)) {
				$valeur_champ=pmb_mysql_result($r_indexint,0,0);
			}
			$op_="BOOLEAN";
			$search[0]="f_2";
		} elseif ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_indexint"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_indexint"];
			$op_="BOOLEAN";
			$search[0]="f_2";
		} elseif ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_subject"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["search_subject"];
			$op_="BOOLEAN";
			$search[0]="f_1";
		} elseif ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"]) {
			switch ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_type"]) {
				case "indexint":
					$search[0]="f_2";
					//Recherche de l'indexation
					$indexint_id=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];
					$requete="select indexint_name from indexint where indexint_id=".$indexint_id;
					$r_indexint=pmb_mysql_query($requete);
					if (@pmb_mysql_num_rows($r_indexint)) {
						$valeur_champ=pmb_mysql_result($r_indexint,0,0);
					}
					break;
				case "categ":
					$search[0]="f_1";
					//Recherche de la catégorie
					$categ_id=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];
					$requete="select libelle_categorie from categories where num_noeud=".$categ_id;
					$r_cat=pmb_mysql_query($requete);
					if (@pmb_mysql_num_rows($r_cat)) {
						$valeur_champ=pmb_mysql_result($r_cat,0,0);
					}
					break;
			}
			$op_="BOOLEAN";
		}

		//opérateur
    	$op="op_0_".$search[0];
    	global ${$op};
    	${$op}=$op_;

    	//contenu de la recherche
    	$field="field_0_".$search[0];
    	$field_=array();
    	$field_[0]=$valeur_champ;
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

class searcher_publisher extends searcher {
	public $p_query;
	public $c_query;
	public $s_query;
	public $nb_p;
	public $nb_c;
	public $nb_s;
	public $t_query;

	public function show_form() {
		global $search_form_editeur,$browser_editeur,$browser_url;

		$search_form_editeur=str_replace("!!base_url!!",$this->base_url,$search_form_editeur);
		$browser_editeur=str_replace("!!browser_url!!",$browser_url,$browser_editeur);
		print $search_form_editeur.$browser_editeur;
	}

	public function make_first_search() {
		global $search_ed;
		global $msg;
		global $browser,$browser_url,$search_form_editeur;

		$aq=new analyse_query(stripslashes($search_ed),0,0,1,1);
		if (!$aq->error) {
				$this->nbresults=0;

				//Recherche dans les éditeurs
				$rq_p_c=$aq->get_query_count("publishers","ed_name","index_publisher","ed_id");
				$this->nb_p=@pmb_mysql_result(@pmb_mysql_query($rq_p_c),0,0);
				if ($this->nb_p) {
					$rq_p=$aq->get_query("publishers","ed_name","index_publisher","ed_id");
					$this->p_query=@pmb_mysql_query($rq_p);
				}
				//Recherche des collections
				$rq_c_c=$aq->get_query_count("collections","collection_name","index_coll","collection_id");
				$this->nb_c=@pmb_mysql_result(@pmb_mysql_query($rq_c_c),0,0);
				if ($this->nb_c) {
					$rq_c=$aq->get_query("collections","collection_name","index_coll","collection_id");
					$this->c_query=@pmb_mysql_query($rq_c);
				}
				//Recherche des sous collections
				$rq_s_c=$aq->get_query_count("sub_collections","sub_coll_name","index_sub_coll","sub_coll_id");
				$this->nb_s=@pmb_mysql_result(@pmb_mysql_query($rq_s_c),0,0);
				if ($this->nb_s) {
					$rq_s=$aq->get_query("sub_collections","sub_coll_name","index_sub_coll","sub_coll_id");
					$this->s_query=@pmb_mysql_query($rq_s);
				}
				if (($this->nb_p+$this->nb_c+$this->nb_s)==0) {
					$search_form_editeur=str_replace("!!base_url!!",$this->base_url,$search_form_editeur);
					print $search_form_editeur;
					error_message($msg["searcher_no_result"],$msg["searcher_no_result_desc"]);
					$browser=str_replace("!!browser_url!!",$browser_url,$browser);
					print $browser;
					return;
				} else return AUT_LIST;
		} else {
				$this->show_error($aq->current_car,$aq->input_html,$aq->error_message);
		}
	}

	public function make_aut_search() {
		global $aut_type,$nb_per_page_a_search;
		global $acces_j;

		if ($nb_per_page_a_search) $this->nb_per_page=$nb_per_page_a_search; else $this->nb_per_page=3;

		switch ($aut_type) {
				case "publisher":
					$requete_count = "select count(distinct notice_id) from notices ";
					$requete_count.= $acces_j;
					$requete_count.= "where (ed1_id='".$this->id."' or ed2_id='".$this->id."') ";

					$requete = "select distinct notice_id from notices ";
					$requete.= $acces_j;
					$requete.= "where (ed1_id='".$this->id."' or ed2_id='".$this->id."') ";
//					$requete.= "order by index_serie,tnvol,index_sew limit ".($this->get_start_page()).",".$this->nb_per_page;
					break;

				case "collection":
					$requete_count = "select count(distinct notice_id) from notices ";
					$requete_count.= $acces_j;
					$requete_count.= "where coll_id='".$this->id."' ";

					$requete = "select distinct notice_id from notices ";
					$requete.= $acces_j;
					$requete.= "where coll_id='".$this->id."' ";
//					$requete.= "order by index_serie,tnvol,index_sew limit ".($this->get_start_page()).",".$this->nb_per_page;
					break;

				case "subcoll":
					$requete_count = "select count(distinct notice_id) from notices ";
					$requete_count.= $acces_j;
					$requete_count.= "where subcoll_id='".$this->id."' ";

					$requete = "select distinct notice_id from notices ";
					$requete.= $acces_j;
					$requete.= "where subcoll_id='".$this->id."' ";
//					$requete.= "order by index_serie,tnvol,index_sew limit ".($this->get_start_page()).",".$this->nb_per_page;
					break;

		}
		$this->nbresults=pmb_mysql_result(@pmb_mysql_query($requete_count),0,0);
		$this->t_query=pmb_mysql_query($requete);
		$this->nbepage=ceil($this->nbresults/$this->nb_per_page);
		$this->text_query=$requete;
	}

	public function store_search() {
		global $search_ed;
		global $show_empty;
		global $charset;
		$champs="<input type='hidden' name='search_ed' value='".htmlentities(stripslashes($search_ed),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='show_empty' value='".$show_empty."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function aut_store_search() {
		global $charset,$aut_type;
		$champs="<input type='hidden' name='aut_id' value='".htmlentities(stripslashes($this->id),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='aut_type' value='".htmlentities(stripslashes($aut_type),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function aut_list() {
		global $msg,$charset;
		global $search_ed;

		$this->human_query="<b>".$msg["356"]." </b> ".htmlentities(stripslashes($search_ed),ENT_QUOTES,$charset);
		$this->human_aut_query=$this->human_query;

		$pair_impair = "";
		$parity = 0;
		if ($this->nb_p) {
				print "<strong>".$msg["searcher_publisher"]." : ".sprintf($msg["searcher_results"],$this->nb_p)."</strong><hr /><table>";
				while ($p=@pmb_mysql_fetch_object($this->p_query)) {
					$pair_impair = $parity % 2 ? "even" : "odd";
					$temp=new editeur($p->ed_id);
					$notice_count_sql = "SELECT count(*) FROM notices WHERE ed1_id = ".$p->ed_id." OR ed2_id = ".$p->ed_id;
					$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
					print "<tr class=\"".$pair_impair."\"><td><a href='".$this->base_url."&etat=aut_search&aut_type=publisher&aut_id=".$p->ed_id."'>".htmlentities($temp->display,ENT_QUOTES,$charset)."</a>";
					if($temp->web) {
						print "&nbsp;<a href=\"".$temp->web."\" target=\"_web\">";
						print "<img src='".get_url_icon('globe.gif')."' style='border:0px;' class='align_top'></a>";
					}
					print "</td><td>$notice_count</td></tr>\n";
					$parity++;
				}
				print "</table>\n";
		}
		if ($this->nb_c) {
				print "<strong>".$msg["searcher_coll"]." : ".sprintf($msg["searcher_results"],$this->nb_c)."</strong><hr /><table>";
				while ($c=@pmb_mysql_fetch_object($this->c_query)) {
					$pair_impair = $parity % 2 ? "even" : "odd";
					$temp=new collection($c->collection_id);
					$notice_count_sql = "SELECT count(*) FROM notices WHERE coll_id = ".$c->collection_id;
					$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
					print "<tr class=\"".$pair_impair."\"><td><a href='".$this->base_url."&etat=aut_search&aut_type=collection&aut_id=".$c->collection_id."'>".htmlentities($temp->display,ENT_QUOTES,$charset)."</a></td><td>$notice_count</td></tr>\n";
					$parity++;
				}
				print "</table>\n";
		}
		if ($this->nb_s) {
				print "<strong>".$msg["searcher_subcoll"]." : ".sprintf($msg["searcher_results"],$this->nb_s)."</strong><hr /><table>";
				while ($s=@pmb_mysql_fetch_object($this->s_query)) {
					$pair_impair = $parity % 2 ? "even" : "odd";
					$temp=new subcollection($s->sub_coll_id);
					$notice_count_sql = "SELECT count(*) FROM notices WHERE subcoll_id = ".$s->sub_coll_id;
					$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
					print "<tr class=\"".$pair_impair."\"><td><a href='".$this->base_url."&etat=aut_search&aut_type=subcoll&aut_id=".$s->sub_coll_id."'>".$temp->display."</a></td><td>$notice_count</td></tr>\n";
					$parity++;
				}
				print "</table>\n";
		}
	}

	public function notice_list_common($title) {
		global $begin_result_liste;
		global $end_result_liste;
		global $msg;
		global $pmb_allow_external_search;
		global $load_tablist_js;
		$research=$title;
		$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
		print "<div class='othersearchinfo'>$research</div>";
		print $begin_result_liste;
		$load_tablist_js=1;
		//Affichage des liens paniers et impression
		if ($this->rec_history) {
			$current=$_SESSION["CURRENT"];
			if ($current!==false) {
				$tri_id_info = $_SESSION["tri"] ? "&sort_id=".$_SESSION["tri"] : "";
				print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare".$tri_id_info."','print',500, 600, -2, -2, 'scrollbars=yes,menubar=0,resizable=yes'); w.focus(); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare','print', 500, 600, -2, -2, 'scrollbars=yes,menubar=0'); return false;\"><img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
				print "&nbsp;<a href='#' onClick=\"openPopUp('./download.php?current_download=$current&action_download=download_prepare".$tri_id_info."','download'); return false;\"><img src='".get_url_icon('upload_docnum.gif')."' style='border:0px' class='center' alt=\"".$msg["docnum_download"]."\" title=\"".$msg["docnum_download"]."\"/></a>";
				if ($pmb_allow_external_search) print "&nbsp;<a href='catalog.php?categ=search&mode=7&from_mode=3&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'><img src='".get_url_icon('external_search.png')."' style='border:0px' class='center' alt=\"".$msg["connecteurs_external_search_sources"]."\"/></a>";

				print $this->get_display_icon_sort();
			}
		}

		print $this->get_current_search_map(2);

		print $this->get_display_records_list();

		// fin de liste
		print $end_result_liste;
	}

	public function aut_notice_list() {
		global $msg;
		global $charset;
		global $aut_type;

		switch ($aut_type) {
				case "publisher":
					$temp = new editeur($this->id);
					$display = "<b>".$msg["searcher_publisher"]."</b>&nbsp;".htmlentities($temp->display,ENT_QUOTES,$charset);
					$this->human_notice_query=$display;
					break;
				case "collection":
					$display = "<b>".$msg["searcher_coll"]."</b>&nbsp;";
					$temp = new collection($this->id);
					$display.= htmlentities($temp->display,ENT_QUOTES,$charset);
					$this->human_notice_query=$display;
					break;
				case "subcoll":
					$display = "<b>".$msg["searcher_subcoll"]."</b>&nbsp;";
					$temp = new subcollection($this->id);
					$display.=$temp->display;
					$this->human_notice_query=$display;
					break;
		}
		$this->notice_list_common($display);
	}

	public function rec_env() {
		global $msg;
		$this->page = intval($this->page);
		switch ($this->etat) {
				case 'first_search':
				    if ($this->is_first_page()) {
						$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]=$this->base_url;
						$_POST["etat"]="";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]=array();
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]=array();
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$this->human_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$msg["356"];
					}
					if ($this->is_first_page()) {
					    $_POST["page"]=1;
					    $this->page=1;
					}
					if (($this->first_search_result==AUT_LIST)&&($_SESSION["CURRENT"]!==false)) {
						$_POST["etat"]="first_search";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]["HUMAN_QUERY"]=$this->human_aut_query;
					}
					if (($this->first_search_result==NOTICE_LIST)&&($_SESSION["CURRENT"]!==false)) {
						$_POST["etat"]="first_search";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["HUMAN_QUERY"]=$this->human_notice_query;
					}
					break;
				case 'aut_search':
					if(!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
					if(isset($_SESSION["CURRENT"])) {
						$_SESSION["CURRENT"]=intval($_SESSION["CURRENT"]);
					}
					if(!is_int($_SESSION["CURRENT"])) {
						$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
					}
					if (($_SESSION["CURRENT"]!==false) && (is_int($_SESSION["CURRENT"]))) {
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['HUMAN_QUERY']=$this->human_notice_query;
					}
					break;
		}
		$_SESSION["last_required"]=false;
	}

	public static function convert_simple_multi($id_champ) {
		global $search;

		$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];
		$op_="EQ";

		$search=array();
		switch ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_type"]) {
			case "publisher":
				$search[0]="f_3";
			break;
			case "collection":
				$search[0]="f_4";
			break;
			case "subcoll":
				$search[0]="f_5";
			break;
		}

		//opérateur
    	$op="op_0_".$search[0];
    	global ${$op};
    	${$op}=$op_;

    	//contenu de la recherche
    	$field="field_0_".$search[0];
    	$field_=array();
    	$field_[0]=$valeur_champ;
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

	public static function convert_simple_multi_unimarc($id_champ) {
		global $search;

		$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];
		$op_="BOOLEAN";

		$search=array();
		switch ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_type"]) {
			case "publisher":
				$search[0]="f_3";
				//Recherche de l'éditeur
				$publisher_id=$valeur_champ;
				$requete="select ed_name from publishers where ed_id=".$publisher_id;
				$r_pub=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_pub)) {
					$valeur_champ=pmb_mysql_result($r_pub,0,0);
				}
			break;
			case "collection":
				$search[0]="f_4";
				//Recherche de l'indexation
				$coll_id=$valeur_champ;
				$requete="select collection_name from collections where collection_id=".$coll_id;
				$r_coll=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_coll)) {
					$valeur_champ=pmb_mysql_result($r_coll,0,0);
				}
			break;
			case "subcoll":
				$search[0]="f_5";
				//Recherche de la sous-collection
				$subcoll_id=$valeur_champ;
				$requete="select sub_coll_name from sub_collections where sub_coll_id=".$subcoll_id;
				$r_subcoll=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_subcoll)) {
					$valeur_champ=pmb_mysql_result($r_subcoll,0,0);
				}
			break;
		}

		//opérateur
    	$op="op_0_".$search[0];
    	global ${$op};
    	${$op}=$op_;

    	//contenu de la recherche
    	$field="field_0_".$search[0];
    	$field_=array();
    	$field_[0]=$valeur_champ;
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

class searcher_titre_uniforme extends searcher {
	public $p_query;
	public $c_query;
	public $s_query;
	public $nb_p;
	public $nb_c;
	public $nb_s;
	public $t_query;

	public function show_form() {
		global $search_form_titre_uniforme,$browser_titre_uniforme,$browser_url;

		$search_form_titre_uniforme=str_replace("!!base_url!!",$this->base_url,$search_form_titre_uniforme);
		$browser_titre_uniforme=str_replace("!!browser_url!!",$browser_url,$browser_titre_uniforme);
		print $search_form_titre_uniforme.$browser_titre_uniforme;
	}

	public function make_first_search() {
		global $search_tu;
		global $msg;
		global $browser,$browser_url,$search_form_titre_uniforme;

		//Recherche dans les titres uniformes
		$searcher_authorities_titres_uniformes = searcher_factory::get_searcher("titres_uniformes", "",stripslashes($search_tu));
		// 			$searcher_authorities_authors = new searcher_authorities_authors(stripslashes($author_query));
		$aq=new analyse_query(stripslashes($search_tu),0,0,1,1);
		if (!$aq->error) {
			$this->nbresults=0;

			$this->nb_tu=$searcher_authorities_titres_uniformes->get_nb_results();
			if ($this->nb_tu) {
				$this->tu_query=pmb_mysql_query('select num_object as tu_id from authorities where id_authority in (select id_authority from ('.$searcher_authorities_titres_uniformes->get_full_query().')as uni) and type_object = 7');
				return AUT_LIST;
			} else {
				$search_form_titre_uniforme=str_replace("!!base_url!!",$this->base_url,$search_form_titre_uniforme);
				print $search_form_titre_uniforme;
				error_message($msg["searcher_no_result"],$msg["searcher_no_result_desc"]);
				$browser=str_replace("!!browser_url!!",$browser_url,$browser);
				print $browser;
				return;
			}
		} else {
			$this->show_error($aq->current_car,$aq->input_html,$aq->error_message);
		}
	}

	public function make_aut_search() {
		global $aut_type,$nb_per_page_a_search;
		global $acces_j;

		if ($nb_per_page_a_search) $this->nb_per_page=$nb_per_page_a_search; else $this->nb_per_page=3;
		switch ($aut_type) {
			case "titre_uniforme":
				$requete_count = "select count(distinct ntu_num_notice) from notices_titres_uniformes, notices ";
				$requete_count.= $acces_j;
				$requete_count.= "where ntu_num_notice=notice_id and ntu_num_tu='".$this->id."' ";

				$requete = "select distinct notice_id from notices_titres_uniformes, notices ";
				$requete.= $acces_j;
				$requete.= "where ntu_num_notice=notice_id and ntu_num_tu='".$this->id."' ";
				$requete.= "order by index_serie,tnvol,index_sew limit ".($this->get_start_page()).",".$this->nb_per_page;
			break;
		}
		$this->nbresults=@pmb_mysql_result(@pmb_mysql_query($requete_count),0,0);
		$this->t_query=@pmb_mysql_query($requete);
		$this->nbepage=ceil($this->nbresults/$this->nb_per_page);
		$this->text_query=$requete;
	}

	public function store_search() {
		global $search_ed;
		global $show_empty;
		global $charset;
		$champs="<input type='hidden' name='search_ed' value='".htmlentities(stripslashes($search_ed),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='show_empty' value='".$show_empty."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function aut_store_search() {
		global $charset,$aut_type;
		$champs="<input type='hidden' name='aut_id' value='".htmlentities(stripslashes($this->id),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='aut_type' value='".htmlentities(stripslashes($aut_type),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function aut_list() {
		global $msg,$charset;
		global $search_tu;
		$this->human_query="<b>".$msg["356"]." </b> ".htmlentities(stripslashes($search_tu),ENT_QUOTES,$charset);
		$this->human_aut_query=$this->human_query;

		$pair_impair = "";
		$parity = 0;
		if ($this->nb_tu) {
			print "<strong>".$msg["search_by_titre_uniforme"]." : ".sprintf($msg["searcher_results"],$this->nb_tu)."</strong><hr /><table>";
			while (($p=@pmb_mysql_fetch_object($this->tu_query))) {
				$pair_impair = $parity % 2 ? "even" : "odd";
				$temp=new titre_uniforme($p->tu_id);
				$notice_count_sql = "SELECT count(*) FROM notices_titres_uniformes WHERE ntu_num_tu = ".$p->tu_id ;
				$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
				print "<tr class=\"".$pair_impair."\"><td><a href='".$this->base_url."&etat=aut_search&aut_type=titre_uniforme&aut_id=".$p->tu_id."'>".htmlentities($temp->get_isbd(),ENT_QUOTES,$charset)."</a>";

				print "</td><td>$notice_count</td></tr>\n";
				$parity++;
			}
			print "</table>\n";
		}
	}

	public function notice_list_common($title) {
		global $begin_result_liste;
		global $end_result_liste;
		global $msg;
		global $pmb_allow_external_search;
		global $load_tablist_js;
		$research=$title;
		$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
		print "<div class='othersearchinfo'>$research</div>";
		print $begin_result_liste;
		$load_tablist_js=1;
		//Affichage des liens paniers et impression
		if ($this->rec_history) {
			$current=$_SESSION["CURRENT"];
			if ($current!==false) {
				$tri_id_info = $_SESSION["tri"] ? "&sort_id=".$_SESSION["tri"] : "";
				print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare".$tri_id_info."','print',500, 600, -2, -2, 'scrollbars=yes,menubar=0,resizable=yes'); w.focus(); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare','print', 500, 600, -2, -2, 'scrollbars=yes,menubar=0'); return false;\"><img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
				print "&nbsp;<a href='#' onClick=\"openPopUp('./download.php?current_download=$current&action_download=download_prepare".$tri_id_info."','download'); return false;\"><img src='".get_url_icon('upload_docnum.gif')."' style='border:0px' class='center' alt=\"".$msg["docnum_download"]."\" title=\"".$msg["docnum_download"]."\"/></a>";
				if ($pmb_allow_external_search) print "&nbsp;<a href='catalog.php?categ=search&mode=7&from_mode=3&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'><img src='".get_url_icon('external_search.png')."' style='border:0px' class='center' alt=\"".$msg["connecteurs_external_search_sources"]."\"/></a>";
				print $this->get_display_icon_sort();
			}
		}
 		print $this->get_current_search_map(9);

 		print $this->get_display_records_list();

		// fin de liste
		print $end_result_liste;
	}

	public function aut_notice_list() {
		global $msg;
		global $charset;
		global $aut_type;

		switch ($aut_type) {
			case "titre_uniforme":
				$temp = new titre_uniforme($this->id);
				$display = "<b>".$msg["search_by_titre_uniforme"]."</b>&nbsp;".htmlentities($temp->name,ENT_QUOTES,$charset);
				$this->human_notice_query=$display;
			break;
		}
		$this->notice_list_common($display);
	}

	public function rec_env() {
		global $msg;
		global $memo_tempo_table_to_rebuild;

		switch ($this->etat) {
			case 'first_search':
			    if ($this->is_first_page()) {
					$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]=$this->base_url;
					$_POST["etat"]="";
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['POST']=$_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['GET']=$_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]=array();
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]=array();
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$this->human_query;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$msg["356"];
				}
				if ($this->is_first_page()) {
				    $_POST["page"]=1;
				    $this->page=1;
				}
				if (($this->first_search_result==AUT_LIST)&&($_SESSION["CURRENT"]!==false)) {
					$_POST["etat"]="first_search";
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['URI']=$this->base_url;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['POST']=$_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['GET']=$_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['PAGE']=intval($this->page);
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]["HUMAN_QUERY"]=$this->human_aut_query;
				}
				if (($this->first_search_result==NOTICE_LIST)&&($_SESSION["CURRENT"]!==false)) {
					$_POST["etat"]="first_search";
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_LIST_QUERY']=$memo_tempo_table_to_rebuild;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=intval($this->page);
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["HUMAN_QUERY"]=$this->human_notice_query;
				}
			break;
			case 'aut_search':
				if(!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
				if(isset($_SESSION["CURRENT"])) {
					$_SESSION["CURRENT"]=intval($_SESSION["CURRENT"]);
				}
				if(!is_int($_SESSION["CURRENT"])) {
					$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
				}
				if (($_SESSION["CURRENT"]!==false) && (is_int($_SESSION["CURRENT"]))) {
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=intval($this->page);
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_LIST_QUERY']=$memo_tempo_table_to_rebuild;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['HUMAN_QUERY']=$this->human_notice_query;
				}
			break;
		}
		$_SESSION["last_required"]=false;
	}

	public static function convert_simple_multi($id_champ) {
		global $search;

		$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];
		$op_="EQ";

		$search=array();
		switch ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_type"]) {
			case "titre_uniforme":
				$search[0]="f_3";//!!!!!!!!! a modifier
			break;
		}
		//opérateur
    	$op="op_0_".$search[0];
    	global ${$op};
    	${$op}=$op_;

    	//contenu de la recherche
    	$field="field_0_".$search[0];
    	$field_=array();
    	$field_[0]=$valeur_champ;
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

	public static function convert_simple_multi_unimarc($id_champ) {
		global $search;

		$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];
		$op_="BOOLEAN";

		$search=array();
		switch ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_type"]) {
			case "titre_uniforme":
				$search[0]="f_3";
				//Recherche de l'éditeur
				$tu_id=$valeur_champ;
				$requete="select tu_name from titres_uniformes where tu_id=".$tu_id;
				$r_pub=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_pub)) {
					$valeur_champ=pmb_mysql_result($r_pub,0,0);
				}
			break;
		}

		//opérateur
    	$op="op_0_".$search[0];
    	global ${$op};
    	${$op}=$op_;

    	//contenu de la recherche
    	$field="field_0_".$search[0];
    	$field_=array();
    	$field_[0]=$valeur_champ;
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


class searcher_serie extends searcher {
	public $p_query;
	public $c_query;
	public $s_query;
	public $nb_p;
	public $nb_c;
	public $nb_s;
	public $t_query;

	public function show_form() {}

	public function show_error($car,$input,$error_message) {}

	public function make_first_search() {}

	public function make_aut_search() {
		global $aut_type,$nb_per_page_a_search;
		global $acces_j;

		if ($nb_per_page_a_search) $this->nb_per_page=$nb_per_page_a_search; else $this->nb_per_page=3;

		switch($aut_type){
			case 'tit_serie':
				$requete_count = "select count(distinct notice_id) from notices ";
				$requete_count.= $acces_j;
				$requete_count.= "where index_serie in (select serie_index from series where serie_id='".$this->id."' ) ";

				$requete = "select distinct notice_id from notices ";
				$requete.= $acces_j;
				$requete.= "where index_serie in (select serie_index from series where serie_id='".$this->id."' ) ";
//				$requete.= "order by index_serie,tnvol,index_sew limit ".($this->get_start_page()).",".$this->nb_per_page;
		}
		$this->nbresults=@pmb_mysql_result(@pmb_mysql_query($requete_count),0,0);
		$this->t_query=@pmb_mysql_query($requete);
		$this->nbepage=ceil($this->nbresults/$this->nb_per_page);
		$this->text_query=$requete;
	}

	public function store_search() {
		global $search_ed;
		global $show_empty;
		global $charset;
		$champs="<input type='hidden' name='search_ed' value='".htmlentities(stripslashes($search_ed),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='show_empty' value='".$show_empty."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function aut_store_search() {
		global $charset,$aut_type;
		$champs="<input type='hidden' name='aut_id' value='".htmlentities(stripslashes($this->id),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='aut_type' value='".htmlentities(stripslashes($aut_type),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function aut_list() {}

	public function notice_list_common($title) {
		global $begin_result_liste;
		global $end_result_liste;
		global $msg;
		global $pmb_allow_external_search;
		global $load_tablist_js;
		$research=$title;
		$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
		print "<div class='othersearchinfo'>$research</div>";
		print $begin_result_liste;
		$load_tablist_js=1;
		//Affichage des liens paniers et impression
		if ($this->rec_history) {
			$current=$_SESSION["CURRENT"];
			if ($current!==false) {
				$tri_id_info = $_SESSION["tri"] ? "&sort_id=".$_SESSION["tri"] : "";
				print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare".$tri_id_info."','print',500, 600, -2, -2, 'scrollbars=yes,menubar=0,resizable=yes'); w.focus(); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare','print', 500, 600, -2, -2, 'scrollbars=yes,menubar=0'); return false;\"><img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
				print "&nbsp;<a href='#' onClick=\"openPopUp('./download.php?current_download=$current&action_download=download_prepare".$tri_id_info."','download'); return false;\"><img src='".get_url_icon('upload_docnum.gif')."' style='border:0px' class='center' alt=\"".$msg["docnum_download"]."\" title=\"".$msg["docnum_download"]."\"/></a>";
				if ($pmb_allow_external_search) print "&nbsp;<a href='catalog.php?categ=search&mode=7&from_mode=3&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'><img src='".get_url_icon('external_search.png')."' style='border:0px' class='center' alt=\"".$msg["connecteurs_external_search_sources"]."\"/></a>";
				print $this->get_display_icon_sort();
			}
		}
 		print $this->get_current_search_map(3);

 		print $this->get_display_records_list();

		// fin de liste
		print $end_result_liste;
	}

	public function aut_notice_list() {
		$this->notice_list_common('');
	}

	public function rec_env() {
		global $msg;
		global $memo_tempo_table_to_rebuild;

		switch ($this->etat) {
				case 'first_search':
				    if ($this->is_first_page()) {
						$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]=$this->base_url;
						$_POST["etat"]="";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]=array();
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]=array();
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$this->human_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$msg["356"];
					}
					if ($this->is_first_page()) {
					    $_POST["page"]=1;
					    $this->page=1;
					}
					if (($this->first_search_result==AUT_LIST)&&($_SESSION["CURRENT"]!==false)) {
						$_POST["etat"]="first_search";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]["HUMAN_QUERY"]=$this->human_aut_query;
					}
					if (($this->first_search_result==NOTICE_LIST)&&($_SESSION["CURRENT"]!==false)) {
						$_POST["etat"]="first_search";
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_LIST_QUERY']=$memo_tempo_table_to_rebuild;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["HUMAN_QUERY"]=$this->human_notice_query;
					}
					break;
				case 'aut_search':
					if(!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
					if(isset($_SESSION["CURRENT"])) {
						$_SESSION["CURRENT"]=intval($_SESSION["CURRENT"]);
					}
					if(!is_int($_SESSION["CURRENT"])) {
						$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
					}
					if (($_SESSION["CURRENT"]!==false) && (is_int($_SESSION["CURRENT"]))) {
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_LIST_QUERY']=$memo_tempo_table_to_rebuild;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
						$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['HUMAN_QUERY']=$this->human_notice_query;
					}
					break;
		}
		$_SESSION["last_required"]=false;
	}

	public static function convert_simple_multi($id_champ) {}

	public static function convert_simple_multi_unimarc($id_champ) {}
}

class searcher_authperso extends searcher {
	public $p_query;
	public $c_query;
	public $s_query;
	public $nb_p;
	public $nb_c;
	public $nb_s;
	public $t_query;

	public function show_form() {
		global $search_form_authperso,$browser_authperso,$browser_url;
		global $msg;
		global $info_authpersos,$id_authperso;

		$authperso_seach_title=str_replace("!!name!!",$info_authpersos[$id_authperso]['name'],$msg["search_by_authperso"]);
		$search_form_authperso=str_replace("!!authperso_search_title!!",$authperso_seach_title,$search_form_authperso);
		$search_form_authperso=str_replace("!!base_url!!",$this->base_url,$search_form_authperso);
		$browser_authperso=str_replace("!!browser_url!!",$browser_url,$browser_authperso);
		print $search_form_authperso.$browser_authperso;
	}

	public function make_first_search() {
		global $search_authperso;
		global $msg;
		global $browser,$browser_url,$search_form_authperso;
		global $info_authpersos,$id_authperso;
		global $mode;

		$aq=new analyse_query(stripslashes($search_authperso),0,0,1,1);
		if (!$aq->error) {
			$this->nbresults=0;
			//Recherche dans les authperso
			$members=$aq->get_query_members("authperso_authorities","authperso_infos_global","authperso_index_infos_global","id_authperso_authority");
			$clause= "where ".$members["where"] ." and authperso_authority_authperso_num=".($mode-1000);
			$rq_authperso_count="select count(1) FROM authperso_authorities $clause ";

			$this->nb_authperso=@pmb_mysql_result(@pmb_mysql_query($rq_authperso_count),0,0);

			if ($this->nb_authperso) {
				$rq_authperso="select id_authperso_authority FROM authperso_authorities $clause ";
				$this->authperso_query=@pmb_mysql_query($rq_authperso);
				return AUT_LIST;
			}else {
				$authperso_seach_title=str_replace("!!name!!",$info_authpersos[$id_authperso]['name'],$msg["search_by_authperso"]);
				$search_form_authperso=str_replace("!!authperso_search_title!!",$authperso_seach_title,$search_form_authperso);
				$search_form_authperso=str_replace("!!base_url!!",$this->base_url,$search_form_authperso);
				print $search_form_authperso;
				error_message($msg["searcher_no_result"],$msg["searcher_no_result_desc"]);
				$browser=str_replace("!!browser_url!!",$browser_url,$browser);
				print $browser;
				return;
			}
		} else {
			$this->show_error($aq->current_car,$aq->input_html,$aq->error_message);
		}
	}

	public function make_aut_search() {
		global $aut_type,$nb_per_page_a_search;
		global $acces_j;
		global $aut_id;

		if ($nb_per_page_a_search) $this->nb_per_page=$nb_per_page_a_search; else $this->nb_per_page=3;

		switch ($aut_type) {
			case "authperso":
				$requete_count = "select count(distinct notice_authperso_notice_num) from notices_authperso, notices ";
				$requete_count.= $acces_j;
				$requete_count.= "where notice_authperso_notice_num=notice_id and notice_authperso_authority_num='".$aut_id."' ";

				$requete = "select distinct notice_id from notices_authperso, notices ";
				$requete.= $acces_j;
				$requete.= "where notice_authperso_notice_num=notice_id and notice_authperso_authority_num='".$aut_id."' ";
				//				$requete.= "order by index_serie,tnvol,index_sew limit ".($this->get_start_page()).",".$this->nb_per_page;
				break;
		}
		$this->nbresults=pmb_mysql_result(@pmb_mysql_query($requete_count),0,0);
		$this->t_query=pmb_mysql_query($requete);
		$this->nbepage=ceil($this->nbresults/$this->nb_per_page);
		$this->text_query=$requete;
	}

	public function store_search() {
		global $search_ed;
		global $show_empty;
		global $charset;
		$champs="<input type='hidden' name='search_ed' value='".htmlentities(stripslashes($search_ed),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='show_empty' value='".$show_empty."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function aut_store_search() {
		global $charset,$aut_type;
		$champs="<input type='hidden' name='aut_id' value='".htmlentities(stripslashes($this->id),ENT_QUOTES,$charset)."'/>";
		$champs.="<input type='hidden' name='aut_type' value='".htmlentities(stripslashes($aut_type),ENT_QUOTES,$charset)."'/>";
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function aut_list() {
		global $msg,$charset;
		global $search_authperso,$mode;

		$authperso=new authperso($mode-1000);
		$info=$authperso->get_data();

		$this->human_query="<b>".$info['name']." </b> ".htmlentities(stripslashes($search_authperso),ENT_QUOTES,$charset);
		$this->human_aut_query=$this->human_query;
		$pair_impair = "";
		$parity = 0;
		if ($this->nb_authperso) {
			$authperso_seach_title=str_replace("!!name!!",$info['name'],$msg["search_by_authperso"]);
			print "<strong>".$authperso_seach_title." : ".sprintf($msg["searcher_results"],$this->nb_authperso)."</strong><hr /><table>";
			while (($p=@pmb_mysql_fetch_object($this->authperso_query))) {
				$pair_impair = $parity % 2 ? "even" : "odd";
				$notice_count_sql = "SELECT count(*) FROM notices_authperso WHERE notice_authperso_authority_num = ".$p->id_authperso_authority ;
				$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);


				$isbd = authperso::get_isbd($p->id_authperso_authority);
				print "<tr class=\"".$pair_impair."\"><td><a href='".$this->base_url."&etat=aut_search&aut_type=authperso&aut_id=".$p->id_authperso_authority."'>".htmlentities($isbd,ENT_QUOTES,$charset)."</a>";
				print "</td><td>$notice_count</td></tr>\n";
				$parity++;
			}
			print "</table>\n";
		}
	}

	public function notice_list_common($title) {
		global $begin_result_liste;
		global $end_result_liste;
		global $msg;
		global $pmb_allow_external_search;
		global $load_tablist_js,$mode;

		$research=$title;
		$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
		print "<div class='othersearchinfo'>$research</div>";
		print $begin_result_liste;
		$load_tablist_js=1;
		//Affichage des liens paniers et impression
		if ($this->rec_history) {
			$current=$_SESSION["CURRENT"];
			if ($current!==false) {
				$tri_id_info = $_SESSION["tri"] ? "&sort_id=".$_SESSION["tri"] : "";
				print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare".$tri_id_info."','print',500, 600, -2, -2, 'scrollbars=yes,menubar=0,resizable=yes'); w.focus(); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare','print', 500, 600, -2, -2, 'scrollbars=yes,menubar=0'); return false;\"><img src='".get_url_icon('print.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
				print "&nbsp;<a href='#' onClick=\"openPopUp('./download.php?current_download=$current&action_download=download_prepare".$tri_id_info."','download'); return false;\"><img src='".get_url_icon('upload_docnum.gif')."' style='border:0px' class='center' alt=\"".$msg["docnum_download"]."\" title=\"".$msg["docnum_download"]."\"/></a>";
				if ($pmb_allow_external_search) print "&nbsp;<a href='catalog.php?categ=search&mode=$mode&from_mode=3&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'><img src='".get_url_icon('external_search.png')."' style='border:0px' class='center' alt=\"".$msg["connecteurs_external_search_sources"]."\"/></a>";
				print $this->get_display_icon_sort();
			}
		}
 		print $this->get_current_search_map(1000);

 		print $this->get_display_records_list();

		// fin de liste
		print $end_result_liste;
	}

	public function aut_notice_list() {
		global $charset;
		global $aut_type,$mode,$aut_id;

		switch ($aut_type) {
			case "authperso":
				$authperso=new authperso($mode-1000);
				$info=$authperso->get_data();
				$isbd = authperso::get_isbd($aut_id);
				$display = "<b>".$info['name']."</b>&nbsp;".htmlentities($isbd,ENT_QUOTES,$charset);
				$this->human_notice_query=$display;
				break;
		}
		$this->notice_list_common($display);
	}

	public function rec_env() {
		global $info_authpersos,$id_authperso;
		global $memo_tempo_table_to_rebuild;

		$this->page = intval($this->page);
		switch ($this->etat) {
			case 'first_search':
			    if ($this->is_first_page()) {
					$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]=$this->base_url;
					$_POST["etat"]="";
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['POST']=$_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['GET']=$_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]=array();
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]=array();
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$this->human_query;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$info_authpersos[$id_authperso]['name'];
				}
				if ($this->is_first_page()) {
				    $_POST["page"]=1;
				    $this->page=1;
				}
				if (($this->first_search_result==AUT_LIST)&&($_SESSION["CURRENT"]!==false)) {
					$_POST["etat"]="first_search";
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['URI']=$this->base_url;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['POST']=$_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['GET']=$_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]['PAGE']=$this->page;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]["HUMAN_QUERY"]=$this->human_aut_query;
				}
				if (($this->first_search_result==NOTICE_LIST)&&($_SESSION["CURRENT"]!==false)) {
					$_POST["etat"]="first_search";
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_LIST_QUERY']=$memo_tempo_table_to_rebuild;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["HUMAN_QUERY"]=$this->human_notice_query;
				}
				break;
			case 'aut_search':
				if(!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
				if(isset($_SESSION["CURRENT"])) {
					$_SESSION["CURRENT"]=intval($_SESSION["CURRENT"]);
				}
				if(!is_int($_SESSION["CURRENT"])) {
					$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
				}
				if (($_SESSION["CURRENT"]!==false) && (is_int($_SESSION["CURRENT"]))) {
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_LIST_QUERY']=$memo_tempo_table_to_rebuild;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['HUMAN_QUERY']=$this->human_notice_query;
				}
				break;
		}
		$_SESSION["last_required"]=false;
	}

	public static function convert_simple_multi($id_champ) {
		global $search;

		$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];
		$op_="EQ";

		$search=array();
		switch ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_type"]) {
			case "authperso":
				$search[0]="f_3";//!!!!!!!!! a modifier
				break;
		}
		//opérateur
		$op="op_0_".$search[0];
		global ${$op};
		${$op}=$op_;

		//contenu de la recherche
		$field="field_0_".$search[0];
		$field_=array();
		$field_[0]=$valeur_champ;
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

	public static function convert_simple_multi_unimarc($id_champ) {
		global $search;

		$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];
		$op_="BOOLEAN";

		$search=array();
		switch ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_type"]) {
			case "authperso":
				$search[0]="f_3";
				//Recherche de l'éditeur
				$tu_id=$valeur_champ;
				$requete="select tu_name from titres_uniformes where tu_id=".$tu_id;
				$r_pub=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_pub)) {
					$valeur_champ=pmb_mysql_result($r_pub,0,0);
				}
				break;
		}

		//opérateur
		$op="op_0_".$search[0];
		global ${$op};
		${$op}=$op_;

		//contenu de la recherche
		$field="field_0_".$search[0];
		$field_=array();
		$field_[0]=$valeur_champ;
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


class searcher_map extends searcher {
	public $t_query;


	public function show_form() {
		global $msg;
		global $charset,$lang;
		global $search_form_map;
		global $all_query,$typdoc_query, $statut_query, $docnum_query, $pmb_indexation_docnum_allfields, $pmb_indexation_docnum;
		global $categ_query,$thesaurus_auto_postage_search,$auto_postage_query;
		global $thesaurus_concepts_active,$concept_query, $thesaurus_concepts_autopostage, $concepts_autopostage_query;
		global $map_echelle_query,$map_projection_query,$map_ref_query,$map_equinoxe_query;
		global $pmb_map_size_search_edition;
		global $pmb_map_base_layer_type;
		global $pmb_map_base_layer_params;
		global $map_emprises_query, $pmb_map_bounding_box;

		if(!isset($typdoc))$typdoc='';
		// on commence par créer le champ de sélection de document
		// récupération des types de documents utilisés.
		$query = "SELECT count(typdoc), typdoc ";
		$query .= "FROM notices where typdoc!='' GROUP BY typdoc";
		$result = @pmb_mysql_query($query);
		$toprint_typdocfield = "  <option value=''>$msg[tous_types_docs]</option>\n";
		$doctype = new marc_list('doctype');
		while (($rt = pmb_mysql_fetch_row($result))) {
			$obj[$rt[1]]=1;
			$qte[$rt[1]]=$rt[0];
		}
		foreach ($doctype->table as $key=>$libelle){
			if (isset($obj[$key]) && ($obj[$key]==1)){
				$toprint_typdocfield .= "  <option ";
				$toprint_typdocfield .= " value='$key'";
				if ($typdoc == $key) $toprint_typdocfield .=" selected='selected' ";
				$toprint_typdocfield .= ">".htmlentities($libelle." (".$qte[$key].")",ENT_QUOTES, $charset)."</option>\n";
			}
		}

		// récupération des statuts de documents utilisés.
		$query = "SELECT count(statut), id_notice_statut, gestion_libelle ";
		$query .= "FROM notices, notice_statut where id_notice_statut=statut GROUP BY id_notice_statut order by gestion_libelle";
		$result = pmb_mysql_query($query);
		$toprint_statutfield = "  <option value=''>$msg[tous_statuts_notice]</option>\n";
		while ($obj = @pmb_mysql_fetch_row($result)) {
			$toprint_statutfield .= "  <option value='$obj[1]'";
			if ($statut_query==$obj[1]) $toprint_statutfield.=" selected";
			$toprint_statutfield .=">".htmlentities($obj[2]."  (".$obj[0].")",ENT_QUOTES, $charset)."</OPTION>\n";
		}

		$search_form_map = str_replace("!!typdocfield!!", $toprint_typdocfield, $search_form_map);
		$search_form_map = str_replace("!!statutfield!!", $toprint_statutfield, $search_form_map);
		$search_form_map = str_replace("!!all_query!!", htmlentities(stripslashes($all_query ?? ''),ENT_QUOTES, $charset),  $search_form_map);
		$search_form_map = str_replace("!!categ_query!!", htmlentities(stripslashes($categ_query ?? ''),ENT_QUOTES, $charset),  $search_form_map);

		if($thesaurus_concepts_active){
			$search_form_map = str_replace("!!concept_query!!", htmlentities(stripslashes($concept_query ?? ''),ENT_QUOTES, $charset),  $search_form_map);
		}
		// map
		$layer_params = json_decode($pmb_map_base_layer_params,true);
		$baselayer =  "baseLayerType: dojox.geo.openlayers.BaseLayerType.".$pmb_map_base_layer_type;
		if(!empty($layer_params) && count($layer_params)){
			if($layer_params['name']) $baselayer.=",baseLayerName:\"".$layer_params['name']."\"";
			if($layer_params['url']) $baselayer.=",baseLayerUrl:\"".$layer_params['url']."\"";
			if($layer_params['options']) $baselayer.=",baseLayerOptions:".json_encode($layer_params['options']);
		}

		$size=explode("*",$pmb_map_size_search_edition);
		if(count($size)!=2) {
			$map_size="width:800px; height:480px;";
		} else {
			if (is_numeric($size[0])) $size[0].= 'px';
			if (is_numeric($size[1])) $size[1].= 'px';
			$map_size= "width:".$size[0]."; height:".$size[1].";";
		}

		$initialFit = '';
		if(!$map_emprises_query){
			$map_emprises_query = array();
			if( $pmb_map_bounding_box) {
            	$map_bounding_box = $pmb_map_bounding_box;
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
		$r="<div id='map_search' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='".$baselayer.",mode:\"search_criteria\",hiddenField:\"map_emprises_query\",initialFit:".json_encode($initialFit,true).",searchHolds:".json_encode($map_holds,true)."'></div>";

		$search_form_map = str_replace("!!map!!", $r,  $search_form_map);

		//champs maps
		$requete = "SELECT map_echelle_id, map_echelle_name FROM map_echelles ORDER BY map_echelle_name ";
		$projections=gen_liste($requete,"map_echelle_id","map_echelle_name","map_echelle_query","",$map_echelle_query,0,"",0,$msg['map_echelle_vide']);
		$search_form_map=str_replace("!!map_echelle_list!!",$projections,$search_form_map);

		$requete = "SELECT map_projection_id, map_projection_name FROM map_projections ORDER BY map_projection_name ";
		$projections=gen_liste($requete,"map_projection_id","map_projection_name","map_projection_query","",$map_projection_query,0,"",0,$msg['map_projection_vide']);
		$search_form_map=str_replace("!!map_projection_list!!",$projections,$search_form_map);

		$requete = "SELECT map_ref_id, map_ref_name FROM map_refs ORDER BY map_ref_name ";
		$refs=gen_liste($requete,"map_ref_id","map_ref_name","map_ref_query","",$map_ref_query,0,"",0,$msg['map_ref_vide']);
		$search_form_map=str_replace("!!map_ref_list!!",$refs,$search_form_map);

		$search_form_map=str_replace("!!map_equinoxe_value!!",$map_equinoxe_query ?? '',$search_form_map);

		$checkbox="";
		if($thesaurus_auto_postage_search){
			$checkbox = "
			<div class='colonne'>
				<div class='row'>
					<input type='checkbox' !!auto_postage_checked!! id='auto_postage_query' name='auto_postage_query'/><label for='auto_postage_query'>".$msg["search_autopostage_check"]."</label>
				</div>
			</div>";
			$checkbox = str_replace("!!auto_postage_checked!!",   (($auto_postage_query) ? 'checked' : ''),  $checkbox);
		}
		$search_form_map = str_replace("!!auto_postage!!",   $checkbox,  $search_form_map);

		$checkbox_concepts_autopostage = "";
		if($thesaurus_concepts_autopostage){
			$checkbox_concepts_autopostage = "
			<div class='colonne'>
				<div class='row'>
					<input type='checkbox' !!concepts_autopostage_checked!! id='concepts_autopostage_query' name='concepts_autopostage_query'/><label for='concepts_autopostage_query'>".$msg["search_concepts_autopostage_check"]."</label>
				</div>
			</div>";
			$checkbox_concepts_autopostage = str_replace("!!concepts_autopostage_checked!!",   (($concepts_autopostage_query) ? 'checked' : ''),  $checkbox_concepts_autopostage);
		}
		$search_form_map = str_replace("!!concepts_autopostage!!", $checkbox_concepts_autopostage, $search_form_map);

		if($pmb_indexation_docnum){
			$checkbox = "<div class='colonne'>
			<div class='row'>
			<input type='checkbox' !!docnum_query_checked!! id='docnum_query' name='docnum_query'/><label for='docnum_query'>$msg[docnum_indexation]</label>
			</div>
			</div>";
			$checkbox = str_replace("!!docnum_query_checked!!",   (($pmb_indexation_docnum_allfields || $docnum_query) ? 'checked' : ''),  $checkbox);
			$search_form_map = str_replace("!!docnum_query!!",   $checkbox,  $search_form_map);
		} else $search_form_map = str_replace("!!docnum_query!!", '' ,  $search_form_map);
		$search_form_map = str_replace("!!base_url!!",     $this->base_url,$search_form_map);
		print pmb_bidi($search_form_map);
	}

	public function make_first_search() {
		global $msg,$charset,$lang;
		global $all_query,$typdoc_query, $statut_query, $etat, $docnum_query;
		global $categ_query,$thesaurus_auto_postage_search, $auto_postage_query;
		global $nb_per_page_a_search;
		global $class_path;
		global $pmb_default_operator;
		global $acces_j;
		global $thesaurus_concepts_active,$concept_query;
		global $map_echelle_query,$map_projection_query,$map_ref_query,$map_equinoxe_query,$map_emprises_query;

		if ($nb_per_page_a_search) $this->nb_per_page=$nb_per_page_a_search; else $this->nb_per_page=3;

		$restrict = '';
		$queries = array();
		//limitation aux notices avec emprises, dans les notices, les categ, les concepts
		$restriction_emprise = "and (
            (notices.notice_id IN (select distinct map_emprise_obj_num FROM map_emprises where map_emprise_type=11))
            or (notices.notice_id IN (select distinct notcateg_notice from notices_categories join map_emprises on map_emprises.map_emprise_obj_num = notices_categories.num_noeud where map_emprises.map_emprise_type=2))
            or (notices.notice_id IN (select distinct num_object from index_concept join map_emprises on map_emprise_type = 10 where type_object = 1 and map_emprise_obj_num = num_concept))
        )";
		//$restrict.= "and notice_id IN (select distinct map_emprise_obj_num FROM map_emprises)";
		$no_results = false;

		if(!$concept_query && !$categ_query && !$map_equinoxe_query && !$map_ref_query && !$map_projection_query && !$map_emprises_query && !$all_query && !$map_echelle_query) $all_query="*";
		//tous les champs
		if($all_query){
			//TODO Searcher all_fields, pas le temps de le faire là...
			// Recherche sur tous les champs (index global) uniquement :
			$aq=new analyse_query(stripslashes($all_query),0,0,1,1);
			$aq2=new analyse_query(stripslashes($all_query));
			if (!$aq->error) {
				$aq1=new analyse_query(stripslashes($all_query),0,0,1,1);
				$members1=$aq1->get_query_members("notices","index_wew","index_sew","notice_id",$restrict);
				global $pmb_title_ponderation;
				$pert1="+".$members1["select"]."*".$pmb_title_ponderation;
				$members=$aq->get_query_members("notices_global_index","infos_global","index_infos_global","num_notice");
				$members2=$aq2->get_query_members("notices_global_index","infos_global","index_infos_global","num_notice");
				if (($members2["where"]!="()")&&($pmb_default_operator)) {
						$where_term="(".$members["where"]." or ".$members2["where"].")";
				} else {
					$where_term=$members["where"];
				}
				if($docnum_query && $all_query!='*'){
					//Si on a activé la recherche dans les docs num
					//On traite les notices
					$members_num_noti = $aq2->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_notice","",0,0,true);
					$members_num_bull = $aq2->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin","",0,0,true);

					$join = "(
					select tc.notice_id, sum(tc.pert) as pert, tc.typdoc from (
					(
						select notice_id, ".$members["select"]."+".$members1["select"]." as pert,typdoc
						from notices join notices_global_index on num_notice=notice_id $acces_j
						where ".$members["where"]." $restrict
					)
					union
					(
					select notice_id, ".$members_num_noti["select"]." as pert,typdoc
					from notices join explnum on explnum_notice=notice_id $acces_j
					where  ".$members_num_noti["where"]." $restrict
					)
					union
					(
					select if(num_notice,num_notice,bulletin_notice) as notice_id, ".$members_num_bull["select"]." as pert,typdoc
					from explnum join bulletins on explnum_bulletin=bulletin_id ,notices $acces_j
					where bulletin_notice=notice_id and ".$members_num_bull["where"]." $restrict
					)
					)as tc group by notice_id
					)";
					$requete_count = "select count(distinct notice_id) from ($join) as union_table";
					$requete="select uni.notice_id, sum(pert) as pert  from ($join) as uni join notices n on n.notice_id=uni.notice_id group by uni.notice_id order by pert desc, index_serie, tnvol, index_sew ";

				} elseif($all_query=="*") {
					$restrict.= " and num_notice = notice_id ";
					$requete_count = "select count(1) from notices ";
					$requete_count.= $acces_j;
					$requete_count.= ", notices_global_index ";
					$requete_count.= "where ".$where_term." ";
					$requete_count.= $restrict;

					$requete = "select notice_id,100 as pert from notices ";
					$requete.= $acces_j;
					$requete.= ", notices_global_index ";
					$requete.= "where $where_term ";
					$requete.= $restrict." order by index_serie, tnvol, index_sew ";
				} else {
					$restrict.= " and num_notice = notice_id ";
					$requete_count = "select count(1) from notices ";
					$requete_count.= $acces_j;
					$requete_count.= ", notices_global_index ";
					$requete_count.= "where ".$where_term." ";
					$requete_count.= $restrict;

					$requete = "select notice_id,".$members["select"]."$pert1 as pert from notices ";
					$requete.= $acces_j;
					$requete.= ", notices_global_index ";
					$requete.= "where $where_term ";
					$requete.= $restrict." order by pert desc, index_serie, tnvol, index_sew ";
				}
				$queries[]=$requete;
			}
		}
		//pour la suite, avant de déclencher les recherches, on vérifie si la recherche est différente de celle tous les champs (on s'économise quelques requetes qui ne serviront à rien)
		//les concepts
		if($thesaurus_concepts_active && $concept_query && $concept_query != $all_query){
			$concept_searcher = searcher_factory::get_searcher("records", "concepts",stripslashes($concept_query));
			if($concept_searcher->get_nb_results()){
				$queries[]=$concept_searcher->get_full_query()." ";
			}else{
				$no_results =true;
			}
		}
		//catégorie
		if($categ_query && $categ_query != $all_query){
			$categ_searcher = searcher_factory::get_searcher("records", "categories",stripslashes($categ_query));
			if($categ_searcher->get_nb_results()){
				$queries[]=$categ_searcher->get_full_query()." ";
			}else{
					$no_results = true;
			}
		}
		//echelle
		if($map_echelle_query){
			//$queries[] = "select notice_id from notices where map_echelle_num=".$map_echelle_query." ";
			$queries[]= " select notice_id, 100 as pert from notices where map_echelle_num='".$map_echelle_query."' ";
		}
		//projection
		if($map_projection_query){
			$queries[]= " select notice_id, 100 as pert from notices where map_projection_num='".$map_projection_query."' ";
		}
		//ref
		if($map_ref_query){
			$queries[]= " select notice_id, 100 as pert from notices where map_ref_num='".$map_ref_query."' ";
		}
		//équinoxe
		if($map_equinoxe_query){
			$queries[]= " select notice_id, 100 as pert from notices where map_equinoxe='".$map_equinoxe_query."' ";
		}
		//map
		if($map_emprises_query){
			foreach($map_emprises_query as $map_emprise_query){
			    $restriction_emprise = '';
				//récupération des emprises de notices correspondantes
				$query_notice="select map_emprise_obj_num as notice_id, 100 as pert from map_emprises where map_emprise_type=11 and contains(geomfromtext('$map_emprise_query'),map_emprise_data) = 1 ";
				// dans les categ
				$query_categories = "select notcateg_notice as notice_id, 100 as pert from notices_categories join map_emprises on num_noeud = map_emprises.map_emprise_obj_num where map_emprise_type = 2 and contains(geomfromtext('$map_emprise_query'),map_emprise_data) = 1";
				// dans les concepts
				$query_concepts = "select num_object as notice_id, 100 as pert from index_concept join map_emprises on map_emprise_type = 10 and contains(geomfromtext('$map_emprise_query'), map_emprise_data) = 1 where type_object = 1 and map_emprise_obj_num = num_concept";

				$queries[] = "select * from ( $query_notice union $query_categories union $query_concepts ) as uni";//TODO-> faire le mapage et mettre le tout dans $queries...
			}
		}
		//on fait un et donc si un élément ne renvoi rien ,on s'embete pas avec les jointures...
		$restrict='';
		if ($no_results || !count($queries)) {
			$this->nbresults = 0;
			if ((!empty($typdoc_query) && !empty($typdoc_query[0])) || (!empty($statut_query) && !empty($statut_query[0]))) {
			    if (!empty($typdoc_query) && !empty($typdoc_query[0])) {
			        $restrict.= " and notices.typdoc in ('".implode("','", $typdoc_query)."') ";
			    }
			    if (!empty($statut_query) && !empty($statut_query[0])) {
			        $restrict.= " and notices.statut in ('".implode("','", $statut_query)."') ";
			    }
			} else {
				$restrict = "notice_id = 0";
			}
			$this->text_query = "select notice_id from notices where ".$restrict;
		} else {
			//TODO le tri sur la pertinance desc, titre devrait être automatique...
			$from = "";
			$select_pert = "";
			for ($i=0 ; $i<count($queries) ; $i++) {
				if ($i==0) {
					$from = "(".$queries[$i].") as t".$i;
					$select_pert = "t".$i.".pert";
				} else {
					$from.= " inner join (".$queries[$i].") as t".$i." on t".$i.".notice_id = t".($i-1).".notice_id";
					$select_pert.= " + t".$i.".pert";
				}
			}
			if (!empty($typdoc_query) && !empty($typdoc_query[0])) {
				$restrict.= " and notices.typdoc in ('".implode("','", $typdoc_query)."') ";
			}
			if (!empty($statut_query) && !empty($statut_query[0])) {
			    $restrict.= " and notices.statut in ('".implode("','", $statut_query)."') ";
			}
			$this->text_query = "select t0.notice_id, (".$select_pert.") as pert from ".$from." join notices on t0.notice_id = notices.notice_id ".$restriction_emprise.$restrict." group by t0.notice_id  order by pert desc, notices.index_sew ";
			$result = pmb_mysql_query($this->text_query);
			$this->nbresults = pmb_mysql_num_rows($result);
		}
		$this->nbepage = ceil($this->nbresults/$this->nb_per_page);

		return NOTICE_LIST;
	}

	public function store_search() {
		global $all_query,$typdoc_query, $statut_query,$categ_query,$docnum_query,$pmb_indexation_docnum;
		global $thesaurus_concepts_active,$concept_query, $thesaurus_concepts_autopostage, $concepts_autopostage_query;
		global $map_echelle_query,$map_projection_query,$map_ref_query,$map_equinoxe_query,$map_emprises_query;
		global $charset;
		$champs="<input type='hidden' name='all_query' value='".htmlentities(stripslashes($all_query),ENT_QUOTES,$charset)."'/>";
		if (!empty($typdoc_query) && count($typdoc_query)) {
    		foreach ($typdoc_query as $elt) {
    		    $champs.="<input type='hidden' name='typdoc_query[]' value='".htmlentities(stripslashes($elt),ENT_QUOTES,$charset)."'/>";
    		}
		}
		if (!empty($statut_query) && count($statut_query)) {
    		foreach ($statut_query as $elt) {
    		    $champs.="<input type='hidden' name='statut_query[]' value='".htmlentities(stripslashes($elt),ENT_QUOTES,$charset)."'/>";
    		}
    	}
		$champs.="<input type='hidden' name='categ_query' value='".htmlentities(stripslashes($categ_query),ENT_QUOTES,$charset)."'/>";
		if($thesaurus_concepts_active){
			$champs.="<input type='hidden' name='concept_query' value='".htmlentities(stripslashes($concept_query),ENT_QUOTES,$charset)."'/>";
			if ($thesaurus_concepts_autopostage) {
				$champs.="<input type='hidden' name='concepts_autopostage_query' value='".$concepts_autopostage_query."'/>";
			}
		}
		$champs.="<input type='hidden' name='map_echelle_query' value='".$map_echelle_query."'/>";
		$champs.="<input type='hidden' name='map_projection_query' value='".$map_projection_query."'/>";
		$champs.="<input type='hidden' name='map_ref_query' value='".$map_ref_query."'/>";
		$champs.="<input type='hidden' name='map_equinoxe_query' value='".htmlentities(stripslashes($map_equinoxe_query),ENT_QUOTES,$charset)."'/>";
		if ($pmb_indexation_docnum) {
			$champs.="<input type='hidden' name='docnum_query' value='".htmlentities(stripslashes($docnum_query ?? ''),ENT_QUOTES,$charset)."'/>";
		}
		if($map_emprises_query)
		foreach($map_emprises_query as $map_emprise_query){
			$champs.="<input type='hidden' name='map_emprises_query[]' value='".$map_emprise_query."'/>";
		}
		$this->store_form=str_replace("!!first_search_variables!!",$champs,$this->store_form);
		print $this->store_form;
	}

	public function notice_list_common($title) {
		global $begin_result_liste;
		global $end_result_liste;
		global $msg;
		global $all_query,$categ_query;
		global $pmb_allow_external_search;
		global $load_tablist_js;

		if ($this->nbresults) {
			$research=$title;
			$research .= " => ".sprintf($msg["searcher_results"],$this->nbresults);
			print pmb_bidi("<div class='othersearchinfo'>$research</div>");
			print $begin_result_liste;
			$load_tablist_js=1;
			//Affichage des liens paniers et impression
			if ($this->rec_history) {

			    if (($this->etat=='first_search')&&($this->is_first_page())) {
					$current=count($_SESSION["session_history"]);
				} else {
					$current=$_SESSION["CURRENT"];
				}
				if ($current!==false) {
					print $this->get_display_icons($current);
				}
			}

			print $this->get_current_search_map(11);

			print $this->get_display_records_list();

			// fin de liste
			print $end_result_liste;
		} else {
			$this->show_form();
			$cles="<strong>".$title."</strong>";
			if ($pmb_allow_external_search) $external="<a href='catalog.php?categ=search&mode=7&from_mode=0&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a>";
			error_message($msg[357], sprintf($msg["connecteurs_no_title"],$cles,$external), 0, "./catalog.php?categ=search&mode=0");
		}
	}

	public function notice_list() {
		global $msg;
		global $charset;
		global $all_query,$categ_query;
		global $thesaurus_concepts_active,$concept_query;
		global $map_echelle_query,$map_projection_query,$map_ref_query,$map_equinoxe_query,$map_emprises_query;

		$research = '';
		if($this->docnum){
			$libelle = " [".$msg['docnum_search_with']."]";
		} else $libelle ='';
		if ($all_query) {
			$research.="<b>".$msg['global_search'].$libelle."</b>&nbsp;".htmlentities(stripslashes($all_query),ENT_QUOTES,$charset);
		}
		if ($categ_query) {
			if ($research != "") $research .= ", ";
			$research .= "<b>{$msg["search_categorie_title"]}</b>&nbsp;".htmlentities(stripslashes($categ_query),ENT_QUOTES,$charset);
		}

		if ($thesaurus_concepts_active && $concept_query) {
			if ($research != "") $research .= ", ";
			$research.="<b>{$msg['search_concept_title']}</b>&nbsp;".htmlentities(stripslashes($concept_query),ENT_QUOTES,$charset);
		}

		if ($map_echelle_query) {
			$requete = "select map_echelle_name from map_echelles where map_echelle_id=".$map_echelle_query;
			$result = pmb_mysql_query($requete);
			if ($result) {
				if ($research != "") $research .= ", ";
				$research .= "<b>{$msg["map_echelle"]}</b>&nbsp;".htmlentities(pmb_mysql_result($result,0,"map_echelle_name"),ENT_QUOTES,$charset);
			}
		}
		if ($map_projection_query) {
			$requete = "select map_projection_name from map_projections where map_projection_id=".$map_projection_query;
			$result = pmb_mysql_query($requete);
			if ($result) {
				if ($research != "") $research .= ", ";
				$research .= "<b>{$msg["map_projection"]}</b>&nbsp;".htmlentities(pmb_mysql_result($result,0,"map_projection_name"),ENT_QUOTES,$charset);
			}
		}
		if ($map_ref_query) {
			$requete = "select map_ref_name from map_refs where map_ref_id=".$map_ref_query;
			$result = pmb_mysql_query($requete);
			if ($result) {
				if ($research != "") $research .= ", ";
				$research .= "<b>{$msg["map_ref"]}</b>&nbsp;".htmlentities(pmb_mysql_result($result,0,"map_ref_name"),ENT_QUOTES,$charset);
			}
		}
		if ($map_equinoxe_query) {
			if ($research != "") $research .= ", ";
			$research .= "<b>{$msg["map_equinoxe"]}</b>&nbsp;".htmlentities(stripslashes($map_equinoxe_query),ENT_QUOTES,$charset);
		}
		if ($map_emprises_query) {
			foreach($map_emprises_query as $map_emprise_query){
				if ($research != "") $research .= ", ";
				$research .= "<b>{$msg["map_emprises_query"]}</b>&nbsp;".htmlentities(stripslashes($map_emprise_query),ENT_QUOTES,$charset);
			}
		}
		$this->human_title=$msg["search_map"];
		$this->human_query=$research;
		$this->human_notice_query=$research;

		$this->notice_list_common($research);
	}

	public function rec_env() {
		global $msg;
		global $memo_tempo_table_to_rebuild;

		switch ($this->etat) {
			case 'first_search':
			    if ($this->is_first_page()) {
					$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]=$this->base_url;
					$_POST["etat"]="";
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['POST']=$_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]['GET']=$_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["AUT"]=array();
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]=array();
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$this->human_query;
					if(!$this->human_title)$this->human_title=$msg["354"];
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]=$this->human_title;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["DOCNUM_QUERY"]=$this->docnum;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["AUTO_POSTAGE_QUERY"]=$this->auto_postage_query;
				}
				if ($this->is_first_page()) {
				    $_POST["page"]=1;
				    $this->page=1;
				}
				if (($this->first_search_result==NOTICE_LIST)&&($_SESSION["CURRENT"]!==false)) {
					$_POST["etat"]="first_search";
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['URI']=$this->base_url;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['POST']=$_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['GET']=$_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_LIST_QUERY']=$memo_tempo_table_to_rebuild;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_QUERY']=$this->text_query;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['PAGE']=$this->page;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["HUMAN_QUERY"]=$this->human_notice_query;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["DOCNUM_QUERY"]=$this->docnum;
					$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["AUTO_POSTAGE_QUERY"]=$this->auto_postage_query;
				}
				break;
		}
		$_SESSION["last_required"]=false;
	}

	public static function convert_simple_multi($id_champ) {
		global $search;

		$x=0;
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["title_query"]) {
			$op_="BOOLEAN";
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["title_query"];

			$search[$x]="f_6";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_[0]=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			${$inter}="";

			//variables auxiliaires
			$fieldvar_="fieldvar_".$x."_".$search[$x];
			global ${$fieldvar_};
			${$fieldvar_}="";
			$fieldvar=${$fieldvar_};
			$x++;
		}
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["all_query"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["all_query"];
			$op_="BOOLEAN";

			$search[$x]="f_7";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_[0]=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			${$inter}="";

			//variables auxiliaires
			$fieldvar_="fieldvar_".$x."_".$search[$x];
			global ${$fieldvar_};
			$t=array();
			$t["is_num"][0]=$_SESSION["session_history"][$id_champ]["NOTI"]["DOCNUM_QUERY"];
			$t["ck_affiche"][0]=$_SESSION["session_history"][$id_champ]["NOTI"]["DOCNUM_QUERY"];
			${$fieldvar_}=$t;
			$fieldvar=${$fieldvar_};
			$x++;
		}
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["author_query"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["author_query"];

			$op_="BOOLEAN";
			$search[$x]="f_8";

			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_[0]=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			if ($x>0) {
				${$inter}="and";
			} else {
				${$inter}="";
			}
			//variables auxiliaires
			$fieldvar_="fieldvar_".$x."_".$search[$x];
			global ${$fieldvar_};
			${$fieldvar_}="";
			$fieldvar=${$fieldvar_};
			$x++;
		} else {
			if ($_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"]) {
				$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["GET"]["aut_id"];

				$op_="EQ";
				$search[$x]="f_8";
				//opérateur
				$op="op_".$x."_".$search[$x];
				global ${$op};
				${$op}=$op_;

				//contenu de la recherche
				$field="field_".$x."_".$search[$x];
				$field_=array();
				$field_[0]=$valeur_champ;
				global ${$field};
				${$field}=$field_;

				//opérateur inter-champ
				$inter="inter_".$x."_".$search[$x];
				global ${$inter};
				if ($x>0) {
					${$inter}="and";
				} else {
					${$inter}="";
				}

				//variables auxiliaires
				$fieldvar_="fieldvar_".$x."_".$search[$x];
				global ${$fieldvar_};
				${$fieldvar_}="";
				$fieldvar=${$fieldvar_};
				$x++;
			}
		}
		if (is_array($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["typdoc_query"]) && $_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["typdoc_query"][0]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["typdoc_query"];
			$op_="EQ";
			$search[$x]="f_9";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			if ($x>0) {
				${$inter}="and";
			} else {
				${$inter}="";
			}
			//variables auxiliaires
			$fieldvar_="fieldvar_".$x."_".$search[$x];
			global ${$fieldvar_};
			${$fieldvar_}="";
			$fieldvar=${$fieldvar_};
			$x++;
		}
		if (is_array($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["statut_query"]) && $_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["statut_query"][0]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["statut_query"];
			$op_="EQ";
			$search[$x]="f_10";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			if ($x>0) {
				${$inter}="and";
			} else {
				${$inter}="";
			}
			//variables auxiliaires
			$fieldvar_="fieldvar_".$x."_".$search[$x];
			global ${$fieldvar_};
			${$fieldvar_}="";
			$fieldvar=${$fieldvar_};
		}
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["map_emprises_query"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["map_emprises_query"];
			$op_="CONTAINS";
			$search[$x]="f_78";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			if ($x>0) {
				${$inter}="and";
			} else {
				${$inter}="";
			}
		}
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["map_echelle_query"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["map_echelle_query"];
			$op_="EQ";
			$search[$x]="f_74";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			if ($x>0) {
				${$inter}="and";
			} else {
				${$inter}="";
			}
		}
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["map_projection_query"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["map_projection_query"];
			$op_="EQ";
			$search[$x]="f_75";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			if ($x>0) {
				${$inter}="and";
			} else {
				${$inter}="";
			}
		}
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["map_ref_query"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["map_ref_query"];
			$op_="EQ";
			$search[$x]="f_76";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			if ($x>0) {
				${$inter}="and";
			} else {
				${$inter}="";
			}
		}
		if ($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["map_equinoxe_query"]) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["map_equinoxe_query"];
			$op_="BOOLEAN";
			$search[$x]="f_77";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			if ($x>0) {
				${$inter}="and";
			} else {
				${$inter}="";
			}
		}
		if (!empty($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["categ_query"])) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["categ_query"];
			$op_="EQ";
			$search[$x]="f_1";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_[0]=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			if ($x>0) {
				${$inter}="and";
			} else {
				${$inter}="";
			}
		}
		if (!empty($_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["concept_query"])) {
			$valeur_champ=$_SESSION["session_history"][$id_champ]["NOTI"]["POST"]["concept_query"];
			$op_="BOOLEAN";
			$search[$x]="f_1000";
			//opérateur
			$op="op_".$x."_".$search[$x];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_".$x."_".$search[$x];
			$field_=array();
			$field_[0]=$valeur_champ;
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_".$x."_".$search[$x];
			global ${$inter};
			if ($x>0) {
				${$inter}="and";
			} else {
				${$inter}="";
			}
		}
	}

}
?>