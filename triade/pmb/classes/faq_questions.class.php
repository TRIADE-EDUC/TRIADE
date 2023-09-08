<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq_questions.class.php,v 1.4 2019-04-11 09:40:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/templates/faq_question.tpl.php");
require_once($class_path."/faq_types.class.php");
require_once($class_path."/faq_themes.class.php");

class faq_questions  {

	public static function get_list($filter = true,$id_theme=0, $id_type=0, $id_statut=0){
		global $msg,$charset,$dbh;
		global $javascript_path, $pmb_opac_url;
		global $module,$categ,$sub;
		
		$query = "select * from faq_questions join faq_themes on faq_question_num_theme = id_theme join faq_types on faq_question_num_type = id_type";
		$id_theme+=0;
		$id_type+=0;
		$list = $restrict = "";
		
		
		if($filter){
			$types = new faq_types("faq_types", "id_type", "libelle_type");
			$themes = new faq_themes("faq_themes","id_theme","libelle_theme");
			$list.="
		<form class='form-".$module." name='faq_filter' action='./demandes.php' method='get'>
			<h3>".htmlentities($msg['faq_filter_form_title'],ENT_QUOTES,$charset)."</h3>
			<div class='form-contenu'>
				<input type='hidden' name='categ' value='".htmlentities($categ,ENT_QUOTES,$charset)."' />
				<input type='hidden' name='sub' value='".htmlentities($sub,ENT_QUOTES,$charset)."' />		
				<div class='row'>
					<div class='colonne3'>
						<label for='faq_filter_type'>".htmlentities($msg['faq_question_theme_label'],ENT_QUOTES,$charset)."</label><br/>
						".$themes->getListSelector($id_theme,"",true)."
					</div>			
					<div class='colonne3'>
						<label for='faq_filter_type'>".htmlentities($msg['faq_question_type_label'],ENT_QUOTES,$charset)."</label><br/>
						".$types->getListSelector($id_type,"",true)."
					</div>
					<div class='colonne_suite'>
						<label for='id_statut'>".$msg['faq_question_statut_label']."</label><br/>
						<select name='id_statut' >
							<option value='0'".($id_statut == 0 ? " selected='selected'" : "").">".$msg['faq_question_statut_visible_0']."</option>
							<option value='1'".($id_statut == 1 ? " selected='selected'" : "").">".$msg['faq_question_statut_visible_1']."</option>
							<option value='2'".($id_statut == 2 ? " selected='selected'" : "").">".$msg['faq_question_statut_visible_2']."</option>
							<option value='3'".($id_statut == 3 ? " selected='selected'" : "").">".$msg['faq_question_statut_visible_3']."</option>
						</select>
					</div>
				</div>	
				<div class='row'></div>				
			</div>
			<div class='row'>
				<div class='left'>
					<input type='submit' class='bouton' value='".htmlentities($msg['faq_filter_form_submit'],ENT_QUOTES,$charset)."'/>
				</div>
			</div>
			<div class='row'></div>
		</form>";
		}
		$list.= "
		<script type='text/javascript' src='".$javascript_path."/sorttable.js'></script>
		<table class='sortable'>	
			<tr>
				<th>".htmlentities($msg['faq_question_theme_label'],ENT_QUOTES,$charset)."</th>
				<th>".htmlentities($msg['faq_question_type_label'],ENT_QUOTES,$charset)."</th>
				<th>".htmlentities($msg['faq_question_statut'],ENT_QUOTES,$charset)."</th>		
				<th>".htmlentities($msg['faq_question_question'],ENT_QUOTES,$charset)."</th>
				<th>".htmlentities($msg['faq_question_answer'],ENT_QUOTES,$charset)."</th>
			</tr>";
		if($id_theme || $id_type || $id_statut){
			if($id_type){
				$restrict = "faq_question_num_type = ".$id_type;
			}
			if($id_theme){
				if($restrict) $restrict.= " and ";
				$restrict.= "faq_question_num_theme = ".$id_theme;
			}
			if($id_statut){
				if($restrict) $restrict.= " and ";
				$restrict.= "faq_question_statut = ".$id_statut;
			}
		}
		if($restrict){
			$query.= " where ".$restrict;
		}
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			$i=0;
			while($row = pmb_mysql_fetch_object($result)){
				//pour l'affichage dans la liste, on nettoie !
				$question = strip_tags($row->faq_question_question);
				$question_title="";
				if(strlen($question) > 200){
					$question_title = $question;
					$question = substr($question,0,200)."[...]";
				}
				$answer = strip_tags($row->faq_question_answer);
				$answer_title="";
				if(strlen($answer) > 200){
					$answer_title = $answer;
					$answer = pmb_substr($answer,0,200)."[...]";
				}
				
				$list.="
			<tr class='".($i%2 !=0 ? "even" : "odd")."' style='cursor:pointer;' onclick='document.location=\"./demandes.php?categ=faq&sub=question&action=edit&id=".$row->id_faq_question."\"'>
				<td>".htmlentities($row->libelle_theme,ENT_QUOTES,$charset)."</td>
				<td>".htmlentities($row->libelle_type,ENT_QUOTES,$charset)."</td>
				<td>".htmlentities($msg['faq_question_statut_visible_'.$row->faq_question_statut],ENT_QUOTES,$charset)."</td>
				<td ".($question_title ? "title='".htmlentities($question_title,ENT_QUOTES,$charset)."'" : "").">".htmlentities($question,ENT_QUOTES,$charset)."</td>
				<td ".($answer_title ? "title='".htmlentities($answer_title,ENT_QUOTES,$charset)."'" : "").">".htmlentities($answer,ENT_QUOTES,$charset)."</td>
			</tr>";
			$i++;	
			}
		}else{
			$list.= "
			<tr>
				<td colspan='5'>".htmlentities($msg['faq_no_question'],ENT_QUOTES,$charset)."</td>
			</tr>";
		}
		$list.="
		</table>
		<div class='row'>
			<div class='left'>
				<input type='button' class='bouton' onclick='document.location=\"./demandes.php?categ=faq&sub=question&action=new\"' value='".htmlentities($msg['faq_add_new_question'],ENT_QUOTES,$charset)."'/>
			</div>
			<div class='right'>
				<a href='".$pmb_opac_url."index.php?lvl=faq' target='_blank'>".$msg['opac_faq_link']."</a>
			</div>
		</div>";
		return $list;
	}
}