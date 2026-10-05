<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dashboard_module_dashboard.class.php,v 1.7 2021/04/23 11:48:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/dashboard/dashboard_module.class.php");

class dashboard_module_dashboard extends dashboard_module {

	public function __construct(){
		global $msg;
		$this->template = "default";
		$this->module = "dashboard";
		$this->module_name = $msg['dashboard'];
		parent::__construct();
	}
	

	public function get_quick_params_form(){
		global $msg;
		$html= "
		<form name='quick_params_dashboard' action='' method='post' onsubmit='return false;'>
			<div class='form-contenu'>";
		$html.= $this->get_dashboard_actions_form();
		$html.= $this->get_circ_actions_form();
		$html.= $this->get_catalog_actions_form();
		$html.= $this->get_autorite_actions_form();
		$html.= $this->get_edit_actions_form();
		$html.= $this->get_dsi_actions_form();
		$html.= $this->get_cms_actions_form();
		$html.= $this->get_admin_actions_form();
		$html.="
			</div>
				<div class='row'>
					<div class='left'>
						<input type='submit' class='bouton' value='".$msg[77]."' onclick='save_dashboard_params()'/>
						<span id='quick_params_dashboard_infos'></span>
					</div>
				</div>
				<div class='row'></div>
		</form>
			<script type='text/javascript'>
				function save_dashboard_params(){
					var parameters = '';
					var deflt2docs_location = document.forms['quick_params_dashboard'].form_deflt2docs_location;
					if(deflt2docs_location){
						for (i=0 ; i<deflt2docs_location.options.length ; i++){
							if(deflt2docs_location.options[i].selected == true){
	 							parameters = 'deflt2docs_location='+deflt2docs_location.options[i].value;
								break;
							}
						}
					}
								
					var param_allloc = document.forms['quick_params_dashboard'].form_param_allloc;
					if(param_allloc){
						if(param_allloc.checked){
							parameters+='&param_allloc=1';	
						}else{
							parameters+='&param_allloc=0';			
						}
					}
					var deflt_cashdesk = document.forms['quick_params_dashboard'].form_deflt_cashdesk;
					if(deflt_cashdesk){
						for (i=0 ; i<deflt_cashdesk.options.length ; i++){
							if(deflt_cashdesk.options[i].selected == true){
		 						parameters+= '&deflt_cashdesk='+deflt_cashdesk.options[i].value;
								break;
							}
						}	
					}
					var req= new http_request();
					req.request('./ajax.php?module=dashboard&categ=dashboard&sub=save_quick_params',1,parameters,1,dashboard_params_saved);
				}
				function dashboard_params_saved(text){
					if(text == 1){
						document.getElementById('quick_params_dashboard_infos').innerHTML='<h2>".addslashes($msg['dashboard_saved_quick_params'])."</h2>';
					}else{
						document.getElementById('quick_params_dashboard_infos').innerHTML='<h2>".addslashes($msg['ajax_saved_failed'])."</h2>';	
					}
					setTimeout(function(){document.getElementById('quick_params_dashboard_infos').innerHTML=''},3000);
				}
			</script>
		";
		return $html;
	}
	
	public function save_quick_params(){
		$query = "update users set ";
		$update=array();
		foreach($_POST as $key => $value){
			switch($key){
				case "deflt2docs_location": 
				case "param_allloc": 
				case "deflt_cashdesk":
					global ${$key};
					$update[] = $key."='".$value."'";
					break;
			}
		}
		if(count($update)){
			$query.=implode(", ",$update)." where userid=".SESSuserid;
			$result = pmb_mysql_query($query);
			return $result;
		}
		return true;
	}

	private function get_dashboard_actions_form(){
		$html= "";
		return $html;
	}
		
	private function get_circ_actions_form(){
		if(SESSrights & PREF_AUTH) {
			$html= "<div class='circ'>";
			$html.= $this->get_user_param_form("deflt2docs_location");
			$html.= $this->get_user_param_form("deflt_cashdesk");
			$html.="<div class='row'></div>			
				</div>";
		} else $html="";
		return $html;
	}	
	
	private  function get_catalog_actions_form(){
// 		$html= "
// 		<div class='catalog'>";
// 		$html.= dashboard_module::get_user_param_form("deflt_docs_location");
// 		$html.= dashboard_module::get_user_param_form("value_deflt_lang");
// 		$html.="
// 		</div>";
		$html= "";
		return $html;
	}
	
	private function get_autorite_actions_form(){
		$html= "";
		return $html;
	}
	
	private function get_edit_actions_form(){
		$html= "";
		return $html;
	}
	
	private function get_dsi_actions_form(){
		$html= "";
		return $html;
	}
	
	private function get_cms_actions_form(){
		$html= "";
		return $html;
	}
	
	private function get_admin_actions_form(){
		$html= "";
		return $html;
	}
}