<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_jsonrest.class.php,v 1.1 2015/10/07 14:35:59 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_jsonrest extends cms_module_common_datasource_list{
	protected $datas;
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function save_form(){
		$this->clean_tmp(true);
		return parent::save_form();
	}
	
	protected function get_store_hash(){
		return "cmsrest_".SESSid."_".md5(serialize($this->parameters));
	}
	
	protected function get_jsonstore_url(){
		return "./cms_rest.php/".$this->get_store_hash();
	}
	
	protected function save_store(){		
		$selector = $this->get_selected_selector();
		file_put_contents("./temp/".$this->get_store_hash(), serialize(array(
			'id' => $this->id,
			'classname' => get_class($this),
			'selector_value' => $selector->get_value(),
			'datas' => $this->datas
		)));
	}
	
	public function get_datas(){
		$this->clean_tmp();
		if(file_exists('./temp/'.$this->get_store_hash())){
			$content = unserialize(file_get_contents("./temp/".$this->get_store_hash()));
			$this->datas = $content['datas'];
		}
		$this->save_store();
		return array(
			'jsonstore'=> $this->get_jsonstore_url()
		);
	}	
	
	public function set_datas($datas){
		$this->datas = $datas;
	}
	
	public function store_proceed($content){
		//Must be rewrite
	}
	
	private function clean_tmp($force = false){
		$dh = opendir('./opac_css/temp');
		while($file = readdir($dh)){
			if($file != 'CVS' && substr($file,0,8) === "cmsrest_"){
				if($force){
					unlink('./opac_css/temp/'.$file);
				}
				$this->check_file($file);
			}
		}
	}
	
	private function check_file($filename){
		$infos = explode("_",$filename);
		$query = 'SELECT SESSID SESSNAME FROM sessions WHERE SESSID = "'.$infos[1].'"';
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)){
 			unlink('./opac_css/temp/'.$filename);
		}		
	}
}