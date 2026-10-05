<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: plugins.class.php,v 1.5.8.1 2024/06/12 07:01:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], '.class.php')) die('no access');

global $class_path, $include_path;
require_once $include_path.'/parser.inc.php';
require_once($class_path.'/plugin.class.php');

/**
 * Classe de gestion du sysètme de plugins
 * @author arenou
 *
 */
class plugins {
	private static $_instance = null;
	private static $messages= array();
	protected $plugins = array();
	private static $plugins_instances = array();
	
	/**
	 * Constructeur...
	 */
	private function __construct(){
		$this->parse();
	}
	
	public static function get_instance(){
		if(is_null(self::$_instance)) {
			self::$_instance = new plugins();
		}
		return self::$_instance;
	}
	
	public static function get_plugin_instance($plugin_name){
		if(!isset(self::$plugins_instances[$plugin_name])){
			self::$plugins_instances[$plugin_name] = new plugin($plugin_name);
		}
		return self::$plugins_instances[$plugin_name];
	}
	
	/**
	 * Méthode de parcours du répertoire de plugins...
	 * Elle déclenche l'analyse de chaque plugin présent
	 */
	private function parse(){
		global $base_path;
		if(file_exists($base_path.'/plugins')){
			$dh = opendir($base_path.'/plugins');
			while(($plugin = readdir($dh)) !== false){
				if($plugin != "." && $plugin != ".." && $plugin != "CVS"){
					if(is_dir($base_path.'/plugins/'.$plugin) && $this->is_activated($plugin)){
						$this->analyze($base_path.'/plugins/'.$plugin);
					}
				}
			}
		}
	}
	
	/**
	 * Détermine si un plugin est activé ou non. Toujours vrai pour le moment, cela permet d'envisager une évolution plus tard!
	 * @param string $plugin_path
	 * @return boolean
	 */
	private function is_activated($plugin){
		return true;
	}
	
	/**
	 * Méthode d'analyse d'un plugin
	 * @param string $plugin_path
	 */
	private function analyze($plugin_path){
		if(file_exists($plugin_path.'/manifest.xml')){
			$parse = _parser_text_no_function_(file_get_contents($plugin_path.'/manifest.xml'));
			$manifest = $parse['MANIFEST']['0'];
			$this->plugins[basename($plugin_path)] =array();
			$this->plugins[basename($plugin_path)]['name'] = $manifest['NAME'][0]['value'];
			if($manifest['AUTHOR']){
				$this->plugins[basename($plugin_path)]['author'] = array();
				if($manifest['AUTHOR'][0]['NAME']){
					$this->plugins[basename($plugin_path)]['author']['name'] = $manifest['AUTHOR'][0]['NAME'][0]['value'];
				}
				if($manifest['AUTHOR'][0]['ORGANISATION']){
					$this->plugins[basename($plugin_path)]['author']['organisation'] = $manifest['AUTHOR'][0]['ORGANISATION'][0]['value'];
				}
			}
			if($manifest['CREATED_DATE']){
				$this->plugins[basename($plugin_path)]['created_date'] = $manifest['CREATED_DATE'][0]['value'];
			}
			if($manifest['VERSION']){
				$this->plugins[basename($plugin_path)]['version'] = $manifest['VERSION'][0]['value'];
			}
			if(isset($manifest['MENUS'][0]['MENU']) && is_array($manifest['MENUS'][0]['MENU'])){
				$this->plugins[basename($plugin_path)]['menus'] = array();
				for($i=0 ; $i<count($manifest['MENUS'][0]['MENU']) ; $i++){
					$menu = $manifest['MENUS'][0]['MENU'][$i];
					$this->plugins[basename($plugin_path)]['menus'][$menu['MODULE']] = array();
					if(isset($menu['TABS']) && is_array($menu['TABS'])){
						for ($j=0 ; $j<count($menu['TABS'][0]['TAB']) ; $j++){	
							$this->plugins[basename($plugin_path)]['menus'][$menu['MODULE']][$menu['TABS'][0]['TAB'][$j]['ID']] = array(
								'name' => $menu['TABS'][0]['TAB'][$j]['value'],
								'items' => array()
							);
						}
						if(is_array($menu['ITEMS']) && is_array($menu['ITEMS'][0]['ITEM'])){
							$items = $menu['ITEMS'][0]['ITEM'];
							foreach ($items as $xml_item) {
								$item = array();
								$item['sub'] = $xml_item['SUB'];
								$item['name'] = $xml_item['NAME'];
								if(!empty($xml_item['HMENU']) && is_array($xml_item['HMENU']) && is_array($xml_item['HMENU'][0]['ITEM'])){
									$item['hmenu'] = array();
									for($k=0 ; $k<count($xml_item['HMENU'][0]['ITEM']) ; $k++){
										$args = '';
										foreach($xml_item['HMENU'][0]['ITEM'][$k] as $key => $value){
											if($key != 'value'){
												if($args){
													$args.= "&";
												}
												$args.=$key.'='.$value;
											}
										}
										$item['hmenu'][strtolower($args)]= $xml_item['HMENU'][0]['ITEM'][$k]['value'];
									}
								}
								$this->plugins[basename($plugin_path)]['menus'][$menu['MODULE']][$xml_item['TAB']]['items'][] = $item;
							}
						}
					}
				}	
			}
		}
	}
	
	/**
	 * Méthode qui construit le menu pour un module de PMB
	 * @param string $module
	 */
	public function get_menu($module){
		global $charset;
		$html = '';
		foreach($this->plugins as $plugin_name => $plugin_infos){
			if(isset($plugin_infos['menus']) && is_array($plugin_infos['menus']) && isset($plugin_infos['menus'][$module]) && is_array($plugin_infos['menus'][$module])){
				foreach($plugin_infos['menus'][$module] as $tab){
					$html.= '
					<h3 onclick="menuHide(this,event)">'.htmlentities(self::check_for_msg($plugin_name, $tab['name']),ENT_QUOTES,$charset).'</h3>
					<ul>';
					foreach($tab['items'] as $item){
						$html.= '		
						<li><a href="'.$module.'.php?categ=plugin&plugin='.$plugin_name.'&sub='.$item['sub'].'">'.htmlentities(self::check_for_msg($plugin_name, $item['name']),ENT_QUOTES,$charset).'</a></li>';
					}
					$html.= '
					</ul>';
				}
			}
		}
		return $html;
	}
	
	private function get_context_menu($module,$plugin,$sub){
		global $charset;
		$html = '';
		if(is_array($this->plugins[$plugin]) && $this->is_activated($plugin) && isset($this->plugins[$plugin]['menus'][$module])){
			foreach($this->plugins[$plugin]['menus'][$module] as $menu){
				for($i=0 ; $i<count($menu['items']) ; $i++){
					if($menu['items'][$i]['sub'] == $sub && isset($menu['items'][$i]['hmenu'])){
						$html = '
						<div class="hmenu">';
						foreach($menu['items'][$i]['hmenu'] as $query => $label){
							$html.= '
							<span'.ongletSelect('categ=plugin&plugin='.$plugin.'&sub='.$sub.'&'.$query).'>
								<a title="'.htmlentities(self::check_for_msg($plugin, $label),ENT_QUOTES,$charset).'" href="'.$module.'.php?categ=plugin&plugin='.$plugin.'&sub='.$sub.'&'.$query.'">'.htmlentities(self::check_for_msg($plugin, $label),ENT_QUOTES,$charset).'</a>
							</span>';
						}
						$html.='
						</div>';
						return $html;
					}
				}
			}
		}
		return '';
	}
	
	public static function check_for_msg($plugin, $code){
		if(strpos($code,'msg:') !== false){
			return self::get_message($plugin, str_replace('msg:','',$code));
		}
		return $code;
	}

	public function proceed($module, $plugin, $sub, $layout = "!!menu_contextuel!!"){
		global $base_path;
		
		$module = plugins::clean_string($module);
		$plugin = plugins::clean_string($plugin);
		
		if(file_exists($base_path . '/plugins/'.$plugin.'/'.$module.'/main.inc.php')){
			return $base_path . '/plugins/'.$plugin.'/'.$module.'/main.inc.php';
		}
		return false;
	}
	
	public function proceed_ajax($module, $plugin, $sub){
		global $base_path;
		
		$module = plugins::clean_string($module);
		$plugin = plugins::clean_string($plugin);
		
		if(file_exists($base_path.'/plugins/'.$plugin.'/'.$module.'/ajax_main.inc.php')){
			return $base_path.'/plugins/'.$plugin.'/'.$module.'/ajax_main.inc.php';
		}
		return false;
	}
	
	public static function clean_string($string){
		if($string){
			return str_replace(' ', '', pmb_alphabetic('^a-z0-9_\-\s', '',pmb_strtolower($string)));
		}
	}
	
	public static function get_message($plugin,$code){
		global $base_path,$msg, $lang;
		if(!isset(self::$messages[$plugin])){
			if(file_exists($base_path.'/plugins/'.$plugin.'/includes/messages/'.$lang.'.xml')){
				$xml = new XMLlist($base_path.'/plugins/'.$plugin.'/includes/messages/'.$lang.'.xml');
				$xml->analyser();
				self::$messages[$plugin] = $xml->table;
			}
		}
		if(isset(self::$messages[$plugin][$code])){
			return self::$messages[$plugin][$code];
		}
		if(isset($msg[$code])){
			return $msg[$code];
		}
		return $code;
	}
}