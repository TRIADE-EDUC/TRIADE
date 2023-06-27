<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_category.class.php,v 1.26 2018-11-27 09:44:05 ngantier Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/category.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($class_path."/authority.class.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/entities/entities_categories_controller.class.php");

global $autoindex_class;
if($autoindex_class) {
	require_once($class_path."/autoindex/".$autoindex_class.".class.php");
}

function parent_link($categ_id,$categ_see) {
	global $caller,$keep_tilde,$p1;
	global $charset;
	global $thesaurus_mode_pmb ;
	global $callback;
	
	if ($categ_see) $categ=$categ_see; else $categ=$categ_id;
	$tcateg =  new category($categ);
	
	if ($tcateg->commentaire) {
		$zoom_comment = "<div id='zoom_comment".$tcateg->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>".htmlentities($tcateg->commentaire,ENT_QUOTES, $charset)."</div>" ;
		$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display='none'; \"" ;
	} else {
		$zoom_comment = "" ;
		$java_comment = "" ;
	}
	if ($thesaurus_mode_pmb && $caller=='notice') $nom_tesaurus='['.$tcateg->thes->getLibelle().'] ' ;
		else $nom_tesaurus='' ;
	if($tcateg->not_use_in_indexation && ($caller == "notice")){
		$link= "<img src='".get_url_icon('interdit.gif')."' style='border:0px; margin:3px 3px'/>";
	}elseif(((!$tcateg->is_under_tilde) || $keep_tilde)){
		if($caller == "search_form"){
			$lib_final=$tcateg->libelle;
		}else{
			$lib_final=$nom_tesaurus.$tcateg->catalog_form;
		}
		$link="<a href=\"\" onclick=\"set_parent('$caller', '$tcateg->id', '".htmlentities(addslashes($lib_final),ENT_QUOTES, $charset)."','$callback','".$tcateg->thes->id_thesaurus."'); return false;\" $java_comment><span class='plus_terme'><span>+</span></span></a>$zoom_comment";
	}
	$visible=true;
	$r=array("VISIBLE"=>$visible,"LINK"=>$link);
	return $r;
}

class selector_category extends selector_authorities {
	
	protected $thesaurus_id;
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'categories';
	}

	public function proceed() {
		global $action;
		global $pmb_allow_authorities_first_page;
		global $search_type;
		global $user_input;
		
		$entity_form = '';
		switch($action){
		    case 'hierarchical_search':
		        $entity_form = $this->get_search_form();
				break;
			case 'terms_search':
				ob_start();
				print $this->get_search_form();
				global $thesaurus_categories_term_search_n_per_page;
				global $keep_tilde;
				
				$n_per_page=$thesaurus_categories_term_search_n_per_page;
				$id_thes = $this->get_thesaurus_id();
				$ts=new term_search("user_input","f_user_input",$n_per_page,static::get_base_url(),"term_show.php","term_search.php",$keep_tilde, $id_thes);
				print $ts->show_list_of_terms();
				$entity_form = ob_get_contents();
				ob_end_clean();
				break;
			case 'autoindex_search':
			    $entity_form = $this->get_search_form();
				break;
			case 'hierarchical_results_search':
			    $entity_form = $this->hierarchical_results_search();
				break;
			case 'terms_results_search':
			    $entity_form = $this->terms_results_search();
				break;
			case 'autoindex_results_search':
			    $entity_form = $this->display_autoindex_list();
				break;
			default:
				parent::proceed();
				break;
		}
		if ($entity_form) {
		    header("Content-Type: text/html; charset=UTF-8");
		    print  encoding_normalize::utf8_normalize($entity_form);
		}
	}
	
	protected function get_thesaurus_id() {
		global $caller, $dyn;
		global $id_thes_unique;
		global $perso_id, $id_thes;
	
		if(!isset($this->thesaurus_id)) {
			if($id_thes_unique>0) {
				$this->thesaurus_id=$id_thes_unique;
			} else{
				//recuperation du thesaurus session en fonction du caller
				switch ($caller) {
					case 'notice' :
						if($id_thes) $this->thesaurus_id = $id_thes;
						else $this->thesaurus_id = thesaurus::getNoticeSessionThesaurusId();
						if (!$perso_id) thesaurus::setNoticeSessionThesaurusId($this->thesaurus_id);
						break;
					case 'categ_form' :
						if($id_thes) $this->thesaurus_id = $id_thes;
						else $this->thesaurus_id = thesaurus::getSessionThesaurusId();
						if( $dyn!=2) thesaurus::setSessionThesaurusId($this->thesaurus_id);
						break;
					default :
						if($id_thes) $this->thesaurus_id = $id_thes;
						else $this->thesaurus_id = thesaurus::getSessionThesaurusId();
						thesaurus::setSessionThesaurusId($this->thesaurus_id);
						break;
				}
			}
		}
		return $this->thesaurus_id;
	}
	
	protected function get_thesaurus_selector() {
		global $msg, $charset;
		global $caller, $dyn;
		global $thesaurus_mode_pmb, $id_thes_unique;
		global $search_type;

		$id_thes = $this->get_thesaurus_id();
		
		$liste_thesaurus = thesaurus::getThesaurusList();
		
		$sel_thesaurus = '';
		if ($thesaurus_mode_pmb != 0 && !$id_thes_unique) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
			$sel_thesaurus = "<select class='saisie-20em' id='id_thes_" . $search_type . "' name='id_thes' ";
		
			//si on vient du form de categories, le choix du thesaurus n'est pas possible
			if($caller == 'categ_form' && $dyn!=2) {
				$sel_thesaurus.= "disabled ";
			}
			if($search_type != 'autoindex' && $search_type != 'hierarchy') {
				$sel_thesaurus.= "onchange = \"this.form.submit()\">" ;
			} else {
				$sel_thesaurus.= '>' ;
			}
			foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
				$sel_thesaurus.= "<option value='".$id_thesaurus."' "; ;
				if ($id_thesaurus == $id_thes) $sel_thesaurus.= " selected";
				$sel_thesaurus.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES,$charset)."</option>";
			}
			$sel_thesaurus.= "<option value=-1 ";
			if ($id_thes == -1) $sel_thesaurus.= "selected ";
			$sel_thesaurus.= ">".htmlentities($msg['thes_all'],ENT_QUOTES, $charset)."</option>";
			$sel_thesaurus.= "</select>&nbsp;";
		}
		return $sel_thesaurus;
	}
	
	protected function get_autoindex_form(){
		global $autoindex_class;
		if(!$autoindex_class) return;
		$autoindex=new $autoindex_class();
		return $autoindex->get_form();
	}
	
	protected function display_autoindex_list(){
		global $autoindex_class;
	
		if(!$autoindex_class) return;
		$autoindex=new $autoindex_class();
		return $autoindex->index_list();
	}
	
	protected function get_search_form() {
		global $msg, $charset;
		global $action;
		global $search_type;
		
		$sel_search_form = parent::get_search_form();
		$sel_search_form=str_replace("!!sel_thesaurus!!", $this->get_thesaurus_selector(),$sel_search_form);
		if($action == 'autoindex_search') {
			$sel_search_form=str_replace("!!sel_index_auto!!", $this->get_autoindex_form(),$sel_search_form);
		} else {
			$sel_search_form=str_replace("!!sel_index_auto!!", "",$sel_search_form);
		}
		return $sel_search_form;
	}
	
	protected function get_searcher_instance() {
		return searcher_factory::get_searcher('categories', '', $this->user_input);
	}
	
	protected function get_sel_search_form_name() {
		global $action;
		
		if($this->objects_type) {
			return "selector_".$this->objects_type."_".$action."_form";
		} else {
			return "selector_search_form";
		}
	}
	
	public function get_sel_search_form_template() {
		global $msg, $charset;
		global $action;
		
		$sel_search_form ="
			<form name='".$this->get_sel_search_form_name()."' method='post' action='".static::get_base_url()."'>
				!!sel_thesaurus!!
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				!!sel_index_auto!!
				<input type='submit' id='launch_".$action."_button' class='bouton_small' value='".$msg[142]."' />
			</form>
			<script type='text/javascript'>
				document.forms['".$this->get_sel_search_form_name()."'].elements['f_user_input'].focus();
			</script>
		";
		return $sel_search_form;
	}
	
	protected function get_sub_tabs(){
		global $autoindex_class;
		global $caller;
		
		$current_url = static::get_base_url();
		$current_url = str_replace('select.php?', 'ajax.php?module=selectors&', $current_url);
	
		$searcher_tab = $this->get_searcher_tabs_instance();
		return '
				<div id="widget-container"></div>
				<script type="text/javascript">
							require(["apps/pmb/form/category/FormCategorySelector", "dojo/dom"], function(FormCategorySelector, dom){
								new FormCategorySelector({doLayout: false, selectorURL:"'.$current_url.'", multicriteriaMode: "'.$searcher_tab->get_mode_multi_search_criteria().'", autoindex_class: "'.$autoindex_class.'", caller: "'.$caller.'"}, "widget-container");
							});
					   </script>
				';
	}
	
	protected function hierarchical_results_search() {
		global $msg, $charset;
		global $id2, $parent;
		global $lang;
		global $page;
		global $nb_per_page;
		global $keep_tilde;
		global $thesaurus_mode_pmb;
		global $caller, $callback;
		global $bouton_ajouter;
		
		$display = '';
		if(!$page) {
			$page = 1;
		}
		if(!$nb_per_page) $nb_per_page = 10;
		$debut = ($page-1)*$nb_per_page;
		
		$libelle_partiel=0;
		if($caller == 'search_form') {
			$libelle_partiel=1;
		}
		
		$id_thes = $this->get_thesaurus_id();
		$thes = new thesaurus($id_thes);
		
		if(!$this->nbr_lignes){
			$query = "SELECT SQL_CALC_FOUND_ROWS noeuds.id_noeud AS categ_id ";
		}else{
			$query = "SELECT noeuds.id_noeud AS categ_id ";
		}
		$query.= ",noeuds.num_thesaurus ";
		
		
		if($this->user_input){
			$aq=new analyse_query($this->user_input);
		}else{
			$aq=new analyse_query("*");
			if($id_thes != -1){
				if ($id2 == 0) {
					//creation, on affiche le thesaurus a partir de la racine
					$id_noeud = $thes->num_noeud_racine;
				} else {//modification, on affiche a partir du pere de id2
					if ($id2 == $parent) {
						$id_noeud = $id2;
					} else {
						if(noeuds::hasChild($id2)){
							$id_noeud = $id2;
						} else {
							$noeud = new noeuds($id2);
							$id_noeud = $noeud->num_parent;
						}
					}
				}
			}
		}
		if ($aq->error) {
			error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
			return;
		}
		
		if(($id_thes != -1) && ($thes->langue_defaut == $lang)){
			$members = $aq->get_query_members("categories", "libelle_categorie", "index_categorie", "num_noeud");
		
			if(!$this->user_input){
				$query.= ", categories.libelle_categorie AS index_categorie ";
			}else{
				$query.= ", categories.index_categorie AS index_categorie ";
				$query.= ", ".$members["select"]." AS pert ";
			}
		
			$query.= "FROM noeuds JOIN categories ON noeuds.id_noeud = categories.num_noeud AND  categories.langue='".$lang."'";
			$query.= "WHERE noeuds.num_thesaurus = '".$id_thes."' ";
			if(!$this->user_input){
				$query.= "AND noeuds.num_parent = '".$id_noeud."' ";
			}else{
				$query.= "AND (".$members["where"].") ";
			}
		
		
		}else{
			$members_catdef = $aq->get_query_members("catdef", "catdef.libelle_categorie", "catdef.index_categorie", "catdef.num_noeud");
			$members_catlg = $aq->get_query_members("catlg", "catlg.libelle_categorie", "catlg.index_categorie", "catlg.num_noeud");
		
			if(!$this->user_input){
				$query.= ", IF (catlg.num_noeud IS NULL, catdef.libelle_categorie, catlg.libelle_categorie) as index_categorie ";
			}else{
					
				$query.= ", IF (catlg.num_noeud IS NULL, catdef.index_categorie, catlg.index_categorie) as index_categorie ";
				$query.= ", IF (catlg.num_noeud IS NULL, (".$members_catdef["select"]."), (".$members_catlg["select"].") ) AS pert ";
			}
		
		
			if(($id_thes != -1)){//Je n'ai qu'un thésaurus mais langue du thésaurus != de langue de l'inteface
				$query.= "FROM noeuds JOIN categories AS catdef ON noeuds.id_noeud = catdef.num_noeud AND catdef.langue = '".$thes->langue_defaut."' ";
				$query.= "LEFT JOIN categories AS catlg ON catdef.num_noeud = catlg.num_noeud AND catlg.langue = '".$lang."' ";
				$query.= "WHERE noeuds.num_thesaurus = '".$id_thes."' ";
				if(!$this->user_input){
					$query.= "AND noeuds.num_parent = '".$id_noeud."' ";
				}else{
					$query.= "AND ( IF (catlg.num_noeud IS NULL, ".$members_catdef["where"].", ".$members_catlg["where"].") ) ";
				}
			}else{
				//Plusieurs thésaurus
				$query.= "FROM noeuds JOIN thesaurus ON thesaurus.id_thesaurus = noeuds.num_thesaurus ";
				$query.= "JOIN categories AS catdef ON noeuds.id_noeud = catdef.num_noeud AND catdef.langue = thesaurus.langue_defaut ";
				$query.= "LEFT JOIN categories AS catlg on catdef.num_noeud = catlg.num_noeud AND catlg.langue = '".$lang."' ";
				$query.= "WHERE 1 ";
				$query.= "AND ( IF (catlg.num_noeud IS NULL, ".$members_catdef["where"].", ".$members_catlg["where"].") ) ";
			}
		
		}
		
		$query.= "ORDER BY ";
		if($this->user_input){
			$query.= "pert DESC,";
		}
		$query.= " num_thesaurus, index_categorie ";
		$query.= "LIMIT ".$debut.",".$nb_per_page." ";
		
		$result = pmb_mysql_query($query);
		if(!$this->nbr_lignes){
			$qry = "SELECT FOUND_ROWS() AS NbRows";
			if($resnum = pmb_mysql_query($qry)){
				$this->nbr_lignes=pmb_mysql_result($resnum,0,0);
			}
		}
		
		if($this->nbr_lignes){
			$browser_top =	"<a href='".static::get_base_url()."&parent=".$thes->num_noeud_racine.'&id2=0&id_thes='.$id_thes."'><img src='".get_url_icon('top.gif')."' style='border:0px; margin:3px 3px' class='align_middle'></a>";
			$premier=true;
			$browser_header="";
			$browser_content="";
			while($cat = pmb_mysql_fetch_row($result)) {
				$tcateg =  new category($cat[0]);
					
				if(!$this->user_input && $premier){
					if(sizeof($tcateg->path_table) && $id_thes !=-1) {
						for($i=0; $i < sizeof($tcateg->path_table) - 1; $i++){
							$browser_header ? $browser_header .= '&gt;' : $browser_header = '';
							$browser_header .= "<a href='";
							$browser_header .= static::get_base_url();
							$browser_header .= "&parent=".$tcateg->path_table[$i]['id'];
							$browser_header .= '&id2='.$tcateg->path_table[$i]['id'];
							$browser_header .= '&id_thes='.$id_thes;
							$browser_header .= "'>";
							$browser_header .= $tcateg->path_table[$i]['libelle'];
							$browser_header .= "</a>";
						}
						$browser_header ? $browser_header .= '&gt;<strong id="categ_libelle_header">' : $browser_header = '<strong id="categ_libelle_header">';
						$browser_header .= $tcateg->path_table[sizeof($tcateg->path_table) - 1]['libelle'];
						$browser_header .= '</strong>';
						$bouton_ajouter=str_replace("!!id_aj!!",$tcateg->path_table[sizeof($tcateg->path_table) - 1]['id'],$bouton_ajouter);
					} else {
						$browser_header = "";
						$t = thesaurus::getByEltId($cat[0]);
						$bouton_ajouter=str_replace("!!id_aj!!",$t->num_noeud_racine,$bouton_ajouter);
					}
					$premier=false;
				}
				if (!$tcateg->is_under_tilde ||($tcateg->voir_id)||($keep_tilde)) {
					$not_use_in_indexation=$tcateg->not_use_in_indexation;
					$browser_content .= "<tr><td>";
		
					$authority = new authority(0,$tcateg->id, AUT_TABLE_CATEG);
					$browser_content .= $authority->get_display_statut_class_html();
		
					if($id_thes == -1 && $thesaurus_mode_pmb){
						$label_display = '['.htmlentities($tcateg->thes->libelle_thesaurus,ENT_QUOTES, $charset).']';
					} else {
						$label_display = '';
					}
					if($tcateg->voir_id) {
						$tcateg_voir = new category($tcateg->voir_id);
						$label_display .= "$tcateg->libelle -&gt;<i>".$tcateg_voir->catalog_form."@</i>";
						$id_=$tcateg->voir_id;
						$not_use_in_indexation=$tcateg_voir->not_use_in_indexation;
						if($libelle_partiel){
							$libelle_=$tcateg_voir->libelle;
						}else{
							$libelle_=$tcateg_voir->catalog_form;
						}
					} else {
						$id_=$tcateg->id;
						if($libelle_partiel){
							$libelle_=$tcateg->libelle;
						}else{
							$libelle_=$tcateg->catalog_form;
						}
						$label_display .= $tcateg->libelle;
					}
					if($tcateg->has_child) {
						$browser_content .= "<a href='".static::get_base_url()."&parent=".$tcateg->id."&id2=".$tcateg->id.'&id_thes='.$tcateg->thes->id_thesaurus."'>";//On mets le bon identifiant de thésaurus
						$browser_content .= "<img src='".get_url_icon('folderclosed.gif')."' style='border:0px; margin:3px 3px'/></a>";
					} else {
						$browser_content .= "<img src='".get_url_icon('doc.gif')."' style='border:0px; margin:3px 3px'/>";
					}
					if ($tcateg->commentaire) {
						$zoom_comment = "<div id='zoom_comment".$tcateg->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>".htmlentities($tcateg->commentaire,ENT_QUOTES, $charset)."</div>" ;
						$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display='none'; \"" ;
					} else {
						$zoom_comment = "" ;
						$java_comment = "" ;
					}
					if ($thesaurus_mode_pmb && $caller=='notice') $nom_tesaurus='['.$tcateg->thes->getLibelle().'] ' ;
					else $nom_tesaurus='' ;
					if($not_use_in_indexation && ($caller == "notice")){
						$browser_content .= "<img src='".get_url_icon('interdit.gif')."' style='border:0px; margin:3px 3px'/>&nbsp;";
						$browser_content .= $label_display;
						$browser_content .=$zoom_comment."\n";
						$browser_content .= "</td></tr>";
					}else{
						$browser_content .= "<a href='#' $java_comment onclick=\"set_parent('$caller', '$id_', '".htmlentities(addslashes($nom_tesaurus.$libelle_),ENT_QUOTES, $charset)."','$callback','".$tcateg->thes->id_thesaurus."')\">";
						$browser_content .= $label_display;
						$browser_content .= "</a>$zoom_comment\n";
						$browser_content .= "</td></tr>";
					}
				}
				// constitution de la page
			}
			//Création barre de navigation
			$nav_bar = aff_pagination ($this->get_link_pagination(), $this->nbr_lignes, $nb_per_page, $page, 10, false, true) ;
			
			$display .= "<br />
					<div class='row'>
						".$browser_top."
						".$browser_header."<hr />
					</div>
					<div class='row'>
						<table style='border:0px'>
							".$browser_content."
						</table>
					</div>".$nav_bar;
		} else {
			$display .= $msg["no_category_found"];
		}
		return $display;
	}
	
	protected function terms_results_search() {
		global $charset;
		global $term;
		global $first;
		global $id_thes;
		global $keep_tilde;
		
		$base_query = "history=".rawurlencode(stripslashes($term))."&history_thes=".rawurlencode(stripslashes($id_thes));
		
		$ts=new term_show(stripslashes($term), "term_show.php", $base_query, "parent_link", $keep_tilde, $id_thes);
		$display = $ts->show_notice();
		return $display;
	}
	
	protected function get_authority_instance($authority_id=0, $object_id=0) {
		return new authority($authority_id, $object_id, AUT_TABLE_CATEG);
	}
	
	protected function get_entities_controller_instance($id=0) {
		return new entities_categories_controller($id);
	}
	
	public static function get_params_url() {
		global $perso_id, $keep_tilde, $parent, $id_thes_unique;
		global $id2, $id_thes, $user_input, $f_user_input;
		
		if(!$parent) $parent=0;
		if(!$user_input) $user_input = $f_user_input;
		
		$params_url = parent::get_params_url();
		$params_url .= ($perso_id ? "&perso_id=".$perso_id : "").($keep_tilde ? "&keep_tilde=".$keep_tilde : "").($parent ? "&parent=".$parent : "").($id_thes_unique ? "&id_thes_unique=".$id_thes_unique : "")."&autoindex_class=autoindex_record";
		$params_url .= ($id2 ? "&id2=".$id2 : "").($id_thes ? "&id_thes=".$id_thes : "");
		return $params_url;
	}
}
?>