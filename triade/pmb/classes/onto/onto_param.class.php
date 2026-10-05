<?php

/**
 * @author abacarisse
 *
 */
class onto_param extends stdClass{

	private $tab_param=array();
	
	protected static $instance;
	
	public function __construct($tab_param = array()) {
		if (empty($tab_param)) {
			$this->tab_param = array('categ' => '', 'sub' => '', 'action' => '', 'page' => '1', 'nb_per_page' => '20');
		} else {
			$this->tab_param = $tab_param;
		}
		$this->assign_globals();
		static::$instance = $this;
	}
	
	private function assign_globals() {
		foreach ($this->tab_param as $param_name => $param_default) {
			global ${$param_name};
			$this->{$param_name} = ${$param_name};
			//ajout d'un cas particulier permettant de remettre la globale page à 1
			//Cette dernière est mise à 0 dans le fichier select.php, 
			//ce qui entraine de nombreux effets de bords dans les selecteurs concept & onto
			if (empty($this->{$param_name})) {
				$this->{$param_name} = $param_default;
			}
		}
	}
	
	public static function get_params() {
		return static::$instance;
	}
}