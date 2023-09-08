<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_composee.class.php,v 1.16 2019-05-09 10:35:37 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/authperso.class.php");
require_once($class_path."/vedette/vedette_link.class.php");

class vedette_composee {
	
	/*** Attributes: ***/	
	
	/**
	 * Le nom du fichier de configuration xml
	 * @var string
	 */
	private $config_filename;
	
	/**
	 * Tableau des éléments de la vedette : le premier niveau est la liste des
	 * subdivisions, l'ordre du tableau dans chaque subdivision détermine l'ordre des
	 * éléments
	 * @var vedette_element
	 * @access private
	 */
	protected $vedette_elements = array();
	
	/**
	 * Identifiant de la vedette composée
	 * @access private
	 */
	protected $id;
	
	/**
	 * Label de la vedette composée
	 * @var string
	 */
	protected $label;
	
	/**
	 * Singleton des configs lues pour ne pas reparser à chaque fois les XML
	 * @var array ($config_filename => array('available_fields', 'subdivisions', 'separator'))
	 */
	static private $configs = array();
	
	static private $grammars;
	
	public function __construct($id = 0, $config_filename = "rameau"){
		$this->id=$id+0;
		if($this->id){
			$this->read();
		} else {
			$this->config_filename = $config_filename;
			$this->set_config();
		}
		$this->build_authpersos();
	}
	
	/**
	 * Renvoie la liste des subdivisions
	 *
	 * @return Array()
	 * @access public
	 */
	public function get_subdivisions(){
		return self::$configs[$this->config_filename]['subdivisions'];
	}
	
	/**
	 * Renvoie la liste des champs disponibles à l'ajout dans une vedette composée
	 * 
	 * @return Array()
	 * @access public
	 */
	public function get_available_fields(){
		return self::$configs[$this->config_filename]['available_fields'];
	}
	
	/**
	 * Renvoie les informations sur le champ visé
	 * en fonction du num
	 *
	 * @return Array()
	 * @access public
	 */
	public function get_at_available_field_num($num){
		foreach(self::$configs[$this->config_filename]['available_fields'] as $key=>$field){
			if($field['num']==$num){
				return $field;
			}
		}
	}
	
	/**
	 * Renvoie les informations sur le champ visé
	 * en fonction du label
	 *
	 * @return Array()
	 * @access public
	 */
	public function get_at_available_field_class_name($class_name){
		foreach(self::$configs[$this->config_filename]['available_fields'] as $key=>$field){
			if($field['class_name']==$class_name){
				return $field;
			}
		}
	}
	
	/**
	 * Ajoute un élément dans le tableau de gestion des éléments
	 *
	 * @param vedette_element vedette_element élément à ajouter
	 * @param string subdivision Subdivision à laquelle ajouter l'ordre
	 * @param int position Position dans la subdivision
	 * 
	 * @return void
	 * @access public
	 */
	public function add_element($vedette_element,$subdivision,$position){
		if(!$vedette_element || !$subdivision){
			return false;
		}
		
		$this->vedette_elements[$subdivision][$position]=$vedette_element;
		return true;
	}
	
	/**
	 * Retourne les éléments présents dans une subdivision
	 *
	 * @param string $subdivision
	 * @return array [position]vedette_element
	 */
	public function get_at_elements_subdivision($subdivision){
		return $this->vedette_elements[$subdivision];
	}
	
	/**
	 * Renvoie les informations d'une subdivision en fonction de son code
	 * @param string $code Code de la subdivision
	 * @return array Informations de la subdivision
	 */
	public function get_at_subdivision_code($code) {
		foreach (self::$configs[$this->config_filename]['subdivisions'] as $subdivision) {
			if ($subdivision["code"] == $code) {
				return $subdivision;
			}
		}
		return array();
	}
	
	/**
	 * Retourne les éléments
	 * 
	 * @return array [subdivision][position]vedette_element
	 */
	public function get_elements(){
		return $this->vedette_elements;
	}
	
	/**
	 * Renvoie le nombre d'éléments dans la subdivision
	 *
	 * @return int
	 * @access public
	 */
	public function get_nb_elements_subdivision($subdivision){
		return sizeof($this->vedette_elements[$subdivision]);
	}
	
	/**
	 * remonte les données de la base de données
	 *
	 * @return void
	 * @access public
	 */
	public function read(){
		global $dbh;
		global $base_path;
		global $class_path;
		global $include_path;
		global $javascript_path;
		global $styles_path;
		if(!$this->id){
			return false;
		}
		
		$query = "select label, grammar from vedette where id_vedette = ".$this->id;
		$result = pmb_mysql_query($query, $dbh);
		if ($result && pmb_mysql_num_rows($result)) {
			if ($vedette = pmb_mysql_fetch_object($result)) {
				$this->label = $vedette->label;
				$this->config_filename = $vedette->grammar;
			}
		}
		
		$this->set_config();
		
		$query='select object_type, object_id, subdivision, position from vedette_object where num_vedette = '.$this->id.' order by position';
		$result=pmb_mysql_query($query,$dbh);
		if(!pmb_mysql_error($dbh) && pmb_mysql_num_rows($result)){
			while($element_from_database=pmb_mysql_fetch_object($result)){
				$field=$this->get_at_available_field_num($element_from_database->object_type);
				$vedette_element_class_name=$field['class_name'];
				if($vedette_element_class_name){
					require_once($class_path."/vedette/".$vedette_element_class_name.".class.php");
					if(isset($field['params']) && $field['params']){
						$element=new $vedette_element_class_name($field['params'],$field["num"], $element_from_database->object_id);
					}else{
						$element=new $vedette_element_class_name($field["num"], $element_from_database->object_id);
					}
					$this->add_element($element, $element_from_database->subdivision, $element_from_database->position);
				}
			}
		}
	}
	
	/**
	 * Lecture de la configuration xml
	 */
	private function set_config(){
	 	global $include_path,$base_path,$charset;
		
		if (!isset(self::$configs[$this->config_filename])) {
		 	self::$configs[$this->config_filename] = array();
			
		 	if(file_exists($include_path.'/vedette/'.$this->config_filename.'_subst.xml')){
		 		$xmlFile = $include_path.'/vedette/'.$this->config_filename.'_subst.xml';
		 	}elseif(file_exists($include_path.'/vedette/'.$this->config_filename.'.xml')){
		 		$xmlFile =$include_path.'/vedette/'.$this->config_filename.'.xml';
			}else{
				//pas de fichier à analyser
				return false;
			}
			
			$fileInfo = pathinfo($xmlFile);
			$tempFile = $base_path."/temp/XML".preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset).".tmp";
	
			if (!file_exists($tempFile) || filemtime($xmlFile) > filemtime($tempFile)) {
				//Le fichier XML original a-t-il été modifié ultérieurement ?
					//on va re-générer le pseudo-cache
					if(file_exists($tempFile)){
						unlink($tempFile);
					}
					//Parse le fichier dans un tableau
					$fp=fopen($xmlFile,"r") or die(htmlentities("Can't find XML file $xmlFile", ENT_QUOTES, $charset));
					$xml=fread($fp,filesize($xmlFile));
					fclose($fp);
					$xml_2_analyze=_parser_text_no_function_($xml, 'COMPOSED_HEADINGS');
					
					self::$configs[$this->config_filename]['available_fields'] = array_map("self::clean_read_xml", $xml_2_analyze['AVAILABLE_FIELDS'][0]['FIELD']);
					self::$configs[$this->config_filename]['subdivisions'] = array_map("self::clean_read_xml", $xml_2_analyze['HEADING'][0]['SUBDIVISION']);
					self::$configs[$this->config_filename]['separator'] = $xml_2_analyze['SEPARATOR'][0]['value'];
					
					$tmp = fopen($tempFile, "wb");
					fwrite($tmp,serialize(self::$configs[$this->config_filename]));
					fclose($tmp);
			} else if (file_exists($tempFile)){
				$tmp = fopen($tempFile, "r");
				self::$configs[$this->config_filename] = unserialize(fread($tmp,filesize($tempFile)));
				fclose($tmp);
			}
		}
		return true;
	}
	
	private static function clean_read_xml($array){
		$return=array();
		foreach ($array as $key=>$val){
			if($val){
				$return[strtolower($key)]=$val;
			}
		}
		return $return;
	}
	
	/**
	 * Retourne le label de la vedette composée
	 */
	public function get_label() {
		return $this->label;
	}
	
	/**
	 * Retourne l'identifiant de la vedette composée
	 */
	public function get_id() {
		return $this->id;
	}
	
	/**
	 * Retourne le separateur du label de la vedette composée
	 */
	public function get_separator() {
		return self::$configs[$this->config_filename]['separator'];
	}
	
	/**
	 * Retourne l'identifiant de la vedette liée à un objet
	 * @param int $object_id Identifiant de l'objet
	 * @param int $object_type Type de l'objet
	 * @return int Identifiant de la vedette liée
	 */
	public static function get_vedette_id_from_object($object_id, $object_type) {
		global $dbh;
		
		if ($object_id) {
			$query = "select num_vedette from vedette_link where num_object = ".$object_id." and type_object = ".$object_type;
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				if ($row = pmb_mysql_fetch_object($result)) {
					return $row->num_vedette;
				}
			}
		}
		return 0;
	}	
	
	/**
	 * Retourne l'identifiant de la vedette liée à un objet
	 * @param int $object_id Identifiant de l'objet
	 * @param int $object_type Type de l'objet
	 * @return int Identifiant de la vedette liée
	 */
	public static function get_object_id_from_vedette_id($vedette_id, $object_type) {
		global $dbh;

		$query = "select num_object from vedette_link where num_vedette = ".$vedette_id." and type_object = ".$object_type;
		$result = pmb_mysql_query($query, $dbh);
		if ($result && pmb_mysql_num_rows($result)) {
			if ($row = pmb_mysql_fetch_object($result)) {
				return $row->num_object;
			}
		}
		return 0;
	}
	
	
	/**
	 * Retourne un tableau des identifiants des vedettes contenant l'élément
	 * @param int $element_id Identifiant en base de l'élément
	 * @param string $element_type Type de l'élément
	 * 
	 * @return array Tableau des identifiants des vedettes
	 */
	public static function get_vedettes_built_with_element($element_id, $element_type) {
		global $dbh;
		
		$vedettes_id = array();
		if($element_type == 'authperso'){
		    $query = "select authperso_authority_authperso_num from authperso_authorities where id_authperso_authority=".$element_id;
		    $result = pmb_mysql_query($query);
		    if(pmb_mysql_num_rows($result)){
		        $element_type.=pmb_mysql_result($result, 0, 0);
		    }
		}
		$grammars = static::get_grammars();
		foreach ($grammars as $grammar) {
			$vedette = new vedette_composee(0, $grammar);
			
			// On récupère l'identifiant lié au type d'élément
			if($vedette->get_available_fields())
				foreach($vedette->get_available_fields() as $key=>$field){
					if($field["type"] == $element_type){
						$element_type_num = $field["num"];
						break;
					}
			}
			
			// On va chercher en base les vedettes contenant cet élément
			$query = "select distinct num_vedette from vedette_object inner join vedette on num_vedette = id_vedette where object_id = ".$element_id." and object_type = ".$element_type_num." and grammar = '".$grammar."' ";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$vedettes_id[] = $row->num_vedette;
				}
			}	
		}
		return $vedettes_id;
	}
	
	 /**
	  * Recalcule le label de la vedette
	  */
	public function update_label() {
		// On trie le tableau des éléments
		$this->sort_vedette_elements();
		
		$label = "";
		
		foreach ($this->vedette_elements as $subdivision=>$elements){
			/* @var $element vedette_element */
			foreach ($elements as $position=>$element){
				if ($label) $label .= self::$configs[$this->config_filename]['separator'];
				$label .= $element->get_isbd();
			}
		}
		$this->label = $label;
	}
	
	/**
	 * Trie le tableau des éléments selon l'ordre des subdivisions
	 */
	private function sort_vedette_elements() {
		$sort_array = array();
		
		// On crée un tableau contenant les ordres
		foreach ($this->vedette_elements as $subdivision_code => $elements) {
			$subdivision = $this->get_at_subdivision_code($subdivision_code);
			$sort_array[] = $subdivision["order"];
		}
		
		// On trie le tableau des vedettes par rapport au tableau des ordres
		array_multisort($sort_array, $this->vedette_elements, SORT_NUMERIC);
	}
	
	protected function build_authpersos(){
		$authorities = array();
		for($i=0 ; $i<count(self::$configs[$this->config_filename]['available_fields']) ; $i++){
			if(self::$configs[$this->config_filename]['available_fields'][$i]['type'] == 'authperso'){
				$infos = self::$configs[$this->config_filename]['available_fields'][$i];
				unset(self::$configs[$this->config_filename]['available_fields'][$i]);
				$authpersos=authpersos::get_instance();
				foreach($authpersos->info as $authority){
					$authorities[] = array(
						'num' => (string)($infos['num']+$authority['id']),
						'name' => $authority['name'],
						'class_name' => "vedette_authpersos",
						'type' => "authperso".$authority['id'],
						'params' => array(
							'id_authority'=> $authority['id'],
							'label' => $authority['name']
						)
					);
				}
				break;
			}
		}
		if(self::$configs[$this->config_filename]['available_fields']){
 			self::$configs[$this->config_filename]['available_fields'] = array_merge(self::$configs[$this->config_filename]['available_fields'], $authorities);
		}  else{
			self::$configs[$this->config_filename]['available_fields'] = $authorities;
		}	
	}
	
	public function get_subdivision_name_by_code($code) {
		global $msg;
		
		foreach (self::$configs[$this->config_filename]['subdivisions'] as $subdivision) {
			if ($subdivision["code"] == $code) {
				if (substr($subdivision['name'], 0, 4) == "msg:") {
					if ($msg[substr($subdivision['name'], 4)]) {
						return $msg[substr($subdivision['name'], 4)];
					} else {
						return substr($subdivision['name'], 4);
					}
				} else if ($subdivision['name']) {
					return $subdivision['name'];
				} else {
					return $subdivision['code'];
				}
			}
		}
		return "";
	}
	
	public static function get_grammars() {
		if(!isset(static::$grammars)) {
			static::$grammars = array();
			$query = "select distinct grammar from vedette";
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					static::$grammars[] = $row->grammar;
				}
			}
		}
		return static::$grammars;
	}
}