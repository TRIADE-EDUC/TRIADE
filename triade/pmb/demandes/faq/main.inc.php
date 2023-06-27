<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2019-04-11 08:03:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($action)) $action = '';
if(!isset($id)) $id = 0;
if(!isset($id_theme)) $id_theme = 0;
if(!isset($id_type)) $id_type = 0;
if(!isset($id_statut)) $id_statut = 0;
if(!isset($num_demande)) $num_demande = 0;

require_once($class_path."/faq_question.class.php");
require_once($class_path."/faq_questions.class.php");

switch($action){
	case "new":
		$question = new faq_question($id);
		print $question->get_form($num_demande);
		break;
	case "edit" :
		$question = new faq_question($id);
		print $question->get_form();
		break;	
	case "save" :
		$question = new faq_question($faq_question_id);
		$result = $question->get_value_from_form();
		if($result){
			$result =$question->save();
		}
		if(!$result){
			error_form_message($msg['faq_question_save_error']);
		}
		print faq_questions::get_list();
		break;
	case "delete" :
		$result = faq_question::delete($id);
		if(!$result){
			error_message("", $msg['faq_question_delete_error']);
			print "<div class='row'>&nbsp;</div>";
		}
		print faq_questions::get_list();
		break;
	case "list" :
	default :
		print faq_questions::get_list(true,$id_theme,$id_type,$id_statut);		
		break;
}
?>