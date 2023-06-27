<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_authority.class.php,v 1.9 2018-07-11 15:08:02 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/authperso.class.php");

class authperso_authority {
	public $id=0; // id de l'autorité 
	public $info=array();
	public $elt_id=0;
	
	private static $authpersos=array();
	
	public function __construct($id=0) {
		$this->id=$id+0;
		$this->fetch_data();
	}
	
	public function fetch_data() {
		$this->info=array();
		if(!$this->id) return;

		$req="select authperso_authority_authperso_num from authperso_authorities where id_authperso_authority=". $this->id;
		$res = pmb_mysql_query($req);
		if(($r=pmb_mysql_fetch_object($res))) {
			$authperso=$this->get_authperso_class($r->authperso_authority_authperso_num);
			//$this->info['isbd']=$authperso->get_isbd($this->id);
			//$this->info['view']=$authperso->get_view($this->id);
			$this->info['authperso']=$authperso->get_data();
			$this->info['data']=$authperso->fetch_data_auth($this->id);
			$this->info['authperso_num']=$r->authperso_authority_authperso_num;
		}
	}
	
	private function get_authperso_class($id_type_authperso){
		if(!isset(self::$authpersos[$id_type_authperso])){
			self::$authpersos[$id_type_authperso] = new authperso($id_type_authperso);
		}
		return self::$authpersos[$id_type_authperso];
	}
	
	public function get_data() {
		return $this->info;
	}
	
	public function get_isbd() {
		if(!isset($this->info['isbd'])){
			$this->info['isbd'] = authperso::get_isbd($this->id);
		}
		return $this->info['isbd'];
	}
	
	public function get_view() {
		if(!isset($this->info['view'])){
		    $authperso=$this->get_authperso_class($this->info['authperso_num']);
			$this->info['view']=$authperso->get_view($this->id);
		}
		return $this->info['isbd'];
	}
	
	public function get_authperso_num() {
		return $this->info['authperso_num'];
	}
	
	public function print_resume() {
		if(!$this->info['view'])return($this->info['authperso']['name'] ." : ".$this->info['isbd']);	
		else return $this->info['view'];
	}
	
	public function get_header() {
		return $this->get_isbd();
	}
	
	public function is_event() {
		return $this->info['authperso']['event'];
	}
	
	public function get_gestion_link(){
		return './autorites.php?categ=see&sub=authperso&id='.$this->id;
	}

	public static function get_format_data_structure($antiloop = false) {
		$main_fields = array();
		$main_fields[] = array(
				'var' => "name",
				'desc' => ''
		);
		$main_fields[] = array(
				'var' => "info",
				'desc' => '',
				'children' => array(
						array(
								'var' => "info.view",
								'desc' => '',
						),
						array(
								'var' => "info.isbd",
								'desc' => '',
						),
				)
		);
		$authority = new authority(0, 0, AUT_TABLE_AUTHPERSO);
		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
	}
	
	public function format_datas($antiloop = false){
		$formatted_data = array();
		$formatted_data['name'] = $this->info['authperso']['name'];
		$formatted_data['info']['view'] = $this->get_view();
		$formatted_data['info']['isbd'] = $this->get_isbd();
		$authority = new authority(0, $this->id, AUT_TABLE_AUTHPERSO);
		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}
} //authperso class end

