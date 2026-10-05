<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_notice.class.php,v 1.13 2023/11/15 07:50:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/authperso.class.php");

class authperso_notice {
	public $id=0; // id de la notice
	public $auth_info=array();
	public $onglets_auth_list=array();
	private static $authpersos=array();
	
	public function __construct($id=0) {
		$this->id=intval($id); // id de la notice
		$this->fetch_data();
	}
	
	public function fetch_data() {
		$this->auth_info=array();
		// pour chaque autorités existantes récupérér les autorités affectés à la notice
		$req="select * from authperso, notices_authperso,authperso_authorities where id_authperso=authperso_authority_authperso_num and notice_authperso_authority_num=id_authperso_authority and notice_authperso_notice_num=".$this->id."
		order by notice_authperso_order";
		$res = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($res)){
			while(($r=pmb_mysql_fetch_object($res))) {
				$authperso = $this->get_authperso_class($r->id_authperso);			
				$view = $authperso->get_view($r->notice_authperso_authority_num);
				$info_fields = $authperso->get_info_fields($r->notice_authperso_authority_num);
				$isbd = authperso::get_isbd($r->notice_authperso_authority_num);
				
				$this->onglets_auth_list[$r->authperso_notice_onglet_num][$r->id_authperso][$r->notice_authperso_authority_num]['id']=$r->notice_authperso_authority_num;
				$this->onglets_auth_list[$r->authperso_notice_onglet_num][$r->id_authperso][$r->notice_authperso_authority_num]['isbd']=$isbd;
				$this->onglets_auth_list[$r->authperso_notice_onglet_num][$r->id_authperso][$r->notice_authperso_authority_num]['authperso_name']=$r->authperso_name;

				$this->auth_info[$r->notice_authperso_authority_num]= array(
				    'onglet_num' => $r->authperso_notice_onglet_num,
				    'authperso_name' => translation::get_translated_text($r->id_authperso, 'authperso', 'authperso_name', $r->authperso_name),
				    'isbd' => $isbd,
				    'info_fields' => $info_fields,
				    'view' => $view,
				    'auth_see' => "<a href='./index.php?lvl=authperso_see&id=".$r->notice_authperso_authority_num."'>$isbd</a>"
				);
			}
		}
	}
	
	public function get_info(){
		return $this->auth_info;
	}
	
	public function get_notice_display(){
		
		$aff="";
		foreach($this->onglets_auth_list as $onglet){
			$authperso_name="";
			foreach($onglet as $auth_perso){
				foreach($auth_perso as $auth){
					if($authperso_name!=$auth['authperso_name']){
						$authperso_name=$auth['authperso_name'];
						$aff.="<br><b>".$authperso_name."</b>&nbsp;: ";
						$new=1;
					}
					if(!$new)	$aff.=", ";
					$aff.=$auth['isbd'];
					$new=0;
				}
			}
		}
		return $aff;
	}
	
	public function get_notice_display_list(){
		$aff_list=array();
		foreach($this->onglets_auth_list as $onglet){
			foreach($onglet as $authperso_num => $auth_perso){
				$aff_list[$authperso_num]['isbd']="";
				$aff_list[$authperso_num]['name']="";
				foreach($auth_perso as $auth){
					$aff_list[$authperso_num]['name']=$auth['authperso_name'];
					if($aff_list[$authperso_num]['isbd'])$aff_list[$authperso_num]['isbd'].=", ";
					$aff_list[$authperso_num]['isbd'].=$auth['isbd'];
				}
			}
		}
		return $aff_list;
	}
	
	private function get_authperso_class($id_type_authperso){
		if(!isset(self::$authpersos[$id_type_authperso])){
			self::$authpersos[$id_type_authperso] = new authperso($id_type_authperso);
		}
		return self::$authpersos[$id_type_authperso];
	}
	
} // authperso_notice class end
	
