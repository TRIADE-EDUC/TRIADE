<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_toolkits.class.php,v 1.4 2017/05/23 15:00:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/cms/cms_toolkit.class.php");
require_once($class_path."/encoding_normalize.class.php");

class cms_toolkits{
	
	public function __construct(){
	}
	
	public static function get_form() {
		global $msg, $base_path;
		global $pmb_url_base;
		
		$build_url=$pmb_url_base;
		
		$form = "<form id='cms_toolkits_form' name='cms_toolkits_form' method='post' action=''>";
		$toolkits = self::get_toolkits();
		foreach ($toolkits as $toolkit) {
			$cms_toolkit = new cms_toolkit($toolkit);
			$form .= $cms_toolkit->get_form();
		}
		$form .= "
			<div class='row' id='cms_toolkits_save'>
				<button data-dojo-type='dijit/form/Button'>
				".$msg['cms_toolkits_save']."
					<script type='dojo/on' data-dojo-event='click'>
						require(['dojo/request/xhr', 'dojo/dom-form', 'dojo/topic'], function(xhr, domForm, topic){
							xhr.post('".$build_url."ajax.php?module=cms&categ=toolkits&action=save',
							 	{
									handleAs: 'json',
									data: domForm.toObject('cms_toolkits_form')
								}
							).then(function(response){
								if(response) {
									for(var toolkit_name in response) {
										if(document.getElementById('cms_toolkit_'+toolkit_name+'_title')) {
											document.getElementById('cms_toolkit_'+toolkit_name+'_title').innerHTML = response[toolkit_name];
										}
									}
									topic.publish('dGrowl', '".addslashes($msg['cms_toolkits_save_done'])."');
								}
							});
						});
					</script>
				</button>
			</div>
		</form>";
		return $form;
	}
	
	public static function get_json_title() {
		global $base_path;
	
		$title = array();
		$toolkits = self::get_toolkits();
		foreach ($toolkits as $toolkit) {
			$cms_toolkit = new cms_toolkit($toolkit);
			$title[$toolkit] = $cms_toolkit->get_title();
		}
		return encoding_normalize::json_encode($title);
	}
	
	public static function load() {
		$headers = array();
		$query = "select cms_toolkit_name from cms_toolkits where cms_toolkit_active = 1 order by cms_toolkit_order";
		$result = pmb_mysql_query($query);
		if($result) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$cms_toolkit = new cms_toolkit($row->cms_toolkit_name);
				$headers = array_merge($headers, $cms_toolkit->load());
			}
		}
		return $headers;
	}
	
	public static function is_active($name) {
		global $base_path;

		$toolkits = self::get_toolkits();
		foreach ($toolkits as $toolkit) {
			if($toolkit == $name) {
				$cms_toolkit = new cms_toolkit($toolkit);
				return $cms_toolkit->get_active();
			}
		}
		return false;
	}
	
	public static function get_toolkits() {
		global $base_path;
	
		$toolkits = array();
		if(file_exists($base_path.'/opac_css/styles/common/toolkits')) {
			$dh = opendir($base_path.'/opac_css/styles/common/toolkits');
			while(($toolkit = readdir($dh)) !== false){
				if($toolkit != "." && $toolkit != ".." && $toolkit != "CVS"){
					$toolkits[] = $toolkit;
				}
			}
		}
		if(in_array('jquery', $toolkits)) {
			array_splice($toolkits, array_search('jquery', $toolkits), 1);
			array_unshift($toolkits,'jquery');
		}
		return $toolkits;
	}
}