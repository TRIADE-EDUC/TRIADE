<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_admin.class.php,v 1.14 2018-12-28 16:27:31 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/authperso_admin.tpl.php");
require_once($include_path."/templates/parametres_perso.tpl.php");
require_once($class_path."/custom_parametres_perso.class.php");

class authperso_admin {
	public $id=0;
	public $info=array();
	
	
	public function __construct($id=0) {
		$this->id=$id+0;
		$this->fetch_data();
	}
	
	public function fetch_data() {
		global $include_path;
		
		$this->info=array();
		$this->info['fields']=array();
		if(!$this->id) {
			$this->info['onglet_num']= 0;
			$this->info['opac_search']= 0;
			$this->info['opac_multi_search']= 0;
			$this->info['gestion_search']= 0;
			$this->info['gestion_multi_search']= 0;
			$this->info['oeuvre_event']= 0;
			return;
		}
		
		$req="select * from authperso where id_authperso=". $this->id;		
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);		
			$this->info['id']= $r->id_authperso;	
			$this->info['name']= $r->authperso_name;
			$this->info['onglet_num']= $r->authperso_notice_onglet_num;		
			$this->info['isbd_script']= $r->authperso_isbd_script;			
			$this->info['view_script']= $r->authperso_view_script;			
			$this->info['opac_search']= $r->authperso_opac_search;			
			$this->info['opac_multi_search']= $r->authperso_gestion_multi_search;			
			$this->info['gestion_search']= $r->authperso_gestion_search;			
			$this->info['gestion_multi_search']= $r->authperso_gestion_multi_search;	
			$this->info['oeuvre_event']= $r->authperso_oeuvre_event;				
			$this->info['comment']= $r->authperso_comment;	
			$this->info['onglet_name']="";
			$req="SELECT * FROM notice_onglet where id_onglet=".$r->authperso_notice_onglet_num;
			$resultat=pmb_mysql_query($req);
			if (pmb_mysql_num_rows($resultat)) {
				$r_onglet=pmb_mysql_fetch_object($resultat);	
				$this->info['onglet_name']= $r_onglet->onglet_name;						
			}
		}
	}
 
	public function get_form() {
		global $authperso_form_tpl,$msg,$charset;		
		
		$tpl=$authperso_form_tpl;
		if($this->id){
			$tpl=str_replace('!!msg_title!!',$msg['admin_authperso_form_edit'],$tpl);
			//bouton supprimer
			$req="select * from authperso_authorities where authperso_authority_authperso_num=". $this->id;
			$res = pmb_mysql_query($req);
			if((pmb_mysql_num_rows($res))) {
				$tpl=str_replace('!!delete!!','', $tpl);
			} else {
				$tpl=str_replace('!!delete!!',"<input type='button' class='bouton' value='".$msg['admin_authperso_delete']."'  onclick=\"document.getElementById('auth_action').value='delete';this.form.submit();\"  />", $tpl);
			}
			$name=$this->info['name'];
			$isbd_script=$this->info['isbd_script'];
			$view_script=$this->info['view_script'];
			$comment=$this->info['comment'];
		}else{ 
			$tpl=str_replace('!!msg_title!!',$msg['admin_authperso_form_add'],$tpl);
			$tpl_objet="";
			$tpl=str_replace('!!delete!!',"",$tpl);
			$name="";
			$isbd_script="";
			$view_script="";
			$comment="";
		}
		$notice_onglet_list=gen_liste ("SELECT * FROM notice_onglet", 
				"id_onglet", "onglet_name", "notice_onglet", "", $this->info['onglet_num'], 0, $msg["admin_authperso_notice_onglet_no"],0,$msg["admin_authperso_notice_onglet_sel"]);
		$multi_search_checked="";
		
		if($this->info['opac_multi_search']) $search_multi_checked= " checked='checked' ";
		else $search_multi_checked= "";
		$search_simple_checked[$this->info['opac_search']+0]= " checked='checked' ";
		$search_tpl="
			<input type='radio' ".(isset($search_simple_checked[0]) ? $search_simple_checked[0] : '')." name='search_simple' value='0' >".$msg["admin_authperso_opac_search_no"]."
			<input type='radio' ".(isset($search_simple_checked[1]) ? $search_simple_checked[1] : '')." name='search_simple' value='1' >".$msg["admin_authperso_opac_search_yes"]."
			<input type='radio' ".(isset($search_simple_checked[2]) ? $search_simple_checked[2] : '')." name='search_simple' value='2' >".$msg["admin_authperso_opac_search_yes_active"]."
		";		
		if($this->info['gestion_multi_search']) $search_multi_checked_gestion= " checked='checked' ";
		else $search_multi_checked_gestion= "";
		$search_simple_checked_gestion[$this->info['gestion_search']+0]= " checked='checked' ";
		$search_tpl_gestion="
			<input type='radio' ".(isset($search_simple_checked_gestion[0]) ? $search_simple_checked_gestion[0] : '')." name='gestion_search_simple' value='0' >".$msg["admin_authperso_gestion_search_no"]."
			<input type='radio' ".(isset($search_simple_checked_gestion[1]) ? $search_simple_checked_gestion[1] : '')." name='gestion_search_simple' value='1' >".$msg["admin_authperso_gestion_search_yes"]."
			<input type='radio' ".(isset($search_simple_checked_gestion[2]) ? $search_simple_checked_gestion[2] : '')." name='gestion_search_simple' value='2' >".$msg["admin_authperso_gestion_search_yes_active"]."
		";
		$fields_options="<select id='fields_options' name='fields_options'>";
		$fields_options.=$this->get_fields_options();
		$fields_options.="</select>";
		
		$fields_options_view="<select id='fields_options_view' name='fields'>";
		$fields_options_view.=$this->get_fields_options();
		$fields_options_view.="</select>";
		if($this->info['oeuvre_event']){
			$tpl=str_replace('!!oeuvre_event!!'," checked='checked' ",$tpl);
		}else{
			$tpl=str_replace('!!oeuvre_event!!',"",$tpl);
		}
		$tpl=str_replace('!!name!!',htmlentities($name, ENT_QUOTES, $charset),$tpl);
		$tpl=str_replace('!!notice_onglet_list!!',$notice_onglet_list,$tpl);
		$tpl=str_replace('!!fields_options!!',$fields_options,$tpl);
		$tpl=str_replace('!!isbd_script!!',htmlentities($isbd_script, ENT_QUOTES, $charset),$tpl);
		$tpl=str_replace('!!fields_options_view!!',$fields_options_view,$tpl);
		$tpl=str_replace('!!view_script!!',htmlentities($view_script, ENT_QUOTES, $charset),$tpl);
		$tpl=str_replace('!!search_simple!!',$search_tpl,$tpl);
		$tpl=str_replace('!!search_multi!!',$search_multi_checked,$tpl);
		$tpl=str_replace('!!search_simple_gestion!!',$search_tpl_gestion,$tpl);
		$tpl=str_replace('!!search_multi_gestion!!',$search_multi_checked_gestion,$tpl);
		$tpl=str_replace('!!comment!!',htmlentities($comment, ENT_QUOTES, $charset),$tpl);
		$tpl=str_replace('!!id_authperso!!',$this->id,$tpl);
		 
		return $tpl;
	}

	public function save() {
		global $dbh;
		global $name;
		global $notice_onglet;
		global $isbd_script;
		global $view_script;
		global $comment;
		global $search_simple;
		global $search_multi;
		global $gestion_search_simple;
		global $gestion_search_multi;
		global $oeuvre_event;
		global $base_path;
		
		$notice_onglet+=0;		
		$fields="
			authperso_name='".$name."',
			authperso_notice_onglet_num='".$notice_onglet."',
			authperso_isbd_script='".$isbd_script."' ,
			authperso_view_script='".$view_script."' ,
			authperso_opac_search='".$search_simple."',
			authperso_opac_multi_search='".$search_multi."',
			authperso_gestion_search='".$gestion_search_simple."',
			authperso_gestion_multi_search='".$gestion_search_multi."',
			authperso_oeuvre_event='".$oeuvre_event."',
			authperso_comment='".$comment."' 
		";		
		if(!$this->id){ // Ajout
			$req="INSERT INTO authperso SET $fields ";	
			pmb_mysql_query($req, $dbh);
			$this->id = pmb_mysql_insert_id($dbh);
		} else {
			$req="UPDATE authperso SET $fields where id_authperso=".$this->id;	
			pmb_mysql_query($req, $dbh);
			$isbd_template_path = $base_path.'/temp/'.LOCATION.'_authperso_isbd_'.$this->id;
			if(file_exists($isbd_template_path)){
				unlink($isbd_template_path);
			}
			$view_template_path = $base_path.'/temp/'.LOCATION.'_authperso_view_'.$this->id;
			if(file_exists($view_template_path)){
				unlink($view_template_path);
			}
		}	
		$this->fetch_data();
	}	
	
	public function delete() {
		global $dbh;
		global $option_navigation,$option_visibilite;
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id,"./admin.php?categ=authorities&sub=authperso&auth_action=edition&id_authperso=".$this->id,$option_navigation,$option_visibilite);
		$p_perso->delete_all();

		$query = "delete from authperso_authorities where  authperso_authority_authperso_num = '".$this->id."' ";
		$result = pmb_mysql_query($query);
		
		$req="DELETE from authperso WHERE id_authperso=".$this->id;
		pmb_mysql_query($req, $dbh);	
		$this->id=0;		
		$this->fetch_data();	
	}	

	public function fields_edition() {
		global $msg;
		
		$option_visibilite=array();
		$option_visibilite["multiple"]="block";
		$option_visibilite["obligatoire"]="block";
		$option_visibilite["search"]="block";
		$option_visibilite["export"]="none";
		$option_visibilite["exclusion"]="none";
		$option_visibilite["opac_sort"]="none";
		
		$option_navigation=array();
		$option_navigation['msg_title']=$msg["admin_menu_docs_perso_authperso"]." : ".$this->info['name'];
		$option_navigation['url_return_list']="./admin.php?categ=authorities&sub=authperso&auth_action=";
		$option_navigation['msg_return_list']=$msg["admin_authperso_return_list"];

		$option_navigation['url_update_global_index']="./admin.php?categ=authorities&sub=authperso&auth_action=update_global_index&id_authperso=".$this->id;
		$option_navigation['msg_update_global_index']=$msg["admin_authperso_update_global_index"];
		
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id,"./admin.php?categ=authorities&sub=authperso&auth_action=edition&id_authperso=".$this->id,$option_navigation,$option_visibilite);
		
		$p_perso->proceed();
	}
	
	public function get_fields_options(){
		global $msg;
		$p_perso=new custom_parametres_perso("authperso","authperso",$this->id);
				
		return $p_perso->get_selector_options_1()."<option value='{% for index_concept in index_concepts %}
   {{index_concept.label}}
{% endfor %}'>index_concepts</option>";
	}		
	
	public function get_gestion() {
		global $msg;
		$tpl="<h1>".$msg[140]."&nbsp;: ". $msg[133]."</h1>";
	}
} //authperso class end





class authperso_admins {	
	public $info=array();
	
	public function __construct() {
		$this->fetch_data();
	}
	
	public function fetch_data() {
		global $PMBuserid;
		$this->info=array();
		$i=0;
		$req="select * from authperso ";
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$this->info[$i]= $authperso=new authperso_admin($r->id_authperso);					
				$i++;
			}
		}
	}
				
	public function get_list() {
		global $authperso_list_tpl,$authperso_list_line_tpl,$msg;
		
		$tpl=$authperso_list_tpl;
		$tpl_list="";
		$odd_even="odd";
		foreach($this->info as $elt){
			$tpl_elt=$authperso_list_line_tpl;
			if($odd_even=='odd')$odd_even="even"; else $odd_even="odd";
			
			if($elt->info['opac_multi_search']) $multi_search="x"; else $multi_search="";
			$simple_search="";
			if($elt->info['opac_search']==1) $simple_search="x";
			if($elt->info['opac_search']==2) $simple_search=$msg['admin_authperso_opac_search_simple_list_valid'];
			
			if($elt->info['gestion_multi_search']) $gestion_multi_search="x"; else $gestion_multi_search="";
			$gestion_simple_search="";
			if($elt->info['gestion_search']==1) $gestion_simple_search="x";
			if($elt->info['gestion_search']==2) $gestion_simple_search=$msg['admin_authperso_gestion_search_simple_list_valid'];
			if($elt->info['oeuvre_event']) $gestion_oeuvre_event="x";
			else $gestion_oeuvre_event="";
			
			$tpl_elt=str_replace('!!odd_even!!',$odd_even, $tpl_elt);	
			$tpl_elt=str_replace('!!name!!',$elt->info['name'], $tpl_elt);	
			$tpl_elt=str_replace('!!notice_onglet!!',$elt->info['onglet_name'],$tpl_elt);
			$tpl_elt=str_replace('!!simple_search!!',$simple_search, $tpl_elt);	
			$tpl_elt=str_replace('!!multi_search!!',$multi_search, $tpl_elt);	
			$tpl_elt=str_replace('!!gestion_simple_search!!',$gestion_simple_search, $tpl_elt);	
			$tpl_elt=str_replace('!!gestion_multi_search!!',$gestion_multi_search, $tpl_elt);	
			$tpl_elt=str_replace('!!comment!!',$elt->info['comment'], $tpl_elt);	
			$tpl_elt=str_replace('!!oeuvre_event!!',$gestion_oeuvre_event, $tpl_elt);	
			$tpl_elt=str_replace('!!id!!',$elt->info['id'], $tpl_elt);	
			$tpl_list.=$tpl_elt;	
		}
		$tpl=str_replace('!!list!!',$tpl_list, $tpl);
		return $tpl;
	}	

    	
} // authpersos class end
	
