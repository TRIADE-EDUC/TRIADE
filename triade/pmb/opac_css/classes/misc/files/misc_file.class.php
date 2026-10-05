<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_file.class.php,v 1.10 2023/08/28 14:01:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/encoding_normalize.class.php");
// require_once($include_path."/templates/misc/files/misc_file.tpl.php");

class misc_file {
	
	protected $id;
	
	/**
	 * Répertoire d'accès au fichier
	 * @var string $path
	 */
	protected $path;
	
	/**
	 * Nom du fichier
	 * @var string $filename
	 */
	protected $filename;
	
	protected $data;
	
	protected $error;
	
	public function __construct($path, $filename) {
		$this->path = $path;
		$this->filename = $filename;
		if(!is_dir($this->path)){
			return;
		}
		$this->fetch_data();
	}

	protected function fetch_data() {
		$this->id = 0;
		$this->data = array();
		$query = "select * from subst_files where subst_file_path = '".addslashes($this->get_opac_path())."' and subst_file_filename = '".addslashes($this->filename)."'";
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_assoc($result);
			pmb_mysql_free_result($result);
			$this->id = $row['id_subst_file'];
			$this->data = encoding_normalize::json_decode($row['subst_file_data'], true);
		}
	}
	
	protected function analyze() {
	
	}
	
	protected function get_visible_checkbox($code) {
		return "<input type='checkbox' name='subst_file_data[".$code."][visible]' id='subst_file_data_".$code."_visible' ".(!isset($this->data[$code]['visible']) || $this->data[$code]['visible'] ? "checked='checked'" : "")." />";
	}
	
	protected function get_substituted_icon($code) {
		global $msg;
		
		return "<img data-file-code='".$code."' data-file-action='add_substitution' ".(!empty($this->type) ? "data-file-type='".$this->type."'" : "")." src='".get_url_icon('b_edit.png')."' alt='".$msg['substituate']."' title='".$msg['substituate']."' style='cursor:pointer;'/>";
	}
	
	public function get_form_title() {
		return str_replace('.', 'pmb', $this->path)."/".$this->filename;
	}
	
	public function get_form() {
		global $msg;
		
		$this->analyze();
		$form = "
		<form name='misc_file_form' id='misc_file_form'>
			<h3>".$this->get_form_title()."</h3>
			<div class='form-contenu'>";
		$form .= $this->get_display_list();
		$form .= "
			</div>
			<div class='row'>
				<div class='left'>
					<input type='button' class='bouton' id='misc_file_cancel' name='misc_file_cancel' value='".$msg['76']."' />
					<input type='button' class='bouton' id='misc_file_save' name='misc_file_save' value='".$msg['77']."' />
					".(count($this->data) ? "<input type='button' class='bouton' id='misc_file_initialization' name='misc_file_initialization' value='".$msg['list_ui_initialization']."' />" : "")."
				</div>
			</div>
		</form>";
		return $form;
	}
	
	protected function get_informations_hidden($code, $group='') {
		global $charset;
	
		$informations_hidden = "<input type='hidden' name='subst_file_data[".$code."][code]' id='subst_file_data_".$code."_code' value='".$code."' />";
		if($group) {
			$informations_hidden .= "<input type='hidden' name='subst_file_data[".$code."][group]' id='subst_file_data_".$code."_group' value='".htmlentities($group, ENT_QUOTES, $charset)."' />";	
		}
		return $informations_hidden;
	}
	
	public function set_properties_from_form() {
		global $subst_file_data;
	
		if(is_array($subst_file_data) && count($subst_file_data)) {
			$this->data = array();
			foreach ($subst_file_data as $code=>$element) {
				$this->data[$code] = array(
						'visible' => (isset($element['visible']) && $element['visible'] ? 1 : 0),
						'group' => (isset($element['group']) ? stripslashes($element['group']) : ''),
				);
			}
		}
	}
	
	public function get_contents() {
		if(file_exists($this->path.'/'.$this->filename)) {
			$contents = file_get_contents($this->path.'/'.$this->filename);
			return encoding_normalize::utf8_normalize($contents);
		} else {
			return encoding_normalize::utf8_normalize($this->get_default_template());
		}
		return '';
	}
	
	public function save_contents() {
		global $contents;
		
		if(strpos($this->filename, '_subst.xml') && file_exists($this->path.'/'.str_replace('_subst.xml', '.xml', $this->filename))) {
		    file_put_contents($this->path.'/'.$this->filename, trim(encoding_normalize::utf8_decode(stripslashes($contents))));
			return true;
		}
		return false;
	}
	
	public function save() {
		$query = "select count(*) from subst_files where subst_file_path = '".addslashes($this->path)."' and subst_file_filename = '".addslashes($this->filename)."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_result($result, 0, 0)) {
			$query = "update subst_files set subst_file_data = '".addslashes(encoding_normalize::json_encode($this->data))."' where subst_file_path = '".addslashes($this->path)."' and subst_file_filename = '".addslashes($this->filename)."'";
			pmb_mysql_query($query);
		} else {
			$query = "insert into subst_files set subst_file_path = '".addslashes($this->path)."', subst_file_filename = '".addslashes($this->filename)."', subst_file_data = '".addslashes(encoding_normalize::json_encode($this->data))."'";
			pmb_mysql_query($query);
		}
		$this->unlink_temporary_file();
		return true;
	}
	
	public function delete() {
		if(strpos($this->filename, '_subst.xml') && file_exists($this->path.'/'.$this->filename)) {
			$response = unlink($this->path.'/'.$this->filename);
			if(!$response) {
				$this->error = "Delete failure";
				return false;
			}
			return true;
		}
		return false;
	}
	
	protected function unlink_temporary_file() {
		global $charset;
		global $base_path;
		
		$fileInfo = pathinfo($this->path.'/'.$this->filename);
		$fileName = preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset);
		if(file_exists($base_path."/temp/XMLWithSubst".$fileName.".tmp")) {
			unlink($base_path."/temp/XMLWithSubst".$fileName.".tmp");
		}
		if(file_exists($base_path."/temp/XML".$fileName.".tmp")) {
			unlink($base_path."/temp/XML".$fileName.".tmp");
		}
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_path() {
		return $this->path;
	}
	
	public function get_filename() {
		return $this->filename;
	}
	
	public function get_data() {
		return $this->data;
	}
	
	public function set_data($data=array()) {
		$this->data = $data;
	}
	
	public function get_json_data() {
		return encoding_normalize::json_encode($this->data);
	}
	
	public function get_error() {
		return $this->error;
	}
	
	protected function has_subst_file() {
		if(file_exists($this->get_substitution_path())) {
			return true;
		}
		return false;
	}
	
	protected function get_substitution_path() {
		return $this->path.'/'.str_replace('.xml', '_subst.xml' , $this->filename);	
	}
	
	protected function get_opac_path() {
		if(strpos($this->path, 'opac_css') === false) {
			return str_replace('./', './opac_css/', $this->path);
		} else {
			return $this->path;
		}
	}
	
	protected function get_sign_template() {
		return '© 2002-'.date('Y').' PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)';
	}
	
	protected function get_versionning_template() {
		global $PMBusername;
		
		return '$PMBId: '.$this->filename.',v 1.1 '.date('Y-m-d H:i:s').' '.$PMBusername.' PMBExp';
	}
	
	public function get_default_template() {
		return '';
	}
}
	
