<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_question.class.php,v 1.20 2023/11/17 14:27:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
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
		$this->id = intval($id);
		$this->fetch_datas();
	}
	
	protected  function fetch_datas(){
		global $msg;
		if($this->id){
			$query = "select id_faq_question,date_format(faq_question_question_date, '".$msg["format_date"]."') as aff_date_demande, date_format(faq_question_answer_date, '".$msg["format_date"]."') as aff_date_answer, faq_question_num_type, faq_question_num_theme, faq_question_num_demande, faq_question_question, faq_question_question_userdate, faq_question_question_date, faq_question_answer, faq_question_answer_userdate, faq_question_answer_date, faq_question_statut from faq_questions where id_faq_question = ".$this->id;
			$result=pmb_mysql_query($query);
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
	
	public function get_content_form() {
	    global $msg;
	    global $lang;
	    global $faq_question_first_desc,$faq_question_other_desc;
	    
	    $interface_content_form = new interface_content_form(static::class);
	    $interface_content_form->add_element('id_type', 'faq_question_type_label')
	    ->set_class('colonne3')
	    ->add_query_node('select', "SELECT id_type, libelle_type FROM faq_types ORDER BY libelle_type", $this->num_type);
	    
	    $interface_content_form->add_element('id_theme', 'faq_question_theme_label')
	    ->set_class('colonne3')
	    ->add_query_node('select', "SELECT id_theme, libelle_theme FROM faq_themes ORDER BY libelle_theme", $this->num_theme);
	    
	    $options = [
                1 => $msg['faq_question_statut_visible_1'],
	            2 => $msg['faq_question_statut_visible_2'],
                3 => $msg['faq_question_statut_visible_3']
	    ];
	    $interface_content_form->add_element('faq_question_statut', 'faq_question_statut_label')
	    ->set_class('colonne3')
	    ->add_select_node($options, $this->statut);
	    
	    $interface_content_form->add_element('faq_question_question', 'faq_question_question')
	    ->add_textarea_node($this->question, 0, 5);
	    $interface_content_form->add_element('faq_question_question_date', 'faq_question_question_date')
	    ->add_input_node('text', $this->aff_date_demande)
	    ->set_class('saisie-15em')
	    ->set_attributes(array('placeholder' => $msg['format_date_input_text_placeholder']));
	    $interface_content_form->add_element('faq_question_answer', 'faq_question_answer')
	    ->add_textarea_node($this->answer, 0, 5);
	    $interface_content_form->add_element('faq_question_answer_date', 'faq_question_answer_date')
	    ->add_input_node('text', $this->aff_date_answer)
	    ->set_class('saisie-15em')
	    ->set_attributes(array('placeholder' => $msg['format_date_input_text_placeholder']));
	    
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
	    $interface_content_form->add_element('faq_question_desc', 'faq_question_desc')
	    ->add_html_node($categs."<div id='addcateg'/></div>");
	    
	    $interface_content_form->add_element('faq_question_id')
	    ->add_input_node('hidden', $this->id);
	    $interface_content_form->add_element('faq_question_num_demande')
	    ->add_input_node('hidden', $this->num_demande);
	        
	    return $interface_content_form->get_display();
	}
	
	public function get_form($id_demande=0,$action="./demandes.php?categ=faq&sub=question"){
		global $msg;
		global $pmb_javascript_office_editor,$base_path;
		global $faq_question_js_form;
		
		if ($pmb_javascript_office_editor) {
			print $pmb_javascript_office_editor ;
			print "<script type='text/javascript'>
                pmb_include('$base_path/javascript/tinyMCE_interface.js');
            </script>";
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
 		
 		$interface_form = new interface_form('faq_question_form');
		if($this->id){
			$interface_form->set_label($msg['faq_question_edit_form']);
		}else{
			$interface_form->set_label($msg['faq_question_new_form']);
		}
		
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['faq_question_confirm_suppression'])
		->set_content_form($this->get_content_form())
		->set_table_name('faq_questions');
		return $interface_form->get_display().$faq_question_js_form;
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
		
		$faq_question_id = intval($faq_question_id);
		if($this->id == $faq_question_id){
			$this->num_theme = intval($id_theme);
			$this->num_type = intval($id_type);
			$this->num_demande = intval($faq_question_num_demande);
			$this->question = stripslashes($faq_question_question);
			$this->question_userdate = stripslashes($faq_question_question_date);
			$this->question_date = detectFormatDate($this->question_userdate);
			$this->answer = stripslashes($faq_question_answer);
			$this->answer_userdate = stripslashes($faq_question_answer_date);
			$this->answer_date = detectFormatDate($this->answer_userdate);
			$this->statut = intval($faq_question_statut);
			$this->descriptors=array();
			for ($i=0 ; $i<$max_categ ; $i++){
				$categ_id = 'f_categ_id'.$i;
				global ${$categ_id};
				if(intval(${$categ_id}) > 0){
					$this->descriptors[] = ${$categ_id};
				}
			}
		}else{
			return false;
		}
		return true;
	}
	
	public function save(){
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
		$result = pmb_mysql_query($query.$where);
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();
		}
 		if($result){
 			$query = "delete from faq_questions_categories where num_faq_question = ".$this->id;
 			$result = pmb_mysql_query($query);
 			if($result){
 				$query = "insert into faq_questions_categories (num_faq_question,num_categ,categ_order) values ";
 				$insert = "";
 				for ($i=0 ; $i<count($this->descriptors) ; $i++){
 					if($insert) $insert.=", ";
					$insert.="(".$this->id.",".$this->descriptors[$i].",".$i.")";					
 				}
 				if($insert){
 					$result = pmb_mysql_query($query.$insert);
				}
 			}
 		}
 		
 		if($result){
 		    static::maj_indexation($this->id);
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
	
	public static function maj_indexation($id){
	    global $include_path;
	    $index = indexations_collection::get_indexation(AUT_TABLE_FAQ);
	    $index->maj($id);
	}
}