<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_pages.class.php,v 1.12 2023/12/15 08:00:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/cms/cms_pages.tpl.php");
require_once($include_path."/cms/cms.inc.php");

class cms_pages {	
	public $list= array();			// tableau contenant les ids des pages à lister...
	public $data= array();			// tableau contenant les données des pages à lister...
	public $pages_classement_list = array();

	public function __construct(){
		$this->fetch_data();
	}
	
	protected function fetch_data(){
		$this->list = array();
		$this->data = array();
		$this->pages_classement_list=array();
		$requete = "select id_page from cms_pages order by page_name ";
		$res = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$this->list[]=$row->id_page;
				$this->data[$row->id_page]['id']=$row->id_page;
				$page=new cms_page($row->id_page);
				$this->data[$row->id_page]['name']=$page->name;
				$this->data[$row->id_page]['hash']=$page->hash;
				$this->data[$row->id_page]['description']=$page->description;
				$this->data[$row->id_page]['classement']=$page->classement;
				if($page->classement)$this->pages_classement_list[$page->classement]=1;
			}
		}
		//printr($this->data);
	}
	
	public function get_list($tpl="",$item_tpl=""){
		global $charset;
		global $cms_build_pages_tpl;
		global $cms_build_pages_tpl_item;
		
		if(!$tpl) {
		    $tpl=$cms_build_pages_tpl;
		}
		$items="";
		$pair_impair = "even";
		foreach($this->data as $page ){
		    if(!$item_tpl) {
		        $item=$cms_build_pages_tpl_item;
		    } else {
		        $item=	$item_tpl;
		    }
			if($pair_impair == "even") $pair_impair = "odd"; else $pair_impair = "even";
			
			$item = str_replace("!!pair_impair!!",$pair_impair,$item);
			$item = str_replace("!!name!!",htmlentities($page['name'],ENT_QUOTES, $charset),$item);
			$item = str_replace("!!id!!",$page['id'],$item);
			$items.=$item;
		}
		$tpl= str_replace("!!items!!",$items,$tpl);
		return $tpl;
	}

	
	public function build_item($id,$tpl_item){
		global $charset;
					
		$page=$this->data[$id];
		$item=$tpl_item;			
		
		$item = str_replace("!!name!!",htmlentities($page['name'],ENT_QUOTES, $charset),$item);
		$item = str_replace("!!id!!",$page['id'],$item);			
		
		return $item;
	}	
}// End of class


class cms_page {
	public $id;		// identifiant de l'objet
	public $hash;	// hash de l'objet
	public $name = "";	// nom
	public $description = "";	// description
	public $vars= array();	// Variables d'environnement
	public $classement = 0;
	
	public function __construct($id=""){
	    $this->id= (int) $id;		
		if($this->id){
			$this->fetch_data();
		}
	}
	
	protected function fetch_data(){
		$this->hash = "";
		$this->name = "";
		$this->description = "";
		$this->vars= array();
		
		if(!$this->id)	return false;					
		// les infos base...	
		$rqt = "select * from cms_pages where id_page ='".$this->id."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$row = pmb_mysql_fetch_object($res);
			$this->hash = $row->page_hash;
			$this->name = $row->page_name;
			$this->description = $row->page_description;
			$this->classement = $row->page_classement;
		}		
		// Variables d'environnement
		$rqt = "select * from cms_vars where var_num_page ='".$this->id."' order by var_name";
		$res = pmb_mysql_query($rqt);	
		$i=0;	
		if(pmb_mysql_num_rows($res)){					
			while($row = pmb_mysql_fetch_object($res)){
				$this->vars[$i]['id']=$row->id_var;
				$this->vars[$i]['name']=$row->var_name;
				$this->vars[$i]['comment']=$row->var_comment;
				$i++;
			}	
		}				
	}	
	
	public function get_form($ajax=0){
		global $msg;
		global $charset;
		global $cms_page_form_tpl,$cms_page_form_del_button_tpl;
		global $cms_page_form_ajax_tpl;
		global $cms_page_form_var_tpl_0;
		global $cms_page_form_var_tpl;
		
		if($ajax)$tpl= $cms_page_form_ajax_tpl;	
		else $tpl=$cms_page_form_tpl;	
		
		$tpl = str_replace("!!name!!",htmlentities($this->name ?? "", ENT_QUOTES, $charset),$tpl);
		$tpl = str_replace("!!description!!",htmlentities($this->description ?? "", ENT_QUOTES, $charset),$tpl);
		if($this->id){
			$tpl = str_replace("!!form_title!!",htmlentities($msg["cms_page_form_title"] ,ENT_QUOTES, $charset),$tpl);
			$tpl = str_replace("!!cms_page_form_suppr!!",$cms_page_form_del_button_tpl,$tpl);
		}else{
			$tpl = str_replace("!!form_title!!",htmlentities($msg["cms_new_page_form_title"] ,ENT_QUOTES, $charset),$tpl);
			$tpl = str_replace("!!cms_page_form_suppr!!","",$tpl);
		}	
		$tpl = str_replace("!!id!!",$this->id,$tpl);	
		$item=$cms_page_form_var_tpl_0;
		$items="";
		$cpt=1;
		if(!count($this->vars)){
			$item = str_replace("!!var_name!!","",$item);
			$item = str_replace("!!var_comment!!","",$item);		
			$item = str_replace("!!var_id!!","",$item);
			$item = str_replace("!!cpt!!",$cpt,$item);
			$items=$item;			
		}
		foreach($this->vars as $var ){	
			$item = str_replace("!!var_name!!",$var['name'],$item);
			$item = str_replace("!!var_comment!!",$var['comment'],$item);			
			$item = str_replace("!!var_id!!",$var['id'],$item);
			$item = str_replace("!!cpt!!",$cpt,$item);
			$cpt++;			
			$items.=$item;		
			$item=$cms_page_form_var_tpl;		
		}
		$items = str_replace("!!var_count!!",$cpt,$items);
		$tpl = str_replace("!!var_list!!",$items,$tpl);	
		
		return $tpl;
	}
	
	public function get_from_form(){		
		global $id;
		global $name;
		global $description;	
		global $var_count;	
			
		$this->id = (int) $id;
		$this->name = stripslashes($name);
		$this->description = stripslashes($description);
		$this->vars= array();	
		(int) $var_count;	
		for($i=0; $i<$var_count; $i++){
			$cpt=$i+1;
			$name="var_name_".$cpt;
			$comment="var_comment_".$cpt;
			global ${$name};
			global ${$comment};
			if(${$name}){
				$this->vars[$i]['name']=stripslashes( ${$name});
				$this->vars[$i]['comment']=stripslashes( ${$comment});			
			}	
		}			
		
	}	
		
	public function save_page_classement($id_page,$classement){		
		$id_page = intval($id_page);
		$query = "update cms_pages set page_classement='$classement' where id_cadre = ".$id_page;
		pmb_mysql_query($query);
	}	
	
	public function save(){
		if(!$this->name) return;
		if($this->id){
			$save = "update ";
			$clause = "where id_page = '".$this->id."'";
		}else{
			$save = "insert into ";
			$clause = "";
		}
		$save.= "cms_pages set 
		page_name = '".addslashes($this->name)."', 
		page_description = '".addslashes($this->description)."'
		$clause";
		pmb_mysql_query($save);
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();
			$hash=cms_hash_new("cms_pages",$this->id);
			$req="update cms_pages set page_hash = '".addslashes($hash)."' where id_page = '".$this->id."'";
			pmb_mysql_query($req);
		}
		$this->delete_vars();
		foreach($this->vars as $var ){
			$req = "insert into cms_vars set 
				var_num_page= ".$this->id.",
				var_name = '".addslashes($var['name'])."', 
				var_comment = '".addslashes($var['comment'])."'
			";
			pmb_mysql_query($req);
		}	
		$this->fetch_data();			
	}	
	
	public function delete(){
		$this->delete_vars();
		$del = "delete from cms_pages where id_page='".$this->id."'";
		pmb_mysql_query($del);
		cms_hash_del($this->hash);		
		$this->id=0;		
		return 0;
	}

	public function delete_vars(){
		$del = "delete from cms_vars where var_num_page='".$this->id."'";
		pmb_mysql_query($del);
		return 0;
	}
	
	public function get_exported_datas(){
		$infos = array(
			'id' => $this->id,
			'hash' => $this->hash,
			'name' => $this->name,
			'description' => $this->description,
			'env_var' => $this->vars
		);
		return $infos;
	}
	
	public function get_id() {
		return $this->id;
	}
}// End of class
