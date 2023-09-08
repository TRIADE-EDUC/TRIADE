<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_question.class.php,v 1.3 2018-07-25 12:15:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/faq_types.class.php");
require_once($class_path."/faq_themes.class.php");

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

	public function __construct($id=0){
		$this->id = $id*1;
		$this->fetch_datas();
	}
	
	protected  function fetch_datas(){
		global $dbh,$msg;
		if($this->id){
			$query = "select id_faq_question, faq_question_num_type, faq_question_num_theme, faq_question_num_demande, faq_question_question, faq_question_question_userdate, faq_question_question_date, faq_question_answer, faq_question_answer_userdate, faq_question_answer_date, faq_question_statut from faq_questions where id_faq_question = ".$this->id;
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
				if($this->question == ""){
					$this->question = $msg['faq_question_no_question'];
				}
				if($this->answer == ""){
					$this->answer = $msg['faq_question_no_answer'];
				}
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
	
	public function get_listview(){
		global $msg,$charset;
		$html = "
		<div class='bg-grey' id='parent_question_".$this->id."' onclick='faq_expand_collaspe(\"".$this->id."\")' title='".htmlentities("voir la réponse")."'>
			<span class='etiq_champ'>".nl2br($this->question)."</span>
		</div>
		<div class='faq_child' id='child_question_".$this->id."' style='display:none;'>
			".nl2br($this->answer)."
		</div>";
		return $html;
	}
}