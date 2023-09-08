<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pclassement.class.php,v 1.6 2019-02-26 09:07:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

include_once($include_path."/templates/pclass.tpl.php");

class pclassement {
	
	protected $id;
	
	protected $name;
	
	protected $typedoc;
	
	protected $locations;
	
	public function __construct($id=0) {
		$this->id = $id+0;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->name = '';
		$this->typedoc = '';
		$this->locations = array();
		// on récupère les données
		$query = "select id_pclass,name_pclass,typedoc,locations from pclassement where id_pclass='".$this->id."' ";
		$result = pmb_mysql_query($query);
		if ($row = pmb_mysql_fetch_object($result)) {
			$this->name = $row->name_pclass;
			$this->typedoc = $row->typedoc;
			$this->locations = explode(',' , $row->locations);
		}
	}
	
	protected function get_locations_form() {
		global $thesaurus_classement_location;
		global $pclassement_locations_form;
		
		$locations_form = '';
		if($thesaurus_classement_location) {
			$locations_form = $pclassement_locations_form;
			
			$locations ="";
			$query = "SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle";
			$result = pmb_mysql_query($query);
			while($obj=pmb_mysql_fetch_object($result)) {
				$as=array_search($obj->idlocation,$this->locations);
				$locations .= "
				<input type='checkbox' name='locations_list[]' value='".$obj->idlocation."' ".($as !== null && $as!==false ? "checked='checked'" : "")." class='checkbox' id='location_".$obj->idlocation."' />
				<label for='numloc".$obj->idlocation."'>&nbsp;".$obj->location_libelle."</label>
				<br />";
			}
			$locations_form = str_replace('!!locations!!', $locations, $locations_form);
		}
		return $locations_form;
	}
	
	public function get_form() {
		global $msg, $charset;
		global $pclassement_form;
		global $id_thes;
		
		$form = str_replace('!!id_thes!!', $id_thes, $pclassement_form);
		if($this->id) {	//modification
			$form = str_replace('!!form_title!!', $msg['pclassement_modification'], $form);
			$delete_button = "<input type='button' class='bouton' value='$msg[63]' onClick=\"confirm_delete();\">";
			
			$identifiant = "<div class='row'><label class='etiquette' >".$msg[38]."</label></div>";
			$identifiant.= "<div class='row'>".$this->id."</div>";
		} else {	//creation
			$form = str_replace('!!form_title!!', $msg['pclassement_creation'], $form);
			$delete_button = '';
			$identifiant = '';
		}
		$form = str_replace('!!libelle!!', htmlentities($this->name, ENT_QUOTES, $charset), $form);
		$form = str_replace('!!identifiant!!', $identifiant, $form);
	
		$doctype = new marc_list('doctype');
		$toprint_typdocfield = " <select name='typedoc_list[]' MULTIPLE SIZE=20 >";
		foreach($doctype->table as $value=>$libelletypdoc) {
			if((strpos($this->typedoc, (string) $value)===false)) $tag = "<option value='$value'>";
			else $tag = "<option value='$value' SELECTED>";
			$toprint_typdocfield .= "$tag$libelletypdoc</option>";
		}
		$toprint_typdocfield .= "</select>";
		$form = str_replace('!!type_doc!!', $toprint_typdocfield, $form);
		
		$form = str_replace('!!locations!!', $this->get_locations_form(), $form);
		
		$form = str_replace('!!update_url!!', "./autorites.php?categ=indexint&sub=pclass_update&id_pclass=".$this->id, $form);
		$form = str_replace('!!delete_url!!', "./autorites.php?categ=indexint&sub=pclass_delete&id_pclass=".$this->id, $form);
		$form = str_replace('!!cancel_url!!', "./autorites.php?categ=indexint&sub=pclass", $form);
		$form = str_replace('!!delete_button!!', $delete_button, $form);
		return $form ;
	}
	
	public function set_properties_from_form() {
		global $libelle;
		global $typedoc_list;
		global $locations_list;
		
		$this->name = stripslashes($libelle);
		$typedoc = '';
		if(is_array($typedoc_list)) {
			foreach($typedoc_list as $doc) {
				$typedoc .=	stripslashes($doc);
			}
		}
		$this->typedoc = $typedoc;
		
		$this->locations = array();
		if(is_array($locations_list)) {
			$this->locations = $locations_list;
		}
	}
	
	public function save() {
		global $msg;
		
		if (trim($this->name) == '') {
			error_form_message($msg["pclassement_libelle_manquant"]);
			exit ;
		}
		if($this->id) {
			$query = "UPDATE pclassement 
				SET name_pclass='".addslashes($this->name)."', 
					typedoc='".addslashes($this->typedoc)."',
					locations='".addslashes(implode(',', $this->locations))."'
				WHERE id_pclass =".$this->id;
		}
		else {
			$query = "INSERT INTO pclassement 
				SET name_pclass='".addslashes($this->name)."', 
					typedoc='".addslashes($this->typedoc)."',
					locations='".addslashes(implode(',', $this->locations))."'";
		}
		pmb_mysql_query($query);
	}
	
	public function delete() {
		global $msg;
		
		if($this->id == 1){
			// Interdire l'effacement de l'id 1
			error_form_message($msg["pclassement_suppr_impossible_protege"]);
			exit;
		}
		$query = "SELECT indexint_id FROM indexint WHERE num_pclass='".$this->id."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			// Il y a des enregistrements. Interdire l'effacement.
			error_form_message($msg["pclassement_suppr_impossible"]);
			exit;
		} else {
			// effacement
			$dummy = "delete FROM pclassement WHERE id_pclass='".$this->id."'";
			pmb_mysql_query($dummy);
		}
	}
	
	/**
	 * affichage de la liste pclassement
	 */
	public static function get_display_list() {
		global $base_path;
		global $msg;
		global $browser_pclassement;
		global $browser_header;
		
		$query = "select id_pclass,name_pclass,typedoc from pclassement ";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$browser_content = '';
			$odd_even = 1;
			while ($row = pmb_mysql_fetch_object($result)) {
				if ($odd_even==0) {
					$browser_content .= "	<tr class='odd'>";
					$odd_even=1;
				} else {
					$browser_content .= "	<tr class='even'>";
					$odd_even=0;
				}
				$browser_content .= "<td>";
				$browser_content .= "<a href='".$base_path."/autorites.php?categ=indexint&sub=pclass_form&id_pclass=".$row->id_pclass."' >".$row->name_pclass."</a>";
				$browser_content .= "</td>";
				$browser_content .= "<td>";
				$browser_content .= $row->typedoc;
				$browser_content .= "</td></tr>";
			}
		} else {
			$browser_content = $msg[4051];
		}
		$display = str_replace('!!browser_header!!', $browser_header, $browser_pclassement);
		$display = str_replace('!!browser_content!!', $browser_content, $display);
		$display = str_replace('!!action!!', $base_path."/autorites.php?categ=indexint&sub=pclass_form&id_pclass=0", $display);
		return $display;
	}
	
	/**
	 * affichage d'un sélecteur de la liste pclassement
	 */
	public static function get_selector($name, $selected='') {
		global $msg;
		global $thesaurus_classement_defaut;
		global $thesaurus_classement_mode_pmb;
		global $thesaurus_classement_location, $deflt_docs_location;
	
		if(!$selected) {
			$selected = $thesaurus_classement_defaut;
		}
		$selector = '';
		$query = "select id_pclass,name_pclass,typedoc, locations from pclassement ";
		$result = pmb_mysql_query($query);
		if ($thesaurus_classement_mode_pmb != 0 && pmb_mysql_num_rows($result) > 1) {
			$selector .= "<select id='".$name."' name='".$name."'>";
			while ($row = pmb_mysql_fetch_object($result)) {
				if(!$thesaurus_classement_location || ($selected == $row->id_pclass) || ($thesaurus_classement_location && in_array($deflt_docs_location, explode(',', $row->locations)))) {
					$selector .= "<option value='".$row->id_pclass."' ".($selected == $row->id_pclass ? "selected='selected'" : "").">".$row->name_pclass."</option>";
				}
			}
			$selector .= "</select>";
		} else {
			$pclassement = new pclassement($selected);
			$selector .= $pclassement->name;
			$selector .= "<input type='hidden' id='".$name."' name='".$name."' value='".$selected."' />";
		}
		return $selector;
	}
	
	public static function is_visible($id_pclass=1) {
		global $thesaurus_classement_location, $deflt_docs_location;
		
		if($thesaurus_classement_location && $deflt_docs_location) {
			$pclassement = new pclassement($id_pclass);
			if(in_array($id_pclass, $pclassement->locations)) {
				return true;
			}
			return false;	
		}
		return true;
	}
	
	public static function get_default_id($id_pclass=1) {
		global $thesaurus_classement_location, $deflt_docs_location;
	
		if($thesaurus_classement_location && $deflt_docs_location) {
			$query = "select id_pclass, locations from pclassement order by id_pclass";
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_object($result)) {
				if(in_array($deflt_docs_location, explode(',', $row->locations))) {
					return $row->id_pclass;
				}
			}
		}
		return $id_pclass;
	}
}

