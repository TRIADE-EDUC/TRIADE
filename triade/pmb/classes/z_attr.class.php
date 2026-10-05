<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: z_attr.class.php,v 1.2 2021/03/12 15:15:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class z_attr {
	
	/* ---------------------------------------------------------------
	 propriétés de la classe
	 --------------------------------------------------------------- */
	
	public $id=0;
	public $libelle='';
	public $attr='';
	
	public function __construct($id=0, $libelle='') {
		$this->id = intval($id);
		$this->libelle = $libelle;
		$this->getData();
	}
	
	/* ---------------------------------------------------------------
	 getData() : récupération des propriétés
	 --------------------------------------------------------------- */
	public function getData() {
		if(!$this->id) return;
		
		$requete = 'SELECT * FROM z_attr WHERE attr_bib_id='.$this->id.' AND attr_libelle="'.addslashes($this->libelle).'"';
		$result = @pmb_mysql_query($requete);
		if(!pmb_mysql_num_rows($result)) {
			pmb_error::get_instance(static::class)->add_message("not_found", "not_found_object");
			return;
		}
		$data = pmb_mysql_fetch_object($result);
		$this->attr = $data->attr_attr;
	}
	
	public function get_form() {
		global $msg;
		global $admin_zattr_form;
		global $include_path ;
		global $charset;
		
		// loading the localized attributes labels
		$la = new XMLlist($include_path."/marc_tables/z3950attributes.xml", 0);
		$la->analyser();
		$codici = $la->table;
		
		if (!$this->libelle) {
			$admin_zattr_form = str_replace('!!form_title!!', $msg["zattr_ajouter_attr"], $admin_zattr_form);
			$admin_zattr_form = str_replace('!!bib_id!!', "", $admin_zattr_form);
			// here the combo box must be enabled because the user is adding a new attr.
			$select = "<div class='row'>
				<div class='colonne4 align_right'>
					<label class='etiquette'>".$msg[$this->libelle]." &nbsp;</label>
				</div>
				<div class='colonne_suite'> ";
			
			$select .= "<select name='form_attr_libelle'>	";
			foreach ($codici as $codeattr => $libelle) {
				if($this->libelle == $codeattr) $select .= "<option value='".htmlentities($codeattr,ENT_QUOTES, $charset)."' SELECTED>".htmlentities($msg["z3950_".$libelle],ENT_QUOTES, $charset)."</option>";
				else $select .= "<option value='".htmlentities($codeattr,ENT_QUOTES, $charset)."'>".htmlentities($msg["z3950_".$libelle],ENT_QUOTES, $charset)."</option>";
			}
			$select .= "</select></div></div>";
			
		} else {
			$admin_zattr_form = str_replace('!!form_title!!', $msg["zattr_modifier_attr"]." : ".$msg["z3950_".$codici[$this->libelle]], $admin_zattr_form);
			$admin_zattr_form = str_replace('!!bib_id!!', $this->id, $admin_zattr_form);
			// here the combo box doesn't appear because the user can't change the attr. label
			
			$select = "<input type=hidden name=form_attr_libelle value='".htmlentities($this->libelle, ENT_QUOTES, $charset)."'>";
		}
		
		$admin_zattr_form = str_replace('!!code!!', $select, $admin_zattr_form);
		
		$admin_zattr_form = str_replace('!!attr_bib_id!!',			$this->id,        $admin_zattr_form);
		$admin_zattr_form = str_replace('!!attr_libelle!!',			htmlentities($this->libelle, ENT_QUOTES, $charset),  $admin_zattr_form);
		$admin_zattr_form = str_replace('!!attr_attr!!',			$this->attr,     $admin_zattr_form);
		$admin_zattr_form = str_replace('!!local_attr_libelle!!',	$msg["z3950_".$codici[$this->libelle]],  $admin_zattr_form);
		
		$form = confirmation_delete("./admin.php?categ=z3950&sub=zattr&action=del&");
		$form .= $admin_zattr_form;
		return $form;
	}
	
	public function set_properties_from_form() {
		global $form_attr_attr;
		
		$this->attr = stripslashes($form_attr_attr);
	}
	
	public function save() {
		global $form_attr_bib_id;
		
		if($this->id) {
			$requete = "UPDATE z_attr SET attr_libelle='".addslashes($this->libelle)."', attr_attr='".addslashes($this->attr)."' WHERE attr_bib_id='".$this->id."' and attr_libelle='".addslashes($this->libelle)."' ";
			pmb_mysql_query($requete);
		} else {
			$requete = "INSERT INTO z_attr (attr_bib_id,  attr_libelle, attr_attr) VALUES ('$form_attr_bib_id', '".addslashes($this->libelle)."', '".addslashes($this->attr)."') ";
			pmb_mysql_query($requete);
			$this->id = $form_attr_bib_id ;
		}
	}
	
	public static function check_data_from_form() {
		global $form_attr_bib_id, $form_attr_libelle, $form_attr_attr;
		
		if(empty($form_attr_bib_id) || empty($form_attr_libelle) || empty($form_attr_attr)) {
			return false;
		}
		return true;
	}
	
	public static function delete($id) {
		global $attr_libelle;
		
		$id = intval($id);
		if (($id) && ($attr_libelle)) {
			$requete = "DELETE FROM z_attr WHERE attr_bib_id='$id' and attr_libelle='$attr_libelle' ";
			pmb_mysql_query($requete);
			return true;
		}
		return true;
	}
} /* fin de définition de la classe */