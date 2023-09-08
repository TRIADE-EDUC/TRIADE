<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_question.class.php,v 1.12 2019-04-11 08:03:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/templates/faq_question.tpl.php");
require_once($class_path."/faq_types.class.php");
require_once($class_path."/faq_themes.class.php");
require_once($class_path."/indexation.class.php");

class faq_question  {
	public $id = 0;
	public $num_type = 0;
	public $num_theme = 0;
	public $num_demande = 0;
	public $question = "";
	public $question_userdate = "";
	public $question_date = "";
	public $answer = "";
	public $answer_userdate = "";
	public $answer_date = "";
	public $descriptors = array();	
	public $statut = 0 ;
	public $aff_date_demande = "" ;
	public $aff_date_answer = "" ;

	public function __construct($id=0){
		$this->id = $id*1;
		$this->fetch_datas();
	}
	
	protected  function fetch_datas(){
		global $dbh,$msg;
		if($this->id){
			$query = "select id_faq_question,date_format(faq_question_question_date, '".$msg["format_date"]."') as aff_date_demande, date_format(faq_question_answer_date, '".$msg["format_date"]."') as aff_date_answer, faq_question_num_type, faq_question_num_theme, faq_question_num_demande, faq_question_question, faq_question_question_userdate, faq_question_question_date, faq_question_answer, faq_question_answer_userdate, faq_question_answer_date, faq_question_statut from faq_questions where id_faq_question = ".$this->id;
			$result=pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->num_theme = $row->faq_question_num_theme;
				$this->num_type = $row->faq_question_num_type;
				$this->num_demande = $row->faq_question_num_demande;
				$this->question = $row->faq_question_question;
				$this->question_userdate =  $row->faq_question_question_userdate;
				$this->question_date =  $row->faq_question_question_date;
				$this->answer = $row->faq_question_answer;
				$this->answer_userdate =  $row->faq_question_answer_userdate;
				$this->answer_date =  $row->faq_question_answer_date;	
				$this->statut = $row->faq_question_statut;			
				$this->aff_date_demande = $row->aff_date_demande;			
				$this->aff_date_answer = $row->aff_date_answer;		
			}else{
				$this->id = 0;
			}
		}else{
			$this->num_theme = 0;
			$this->num_type = 0;
			$this->num_demande = 0;
			$this->question = "";
			$this->question_userdate = "";
			$this->question_date =  "";
			$this->answer = "";
			$this->answer_userdate = "";
			$this->answer_date = "";
			$this->statut = 0;
			$this->aff_date_demande = "";			
			$this->aff_date_answer = "";		
		}
		$this->descriptors = array();
		if($this->id){
			$query = "select num_faq_question,num_categ,categ_order from faq_questions_categories where num_faq_question = ".$this->id." order by 3";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$this->descriptors[] = $row->num_categ;
				}
			}
		}
	}
	
	public function get_form($id_demande=0,$action="./demandes.php?categ=faq&sub=question"){
		global $faq_question_form;
		global $msg, $charset;
		global $pmb_javascript_office_editor,$base_path;
		global $lang;
		global $faq_question_first_desc,$faq_question_other_desc;
		
		if ($pmb_javascript_office_editor) {
			print $pmb_javascript_office_editor ;
			print "<script type='text/javascript' src='".$base_path."/javascript/tinyMCE_interface.js'></script>";
		}
		
 		if($id_demande && !$this->id){
 			$query = "select date_demande,date_format(date_demande, '".$msg["format_date"]."') as aff_date_demande, sujet_demande, libelle_theme,libelle_type, reponse_finale from demandes d, demandes_theme dt, demandes_type dy where dy.id_type=d.type_demande and dt.id_theme=d.theme_demande and id_demande='".$id_demande."'";
 			$result = pmb_mysql_query($query);
 			if(pmb_mysql_num_rows($result)){
 				$row = pmb_mysql_fetch_object($result);
 				$this->num_demande = $id_demande;
 				$this->question = $row->sujet_demande;
 				$this->answer = $row->reponse_finale;
 				$this->question_userdate = formatdate($row->date_demande);
 				$this->aff_date_demande = $row->aff_date_demande;
				//recherche du theme
				$query = " select id_theme from faq_themes where libelle_theme like '".addslashes($row->libelle_theme)."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$this->num_theme = pmb_mysql_result($result,0,0);
				}
				//recherche du type...
				$query = " select id_type from faq_types where libelle_type like '".addslashes($row->libelle_type)."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$this->num_type = pmb_mysql_result($result,0,0);
				}
 			}
 		}
 		if(!$this->aff_date_demande)$this->aff_date_demande=format_date(today());
 		if(!$this->aff_date_answer)$this->aff_date_answer=format_date(today());
 		
		if($this->id){
			$suppression = "
			<script type='text/javascript'>
				function confirm_delete_question(id){
					result = confirm(\"".$msg['faq_question_confirm_suppression']."\");
        			if(result) document.location = './demandes.php?categ=faq&sub=question&id='+id+'&action=delete' ;
				}		
			</script>
			<input type='button' class='bouton' value=' ".$msg[63]." ' onclick='confirm_delete_question(\"".$this->id."\")' />";
			$form = str_replace("!!form_title!!",htmlentities($msg['faq_question_edit_form'],ENT_QUOTES,$charset),$faq_question_form);
 			$form = str_replace("!!bouton_supprimer!!",$suppression,$form);
		}else{
			$form = str_replace("!!form_title!!",htmlentities($msg['faq_question_new_form'],ENT_QUOTES,$charset),$faq_question_form);
			$form = str_replace("!!bouton_supprimer!!","",$form);
		}
		$form = str_replace("!!id!!",htmlentities($this->id,ENT_QUOTES,$charset),$form);
		$form = str_replace("!!num_demande!!",htmlentities($this->num_demande,ENT_QUOTES,$charset),$form);
		$form = str_replace("!!action!!",$action,$form);
		
		
		$types = new faq_types("faq_types", "id_type", "libelle_type");
		$form = str_replace("!!type_selector!!", $types->getListSelector($this->num_type), $form);
		
		$themes = new faq_themes("faq_themes", "id_theme" , "libelle_theme");
		$form = str_replace("!!theme_selector!!", $themes->getListSelector($this->num_theme), $form);
		
		$statut = "	
		<select name='faq_question_statut'>
			<option value='1'".($this->statut == 1 ? " selected='selected'" : "").">".$msg['faq_question_statut_visible_1']."</option>
			<option value='2'".($this->statut == 2 ? " selected='selected'" : "").">".$msg['faq_question_statut_visible_2']."</option>
			<option value='3'".($this->statut == 3 ? " selected='selected'" : "").">".$msg['faq_question_statut_visible_3']."</option>
		</select>";
		$form = str_replace("!!statut_selector!!", $statut, $form);
		
		$form = str_replace("!!question!!", htmlentities($this->question,ENT_QUOTES,$charset), $form);
		$form = str_replace("!!question_date!!", htmlentities($this->aff_date_demande,ENT_QUOTES,$charset), $form);
		
		$form = str_replace("!!answer!!", htmlentities($this->answer,ENT_QUOTES,$charset), $form);
		$form = str_replace("!!answer_date!!", htmlentities($this->aff_date_answer,ENT_QUOTES,$charset), $form);
		
		
		
		//gestion des descripteurs	
		$categs = "";
		if(count($this->descriptors)){
			for ($i=0 ; $i<count($this->descriptors) ; $i++){
				if($i==0) $categ=$faq_question_first_desc;
				else $categ = $faq_question_other_desc;
				//on y va
				$categ = str_replace('!!icateg!!', $i, $categ);
				$categ = str_replace('!!categ_id!!', $this->descriptors[$i], $categ);
				$categorie = new categories($this->descriptors[$i],$lang);
				$categ = str_replace('!!categ_libelle!!', $categorie->libelle_categorie, $categ);
				$categs.=$categ;
			}
			$categs = str_replace("!!max_categ!!",count($this->descriptors),$categs);
		}else{
			$categs=$faq_question_first_desc;
			$categs = str_replace('!!icateg!!', 0, $categs) ;
			$categs = str_replace('!!categ_id!!', "", $categs);
			$categs = str_replace('!!categ_libelle!!', "", $categs);
			$categs = str_replace('!!max_categ!!', 1, $categs);
		}
		return str_replace("!!faq_question_categs!!",$categs,$form);
		return $form;
	}
	
	public function get_value_from_form(){
		global $faq_question_question;
		global $faq_question_question_date;
		global $faq_question_answer;
		global $faq_question_answer_date;
		global $faq_question_id;
		global $faq_question_num_demande;
		global $id_type;
		global $id_theme;
		global $max_categ;
		global $faq_question_statut;
		
		if($this->id == $faq_question_id*1){
			$this->num_theme = $id_theme*1;
			$this->num_type = $id_type*1;
			$this->num_demande = $faq_question_num_demande*1;
			$this->question = stripslashes($faq_question_question);
			$this->question_userdate = stripslashes($faq_question_question_date);
			$this->question_date = detectFormatDate($this->question_userdate);
			$this->answer = stripslashes($faq_question_answer);
			$this->answer_userdate = stripslashes($faq_question_answer_date);
			$this->answer_date = detectFormatDate($this->answer_userdate);
			$this->statut = $faq_question_statut*1;
			$this->descriptors=array();
			for ($i=0 ; $i<$max_categ ; $i++){
				$categ_id = 'f_categ_id'.$i;
				global ${$categ_id};
				if((${$categ_id}*1) > 0){
					$this->descriptors[] = ${$categ_id};
				}
			}
		}else{
			return false;
		}
		return true;
	}
	
	public function save(){
		global $include_path;
		global $dbh;
		
		if($this->id){
			$query = "update ";
			$where = " where id_faq_question = ".$this->id;
		}else{
			$query = "insert into ";
			$where = "";
			
		}
		$query.= "faq_questions set ";
		$query.= "faq_question_num_type = ".$this->num_type.",";
		$query.= "faq_question_num_theme = ".$this->num_theme.",";
		$query.= "faq_question_num_demande = ".$this->num_demande.",";
		$query.= "faq_question_question = '".addslashes($this->question)."',";
		$query.= "faq_question_question_date = '".addslashes($this->question_date)."',";
		$query.= "faq_question_question_userdate = '".addslashes(detectFormatDate($this->question_userdate))."',";
		$query.= "faq_question_answer = '".addslashes($this->answer)."',";
		$query.= "faq_question_answer_userdate = '".addslashes($this->answer_userdate)."',";
		$query.= "faq_question_answer_date = '".addslashes(detectFormatDate($this->answer_userdate))."',";
		$query.= "faq_question_statut = ".$this->statut."";
		$result = pmb_mysql_query($query.$where,$dbh);
		if(!$this->id){
			$this->id = pmb_mysql_insert_id($dbh);
		}
 		if($result){
 			$query = "delete from faq_questions_categories where num_faq_question = ".$this->id;
 			$result = pmb_mysql_query($query,$dbh);
 			if($result){
 				$query = "insert into faq_questions_categories (num_faq_question,num_categ,categ_order) values ";
 				$insert = "";
 				for ($i=0 ; $i<count($this->descriptors) ; $i++){
 					if($insert) $insert.=", ";
					$insert.="(".$this->id.",".$this->descriptors[$i].",".$i.")";					
 				}
 				if($insert){
 					$result = pmb_mysql_query($query.$insert,$dbh);
				}
 			}
 		}
 		
 		if($result){
 			$xmlpath = $include_path."/indexation/faq/question.xml";
 			$index = new indexation($xmlpath,"faq_questions");
 			$index->maj($this->id);
 		}
 		
		return $result;
	}
	
	public static function delete($id=0){
		$id = intval($id);
		if($id){
			$query = "delete from faq_questions_categories where num_faq_question = ".$id;
			pmb_mysql_query($query);
			$query = "delete from faq_questions where id_faq_question = ".$id;
			$result = pmb_mysql_query($query);
			if($result){
				return true;
			}
		}
		return false;
	}
}