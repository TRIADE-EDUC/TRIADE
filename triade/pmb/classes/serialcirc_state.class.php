<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_state.class.php,v 1.9 2019-06-05 06:41:21 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/serialcirc.inc.php");
require_once($include_path."/templates/serialcirc_state.tpl.php");
require_once($class_path."/serial_display.class.php");
require_once($class_path."/serialcirc_diff.class.php");

class serialcirc_state {

	/**
	 * Tableau des listes de circulation de périodiques
	 * @var serialcirc_diff
	 */
	protected $serialcirc_diffs;
	
	/**
	 * Identifiant de la localisation sur laquelle on filtre
	 * @var int
	 */
	protected $location_id;
	
	/**
	 * Identifiant de la perio sur laquelle on filtre
	 * @var int
	 */
	protected $perio_id;
	
	/**
	 * Identifiant du panier sur lequel on filtre
	 * @var int $caddie_id
	 */
	protected $caddie_id;
	
	/**
	 * Date de fin d'abonnement sur laquelle on filtre
	 * @var string $date_echeance
	 */
	protected $date_echeance;
	
	/**
	 * Tableau des libellés de localisations
	 * @var array
	 */
	protected $locations_labels;
	
	/**
	 * Liste des identifiants de notices séparés par des virgules sur lesquelles on filtre
	 * @var string
	 */
	protected $records_ids;
	
	/**
	 * Nombre total de résultats
	 * @var int
	 */
	protected $nb_results;
	
	public function __construct(){
		$this->date_echeance = date("Y-m-d");
	}
	
	public function get_filters_from_form() {
		global $serialcirc_state_location_filter;
		global $serialcirc_state_perio_filter_id;
		global $serialcirc_state_caddie_filter;
		global $serialcirc_state_date_echeance;
		
		if ($serialcirc_state_location_filter*1) {
			$this->location_id = $serialcirc_state_location_filter*1;
		}
		$this->records_ids = '';
		
		if ($serialcirc_state_caddie_filter*1) {
			$this->caddie_id = $serialcirc_state_caddie_filter*1;
		}

		if ($serialcirc_state_perio_filter_id*1) {
			$this->perio_id = $serialcirc_state_perio_filter_id*1;
		}
		
		if ($serialcirc_state_date_echeance) {
			$this->date_echeance = $serialcirc_state_date_echeance;
		}
		
		if ($this->caddie_id) {
			$query = 'select group_concat(object_id separator ",") from caddie_content where caddie_id = '.$this->caddie_id;
			if ($this->perio_id) {
				$query.= ' and object_id = '.$this->perio_id;
			}
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$this->records_ids = pmb_mysql_result($result, 0, 0);
			}
		} else if ($this->perio_id) {
			$this->records_ids = $this->perio_id;
		}
	}

	function fetch_data($page = 0, $nb_per_page = 0) {
		global $dbh;
		
		$this->serialcirc_diffs = array();
		if ($this->caddie_id && !$this->records_ids) {
			return false;
		}
		
		$query = 'select SQL_CALC_FOUND_ROWS id_serialcirc, if(serialcirc_diff.serialcirc_diff_empr_type = '.SERIALCIRC_EMPR_TYPE_empr.', serialcirc_diff.num_serialcirc_diff_empr, serialcirc_group.num_serialcirc_group_empr) as empr_id from serialcirc 
				join abts_abts on serialcirc.num_serialcirc_abt = abts_abts.abt_id 
				join notices on abts_abts.num_notice = notices.notice_id 
				join serialcirc_diff on serialcirc.id_serialcirc = serialcirc_diff.num_serialcirc_diff_serialcirc 
				left join serialcirc_group on serialcirc_diff.serialcirc_diff_empr_type = '.SERIALCIRC_EMPR_TYPE_group.' and serialcirc_diff.id_serialcirc_diff = serialcirc_group.num_serialcirc_group_diff 
				where 1';
		// Filtre de localisation
		if ($this->location_id) {
			$query.= ' and abts_abts.location_id = '.$this->location_id;
		}
		// Filtre de notices
		if ($this->records_ids) {
			$query.= ' and abts_abts.num_notice in ('.$this->records_ids.')';
		}
		// Filtre date echeance
		if ($this->date_echeance && ($this->date_echeance!=-1)) {
			$query.= ' and abts_abts.date_fin >= "'.$this->date_echeance .'"';
		}
		// Order
		$query.= ' order by notices.index_sew, abts_abts.abt_name, serialcirc_diff.serialcirc_diff_order, serialcirc_group.serialcirc_group_order';
		// Limite
		if ($page && $nb_per_page) {
			$query.= ' limit '.($page-1)*$nb_per_page.', '.$nb_per_page;
		}
		
		$result = pmb_mysql_query($query, $dbh);
		$this->nb_results = pmb_mysql_result(pmb_mysql_query('select FOUND_ROWS()'), 0, 0);
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if (!isset($this->serialcirc_diffs[$row->id_serialcirc])) {
					$this->serialcirc_diffs[$row->id_serialcirc] = array(
							'object' => new serialcirc_diff($row->id_serialcirc),
							'emprs' => array()
					);
				}
				$this->serialcirc_diffs[$row->id_serialcirc]['emprs'][] = $row->empr_id;
			}
		}
	}
	
	public function get_list() {
		global $serialcirc_state_list, $serialcirc_state_list_line;
		global $nb_per_page, $page;
		
		if (!$nb_per_page) {
			$nb_per_page = 20;
		}
		if (!$page) {
			$page = 1;
		}
		
		$this->fetch_data($page, $nb_per_page);
		
		$tpl = $serialcirc_state_list;
		
		$tpl = str_replace('!!filters_form!!', $this->get_filters_form(), $tpl);
		
		$lines = '';
		foreach ($this->serialcirc_diffs as $serialcirc_diff) {
			$serialcirc = $serialcirc_diff['object'];
			if ($serialcirc->no_ret_circ) {
				// Le dernier lecteur garde le bulletin
				$last_empr = end($serialcirc->empr_info);
				$endlocation = $this->get_location_label($last_empr['location']);
			} else {
				// Le bulletin revient à la localisation de l'abonnement
				$endlocation = $this->get_location_label($serialcirc->serial_info['abt_location']);
			}
			foreach ($serialcirc_diff['emprs'] as $empr_id) {
				$empr = $serialcirc->empr_info[$empr_id];
				$line = $serialcirc_state_list_line;
				$line = str_replace('!!periodique!!', $serialcirc->serial_info['serial_name'], $line);
				$line = str_replace('!!periodique_link!!', $serialcirc->serial_info['serial_link'], $line);
				$line = str_replace('!!abonnement!!', $serialcirc->abt_name, $line);
				$line = str_replace('!!abonnement_link!!', $serialcirc->serial_info['serialcirc_link'], $line);
				$line = str_replace('!!empr!!', $empr['nom'].'&nbsp;'.$empr['prenom'], $line);
				$line = str_replace('!!empr_link!!', $empr['view_link'], $line);
				$line = str_replace('!!address!!', $empr['adr1'], $line);
				$line = str_replace('!!city!!', $empr['ville'], $line);
				$line = str_replace('!!end_location!!', $endlocation, $line);
				$lines.= $line;
			}
		}

		$tpl = str_replace('!!lines!!', $lines, $tpl);
		$url = './edit.php?categ=serials&sub=circ_state&serialcirc_state_location_filter='.$this->location_id.'&serialcirc_state_caddie_filter='.$this->caddie_id.'&serialcirc_state_perio_filter_id='.$this->perio_id.'&serialcirc_state_date_echeance='.$this->date_echeance;
		$tpl = str_replace('!!pagination!!', aff_pagination($url, $this->nb_results, $nb_per_page, $page, 10, true, true), $tpl);
		return $tpl;
	}
	
	public function get_serialcirc_list() {
		return $this->serialcirc_list;
	}
	
	public function get_location_id(){
		return $this->location_id;
	}

	public function set_location_id($location_id) {
		$this->location_id = $location_id;
	}
	
	protected function get_location_label($location_id) {
		if (!$this->locations_labels) {
			$this->locations_labels = array();
			$query = 'select idlocation, location_libelle from docs_location';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->locations_labels[$row->idlocation] = $row->location_libelle;
				}
			}
		}
		return $this->locations_labels[$location_id];
	}
	
	protected function get_filters_form() {
		global $serialcirc_state_filters_form, $msg;
		global $PMBuserid;
		
		$filters = $serialcirc_state_filters_form;
		
		// Action
		$filters = str_replace('!!form_action!!', './edit.php?categ=serials&sub=circ_state', $filters);
		
		// Sélecteur de localisation
		$query = 'select idlocation, location_libelle from docs_location order by location_libelle';
		$location_filter = gen_liste($query, 'idlocation', 'location_libelle', 'serialcirc_state_location_filter', '', $this->location_id, '', '', 0, $msg['all_location']);
		$filters = str_replace('!!serialcirc_state_location_filter!!', $location_filter, $filters);
		
		// Sélecteur de panier
		$query = 'select idcaddie, name from caddie where type = "NOTI" and (autorisations="'.$PMBuserid.'" or autorisations like "'.$PMBuserid.' %" or autorisations like "% '.$PMBuserid.' %" or autorisations like "% '.$PMBuserid.'") order by name';
		$caddie_filter = gen_liste($query, 'idcaddie', 'name', 'serialcirc_state_caddie_filter', '', $this->caddie_id, '', '', 0, $msg['serialcirc_diff_no_selection_caddie']);
		$filters = str_replace('!!serialcirc_state_caddie_filter!!', $caddie_filter, $filters);
		
		// Sélecteur de fin d'abonnement
		if ($this->date_echeance!=-1) {
			$serialcirc_state_date_echeance_lib = format_date($this->date_echeance);
		} else {
			$serialcirc_state_date_echeance_lib = $msg['parperso_nodate'];
		}
		$date_echeance_filter = "<input type='hidden' id='serialcirc_state_date_echeance' name='serialcirc_state_date_echeance' value='".$this->date_echeance."' />
			<input type='button' id='serialcirc_state_date_echeance_lib' class='bouton_small' value='".$serialcirc_state_date_echeance_lib."' onclick=\"var date_c='';if (this.form.elements['serialcirc_state_date_echeance'].value!='-1') date_c=this.form.elements['serialcirc_state_date_echeance'].value; openPopUp('./select.php?what=calendrier&caller='+this.form.name+'&date_caller='+date_c+'&param1=serialcirc_state_date_echeance&param2=serialcirc_state_date_echeance_lib&auto_submit=NO&date_anterieure=YES', 'date_date_test', 250, 300, -2, -2, 'toolbar=no, dependent=yes, resizable=yes')\" />
			<input type='button' class='bouton_small' style='width:25px;' value='".$msg['raz']."' onclick=\"this.form.elements['serialcirc_state_date_echeance_lib'].value='".$msg['parperso_nodate']."'; this.form.elements['serialcirc_state_date_echeance'].value='-1';\" />";
		$filters = str_replace('!!serialcirc_state_date_echeance_filter!!', $date_echeance_filter, $filters);
		
		// Sélecteur de pério
		$perio_label = '';
		if ($this->perio_id) {
			$query = 'select tit1 from notices where notice_id = '.$this->perio_id;
			$perio_label = pmb_mysql_result(pmb_mysql_query($query), 0, 0);
		}
		$filters = str_replace('!!perio_label!!', $perio_label, $filters);
		$filters = str_replace('!!perio_id!!', $this->perio_id, $filters);
		
		return $filters;
	}
	
	public function export_list_tableau() {
		global $msg;
		
		$this->fetch_data();
		
		$worksheet = new spreadsheetPMB();
		
		$worksheet->write(0, 0, $msg["1150"].' : '.$msg['serial_circ_state_edit']);

		$worksheet->write(2, 0, $msg["1150"]);
		$worksheet->write(2, 1, $msg["serialcirc_circ_list_bull_circulation_abonnement"]);
		$worksheet->write(2, 2, $msg["379"]);
		$worksheet->write(2, 3, $msg["adresse_empr"]);
		$worksheet->write(2, 4, $msg["ville_empr"]);
		$worksheet->write(2, 5, $msg["serial_circ_state_end_location"]);

		$line = 3;
		foreach ($this->serialcirc_diffs as $serialcirc_diff) {
			$serialcirc = $serialcirc_diff['object'];
			if ($serialcirc->no_ret_circ) {
				// Le dernier lecteur garde le bulletin
				$last_empr = end($serialcirc->empr_info);
				$endlocation = $this->get_location_label($last_empr['location']);
			} else {
				// Le bulletin revient à la localisation de l'abonnement
				$endlocation = $this->get_location_label($serialcirc->serial_info['abt_location']);
			}
			foreach ($serialcirc_diff['emprs'] as $empr_id) {
				$empr = $serialcirc->empr_info[$empr_id];
				$worksheet->write($line, 0, $serialcirc->serial_info['serial_name']);
				$worksheet->write($line, 1, $serialcirc->abt_name);
				$worksheet->write($line, 2, $empr['nom'].' '.$empr['prenom']);
				$worksheet->write($line, 3, $empr['adr1']);
				$worksheet->write($line, 4, $empr['ville']);
				$worksheet->write($line, 5, $endlocation);
				$line++;
			}
		}
		
		$worksheet->download('Circulations.xls');
	}
	
	public function export_list_tableauhtml() {
		global $msg;
		
		$this->fetch_data();
		
		$content = "<h1>".$msg["1150"].' : '.$msg['serial_circ_state_edit']."</h1>" ;  
		
		$content.= "<table>" ;
		$content.= "<tr>
			<th style='width:20%'>".$msg["1150"]."</th>
			<th style='width:16%'>".$msg["serialcirc_circ_list_bull_circulation_abonnement"]."</th>
			<th style='width:16%'>".$msg["379"]."</th>
			<th style='width:16%'>".$msg["adresse_empr"]."</th>
			<th style='width:16%'>".$msg["ville_empr"]."</th>
			<th style='width:16%'>".$msg["serial_circ_state_end_location"]."</th>
		</tr>";
		

		foreach ($this->serialcirc_diffs as $serialcirc_diff) {
			$serialcirc = $serialcirc_diff['object'];
			if ($serialcirc->no_ret_circ) {
				// Le dernier lecteur garde le bulletin
				$last_empr = end($serialcirc->empr_info);
				$endlocation = $this->get_location_label($last_empr['location']);
			} else {
				// Le bulletin revient à la localisation de l'abonnement
				$endlocation = $this->get_location_label($serialcirc->serial_info['abt_location']);
			}
			foreach ($serialcirc_diff['emprs'] as $empr_id) {
				$empr = $serialcirc->empr_info[$empr_id];
				
				$content.= "<tr>";
				$content.= "
						<td>".$serialcirc->serial_info['serial_name']."</td>
						<td>".$serialcirc->abt_name."</td>
						<td>".$empr['nom'].' '.$empr['prenom']."</td>
						<td>".$empr['adr1']."</td>
						<td>".$empr['ville']."</td>
						<td>".$endlocation."</td>
					</tr>";
			}
		}
		$content.= "</table>";
		return $content;
	}
} 

 