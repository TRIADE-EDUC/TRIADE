<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_selfservice_pret_ui.class.php,v 1.2.8.1 2024/05/15 06:34:50 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_selfservice_pret_ui extends list_configuration_selfservice_ui {
	
	protected function fetch_data() {
		$this->objects = array();
		$message = $this->get_parameter('selfservice', 'pret_carte_invalide_msg');
		$this->add_selfservice('selfservice_admin_pret_carte_invalide', $message);
		$message = $this->get_parameter('selfservice', 'pret_pret_interdit_msg');
		$this->add_selfservice('selfservice_admin_pret_pret_interdit', $message);
		$message = $this->get_parameter('selfservice', 'pret_deja_prete_msg');
		$this->add_selfservice('selfservice_admin_pret_deja_prete', $message);
		$message = $this->get_parameter('selfservice', 'pret_deja_reserve_msg');
		$this->add_selfservice('selfservice_admin_pret_deja_reserve', $message);
		$message = $this->get_parameter('selfservice', 'pret_quota_bloc_msg');
		$this->add_selfservice('selfservice_admin_pret_quota_bloc', $message);
		$message = $this->get_parameter('selfservice', 'pret_non_pretable_msg');
		$this->add_selfservice('selfservice_admin_pret_non_pretable', $message);
		$message = $this->get_parameter('selfservice', 'pret_expl_inconnu_msg');
		$this->add_selfservice('selfservice_admin_pret_expl_inconnu', $message);
		
		$this->add_separator_parameter('selfservice_param_prolong');
		$message = $this->get_parameter('selfservice', 'pret_prolonge_non_msg');
		$this->add_selfservice('selfservice_admin_pret_prolonge_non', $message);
		$message = $this->get_parameter('selfservice', 'already_loaned');
		$this->add_selfservice('selfservice_admin_already_loaned', $message);
	}
	
	public function init_applied_group($applied_group=array()) {
		$this->applied_group = array(0 => 'section');
	}
	
	protected function get_message_not_grouped() {
		return '';
	}
	
	protected function get_display_group_header_list($group_label, $level=1, $uid='') {
		if($group_label == $this->get_message_not_grouped()) {
			return '';
		}
		$display = "
		<tr id='".$uid."_group_header'>
			<th colspan='".count($this->columns)."'>
				".$this->get_cell_group_label($group_label, ($level-1))."
			</th>
		</tr>";
		return $display;
	}
}