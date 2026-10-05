<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_files.class.php,v 1.3 2022/06/28 06:14:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// require_once($include_path."/templates/files/files.tpl.php");

class misc_files {
	
	protected $tree_data;

	/**
	 * Liste des fichiers personnalisables
	 */
	protected $folders_files;
	
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function _recursive_sub_folders($folder, $name, $path, $parent) {
		$children = array();
		$files = array();
		if(is_array($folder)) {
			foreach ($folder as $sub_name=>$sub_folder) {
				if($sub_name == 'files') {
					foreach ($folder['files'] as $file_indice=>$file) {
						if(file_exists($path.'/'.$file['filename'])) {
							$files_children = array();
							$subst_xml_filename = str_replace('.xml', '_subst.xml', $file['filename']);
							if(file_exists($path.'/'.$subst_xml_filename)) {
								$files_children[] = $this->get_tree_element($path.'/'.$subst_xml_filename, 'substFile', '_subst.xml', $path.'/'.$file['filename']);
							}
							$files[] = $this->get_tree_element($path.'/'.$file['filename'], 'file', $file['filename'], $path, $files_children);
						}
					}
				} else {
					$sub_path = $path.'/'.$sub_name;
					$children[] = $this->_recursive_sub_folders($this->get_sub_folders($sub_folder), $sub_name, $sub_path, $path);
				}
			}
		}
		return $this->get_tree_element($path, 'folder', $name, $parent, $children, $files);
	}
	
	protected function fetch_data() {
		$files = array();
		$tree_data = $this->_recursive_sub_folders($this->get_folders_files(), 'pmb', '.', '-1');
		$this->tree_data = array($tree_data);
	}
	
	protected function get_sub_folders($folder) {
		$sub_folders = array();
		if(is_array($folder)) {
			foreach ($folder as $name=>$sub_folder) {
				$sub_folders[$name] = $sub_folder;
			}
		}
		return $sub_folders;
	}
	
	protected function get_file($type, $filename) {
		return array('type' => $type, 'filename' => $filename);
	}
	
// 	protected function get_lang() {
// 		return array('ar','br_BR','ca_ES','de_DE','en_UK','en_US','es_ES','fr_FR','hu_HU','it_IT','ja_JP','la_LA','nl_NL','oc_FR','pt_BR','pt_PT','ro_RO','tr_TR');
// 	}
// 	protected function get_opac_lang() {
// 		return array('ar','br_BR','ca_ES','de_DE','en_UK','en_US','es_ES','fr_FR','hu_HU','it_IT','ja_JP','la_LA','nl_NL','oc_FR','pt_BR','pt_PT','ro_RO','tr_TR');
// 	}
	
// 	protected function get_marc_tables_lang_files($opac=0) {
// 		$marc_tables_lang_files = array();
// 		if($opac) {
// 			$lang = $this->get_opac_lang();
// 			$marc_tables_files = array(
// 					'codstat_995', 'country', 'doctype', 'function',
// 					'lang', 'music_form', 'music_key', 'nivbiblio',
// 					'oeuvre_link', 'oeuvre_nature', 'oeuvre_type', 'recordtype', 'recordtype',
// 					'relationtype_aut', 'relationtype_autup', 'relationtypedown', 'relationtypedown_unimarc',
// 					'relationtypeup', 'relationtypeup_unimarc',
// 					'rent_account_type', 'rent_destination', 'rent_request_type',
// 					'section_995', 'typdoc_995'
// 			);
// 		} else {
// 			$lang = $this->get_lang();
// 			$marc_tables_files = array(
// 					'codstat_995', 'country', 'doctype', 'function',
// 					'lang', 'music_form', 'music_key', 'nivbiblio',
// 					'oeuvre_link', 'oeuvre_nature', 'oeuvre_type', 'recordtype', 'recordtype',
// 					'relationtype_aut', 'relationtype_autup', 'relationtypedown', 'relationtypedown_unimarc',
// 					'relationtypeup', 'relationtypeup_unimarc'
// 			);
// 		}
// 		foreach ($lang as $lg) {
// 			foreach ($marc_tables_files as $marc_tables_file) {
// 				$marc_tables_lang_files[$lg]['files'][] = $this->get_file('marc_tables', $marc_tables_file);
// 			}
// 		}
// 		$marc_tables_lang_files['files'][] = $this->get_file('diacritique', $marc_tables_file);
// 		$marc_tables_lang_files['files'][] = $this->get_file('icondoc', $marc_tables_file);
// 		$marc_tables_lang_files['files'][] = $this->get_file('icondoc_big', $marc_tables_file);
// 		return $marc_tables_lang_files;
// 	}
	
	protected function get_files_parsed_folder($folder_path, $type='mixed', $recursive=1){
		$folder_files = array();
		if(file_exists($folder_path)){
			$dh = opendir($folder_path);
			while(($file = readdir($dh)) !== false){
				if($file != "." && $file != ".." && $file != "CVS"){
					if(is_dir($folder_path.'/'.$file) && $recursive){
						$folder_files[$file] = $this->get_files_parsed_folder($folder_path.'/'.$file, $type, 1);
						if(!count($folder_files[$file])) {
							unset($folder_files[$file]);
						}
						ksort($folder_files);
					} elseif(!strpos($file, '_subst.xml') && strpos($file, '.xml') && $file != 'manifest.xml') {
						switch ($type) {
							case 'mixed':
								if(strpos($folder_path, '/messages/')) {
									$folder_files['files'][] = $this->get_file('messages', $file);
								} elseif($file == 'catalog.xml') {
									$folder_files['files'][] = $this->get_file('catalog', $file);
								}
								break;
							default:
								$folder_files['files'][] = $this->get_file($type, $file);
								break;
						}
					}
				}
			}
		}
		return $folder_files;
	}
	
	public function get_folders_files() {
		global $base_path;
		global $class_path;
		global $include_path;
	
		if(!isset($this->folders_files)) {
			$this->folders_files = array(
				'admin' => array(
					'connecteurs' => $this->get_files_parsed_folder($base_path.'/admin/connecteurs'),
					'planificateur' => $this->get_files_parsed_folder($base_path.'/admin/planificateur'),
						
				),
// 				'classes' => array(
// 						'frbr' => $this->get_files_parsed_folder($base_path.'/classes/frbr', 'messages'),
// 				),
// 				'cms' => $this->get_files_parsed_folder($base_path.'/cms', 'messages'),
				'includes' => array(
					'indexation' => $this->get_files_parsed_folder($base_path.'/includes/indexation', 'indexation'),
					'marc_tables' => $this->get_files_parsed_folder($base_path.'/includes/marc_tables', 'list'),
// 					'messages' => $this->get_files_parsed_folder($base_path.'/includes/messages', 'messages'),
					'search_queries' => $this->get_files_parsed_folder($base_path.'/includes/search_queries', 'search_fields'),
					'sort' => $this->get_files_parsed_folder($base_path.'/includes/sort', 'sort'),
				),
				'opac_css' => array(
// 					'cms' => $this->get_files_parsed_folder($base_path.'/opac_css/cms', 'messages'),
// 					'classes' => array(
// 							'frbr' => $this->get_files_parsed_folder($base_path.'/opac_css/classes/frbr', 'messages')
// 					),
					'includes' => array(
						'indexation' => $this->get_files_parsed_folder($base_path.'/opac_css/includes/indexation', 'indexation'),
						'marc_tables' => $this->get_files_parsed_folder($base_path.'/opac_css/includes/marc_tables', 'list'),
// 						'messages' => $this->get_files_parsed_folder($base_path.'/opac_css/includes/messages', 'messages'),
						'search_queries' => $this->get_files_parsed_folder($base_path.'/opac_css/includes/search_queries', 'search_fields'),
						'sort' => $this->get_files_parsed_folder($base_path.'/opac_css/includes/sort', 'sort'),
					)
				)
			);
		}
		return $this->folders_files;
	}
	
	public function get_tree_element($id, $type, $title, $num_parent, $children=array(), $files=array()) {
		return array(
			'id' => $id,
			'type' => $type,
			'title' => $title,
			'num_parent' => $num_parent,
			'children' => $children,
			'files' => $files
		);
	}
	
	public function get_file_type($path, $filename) {
		//S'il s'agit du subst..
		$filename = str_replace('_subst.xml', '.xml', $filename);
		
		$this->get_folders_files();
		
		if(substr($path, 0, 2) == './') {
			$path = substr($path, 2);
		}
// 		$filename = str_replace(array('.xml','_subst.xml'), '', $filename);
		$exploded_path = explode('/', $path);
		$temp_folders_files = $this->folders_files;
		do {
			$temp_folders_files = $temp_folders_files[$exploded_path[0]];
			array_shift($exploded_path);
		} while(count($exploded_path) > 0);
		foreach ($temp_folders_files['files'] as $file) {
			if($file['filename'] == $filename) {
				return $file['type']; 
			}
		}
	}
	
	public function get_tree_data() {
		return $this->tree_data;
	}
	
	public static function proceed($path='', $filename='') {
		global $action;
	
		switch($action) {
			case 'edit':
				$model_instance = static::get_model_instance($path, $filename);
				print $model_instance->get_form();
				break;
			case 'save':
				$model_instance = static::get_model_instance($path, $filename);
				$model_instance->set_properties_from_form();
				$model_instance->save();
				break;
			case 'delete':
				$model_class_name = static::model_class_name;
				$model_class_name::delete($path, $filename);
				break;
			default:
				break;
		}
	}
	
	public static function get_model_instance($path='', $filename='') {
		global $class_path;
		
		$misc_files = new misc_files();
		$file_type = $misc_files->get_file_type($path, $filename);
		if($file_type) {
			$class_name = 'misc_file_'.$file_type;
			
		} else {
			$class_name = 'misc_file';
		}
		require_once($class_path."/misc/files/".$class_name.".class.php");
		return new $class_name($path, $filename);
	}
	
} // misc_files class end
	
