<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_publications_states.class.php,v 1.11 2018-05-26 07:27:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/cms/cms_editorial_publications_states.tpl.php");
require_once($class_path."/cms/cms_cache.class.php");

class cms_editorial_publications_states {
	public $publications_states;	//tableau des statuts de publication
	
	public function __construct(){
		$this->publications_states = array();
	}
	
	protected function fetch_data_cache(){
		if($tmp=cms_cache::get_at_cms_cache($this)){
			$this->restore($tmp);
		}else{
			$this->fetch_data();
			cms_cache::set_at_cms_cache($this);
		}
	}
	
	protected function restore($cms_object){
		foreach(get_object_vars($cms_object) as $propertieName=>$propertieValue){
			$this->{$propertieName}=$propertieValue;
		}
	}

	protected function fetch_data(){
		$rqt = "select * from cms_editorial_publications_states order by editorial_publication_state_label asc";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->publications_states[] =array(
					'id' => $row->id_publication_state,
					'label' => $row->editorial_publication_state_label,
					'opac_show' => $row->editorial_publication_state_opac_show,
					'auth_opac_show' => $row->editorial_publication_state_auth_opac_show,
					'class_html' => $row->editorial_publication_state_class_html
				);
			}
		}
	}

	public function get_publications_states(){
		if(!$this->publications_states) {
			$this->fetch_data_cache();
		}
		return $this->publications_states;
	}

	public function get_selector_options($selected=0){
		global $charset;
		global $deflt_cms_article_statut;
		
		if(!$selected){
			$selected=$deflt_cms_article_statut;
		}		
		$options = "";
		$this->get_publications_states();
		for($i=0 ; $i<count($this->publications_states) ; $i++){
			$options.= "
			<option value='".$this->publications_states[$i]['id']."'".($this->publications_states[$i]['id']==$selected ? "selected='selected'" : "").">".htmlentities($this->publications_states[$i]['label'],ENT_QUOTES,$charset)."</option>";	
		}
		return $options;
	}
	
	public function get_table($form_link="./admin.php?categ=cms_editorial&sub=publication_state&action=edit"){
		global $msg,$charset;
		$this->get_publications_states();
		$table = "
		<table>
			<tr>
				<th>".$msg['editorial_content_publication_state_label']."</th>
				<th>".$msg['editorial_content_publication_state_visible']."</th>
				<th>".$msg['editorial_content_publication_state_visible_abo']."</th>
			</tr>";
		
		for($i=0 ; $i<count($this->publications_states) ; $i++){
			$class = ($i%2 ? "odd":"even");
			$table.= "
			<tr class='".($i%2 ? "odd":"even")."' onclick='document.location=\"".$form_link."&id=".$this->publications_states[$i]['id']."\"' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($i%2 ? "odd":"even")."'\">
				<td><span class='".$this->publications_states[$i]['class_html']."'  style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span>
					".htmlentities($this->publications_states[$i]['label'],ENT_QUOTES,$charset)."
				</td>
				<td>".($this->publications_states[$i]['opac_show'] ? "X" : "")."</td>
				<td>".($this->publications_states[$i]['auth_opac_show'] ? "X" : "")."</td>
			</tr>";
		}
		$table.= "
		</table>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<input type='button' class='bouton' value='".$msg['editorial_content_publication_state_add']."' onclick='document.location=\"".$form_link."\"'/>
		</div>";
		return $table;
	}
	
	public function get_form($id=0,$url="./admin.php?categ=cms_editorial&sub=publication_state"){
		global $msg,$charset;
		global $cms_editorial_publication_state_form;
		$this->get_publications_states();
		
		$form =str_replace("!!action!!",$url,$cms_editorial_publication_state_form);
		if($id){
			for($i=0 ; $i<count($this->publications_states) ; $i++){
				if($this->publications_states[$i]['id'] == $id){
					$publication_state = $this->publications_states[$i];
					break;
				}
			}
		}
		if($publication_state['id']){
			$form = str_replace("!!form_title!!",$msg['editorial_content_publication_state_edit'],$form);
			$form = str_replace("!!label!!",htmlentities($publication_state['label'],ENT_QUOTES,$charset),$form);
			$form = str_replace("!!visible!!",($publication_state['opac_show'] ? "checked='checked'": ""),$form);
			$form = str_replace("!!visible_abo!!",($publication_state['auth_opac_show'] ? "checked='checked'": ""),$form);
			$form = str_replace("!!id!!",$publication_state['id'],$form);
			$form = str_replace("!!bouton_supprimer!!","<input type='button' class='bouton' value=' ".$msg[63]." ' onclick='confirmation_delete(\"&action=delete&id=".$publication_state['id']."\",\"".htmlentities($publication_state['label'],ENT_QUOTES,$charset)."\")'/>",$form);
			$form.= confirmation_delete($url);
		}else{
			$form = str_replace("!!form_title!!",$msg['editorial_content_publication_state_add'],$form);	
			$form = str_replace("!!label!!","",$form);
			$form = str_replace("!!visible!!","",$form);
			$form = str_replace("!!visible_abo!!","",$form);
			$form = str_replace("!!id!!",0,$form);
			$form = str_replace("!!bouton_supprimer!!","",$form);
		}
		for ($i=1;$i<=20; $i++) {
			if ($publication_state['class_html']=="statutnot".$i) $checked = "checked";
			else $checked = "";
			$couleur[$i]="<span for='statutnot".$i."' class='statutnot".$i."' style='margin: 7px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' />
					<input id='statutnot".$i."' type=radio name='cms_editorial_publication_state_class_html' value='statutnot".$i."' $checked class='checkbox' /></span>";
			if ($i==10) $couleur[10].="<br />";
			elseif ($i!=20) $couleur[$i].="<b>|</b>";
		}
		$couleurs=implode("",$couleur);
		$form = str_replace('!!class_html!!', $couleurs, $form);
		
		return $form;
	}
	
	public function save(){
		global $dbh;
		global $cms_editorial_publication_state_label,$cms_editorial_publication_state_visible,$cms_editorial_publication_state_visible_abo,$cms_editorial_publication_state_id;
		global $cms_editorial_publication_state_class_html;
		if($cms_editorial_publication_state_id){
			$cms_editorial_publication_state_id+=0;
			$query = "update cms_editorial_publications_states set ";
			$clause = "where id_publication_state = ".$cms_editorial_publication_state_id;
		}else{
			$query = "insert into cms_editorial_publications_states set ";
			$clause = "";
		}
		$query.= "
			editorial_publication_state_label = '".$cms_editorial_publication_state_label."',
			editorial_publication_state_opac_show = ".($cms_editorial_publication_state_visible ? 1 : 0).",
			editorial_publication_state_auth_opac_show = ".($cms_editorial_publication_state_visible_abo ? 1 : 0).",
			editorial_publication_state_class_html = '".$cms_editorial_publication_state_class_html."'";
		$query.= " ".$clause;
		pmb_mysql_query($query);
	}
	
	public function delete($id){
		global $msg,$charset;
		$id+=0;
		if($id){
			//on regarde si le statut est utilisé dans les rubriques
			$query = "select id_section from cms_sections where section_publication_state = ".$id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$error = $msg['publication_state_used_in_section'];
			}else{
				//on regarde si le statut est utilisé dans les articles
				$query = "select id_article from cms_articles where article_publication_state = ".$id;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					$error = $msg['publication_state_used_in_article'];
				}
			}
		}
		if($error){
			print "
			<script type='text/javascript'>
				alert(\"".$msg['cant_delete'].". ".$error."\");
			</script>";
		}else{
			$query = "delete from cms_editorial_publications_states where id_publication_state = ".$id;
			pmb_mysql_query($query);
		}
	}
}