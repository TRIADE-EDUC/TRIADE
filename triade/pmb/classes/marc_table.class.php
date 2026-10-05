<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: marc_table.class.php,v 1.49 2022/12/07 16:32:48 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classe de gestion des tables MARC en XML

if ( ! defined( 'MARC_TABLE_CLASS' ) ) {
  define( 'MARC_TABLE_CLASS', 1 );

// pas bon, à remonter dans les fichiers appelants
require_once($class_path.'/XMLlist.class.php');
require_once($class_path.'/XMLlist_links.class.php');

class marc_list {

// propriétés
	public $table;
	public $tablefav;
	public $parser;
	public $inverse_of = array();
	public $attributes = array();
	
	public const TYPE_DOCTYPE = "doctype";
	
	public const TYPE_NIVEAU_BIBLIO = "nivbiblio";

// méthodes

	// constructeur
	public function __construct($type) {
		global $lang;
		global $charset;
		global $include_path;
		switch($type) {
			case 'country':
				$parser = new XMLlist("$include_path/marc_tables/$lang/country.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'icondoc':
				$parser = new XMLlist("$include_path/marc_tables/icondoc.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'icondoc_big':
				$parser = new XMLlist("$include_path/marc_tables/icondoc_big.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'lang':
				$parser = new XMLlist("$include_path/marc_tables/$lang/lang.xml");
				$parser->analyser();
				$this->table = $parser->table;
				$this->tablefav = $parser->tablefav;
				break;
			case \marc_list::TYPE_DOCTYPE:
				$parser = new XMLlist("$include_path/marc_tables/$lang/doctype.xml", 0);
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'recordtype':
				$parser = new XMLlist("$include_path/marc_tables/$lang/recordtype.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'function':
				$parser = new XMLlist("$include_path/marc_tables/$lang/function.xml");
				$parser->analyser();
				$this->table = $parser->table;
				$this->tablefav = $parser->tablefav;
				break;
			case 'section_995':
				$parser = new XMLlist("$include_path/marc_tables/$lang/section_995.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'typdoc_995':
				$parser = new XMLlist("$include_path/marc_tables/$lang/typdoc_995.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;			
			case 'codstatdoc_995':
				$parser = new XMLlist("$include_path/marc_tables/$lang/codstat_995.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;			
			case 'diacritique':
			// Armelle : a priori plus utile.
				$parser = new XMLlist("$include_path/marc_tables/diacritique.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case \marc_list::TYPE_NIVEAU_BIBLIO:
				$parser = new XMLlist("$include_path/marc_tables/$lang/nivbiblio.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;	
			case 'relationtypeup':
				$parser = new XMLlist("$include_path/marc_tables/$lang/relationtypeup.xml");
				$parser->setAttributesToParse(array(array('name' => "REVERSE_CODE"),array('name' => "REVERSE_CODE_DEFAULT_CHECKED", 'default_value' => 'yes')));
				$parser->analyser();
				$this->attributes=$parser->getAttributes();
				$this->table = $parser->table;
				break;		
			case 'relationtypedown':
				$parser = new XMLlist("$include_path/marc_tables/$lang/relationtypedown.xml");
				$parser->setAttributesToParse(array(array('name' => "REVERSE_CODE"),array('name' => "REVERSE_CODE_DEFAULT_CHECKED", 'default_value' => 'yes')));
				$parser->analyser();
				$this->attributes=$parser->getAttributes();
				$this->table = $parser->table;
				break;
			case 'relationtype_aut':
				$parser = new XMLlist("$include_path/marc_tables/$lang/relationtype_aut.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'relationtype_autup':
				$parser = new XMLlist("$include_path/marc_tables/$lang/relationtype_autup.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'relationtypedown_unimarc':
				$parser = new XMLlist("$include_path/marc_tables/$lang/relationtypedown_unimarc.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'relationtypeup_unimarc':
				$parser = new XMLlist("$include_path/marc_tables/$lang/relationtypeup_unimarc.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'languages':
				$parser = new XMLlist("$include_path/messages/languages.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'music_key':
				$parser = new XMLlist("$include_path/marc_tables/$lang/music_key.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;	
			case 'music_form':
				$parser = new XMLlist("$include_path/marc_tables/$lang/music_form.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;	
			case 'oeuvre_type':
				$parser = new XMLlist("$include_path/marc_tables/$lang/oeuvre_type.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'oeuvre_nature':
				$parser = new XMLlist("$include_path/marc_tables/$lang/oeuvre_nature.xml");
				$parser->setAttributesToParse(array(array('name' => "NATURE")));				
				$parser->analyser();
				$this->attributes=$parser->getAttributes();
				$this->table = $parser->table;
				break;
			case 'oeuvre_link':
				$parser = new XMLlist_links("$include_path/marc_tables/$lang/oeuvre_link.xml");
				$parser->setAttributesToParse(array(array('name' => 'EXPRESSION', 'default_value' => 'no'), array('name' => 'OTHER_LINK', 'default_value' => 'yes'), array('name' => 'GROUP', 'default_value' => '')));
				$parser->analyser();
				$this->table = $parser->table;
				$this->attributes = $parser->getAttributes();
				$this->inverse_of = $parser->inverse_of;
				break;				
			case 'aut_link':
			    $parser = new XMLlist_links("$include_path/marc_tables/$lang/aut_link.xml");
			    $parser->setAttributesToParse(array(array('name' => 'AUT_LINK', 'default_value' => 'yes'), array('name' => 'GROUP', 'default_value' => '')));
			    $parser->analyser();
			    $this->table = $parser->table;
			    $this->attributes = $parser->getAttributes();
			    $this->inverse_of = $parser->inverse_of;
			    break;
			case 'rent_account_type':
				$parser = new XMLlist("$include_path/marc_tables/$lang/rent_account_type.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'rent_request_type':
				$parser = new XMLlist("$include_path/marc_tables/$lang/rent_request_type.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			case 'rent_destination':
				$parser = new XMLlist("$include_path/marc_tables/$lang/rent_destination.xml");
				$parser->analyser();
				$this->table = $parser->table;
				break;
			default:
				$this->table=array();
				break;
		}
	}

}

class marc_select {

// propriétés

	public $table;
	public $name;
	public $selected;
	public $onchange;
	public $display;
	public $libelle; // libellé du selected
	public $attributes=array();

// méthodes

	// constructeur
	public function __construct($type, $name='mySelector', $selected='', $onchange='', $option_premier_code='', $option_premier_info='', $attributes=array()){
		$source = marc_list_collection::get_instance($type);
		$this->table = $source->table;
		if($option_premier_code!=='' && $option_premier_info!=='') {
			$option_premier_tab = array($option_premier_code=>$option_premier_info);
			$this->table=$option_premier_tab + $this->table;
		}
		$this->name = $name;
		$this->selected = $selected;
		$this->onchange = $onchange;
		$this->attributes = $attributes;
		$this->get_selector();
	}

	public function get_selector(){
		global $charset;
		
		$attribute_fields=' ';
		foreach ($this->attributes as $attribute){
			$attribute_fields.=$attribute['name'].'="'.$attribute['value'].'" ';
		}
		if ($this->onchange) $onchange=" onchange=\"".$this->onchange."\" ";
		else $onchange="";
		$this->display = '<select id="'.$this->name.'" name="'.$this->name.'" '.$attribute_fields.' '.$onchange.' >';
		
		foreach($this->table as $value=>$libelle) {
			if(is_array($libelle)){
				$this->display.='
					<optgroup label="'.htmlentities($value,ENT_QUOTES,$charset).'">';
				foreach($libelle as $key => $val){
					$this->gen_option($key, $val);
				}
				$this->display.="
					</optgroup>";
			}else {
				$this->gen_option($value, $libelle);
			}
		}
		$this->display .= "</select>";
	}
	
	private function gen_option($value, $libelle){
		global $charset;
		if(!($value == $this->selected))
			$tag = "<option value='".$value."'>";
		else{
			$tag = "<option value='".$value."' selected='selected'>";
			$this->libelle=$libelle;
		}
		$this->display .= $tag.htmlentities($libelle,ENT_QUOTES,$charset)."</option>";
	}
	
	public function get_radio_selector(){
		$display = "";
		foreach($this->table as $value=>$libelle) {
			if(is_array($libelle)){
				foreach($libelle as $key => $val){
					$display.= $this->gen_radio_item($key, $val);
				}
			}else {
				$display.= $this->gen_radio_item($value, $libelle);
			}
		}
		return $display;
	}
	
	private function gen_radio_item($value, $libelle){
		global $charset;
		
		$onchange = $selected = '';
		if ($this->onchange) $onchange = " onclick=\"".$this->onchange."\" ";
		if((!$this->selected && !$value) || ($value === $this->selected)){
			$selected = " checked='checked' ";
			$this->libelle = $libelle;
		}
		return "&nbsp;<input type='radio' id='".$this->name."_".htmlentities($value,ENT_QUOTES,$charset)."' name='".$this->name."' value='".htmlentities($value,ENT_QUOTES,$charset)."'".$selected.$onchange."/>&nbsp;
				<label for='".$this->name."_".htmlentities($value,ENT_QUOTES,$charset)."'>".htmlentities($libelle,ENT_QUOTES,$charset)."</label>";
	}
	
	public function first_item_at_last() {
		$item = array_shift($this->table);
		$this->table[] = $item;
	}
}

class marc_list_collection {

	private static $marc_list = array();

	public static function get_instance($type) {
		if (!isset(self::$marc_list[$type])) {
			self::$marc_list[$type] = new marc_list($type);
		}
		return self::$marc_list[$type];
	}
}

} # fin de déclaration
