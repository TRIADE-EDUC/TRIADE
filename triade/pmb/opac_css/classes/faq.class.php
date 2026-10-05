<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq.class.php,v 1.20.2.1 2024/09/13 12:01:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/faq_question.class.php");
require_once($include_path."/navbar.inc.php");
require_once($class_path."/analyse_query.class.php");

//classe gérant la visualisation du module FAQ à l'OPAC...
class faq {
	public $themes = array();
	public $types = array();
	public $descriptors = array();
	public $years;
	public $questions_ids = array();
	public $questions = array();
	public $nb_questions_by_page=0;
	public $page=0;
	public $filters =array();
	public $user_query = "";

	public function __construct($page=1, $nb_questions_by_page=0,$filters=array()){
		global $opac_search_results_per_page;
		$this->page = intval($page);
		if($this->page>=1) $this->page-=1;
		$this->nb_questions_by_page = ($nb_questions_by_page ? $nb_questions_by_page : $opac_search_results_per_page);
		$this->themes = array_filter((isset($filters['themes']) && $filters['themes'] ? $filters['themes'] : array()));
		$this->types = array_filter((isset($filters['types']) && $filters['types'] ? $filters['types'] : array()));
		$this->descriptors = array_filter((isset($filters['descriptors']) && $filters['descriptors'] ? $filters['descriptors'] : array()));
		$this->years = array_filter((isset($filters['years']) && $filters['years'] ? $filters['years'] : array()),function($var){return ($var!=="");});
		$this->user_query = (isset($filters['user_query']) ? trim($filters['user_query']) : '');
		
		$this->fetch_datas();
	}
	
	protected static function int_caster(&$item)
	{
	    return intval($item);
	}
	
	protected function fetch_datas(){
		$query = "select id_faq_question from faq_questions !!join!!!!where!! order by faq_question_question_date desc, faq_question_answer_date desc, faq_question_question asc";
		$join = $where= array();
		$where[] = ($_SESSION["id_empr_session"] ? "faq_question_statut in (2,3)" : "faq_question_statut = 2");
		if(count($this->themes)){
		    array_walk($this->themes, 'static::int_caster');
			$where[] = "faq_question_num_theme in (".implode(",",$this->themes).")";
		}
		if(count($this->types)){
		    array_walk($this->types, 'static::int_caster');
			$where[] = "faq_question_num_type in (".implode(",",$this->types).")";
		}
		if(count($this->descriptors)){
		    array_walk($this->descriptors, 'static::int_caster');
			$join[] = "join faq_questions_categories on id_faq_question=num_faq_question ";
			$where[] = "num_categ in (".implode(",",$this->descriptors).")";
		}
		if(count($this->years)){
			$where[] = "year(faq_question_answer_date) in (".implode(",",$this->years).")";
		}
		$query = str_replace("!!join!!",implode(" ",$join),$query);
		if(count($where)){
			$where = implode(" and ",$where);
			$query = str_replace("!!where!!","where ".$where,$query);
		}else $query = str_replace("!!where!!","",$query);

		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$this->questions_ids = array();
			while($row = pmb_mysql_fetch_object($result)){
				$this->questions_ids[] = $row->id_faq_question;
			}
		}
		
		if($this->user_query){
			$aq = new analyse_query($this->user_query);
			$query = $aq->get_query_mot("id_faq_question", "faq_questions_words_global_index", "word","faq_questions_fields_global_index", "value");
			$query.= " where id_faq_question in(".implode(",",$this->questions_ids).")";
			$this->questions_ids = array();
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->questions_ids[] = $row->id_faq_question;
				}
			}
		}
	}
	
	protected function get_page_title(){
		global $msg;
		return sprintf($msg['faq_question_page_title'],count($this->questions_ids));
	}
	
	public function show(){
	    global $include_path, $msg, $opac_rgaa_active;
		
		if ($opac_rgaa_active) {
		    $title = "<h1>".$msg['faq']."</h1>";
		    $title .= "<h2>".$this->get_page_title()."</h2>";
		} else {
		    $title = "<h3>".$this->get_page_title()."</h3>";
		}
		
		
		$html="
		<script src='".$include_path."/javascript/faq.js'></script>
		<div class='faq' id='faq'>
			".$title."
			<div class='row'>&nbsp;</div>
			<div class='faq_content'>";
		$start = $this->page * $this->nb_questions_by_page;
		$end =$start+$this->nb_questions_by_page;
		for($i=$start ; $i<$end ; $i++){
			if(isset($this->questions_ids[$i])){
				$this->question[$this->questions_ids[$i]] = new faq_question($this->questions_ids[$i]);
				$html.=$this->question[$this->questions_ids[$i]]->get_listview();
			}
		}
		$html.="
			</div>
			".$this->get_navigator()."
		</div>";
		return $html;
	}
	
	protected function get_navigator(){
		if(count($this->questions_ids)){
			$url_page = "javascript:document.faq_filters.faq_page.value=!!page!!;document.faq_filters.submit()";
			$nb_per_page_custom_url = "javascript:document.faq_filters.nb_per_page_custom.value=!!nb_per_page_custom!!";
			$action = "javascript:document.faq_filters.faq_page.value=document.form.page.value;document.faq_filters.submit()";
			return "
			<div id='navbar'>
				<hr />
				<div style='text-align:center'>
				".printnavbar($this->page+1, count($this->questions_ids), $this->nb_questions_by_page, $url_page, $nb_per_page_custom_url, $action)."
				</div>		
			</div>";
		}
	}
	
	protected function init_filters_infos(){
		global $msg;

		//thèmes
		$themes = $this->get_facette_informations(3,1);
		if(count($themes)) $this->filters['themes'] = $themes;
		//types
		$types = $this->get_facette_informations(4,1);
		if(count($types)) $this->filters['types'] = $types;
		//descripteurs
		$descriptors = $this->get_facette_informations(6,1);
		if(count($descriptors))	$this->filters['descriptors'] = $descriptors;
		//descripteurs
		$years = $this->get_facette_informations(9,0);
		if(count($years)){
			foreach($years as $key =>$values){
			    if($values['label'] == 0){
					$years[$key]['label'] = $msg['faq_facette_no_date'];
				}
			}
			$this->filters['years'] = $years;
		}
		
	}
	
	protected function get_facette_informations($code_champ,$code_ss_champ=0){
		global $lang;
		$informations = array();
		$query = "select distinct value as label ,authority_num as id,lang,count(id_faq_question) as nb_questions from faq_questions_fields_global_index where id_faq_question in (".implode(",",$this->questions_ids).") and code_champ = '".$code_champ."'";
		if($code_ss_champ){
			$query.= " and code_ss_champ = '".$code_ss_champ."'";
		}
		$query.= " and lang in ('','".$lang."') group by label order by nb_questions desc";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)>0){
			while ($row = pmb_mysql_fetch_object($result)){
				if(!$row->id){
					$row->id = $row->label;
				}
				$informations[$row->id] = array(
						'id' => $row->id,
						'label' => $row->label,
						'nb_questions' => $row->nb_questions
				);
			}
		}
		return $informations;
	}
	
	protected function get_actives_facettes(){
		global $msg,$charset;
		$filter_actives = $facettes_filter = "";

		//thèmes
		if(count($this->themes)){
			$themes=new faq_themes("faq_themes", "id_theme", "libelle_theme");
			foreach($this->themes as $theme){
				$facettes_filter.="
					<input type='hidden' id='faq_filters_themes' name='faq_filters[themes][]' value='".$theme."' />";
				$link="javascript:document.getElementById('faq_filters_themes').value='';document.faq_filters.faq_page.value=1;document.faq_filters.submit();";
				$filter_actives.="
						<tr>
							<td>".htmlentities($msg['faq_filter_themes'].": ".$themes->getLabel($theme),ENT_QUOTES,$charset)."</td>
							<td><a href=\"".$link."\"><img src='".get_url_icon('cross.png')."' alt='".$msg['disable_this_filter']."'/></a></td>
						</tr>";				
			}
		}else{
			$facettes_filter.="
					<input type='hidden' id='faq_filters_themes' name='faq_filters[themes][]' value='' />";
		}
		
		//Types
		if(count($this->types)){
			$types=new faq_themes("faq_types", "id_type", "libelle_type");
			foreach($this->types as $type){
				$facettes_filter.="
					<input type='hidden' id='faq_filters_types' name='faq_filters[types][]' value='".$type."' />";
				$link="javascript:document.getElementById('faq_filters_types').value='';document.faq_filters.faq_page.value=1;document.faq_filters.submit();";
				$filter_actives.="
						<tr>
							<td>".htmlentities($msg['faq_filter_types'].": ".$types->getLabel($type),ENT_QUOTES,$charset)."</td>
							<td><a href=\"".$link."\"><img src='".get_url_icon('cross.png')."' alt='".$msg['disable_this_filter']."'/></a></td>
						</tr>";
			}
		}else{
			$facettes_filter.="
					<input type='hidden' id='faq_filters_types' name='faq_filters[types][]' value='' />";
		}
		
		//descripteurs
		if(count($this->descriptors)){
			foreach($this->descriptors as $descriptor){
				$facettes_filter.="
					<input type='hidden' id='faq_filters_descriptors' name='faq_filters[descriptors][]' value='".$descriptor."' />";
				$link="javascript:document.getElementById('faq_filters_descriptors').value='';document.faq_filters.faq_page.value=1;document.faq_filters.submit();";
				$filter_actives.="
						<tr>
							<td>".htmlentities($msg['faq_filter_descriptors'].": ".$this->filters['descriptors'][$descriptor]['label'],ENT_QUOTES,$charset)."</td>
							<td><a href=\"".$link."\"><img src='".get_url_icon('cross.png')."' alt='".$msg['disable_this_filter']."'/></a></td>
						</tr>";
			}
		}else{
			$facettes_filter.="
					<input type='hidden' id='faq_filters_descriptors' name='faq_filters[descriptors][]' value='' />";
		}
		
		if(count($this->years)){
			foreach($this->years as $year){
				$facettes_filter.="
					<input type='hidden' id='faq_filters_years' name='faq_filters[years][]' value='".$year."' />";
				$link="javascript:document.getElementById('faq_filters_years').value='';document.faq_filters.faq_page.value=1;document.faq_filters.submit();";
				$filter_actives.="
						<tr>
							<td>".htmlentities($msg['faq_filter_years'].": ".$this->filters['years'][$year]['label'],ENT_QUOTES,$charset)."</td>
							<td><a href=\"".$link."\"><img src='".get_url_icon('cross.png')."' alt='".$msg['disable_this_filter']."'/></a></td>
						</tr>";
			}
		}else{
			$facettes_filter.="
					<input type='hidden' id='faq_filters_years' name='faq_filters[years][]' value='' />";
		}
		if($filter_actives){
			$facettes_filter.="
				<div class='faq_filters_actives'>
					<h3>".htmlentities($msg['faq_facettes_actives'],ENT_QUOTES,$charset)."</h3>
					<table id='active_facette' role='presentation'>".$filter_actives."
					</table>
				</div>
				<div class='row'>&nbsp;</div>";
		}
		return $facettes_filter;
	}
	
	protected function get_allowed_facettes(){
		global $msg,$charset;
		$facettes_filter = $allowed_facettes= "";
		if(count($this->filters)){
			foreach($this->filters as $filter => $filter_values){
				if(count($filter_values)>1){
					$allowed_facettes.="
					<table role='presentation'>
						<tr>
							<th>".htmlentities($msg['faq_filter_'.$filter],ENT_QUOTES,$charset)."</th>
						</tr>";
					foreach($filter_values as $infos){
						$link="javascript:document.getElementById(\"faq_filters_".$filter."\").value=\"".$infos['id']."\";document.faq_filters.faq_page.value=1;document.faq_filters.submit();";
						$allowed_facettes.="
						<tr>
							<td><a href='".$link."'>".htmlentities($infos['label']." (".$infos['nb_questions'].")",ENT_QUOTES,$charset)."</a></td>
						</tr>";
					}
					$allowed_facettes.="
					</table>";
				}
			}
		}
		if($allowed_facettes){
			$facettes_filter.="
				<div class='faq_filters_allowed'>
					<h3>".htmlentities($msg['facette_list'],ENT_QUOTES,$charset)."</h3>".$allowed_facettes."
				</div>";
		}		
		return $facettes_filter;
	}
	
	protected function get_search_filter(){
		global $msg,$charset;
		
		$facettes_filter = "
		<div class='faq_user_query_filter'>
			<h3>".htmlentities($msg['faq_filter_user_query'],ENT_QUOTES,$charset)."</h3>
			<input type='text' name='faq_filters[user_query]' value='".htmlentities($this->user_query,ENT_QUOTES,$charset)."'/>
			<input type='submit' class='boutonrechercher' name='ok' value='".$msg[142]."'/>
			<div class='row'>&nbsp;</div>
		</div>";
		return $facettes_filter;
	}
	
	public function get_facettes_filter(){
		global $charset;
		global $nb_per_page_custom;
		$this->init_filters_infos();
		
		$facettes_filter = "
		<div class='faq_filters' id='facette'>
			<form action='./index.php?lvl=faq' method='post' id='faq_filters' name='faq_filters'>
				<input type='hidden' name='faq_page' value='".htmlentities(($this->page+1),ENT_QUOTES,$charset)."'/>
				<input type='hidden' name='nb_per_page_custom' value='".$nb_per_page_custom."'/>";
		$facettes_filter.= $this->get_search_filter();
		$facettes_filter.= $this->get_actives_facettes();
		$facettes_filter.= $this->get_allowed_facettes();
		$facettes_filter.= "
			</form>
		</div>";
		return $facettes_filter;
	}
}