<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_pages.class.php,v 1.5.4.1 2025/02/12 12:34:07 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
	public $name;	// nom
	public $description;	// description
	public $vars= array();	// Variables d'environnement

	public function __construct($id=""){
		$this->id= intval($id);
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

	public function get_env(){
		return $this->vars;
	}

	public function get_id() {
		return $this->id;
	}

}// End of class
