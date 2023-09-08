<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_objects.class.php,v 1.2 2017-02-01 09:22:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/contact_form/contact_form_object.class.php");

class contact_form_objects {
	
	/**
	 * Liste des objets
	 */
	protected $objects;
	
	/**
	 * Constructeur
	 */
	public function __construct() {
		$this->fetch_data();
	}
	
	/**
	 * Données
	 */
	protected function fetch_data() {
		
		$this->objects = array();
		$query = 'select id_object from contact_form_objects order by object_label';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->objects[] = new contact_form_object($row->id_object);
			}
		}
	}
	
	/**
	 * Sélecteur d'objets de mail
	 */
	public function gen_selector() {
		$selector = "<select name='contact_form_objects' data-dojo-type='dijit/form/Select'>";
		foreach ($this->objects as $object) {
			$selector .= "<option value='".$object->get_id()."'>".$object->get_label()."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	/**
	 * Liste des objets
	 */
	public function get_display_content_list() {
		
		$display = '';
		$parity=1;
		foreach($this->objects as $object) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity++;
			$tr_css_style = "style='cursor: pointer;'";
			$td_javascript = " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
			$td_javascript .= " onmousedown=\"document.location='admin.php?categ=contact_form&sub=objects&action=edit&id=".$object->get_id()."';\" ";
			
			$display .= "<tr class='$pair_impair' $tr_css_style>";
			$display .= '<td '.$td_javascript.'>'.$object->get_label().'</td>';
			$display .= '</tr>';
		}
		return $display;
	}
	
	/**
	 * Header de la liste
	 */
	public function get_display_header_list() {
		global $msg, $charset;
		
		$display = "
		<tr>
			<th>".htmlentities($msg['admin_opac_contact_form_object_label'],ENT_QUOTES,$charset)."</th>
		</tr>
		";
		return $display;
	}
	
	/**
	 * Affiche la liste des objets
	 */
	public function get_display_list() {
		global $msg, $charset;
		
		$display = "<table id='objects_list'>";
		$display .= $this->get_display_header_list();
		if(count($this->objects)) {
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>";
		$display .= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
				<input type='button' class='bouton' name='contact_form_object_add' value='".htmlentities($msg["admin_opac_contact_form_object_add"], ENT_QUOTES, $charset)."' 
					onclick=\"document.location='./admin.php?categ=contact_form&sub=objects&action=edit'\" />
			</div>
			<div class='right'>
			</div>
		</div>";
		return $display;
	}
	
	public function get_objects() {
		return $this->objects;
	}
}