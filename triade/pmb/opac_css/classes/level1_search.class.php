<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_search.class.php,v 1.31 2023/12/13 09:41:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/searcher.class.php");
global $opac_search_other_function;
if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/searcher/opac_searcher_autorities_skos_concepts.class.php");

/* Classe qui permet de faire la recherche de premier niveau */

class level1_search {

	protected $type;

	protected $nb_results;


	public $user_query;

	protected $mode;

	protected $form_action;

    public function __construct($type='') {
    	$this->type = $type;
    }

    public function set_user_query($user_query) {
    	$this->user_query = $user_query;
    }

    protected function get_hidden_search_form_name() {
    	return "search_".$this->type;
    }

    protected function get_hidden_search_content_form() {
    	global $charset;

    	$content_form = "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities($this->user_query,ENT_QUOTES,$charset)."\">";
    	if (function_exists("search_other_function_post_values")){
    		$content_form .=search_other_function_post_values();
    	}
    	$content_form .= "<input type=\"hidden\" name=\"mode\" value=\"".$this->get_mode()."\">";
    	$content_form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">";
    	$content_form .= "<input type=\"hidden\" name=\"count\" value=\"".$this->get_nb_results()."\">";
    	return $content_form;
    }

    protected function get_hidden_search_form() {
    	$form = "<form id='form_".$this->get_hidden_search_form_name()."' name=\"".$this->get_hidden_search_form_name()."\" action=\"".$this->get_form_action()."\" method=\"post\">\n";
    	$form .= $this->get_hidden_search_content_form();
    	$form .= "</form>";
    	return $form;
    }

    protected function get_nb_results_level1_authorities_search($type, $classname='') {
    	if($classname) {
    		static::load_class('/search/level1/'.$classname.'.class.php');
    		$level1_authorities_search = new $classname($type);
    	} else {
    		static::load_class('/search/level1/level1_authorities_search.class.php');
    		$level1_authorities_search = new level1_authorities_search($type);
    	}
    	$level1_authorities_search->set_form_action($this->form_action);
    	$level1_authorities_search->set_user_query($this->user_query);
    	return $level1_authorities_search->get_nb_results();
    }

    protected function get_nb_results_level1_records_search($type, $classname='') {
    	if($classname) {
    		static::load_class('/search/level1/'.$classname.'.class.php');
    		$level1_records_search = new $classname($type);
    	} else {
    		static::load_class('/search/level1/level1_records_search.class.php');
    		$level1_records_search = new level1_records_search($type);
    	}
    	$level1_records_search->set_form_action($this->form_action);
    	$level1_records_search->set_user_query($this->user_query);
    	return $level1_records_search->get_nb_results();
    }

    public function search_docnums() {
    	global $typdoc,$charset,$gestion_acces_active,$gestion_acces_empr_notice,$opac_search_other_function,$class_path;
    	global $opac_stemming_active;
    	if(isset($_SESSION["opac_view"]) && $_SESSION["opac_view"] && $_SESSION["opac_view_query"] ){
			$opac_view_restrict=" notice_id in (select opac_view_num_notice from  opac_view_notices_".$_SESSION["opac_view"].") ";
		} else {
			$opac_view_restrict="";
		}
		if ($typdoc) $restrict="typdoc='".$typdoc."'"; else $restrict="";

		//droits d'acces emprunteur/notice
		$acces_j='';
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			require_once("$class_path/acces.class.php");
			$ac= new acces();
			$dom_2= $ac->setDomain(2);
			$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],16,'notice_id');
		}

		// on regarde comment la saisie utilisateur se presente
		$clause = '';
		$clause_bull = '';
		$clause_bull_num_notice = '';
		$add_notice = '';

		$aq=new analyse_query(stripslashes($this->user_query),0,0,1,0,$opac_stemming_active);

		if ($acces_j) {
			$members=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_notice"," explnum_notice=notice_id and explnum_bulletin=0",0,0,true);
			$clause="where ".$members["where"]." and (".$members["restrict"].")";

			$members_bull=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin"," explnum_bulletin=bulletin_id and explnum_notice=0 and num_notice=0 and bulletin_notice=notice_id",0,0,true);
			$clause_bull="where ".$members_bull["where"]." and (".$members_bull["restrict"].")";

			$members_bull_num_notice=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin"," explnum_bulletin=bulletin_id and num_notice=notice_id",0,0,true);
			$clause_bull_num_notice="where ".$members_bull_num_notice["where"]." and (".$members_bull_num_notice["restrict"].")";
			$statut_j='';

		} else {
			$members=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_notice" ," explnum_notice=notice_id and statut=id_notice_statut and (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0)) ".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"").")",0,0,true);
			$clause="where ".$members["where"]." and (".$members["restrict"].")";

			$members_bull=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin" ," explnum_bulletin=bulletin_id and bulletin_notice=notice_id and num_notice=0 and statut=id_notice_statut and (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0)) ".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"").")",0,0,true);
			$clause_bull="where ".$members_bull["where"]." and (".$members_bull["restrict"].")";

			$members_bull_num_notice=$aq->get_query_members("explnum","explnum_index_wew","explnum_index_sew","explnum_bulletin" ," explnum_bulletin=bulletin_id and num_notice=notice_id and statut=id_notice_statut and (((notice_visible_opac=1 and notice_visible_opac_abon=0) and (explnum_visible_opac=1 and explnum_visible_opac_abon=0)) ".($_SESSION["user_code"]?" or ((notice_visible_opac_abon=1 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1)) or ((notice_visible_opac_abon=0 and notice_visible_opac=1) and (explnum_visible_opac=1 and explnum_visible_opac_abon=1))":"").")",0,0,true);
			$clause_bull_num_notice="where ".$members_bull_num_notice["where"]." and (".$members_bull_num_notice["restrict"].")";

			$statut_j=',notice_statut';
		}

		if ($opac_search_other_function) {
			$add_notice = search_other_function_clause();
			if ($add_notice) {
				$clause.= ' and notice_id in ('.$add_notice.')';
				$clause_bull.= ' and notice_id in ('.$add_notice.')';
				$clause_bull_num_notice.= ' and notice_id in ('.$add_notice.')';
			}
		}

		$search_terms = $aq->get_positive_terms($aq->tree);
		//On enlève le dernier terme car il s'agit de la recherche booléenne complète
		if(is_array($search_terms) && count($search_terms)){
			unset($search_terms[count($search_terms)-1]);
		}

		$pert=$members["select"]." as pert";
		$tri="order by pert desc, index_serie, tnvol, index_sew";

		if ($restrict) {
			$clause.=" and ".$restrict;
			$clause_bull.=" and ".$restrict;
			$clause_bull_num_notice.=" and ".$restrict;
		}

		if($opac_view_restrict)  $clause.=" and ".$opac_view_restrict;

		if($clause) {
			// instanciation de la nouvelle requête
			$q_docnum_noti = "select explnum_id from explnum, notices $statut_j $acces_j $clause";
			$q_docnum_bull = "select explnum_id from bulletins, explnum, notices $statut_j $acces_j $clause_bull";
			$q_docnum_bull_notice = "select explnum_id from bulletins, explnum, notices $statut_j $acces_j $clause_bull_num_notice";

			$q_docnum = "select count(explnum_id) from ( $q_docnum_noti UNION $q_docnum_bull UNION $q_docnum_bull_notice) as uni	";
			$docnum = pmb_mysql_query($q_docnum);
			$nb_result_docnum=0;
			if($docnum && pmb_mysql_num_rows($docnum)){
				$nb_result_docnum = pmb_mysql_result($docnum, 0, 0);
			}

			$req_typdoc_noti="select distinct typdoc from explnum,notices $statut_j $acces_j $clause group by typdoc";
			$req_typdoc_bull = "select distinct typdoc from bulletins, explnum,notices $statut_j $acces_j $clause_bull group by typdoc";
			$req_typdoc_bull_num_notice = "select distinct typdoc from bulletins, explnum,notices $statut_j $acces_j $clause_bull_num_notice group by typdoc";
			$req_typdoc = "($req_typdoc_noti) UNION ($req_typdoc_bull) UNION ($req_typdoc_bull_num_notice)";
			$res_typdoc = pmb_mysql_query($req_typdoc);
			$t_typdoc=array();
			if($res_typdoc && pmb_mysql_num_rows($res_typdoc)){
				while (($tpd=pmb_mysql_fetch_object($res_typdoc))) {
					$t_typdoc[]=$tpd->typdoc;
				}
			}
			$l_typdoc=implode(",",$t_typdoc);
			if ($nb_result_docnum) {
				$form = "<form id='form_search_docnum' name='search_docnum' action=\"./index.php?lvl=more_results\" method=\"post\">";
				$form .= "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(stripslashes($this->user_query),ENT_QUOTES,$charset)."\">\n";
				if (function_exists("search_other_function_post_values")){ $form .=search_other_function_post_values(); }
				$form .= "<input type=\"hidden\" name=\"mode\" value=\"docnum\">\n";
				$form .= "<input type=\"hidden\" name=\"search_type_asked\" value=\"simple_search\">\n";
				$form .= "<input type=\"hidden\" name=\"count\" value=\"".$nb_result_docnum."\">\n";
				$form .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"clause_bull\" value=\"".htmlentities($clause_bull,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"clause_bull_num_notice\" value=\"".htmlentities($clause_bull_num_notice,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\">\n";
				$form .= "<input type=\"hidden\" name=\"search_terms\" value=\"".htmlentities(serialize($search_terms),ENT_QUOTES,$charset)."\"></form>\n";
				$_SESSION["level1"]["docnum"]["form"]=$form;
				$_SESSION["level1"]["docnum"]["count"]=$nb_result_docnum;
			}
		}
    }

    public function make_search() {
    	global $opac_modules_search_title,$opac_modules_search_author,$opac_modules_search_publisher;
    	global $opac_modules_search_titre_uniforme,$opac_modules_search_collection,$opac_modules_search_subcollection;
    	global $opac_modules_search_category,$opac_modules_search_indexint,$opac_modules_search_keywords;
    	global $opac_modules_search_abstract,$opac_modules_search_docnum,$opac_modules_search_all, $opac_modules_search_concept;
    	global $look_TITLE,$look_AUTHOR,$look_PUBLISHER,$look_TITRE_UNIFORME,$look_COLLECTION,$look_SUBCOLLECTION;
    	global $look_CATEGORY,$look_INDEXINT,$look_KEYWORDS,$look_ABSTRACT,$look_DOCNUM,$look_ALL, $look_CONCEPT;
    	global $user_query;

    	$this->user_query=$user_query;
    	$total_results = 0;

    	if ($opac_modules_search_title && $look_TITLE) {
			$total_results += $this->get_nb_results_level1_records_search('title');
		}
		if ($opac_modules_search_author && $look_AUTHOR) {
			$total_results += $this->get_nb_results_level1_authorities_search('authors', 'level1_authors_search');
		}
		if ($opac_modules_search_publisher && $look_PUBLISHER) {
			$total_results += $this->get_nb_results_level1_authorities_search('publishers');
		}
		if ($opac_modules_search_titre_uniforme && $look_TITRE_UNIFORME) {
			$total_results += $this->get_nb_results_level1_authorities_search('titres_uniformes');
		}
    	if ($opac_modules_search_collection && $look_COLLECTION) {
			$total_results += $this->get_nb_results_level1_authorities_search('collections');
		}
		if ($opac_modules_search_subcollection && $look_SUBCOLLECTION) {
			$total_results += $this->get_nb_results_level1_authorities_search('subcollections');
		}
		if ($opac_modules_search_category && $look_CATEGORY) {
			$total_results += $this->get_nb_results_level1_authorities_search('categories', 'level1_categories_search');
		}
		if ($opac_modules_search_indexint && $look_INDEXINT) {
			$total_results += $this->get_nb_results_level1_authorities_search('indexint');
		}
		if ($opac_modules_search_keywords && $look_KEYWORDS) {
			$total_results += $this->get_nb_results_level1_records_search('keywords', 'level1_records_keywords_search');
		}
		if ($opac_modules_search_abstract && $look_ABSTRACT) {
			$total_results += $this->get_nb_results_level1_records_search('abstract');
		}
		if ($opac_modules_search_docnum && $look_DOCNUM) {
			$total_results += $this->search_docnums();
		}
		if ($opac_modules_search_concept && $look_CONCEPT) {
			$total_results += $this->get_nb_results_level1_authorities_search('concepts', 'level1_concepts_search');
		}
		$authpersos=authpersos::get_instance();
		$total_results +=$authpersos->search_authperso($this->user_query);

		/*if ($opac_modules_search_all && $look_ALL) {
		 $nb_result += $this->get_nb_results_level1_records_search('all', 'level1_records_all_search');
		 $total_results += $nb_result;
		 $nb_all_results=$nb_result;
		 }*/
		return $total_results;
    }

    protected function get_mode() {
    	return $this->type;
    }

    protected function get_affiliate_mode() {
    	return $this->type;
    }

    protected function get_affiliate_label() {
    	global $msg;

    	return $msg[$this->type];
    }

    protected function get_display_link_affiliate() {
        global $msg;

        if ($this->get_nb_results()) {
            return "<a href='#' onclick=\"
                        document." . $this->get_hidden_search_form_name() . ".action = '" . $this->get_form_action() . "&tab=catalog';
                        document." . $this->get_hidden_search_form_name() . ".submit();
                        return false;\">
                        " . $msg['suite'] . "&nbsp;<img src='" . get_url_icon('search.gif') . "' style='border:0px' />
                    </a>";
        }
        return "";
    }

    protected function get_affiliate_template() {
    	global $search_result_affiliate_lvl1;

    	return $search_result_affiliate_lvl1;
    }

    protected function get_display_result_affiliate() {
    	global $msg, $charset;

    	$search_result_affiliate_all =  str_replace("!!mode!!", $this->get_affiliate_mode(),$this->get_affiliate_template());
    	$search_result_affiliate_all =  str_replace("!!search_type!!",$this->get_search_type(),$search_result_affiliate_all);
    	$search_result_affiliate_all =  str_replace("!!label!!", $this->get_affiliate_label(),$search_result_affiliate_all);
    	$search_result_affiliate_all =  str_replace("!!nb_result!!",$this->get_nb_results(),$search_result_affiliate_all);

    	$search_result_affiliate_all =  str_replace("!!link!!",$this->get_display_link_affiliate(),$search_result_affiliate_all);
    	$search_result_affiliate_all =  str_replace("!!style!!","",$search_result_affiliate_all);
    	$search_result_affiliate_all =  str_replace("!!user_query!!",rawurlencode(stripslashes((($charset == "utf-8")? $this->user_query : encoding_normalize::utf8_normalize($this->user_query)))),$search_result_affiliate_all);
    	$search_result_affiliate_all =  str_replace("!!form_name!!",$this->get_hidden_search_form_name(),$search_result_affiliate_all);
    	$search_result_affiliate_all =  str_replace("!!form!!",$this->get_hidden_search_form(),$search_result_affiliate_all);
    	return $search_result_affiliate_all;
    }

    protected function get_display_link_result() {
    	global $msg, $charset;

    	$display = $this->get_nb_results()." ".htmlentities($msg['results'], ENT_QUOTES, $charset)." ";
    	$display .= "<a href=\"#\" onclick=\"document.forms['".$this->get_hidden_search_form_name()."'].submit(); return false;\">";
    	$display .= htmlentities($msg['suite'], ENT_QUOTES, $charset)."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' /></a><br />";
    	return $display;
    }

    protected function get_display_result() {
    	global $msg;

    	$display = "<div class='search_result' id=\"".$this->type."\" name=\"".$this->type."\">";
    	$display .= "<strong>".$msg[$this->type]."</strong> ";
    	$display .= $this->get_display_link_result();
    	$display .= $this->get_hidden_search_form();
    	$display .= "</div>";
    	return $display;
    }

    public function proceed() {
    	global $opac_allow_affiliate_search;

    	if($opac_allow_affiliate_search){
    		print $this->get_display_result_affiliate();
    	}else{
    		if($this->get_nb_results()) {
    			print $this->get_display_result();
    		}
    	}
    	$this->search_log($this->get_nb_results());
    }

    /**
     * Enregistrement des stats
     */
    protected function search_log($count) {
    	global $nb_results_tab;

    	$nb_results_tab[$this->type] = $count;
    }

    public function get_form_action() {
    	global $base_path;
    	if(!isset($this->form_action)) {
    		$this->form_action = $base_path.'/index.php?lvl=more_results';
    	}
    	return $this->form_action;
    }

    public function set_form_action($form_action) {
    	$this->form_action = $form_action;
    }

    protected static function load_class($file){
    	global $base_path;
    	global $class_path;
    	global $include_path;
    	global $javascript_path;
    	global $styles_path;
    	global $msg,$charset;

    	if(file_exists($class_path.$file)){
    		require_once($class_path.$file);
    	}else{
    		return false;
    	}
    	return true;
    }
}
