<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thresholds.class.php,v 1.3 2017-10-19 14:06:54 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/entites.class.php");
require_once($class_path."/threshold.class.php");

class thresholds {
	
	/**
	 * Etablissement associé
	 * @var entites
	 */
	protected $entity;
	
	/**
	 * Tableau de seuils
	 */
	protected $thresholds;
	
	public function __construct($num_entity=0) {
		$this->entity = null;
		$this->thresholds = array();
		if($num_entity*1) {
			$this->entity = new entites($num_entity);
			$this->fetch_data();
		}
	}
	
	/**
	 * Data
	 */
	protected function fetch_data() {
		$query = 'select id_threshold from thresholds where threshold_num_entity = '.$this->entity->id_entite.' order by threshold_amount';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->thresholds[] = new threshold($row->id_threshold);
			}
		}
	}
		
	public function get_display_header_list() {
		global $msg;
		$display = "
			<th>".$msg['threshold_label']."</th>
			<th>".$msg['threshold_amount']."</th>
			<th>".$msg['threshold_amount_tax_included']."</th>
			<th>".$msg['threshold_footer']."</th>";
		return $display;
	}
	
	public function get_display_content_list() {
		global $charset;
		global $pmb_gestion_devise;
		
		$display = "";
		$parity=1;
		foreach($this->thresholds as $threshold) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity++;
			$tr_javascript = " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"
			onmousedown=\"document.location='./admin.php?categ=acquisition&sub=thresholds&action=edit&id=".$threshold->get_id()."';\"
				style='cursor: pointer;' ";
			$display .= "<tr ".$tr_javascript.">
				<td>".htmlentities($threshold->get_label(), ENT_QUOTES, $charset)."</td>
				<td class='center'>".htmlentities(number_format($threshold->get_amount(),'2','.',' '), ENT_QUOTES, $charset)." ".$pmb_gestion_devise."</td>
				<td class='center'>".($threshold->get_amount_tax_included() ? "X" : "")."</td>
				<td>".htmlentities($threshold->get_footer(), ENT_QUOTES, $charset)."</td>
			</tr>";
		}
		return $display;
	}
	
	public function get_display_list() {
		global $base_path, $msg, $charset;
	
		$display = '';
		$display .= "<div class='row'><label>".$this->entity->raison_sociale."</label></div>";
		$display .= "<table id='thresholds_list'>";
		$display .= $this->get_display_header_list();
		if(count($this->thresholds)) {
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>";
		$display .= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
				<input type='button' class='bouton' onclick=\"document.location='".$base_path."/admin.php?categ=acquisition&sub=thresholds&action=edit&id=0&id_entity=".$this->entity->id_entite."'\" value='".htmlentities($msg['ajouter'], ENT_QUOTES, $charset)."' />
			</div>
			<div class='right'>
			</div>
		</div>";
		return $display;
	}
	
	public function get_data() {
		$data = array();
		foreach($this->thresholds as $threshold) {
			$data[] = $threshold->get_data();
		}
		return $data;
	}
	
	public function get_json_data() {
		return json_encode(encoding_normalize::utf8_normalize($this->get_data()));
	}
	
	public function get_threshold_from_price($ht_price='0.00', $ttc_price='0.00') {
		$thresholds = array_reverse($this->thresholds);
		foreach($thresholds as $threshold) {
			if((!$threshold->get_amount_tax_included() & ($threshold->get_amount() <= $ht_price)) || ($threshold->get_amount_tax_included() & ($threshold->get_amount() <= $ttc_price))) {
				return $threshold; 
			}
		}
		return false;
	}
	
	public function get_entity() {
		return $this->entity;
	}
	
	public function get_thresholds() {
		return $this->thresholds;
	}
}